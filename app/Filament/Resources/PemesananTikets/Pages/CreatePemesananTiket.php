<?php

namespace App\Filament\Resources\PemesananTikets\Pages;

use App\Filament\Resources\PemesananTikets\PemesananTiketResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePemesananTiket extends CreateRecord
{
    protected static string $resource = PemesananTiketResource::class;

    protected static ?string $title = 'Tambah Pemesanan Tiket';

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
