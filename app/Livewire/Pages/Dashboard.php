<?php

namespace App\Livewire\Pages;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class Dashboard extends Component
{
    private const DATA_CENTERS = [
        // WIB - UTC+7
        [
            'name' => 'DC Jakarta Alpha',
            'company' => 'Nusantara Cloud',
            'city' => 'Jakarta, DKI Jakarta',
            'status' => 'Operational',
            'load' => 24,
            'latitude' => -6.2088,
            'longitude' => 106.8456,
        ],
        [
            'name' => 'DC Bekasi Beta',
            'company' => 'Satria Data',
            'city' => 'Bekasi, Jawa Barat',
            'status' => 'Construction',
            'load' => 12,
            'latitude' => -6.2383,
            'longitude' => 106.9756,
        ],
        [
            'name' => 'DC Batam Gamma',
            'company' => 'Bumi Digital',
            'city' => 'Batam, Kepulauan Riau',
            'status' => 'Operational',
            'load' => 32,
            'latitude' => 1.0456,
            'longitude' => 104.0305,
        ],
        [
            'name' => 'DC Surabaya Delta',
            'company' => 'Garuda Infrastruktur',
            'city' => 'Surabaya, Jawa Timur',
            'status' => 'Planning',
            'load' => 10,
            'latitude' => -7.2575,
            'longitude' => 112.7521,
        ],
        [
            'name' => 'DC Medan Epsilon',
            'company' => 'Metro Compute',
            'city' => 'Medan, Sumatera Utara',
            'status' => 'Operational',
            'load' => 20,
            'latitude' => 3.5952,
            'longitude' => 98.6722,
        ],
        [
            'name' => 'DC Bandung Zeta',
            'company' => 'Nusantara Cloud',
            'city' => 'Bandung, Jawa Barat',
            'status' => 'Decommissioned',
            'load' => 8,
            'latitude' => -6.9175,
            'longitude' => 107.6191,
        ],
        // WITA - UTC+8
        [
            'name' => 'DC Denpasar Eta',
            'company' => 'Bali Digital Hub',
            'city' => 'Denpasar, Bali',
            'status' => 'Operational',
            'load' => 16,
            'latitude' => -8.6500,
            'longitude' => 115.2167,
        ],
        [
            'name' => 'DC Makassar Theta',
            'company' => 'Sulawesi Cloud',
            'city' => 'Makassar, Sulawesi Selatan',
            'status' => 'Construction',
            'load' => 20,
            'latitude' => -5.1477,
            'longitude' => 119.4327,
        ],
        [
            'name' => 'DC Balikpapan Iota',
            'company' => 'Kalimantan Data Network',
            'city' => 'Balikpapan, Kalimantan Timur',
            'status' => 'Operational',
            'load' => 28,
            'latitude' => -1.2379,
            'longitude' => 116.8529,
        ],
        [
            'name' => 'DC Samarinda Kappa',
            'company' => 'Borneo Compute',
            'city' => 'Samarinda, Kalimantan Timur',
            'status' => 'Planning',
            'load' => 14,
            'latitude' => -0.5022,
            'longitude' => 117.1536,
        ],
        [
            'name' => 'DC Manado Lambda',
            'company' => 'Sulawesi Cloud',
            'city' => 'Manado, Sulawesi Utara',
            'status' => 'Operational',
            'load' => 12,
            'latitude' => 1.4748,
            'longitude' => 124.8421,
        ],
        [
            'name' => 'DC Mataram Mu',
            'company' => 'Nusa Digital',
            'city' => 'Mataram, Nusa Tenggara Barat',
            'status' => 'Decommissioned',
            'load' => 6,
            'latitude' => -8.5833,
            'longitude' => 116.1167,
        ],

        // WIT - UTC+9
        [
            'name' => 'DC Jayapura Nu',
            'company' => 'Papua Digital Network',
            'city' => 'Jayapura, Papua',
            'status' => 'Operational',
            'load' => 18,
            'latitude' => -2.5337,
            'longitude' => 140.7181,
        ],
        [
            'name' => 'DC Sorong Xi',
            'company' => 'Eastern Cloud',
            'city' => 'Sorong, Papua Barat Daya',
            'status' => 'Construction',
            'load' => 15,
            'latitude' => -0.8762,
            'longitude' => 131.2558,
        ],
        [
            'name' => 'DC Ambon Omicron',
            'company' => 'Maluku Digital Hub',
            'city' => 'Ambon, Maluku',
            'status' => 'Operational',
            'load' => 10,
            'latitude' => -3.6954,
            'longitude' => 128.1814,
        ],
        [
            'name' => 'DC Manokwari Pi',
            'company' => 'Papua Digital Network',
            'city' => 'Manokwari, Papua Barat',
            'status' => 'Planning',
            'load' => 12,
            'latitude' => -0.8615,
            'longitude' => 134.0620,
        ],
        [
            'name' => 'DC Ternate Rho',
            'company' => 'Eastern Cloud',
            'city' => 'Ternate, Maluku Utara',
            'status' => 'Operational',
            'load' => 8,
            'latitude' => 0.7903,
            'longitude' => 127.3842,
        ],
        [
            'name' => 'DC Merauke Sigma',
            'company' => 'Papua Infrastruktur',
            'city' => 'Merauke, Papua Selatan',
            'status' => 'Decommissioned',
            'load' => 5,
            'latitude' => -8.4932,
            'longitude' => 140.4018,
        ],

    ];

    private const STATUSES = ['Planning', 'Construction', 'Operational', 'Decommissioned'];

    public function render(): View
    {
        return view('livewire.pages.dashboard', [
            ...$this->summarizeDataCenters(),
            'dataCenters' => self::DATA_CENTERS,
            'yearlyCounts' => [1, 2, 3, 4, 5, 6],
        ])->layout('layouts.app', ['title' => 'Dashboard']);
    }

    private function summarizeDataCenters(): array
    {
        $companies = [];
        $totalCapacity = 0;
        $statusCounts = array_fill_keys(self::STATUSES, 0);

        foreach (self::DATA_CENTERS as $center) {
            $companies[$center['company']] = true;
            $statusCounts[$center['status']]++;

            if ($center['status'] !== 'Decommissioned') {
                $totalCapacity += $center['load'];
            }
        }

        return [
            'dataCenterCount' => count(self::DATA_CENTERS),
            'companyCount' => count($companies),
            'totalCapacity' => $totalCapacity,
            'statusCounts' => $statusCounts,
        ];
    }
}
