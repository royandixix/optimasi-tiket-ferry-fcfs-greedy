<?php

namespace App\Filament\Resources\PemesananTikets\Pages;

use App\Filament\Resources\PemesananTikets\PemesananTiketResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPemesananTikets extends ListRecords
{
    protected static string $resource = PemesananTiketResource::class;

    protected static ?string $title = 'Data Pemesanan Tiket';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah Pemesanan')
                ->visible(fn (): bool => static::getResource()::canCreate()),
        ];
    }
}