<?php

namespace App\Filament\Resources\AlokasiTikets\Pages;

use App\Filament\Resources\AlokasiTikets\AlokasiTiketResource;
use Filament\Resources\Pages\ListRecords;

class ListAlokasiTikets extends ListRecords
{
    protected static string $resource =
        AlokasiTiketResource::class;

    protected static ?string $title =
        'Hasil Alokasi';

    public function getBreadcrumb(): string
    {
        return 'Daftar';
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}