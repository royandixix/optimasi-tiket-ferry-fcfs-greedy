<?php

namespace App\Services;

use App\Models\JadwalKeberangkatan;
use App\Models\Kapal;
use Illuminate\Support\Collection;

class JadwalRecommendationService
{
    public function activeShips(): Collection
    {
        return Kapal::query()
            ->where('status', 'aktif')
            ->orderBy('nama_kapal')
            ->get();
    }

    public function availableSchedules(): Collection
    {
        return JadwalKeberangkatan::query()
            ->with([
                'kapal',
                'rute',
            ])
            ->whereHas('kapal', function ($query): void {
                $query->where('status', 'aktif');
            })
            ->whereHas('rute', function ($query): void {
                $query->where('status', 'aktif');
            })
            ->withSum([
                'pemesananTikets as total_tiket_aktif' => function ($query): void {
                    $query->whereIn(
                        'status_pemesanan',
                        ['pending', 'diterima']
                    );
                },
            ], 'jumlah_tiket')
            ->where('status', 'tersedia')
            ->where('kapasitas_total', '>', 0)
            ->whereDate(
                'tanggal_berangkat',
                '>=',
                now()->toDateString()
            )
            ->orderBy('tanggal_berangkat')
            ->orderBy('jam_berangkat')
            ->get()
            ->map(function (JadwalKeberangkatan $jadwal): JadwalKeberangkatan {
                $kapasitas = max(
                    (int) $jadwal->kapasitas_total,
                    0
                );

                $permintaanAktif = max(
                    (int) ($jadwal->total_tiket_aktif ?? 0),
                    0
                );

                $perkiraanSisa = max(
                    $kapasitas - $permintaanAktif,
                    0
                );

                $persenKosong = $kapasitas > 0
                    ? ($perkiraanSisa / $kapasitas) * 100
                    : 0;

                $persenTerisi = $kapasitas > 0
                    ? min(
                        ($permintaanAktif / $kapasitas) * 100,
                        100
                    )
                    : 100;

                [$label, $level] = $this->availabilityLabel(
                    $persenKosong
                );

                $jadwal->setAttribute(
                    'rekomendasi_permintaan_aktif',
                    $permintaanAktif
                );

                $jadwal->setAttribute(
                    'rekomendasi_sisa',
                    $perkiraanSisa
                );

                $jadwal->setAttribute(
                    'rekomendasi_persen_kosong',
                    round($persenKosong, 1)
                );

                $jadwal->setAttribute(
                    'rekomendasi_persen_terisi',
                    round($persenTerisi, 1)
                );

                $jadwal->setAttribute(
                    'rekomendasi_label',
                    $label
                );

                $jadwal->setAttribute(
                    'rekomendasi_level',
                    $level
                );

                return $jadwal;
            })
            ->filter(
                fn (JadwalKeberangkatan $jadwal): bool =>
                    (int) ($jadwal->rekomendasi_sisa ?? 0) > 0
            )
            ->values();
    }

    public function topRecommendations(
        Collection $jadwals,
        int $limit = 3
    ): Collection {
        return $jadwals
            ->filter(
                fn (JadwalKeberangkatan $jadwal): bool =>
                    (int) ($jadwal->rekomendasi_sisa ?? 0) > 0
            )
            ->sort(function (
                JadwalKeberangkatan $a,
                JadwalKeberangkatan $b
            ): int {
                $byPercent =
                    ((float) $b->rekomendasi_persen_kosong)
                    <=>
                    ((float) $a->rekomendasi_persen_kosong);

                if ($byPercent !== 0) {
                    return $byPercent;
                }

                $byRemaining =
                    ((int) $b->rekomendasi_sisa)
                    <=>
                    ((int) $a->rekomendasi_sisa);

                if ($byRemaining !== 0) {
                    return $byRemaining;
                }

                $aDate = sprintf(
                    '%s %s',
                    optional($a->tanggal_berangkat)
                        ->format('Y-m-d') ?? '9999-12-31',
                    (string) ($a->jam_berangkat ?? '23:59:59')
                );

                $bDate = sprintf(
                    '%s %s',
                    optional($b->tanggal_berangkat)
                        ->format('Y-m-d') ?? '9999-12-31',
                    (string) ($b->jam_berangkat ?? '23:59:59')
                );

                return $aDate <=> $bDate;
            })
            ->take(max($limit, 0))
            ->values();
    }

    private function availabilityLabel(
        float $persenKosong
    ): array {
        if ($persenKosong >= 60) {
            return [
                'Sangat Longgar',
                'success',
            ];
        }

        if ($persenKosong >= 30) {
            return [
                'Cukup Longgar',
                'info',
            ];
        }

        if ($persenKosong > 0) {
            return [
                'Mulai Padat',
                'warning',
            ];
        }

        return [
            'Permintaan Tinggi',
            'danger',
        ];
    }
}