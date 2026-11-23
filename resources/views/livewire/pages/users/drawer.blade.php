<x-drawer name="user-detail" title="Detail Pengguna" size="md" wire:key="user-detail-drawer">
    <dl class="space-y-2.5">
        <div>
            <dt class="text-xs text-neutral-700">Nama Pengguna</dt>
            <dd class="mt-1 text-sm font-medium wrap-anywhere" x-text="$wire.users[selectedId]?.name ?? ''"></dd>
        </div>
        <div>
            <dt class="text-xs text-neutral-700">Email</dt>
            <dd class="mt-1 text-sm font-medium wrap-anywhere" x-text="$wire.users[selectedId]?.email ?? ''"></dd>
        </div>
        <div>
            <dt class="text-xs text-neutral-700">Role</dt>
            <dd class="mt-1">
                @foreach ($roles as $value => $label)
                    <template x-if="$wire.users[selectedId]?.role === @js($value)">
                        <x-badge :variant="match ($value) { 'super_admin' => 'purple', 'admin' => 'primary', default => 'neutral' }" class="whitespace-normal!">{{ $label }}</x-badge>
                    </template>
                @endforeach
            </dd>
        </div>
    </dl>
</x-drawer>
