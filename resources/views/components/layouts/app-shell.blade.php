@props([
    'sidebar' => null,
])

<div
    x-data="{
        sidebarOpen: false,
        sidebarCollapsed: false,
    }"
    x-init="$watch('sidebarOpen', value => { document.body.classList.toggle('overflow-hidden', !!value) })"
    x-on:keydown.escape.window="sidebarOpen = false"
    class="min-h-screen bg-gray-100"
>
    <header class="sticky top-0 z-40 bg-white/90 backdrop-blur border-b border-gray-100">
        <div class="w-full">
            {{ $topbar ?? '' }}
        </div>
    </header>

    <div class="flex min-h-[calc(100vh-4rem)]">
        <!-- Mobile off-canvas sidebar -->
        <div class="lg:hidden">
            <div
                x-show="sidebarOpen"
                x-cloak
                class="fixed inset-0 z-50"
                aria-hidden="true"
            >
                <div class="absolute inset-0 bg-gray-900/40" x-on:click="sidebarOpen = false"></div>
                <aside
                    role="dialog"
                    aria-modal="true"
                    class="absolute left-0 top-0 h-full w-80 max-w-[85vw] bg-white shadow-xl border-r border-gray-100 overflow-y-auto"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="-translate-x-4 opacity-0"
                    x-transition:enter-end="translate-x-0 opacity-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="translate-x-0 opacity-100"
                    x-transition:leave-end="-translate-x-4 opacity-0"
                >
                    {{ $sidebar }}
                </aside>
            </div>
        </div>

        <!-- Desktop sidebar -->
        <aside
            class="hidden lg:block border-r border-gray-100 bg-white transition-[width] duration-200 ease-out"
            :class="sidebarCollapsed ? 'lg:w-0 lg:-ml-px' : 'lg:w-72'"
        >
            <div class="h-full overflow-y-auto">
                {{ $sidebar }}
            </div>
        </aside>

        <main class="flex-1 min-w-0">
            <div class="h-full overflow-y-auto">
                {{ $slot }}
            </div>
        </main>
    </div>
</div>

