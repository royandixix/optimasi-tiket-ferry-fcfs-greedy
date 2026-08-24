<?php

namespace App\Filament\Resources\Kapals\Pages;

use App\Filament\Resources\Kapals\KapalResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListKapals extends ListRecords
{
    protected static string $resource = KapalResource::class;

    protected static ?string $title = 'Data Kapal';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah Kapal')
                ->visible(fn (): bool => static::getResource()::canCreate()),
        ];
    }
}
