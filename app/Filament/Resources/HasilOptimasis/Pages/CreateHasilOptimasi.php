<?php

namespace App\Filament\Resources\HasilOptimasis\Pages;

use App\Filament\Resources\HasilOptimasis\HasilOptimasiResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
class CreateHasilOptimasi extends CreateRecord
{
    protected static string $resource = HasilOptimasiResource::class;

    protected static ?string $title = 'Tambah Hasil Optimasi';

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['diproses_oleh'] = $data['diproses_oleh'] ?? Auth::id();

        return $data;
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Data hasil optimasi berhasil ditambahkan';
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}