<?php

namespace Tests\Feature\Livewire\Pages;

use App\Livewire\Pages\Dashboard;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    public function test_dashboard_renders_all_sections(): void
    {
        $response = $this->get('/dashboard');

        $response->assertOk()->assertSeeInOrder([
            'Total Perusahaan',
            'Total Data Center',
            'Total Kapasitas IT Load',
            'Perkembangan Pusat Data',
            'Status Pusat Data',
            'Persebaran Pusat Data',
            'Daftar Pusat Data',
        ]);
    }

    public function test_dashboard_preserves_dummy_summaries_and_chart_data(): void
    {
        $data = (new Dashboard)->render()->getData();

        $this->assertSame(14, $data['companyCount']);
        $this->assertSame(18, $data['dataCenterCount']);
        $this->assertCount(18, $data['dataCenters']);
        $this->assertSame(251, $data['totalCapacity']);
        $this->assertSame([
            'Planning' => 3,
            'Construction' => 3,
            'Operational' => 9,
            'Decommissioned' => 3,
        ], $data['statusCounts']);
        $this->assertSame([1, 2, 3, 4, 5, 6], $data['yearlyCounts']);
    }
}
