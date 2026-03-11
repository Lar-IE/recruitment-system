@php
    use App\Helpers\CmsHelper;
@endphp
<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-900 leading-tight">
            {{ __('CMS Management') }}
        </h2>
    </x-slot>

    <div class="space-y-6">
        @if (session('success'))
            <div class="rounded-xl bg-green-50 p-4 text-sm text-green-700 border border-green-100">
                {{ session('success') }}
            </div>
        @endif

        <x-ui.card>
            <form method="POST" action="{{ route('admin.cms.update') }}" enctype="multipart/form-data" class="space-y-8">
                @csrf

                {{-- ── Site Identity ──────────────────────────────────────── --}}
                <div class="space-y-6 border-b border-gray-200 pb-8">
                    <h3 class="text-lg font-semibold text-gray-900">{{ __('Site Identity') }}</h3>

                    <div>
                        <x-input-label for="site_name" :value="__('Site Name')" />
                        <x-text-input id="site_name" name="site_name" class="mt-1 block w-full"
                            :value="old('site_name', $cms['site_name'] ?? config('app.name'))" required />
                        <x-input-error :messages="$errors->get('site_name')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="logo" :value="__('Logo')" />
                        @if(($cms['logo'] ?? null) && CmsHelper::getImage('logo'))
                            <div class="mt-2 flex items-center gap-4">
                                <img src="{{ CmsHelper::getImage('logo') }}" alt="Logo" class="h-16 w-auto object-contain">
                                <label for="remove_logo" class="inline-flex items-center gap-2 text-sm text-red-600 hover:text-red-700 cursor-pointer">
                                    <input type="checkbox" id="remove_logo" name="remove_logo" value="1"
                                        class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                                    {{ __('Remove logo') }}
                                </label>
                            </div>
                        @endif
                        <x-text-input id="logo" name="logo" type="file" accept="image/*" class="mt-1 block w-full" />
                        <p class="mt-1 text-sm text-gray-500">{{ __('PNG, JPG, SVG, WebP – Max 2 MB') }}</p>
                        <x-input-error :messages="$errors->get('logo')" class="mt-2" />
                    </div>
                </div>

                {{-- ── Hero Section ────────────────────────────────────────── --}}
                <div class="space-y-6 border-b border-gray-200 pb-8" x-data="{ newSlideCount: 1 }">
                    <h3 class="text-lg font-semibold text-gray-900">{{ __('Hero Section') }}</h3>

                    <div>
                        <x-input-label for="hero_title" :value="__('Hero Title')" />
                        <x-text-input id="hero_title" name="hero_title" class="mt-1 block w-full"
                            :value="old('hero_title', $cms['hero_title'] ?? __('Find Your Dream Job Today'))" required />
                        <x-input-error :messages="$errors->get('hero_title')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="hero_description" :value="__('Hero Description')" />
                        <textarea id="hero_description" name="hero_description" rows="3"
                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>{{ old('hero_description', $cms['hero_description'] ?? __('Connecting talented professionals with trusted employers.')) }}</textarea>
                        <x-input-error :messages="$errors->get('hero_description')" class="mt-2" />
                    </div>

                    {{-- Carousel Slides Manager --}}
                    <div>
                        <x-input-label :value="__('Hero Carousel Images')" />
                        <p class="mt-1 text-sm text-gray-500">
                            {{ __('Upload one or more background images. They will auto-rotate on the landing page. If no images are uploaded, a dark gradient is used.') }}
                        </p>

                        {{-- Recommended size guide (tooltip) --}}
                        <div
                            class="relative mt-2 inline-flex"
                            x-data="{ open: false }"
                            @mouseenter="open = true"
                            @mouseleave="open = false"
                            @click.stop="open = !open"
                            @click.outside="open = false"
                        >
                            {{-- Info icon trigger --}}
                            <button type="button"
                                class="inline-flex items-center justify-center w-6 h-6 rounded-full text-blue-500 hover:text-blue-700 hover:bg-blue-50 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
                                :aria-expanded="open"
                                aria-label="{{ __('Image specifications') }}"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20A10 10 0 0012 2z"/>
                                </svg>
                            </button>

                            {{-- Tooltip panel --}}
                            <div
                                x-show="open"
                                x-transition:enter="transition ease-out duration-150"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-100"
                                x-transition:leave-start="opacity-100 scale-100"
                                x-transition:leave-end="opacity-0 scale-95"
                                x-cloak
                                class="absolute left-7 top-0 z-50 w-72 rounded-xl border border-blue-100 bg-blue-50 p-4 shadow-lg"
                            >
                                <p class="text-sm font-semibold text-blue-800 mb-2">{{ __('Recommended Image Specifications') }}</p>
                                <ul class="space-y-1 text-xs text-blue-700 list-disc list-inside">
                                    <li>{{ __('Resolution: 1920 × 780 px or wider') }}</li>
                                    <li>{{ __('Minimum width: 1280 px') }}</li>
                                    <li>{{ __('Aspect ratio: 16:9 or wider (panoramic)') }}</li>
                                    <li>{{ __('Format: JPG or WebP for best performance') }}</li>
                                    <li>{{ __('Max file size: 5 MB per image') }}</li>
                                    <li>{{ __('Keep subjects centered — edges may crop on mobile') }}</li>
                                </ul>
                            </div>
                        </div>

                        {{-- Existing slides --}}
                        @if(!empty($cms['hero_carousel']))
                            <div class="mt-4 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                                @foreach($cms['hero_carousel'] as $slide)
                                    <div class="relative group rounded-xl overflow-hidden border border-gray-200 bg-gray-50">
                                        <img src="{{ $slide['url'] }}" alt="{{ $slide['alt'] }}"
                                            class="w-full h-28 object-cover">
                                        {{-- Alt text --}}
                                        <div class="p-2">
                                            <input type="text"
                                                name="hero_carousel_alts_existing[{{ $slide['path'] }}]"
                                                value="{{ $slide['alt'] }}"
                                                placeholder="{{ __('Alt text (optional)') }}"
                                                class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 px-2 py-1">
                                        </div>
                                        {{-- Remove checkbox --}}
                                        <label class="absolute top-1.5 right-1.5 flex items-center gap-1 bg-white/90 text-red-600 text-xs font-medium rounded-lg px-2 py-1 cursor-pointer shadow-sm hover:bg-red-50 transition">
                                            <input type="checkbox"
                                                name="hero_carousel_remove[]"
                                                value="{{ $slide['path'] }}"
                                                class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                                            {{ __('Remove') }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        {{-- Upload new slides --}}
                        <div class="mt-4 space-y-3">
                            <p class="text-sm font-medium text-gray-700">{{ __('Add New Slides') }}</p>
                            <template x-for="i in newSlideCount" :key="i">
                                <div class="flex gap-3 items-start">
                                    <input type="file" :name="'hero_carousel_new[' + (i - 1) + ']'"
                                        accept="image/jpeg,image/png,image/gif,image/webp"
                                        class="block flex-1 rounded-lg border border-gray-300 text-sm file:mr-3 file:rounded-l-lg file:border-0 file:bg-gray-100 file:px-3 file:py-2 file:text-sm file:font-medium hover:file:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                    <input type="text" :name="'hero_carousel_alts_new[' + (i - 1) + ']'"
                                        placeholder="{{ __('Alt text (optional)') }}"
                                        class="w-40 rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 px-3 py-2">
                                </div>
                            </template>
                            <button type="button" @click="newSlideCount++"
                                class="inline-flex items-center gap-1.5 text-sm font-medium text-indigo-600 hover:text-indigo-800">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                {{ __('Add another slide') }}
                            </button>
                        </div>
                        <p class="mt-1 text-sm text-gray-500">{{ __('PNG, JPG, WebP, GIF – Max 5 MB each') }}</p>
                        <x-input-error :messages="$errors->get('hero_carousel_new.*')" class="mt-2" />
                    </div>
                </div>

                {{-- ── Features Section ───────────────────────────────────── --}}
                <div class="space-y-6 border-b border-gray-200 pb-8">
                    <h3 class="text-lg font-semibold text-gray-900">{{ __('Features Section') }}</h3>

                    <div>
                        <x-input-label for="features_title" :value="__('Features Title')" />
                        <x-text-input id="features_title" name="features_title" class="mt-1 block w-full"
                            :value="old('features_title', $cms['features_title'] ?? __('Why Choose Us'))" required />
                        <x-input-error :messages="$errors->get('features_title')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="features_description" :value="__('Features Description')" />
                        <textarea id="features_description" name="features_description" rows="2"
                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>{{ old('features_description', $cms['features_description'] ?? __('Everything you need to hire or get hired, in one place.')) }}</textarea>
                        <x-input-error :messages="$errors->get('features_description')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label :value="__('Features (JSON)')" />
                        <p class="text-sm text-gray-500 mb-2">{{ __('Format: [{"title":"...","description":"...","icon":"document|chart|check|shield"}]') }}</p>
                        <textarea id="features" name="features" rows="8"
                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-mono text-sm">{{ old('features', json_encode($cms['features'] ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) }}</textarea>
                        <x-input-error :messages="$errors->get('features')" class="mt-2" />
                    </div>
                </div>

                {{-- ── How It Works ────────────────────────────────────────── --}}
                <div class="space-y-6 border-b border-gray-200 pb-8">
                    <h3 class="text-lg font-semibold text-gray-900">{{ __('How It Works Section') }}</h3>

                    <div>
                        <x-input-label for="how_it_works_title" :value="__('How It Works Title')" />
                        <x-text-input id="how_it_works_title" name="how_it_works_title" class="mt-1 block w-full"
                            :value="old('how_it_works_title', $cms['how_it_works_title'] ?? __('How It Works'))" required />
                        <x-input-error :messages="$errors->get('how_it_works_title')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="how_it_works_description" :value="__('How It Works Description')" />
                        <textarea id="how_it_works_description" name="how_it_works_description" rows="2"
                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>{{ old('how_it_works_description', $cms['how_it_works_description'] ?? __('Get started in three simple steps.')) }}</textarea>
                        <x-input-error :messages="$errors->get('how_it_works_description')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label :value="__('How It Works Steps (JSON)')" />
                        <p class="text-sm text-gray-500 mb-2">{{ __('Format: [{"title":"...","description":"..."}]') }}</p>
                        <textarea id="how_it_works_steps" name="how_it_works_steps" rows="6"
                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-mono text-sm">{{ old('how_it_works_steps', json_encode($cms['how_it_works_steps'] ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) }}</textarea>
                        <x-input-error :messages="$errors->get('how_it_works_steps')" class="mt-2" />
                    </div>
                </div>

                {{-- ── Call to Action ──────────────────────────────────────── --}}
                <div class="space-y-6 border-b border-gray-200 pb-8">
                    <h3 class="text-lg font-semibold text-gray-900">{{ __('Call to Action Section') }}</h3>

                    <div>
                        <x-input-label for="cta_title" :value="__('CTA Title')" />
                        <x-text-input id="cta_title" name="cta_title" class="mt-1 block w-full"
                            :value="old('cta_title', $cms['cta_title'] ?? __('Start Hiring or Get Hired Today'))" required />
                        <x-input-error :messages="$errors->get('cta_title')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="cta_description" :value="__('CTA Description')" />
                        <textarea id="cta_description" name="cta_description" rows="2"
                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>{{ old('cta_description', $cms['cta_description'] ?? __('Join thousands of professionals and companies already using our platform.')) }}</textarea>
                        <x-input-error :messages="$errors->get('cta_description')" class="mt-2" />
                    </div>
                </div>

                {{-- ── Footer Section ──────────────────────────────────────── --}}
                <div class="space-y-6 border-b border-gray-200 pb-8">
                    <h3 class="text-lg font-semibold text-gray-900">{{ __('Footer Section') }}</h3>

                    <div>
                        <x-input-label for="footer_description" :value="__('Footer Description')" />
                        <textarea id="footer_description" name="footer_description" rows="2"
                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>{{ old('footer_description', $cms['footer_description'] ?? __('Connecting talented professionals with trusted employers.')) }}</textarea>
                        <x-input-error :messages="$errors->get('footer_description')" class="mt-2" />
                    </div>
                </div>

                {{-- ── Social Media Links ──────────────────────────────────── --}}
                <div class="space-y-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">{{ __('Social Media Links') }}</h3>
                        <p class="mt-1 text-sm text-gray-500">{{ __('Enable a platform and enter its URL to show the icon in the footer. Leave the URL blank or disable to hide it.') }}</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        @foreach($cms['social_links'] as $platform)
                            @php $key = $platform['key']; @endphp
                            <div class="flex items-start gap-3 p-4 rounded-xl border border-gray-200 bg-gray-50/50">
                                {{-- Platform icon --}}
                                <div class="mt-0.5 shrink-0 w-8 h-8 flex items-center justify-center rounded-lg bg-white border border-gray-200 text-gray-600">
                                    @include('admin.cms._social-icon', ['key' => $key])
                                </div>
                                <div class="flex-1 min-w-0 space-y-2">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-semibold text-gray-800">{{ $platform['label'] }}</span>
                                        <label class="inline-flex items-center gap-1.5 text-sm text-gray-600 cursor-pointer select-none">
                                            <input type="checkbox"
                                                name="social_enabled[{{ $key }}]"
                                                value="1"
                                                {{ old("social_enabled.{$key}", $platform['enabled'] ? '1' : '') ? 'checked' : '' }}
                                                class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                            {{ __('Enabled') }}
                                        </label>
                                    </div>
                                    <input type="url"
                                        name="social_url[{{ $key }}]"
                                        value="{{ old("social_url.{$key}", $platform['url']) }}"
                                        placeholder="https://..."
                                        class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-3 py-2">
                                    <x-input-error :messages="$errors->get('social_url.' . $key)" class="mt-1" />
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                    <a href="{{ route('admin.settings') }}"
                        class="inline-flex items-center px-4 py-2 rounded-lg border border-gray-300 bg-white text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2">
                        {{ __('Cancel') }}
                    </a>
                    <x-primary-button>{{ __('Save CMS Content') }}</x-primary-button>
                </div>
            </form>
        </x-ui.card>
    </div>
</x-app-layout>
