<?php

namespace App\Filament\Resources\ValidasiTikets\Pages;

use App\Filament\Resources\ValidasiTikets\ValidasiTiketResource;
use App\Services\TicketValidationService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use Marcelorodrigo\FilamentBarcodeScannerField\Forms\Components\BarcodeInput;

class ManageValidasiTikets extends ManageRecords
{
    protected static string $resource = ValidasiTiketResource::class;

    protected static ?string $title = 'Validasi Tiket Penumpang';

    public function getHeading(): string
    {
        return 'Validasi Tiket Penumpang';
    }

    public function getSubheading(): ?string
    {
        return 'Scanner berjalan tanpa batas waktu dan dapat memakai kamera realtime atau foto QR. Admin dan petugas dapat melakukan validasi.';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('validasi_tiket')
                ->label('Validasi Tiket')
                ->icon(Heroicon::OutlinedQrCode)
                ->color('success')
                ->modalHeading('Validasi Tiket Penumpang')
                ->modalDescription(
                    'Izinkan akses kamera lalu arahkan ke QR Code. Scanner terus aktif sampai kode terbaca. Bila ada pantulan lampu, miringkan HP sedikit atau gunakan tombol Fokus Ulang.'
                )
                ->modalSubmitActionLabel('Validasi Sekarang')
                ->modalCancelActionLabel('Batal')
                ->closeModalByClickingAway(false)
                ->schema([
                    BarcodeInput::make('kode_tiket')
                        ->label('QR Code atau Kode Booking')
                        ->placeholder('Contoh: PM-202608021501103-Y4FM')
                        ->helperText(
                            'Setelah QR terbaca, kode otomatis masuk ke kolom ini. Tidak ada batas waktu pemindaian.'
                        )
                        ->required()
                        ->maxLength(100),
                ])
                ->visible(
                    fn (): bool => Auth::user()?->canValidateTickets() ?? false
                )
                ->action(function (array $data): void {
                    $userId = Auth::id();

                    if ($userId === null) {
                        Notification::make()
                            ->title('Sesi login tidak ditemukan')
                            ->body('Silakan login kembali sebelum melakukan validasi tiket.')
                            ->danger()
                            ->persistent()
                            ->send();

                        return;
                    }

                    $kodeTiket = trim((string) ($data['kode_tiket'] ?? ''));

                    if ($kodeTiket === '') {
                        Notification::make()
                            ->title('Kode tiket belum tersedia')
                            ->body('Pindai QR Code atau masukkan kode booking terlebih dahulu.')
                            ->danger()
                            ->persistent()
                            ->send();

                        return;
                    }

                    $result = app(TicketValidationService::class)->validate(
                        input: $kodeTiket,
                        validatedBy: $userId,
                    );

                    $this->resetTable();

                    $notification = Notification::make()
                        ->title($result['title'])
                        ->body($result['message'])
                        ->persistent();

                    if ($result['success']) {
                        $notification->success()->send();

                        return;
                    }

                    $notification->danger()->send();
                }),
        ];
    }
}
