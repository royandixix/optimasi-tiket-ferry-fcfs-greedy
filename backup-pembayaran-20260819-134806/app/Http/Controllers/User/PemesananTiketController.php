<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\JadwalKeberangkatan;
use App\Models\PemesananTiket;
use App\Models\Penumpang;
use App\Services\JadwalRecommendationService;
use App\Support\FerryTariff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PemesananTiketController extends Controller
{
    public function __construct(
        private readonly JadwalRecommendationService $recommendationService
    ) {
    }

    public function index(Request $request)
    {
        $penumpang = $this->getOrCreatePenumpang();

        $pemesanans = PemesananTiket::query()
            ->with(['jadwal.kapal', 'jadwal.rute'])
            ->where('penumpang_id', $penumpang->id)
            ->latest()
            ->paginate(10);

        // Katalog kapal selalu dibaca ulang dari database pada setiap request.
        // Dengan demikian kapal yang baru ditambahkan admin langsung terlihat
        // saat user membuka/refresh halaman tanpa perlu logout atau daftar ulang.
        $jadwals = $this->getAvailableJadwals();
        $kapalAktifs = $this->recommendationService->activeShips();
        $jadwalsByKapal = $jadwals->groupBy('kapal_id');
        $rekomendasiJadwals = collect();

        // Modal rekomendasi dibuka saat user masuk melalui menu "Pemesanan".
        if ($request->boolean('rekomendasi')) {
            $rekomendasiJadwals = $this->recommendationService
                ->topRecommendations($jadwals, 3);
        }

        return response()
            ->view(
                'user.pemesanan.index',
                compact(
                    'pemesanans',
                    'rekomendasiJadwals',
                    'kapalAktifs',
                    'jadwalsByKapal'
                )
            )
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache');
    }

    public function create(Request $request)
    {
        $jadwals = $this->getAvailableJadwals();

        // Jika user datang dari kartu kapal tertentu, tampilkan jadwal kapal itu
        // lebih dahulu tanpa mengunci pilihan user ke kapal tersebut.
        if ($request->filled('kapal_id')) {
            $kapalId = (int) $request->integer('kapal_id');
            $jadwals = $jadwals
                ->sortByDesc(fn (JadwalKeberangkatan $jadwal): bool =>
                    (int) $jadwal->kapal_id === $kapalId
                )
                ->values();
        }

        $kapalAktifs = $this->recommendationService->activeShips();
        $jadwalsByKapal = $jadwals->groupBy('kapal_id');
        $rekomendasiJadwals = $this->recommendationService
            ->topRecommendations($jadwals, 3);
        $rekomendasiIds = $rekomendasiJadwals
            ->pluck('id')
            ->values()
            ->all();

        return response()
            ->view(
                'user.pemesanan.create',
                compact(
                    'jadwals',
                    'rekomendasiJadwals',
                    'rekomendasiIds',
                    'kapalAktifs',
                    'jadwalsByKapal'
                )
            )
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache');
    }

    public function store(Request $request)
    {
        $validated = $this->validateBooking($request);
        $penumpang = $this->getOrCreatePenumpang();
        $pricing = FerryTariff::calculate(
            $validated['jenis_tarif'],
            $validated['jumlah_tiket']
        );

        DB::transaction(function () use (
            $validated,
            $penumpang,
            $pricing
        ): void {
            $jadwal = $this->lockBookableSchedule((int) $validated['jadwal_id']);
            $this->validateQuantityAgainstShipCapacity(
                $pricing['jumlah_tiket'],
                $jadwal
            );

            PemesananTiket::create([
                'kode_pemesanan' => 'PM-'
                    . now()->format('YmdHis')
                    . '-'
                    . Str::upper(Str::random(4)),
                'penumpang_id' => $penumpang->id,
                'jadwal_id' => $validated['jadwal_id'],
                'jenis_tarif' => $pricing['jenis_tarif'],
                'tarif_label' => $pricing['tarif_label'],
                'satuan' => $pricing['satuan'],
                'harga_satuan' => $pricing['harga_satuan'],
                'jumlah_tiket' => $pricing['jumlah_tiket'],
                'total_harga' => $pricing['total_harga'],
                'waktu_pemesanan' => now(),
                'status_pemesanan' => 'pending',
                'metode_alokasi' => null,
                'created_by' => Auth::guard('web')->id(),
                'catatan' => $validated['catatan'] ?? null,
            ]);
        });

        return redirect()
            ->route('user.pemesanan.index')
            ->with(
                'success',
                'Pemesanan berhasil dibuat dan menunggu konfirmasi admin. Total pembayaran: '
                . FerryTariff::rupiah($pricing['total_harga'])
                . '.'
            );
    }

    public function show(PemesananTiket $pemesanan)
    {
        $this->authorizeOwnedBooking($pemesanan);

        $pemesanan->load([
            'jadwal.kapal',
            'jadwal.rute',
            'alokasiTikets',
        ]);

        return view('user.pemesanan.show', compact('pemesanan'));
    }

    public function edit(PemesananTiket $pemesanan)
    {
        $this->authorizeOwnedBooking($pemesanan);

        if ($pemesanan->status_pemesanan !== 'pending') {
            return redirect()
                ->route('user.pemesanan.show', $pemesanan)
                ->with(
                    'error',
                    'Pemesanan yang sudah diproses tidak dapat diubah.'
                );
        }

        $jadwals = $this->getAvailableJadwals();
        $rekomendasiJadwals = $this->recommendationService
            ->topRecommendations($jadwals, 3);
        $rekomendasiIds = $rekomendasiJadwals
            ->pluck('id')
            ->values()
            ->all();

        return view(
            'user.pemesanan.edit',
            compact(
                'pemesanan',
                'jadwals',
                'rekomendasiJadwals',
                'rekomendasiIds'
            )
        );
    }

    public function update(
        Request $request,
        PemesananTiket $pemesanan
    ) {
        $this->authorizeOwnedBooking($pemesanan);

        if ($pemesanan->status_pemesanan !== 'pending') {
            return redirect()
                ->route('user.pemesanan.show', $pemesanan)
                ->with(
                    'error',
                    'Pemesanan yang sudah diproses tidak dapat diubah.'
                );
        }

        $validated = $this->validateBooking($request);
        $pricing = FerryTariff::calculate(
            $validated['jenis_tarif'],
            $validated['jumlah_tiket']
        );

        DB::transaction(function () use (
            $validated,
            $pricing,
            $pemesanan
        ): void {
            $jadwal = $this->lockBookableSchedule((int) $validated['jadwal_id']);
            $this->validateQuantityAgainstShipCapacity(
                $pricing['jumlah_tiket'],
                $jadwal
            );

            $pemesanan->update([
                'jadwal_id' => $validated['jadwal_id'],
                'jenis_tarif' => $pricing['jenis_tarif'],
                'tarif_label' => $pricing['tarif_label'],
                'satuan' => $pricing['satuan'],
                'harga_satuan' => $pricing['harga_satuan'],
                'jumlah_tiket' => $pricing['jumlah_tiket'],
                'total_harga' => $pricing['total_harga'],
                'catatan' => $validated['catatan'] ?? null,
            ]);
        });

        return redirect()
            ->route('user.pemesanan.index')
            ->with(
                'success',
                'Pemesanan berhasil diperbarui dan tetap menunggu konfirmasi admin. Total pembayaran: '
                . FerryTariff::rupiah($pricing['total_harga'])
                . '.'
            );
    }

    private function validateBooking(Request $request): array
    {
        return $request->validate([
            'jadwal_id' => [
                'required',
                'exists:jadwal_keberangkatans,id',
            ],
            'jenis_tarif' => [
                'required',
                Rule::in(FerryTariff::codes()),
            ],
            'jumlah_tiket' => [
                'required',
                'integer',
                'min:1',
            ],
            'catatan' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ], [
            'jadwal_id.required' => 'Jadwal keberangkatan wajib dipilih.',
            'jadwal_id.exists' => 'Jadwal keberangkatan tidak valid.',
            'jenis_tarif.required' => 'Jenis tarif wajib dipilih.',
            'jenis_tarif.in' => 'Jenis tarif yang dipilih tidak valid.',
            'jumlah_tiket.required' => 'Jumlah tiket atau unit wajib diisi.',
            'jumlah_tiket.integer' => 'Jumlah harus berupa angka bulat.',
            'jumlah_tiket.min' => 'Jumlah minimal 1.',
        ]);
    }

    private function getOrCreatePenumpang(): Penumpang
    {
        $user = Auth::guard('web')->user();

        return Penumpang::firstOrCreate(
            ['user_id' => $user->id],
            [
                'nama_penumpang' => $user->name,
                'nik' => null,
                'jenis_kelamin' => null,
                'no_hp' => null,
                'alamat' => null,
            ]
        );
    }

    private function authorizeOwnedBooking(
        PemesananTiket $pemesanan
    ): void {
        $penumpang = $this->getOrCreatePenumpang();

        if ((int) $pemesanan->penumpang_id !== (int) $penumpang->id) {
            abort(403, 'Anda tidak memiliki akses ke pemesanan ini.');
        }
    }

    private function getAvailableJadwals()
    {
        return $this->recommendationService->availableSchedules();
    }

    private function lockBookableSchedule(int $jadwalId): JadwalKeberangkatan
    {
        $jadwal = JadwalKeberangkatan::query()
            ->whereKey($jadwalId)
            ->where('status', 'tersedia')
            ->whereDate('tanggal_berangkat', '>=', now()->toDateString())
            ->whereHas('kapal', fn ($query) => $query->where('status', 'aktif'))
            ->whereHas('rute', fn ($query) => $query->where('status', 'aktif'))
            ->lockForUpdate()
            ->first();

        if (! $jadwal) {
            throw ValidationException::withMessages([
                'jadwal_id' => 'Jadwal tidak lagi tersedia atau tanggal keberangkatannya sudah lewat.',
            ]);
        }

        return $jadwal;
    }

    private function validateQuantityAgainstShipCapacity(
        int $jumlah,
        JadwalKeberangkatan $jadwal
    ): void {
        $kapasitasTotal = max((int) $jadwal->kapasitas_total, 0);

        if ($jumlah > $kapasitasTotal) {
            throw ValidationException::withMessages([
                'jumlah_tiket' => "Satu pemesanan maksimal {$kapasitasTotal} tiket/unit sesuai kapasitas total kapal.",
            ]);
        }
    }
}
