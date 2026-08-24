<?php

namespace App\Filament\Resources\Kapals\Pages;

use App\Filament\Resources\Kapals\KapalResource;
use Filament\Resources\Pages\CreateRecord;

class CreateKapal extends CreateRecord
{
    protected static string $resource = KapalResource::class;

    protected static ?string $title = 'Tambah Data Kapal';

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['kode_kapal'] = filled($data['kode_kapal'] ?? null) ? strtoupper(trim($data['kode_kapal'])) : null;
        $data['nama_kapal'] = trim($data['nama_kapal']);
        $data['status'] = $data['status'] ?? 'aktif';

        return $data;
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Data kapal berhasil ditambahkan';
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
