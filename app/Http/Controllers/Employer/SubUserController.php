<?php

namespace App\Http\Controllers\Employer;

use App\Enums\EmployerSubUserRole;
use App\Http\Controllers\Controller;
use App\Models\EmployerSubUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SubUserController extends Controller
{
    public function index(Request $request): View
    {
        $employer = $request->user()->employer;

        $subUsers = $employer->subUsers()
            ->latest()
            ->paginate(10);

        return view('employer.sub-users.index', [
            'subUsers' => $subUsers,
        ]);
    }

    public function create(): View
    {
        return view('employer.sub-users.create', [
            'roles' => EmployerSubUserRole::labels(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $employer = $request->user()->employer;

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:employer_sub_users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', Rule::in(EmployerSubUserRole::values())],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        $data['employer_id'] = $employer->id;

        EmployerSubUser::create($data);

        return redirect()->route('employer.sub-users.index')
            ->with('success', __('Sub-user created.'));
    }

    public function edit(Request $request, EmployerSubUser $subUser): View
    {
        $subUser = $this->findEmployerSubUser($request, $subUser->id);

        return view('employer.sub-users.edit', [
            'subUser' => $subUser,
            'roles' => EmployerSubUserRole::labels(),
        ]);
    }

    public function update(Request $request, EmployerSubUser $subUser): RedirectResponse
    {
        $subUser = $this->findEmployerSubUser($request, $subUser->id);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('employer_sub_users', 'email')->ignore($subUser->id),
            ],
            'password' => ['nullable', 'string', 'min:8'],
            'role' => ['required', Rule::in(EmployerSubUserRole::values())],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $subUser->update($data);

        return redirect()->route('employer.sub-users.index')
            ->with('success', __('Sub-user updated.'));
    }

    public function toggleStatus(Request $request, EmployerSubUser $subUser): RedirectResponse
    {
        $subUser = $this->findEmployerSubUser($request, $subUser->id);
        $nextStatus = $subUser->status === 'active' ? 'inactive' : 'active';

        $subUser->update([
            'status' => $nextStatus,
        ]);

        return redirect()->route('employer.sub-users.index')
            ->with('success', __('Sub-user status updated.'));
    }

    public function destroy(Request $request, EmployerSubUser $subUser): RedirectResponse
    {
        $subUser = $this->findEmployerSubUser($request, $subUser->id);
        $subUser->delete();

        return redirect()->route('employer.sub-users.index')
            ->with('success', __('Sub-user deleted.'));
    }

    private function findEmployerSubUser(Request $request, int $subUserId): EmployerSubUser
    {
        $employer = $request->user()->employer;

        return EmployerSubUser::where('employer_id', $employer->id)
            ->where('id', $subUserId)
            ->firstOrFail();
    }
}
