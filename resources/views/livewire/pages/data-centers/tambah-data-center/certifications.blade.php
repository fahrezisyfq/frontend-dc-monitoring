<div class="flex flex-wrap items-center justify-between gap-3">
    <h3 class="text-base font-semibold">Data Sertifikasi</h3>
    <x-button variant="secondary" ::disabled="busy" @click="addCertificate()" class="px-4! py-2! sm:min-h-11! sm:py-2! transition-none! enabled:active:opacity-100! disabled:cursor-wait! disabled:hover:bg-neutral-100">+ Tambah Sertifikasi</x-button>
</div>
<template x-for="([index, certificate], position) in Object.entries(certificates)" :key="index">
    <section :aria-labelledby="'dc-certificate-title-' + index" class="space-y-4 rounded-xl border border-neutral-300 p-4">
        <div class="flex items-center justify-between gap-3">
            <h4 :id="'dc-certificate-title-' + index" class="text-sm font-semibold" x-text="'Sertifikasi ' + (position + 1)"></h4>
            <x-button variant="secondary" ::disabled="busy" @click="removeCertificate(index)" x-bind:aria-label="'Hapus sertifikasi ' + (position + 1)" class="px-4! py-2! sm:min-h-11! sm:py-2! transition-none! enabled:active:opacity-100! disabled:cursor-wait! disabled:hover:bg-neutral-100 text-red!">Hapus</x-button>
        </div>
        <div class="grid gap-4 md:grid-cols-2">
            @foreach ($certificateFields as $key => [$label, $type, $unit, $placeholder])
                <div @class(['md:col-span-2' => $type === 'textarea'])>
                    @include('livewire.pages.data-centers.tambah-data-center.field', ['field' => ['model' => 'certificates.template.'.$key, 'expression' => 'certificates[index].'.$key, 'path' => "'certificates.' + index + '.".$key."'", 'label' => $label, 'type' => $type, 'placeholder' => $placeholder, 'options' => array_combine($categories, $categories), 'required' => in_array($key, ['name', 'issuer'])]])
                    @if ($type === 'date')<p class="mt-1 text-xs text-neutral-700">Format: YYYY-MM-DD (tampilan mengikuti pengaturan browser).</p>@endif
                </div>
            @endforeach
        </div>
    </section>
</template>
<div x-show="!Object.keys(certificates).length">
    <div class="rounded-lg border border-dashed border-neutral-300 px-4 py-8 text-center">
        <p class="text-sm font-medium">Belum ada sertifikasi</p>
        <p class="mt-2 text-sm text-neutral-700">Tambahkan sertifikasi jika tersedia, atau lanjutkan ke Konfirmasi.</p>
    </div>
</div>
