<x-public-layout>
    <div class="bg-white shadow-sm rounded-lg p-6 space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-xl font-semibold text-gray-900">{{ __('Digital ID Verification') }}</h1>
                <p class="text-sm text-gray-500">{{ __('Verify employment details and uploaded documents.') }}</p>
            </div>
            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium bg-gray-100 text-gray-700">
                {{ ucfirst($digitalId->status) }}
            </span>
        </div>

        @if ($digitalId->status !== 'active')
            <div class="rounded-md bg-amber-50 p-4 text-sm text-amber-800">
                {{ __('This digital ID is inactive. Please contact the issuing employer for clarification.') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm text-gray-700">
            <div class="space-y-2">
                <h2 class="text-sm font-semibold text-gray-900">{{ __('Applicant Details') }}</h2>
                <p><span class="font-semibold text-gray-800">{{ __('Name:') }}</span> {{ $digitalId->jobseeker->user->name ?? __('N/A') }}</p>
                <p><span class="font-semibold text-gray-800">{{ __('Employer:') }}</span> {{ $digitalId->employer->company_name ?? $digitalId->employer->user->name ?? __('N/A') }}</p>
                <p><span class="font-semibold text-gray-800">{{ __('Job Title:') }}</span> {{ $digitalId->job_title }}</p>
                <p><span class="font-semibold text-gray-800">{{ __('Employee ID:') }}</span> {{ $digitalId->employee_identifier }}</p>
                <p><span class="font-semibold text-gray-800">{{ __('Issue Date:') }}</span> {{ $digitalId->issue_date?->format('M d, Y') ?? __('N/A') }}</p>
            </div>
            <div class="space-y-2">
                <h2 class="text-sm font-semibold text-gray-900">{{ __('Job Details') }}</h2>
                <p><span class="font-semibold text-gray-800">{{ __('Company:') }}</span> {{ $digitalId->company_name }}</p>
                <p><span class="font-semibold text-gray-800">{{ __('Job Post:') }}</span> {{ $digitalId->jobPost->title ?? __('N/A') }}</p>
                <p><span class="font-semibold text-gray-800">{{ __('Status:') }}</span> {{ ucfirst($digitalId->status) }}</p>
            </div>
        </div>

        <div class="border-t pt-5 space-y-3" x-data="{
            downloadLabel: '',
            downloadType: '',
            openDownload(type, label) {
                this.downloadType = type;
                this.downloadLabel = label;
                this.$dispatch('open-modal', 'document-download');
            }
        }">
            <h2 class="text-sm font-semibold text-gray-900">{{ __('Uploaded Documents') }}</h2>
            @if ($errors->has('password'))
                <div class="rounded-md bg-red-50 p-3 text-sm text-red-700">
                    {{ $errors->first('password') }}
                </div>
            @endif
            <div class="space-y-6 text-sm text-gray-700">
                @foreach (['sss','pagibig','philhealth','psa'] as $type)
                    @php
                        $document = $documents[$type] ?? null;
                    @endphp
                    @if ($document)
                        @php
                            $label = \Illuminate\Support\Str::of($type)->replace('_', ' ')->title();
                            $statusLabel = $document->status === 'pending' ? __('Updated') : ($document->status === 'rejected' ? __('Needs Update') : ucfirst($document->status));
                            $fileUrl = asset('storage/'.$document->file_path);
                            $ext = strtolower(pathinfo($document->file_path, PATHINFO_EXTENSION));
                            $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true);
                            $isPdf = $ext === 'pdf';
                        @endphp
                        <div class="space-y-3">
                            <div class="flex items-center gap-2 text-gray-800">
                                <span class="font-semibold">{{ $label }}</span>
                                <span class="text-xs text-gray-500">
                                    {{ __(':status', ['status' => $statusLabel]) }}
                                </span>
                            </div>
                            @if ($isImage)
                                <div class="w-full max-w-sm aspect-[3/4] rounded-lg border overflow-hidden bg-gray-50">
                                    <img src="{{ $fileUrl }}" alt="{{ $label }}" class="h-full w-full object-contain">
                                </div>
                            @elseif ($isPdf)
                                <div class="relative w-full max-w-sm aspect-[3/4] rounded-lg border overflow-hidden bg-gray-50 pdf-preview" data-src="{{ $fileUrl }}">
                                    <canvas class="h-full w-full"></canvas>
                                    <div class="absolute inset-0 bg-transparent" aria-hidden="true"></div>
                                </div>
                            @else
                                <p class="text-xs text-gray-500">{{ __('Preview not available for this file type.') }}</p>
                            @endif
                            <button type="button" class="text-xs text-indigo-600 hover:text-indigo-900" x-on:click="openDownload('{{ $type }}', '{{ $label }}')">
                                {{ __('Download (password required)') }}
                            </button>
                        </div>
                    @else
                        <div class="flex items-center gap-2 text-gray-500">
                            <span class="font-semibold text-gray-700">{{ \Illuminate\Support\Str::of($type)->replace('_', ' ')->title() }}</span>
                            <span class="text-xs">{{ __('Missing') }}</span>
                        </div>
                    @endif
                @endforeach
            </div>

            <x-modal name="document-download" maxWidth="sm">
                <div class="p-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-base font-semibold text-gray-900" x-text="downloadLabel"></h3>
                        <button type="button" class="text-sm text-gray-500 hover:text-gray-700" x-on:click="$dispatch('close-modal', 'document-download')">
                            {{ __('Close') }}
                        </button>
                    </div>
                    <p class="text-sm text-gray-600">{{ __('Enter the employee ID to download this document.') }}</p>
                    <form method="POST" x-bind:action="'{{ route('digital-ids.verify.documents.download', [$digitalId->public_token, 'TYPE']) }}'.replace('TYPE', downloadType)">
                        @csrf
                        <div>
                            <x-input-label for="download_password" :value="__('Password')" />
                            <x-text-input id="download_password" name="password" type="password" class="mt-1 block w-full" required />
                        </div>
                        <div class="mt-4 flex justify-end">
                            <x-primary-button type="submit">{{ __('Download') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </x-modal>
        </div>

        <p class="text-xs text-gray-500">
            {{ __('This verification page is generated from the QR code on the Digital ID.') }}
        </p>
    </div>
</x-public-layout>

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script>
    (function () {
        if (!window.pdfjsLib) {
            return;
        }

        window.pdfjsLib.GlobalWorkerOptions.workerSrc =
            'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

        const previews = document.querySelectorAll('.pdf-preview');
        previews.forEach((preview) => {
            const url = preview.dataset.src;
            const canvas = preview.querySelector('canvas');
            if (!url || !canvas) {
                return;
            }

            const context = canvas.getContext('2d');
            window.pdfjsLib.getDocument(url).promise.then((pdf) => {
                return pdf.getPage(1);
            }).then((page) => {
                const containerWidth = preview.clientWidth;
                const viewport = page.getViewport({ scale: 1 });
                const scale = containerWidth / viewport.width;
                const scaledViewport = page.getViewport({ scale });

                canvas.width = scaledViewport.width;
                canvas.height = scaledViewport.height;

                const renderContext = {
                    canvasContext: context,
                    viewport: scaledViewport,
                };

                return page.render(renderContext).promise;
            }).catch(() => {
                preview.innerHTML = '<div class="flex h-full w-full items-center justify-center text-xs text-gray-500">PDF preview unavailable.</div>';
            });
        });
    })();
</script>
