<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Employer\StoreJobPostRequest;
use App\Http\Requests\Employer\UpdateJobPostRequest;
use App\Models\JobPost;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class JobPostController extends Controller
{
    public function index(Request $request): View
    {
        $employer = $this->requireEmployer($request);

        $jobPosts = JobPost::where('employer_id', $employer->id)
            ->latest()
            ->paginate(10);

        return view('employer.job-posts.index', [
            'jobPosts' => $jobPosts,
        ]);
    }

    public function create(): View
    {
        return view('employer.job-posts.create', [
            'jobTypes' => $this->jobTypes(),
        ]);
    }

    public function store(StoreJobPostRequest $request): RedirectResponse
    {
        $employer = $this->requireEmployer($request);
        $data = $request->validated();
        $data['employer_id'] = $employer->id;
        $data['slug'] = $this->generateUniqueSlug($data['title']);
        $data['status'] = $data['status'] ?? 'draft';
        $data['published_at'] = $data['status'] === 'published' ? now() : null;

        $jobPost = JobPost::create($data);

        return redirect()->route('employer.job-posts.show', $jobPost)
            ->with('success', __('Job post created.'));
    }

    public function show(Request $request, JobPost $jobPost): View
    {
        $jobPost = $this->findEmployerJobPost($request, $jobPost->id);

        return view('employer.job-posts.show', [
            'jobPost' => $jobPost,
        ]);
    }

    public function edit(Request $request, JobPost $jobPost): View
    {
        $jobPost = $this->findEmployerJobPost($request, $jobPost->id);

        return view('employer.job-posts.edit', [
            'jobPost' => $jobPost,
            'jobTypes' => $this->jobTypes(),
        ]);
    }

    public function update(UpdateJobPostRequest $request, JobPost $jobPost): RedirectResponse
    {
        $jobPost = $this->findEmployerJobPost($request, $jobPost->id);
        $data = $request->validated();

        if (isset($data['title']) && $data['title'] !== $jobPost->title) {
            $data['slug'] = $this->generateUniqueSlug($data['title'], $jobPost->id);
        }

        if (($data['status'] ?? $jobPost->status) === 'published' && ! $jobPost->published_at) {
            $data['published_at'] = now();
        }

        if (($data['status'] ?? $jobPost->status) === 'closed' && ! $jobPost->closed_at) {
            $data['closed_at'] = now();
        }

        $jobPost->update($data);

        return redirect()->route('employer.job-posts.show', $jobPost)
            ->with('success', __('Job post updated.'));
    }

    public function destroy(Request $request, JobPost $jobPost): RedirectResponse
    {
        $jobPost = $this->findEmployerJobPost($request, $jobPost->id);
        $jobPost->delete();

        return redirect()->route('employer.job-posts.index')
            ->with('success', __('Job post deleted.'));
    }

    public function publish(Request $request, JobPost $jobPost): RedirectResponse
    {
        $jobPost = $this->findEmployerJobPost($request, $jobPost->id);

        $jobPost->update([
            'status' => 'published',
            'published_at' => $jobPost->published_at ?? now(),
            'closed_at' => null,
        ]);

        return redirect()->route('employer.job-posts.show', $jobPost)
            ->with('success', __('Job post published.'));
    }

    public function close(Request $request, JobPost $jobPost): RedirectResponse
    {
        $jobPost = $this->findEmployerJobPost($request, $jobPost->id);

        $jobPost->update([
            'status' => 'closed',
            'closed_at' => $jobPost->closed_at ?? now(),
        ]);

        return redirect()->route('employer.job-posts.show', $jobPost)
            ->with('success', __('Job post closed.'));
    }

    public function duplicate(Request $request, JobPost $jobPost): RedirectResponse
    {
        $jobPost = $this->findEmployerJobPost($request, $jobPost->id);

        $copy = $jobPost->replicate([
            'slug',
            'status',
            'published_at',
            'closed_at',
            'created_at',
            'updated_at',
        ]);

        $copy->title = $jobPost->title.' (Copy)';
        $copy->slug = $this->generateUniqueSlug($copy->title);
        $copy->status = 'draft';
        $copy->published_at = null;
        $copy->closed_at = null;
        $copy->save();

        return redirect()->route('employer.job-posts.edit', $copy)
            ->with('success', __('Job post duplicated.'));
    }

    private function requireEmployer(Request $request)
    {
        $employer = $request->user()->employer;

        if (! $employer) {
            abort(403);
        }

        return $employer;
    }

    private function findEmployerJobPost(Request $request, int $jobPostId): JobPost
    {
        $employer = $this->requireEmployer($request);

        return JobPost::where('employer_id', $employer->id)
            ->where('id', $jobPostId)
            ->firstOrFail();
    }

    private function jobTypes(): array
    {
        return [
            'full_time' => 'Full Time',
            'part_time' => 'Part Time',
            'contract' => 'Contract',
            'temporary' => 'Temporary',
            'internship' => 'Internship',
        ];
    }

    private function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $counter = 2;

        while ($this->slugExists($slug, $ignoreId)) {
            $slug = $base.'-'.$counter;
            $counter++;
        }

        return $slug;
    }

    private function slugExists(string $slug, ?int $ignoreId = null): bool
    {
        $query = JobPost::where('slug', $slug);

        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        return $query->exists();
    }
}
