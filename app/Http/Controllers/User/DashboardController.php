<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\PemesananTiket;
use App\Services\JadwalRecommendationService;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct(
        private readonly JadwalRecommendationService $recommendationService
    ) {
    }

    public function index()
    {
        $user = Auth::user();
        $penumpang = $user->penumpang;

        $totalPemesanan = 0;
        $pending = 0;
        $diterima = 0;
        $ditolak = 0;
        $totalNilai = 0;
        $nilaiDiterima = 0;
        $pemesananTerbaru = collect();

        if ($penumpang) {
            $query = PemesananTiket::query()
                ->where('penumpang_id', $penumpang->id);

            $totalPemesanan = (clone $query)->count();
            $pending = (clone $query)->where('status_pemesanan', 'pending')->count();
            $diterima = (clone $query)->where('status_pemesanan', 'diterima')->count();
            $ditolak = (clone $query)->where('status_pemesanan', 'ditolak')->count();
            $totalNilai = (int) (clone $query)->sum('total_harga');
            $nilaiDiterima = (int) (clone $query)
                ->where('status_pemesanan', 'diterima')
                ->sum('total_harga');

            $pemesananTerbaru = (clone $query)
                ->with(['jadwal.kapal', 'jadwal.rute'])
                ->latest()
                ->limit(5)
                ->get();
        }

        // Dibaca langsung dari data operasional admin setiap dashboard dibuka.
        $kapalAktifCount = $this->recommendationService->activeShips()->count();
        $jadwalTersediaCount = $this->recommendationService->availableSchedules()->count();

        return response()
            ->view('user.dashboard', compact(
                'user',
                'penumpang',
                'totalPemesanan',
                'pending',
                'diterima',
                'ditolak',
                'totalNilai',
                'nilaiDiterima',
                'pemesananTerbaru',
                'kapalAktifCount',
                'jadwalTersediaCount',
            ))
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache');
    }
}
