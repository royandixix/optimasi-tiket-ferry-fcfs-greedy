<?php

namespace App\Filament\Resources\Penumpangs\Pages;

use App\Filament\Resources\Penumpangs\PenumpangResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePenumpang extends CreateRecord
{
    protected static string $resource = PenumpangResource::class;

    protected static ?string $title = 'Tambah Data Penumpang';

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Data penumpang berhasil ditambahkan';
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}