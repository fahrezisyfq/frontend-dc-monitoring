<div class="space-y-4" wire:ignore x-data="dataCenterCreate(@js($initialState))">
    <x-toast />
    <h1 id="data-center-create-title" class="text-xl font-semibold tracking-tight">Tambah Pusat Data</h1>
    <p class="text-sm text-neutral-700">Lengkapi data secara bertahap, lalu tinjau sebelum konfirmasi. Simulasi ini tidak
        menyimpan data secara permanen.</p>
    <nav aria-label="Tahapan tambah pusat data" class="rounded-xl border border-neutral-300 bg-white p-4 sm:p-6">
        <div class="mb-3 flex items-center justify-between gap-3 text-sm font-medium" aria-live="polite">
            <p x-text="`Langkah ${step} dari ${Object.keys(steps).length}`">Langkah 1 dari 4</p>
            <span class="tabular-nums text-primary" x-text="`${step * 25}%`">25%</span>
        </div>
        <div role="progressbar" aria-label="Progres tahapan form" aria-valuemin="0" aria-valuemax="100"
            :aria-valuenow="step * 25"
            :aria-valuetext="`Langkah ${step} dari 4: ${steps[step]}`"
            class="mb-3 h-1.5 overflow-hidden rounded-full bg-neutral-100">
            <div class="h-full rounded-full bg-primary transition-[width] duration-200 ease-out motion-reduce:transition-none"
                :class="{ 'w-1/4': step === 1, 'w-1/2': step === 2, 'w-3/4': step === 3, 'w-full': step === 4 }"></div>
        </div>
        <ol class="grid grid-cols-2 gap-2 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ($steps as $number => $title)
                <li class="min-w-0">
                    <button type="button" @click="goToStep({{ $number }})" :disabled="busy"
                        :aria-current="step === {{ $number }} ? 'step' : null"
                        :class="step === {{ $number }} ? 'border-primary' : 'border-neutral-300'"
                        class="flex h-full min-h-11 w-full items-center gap-2 rounded-lg border p-2 text-left transition-colors duration-150 hover:bg-neutral-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary motion-reduce:transition-none disabled:cursor-wait">
                        <span
                            class="flex size-8 shrink-0 items-center justify-center rounded-full bg-neutral-100 text-sm font-semibold text-neutral-700">{{ $number }}</span>
                        <span class="min-w-0 text-sm font-medium">{{ $title }}</span>
                    </button>
                </li>
            @endforeach
        </ol>
    </nav>
    <section class="min-w-0 rounded-xl border border-neutral-300 bg-white" aria-labelledby="dc-step-title">
        <header class="border-b border-neutral-300 px-4 py-4 sm:px-6">
            <h2 id="dc-step-title" x-ref="stepTitle" tabindex="-1" class="text-lg font-semibold focus:outline-none" x-text="steps[step]">Identitas Pusat Data</h2>
            <p class="mt-1 text-sm text-neutral-700" x-text="stepDescription"></p>
        </header>
        <form @submit.prevent="submit()" novalidate>
            <div class="min-h-96 p-4 sm:p-6">
                <p x-cloak x-show="Object.keys(errors).length || message" role="alert" class="mb-6 rounded-lg bg-red/5 p-3 text-sm text-red"
                    x-text="message || 'Periksa kembali field yang ditandai di bawah.'"></p>
                <div data-step="1" x-show="step === 1" class="space-y-6">
                    @include('livewire.pages.data-centers.tambah-data-center.identity')
                </div>
                <div data-step="2" x-cloak x-show="step === 2" class="space-y-6">
                    @include('livewire.pages.data-centers.tambah-data-center.capacity')
                </div>
                <div data-step="3" x-cloak x-show="step === 3" class="space-y-6">
                    @include('livewire.pages.data-centers.tambah-data-center.certifications')
                </div>
                <div data-step="4" x-cloak x-show="step === 4" class="space-y-6">
                    @include('livewire.pages.data-centers.tambah-data-center.confirmation')
                </div>
            </div>
            <footer
                class="flex flex-wrap items-center justify-between gap-3 border-t border-neutral-300 px-4 py-4 sm:px-6">
                <div><span x-cloak x-show="busy" role="status" class="text-sm text-neutral-700">Memproses…</span></div>
                <div class="flex flex-wrap gap-2">
                        <x-button variant="secondary" ::disabled="busy" @click="backTo(step - 1)" x-cloak x-show="step > 1" class="px-4! py-2! sm:min-h-11! sm:py-2! transition-none! enabled:active:opacity-100! disabled:cursor-wait! disabled:hover:bg-neutral-100">Kembali</x-button>
                    <x-button type="submit" variant="primary" ::disabled="busy" class="px-4! py-2! sm:min-h-11! sm:py-2! transition-none! enabled:active:opacity-100! disabled:cursor-wait! disabled:hover:bg-primary/90"><span x-text="step === 4 ? 'Konfirmasi (simulasi)' : 'Selanjutnya'"></span></x-button>
                </div>
            </footer>
        </form>
    </section>
</div>
