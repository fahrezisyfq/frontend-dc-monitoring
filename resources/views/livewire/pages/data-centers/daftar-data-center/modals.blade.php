@php
    $companyOptions = array_map(fn (array $company): string => $company['name'].' — NIB '.$company['nib'], $companies);
    $statusOptions = array_combine($statuses, $statuses);
    $provinceOptions = array_combine(array_keys($regions), array_keys($regions));
    $cityOptions = array_combine(array_keys($regions[$form['province'] ?? '']['cities'] ?? []), array_keys($regions[$form['province'] ?? '']['cities'] ?? []));
    $districtOptions = array_combine(array_keys($regions[$form['province'] ?? '']['cities'][$form['city'] ?? ''] ?? []), array_keys($regions[$form['province'] ?? '']['cities'][$form['city'] ?? ''] ?? []));
    $areaOptions = array_combine($areas, $areas);
    $categoryOptions = array_combine($categories, $categories);
@endphp

<x-modal-edit name="data-center-edit" load-action="loadDataCenter" action="saveDataCenter"
    title="Edit Pusat Data" description="Perubahan hanya berlaku pada data simulasi halaman ini."
    content-class="p-4 text-sm" fields-class="space-y-6" show-close>
    <p x-cloak x-show="Object.keys(errors).length" role="alert"
        class="mb-4 rounded-lg bg-red/5 p-3 text-xs text-red">Periksa kembali field yang ditandai di bawah.</p>
    <section class="space-y-4">
        <h4 class="text-sm font-semibold">Identitas Pusat Data</h4>
        <div class="grid gap-4 sm:grid-cols-2">
            @include('livewire.pages.data-centers.daftar-data-center.edit-field', ['name' => 'form.company', 'label' => 'Perusahaan', 'type' => 'select', 'options' => $companyOptions, 'required' => true])
            @include('livewire.pages.data-centers.daftar-data-center.edit-field', ['name' => 'form.name', 'label' => 'Nama Pusat Data', 'required' => true])
            @include('livewire.pages.data-centers.daftar-data-center.edit-field', ['name' => 'form.status', 'label' => 'Status Pusat Data', 'type' => 'select', 'options' => $statusOptions, 'required' => true])
            @include('livewire.pages.data-centers.daftar-data-center.edit-field', ['name' => 'form.construction_year', 'label' => 'Tahun Mulai Konstruksi', 'type' => 'year', 'placeholder' => 'YYYY'])
            @include('livewire.pages.data-centers.daftar-data-center.edit-field', ['name' => 'form.operation_year', 'label' => 'Tahun Mulai Operasi', 'type' => 'year', 'placeholder' => 'YYYY'])
            @include('livewire.pages.data-centers.daftar-data-center.edit-field', ['name' => 'form.province', 'label' => 'Provinsi', 'type' => 'select', 'options' => $provinceOptions, 'required' => true])
            @include('livewire.pages.data-centers.daftar-data-center.edit-field', ['name' => 'form.city', 'label' => 'Kota/Kabupaten', 'type' => 'select', 'options' => $cityOptions, 'required' => true])
            @include('livewire.pages.data-centers.daftar-data-center.edit-field', ['name' => 'form.district', 'label' => 'Kecamatan', 'type' => 'select', 'options' => $districtOptions])
            @include('livewire.pages.data-centers.daftar-data-center.edit-field', ['name' => 'form.area', 'label' => 'Kawasan', 'type' => 'select', 'options' => $areaOptions])
        </div>
        <fieldset>
            <legend class="mb-2 text-sm font-medium">AI Ready</legend>
            <div class="flex gap-6">
                @foreach (['Ya', 'Tidak'] as $option)
                    <label class="flex min-h-11 cursor-pointer items-center gap-2 text-sm">
                        <input type="radio" wire:model="form.ai_ready" value="{{ $option }}"
                            class="size-4 accent-primary focus-visible:outline-2 focus-visible:outline-primary">{{ $option }}
                    </label>
                @endforeach
            </div>
            <p x-cloak x-show="errors['form.ai_ready']" id="form-ai-ready-error" role="alert"
                class="mt-1 text-xs text-red" x-text="(errors['form.ai_ready'] || [])[0] || ''"></p>
        </fieldset>
        <fieldset>
            <legend class="mb-2 text-sm font-medium">Service Models <span class="font-normal text-neutral-700">(boleh lebih dari satu)</span></legend>
            <div class="grid gap-x-4 gap-y-1 sm:grid-cols-2">
                @foreach ($serviceOptions as $name => $description)
                    <label class="flex min-h-11 cursor-pointer items-center gap-3 rounded-lg px-2 text-sm hover:bg-neutral-100">
                        <input type="checkbox" wire:model="form.services" value="{{ $name }}"
                            class="size-4 shrink-0 accent-primary focus-visible:outline-2 focus-visible:outline-primary">
                        <span class="min-w-0 py-2 wrap-anywhere">
                            <span class="block">{{ $name }}</span>
                            @if (filled($description))
                                <span class="mt-0.5 block text-xs text-neutral-700">{{ $description }}</span>
                            @endif
                        </span>
                    </label>
                @endforeach
            </div>
            <p x-cloak x-show="errors['form.services']" id="form-services-error" role="alert"
                class="mt-1 text-xs text-red" x-text="(errors['form.services'] || [])[0] || ''"></p>
        </fieldset>
    </section>

    <section class="space-y-4 border-t border-neutral-300 pt-5">
        <h4 class="text-sm font-semibold">Kapasitas &amp; Utilisasi</h4>
        <div class="grid gap-4 sm:grid-cols-2">
            @foreach ($capacityFields as $key => [$label, $type, $unit, $placeholder])
                @if ($key !== 'reporting_year')
                    @include('livewire.pages.data-centers.daftar-data-center.edit-field', ['name' => 'form.'.$key, 'label' => $label, 'type' => $type, 'unit' => $unit, 'placeholder' => $placeholder])
                @endif
            @endforeach
        </div>
    </section>

    <section class="space-y-4 border-t border-neutral-300 pt-5">
        <h4 class="text-sm font-semibold">Energi dan Air</h4>
        <div class="grid gap-4 sm:grid-cols-2">
            @include('livewire.pages.data-centers.daftar-data-center.edit-field', ['name' => 'form.reporting_year', 'label' => 'Tahun Pelaporan (Reporting Year)', 'type' => 'year', 'placeholder' => 'YYYY'])
            @foreach ($energyFields as $key => [$label, $type, $unit, $placeholder])
                @include('livewire.pages.data-centers.daftar-data-center.edit-field', ['name' => 'form.'.$key, 'label' => $label, 'type' => $type, 'unit' => $unit, 'placeholder' => $placeholder])
            @endforeach
        </div>
        <p class="text-xs text-neutral-700">PUE dan WUE dihitung otomatis pada detail berdasarkan nilai energi dan air di atas.</p>
    </section>

    <section class="space-y-4 border-t border-neutral-300 pt-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h4 class="text-sm font-semibold">Sertifikasi</h4>
            <x-button variant="secondary" type="button" wire:click="addCertificate"
                class="px-3! py-1.5! transition-none! enabled:active:opacity-100!">+ Tambah Sertifikasi</x-button>
        </div>
        <p x-cloak x-show="errors['certificates']" role="alert" class="text-xs text-red"
            x-text="(errors['certificates'] || [])[0] || ''"></p>
        @forelse ($certificates as $index => $certificate)
            <div wire:key="data-center-certificate-{{ $index }}" class="space-y-3 rounded-lg border border-neutral-300 p-3">
                <div class="flex items-center justify-between gap-3">
                    <h5 class="text-sm font-semibold">Sertifikasi {{ $loop->iteration }}</h5>
                    <x-button variant="ghost" type="button" wire:click="removeCertificate({{ $index }})"
                        class="px-2! py-1! text-xs! transition-none! enabled:active:opacity-100!">Hapus</x-button>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    @foreach ($certificateFields as $key => [$label, $type])
                        @include('livewire.pages.data-centers.daftar-data-center.edit-field', [
                            'name' => 'certificates.'.$index.'.'.$key,
                            'label' => $label,
                            'type' => $type,
                            'options' => $type === 'select' ? $categoryOptions : [],
                        ])
                    @endforeach
                </div>
            </div>
        @empty
            <p class="text-sm text-neutral-700">Belum ada sertifikasi.</p>
        @endforelse
    </section>
</x-modal-edit>

<x-modal-delete name="data-center-delete" action="deleteDataCenter" title="Hapus Pusat Data"
    message="Apakah Anda yakin ingin menghapus pusat data berikut? Perubahan ini hanya berlaku pada data simulasi." />
