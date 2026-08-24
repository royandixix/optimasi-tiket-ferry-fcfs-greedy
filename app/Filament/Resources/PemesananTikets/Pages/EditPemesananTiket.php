<?php

namespace App\Filament\Resources\PemesananTikets\Pages;

use App\Filament\Resources\PemesananTikets\PemesananTiketResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPemesananTiket extends EditRecord
{
    protected static string $resource = PemesananTiketResource::class;

    protected static ?string $title = 'Validasi Pemesanan Tiket';

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->label('Hapus Pemesanan')
                ->modalHeading('Hapus Pemesanan Tiket')
                ->modalDescription('Data pemesanan tiket yang dihapus tidak dapat dikembalikan.')
                ->modalSubmitActionLabel('Ya, Hapus')
                ->visible(fn (): bool => auth()->user()?->isAdmin() ?? false),
        ];
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Pemesanan berhasil divalidasi';
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
