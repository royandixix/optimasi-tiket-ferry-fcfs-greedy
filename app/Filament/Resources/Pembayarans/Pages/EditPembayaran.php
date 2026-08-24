<?php

namespace App\Filament\Resources\Pembayarans\Pages;

use App\Filament\Resources\Pembayarans\PembayaranResource;
use Filament\Resources\Pages\EditRecord;

class EditPembayaran extends EditRecord
{
    protected static string $resource = PembayaranResource::class;

    protected static ?string $title = 'Verifikasi Pembayaran';

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (in_array($data['status_pembayaran'], ['dibayar', 'ditolak'], true)) {
            $data['diverifikasi_oleh'] = auth()->id();
        } else {
            $data['diverifikasi_oleh'] = null;
        }

        $data['dibayar_pada'] = $data['status_pembayaran'] === 'dibayar'
            ? now()
            : null;

        return $data;
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Status pembayaran berhasil diperbarui';
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
