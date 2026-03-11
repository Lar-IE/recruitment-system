<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-900 leading-tight">
            {{ __('Create Virtual Event') }}
        </h2>
    </x-slot>

    <div class="max-w-4xl">
        <x-ui.card>
            <form method="POST" action="{{ route('employer.virtual-events.store') }}" class="space-y-6">
                @csrf

                <div>
                    <x-input-label for="title" :value="__('Event Title')" />
                    <x-text-input id="title" name="title" class="mt-1 block w-full" :value="old('title')" required />
                    <x-input-error :messages="$errors->get('title')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="description" :value="__('Event Description')" />
                    <textarea id="description" name="description" rows="5" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 focus-visible:ring-2 focus-visible:ring-offset-0">{{ old('description') }}</textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <x-input-label for="date" :value="__('Event Date')" />
                        <x-text-input id="date" name="date" type="date" class="mt-1 block w-full" :value="old('date')" required />
                        <x-input-error :messages="$errors->get('date')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="platform" :value="__('Platform')" />
                        <select id="platform" name="platform" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 focus-visible:ring-2 focus-visible:ring-offset-0" required>
                            <option value="" disabled {{ old('platform') ? '' : 'selected' }}>{{ __('Select platform') }}</option>
                            @foreach ($platforms as $key => $label)
                                <option value="{{ $key }}" {{ old('platform') === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('platform')" class="mt-2" />
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <x-input-label for="start_time" :value="__('Start Time')" />
                        <x-text-input id="start_time" name="start_time" type="time" class="mt-1 block w-full" :value="old('start_time')" required />
                        <x-input-error :messages="$errors->get('start_time')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="end_time" :value="__('End Time')" />
                        <x-text-input id="end_time" name="end_time" type="time" class="mt-1 block w-full" :value="old('end_time')" required />
                        <x-input-error :messages="$errors->get('end_time')" class="mt-2" />
                    </div>
                </div>

                <div>
                    <x-input-label for="meeting_link" :value="__('Meeting Link')" />
                    <x-text-input id="meeting_link" name="meeting_link" type="url" class="mt-1 block w-full" :value="old('meeting_link')" placeholder="https://zoom.us/j/..." required />
                    <p class="mt-1 text-sm text-gray-600">{{ __('The meeting link will only be visible to registered jobseekers.') }}</p>
                    <x-input-error :messages="$errors->get('meeting_link')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="registration_deadline" :value="__('Registration Deadline (Optional)')" />
                    <x-text-input id="registration_deadline" name="registration_deadline" type="datetime-local" class="mt-1 block w-full" :value="old('registration_deadline')" />
                    <p class="mt-1 text-sm text-gray-600">{{ __('If set, jobseekers cannot register after this date/time.') }}</p>
                    <x-input-error :messages="$errors->get('registration_deadline')" class="mt-2" />
                </div>

                <div class="flex flex-wrap items-center gap-3 pt-2">
                    <x-primary-button>{{ __('Create Event') }}</x-primary-button>
                    <a href="{{ route('employer.virtual-events.index') }}" class="inline-flex items-center px-4 py-2 rounded-lg border border-gray-300 bg-white text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2">
                        {{ __('Cancel') }}
                    </a>
                </div>
            </form>
        </x-ui.card>
    </div>
</x-app-layout>
