<?php

namespace App\Filament\Resources\HasilOptimasis\Pages;

use App\Filament\Resources\HasilOptimasis\HasilOptimasiResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditHasilOptimasi extends EditRecord
{
    protected static string $resource = HasilOptimasiResource::class;

    protected static ?string $title = 'Ubah Hasil Optimasi';

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->label('Hapus Data')
                ->modalHeading('Hapus Hasil Optimasi')
                ->modalDescription('Data hasil optimasi yang dihapus tidak dapat dikembalikan.')
                ->modalSubmitActionLabel('Ya, Hapus'),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['diproses_oleh'] = $data['diproses_oleh'] ?? Auth::id();

        return $data;
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Data hasil optimasi berhasil diperbarui';
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}