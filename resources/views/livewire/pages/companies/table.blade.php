<section class="min-w-0 overflow-hidden rounded-xl border border-neutral-300 bg-white"
    aria-labelledby="company-list-title">
    <div class="flex flex-col gap-4 border-b border-neutral-300 p-4 sm:flex-row sm:items-end sm:justify-between sm:p-4">
        <div>
            <h3 id="company-list-title" class="text-base font-semibold">Daftar perusahaan</h3>
            <div class="relative mt-1 text-xs text-neutral-700">
                <p wire:loading.delay.class="invisible" wire:target="search">
                    {{ count($filteredCompanies) }} dari {{ count($companies) }} perusahaan
                </p>
                <p wire:loading.delay wire:target="search" role="status" class="absolute inset-0 whitespace-nowrap">
                    Mencari perusahaan…
                </p>
            </div>
        </div>

        <div class="w-full sm:max-w-sm">
            <label for="company-search" class="mb-1 block text-xs font-medium">Cari perusahaan</label>
            <x-search id="company-search" :with-icon="false" wire:model.live.debounce.300ms="search"
                placeholder="Cari NIB atau nama perusahaan" aria-label="Cari perusahaan" />
        </div>
    </div>
    <x-table label="Tabel perusahaan" :framed="false">
        <x-slot:header>
            <tr>
                <th scope="col" class="w-16 whitespace-nowrap">No.</th>
                <th scope="col" class="whitespace-nowrap">NIB</th>
                <th scope="col" class="whitespace-nowrap">Nama Perusahaan</th>
                <th scope="col" class="whitespace-nowrap">Kontak Perusahaan</th>
                <th scope="col" class="whitespace-nowrap">Alamat Perusahaan</th>
                <th scope="col" class="whitespace-nowrap text-center">Total DC</th>
                <th scope="col" class="whitespace-nowrap text-center">Aksi</th>
            </tr>
        </x-slot:header>
        <x-slot:body wire:loading.delay.class="opacity-75" wire:target="search"
            class="transition-opacity duration-200 ease-out motion-reduce:transition-none">
            @forelse ($filteredCompanies as $id => $company)
                <tr wire:key="company-{{ $id }}" class="hover:bg-neutral-100/50">
                    <td class="tabular-nums">{{ $loop->iteration }}</td>
                    <td class="tabular-nums">{{ $company['nib'] }}</td>
                    <td class="min-w-48 font-medium">{{ $company['company_name'] }}</td>
                    <td>
                        <p class="font-medium">{{ $company['contact_name'] }}</p>
                        <p class="mt-0.5 text-xs text-neutral-700">{{ $company['contact_email'] }}</p>
                        <p class="mt-0.5 whitespace-nowrap text-xs text-neutral-700 tabular-nums">
                            {{ $company['contact_phone'] }}</p>
                    </td>
                    <td class="min-w-52 text-neutral-700">
                        <p class="max-w-xs wrap-anywhere">{{ $company['company_address'] }}</p>
                    </td>
                    <td class="text-center tabular-nums">{{ count($companyDataCenters[$id] ?? []) }}</td>
                    <td class="w-40 py-2">
                        <div class="flex min-w-35 items-center justify-center gap-1 sm:min-w-29">
                            <x-button-detail :data-id="$id" :data-name="$company['company_name']" wire:loading.attr="disabled"
                                ::disabled="modalActive"
                                @click="$dispatch('drawer:open', { name: 'company-detail', id: Number($el.dataset.id) })" />
                            <x-button-edit :data-id="$id" :data-name="$company['company_name']" wire:loading.attr="disabled"
                                ::disabled="modalActive"
                                @click="$dispatch('modal-edit:open', { name: 'company-edit', id: Number($el.dataset.id), data: { ...$wire.companies[$el.dataset.id] } })" />
                            <x-button-delete :data-id="$id" :data-name="$company['company_name']" wire:loading.attr="disabled"
                                ::disabled="modalActive"
                                @click="$dispatch('modal-delete:open', { name: 'company-delete', id: Number($el.dataset.id), dataName: $el.dataset.name })" />
                        </div>
                    </td>
                </tr>
            @empty
                <tr class="hover:bg-transparent">
                    <td colspan="7" class="px-4 py-8 text-center">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                            stroke-linecap="round" stroke-linejoin="round" class="mx-auto mb-2 size-6 text-neutral-700"
                            aria-hidden="true">
                            <path d="M3 21h18M5 21V7l8-4v18M19 21V11l-6-4M9 9v.01M9 13v.01M9 17v.01" />
                        </svg>
                        <p class="font-semibold">
                            {{ trim($search) !== '' ? 'Perusahaan tidak ditemukan' : 'Belum ada perusahaan' }}</p>
                        <p class="mt-1 text-neutral-700">
                            {{ trim($search) !== '' ? 'Coba NIB atau nama perusahaan lainnya.' : 'Tambahkan perusahaan untuk memulai simulasi.' }}
                        </p>
                        @if (trim($search) !== '')
                            <x-button variant="ghost" wire:click="$set('search', '')"
                                class="mt-3 py-0! focus-visible:outline-offset-0!">Reset pencarian</x-button>
                        @endif
                    </td>
                </tr>
            @endforelse
        </x-slot:body>
    </x-table>
</section>
