<section aria-labelledby="dc-main-title" class="space-y-4">
    <h3 id="dc-main-title" class="text-base font-semibold">Informasi utama</h3>
    <div x-data="{ open: false }" @dc-company-added.window="open = false; $refs.companyToggle.focus()" class="space-y-3">
        @include('livewire.pages.data-centers.tambah-data-center.field', [
            'field' => [
                'model' => 'form.company',
                'label' => 'Perusahaan',
                'type' => 'select',
                'dynamicOptions' => 'companyOptions',
            ],
        ])
        <button type="button" x-ref="companyToggle" :disabled="busy"
            @click="open = !open; if (open) $nextTick(() => $refs.companyPanel.querySelector('input').focus())"
            :aria-expanded="open" aria-controls="dc-company-panel"
            class="min-h-11 rounded-lg text-sm font-medium text-primary focus-visible:outline-2 focus-visible:outline-primary">+
            Tambah Perusahaan</button>
        <div id="dc-company-panel" x-ref="companyPanel" x-cloak x-show="open"
            x-transition:enter="transition-opacity duration-150 motion-reduce:transition-none"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            class="space-y-4 rounded-lg border border-neutral-300 bg-neutral-100/50 p-4">
            <h4 class="text-sm font-semibold">Perusahaan baru (simulasi)</h4>
            <div class="grid gap-4 md:grid-cols-2">
                @include('livewire.pages.data-centers.tambah-data-center.field', [
                    'field' => ['model' => 'newCompany.name', 'label' => 'Nama Perusahaan', 'required' => true],
                ])
                @include('livewire.pages.data-centers.tambah-data-center.field', [
                    'field' => [
                        'model' => 'newCompany.nib',
                        'label' => 'NIB',
                        'placeholder' => '13 digit angka',
                        'required' => true,
                    ],
                ])
            </div>
            <div class="flex justify-end gap-2">
                <x-button variant="secondary" ::disabled="busy" @click="open = false; $refs.companyToggle.focus()"
                    class="px-4! py-2! sm:min-h-11! sm:py-2! transition-none! enabled:active:opacity-100! disabled:cursor-wait! disabled:hover:bg-neutral-100">Batal</x-button>
                <x-button variant="primary" ::disabled="busy" @click="addCompany()"
                    class="px-4! py-2! sm:min-h-11! sm:py-2! transition-none! enabled:active:opacity-100! disabled:cursor-wait! disabled:hover:bg-primary/90">Tambahkan</x-button>
            </div>
        </div>
    </div>
    <div class="grid gap-4 md:grid-cols-2">
        {{-- Nama Pusat Data --}}
        @include('livewire.pages.data-centers.tambah-data-center.field', [
            'field' => [
                'model' => 'form.name',
                'label' => 'Nama Pusat Data',
                'type' => 'text',
                'placeholder' => 'Masukkan nama pusat data',
                'required' => true,
            ],
        ])

        {{-- Status Pusat Data --}}
        @include('livewire.pages.data-centers.tambah-data-center.field', [
            'field' => [
                'model' => 'form.status',
                'label' => 'Status Pusat Data',
                'type' => 'select',
                'options' => array_combine($statuses, $statuses),
            ],
        ])

        {{-- Tahun Konstruksi dan Operasional --}}
        @foreach ($mainFields as $key => [$label, $type, $unit, $placeholder])
            @if ($key !== 'name')
                @include('livewire.pages.data-centers.tambah-data-center.field', [
                    'field' => [
                        'model' => 'form.' . $key,
                        'label' => $label,
                        'type' => $type,
                        'unit' => $unit,
                        'placeholder' => $placeholder,
                    ],
                ])
            @endif
        @endforeach
    </div>
</section>

<section aria-labelledby="dc-classification-title" class="space-y-4 border-t border-neutral-300 pt-6">
    <h3 id="dc-classification-title" class="text-base font-semibold">Klasifikasi dan layanan</h3>
    <x-field label="Kawasan" name="form.area" input-id="dc-form-area" class="[&>label]:text-sm">
        <select id="dc-form-area" x-model="form.area" :disabled="busy" aria-describedby="dc-form-area-error"
            :aria-invalid="errorFor('form.area') ? 'true' : 'false'"
            class="min-h-11 w-full rounded-lg border bg-white px-3 py-2 text-sm focus:border-primary focus:outline-2 focus:outline-primary"
            :class="errorFor('form.area') ? 'border-red' : 'border-neutral-300'">
            <option value="">{{ count($areas) ? 'Pilih Kawasan' : 'Belum ada kawasan terdaftar' }}</option>
            @foreach ($areas as $id => $name)
                <option value="{{ $id }}">{{ $name }}</option>
            @endforeach
        </select>
        <x-slot:errorMessage x-text="errorFor('form.area')"></x-slot:errorMessage>
    </x-field>
    <x-field label="AI Ready" name="form.ai_ready" input-id="dc-ai-ready" class="[&>label]:text-sm">
        <select id="dc-ai-ready" x-model="form.ai_ready" :disabled="busy" aria-describedby="dc-ai-ready-error"
            :aria-invalid="errorFor('form.ai_ready') ? 'true' : 'false'"
            class="min-h-11 w-full rounded-lg border bg-white px-3 py-2 text-sm focus:border-primary focus:outline-2 focus:outline-primary"
            :class="errorFor('form.ai_ready') ? 'border-red' : 'border-neutral-300'">
            <option value="">Pilih AI Ready</option>
            <option value="Ya">Ya</option>
            <option value="Tidak">Tidak</option>
        </select>
        <x-slot:errorMessage x-text="errorFor('form.ai_ready')"></x-slot:errorMessage>
    </x-field>
    <fieldset>
        <legend class="mb-2 text-sm font-medium">Service Models <span class="font-normal text-neutral-700">(boleh lebih
                dari satu)</span></legend>
        <div class="grid gap-x-4 gap-y-1 md:grid-cols-2">
            @foreach ($services as $name => $description)
                <label
                    class="flex min-h-11 cursor-pointer items-center gap-3 rounded-lg px-2 text-sm hover:bg-neutral-100">
                    <input type="checkbox" :disabled="busy" x-model="form.services" value="{{ $name }}"
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
        <p x-show="errorFor('form.services')" x-text="errorFor('form.services')" class="text-xs text-red"></p>
    </fieldset>
</section>
<section aria-labelledby="dc-address-title" class="space-y-4 border-t border-neutral-300 pt-6">
    <div>
        <h3 id="dc-address-title" class="text-base font-semibold">Alamat Pusat Data</h3>
        <p class="mt-1 text-xs text-neutral-700">Pilihan wilayah dan Kode PUM adalah contoh simulasi, bukan referensi
            kode resmi.</p>
    </div>
    <div class="grid gap-4 md:grid-cols-2">
        @include('livewire.pages.data-centers.tambah-data-center.field', [
            'field' => [
                'model' => 'form.province',
                'label' => 'Provinsi',
                'type' => 'select',
                'dynamicOptions' => 'provinceOptions',
            ],
        ])
        @include('livewire.pages.data-centers.tambah-data-center.field', [
            'field' => [
                'model' => 'form.city',
                'label' => 'Kota/Kabupaten',
                'type' => 'select',
                'dynamicOptions' => 'cityOptions',
                'dynamicPlaceholder' =>
                    "Object.keys(cities).length ? 'Pilih kota/kabupaten' : 'Pilih provinsi terlebih dahulu'",
            ],
        ])
        @include('livewire.pages.data-centers.tambah-data-center.field', [
            'field' => [
                'model' => 'form.district',
                'label' => 'Kecamatan',
                'type' => 'select',
                'dynamicOptions' => 'districtOptions',
                'dynamicPlaceholder' =>
                    "Object.keys(districts).length ? 'Pilih kecamatan' : 'Pilih kota/kabupaten terlebih dahulu'",
            ],
        ])
        @foreach (['pum' => ['Kode PUM', 'pum'], 'timezone' => ['Zona Waktu', 'timezone']] as $key => [$label, $value])
            <div>
                <label for="dc-{{ $key }}" class="mb-1 block text-sm font-medium">{{ $label }}</label>
                <input id="dc-{{ $key }}" disabled :value="{{ $value }}"
                    placeholder="Otomatis setelah memilih kecamatan"
                    class="min-h-11 w-full cursor-not-allowed rounded-lg border border-neutral-300 bg-neutral-100 px-3 py-2 text-sm text-neutral-700 opacity-100">
            </div>
        @endforeach
    </div>
</section>
