<?php

namespace App\Filament\Auth\Responses;

use App\Filament\Pages\Dashboard;
use App\Filament\Resources\ValidasiTikets\ValidasiTiketResource;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Filament\Facades\Filament;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;

class RoleLoginResponse implements LoginResponse
{
    public function toResponse(
        $request
    ): RedirectResponse|Redirector {
        $user = Filament::auth()->user();

        if ($user?->isPetugas()) {
            return redirect()->to(
                ValidasiTiketResource::getUrl(
                    'index',
                    panel: 'admin'
                )
            );
        }

        return redirect()->to(
            Dashboard::getUrl(panel: 'admin')
        );
    }
}
