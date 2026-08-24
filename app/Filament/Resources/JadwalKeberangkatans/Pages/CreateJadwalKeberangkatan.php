<?php

namespace App\Filament\Resources\JadwalKeberangkatans\Pages;

use App\Filament\Resources\JadwalKeberangkatans\JadwalKeberangkatanResource;
use App\Models\Kapal;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Validation\ValidationException;

class CreateJadwalKeberangkatan extends CreateRecord
{
    protected static string $resource = JadwalKeberangkatanResource::class;

    protected static ?string $title = 'Tambah Jadwal Keberangkatan';

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $kapal = Kapal::query()->find($data['kapal_id'] ?? null);

        if (! $kapal) {
            throw ValidationException::withMessages([
                'kapal_id' => 'Kapal yang dipilih tidak ditemukan.',
            ]);
        }

        if ($kapal->status !== 'aktif') {
            throw ValidationException::withMessages([
                'kapal_id' => 'Kapal nonaktif tidak dapat digunakan untuk jadwal baru.',
            ]);
        }

        // Kapasitas jadwal selalu mengikuti data kapal pada saat jadwal dibuat.
        // Nilai dari browser tidak dipercaya sebagai sumber utama.
        $data['kapasitas_total'] = max((int) $kapal->kapasitas_penumpang, 0);
        $data['kapasitas_terpakai'] = 0;
        $data['sisa_kapasitas'] = $data['kapasitas_total'];
        $data['status'] = ($data['status'] ?? 'tersedia') === 'batal'
            ? 'batal'
            : ($data['kapasitas_total'] > 0 ? 'tersedia' : 'penuh');

        return $data;
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Jadwal keberangkatan berhasil ditambahkan';
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
