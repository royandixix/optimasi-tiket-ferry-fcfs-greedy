<?php

namespace App\Filament\Resources\AlokasiTikets\Pages;

use App\Filament\Resources\AlokasiTikets\AlokasiTiketResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;
class EditAlokasiTiket extends EditRecord
{
    protected static string $resource = AlokasiTiketResource::class;

    protected static ?string $title = 'Ubah Data Alokasi Tiket';

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->label('Hapus Data')
                ->modalHeading('Hapus Data Alokasi Tiket')
                ->modalDescription('Data alokasi tiket yang dihapus tidak dapat dikembalikan.')
                ->modalSubmitActionLabel('Ya, Hapus'),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['diproses_oleh'] = $data['diproses_oleh'] ?? Auth::id();

        return $data;
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Data alokasi tiket berhasil diperbarui';
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}