<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function index(): View
    {
        $settings = Setting::pluck('value', 'key');

        return view('admin.settings.index', [
            'settings' => $settings,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'site_name' => ['required', 'string', 'max:255'],
            'support_email' => ['required', 'email', 'max:255'],
            'allow_employer_registration' => ['nullable', 'boolean'],
            'maintenance_mode' => ['nullable', 'boolean'],
        ]);

        $this->save('site_name', $validated['site_name']);
        $this->save('support_email', $validated['support_email']);
        $this->save('allow_employer_registration', (string) ($validated['allow_employer_registration'] ?? false));
        $this->save('maintenance_mode', (string) ($validated['maintenance_mode'] ?? false));

        return redirect()->route('admin.settings')
            ->with('success', __('Settings saved.'));
    }

    public function toggleMaintenance(): RedirectResponse
    {
        $enabled = Setting::getValue('maintenance_mode', '0') === '1';
        $this->save('maintenance_mode', $enabled ? '0' : '1');

        return back()->with('success', $enabled
            ? __('Maintenance mode disabled.')
            : __('Maintenance mode enabled.'));
    }

    private function save(string $key, string $value): void
    {
        Setting::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }
}
