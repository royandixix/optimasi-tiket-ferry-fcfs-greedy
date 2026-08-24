<?php

namespace App\Filament\Resources\JadwalKeberangkatans\Pages;

use App\Filament\Resources\JadwalKeberangkatans\JadwalKeberangkatanResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListJadwalKeberangkatans extends ListRecords
{
    protected static string $resource = JadwalKeberangkatanResource::class;

    protected static ?string $title = 'Data Jadwal Keberangkatan';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah Jadwal')
                ->visible(fn (): bool => static::getResource()::canCreate()),
        ];
    }
}