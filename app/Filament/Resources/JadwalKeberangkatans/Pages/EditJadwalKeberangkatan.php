<?php

namespace App\Filament\Resources\JadwalKeberangkatans\Pages;

use App\Filament\Resources\JadwalKeberangkatans\JadwalKeberangkatanResource;
use App\Models\Kapal;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Validation\ValidationException;

class EditJadwalKeberangkatan extends EditRecord
{
    protected static string $resource = JadwalKeberangkatanResource::class;

    protected static ?string $title = 'Ubah Jadwal Keberangkatan';

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Admin cukup melihat 2 status.
        // Penuh, selesai, dan batal dianggap Tidak Tersedia.
        $data['status'] = ($data['status'] ?? null) === 'tersedia'
            ? 'tersedia'
            : 'batal';

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->label('Hapus Jadwal')
                ->modalHeading('Hapus Jadwal Keberangkatan')
                ->modalDescription('Data jadwal yang dihapus tidak dapat dikembalikan.')
                ->modalSubmitActionLabel('Ya, Hapus'),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $kapal = Kapal::query()->find($data['kapal_id'] ?? null);

        if (! $kapal) {
            throw ValidationException::withMessages([
                'kapal_id' => 'Kapal yang dipilih tidak ditemukan.',
            ]);
        }

        $kapasitasTotal = max((int) $kapal->kapasitas_penumpang, 0);
        $kapasitasTerpakai = max((int) ($this->record->kapasitas_terpakai ?? 0), 0);

        // Saat kapal diganti/diedit, kapasitas total disinkronkan ulang dari data
        // kapal. Kapasitas terpakai tidak boleh diubah manual dari form.
        $data['kapasitas_total'] = $kapasitasTotal;
        $data['kapasitas_terpakai'] = min($kapasitasTerpakai, $kapasitasTotal);
        $data['sisa_kapasitas'] = max(
            $kapasitasTotal - $data['kapasitas_terpakai'],
            0
        );

        if (($data['status'] ?? 'tersedia') === 'batal') {
            $data['status'] = 'batal';
        } else {
            $data['status'] = $data['sisa_kapasitas'] > 0
                ? 'tersedia'
                : 'penuh';
        }

        return $data;
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Jadwal keberangkatan berhasil diperbarui';
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
