<?php

namespace App\Filament\Pages;

use App\Filament\Resources\ValidasiTikets\ValidasiTiketResource;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $title = 'Dashboard';

    public function mount(): void
    {
        if (auth()->user()?->isPetugas()) {
            $this->redirect(
                ValidasiTiketResource::getUrl(
                    'index',
                    panel: 'admin'
                ),
                navigate: true
            );

            return;
        }
    }

    public static function canAccess(): bool
    {
        return auth()->check();
    }

    public static function shouldRegisterNavigation(): bool
    {
        return ! (auth()->user()?->isPetugas() ?? false);
    }

    public function getTitle(): string
    {
        return 'Dashboard Optimasi Tiket Ferry';
    }

    public function getHeading(): string
    {
        return 'Dashboard Optimasi Tiket Ferry';
    }

    public function getSubheading(): ?string
    {
        return 'Monitoring data kapal, rute, jadwal, pemesanan tiket, alokasi tiket, dan hasil optimasi kapasitas kapal ferry.';
    }

    public function getColumns(): int|array
    {
        return [
            'default' => 1,
            'sm' => 1,
            'md' => 1,
            'lg' => 1,
            'xl' => 2,
            '2xl' => 2,
        ];
    }
}
