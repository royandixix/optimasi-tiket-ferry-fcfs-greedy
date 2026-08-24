<?php

namespace App\Filament\Resources\AlokasiTikets\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AlokasiTiketsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('waktu_pemesanan', 'desc')
            ->columns([
                TextColumn::make('kode_pemesanan')
                    ->label('Kode')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('penumpang.nama_penumpang')
                    ->label('Nama')
                    ->searchable(),

                TextColumn::make('jadwal.kapal.nama_kapal')
                    ->label('Kapal')
                    ->searchable(),

                TextColumn::make('jadwal.rute.pelabuhan_asal')
                    ->label('Asal')
                    ->searchable(),

                TextColumn::make('jadwal.rute.pelabuhan_tujuan')
                    ->label('Tujuan')
                    ->searchable(),

                TextColumn::make('waktu_pemesanan')
                    ->label('Tanggal')
                    ->date('d-m-Y')
                    ->sortable(),

                TextColumn::make('waktu_pemesanan')
                    ->label('Jam')
                    ->dateTime('H:i:s')
                    ->sortable(),

                TextColumn::make('jumlah_tiket')
                    ->label('Nilai Prioritas')
                    ->numeric()
                    ->sortable(),
            ])
            ->emptyStateHeading('Belum ada pemesanan tiket')
            ->emptyStateDescription(
                'Data akan muncul otomatis setelah user melakukan pemesanan tiket.'
            );
    }
}