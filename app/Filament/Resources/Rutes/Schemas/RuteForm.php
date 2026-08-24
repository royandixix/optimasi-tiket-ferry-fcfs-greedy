<?php

namespace App\Filament\Resources\Rutes\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class RuteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Rute Penyeberangan')
                    ->description('Data rute digunakan sebagai dasar penentuan jadwal keberangkatan kapal ferry.')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextInput::make('pelabuhan_asal')
                            ->label('Pelabuhan Asal')
                            ->placeholder('Contoh: Siwa')
                            ->maxLength(100)
                            ->required(),

                        TextInput::make('pelabuhan_tujuan')
                            ->label('Pelabuhan Tujuan')
                            ->placeholder('Contoh: Tobaku')
                            ->maxLength(100)
                            ->required(),

                        Select::make('status')
                            ->label('Status Rute')
                            ->options([
                                'aktif' => 'Aktif',
                                'nonaktif' => 'Nonaktif',
                            ])
                            ->native(false)
                            ->default('aktif')
                            ->required()
                            ->columnSpanFull(),
                    ]),

                Section::make('Keterangan Tambahan')
                    ->description('Bagian ini dapat digunakan untuk mencatat informasi tambahan terkait rute penyeberangan.')
                    ->columnSpanFull()
                    ->schema([
                        Textarea::make('keterangan')
                            ->label('Keterangan')
                            ->placeholder('Masukkan keterangan jika diperlukan')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}