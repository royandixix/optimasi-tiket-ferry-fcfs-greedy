<?php

namespace App\Filament\Resources\Rutes\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class RutesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('pelabuhan_asal')
                    ->label('Pelabuhan Asal')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('pelabuhan_tujuan')
                    ->label('Pelabuhan Tujuan')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('nama_rute')
                    ->label('Rute')
                    ->state(function ($record): string {
                        return "{$record->pelabuhan_asal} → {$record->pelabuhan_tujuan}";
                    })
                    ->badge()
                    ->color('info'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'aktif' => 'Aktif',
                        'nonaktif' => 'Nonaktif',
                        default => ucfirst($state),
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'aktif' => 'success',
                        'nonaktif' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('jadwal_keberangkatans_count')
                    ->label('Total Jadwal')
                    ->counts('jadwalKeberangkatans')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Diubah')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Filter Status')
                    ->options([
                        'aktif' => 'Aktif',
                        'nonaktif' => 'Nonaktif',
                    ]),
            ])
            ->recordActions([
                EditAction::make()
                    ->label('Ubah'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Hapus Data Terpilih'),
                ]),
            ])
            ->emptyStateHeading('Belum ada data rute')
            ->emptyStateDescription('Tambahkan rute penyeberangan seperti Siwa ke Tobaku atau Tobaku ke Siwa.');
    }
}