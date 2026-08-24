<?php

namespace App\Filament\Resources\Penumpangs\Pages;

use App\Filament\Resources\Penumpangs\PenumpangResource;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Throwable;

class EditPenumpang extends EditRecord
{
    protected static string $resource = PenumpangResource::class;

    protected static ?string $title = 'Ubah Data Penumpang';

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->label('Hapus Penumpang')
                ->modalHeading('Hapus Data Penumpang')
                ->modalDescription(
                    'Data penumpang yang dihapus tidak dapat dikembalikan. Pastikan data ini tidak sedang digunakan pada pemesanan tiket.'
                )
                ->modalSubmitActionLabel('Ya, Hapus'),
        ];
    }

    protected function beforeSave(): void
    {
        $user = auth()->user();

        if (! $user || ! $user->canManageBookingData()) {
            Notification::make()
                ->danger()
                ->title('Data penumpang tidak dapat diubah')
                ->body(
                    'Anda tidak memiliki hak akses untuk mengubah data penumpang.'
                )
                ->persistent()
                ->send();

            $this->halt();
        }
    }

    protected function handleRecordUpdate(
        Model $record,
        array $data
    ): Model {
        try {
            $record->update($data);

            return $record;
        } catch (Throwable $exception) {
            report($exception);

            Notification::make()
                ->danger()
                ->title('Data penumpang gagal diubah')
                ->body(
                    'Terjadi kesalahan saat menyimpan perubahan. Periksa kembali data yang dimasukkan lalu coba lagi.'
                )
                ->persistent()
                ->send();

            $this->halt();

            return $record;
        }
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Data penumpang berhasil diperbarui')
            ->body('Perubahan data penumpang telah berhasil disimpan.');
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}