<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Digital ID') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if ($digitalId)
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold">{{ __('Digital ID Preview') }}</h3>
                            <span class="text-xs px-2 py-1 rounded-full bg-gray-100 text-gray-700">
                                {{ ucfirst($digitalId->status) }}
                            </span>
                        </div>

                        @if ($digitalId->status !== 'active')
                            <div class="rounded-md bg-yellow-50 p-4 text-sm text-yellow-800">
                                {{ __('Your digital ID is inactive. Please contact your employer.') }}
                            </div>
                        @else
                        @php
                            $verifyUrl = route('digital-ids.verify', $digitalId->public_token);
                            $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=120x120&data='.urlencode($verifyUrl);
                            $photoUrl = $digitalId->photo_path ? asset('storage/'.$digitalId->photo_path) : null;
                        @endphp

                        <div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <div class="space-y-3" x-data="{ side: 'front' }">
                                <div class="flex items-center justify-between text-sm text-gray-600">
                                    <span class="font-semibold">{{ __('Preview') }}</span>
                                    <button type="button" class="text-indigo-600 hover:text-indigo-900" x-on:click="side = side === 'front' ? 'back' : 'front'">
                                        <span x-show="side === 'front'">{{ __('View Back') }}</span>
                                        <span x-show="side === 'back'">{{ __('View Front') }}</span>
                                    </button>
                                </div>

                                <div x-show="side === 'front'" class="relative overflow-hidden rounded-xl border bg-gradient-to-br from-slate-900 via-slate-800 to-indigo-900 text-white shadow-lg aspect-[3/4]">
                                    <div class="p-6 space-y-4">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-xs uppercase tracking-widest text-indigo-200">{{ __('Employee Digital ID') }}</p>
                                            <h4 class="text-lg font-semibold">{{ $digitalId->company_name }}</h4>
                                        </div>
                                        <div class="h-12 w-12 rounded-full bg-indigo-500/40 flex items-center justify-center text-xs font-semibold">
                                            {{ __('ID') }}
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-4">
                                        <div class="h-20 w-20 rounded-lg overflow-hidden border border-white/30 bg-white/10 flex items-center justify-center text-xs uppercase tracking-widest">
                                            @if ($photoUrl)
                                                <img src="{{ $photoUrl }}" alt="{{ __('Photo') }}" class="h-full w-full object-cover">
                                            @else
                                                {{ __('Photo') }}
                                            @endif
                                        </div>
                                        <div>
                                            <p class="text-sm text-indigo-200">{{ __('Name') }}</p>
                                            <p class="text-lg font-semibold">{{ Auth::user()->name }}</p>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-4 text-sm">
                                        <div>
                                            <p class="text-indigo-200">{{ __('Job Title') }}</p>
                                            <p class="font-semibold">{{ $digitalId->job_title }}</p>
                                        </div>
                                        <div>
                                            <p class="text-indigo-200">{{ __('Employee ID') }}</p>
                                            <p class="font-semibold">{{ $digitalId->employee_identifier }}</p>
                                        </div>
                                        <div>
                                            <p class="text-indigo-200">{{ __('Issue Date') }}</p>
                                            <p class="font-semibold">{{ $digitalId->issue_date?->format('M d, Y') }}</p>
                                        </div>
                                        <div>
                                            <p class="text-indigo-200">{{ __('Status') }}</p>
                                            <p class="font-semibold">{{ ucfirst($digitalId->status) }}</p>
                                        </div>
                                    </div>
                                    </div>
                                    <div class="absolute right-4 bottom-4 text-right space-y-2">
                                        <img src="{{ $qrUrl }}" alt="{{ __('QR Code') }}" class="h-20 w-20 rounded-md border border-white/30 bg-white">
                                        <a href="{{ $verifyUrl }}" target="_blank" class="inline-flex items-center justify-center rounded-md bg-white/90 px-2 py-1 text-[10px] font-semibold text-indigo-700 hover:bg-white">
                                            {{ __('Open Verification Page') }}
                                        </a>
                                    </div>
                                </div>

                                <div x-show="side === 'back'" class="relative overflow-hidden rounded-xl border bg-gradient-to-br from-slate-900 via-slate-800 to-indigo-900 text-white shadow-lg aspect-[3/4]">
                                    <div class="p-6 space-y-4">
                                        <p class="text-xs uppercase tracking-widest text-indigo-200">{{ __('Digital ID Back') }}</p>
                                        <div class="space-y-2 text-sm text-indigo-100">
                                            <p>{{ __('Company: :company', ['company' => $digitalId->company_name]) }}</p>
                                            <p>{{ __('Employee ID: :id', ['id' => $digitalId->employee_identifier]) }}</p>
                                            <p>{{ __('Issued: :date', ['date' => $digitalId->issue_date?->format('M d, Y')]) }}</p>
                                        </div>
                                        <div class="mt-6 rounded-md border border-white/30 bg-white/10 p-4 text-xs text-indigo-100">
                                            {{ __('This digital ID is system generated. If found, please return to the issuing employer.') }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="border rounded-lg p-4 space-y-4">
                                <h4 class="font-semibold">{{ __('Upload Your Photo') }}</h4>
                                @if (session('success'))
                                    <p class="text-sm text-green-600">{{ session('success') }}</p>
                                @endif
                                <form method="POST" action="{{ route('jobseeker.digital-id.photo') }}" enctype="multipart/form-data" class="space-y-3">
                                    @csrf
                                    <input type="file" name="photo" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" required>
                                    <x-input-error :messages="$errors->get('photo')" class="mt-1" />
                                    <x-primary-button>{{ $photoUrl ? __('Update Photo') : __('Upload Photo') }}</x-primary-button>
                                </form>
                                <p class="text-xs text-gray-500">{{ __('Accepted formats: JPG, PNG. Max size 4MB.') }}</p>
                            </div>
                        </div>
                        @endif
                    @else
                        <p class="text-sm text-gray-500">{{ __('No digital ID issued yet.') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
