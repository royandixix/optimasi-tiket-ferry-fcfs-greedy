<?php

namespace App\Services;

use App\Models\PemesananTiket;
use App\Models\ValidasiTiket;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TicketValidationService
{
    /**
     * @return array{
     *     success: bool,
     *     title: string,
     *     message: string,
     *     booking_code?: string,
     *     passenger_name?: string
     * }
     */
    public function validate(string $input, int $validatedBy): array
    {
        $input = trim($input);

        if ($input === '') {
            return [
                'success' => false,
                'title' => 'Kode tiket belum diisi',
                'message' => 'Scan QR Code atau masukkan kode booking terlebih dahulu.',
            ];
        }

        return DB::transaction(function () use ($input, $validatedBy): array {
            $booking = PemesananTiket::query()
                ->with([
                    'penumpang',
                    'jadwal.kapal',
                    'jadwal.rute',
                    'divalidasiOleh',
                ])
                ->where(function (Builder $query) use ($input): void {
                    $query
                        ->where('qr_token', $input)
                        ->orWhere('kode_pemesanan', Str::upper($input));
                })
                ->lockForUpdate()
                ->first();

            if (! $booking) {
                $this->recordAttempt(
                    booking: null,
                    validatedBy: $validatedBy,
                    method: $this->detectInputMethod($input),
                    input: $input,
                    status: 'gagal',
                    failureReason: 'QR Code atau kode booking tidak ditemukan.',
                );

                return [
                    'success' => false,
                    'title' => 'Tiket tidak ditemukan',
                    'message' => 'QR Code atau kode booking tidak terdaftar di dalam sistem.',
                ];
            }

            $method = $this->detectMatchedMethod($booking, $input);
            $failureReason = $this->getFailureReason($booking);

            if ($failureReason !== null) {
                $this->recordAttempt(
                    booking: $booking,
                    validatedBy: $validatedBy,
                    method: $method,
                    input: $input,
                    status: 'gagal',
                    failureReason: $failureReason,
                );

                return [
                    'success' => false,
                    'title' => 'Validasi tiket gagal',
                    'message' => $failureReason,
                ];
            }

            $booking->update([
                'digunakan_pada' => now(),
                'divalidasi_oleh' => $validatedBy,
            ]);

            $this->recordAttempt(
                booking: $booking,
                validatedBy: $validatedBy,
                method: $method,
                input: $input,
                status: 'berhasil',
                failureReason: null,
            );

            return [
                'success' => true,
                'title' => 'Validasi tiket berhasil',
                'message' => sprintf(
                    'Tiket %s atas nama %s berhasil divalidasi. Jumlah tiket: %d. Total harga: Rp%s.',
                    $booking->kode_pemesanan,
                    $booking->penumpang?->nama_penumpang ?? '-',
                    $booking->jumlah_tiket,
                    number_format((float) $booking->total_harga, 0, ',', '.'),
                ),
                'booking_code' => $booking->kode_pemesanan,
                'passenger_name' => $booking->penumpang?->nama_penumpang ?? '-',
            ];
        });
    }

    private function getFailureReason(PemesananTiket $booking): ?string
    {
        if ($booking->status_pemesanan === 'pending') {
            return 'Pemesanan masih menunggu konfirmasi admin.';
        }

        if ($booking->status_pemesanan === 'ditolak') {
            return 'Pemesanan tiket telah ditolak oleh admin.';
        }

        if ($booking->status_pemesanan !== 'diterima') {
            return 'Status pemesanan tidak memenuhi persyaratan validasi.';
        }

        if ($booking->digunakan_pada !== null) {
            $validationTime = $booking->digunakan_pada->format('d M Y H:i');
            $officerName = $booking->divalidasiOleh?->name ?? '-';

            return "Tiket sudah pernah digunakan pada {$validationTime} oleh {$officerName}.";
        }

        if (! $booking->jadwal) {
            return 'Data jadwal keberangkatan tidak ditemukan.';
        }

        if ($booking->jadwal->status === 'batal') {
            return 'Jadwal keberangkatan telah dibatalkan.';
        }

        if ($booking->jadwal->status === 'selesai') {
            return 'Jadwal keberangkatan telah selesai.';
        }

        return null;
    }

    private function detectMatchedMethod(PemesananTiket $booking, string $input): string
    {
        if (
            filled($booking->qr_token)
            && hash_equals((string) $booking->qr_token, $input)
        ) {
            return 'qr_code';
        }

        return 'kode_booking';
    }

    private function detectInputMethod(string $input): string
    {
        return Str::startsWith(Str::upper($input), 'PM-')
            ? 'kode_booking'
            : 'qr_code';
    }

    private function recordAttempt(
        ?PemesananTiket $booking,
        int $validatedBy,
        string $method,
        string $input,
        string $status,
        ?string $failureReason,
    ): void {
        ValidasiTiket::create([
            'pemesanan_tiket_id' => $booking?->id,
            'divalidasi_oleh' => $validatedBy,
            'metode_validasi' => $method,
            'nilai_diperiksa' => $input,
            'status_validasi' => $status,
            'alasan_gagal' => $failureReason,
            'waktu_validasi' => now(),
        ]);
    }
}
