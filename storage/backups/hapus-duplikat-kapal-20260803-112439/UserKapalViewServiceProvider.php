<?php

namespace App\Providers;

use App\Models\JadwalKeberangkatan;
use App\Models\Kapal;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View as ViewInstance;

class UserKapalViewServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        View::composer(
            'user.pemesanan.create',
            function (ViewInstance $view): void {
                /*
                 * Seluruh kapal admin ditampilkan tanpa bergantung
                 * pada tanggal registrasi akun user.
                 */
                $kapals = Kapal::query()
                    ->orderBy('nama_kapal')
                    ->get();

                /*
                 * Jadwal yang dapat dipilih tetap dibatasi:
                 * - status tersedia
                 * - kapasitas masih ada
                 * - tanggal belum lewat
                 */
                $jadwalsByKapal = JadwalKeberangkatan::query()
                    ->with('rute')
                    ->whereIn(
                        'kapal_id',
                        $kapals->pluck('id')
                    )
                    ->whereRaw(
                        'LOWER(status) = ?',
                        ['tersedia']
                    )
                    ->where('sisa_kapasitas', '>', 0)
                    ->whereDate(
                        'tanggal_berangkat',
                        '>=',
                        now()->toDateString()
                    )
                    ->orderBy('tanggal_berangkat')
                    ->orderBy('jam_berangkat')
                    ->get()
                    ->groupBy('kapal_id');

                $view->with([
                    'kapals' => $kapals,
                    'jadwalsByKapal' => $jadwalsByKapal,
                ]);
            }
        );
    }
}
