<?php

namespace App\Filament\Resources\JadwalKeberangkatans\Schemas;

use App\Models\Kapal;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class JadwalKeberangkatanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Keberangkatan')
                    ->description(
                        'Jadwal hanya mengatur kapal, rute, tanggal, jam, dan kapasitas. Harga dipilih penumpang saat checkout.'
                    )
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        Select::make('kapal_id')
                            ->label('Kapal')
                            ->relationship(
                                'kapal',
                                'nama_kapal',
                                modifyQueryUsing: fn ($query) => $query->where('status', 'aktif')
                            )
                            ->getOptionLabelFromRecordUsing(
                                fn ($record): string =>
                                    "{$record->nama_kapal} - Kapasitas {$record->kapasitas_penumpang}"
                            )
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(function ($state, $set): void {
                                $kapasitas = Kapal::find($state)?->kapasitas_penumpang ?? 0;
                                $set('kapasitas_total', $kapasitas);
                            })
                            ->required(),

                        Select::make('rute_id')
                            ->label('Rute Penyeberangan')
                            ->relationship(
                                'rute',
                                'pelabuhan_asal',
                                modifyQueryUsing: fn ($query) => $query->where('status', 'aktif')
                            )
                            ->getOptionLabelFromRecordUsing(
                                fn ($record): string =>
                                    "{$record->pelabuhan_asal} ke {$record->pelabuhan_tujuan}"
                            )
                            ->searchable()
                            ->preload()
                            ->required(),

                        DatePicker::make('tanggal_berangkat')
                            ->label('Tanggal Berangkat')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->minDate(today())
                            ->helperText('Gunakan tanggal hari ini atau tanggal berikutnya agar jadwal tampil pada halaman pemesanan user.')
                            ->required(),

                        TimePicker::make('jam_berangkat')
                            ->label('Jam Berangkat')
                            ->native(false)
                            ->seconds(false)
                            ->required(),
                    ]),

                Section::make('Informasi Jadwal')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextInput::make('kapasitas_total')
                            ->label('Kapasitas Total')
                            ->numeric()
                            ->default(0)
                            ->disabled()
                            ->dehydrated(true)
                            ->helperText('Mengikuti kapasitas kapal yang dipilih.')
                            ->required(),

                        Select::make('status')
                            ->label('Status Jadwal')
                            ->options([
                                'tersedia' => 'Tersedia',
                                'batal' => 'Tidak Tersedia',
                            ])
                            ->helperText(
                                'Tersedia = tampil di user. Tidak Tersedia = disembunyikan dari user.'
                            )
                            ->native(false)
                            ->default('tersedia')
                            ->required(),
                    ]),
            ]);
    }
}
