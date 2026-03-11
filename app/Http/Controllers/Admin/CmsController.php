<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\CmsHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CmsController extends Controller
{
    /**
     * Display the CMS management page
     */
    public function index(): View
    {
        return view('admin.cms.index', [
            'cms' => [
                'site_name'              => CmsHelper::get('site_name', config('app.name')),
                'logo'                   => CmsHelper::get('logo'),
                'hero_title'             => CmsHelper::get('hero_title', __('Find Your Dream Job Today')),
                'hero_description'       => CmsHelper::get('hero_description', __('Connecting talented professionals with trusted employers.')),
                'hero_carousel'          => CmsHelper::heroCarouselSlides(),
                'features_title'         => CmsHelper::get('features_title', __('Why Choose Us')),
                'features_description'   => CmsHelper::get('features_description', __('Everything you need to hire or get hired, in one place.')),
                'features'               => CmsHelper::features(),
                'how_it_works_title'     => CmsHelper::get('how_it_works_title', __('How It Works')),
                'how_it_works_description' => CmsHelper::get('how_it_works_description', __('Get started in three simple steps.')),
                'how_it_works_steps'     => CmsHelper::howItWorksSteps(),
                'cta_title'              => CmsHelper::get('cta_title', __('Start Hiring or Get Hired Today')),
                'cta_description'        => CmsHelper::get('cta_description', __('Join thousands of professionals and companies already using our platform.')),
                'footer_description'     => CmsHelper::get('footer_description', __('Connecting talented professionals with trusted employers.')),
                'social_links'           => CmsHelper::socialLinks(),
            ],
        ]);
    }

    /**
     * Update CMS content
     */
    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'site_name'                  => ['required', 'string', 'max:255'],
            'logo'                       => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:2048'],
            'hero_title'                 => ['required', 'string', 'max:255'],
            'hero_description'           => ['required', 'string', 'max:500'],
            'hero_carousel_new.*'        => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'],
            'hero_carousel_alts.*'       => ['nullable', 'string', 'max:255'],
            'features_title'             => ['required', 'string', 'max:255'],
            'features_description'       => ['required', 'string', 'max:500'],
            'features'                   => ['nullable', 'json'],
            'how_it_works_title'         => ['required', 'string', 'max:255'],
            'how_it_works_description'   => ['required', 'string', 'max:500'],
            'how_it_works_steps'         => ['nullable', 'json'],
            'cta_title'                  => ['required', 'string', 'max:255'],
            'cta_description'            => ['required', 'string', 'max:500'],
            'footer_description'         => ['required', 'string', 'max:500'],
            'remove_logo'                => ['nullable', 'boolean'],
            'social_url.*'               => ['nullable', 'url', 'max:512'],
            'social_enabled.*'           => ['nullable', 'boolean'],
        ]);

        // ── Logo ──────────────────────────────────────────────────────────
        if ($request->hasFile('logo')) {
            $old = CmsHelper::get('logo');
            if ($old && Storage::disk('public')->exists($old)) Storage::disk('public')->delete($old);
            CmsHelper::set('logo', $request->file('logo')->store('cms/logo', 'public'));
        } elseif ($request->boolean('remove_logo')) {
            $old = CmsHelper::get('logo');
            if ($old && Storage::disk('public')->exists($old)) Storage::disk('public')->delete($old);
            CmsHelper::set('logo', null);
        }

        // ── Hero Carousel ─────────────────────────────────────────────────
        // Load existing slides
        $existingRaw  = CmsHelper::get('hero_carousel');
        $existingSlides = $existingRaw ? (json_decode($existingRaw, true) ?: []) : [];

        // Remove slides marked for deletion
        $removeSlides = $request->input('hero_carousel_remove', []);
        $keptSlides = [];
        foreach ($existingSlides as $slide) {
            if (in_array($slide['path'] ?? '', $removeSlides, true)) {
                // Delete file from storage
                if (Storage::disk('public')->exists($slide['path'])) {
                    Storage::disk('public')->delete($slide['path']);
                }
            } else {
                $keptSlides[] = $slide;
            }
        }

        // Update alts for kept slides
        $keptAlts = $request->input('hero_carousel_alts_existing', []);
        foreach ($keptSlides as $i => &$slide) {
            $path = $slide['path'];
            if (isset($keptAlts[$path])) {
                $slide['alt'] = $keptAlts[$path];
            }
        }
        unset($slide);

        // Upload new slides
        if ($request->hasFile('hero_carousel_new')) {
            $newAlts = $request->input('hero_carousel_alts_new', []);
            foreach ($request->file('hero_carousel_new') as $idx => $file) {
                if ($file && $file->isValid()) {
                    $path = $file->store('cms/hero', 'public');
                    $keptSlides[] = [
                        'path' => $path,
                        'alt'  => $newAlts[$idx] ?? '',
                    ];
                }
            }
        }

        CmsHelper::set('hero_carousel', json_encode(array_values($keptSlides)));

        // ── Text content ──────────────────────────────────────────────────
        CmsHelper::set('site_name', $request->input('site_name'));
        CmsHelper::set('hero_title', $request->input('hero_title'));
        CmsHelper::set('hero_description', $request->input('hero_description'));
        CmsHelper::set('features_title', $request->input('features_title'));
        CmsHelper::set('features_description', $request->input('features_description'));
        CmsHelper::set('how_it_works_title', $request->input('how_it_works_title'));
        CmsHelper::set('how_it_works_description', $request->input('how_it_works_description'));
        CmsHelper::set('cta_title', $request->input('cta_title'));
        CmsHelper::set('cta_description', $request->input('cta_description'));
        CmsHelper::set('footer_description', $request->input('footer_description'));

        if ($request->has('features')) {
            CmsHelper::set('features', $request->input('features'));
        }
        if ($request->has('how_it_works_steps')) {
            CmsHelper::set('how_it_works_steps', $request->input('how_it_works_steps'));
        }

        // ── Social Media Links ────────────────────────────────────────────
        $allPlatforms = CmsHelper::socialLinks(); // get ordered list with defaults
        $urlInputs     = $request->input('social_url', []);
        $enabledInputs = $request->input('social_enabled', []);

        $socialData = array_map(function ($platform) use ($urlInputs, $enabledInputs) {
            $key = $platform['key'];
            return [
                'key'     => $key,
                'label'   => $platform['label'],
                'url'     => $urlInputs[$key] ?? '',
                'enabled' => isset($enabledInputs[$key]) && $enabledInputs[$key] ? true : false,
            ];
        }, $allPlatforms);

        CmsHelper::set('social_links', json_encode($socialData));

        return redirect()->route('admin.cms.index')
            ->with('success', __('CMS content updated successfully.'));
    }
}
