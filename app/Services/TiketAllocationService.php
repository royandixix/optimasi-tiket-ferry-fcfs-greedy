<?php

namespace App\Services;

use App\Models\AlokasiTiket;
use App\Models\HasilOptimasi;
use App\Models\JadwalKeberangkatan;
use App\Models\PemesananTiket;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class TiketAllocationService
{
    public function process(int $jadwalId, string $metode): HasilOptimasi
    {
        if (! in_array($metode, ['fcfs', 'greedy'], true)) {
            throw new InvalidArgumentException(
                'Metode alokasi tidak valid.'
            );
        }

        return DB::transaction(function () use ($jadwalId, $metode) {
            $startTime = microtime(true);

            $jadwal = JadwalKeberangkatan::query()
                ->lockForUpdate()
                ->findOrFail($jadwalId);

            $kapasitasKapal = (int) $jadwal->kapasitas_total;

            if ($kapasitasKapal <= 0) {
                throw new InvalidArgumentException(
                    'Kapasitas kapal harus lebih dari 0.'
                );
            }

            $sisaKapasitas = $kapasitasKapal;

            AlokasiTiket::query()
                ->where('jadwal_id', $jadwal->id)
                ->where('metode', $metode)
                ->delete();

            $query = PemesananTiket::query()
                ->where('jadwal_id', $jadwal->id);

            if ($metode === 'fcfs') {
                $query
                    ->orderBy('waktu_pemesanan', 'asc')
                    ->orderBy('id', 'asc');
            }

            if ($metode === 'greedy') {
                $query
                    ->orderBy('jumlah_tiket', 'desc')
                    ->orderBy('waktu_pemesanan', 'asc')
                    ->orderBy('id', 'asc');
            }

            $pemesanans = $query
                ->lockForUpdate()
                ->get();

            if ($pemesanans->isEmpty()) {
                throw new InvalidArgumentException(
                    'Jadwal ini belum memiliki pemesanan tiket.'
                );
            }

            $totalPemesanan = $pemesanans->count();
            $totalTiketDiminta = 0;
            $totalTiketDiterima = 0;
            $totalTiketDitolak = 0;

            foreach ($pemesanans as $pemesanan) {
                $jumlahTiket = (int) $pemesanan->jumlah_tiket;

                if ($jumlahTiket <= 0) {
                    continue;
                }

                $nilaiPrioritas = $jumlahTiket;

                $totalTiketDiminta += $jumlahTiket;

                $sisaSebelum = $sisaKapasitas;

                if ($nilaiPrioritas <= $sisaSebelum) {
                    $statusAlokasi = 'diterima';

                    $jumlahDialokasikan = $nilaiPrioritas;

                    $sisaKapasitas =
                        $sisaSebelum - $nilaiPrioritas;

                    $totalTiketDiterima +=
                        $jumlahDialokasikan;
                } else {
                    $statusAlokasi = 'ditolak';

                    $jumlahDialokasikan = 0;

                    $sisaKapasitas = $sisaSebelum;

                    $totalTiketDitolak += $jumlahTiket;
                }

                AlokasiTiket::create([
                    'pemesanan_tiket_id' => $pemesanan->id,
                    'jadwal_id' => $jadwal->id,
                    'metode' => $metode,
                    'jumlah_dialokasikan' => $jumlahDialokasikan,
                    'nilai_prioritas' => $nilaiPrioritas,
                    'sisa_kapasitas_sebelum' => $sisaSebelum,
                    'sisa_kapasitas_sesudah' => $sisaKapasitas,
                    'status_alokasi' => $statusAlokasi,
                    'diproses_oleh' => Auth::id(),
                ]);
            }

            $kapasitasTerpakai = $totalTiketDiterima;

            $loadFactor =
                ($kapasitasTerpakai / $kapasitasKapal) * 100;

            $waktuProsesMs =
                (microtime(true) - $startTime) * 1000;

            return HasilOptimasi::updateOrCreate(
                [
                    'jadwal_id' => $jadwal->id,
                    'metode' => $metode,
                ],
                [
                    'total_pemesanan' => $totalPemesanan,
                    'total_tiket_diminta' => $totalTiketDiminta,
                    'total_tiket_diterima' => $totalTiketDiterima,
                    'total_tiket_ditolak' => $totalTiketDitolak,
                    'kapasitas_kapal' => $kapasitasKapal,
                    'kapasitas_terpakai' => $kapasitasTerpakai,
                    'load_factor' => round($loadFactor, 2),
                    'waktu_proses_ms' => round($waktuProsesMs, 4),
                    'diproses_oleh' => Auth::id(),
                ]
            );
        });
    }
}