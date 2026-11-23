<?php

namespace Tests\Feature\Livewire\Pages;

use App\Livewire\Pages\Trash\Index as Trash;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

class TrashTest extends TestCase
{
    public function test_reusable_controls_keep_columns_badges_and_confirmation_bindings(): void
    {
        $component = Livewire::test(Trash::class);
        $document = new \DOMDocument;
        @$document->loadHTML($component->html());
        $xpath = new \DOMXPath($document);
        $this->assertSame(['No.', 'Jenis Data', 'Nama Data', 'Dihapus Oleh', 'Tanggal Dihapus', 'Aksi'], array_map(
            fn ($heading) => trim($heading->textContent), iterator_to_array($xpath->query('//table/thead/tr/th')),
        ));
        $input = $xpath->query('//input[@id="trash-search"]')->item(0);
        $this->assertSame('search', $input->getAttribute('wire:model.live.debounce.300ms'));
        $this->assertSame('Cari nama data terhapus', $input->getAttribute('placeholder'));
        $this->assertCount(1, $xpath->query('//*[@x-ref="trashTable"]//*[@role="region" and @tabindex="0"]'));
        $colors = ['Perusahaan' => 'bg-primary/10 text-primary', 'Data Center' => 'bg-cyan/15 text-neutral-900', 'Pengguna' => 'bg-neutral-100 text-neutral-700'];
        $rows = $xpath->query('//table/tbody/tr');
        $this->assertCount(8, $rows);
        foreach ($rows as $row) {
            $badge = $xpath->query('./td[2]/span', $row)->item(0);
            $this->assertStringContainsString($colors[trim($badge->textContent)], $badge->getAttribute('class'));
            $this->assertCount(2, $xpath->query('./td[6]//button', $row));
        }
        foreach (['restore' => 'bg-primary', 'delete' => 'bg-red'] as $action => $color) {
            $component->call('openModal', 1, $action);
            @$document->loadHTML($component->html());
            $xpath = new \DOMXPath($document);
            $button = $xpath->query('//button[@*[name()="wire:click"]="confirm"]')->item(0);
            $this->assertSame('button', $button->getAttribute('type'));
            $this->assertSame('disabled', $button->getAttribute('wire:loading.attr'));
            $this->assertStringContainsString($color, $button->getAttribute('class'));
            $this->assertCount(2, $xpath->query('.//span[@*[name()="wire:target"]="confirm"]', $button));
        }
    }

    public function test_search_matches_only_names_without_case_or_surrounding_spaces(): void
    {
        Livewire::test(Trash::class)->set('search', ' DIGITAL ')
            ->assertSee('2 dari 8 data contoh')->assertSee('PT Contoh Digital')
            ->assertSee('Bandung Digital Park')->assertDontSee('Budi Santoso')
            ->set('search', 'Super Admin')->assertSee('Data tidak ditemukan')
            ->assertSee('Ubah pencarian, jenis data, atau rentang tanggal, atau gunakan tombol Reset.');
    }

    public function test_each_data_type_filters_the_rows(): void
    {
        foreach (['Perusahaan' => [1, 4, 7], 'Data Center' => [3, 5, 8], 'Pengguna' => [2, 6]] as $type => $ids) {
            Livewire::test(Trash::class)->set('type', $type)
                ->assertViewHas('filteredItems', fn ($items) => array_column($items, 'id') === $ids);
        }
    }

    public function test_date_bounds_are_inclusive_and_work_independently(): void
    {
        Livewire::test(Trash::class)->set('startDate', '2026-11-17')
            ->assertSee('6 dari 8 data contoh')->assertDontSee('Surabaya Beta DC')
            ->set('endDate', '2026-11-17')->assertSee('3 dari 8 data contoh')
            ->assertSee('Sari Wulandari')->assertDontSee('Budi Santoso')
            ->set('startDate', '')->assertSee('5 dari 8 data contoh')
            ->assertSee('Surabaya Beta DC')
            ->set('startDate', '2026-11-18')->assertSee('Data tidak ditemukan')
            ->assertSee('Tanggal akhir harus sama dengan atau setelah tanggal awal.');
    }

    public function test_combined_filters_and_reset_preserve_samples_without_database_queries(): void
    {
        $queries = [];
        DB::listen(function ($query) use (&$queries) {
            $queries[] = $query->sql;
        });
        $component = Livewire::test(Trash::class);
        $originalItems = $component->get('items');

        $component->set('search', 'digital')->set('type', 'Data Center')
            ->set('startDate', '2026-11-17')->set('endDate', '2026-11-17')
            ->assertSee('1 dari 8 data contoh')->assertSee('Bandung Digital Park')
            ->assertDontSee('PT Contoh Digital')
            ->set('startDate', '2026-11-18')->assertSee('Data tidak ditemukan')
            ->call('resetFilters')->assertSee('8 dari 8 data contoh')
            ->assertSet('search', '')->assertSet('type', '')
            ->assertSet('startDate', '')->assertSet('endDate', '')
            ->assertSet('items', $originalItems);

        $this->assertSame([], $queries);
    }

    public function test_actions_on_filtered_rows_keep_filters_and_reset_does_not_restore_removed_items(): void
    {
        Livewire::test(Trash::class)->set('type', 'Pengguna')
            ->call('openModal', 6, 'restore')->call('confirm')
            ->assertSet('type', 'Pengguna')->assertSee('1 dari 7 data contoh')
            ->assertSee('Budi Santoso')->assertDontSee('Sari Wulandari')
            ->call('openModal', 2, 'delete')->call('confirm')
            ->assertSee('Data tidak ditemukan')->assertSee('0 dari 6 data contoh')
            ->call('resetFilters')->assertSee('6 dari 6 data contoh')
            ->assertDontSee('Budi Santoso')->assertDontSee('Sari Wulandari');
    }

    public function test_confirmed_actions_only_remove_dummy_items_without_database_queries(): void
    {
        $queries = [];
        DB::listen(function ($query) use (&$queries) {
            $queries[] = $query->sql;
        });
        $component = Livewire::test(Trash::class);

        $component->call('openModal', 1, 'restore')->assertCount('items', 8)
            ->assertSee('Pulihkan Data')->call('confirm')
            ->assertCount('items', 7)->assertDontSee('PT Contoh Digital')
            ->assertSet('modal', null)->assertSet('selectedId', null)
            ->assertDispatched('toast', type: 'success', message: 'Data berhasil dipulihkan dalam simulasi.')
            ->call('openModal', 2, 'delete')->assertSee('data tidak dapat dipulihkan kembali.')
            ->call('confirm')->assertCount('items', 6)->assertDontSee('Budi Santoso')
            ->assertDispatched('toast', type: 'success', message: 'Data berhasil dihapus permanen dalam simulasi.')
            ->call('confirm')->assertCount('items', 6);

        Livewire::test(Trash::class)->assertCount('items', 8)->assertSee('PT Contoh Digital')->assertSee('Budi Santoso');
        $this->assertSame([], $queries);
    }

    public function test_cancellation_and_invalid_actions_leave_the_list_unchanged(): void
    {
        $component = Livewire::test(Trash::class);

        $component->call('confirm')->assertCount('items', 8)
            ->call('openModal', 1, 'delete')->call('closeModal')->call('confirm')
            ->assertCount('items', 8)->assertSet('modal', null)
            ->call('openModal', 999, 'restore')->call('confirm')
            ->assertCount('items', 8)->assertSet('selectedId', null)
            ->call('openModal', 1, 'invalid')->call('confirm')
            ->assertCount('items', 8)->assertSet('modal', null)
            ->assertNotDispatched('toast');
    }

    public function test_removing_all_samples_shows_the_empty_state(): void
    {
        $component = Livewire::test(Trash::class);

        foreach (range(1, 8) as $id) {
            $component->call('openModal', $id, 'restore')->call('confirm');
        }

        $component->assertCount('items', 0)->assertSee('Tidak ada data terhapus');
    }
}
