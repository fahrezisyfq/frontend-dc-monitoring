<section aria-labelledby="dashboard-table-title"
    class="min-w-0 overflow-hidden rounded-xl border border-neutral-300 bg-white">
    <div class="border-b border-neutral-300 p-4">
        <h2 id="dashboard-table-title" class="text-base font-semibold text-neutral-900">Daftar Pusat Data</h2>
        <p class="mt-1 text-xs text-neutral-500">{{ $dataCenterCount }} lokasi dalam data simulasi</p>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full min-w-[720px] border-collapse text-left text-sm">
            <thead class="border-b border-neutral-300 bg-neutral-100 text-neutral-700">
                <tr>
                    <th scope="col" class="px-3 py-2 font-semibold">Nama Data Center</th>
                    <th scope="col" class="px-3 py-2 font-semibold">Perusahaan</th>
                    <th scope="col" class="px-3 py-2 font-semibold">Lokasi</th>
                    <th scope="col" class="px-3 py-2 font-semibold">Status</th>
                    <th scope="col" class="px-3 py-2 font-semibold text-right">IT Load (MW)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-300">
                @foreach ($dataCenters as $center)
                    <tr class="transition-colors hover:bg-neutral-100/70">
                        <td class="whitespace-nowrap px-3 py-2.5 font-medium text-neutral-900">{{ $center['name'] }}
                        </td>
                        <td class="whitespace-nowrap px-3 py-2.5 text-neutral-700">{{ $center['company'] }}</td>
                        <td class="whitespace-nowrap px-3 py-2.5 text-neutral-700">{{ $center['city'] }}</td>
                        <td class="whitespace-nowrap px-3 py-2.5"><x-badge :variant="match ($center['status']) { 'Planning' => 'warning', 'Construction' => 'info', 'Operational' => 'primary', default => 'neutral' }" class="whitespace-normal!">{{ $center['status'] }}</x-badge></td>
                        <td class="whitespace-nowrap px-3 py-2.5 text-center tabular-nums text-neutral-900">
                            {{ $center['load'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>
