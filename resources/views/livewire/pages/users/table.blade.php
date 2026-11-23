<section class="min-w-0 overflow-hidden rounded-xl border border-neutral-300 bg-white" aria-labelledby="user-list-title">
    <div class="flex flex-col gap-4 border-b border-neutral-300 p-4 sm:flex-row sm:items-end sm:justify-between sm:p-4">
        <div>
            <h3 id="user-list-title" class="text-base font-semibold">Daftar pengguna</h3>
            <p class="mt-1 text-xs text-neutral-700" role="status">{{ count($filteredUsers) }} dari {{ count($users) }}
                pengguna</p>
        </div>
        <div class="w-full sm:max-w-sm">
            <label for="user-search" class="mb-1 block text-xs font-medium">Cari pengguna</label>
            <x-search id="user-search" :with-icon="false" wire:model.live.debounce.300ms="search"
                placeholder="Cari nama atau email pengguna" aria-label="Cari pengguna" />
        </div>
    </div>
    <x-table label="Tabel pengguna" :framed="false">
        <x-slot:header>
            <tr>
                <th scope="col">No.</th>
                <th scope="col">Nama Pengguna</th>
                <th scope="col">Email</th>
                <th scope="col">Role</th>
                <th scope="col">Last Activity</th>
                <th scope="col" class="text-center">Aksi</th>
            </tr>
        </x-slot:header>
        @forelse ($filteredUsers as $id => $user)
            <tr wire:key="user-{{ $id }}">
                <td class="tabular-nums">{{ $loop->iteration }}</td>
                <td class="min-w-48 max-w-72 font-medium wrap-anywhere">{{ $user['name'] }}</td>
                <td class="max-w-80 text-neutral-700 wrap-anywhere">{{ $user['email'] }}</td>
                <td><x-badge :variant="match ($user['role']) {
                    'super_admin' => 'purple',
                    'admin' => 'primary',
                    default => 'neutral',
                }">{{ $roles[$user['role']] }}</x-badge></td>
                <td class="whitespace-nowrap text-neutral-700 tabular-nums">
                    {{ $user['last_activity'] ? \Carbon\CarbonImmutable::parse($user['last_activity'])->setTimezone('Asia/Jakarta')->format('d M Y, H:i') . ' WIB' : 'Belum pernah login' }}
                </td>
                <td class="py-2 text-center">
                    <div class="flex items-center justify-center gap-1">
                        <x-button-detail :data-id="$id" :data-name="$user['name']" wire:loading.attr="disabled"
                            ::disabled="modalActive"
                            @click="$dispatch('drawer:open', { name: 'user-detail', id: Number($el.dataset.id) })" />
                        <x-button-edit :data-id="$id" :data-name="$user['name']" wire:loading.attr="disabled"
                            ::disabled="modalActive"
                            @click="$dispatch('modal-edit:open', { name: 'user-edit', id: Number($el.dataset.id), data: { ...$wire.users[$el.dataset.id], password: '', confirmation: '' } })" />
                        <x-button-delete :data-id="$id" :data-name="$user['name']" wire:loading.attr="disabled"
                            ::disabled="modalActive"
                            @click="$dispatch('modal-delete:open', { name: 'user-delete', id: Number($el.dataset.id), dataName: $el.dataset.name })" />
                    </div>
                </td>
            </tr>
        @empty
            <tr class="hover:bg-transparent">
                <td colspan="6" class="px-4 py-8 text-center">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                        stroke-linecap="round" stroke-linejoin="round" class="mx-auto mb-2 size-6 text-neutral-700"
                        aria-hidden="true">
                        <path
                            d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2M20 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" />
                        <circle cx="10" cy="7" r="4" />
                    </svg>
                    <p class="font-semibold">
                        {{ trim($search) !== '' ? 'Pengguna tidak ditemukan' : 'Belum ada pengguna' }}</p>
                    <p class="mt-1 text-neutral-700">
                        {{ trim($search) !== '' ? 'Coba nama atau email pengguna lainnya.' : 'Tambahkan pengguna untuk memulai simulasi.' }}
                    </p>
                    @if (trim($search) !== '')
                        <x-button variant="ghost" wire:loading.attr="disabled" wire:click="$set('search', '')"
                            class="mt-3 py-0! focus-visible:outline-offset-0! disabled:cursor-default! disabled:hover:bg-primary/5 enabled:active:opacity-100! ease-in-out!">Reset
                            pencarian</x-button>
                    @endif
                </td>
            </tr>
        @endforelse
    </x-table>
</section>
