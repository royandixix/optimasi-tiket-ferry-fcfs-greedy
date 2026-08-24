<?php

namespace App\Filament\Widgets;

use App\Models\HasilOptimasi;
use Filament\Widgets\ChartWidget;

class LoadFactorChart extends ChartWidget
{
    protected ?string $heading = 'Perbandingan Load Factor';

    protected ?string $description = 'Rata-rata pemanfaatan kapasitas kapal berdasarkan metode FCFS dan Greedy Heuristik.';

    protected static ?int $sort = 3;

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
        $fcfs = HasilOptimasi::where('metode', 'fcfs')->avg('load_factor') ?? 0;
        $greedy = HasilOptimasi::where('metode', 'greedy')->avg('load_factor') ?? 0;

        return [
            'datasets' => [
                [
                    'label' => 'Load Factor (%)',
                    'data' => [
                        round($fcfs, 2),
                        round($greedy, 2),
                    ],
                    'backgroundColor' => [
                        'rgba(100, 116, 139, 0.85)',
                        'rgba(34, 197, 94, 0.85)',
                    ],
                    'borderColor' => [
                        'rgb(100, 116, 139)',
                        'rgb(34, 197, 94)',
                    ],
                    'borderWidth' => 2,
                    'borderRadius' => 10,
                ],
            ],
            'labels' => [
                'FCFS',
                'Greedy Heuristik',
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
