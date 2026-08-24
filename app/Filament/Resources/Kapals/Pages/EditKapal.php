<?php

namespace App\Filament\Resources\Kapals\Pages;

use App\Filament\Resources\Kapals\KapalResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditKapal extends EditRecord
{
    protected static string $resource = KapalResource::class;

    protected static ?string $title = 'Ubah Data Kapal';

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->label('Hapus Kapal')
                ->modalHeading('Hapus Data Kapal')
                ->modalDescription('Data kapal yang dihapus tidak dapat dikembalikan. Pastikan kapal ini tidak sedang digunakan pada jadwal keberangkatan.')
                ->modalSubmitActionLabel('Ya, Hapus'),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['kode_kapal'] = filled($data['kode_kapal'] ?? null) ? strtoupper(trim($data['kode_kapal'])) : null;
        $data['nama_kapal'] = trim($data['nama_kapal']);

        return $data;
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Data kapal berhasil diperbarui';
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
