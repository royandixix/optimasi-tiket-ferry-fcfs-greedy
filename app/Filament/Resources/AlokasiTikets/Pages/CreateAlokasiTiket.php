<?php

namespace App\Filament\Resources\AlokasiTikets\Pages;

use App\Filament\Resources\AlokasiTikets\AlokasiTiketResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateAlokasiTiket extends CreateRecord
{
    protected static string $resource = AlokasiTiketResource::class;

    protected static ?string $title = 'Tambah Data Alokasi Tiket';

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['diproses_oleh'] = $data['diproses_oleh'] ?? Auth::id();

        return $data;
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Data alokasi tiket berhasil ditambahkan';
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}