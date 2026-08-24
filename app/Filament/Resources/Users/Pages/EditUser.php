<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;
class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected static ?string $title = 'Ubah User';

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()
                ->label('Lihat Detail'),

            DeleteAction::make()
                ->label('Hapus User')
                ->modalHeading('Hapus User')
                ->modalDescription('User yang dihapus tidak dapat dikembalikan. Pastikan akun ini tidak sedang digunakan.')
                ->modalSubmitActionLabel('Ya, Hapus')
                ->visible(fn ($record): bool => $record->id !== auth()->id()),
        ];
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'User berhasil diperbarui';
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}