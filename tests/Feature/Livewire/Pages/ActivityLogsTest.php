<?php

namespace Tests\Feature\Livewire\Pages;

use App\Livewire\Pages\ActivityLogs\Index as ActivityLogs;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

class ActivityLogsTest extends TestCase
{
    public function test_reusable_controls_preserve_columns_bindings_and_activity_variants(): void
    {
        $component = Livewire::test(ActivityLogs::class);
        $document = new \DOMDocument;
        @$document->loadHTML($component->html());
        $xpath = new \DOMXPath($document);

        $this->assertSame(['No.', 'Pengguna', 'Aktivitas', 'Deskripsi', 'Waktu'], array_map(
            fn ($heading) => trim($heading->textContent), iterator_to_array($xpath->query('//table/thead/tr/th')),
        ));
        $input = $xpath->query('//input[@id="activity-search"]')->item(0);
        $this->assertSame('search', $input->getAttribute('type'));
        $this->assertSame('search', $input->getAttribute('wire:model.live.debounce.300ms'));
        $this->assertSame('Cari nama pengguna atau deskripsi', $input->getAttribute('placeholder'));
        $expected = [
            'Created' => 'bg-primary text-white',
            'Restored' => 'bg-primary text-white',
            'Updated' => 'bg-cyan/15 text-neutral-900',
            'Deleted' => 'bg-red/10 text-red',
            'Login' => 'bg-primary/10 text-primary',
            'Logout' => 'bg-neutral-100 text-neutral-700',
        ];
        $badges = $xpath->query('//table/tbody/tr/td[3]/span');
        $this->assertCount(6, $badges);
        foreach ($badges as $badge) {
            $this->assertStringContainsString($expected[trim($badge->textContent)], $badge->getAttribute('class'));
        }
    }

    public function test_search_matches_user_or_description_without_case_or_surrounding_spaces(): void
    {
        Livewire::test(ActivityLogs::class)
            ->set('search', ' SUPER ADMIN ')
            ->assertSee('3 dari 6 aktivitas')->assertSee('Berhasil masuk ke sistem')->assertDontSee('Keluar dari sistem')
            ->set('search', ' JAKARTA ALPHA ')
            ->assertSee('1 dari 6 aktivitas')->assertSee('Mengubah data pusat data Jakarta Alpha DC')
            ->assertDontSee('Berhasil masuk ke sistem')
            ->set('search', 'missing')->assertSee('Aktivitas tidak ditemukan');
    }

    public function test_each_activity_type_filters_the_rows(): void
    {
        foreach (['Login', 'Logout', 'Created', 'Updated', 'Deleted', 'Restored'] as $activity) {
            Livewire::test(ActivityLogs::class)->set('activity', $activity)
                ->assertViewHas('filteredLogs', fn ($logs) => count($logs) === 1 && array_values($logs)[0]['activity'] === $activity);
        }
    }

    public function test_date_bounds_are_inclusive_and_can_be_used_independently(): void
    {
        Livewire::test(ActivityLogs::class)
            ->set('startDate', '2026-11-18')->assertSee('6 dari 6 aktivitas')
            ->set('endDate', '2026-11-18')->assertSee('6 dari 6 aktivitas')
            ->set('startDate', '2026-11-19')->assertSee('0 dari 6 aktivitas')
            ->assertSee('Tanggal akhir harus sama dengan atau setelah tanggal awal.')
            ->set('endDate', '')->assertSee('0 dari 6 aktivitas')
            ->set('startDate', '')->set('endDate', '2026-11-17')->assertSee('0 dari 6 aktivitas')
            ->set('endDate', '2026-11-18')->assertSee('6 dari 6 aktivitas');
    }

    public function test_combined_filters_and_reset_preserve_dummy_data_without_database_queries(): void
    {
        $queries = [];
        DB::listen(function ($query) use (&$queries) {
            $queries[] = $query->sql;
        });
        $component = Livewire::test(ActivityLogs::class);
        $originalLogs = $component->get('logs');

        $component->set('search', 'Admin')->set('activity', 'Updated')
            ->set('startDate', '2026-11-18')->set('endDate', '2026-11-18')
            ->assertSee('1 dari 6 aktivitas')->assertSee('Mengubah data pusat data Jakarta Alpha DC')
            ->set('search', 'Super Admin')->assertSee('Aktivitas tidak ditemukan')
            ->call('resetFilters')->assertSee('6 dari 6 aktivitas')->assertDontSee('Aktivitas tidak ditemukan')
            ->assertSet('search', '')->assertSet('activity', '')->assertSet('startDate', '')->assertSet('endDate', '')
            ->assertSet('logs', $originalLogs);

        $this->assertSame([], $queries);
    }
}
