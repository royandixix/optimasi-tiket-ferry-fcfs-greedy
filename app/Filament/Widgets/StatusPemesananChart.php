<?php

namespace App\Filament\Widgets;

use App\Models\PemesananTiket;
use Filament\Widgets\ChartWidget;

class StatusPemesananChart extends ChartWidget
{
    protected ?string $heading = 'Status Pemesanan Tiket';

    protected ?string $description = 'Komposisi status pemesanan tiket yang sudah masuk ke sistem.';

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = [
        'default' => 'full',
        'sm' => 'full',
        'md' => 'full',
        'lg' => 'full',
        'xl' => 1,
        '2xl' => 1,
    ];

    protected ?string $maxHeight = '320px';

    public static function canView(): bool
    {
        return auth()->user()?->canViewReportData() ?? false;
    }

    protected function getData(): array
    {
        $pending = PemesananTiket::where('status_pemesanan', 'pending')->count();
        $diterima = PemesananTiket::where('status_pemesanan', 'diterima')->count();
        $ditolak = PemesananTiket::where('status_pemesanan', 'ditolak')->count();

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Pemesanan',
                    'data' => [
                        $pending,
                        $diterima,
                        $ditolak,
                    ],
                    'backgroundColor' => [
                        'rgba(245, 158, 11, 0.85)',
                        'rgba(34, 197, 94, 0.85)',
                        'rgba(239, 68, 68, 0.85)',
                    ],
                    'borderColor' => [
                        'rgb(245, 158, 11)',
                        'rgb(34, 197, 94)',
                        'rgb(239, 68, 68)',
                    ],
                    'borderWidth' => 2,
                    'hoverOffset' => 8,
                ],
            ],
            'labels' => [
                'Pending',
                'Diterima',
                'Ditolak',
            ],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
