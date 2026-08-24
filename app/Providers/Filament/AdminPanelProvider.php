<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\Login;
use App\Filament\Pages\Dashboard;
use App\Filament\Resources\ValidasiTikets\ValidasiTiketResource;
use App\Filament\Widgets\JadwalTerbaruWidget;
use App\Filament\Widgets\LoadFactorChart;
use App\Filament\Widgets\StatistikDashboardWidget;
use App\Filament\Widgets\StatusPemesananChart;
use App\Http\Middleware\UseAdminGuard;
use Filament\Actions\Action;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->authGuard('admin')

            // Menggunakan halaman login dengan pilihan role.
            ->login(Login::class)

            ->brandName('Optimasi Tiket Ferry')

            ->userMenuItems([
                'logout' => Action::make('logout')
                    ->label('Keluar')
                    ->url(fn (): string => route('admin.logout.safe'))
                    ->postToUrl(),
            ])
            // Menentukan tujuan berdasarkan role.
            ->homeUrl(
                fn (): string => auth()->user()?->isPetugas()
                    ? ValidasiTiketResource::getUrl(
                        'index',
                        panel: 'admin'
                    )
                    : Dashboard::getUrl(panel: 'admin')
            )

            ->sidebarCollapsibleOnDesktop()

            ->colors([
                'primary' => Color::Blue,
                'gray' => Color::Slate,
                'success' => Color::Green,
                'warning' => Color::Amber,
                'danger' => Color::Red,
                'info' => Color::Sky,
            ])

            ->discoverResources(
                in: app_path('Filament/Resources'),
                for: 'App\\Filament\\Resources'
            )

            ->discoverPages(
                in: app_path('Filament/Pages'),
                for: 'App\\Filament\\Pages'
            )

            

            ->pages([
                Dashboard::class,
            ])

            ->widgets([
                StatistikDashboardWidget::class,
                StatusPemesananChart::class,
                LoadFactorChart::class,
                JadwalTerbaruWidget::class,
            ])

            ->middleware([
                UseAdminGuard::class,
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])

            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
