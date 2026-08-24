<?php

namespace App\Filament\Widgets;

use App\Models\HasilOptimasi;
use App\Models\JadwalKeberangkatan;
use App\Models\Kapal;
use App\Models\PemesananTiket;
use App\Models\Penumpang;
use App\Models\Rute;
use App\Support\FerryTariff;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatistikDashboardWidget extends StatsOverviewWidget
{
    protected ?string $heading = 'Ringkasan Sistem';

    protected ?string $description =
        'Monitoring pemesanan, pendapatan, jadwal, dan optimasi tiket ferry.';

    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()?->canViewReportData() ?? false;
    }

    protected function getStats(): array
    {
        $totalKapal = Kapal::count();
        $totalRute = Rute::count();
        $jadwalTersedia = JadwalKeberangkatan::where('status', 'tersedia')->count();
        $totalPenumpang = Penumpang::count();
        $pending = PemesananTiket::where('status_pemesanan', 'pending')->count();
        $diterima = PemesananTiket::where('status_pemesanan', 'diterima')->count();
        $totalTransaksi = (int) PemesananTiket::sum('total_harga');
        $pendapatanDiterima = (int) PemesananTiket::where(
            'status_pemesanan',
            'diterima'
        )->sum('total_harga');
        $loadFactor = HasilOptimasi::avg('load_factor') ?? 0;

        return [
            Stat::make('Total Transaksi', FerryTariff::rupiah($totalTransaksi))
                ->description('Nilai seluruh pemesanan')
                ->descriptionIcon(Heroicon::OutlinedBanknotes)
                ->color('info'),

            Stat::make(
                'Pemesanan Diterima',
                FerryTariff::rupiah($pendapatanDiterima)
            )
                ->description(number_format($diterima) . ' pemesanan diterima')
                ->descriptionIcon(Heroicon::OutlinedCheckCircle)
                ->color('success'),

            Stat::make('Pemesanan Pending', number_format($pending))
                ->description('Menunggu proses alokasi')
                ->descriptionIcon(Heroicon::OutlinedTicket)
                ->color($pending > 0 ? 'warning' : 'success'),

            Stat::make('Jadwal Tersedia', number_format($jadwalTersedia))
                ->description('Siap untuk checkout')
                ->descriptionIcon(Heroicon::OutlinedCalendarDays)
                ->color('success'),

            Stat::make('Total Penumpang', number_format($totalPenumpang))
                ->description('Akun penumpang terdaftar')
                ->descriptionIcon(Heroicon::OutlinedUsers)
                ->color('warning'),

            Stat::make('Rata-rata Load Factor', number_format($loadFactor, 2) . '%')
                ->description(
                    number_format($totalKapal) . ' kapal / '
                    . number_format($totalRute) . ' rute'
                )
                ->descriptionIcon(Heroicon::OutlinedChartBar)
                ->color($loadFactor >= 80 ? 'success' : 'info'),
        ];
    }
}
