<?php

namespace App\Filament\Resources\Rutes\Pages;

use App\Filament\Resources\Rutes\RuteResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRute extends EditRecord
{
    protected static string $resource = RuteResource::class;

    protected static ?string $title = 'Ubah Data Rute';

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->label('Hapus Rute')
                ->modalHeading('Hapus Data Rute')
                ->modalDescription('Data rute yang dihapus tidak dapat dikembalikan. Pastikan rute ini tidak sedang digunakan pada jadwal keberangkatan.')
                ->modalSubmitActionLabel('Ya, Hapus'),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['pelabuhan_asal'] = trim($data['pelabuhan_asal']);
        $data['pelabuhan_tujuan'] = trim($data['pelabuhan_tujuan']);

        return $data;
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Data rute berhasil diperbarui';
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}