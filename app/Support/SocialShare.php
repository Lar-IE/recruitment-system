<?php

namespace App\Support;

use App\Models\JobPost;
use Illuminate\Support\Str;

class SocialShare
{
    /**
     * Build the shareable public URL for a job post (absolute URL for share intents and OG).
     */
    public static function publicJobUrl(JobPost $jobPost): string
    {
        return url(route('jobs.public.show', ['slug' => $jobPost->slug]));
    }

    /**
     * Short description for Open Graph / social previews (single line, ~160 chars).
     */
    public static function ogDescription(JobPost $jobPost): string
    {
        $jobPost->loadMissing('employer.companyProfile');

        $company = $jobPost->employer?->companyProfile?->company_name
            ?? $jobPost->employer?->company_name
            ?? __('Company');

        $parts = [];
        $parts[] = $jobPost->title;
        $parts[] = $company;
        $desc = trim((string) ($jobPost->description ?? ''));
        if ($desc !== '') {
            $parts[] = Str::limit(preg_replace('/\s+/', ' ', $desc), 120);
        }

        return implode(' · ', $parts);
    }

    /**
     * Build a human-friendly share text (can be copied or used by share intents).
     */
    public static function shareText(JobPost $jobPost): string
    {
        $jobPost->loadMissing('employer.companyProfile');

        $company = $jobPost->employer?->companyProfile?->company_name
            ?? $jobPost->employer?->company_name
            ?? __('Company');

        $lines = [];
        $lines[] = __('Job: :title', ['title' => $jobPost->title]);
        $lines[] = __('Company: :company', ['company' => $company]);

        $desc = trim((string) ($jobPost->description ?? ''));
        if ($desc !== '') {
            $lines[] = Str::limit(preg_replace('/\s+/', ' ', $desc), 140);
        }

        $salary = self::salaryLine($jobPost);
        if ($salary !== null) {
            $lines[] = $salary;
        }

        $lines[] = __('View details: :url', ['url' => self::publicJobUrl($jobPost)]);

        return implode("\n", $lines);
    }

    /**
     * Single-line text for Twitter/X (pre-filled tweet, ~240 chars to leave room for URL).
     */
    public static function tweetText(JobPost $jobPost): string
    {
        $jobPost->loadMissing('employer.companyProfile');
        $company = $jobPost->employer?->companyProfile?->company_name
            ?? $jobPost->employer?->company_name
            ?? __('Company');
        $line = $jobPost->title . ' at ' . $company;
        $desc = trim((string) ($jobPost->description ?? ''));
        if ($desc !== '') {
            $line .= ' – ' . Str::limit(preg_replace('/\s+/', ' ', $desc), 120);
        }
        return $line;
    }

    /**
     * Icon-only share platforms for the action bar (FB, IG, X, Threads).
     * Uses absolute job URL and platform-specific text. Call with url = publicJobUrl($jobPost).
     *
     * @return array<int, array{key:string,label:string,intent_url:?string,copy_only:bool}>
     */
    public static function shareIcons(JobPost $jobPost): array
    {
        $url = self::publicJobUrl($jobPost);
        $encodedUrl = rawurlencode($url);
        $shareText = self::shareText($jobPost);
        $encodedShareText = rawurlencode($shareText);
        $tweetText = self::tweetText($jobPost);
        $encodedTweetText = rawurlencode($tweetText);

        return [
            [
                'key' => 'facebook',
                'label' => __('Share on Facebook'),
                'intent_url' => "https://www.facebook.com/sharer/sharer.php?u={$encodedUrl}",
                'copy_only' => false,
            ],
            [
                'key' => 'x',
                'label' => __('Share on X'),
                'intent_url' => "https://twitter.com/intent/tweet?url={$encodedUrl}&text={$encodedTweetText}",
                'copy_only' => false,
            ],
            [
                'key' => 'threads',
                'label' => __('Share on Threads'),
                'intent_url' => null,
                'copy_only' => true,
            ],
            [
                'key' => 'instagram',
                'label' => __('Share on Instagram'),
                'intent_url' => null,
                'copy_only' => true,
            ],
        ];
    }

    /**
     * Data for share modal: title, company, description, salary, url, share_text.
     * All from database via job post; use for displaying and copying.
     *
     * @return array{title:string, company_name:string, description_short:string, salary_line:?string, public_url:string, share_text:string}
     */
    public static function sharePayload(JobPost $jobPost): array
    {
        $jobPost->loadMissing('employer.companyProfile');
        $company = $jobPost->employer?->companyProfile?->company_name
            ?? $jobPost->employer?->company_name
            ?? __('Company');
        $desc = trim((string) ($jobPost->description ?? ''));
        $descriptionShort = $desc !== '' ? Str::limit(preg_replace('/\s+/', ' ', $desc), 200) : '';

        return [
            'title' => $jobPost->title,
            'company_name' => $company,
            'description_short' => $descriptionShort,
            'salary_line' => self::salaryLine($jobPost),
            'public_url' => self::publicJobUrl($jobPost),
            'share_text' => self::shareText($jobPost),
        ];
    }

    /**
     * @return array<int, array{key:string,label:string,intent_url:?string,copy_only:bool,help:?string}>
     */
    public static function platforms(string $url, string $text): array
    {
        $encodedUrl = rawurlencode($url);
        $encodedText = rawurlencode($text);

        return [
            [
                'key' => 'facebook',
                'label' => 'Facebook',
                'intent_url' => "https://www.facebook.com/sharer/sharer.php?u={$encodedUrl}",
                'copy_only' => false,
                'help' => null,
            ],
            [
                'key' => 'x',
                'label' => 'X (Twitter)',
                'intent_url' => "https://twitter.com/intent/tweet?url={$encodedUrl}&text={$encodedText}",
                'copy_only' => false,
                'help' => null,
            ],
            [
                'key' => 'linkedin',
                'label' => 'LinkedIn',
                'intent_url' => "https://www.linkedin.com/sharing/share-offsite/?url={$encodedUrl}",
                'copy_only' => false,
                'help' => null,
            ],
            [
                'key' => 'whatsapp',
                'label' => 'WhatsApp',
                'intent_url' => "https://api.whatsapp.com/send?text={$encodedText}%20{$encodedUrl}",
                'copy_only' => false,
                'help' => null,
            ],
            [
                'key' => 'threads',
                'label' => 'Threads',
                'intent_url' => null,
                'copy_only' => true,
                'help' => __('Threads does not support a reliable web share dialog. Use Copy Text/Link then paste into Threads.'),
            ],
            [
                'key' => 'instagram',
                'label' => 'Instagram',
                'intent_url' => null,
                'copy_only' => true,
                'help' => __('Instagram does not support direct web sharing. Use Copy Text/Link then paste into Instagram.'),
            ],
        ];
    }

    public static function salaryLine(JobPost $jobPost): ?string
    {
        $type = $jobPost->salary_type ?? 'salary_range';
        $currency = $jobPost->currency ?: 'PHP';

        if ($type === 'daily_rate' && $jobPost->salary_daily !== null) {
            return __('Rate per Day: :amount :currency', [
                'amount' => number_format((float) $jobPost->salary_daily, 2),
                'currency' => $currency,
            ]);
        }

        if ($type === 'fixed' && $jobPost->salary_monthly !== null) {
            return __('Monthly Rate: :amount :currency', [
                'amount' => number_format((float) $jobPost->salary_monthly, 2),
                'currency' => $currency,
            ]);
        }

        if ($jobPost->salary_min !== null || $jobPost->salary_max !== null) {
            return __('Salary: :min - :max :currency', [
                'min' => $jobPost->salary_min !== null ? number_format((float) $jobPost->salary_min, 2) : '-',
                'max' => $jobPost->salary_max !== null ? number_format((float) $jobPost->salary_max, 2) : '-',
                'currency' => $currency,
            ]);
        }

        return null;
    }
}

