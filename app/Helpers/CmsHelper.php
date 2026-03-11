<?php

namespace App\Helpers;

use App\Models\Setting;
use Illuminate\Support\Facades\Storage;

class CmsHelper
{
    /**
     * Get CMS content by key with optional default value
     *
     * @param string $key
     * @param string|null $default
     * @return string|null
     */
    public static function get(string $key, ?string $default = null): ?string
    {
        return Setting::getValue("cms_{$key}", $default);
    }

    /**
     * Set CMS content by key
     *
     * @param string $key
     * @param string|null $value
     * @return void
     */
    public static function set(string $key, ?string $value): void
    {
        Setting::updateOrCreate(
            ['key' => "cms_{$key}"],
            ['value' => $value]
        );
    }

    /**
     * Get CMS image URL
     * Returns the storage URL if the image exists, otherwise returns default or null
     *
     * @param string $key
     * @param string|null $default
     * @return string|null
     */
    public static function getImage(string $key, ?string $default = null): ?string
    {
        $imagePath = self::get($key);
        
        if (!$imagePath) {
            return $default;
        }

        // If it's already a full URL, return as is
        if (filter_var($imagePath, FILTER_VALIDATE_URL)) {
            return $imagePath;
        }

        // Check if file exists in storage
        if (Storage::disk('public')->exists($imagePath)) {
            return Storage::disk('public')->url($imagePath);
        }

        return $default;
    }

    /**
     * Get hero section title
     *
     * @return string
     */
    public static function heroTitle(): string
    {
        return self::get('hero_title', __('Find Your Dream Job Today'));
    }

    /**
     * Get hero section description
     *
     * @return string
     */
    public static function heroDescription(): string
    {
        return self::get('hero_description', __('Connecting talented professionals with trusted employers.'));
    }

    /**
     * Get site logo URL
     *
     * @return string|null
     */
    public static function logo(): ?string
    {
        return self::getImage('logo', asset('assets/images/sfi_tagline_main.png'));
    }

    /**
     * Get hero background image URL (single, kept for backward-compat)
     *
     * @return string|null
     */
    public static function heroBackground(): ?string
    {
        return self::getImage('hero_background');
    }

    /**
     * Get hero carousel slides (stored as JSON array of paths)
     * Each item: { "path": "cms/hero/xxx.jpg", "alt": "optional alt text" }
     *
     * @return array<int, array{url: string, alt: string}>
     */
    public static function heroCarouselSlides(): array
    {
        $json = self::get('hero_carousel');
        $slides = [];

        if ($json) {
            $decoded = json_decode($json, true);
            if (is_array($decoded)) {
                foreach ($decoded as $item) {
                    $path = $item['path'] ?? null;
                    if (!$path) continue;
                    $url = filter_var($path, FILTER_VALIDATE_URL)
                        ? $path
                        : (Storage::disk('public')->exists($path)
                            ? Storage::disk('public')->url($path)
                            : null);
                    if ($url) {
                        $slides[] = [
                            'url' => $url,
                            'path' => $path,
                            'alt' => $item['alt'] ?? '',
                        ];
                    }
                }
            }
        }

        return $slides;
    }

    /**
     * Get all social media platforms with their config.
     * Stored as cms_social_links JSON:
     * [{ "key":"facebook","label":"Facebook","url":"https://...","enabled":true }, ...]
     *
     * @return array
     */
    public static function socialLinks(): array
    {
        $json = self::get('social_links');

        // Default platform list (all disabled by default until admin sets a URL)
        $defaults = [
            ['key' => 'facebook',  'label' => 'Facebook',  'url' => '', 'enabled' => false],
            ['key' => 'instagram', 'label' => 'Instagram', 'url' => '', 'enabled' => false],
            ['key' => 'x',        'label' => 'X (Twitter)','url' => '', 'enabled' => false],
            ['key' => 'threads',   'label' => 'Threads',   'url' => '', 'enabled' => false],
            ['key' => 'tiktok',   'label' => 'TikTok',    'url' => '', 'enabled' => false],
            ['key' => 'youtube',   'label' => 'YouTube',   'url' => '', 'enabled' => false],
            ['key' => 'linkedin',  'label' => 'LinkedIn',  'url' => '', 'enabled' => false],
            ['key' => 'viber',    'label' => 'Viber',     'url' => '', 'enabled' => false],
            ['key' => 'telegram',  'label' => 'Telegram',  'url' => '', 'enabled' => false],
            ['key' => 'kumu',     'label' => 'Kumu',      'url' => '', 'enabled' => false],
        ];

        if (!$json) return $defaults;

        $saved = json_decode($json, true);
        if (!is_array($saved)) return $defaults;

        // Merge saved values over defaults (preserves order from defaults, fills in saved data)
        $savedByKey = collect($saved)->keyBy('key')->all();
        return array_map(function ($platform) use ($savedByKey) {
            if (isset($savedByKey[$platform['key']])) {
                return array_merge($platform, $savedByKey[$platform['key']]);
            }
            return $platform;
        }, $defaults);
    }

    /**
     * Get only enabled social links with a URL set
     *
     * @return array
     */
    public static function activeSocialLinks(): array
    {
        return array_filter(self::socialLinks(), fn($s) => !empty($s['enabled']) && !empty($s['url']));
    }

    /**
     * Get site name
     *
     * @return string
     */
    public static function siteName(): string
    {
        return self::get('site_name', config('app.name', 'Recruitment System'));
    }

    /**
     * Get features section title
     *
     * @return string
     */
    public static function featuresTitle(): string
    {
        return self::get('features_title', __('Why Choose Us'));
    }

    /**
     * Get features section description
     *
     * @return string
     */
    public static function featuresDescription(): string
    {
        return self::get('features_description', __('Everything you need to hire or get hired, in one place.'));
    }

    /**
     * Get CTA section title
     *
     * @return string
     */
    public static function ctaTitle(): string
    {
        return self::get('cta_title', __('Start Hiring or Get Hired Today'));
    }

    /**
     * Get CTA section description
     *
     * @return string
     */
    public static function ctaDescription(): string
    {
        return self::get('cta_description', __('Join thousands of professionals and companies already using our platform.'));
    }

    /**
     * Get footer description
     *
     * @return string
     */
    public static function footerDescription(): string
    {
        return self::get('footer_description', __('Connecting talented professionals with trusted employers.'));
    }

    /**
     * Get feature items (stored as JSON)
     *
     * @return array
     */
    public static function features(): array
    {
        $featuresJson = self::get('features');
        
        if ($featuresJson) {
            $decoded = json_decode($featuresJson, true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        // Default features
        return [
            [
                'title' => __('Easy Job Application'),
                'description' => __('Apply to jobs in a few clicks and track your applications in real time.'),
                'icon' => 'document',
            ],
            [
                'title' => __('Employer Dashboard'),
                'description' => __('Post jobs, manage applicants, and streamline your hiring pipeline.'),
                'icon' => 'chart',
            ],
            [
                'title' => __('Real-time Application Tracking'),
                'description' => __('See application status updates and stay in sync with employers.'),
                'icon' => 'check',
            ],
            [
                'title' => __('Secure & Verified Accounts'),
                'description' => __('Verified employers and secure document handling for your peace of mind.'),
                'icon' => 'shield',
            ],
        ];
    }

    /**
     * Get how it works section title
     *
     * @return string
     */
    public static function howItWorksTitle(): string
    {
        return self::get('how_it_works_title', __('How It Works'));
    }

    /**
     * Get how it works section description
     *
     * @return string
     */
    public static function howItWorksDescription(): string
    {
        return self::get('how_it_works_description', __('Get started in three simple steps.'));
    }

    /**
     * Get how it works steps (stored as JSON)
     *
     * @return array
     */
    public static function howItWorksSteps(): array
    {
        $stepsJson = self::get('how_it_works_steps');
        
        if ($stepsJson) {
            $decoded = json_decode($stepsJson, true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        // Default steps
        return [
            [
                'title' => __('Create Account'),
                'description' => __('Register as a job seeker or employer in minutes.'),
            ],
            [
                'title' => __('Apply or Post Jobs'),
                'description' => __('Browse and apply to jobs, or post openings and receive applications.'),
            ],
            [
                'title' => __('Get Hired / Hire Talent'),
                'description' => __('Connect, interview, and build your team or land your next role.'),
            ],
        ];
    }
}
