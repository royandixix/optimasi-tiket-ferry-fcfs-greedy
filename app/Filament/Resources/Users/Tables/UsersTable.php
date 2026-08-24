<?php

namespace App\Filament\Resources\Users\Tables;

use App\Models\User;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('name')->label('Nama User')->searchable()->sortable(),
                TextColumn::make('email')->label('Email')->searchable()->sortable(),
                TextColumn::make('role')
                    ->label('Role')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        User::ROLE_ADMIN => 'Admin',
                        User::ROLE_PETUGAS => 'Petugas Validasi',
                        User::ROLE_USER => 'User',
                        default => ucfirst($state),
                    })
                    ->color(fn (string $state): string => match ($state) {
                        User::ROLE_ADMIN => 'warning',
                        User::ROLE_PETUGAS => 'success',
                        User::ROLE_USER => 'gray',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => $state === 'aktif' ? 'Aktif' : 'Nonaktif')
                    ->color(fn (string $state): string => $state === 'aktif' ? 'success' : 'danger')
                    ->sortable(),
                TextColumn::make('email_verified_at')
                    ->label('Verifikasi Email')
                    ->dateTime('d M Y H:i')
                    ->placeholder('-')
                    ->toggleable(),
                TextColumn::make('created_at')->label('Dibuat')->dateTime('d M Y H:i')->sortable(),
                TextColumn::make('updated_at')
                    ->label('Diubah')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->label('Filter Role')
                    ->options([
                        User::ROLE_USER => 'User',
                        User::ROLE_ADMIN => 'Admin',
                        User::ROLE_PETUGAS => 'Petugas Validasi',
                    ]),
                SelectFilter::make('status')
                    ->label('Filter Status')
                    ->options(['aktif' => 'Aktif', 'nonaktif' => 'Nonaktif']),
            ])
            ->recordActions([
                ViewAction::make()->label('Detail'),
                EditAction::make()->label('Ubah'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('Hapus Data Terpilih'),
                ]),
            ])
            ->emptyStateHeading('Belum ada data user')
            ->emptyStateDescription('Tambahkan user untuk mengatur akses sistem.');
    }
}
