<?php

namespace App\Filament\Resources\Rutes\Pages;

use App\Filament\Resources\Rutes\RuteResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRutes extends ListRecords
{
    protected static string $resource = RuteResource::class;

    protected static ?string $title = 'Data Rute Penyeberangan';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah Rute')
                ->visible(fn (): bool => static::getResource()::canCreate()),
        ];
    }
}