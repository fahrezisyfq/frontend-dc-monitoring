<div class="space-y-4" data-dashboard>
    @include('livewire.pages.dashboard.summary')

    <section aria-label="Grafik dan persebaran pusat data" class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
        @include('livewire.pages.dashboard.trend')
        @include('livewire.pages.dashboard.status')
        @include('livewire.pages.dashboard.map')
    </section>

    @include('livewire.pages.dashboard.table')
</div>
