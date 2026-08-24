<?php

namespace App\Filament\Resources\Kapals\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class KapalsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                ImageColumn::make('gambar_kapal')
                    ->label('Gambar')
                    ->disk('public')
                    ->imageHeight(48)
                    ->stacked()
                    ->limit(3)
                    ->limitedRemainingText(),

                TextColumn::make('kode_kapal')
                    ->label('Kode Kapal')
                    ->searchable()
                    ->sortable()
                    ->placeholder('-'),

                TextColumn::make('nama_kapal')
                    ->label('Nama Kapal')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('kapasitas_penumpang')
                    ->label('Kapasitas')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color('info'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(
                        fn (string $state): string => match ($state) {
                            'aktif' => 'Aktif',
                            'nonaktif' => 'Nonaktif',
                            default => ucfirst($state),
                        }
                    )
                    ->color(
                        fn (string $state): string => match ($state) {
                            'aktif' => 'success',
                            'nonaktif' => 'danger',
                            default => 'gray',
                        }
                    )
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
            ->emptyStateHeading('Belum ada data kapal')
            ->emptyStateDescription(
                'Tambahkan data kapal ferry agar dapat digunakan pada jadwal keberangkatan.'
            );
    }
}
