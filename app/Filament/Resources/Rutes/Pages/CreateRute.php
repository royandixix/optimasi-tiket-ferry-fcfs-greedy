<?php

namespace App\Filament\Resources\Rutes\Pages;

use App\Filament\Resources\Rutes\RuteResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRute extends CreateRecord
{
    protected static string $resource = RuteResource::class;

    protected static ?string $title = 'Tambah Data Rute';

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['pelabuhan_asal'] = trim($data['pelabuhan_asal']);
        $data['pelabuhan_tujuan'] = trim($data['pelabuhan_tujuan']);
        $data['status'] = $data['status'] ?? 'aktif';

        return $data;
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Data rute berhasil ditambahkan';
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}