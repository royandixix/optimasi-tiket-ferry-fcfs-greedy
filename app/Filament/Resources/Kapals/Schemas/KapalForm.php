<?php

namespace App\Filament\Resources\Kapals\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class KapalForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Kapal')
                    ->description(
                        'Data kapal digunakan sebagai dasar jadwal keberangkatan dan perhitungan kapasitas tiket.'
                    )
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextInput::make('kode_kapal')
                            ->label('Kode Kapal')
                            ->placeholder('Contoh: KPL-001')
                            ->maxLength(50)
                            ->unique(ignoreRecord: true),

                        TextInput::make('nama_kapal')
                            ->label('Nama Kapal')
                            ->placeholder('Contoh: KMP Afta Trans')
                            ->maxLength(150)
                            ->required(),

                        TextInput::make('kapasitas_penumpang')
                            ->label('Kapasitas Penumpang')
                            ->placeholder('Contoh: 120')
                            ->numeric()
                            ->minValue(1)
                            ->required(),

                        Select::make('status')
                            ->label('Status Kapal')
                            ->options([
                                'aktif' => 'Aktif',
                                'nonaktif' => 'Nonaktif',
                            ])
                            ->native(false)
                            ->default('aktif')
                            ->required(),
                    ]),

                Section::make('Galeri Kapal')
                    ->description(
                        'Unggah sekurang-kurangnya 3 gambar yang memperlihatkan kondisi kapal.'
                    )
                    ->columnSpanFull()
                    ->schema([
                        FileUpload::make('gambar_kapal')
                            ->label('Gambar Kapal')
                            ->image()
                            ->multiple()
                            ->required()
                            ->minFiles(3)
                            ->disk('public')
                            ->directory('kapal')
                            ->visibility('public')
                            ->acceptedFileTypes([
                                'image/jpeg',
                                'image/png',
                                'image/webp',
                            ])
                            ->maxSize(5120)
                            ->reorderable()
                            ->openable()
                            ->downloadable()
                            ->helperText(
                                'Wajib minimal 3 gambar. Format JPG, PNG, atau WEBP. Maksimal 5 MB per gambar.'
                            )
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
