<?php

namespace App\Filament\Resources\PemesananTikets\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PemesananTiketForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Data Checkout Penumpang')
                    ->description(
                        'Harga ditentukan oleh pilihan penumpang saat checkout. Admin hanya melihat data transaksi.'
                    )
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextInput::make('kode_pemesanan')
                            ->label('Kode Pemesanan')
                            ->disabled()
                            ->dehydrated(false),

                        DateTimePicker::make('waktu_pemesanan')
                            ->label('Waktu Pemesanan')
                            ->native(false)
                            ->seconds(false)
                            ->disabled()
                            ->dehydrated(false),

                        Select::make('penumpang_id')
                            ->label('Nama Penumpang')
                            ->relationship('penumpang', 'nama_penumpang')
                            ->searchable()
                            ->preload()
                            ->disabled()
                            ->dehydrated(false),

                        Select::make('jadwal_id')
                            ->label('Jadwal Keberangkatan')
                            ->relationship('jadwal', 'tanggal_berangkat')
                            ->getOptionLabelFromRecordUsing(function ($record): string {
                                $tanggal = optional($record->tanggal_berangkat)->format('d-m-Y') ?? '-';
                                $jam = $record->jam_berangkat ?? '-';
                                $kapal = $record->kapal?->nama_kapal ?? '-';
                                $asal = $record->rute?->pelabuhan_asal ?? '-';
                                $tujuan = $record->rute?->pelabuhan_tujuan ?? '-';

                                return "{$tanggal} {$jam} - {$kapal} ({$asal} ke {$tujuan})";
                            })
                            ->searchable()
                            ->preload()
                            ->disabled()
                            ->dehydrated(false),
                    ]),

                Section::make('Rincian Harga Checkout')
                    ->description(
                        'Total = harga satuan × jumlah tiket atau unit. Nilai dihitung ulang oleh server.'
                    )
                    ->columnSpanFull()
                    ->columns(3)
                    ->schema([
                        TextInput::make('tarif_label')
                            ->label('Jenis Tarif')
                            ->disabled()
                            ->dehydrated(false),

                        TextInput::make('jumlah_tiket')
                            ->label('Jumlah')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(false),

                        TextInput::make('satuan')
                            ->label('Satuan')
                            ->disabled()
                            ->dehydrated(false),

                        TextInput::make('harga_satuan')
                            ->label('Harga Satuan')
                            ->prefix('Rp')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(false),

                        TextInput::make('total_harga')
                            ->label('Total Harga')
                            ->prefix('Rp')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(false),
                    ]),

                Section::make('Validasi Pemesanan')
                    ->description(
                        'Periksa data pemesanan kemudian tentukan apakah tiket diterima atau ditolak.'
                    )
                    ->columnSpanFull()
                    ->columns(1)
                    ->schema([
                        Select::make('status_pemesanan')
                            ->label('Status Pemesanan')
                            ->options([
                                'pending' => 'Menunggu Konfirmasi',
                                'diterima' => 'Diterima',
                                'ditolak' => 'Ditolak',
                            ])
                            ->native(false)
                            ->required(),

                        Textarea::make('catatan')
                            ->label('Catatan Admin')
                            ->placeholder('Opsional. Isi jika ada keterangan untuk pemesanan ini.')
                            ->rows(3),
                    ]),
            ]);
    }
}
