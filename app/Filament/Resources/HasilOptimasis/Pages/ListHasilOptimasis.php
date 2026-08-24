<?php

namespace App\Filament\Resources\HasilOptimasis\Pages;

use App\Filament\Resources\HasilOptimasis\HasilOptimasiResource;
use App\Models\AlokasiTiket;
use App\Models\JadwalKeberangkatan;
use App\Services\TiketAllocationService;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Throwable;

class ListHasilOptimasis extends ListRecords
{
    protected static string $resource =
        HasilOptimasiResource::class;

    protected static ?string $title =
        'Proses Optimasi';

    public ?string $metodeAktif = null;

    public ?int $jadwalAktif = null;

    public ?int $jadwalPerbandingan = null;

    public function getBreadcrumb(): string
    {
        return 'Daftar';
    }

    public function getSubheading(): ?string
    {
        if (
            $this->metodeAktif === null
            || $this->jadwalAktif === null
        ) {
            return
                'Pilih Proses Greedy atau Proses FCFS untuk menampilkan hasil optimasi.';
        }

        $jadwal = JadwalKeberangkatan::query()
            ->with([
                'kapal',
                'rute',
            ])
            ->find($this->jadwalAktif);

        if (! $jadwal) {
            return null;
        }

        $kapal =
            $jadwal->kapal?->nama_kapal ?? '-';

        $tanggal =
            optional(
                $jadwal->tanggal_berangkat
            )->format('d-m-Y') ?? '-';

        $jam = $jadwal->jam_berangkat
            ? substr(
                (string) $jadwal->jam_berangkat,
                0,
                8
            )
            : '-';

        if ($this->metodeAktif === 'greedy') {
            return
                "Hasil Greedy untuk {$kapal}, {$tanggal} {$jam}. "
                . 'Pemesanan diurutkan berdasarkan jumlah tiket terbesar. '
                . 'Nilai prioritas adalah jumlah tiket yang dipesan.';
        }

        return
            "Hasil FCFS untuk {$kapal}, {$tanggal} {$jam}. "
            . 'Pemesanan diurutkan berdasarkan waktu pemesanan paling awal. '
            . 'Nilai prioritas adalah jumlah tiket yang dipesan.';
    }

    protected function getTableQuery(): Builder
    {
        $query = AlokasiTiket::query()
            ->with([
                'pemesananTiket.penumpang',
                'jadwal.kapal',
                'jadwal.rute',
            ]);

        if (
            $this->metodeAktif === null
            || $this->jadwalAktif === null
        ) {
            return $query->whereRaw('1 = 0');
        }

        return $query
            ->where(
                'jadwal_id',
                $this->jadwalAktif
            )
            ->where(
                'metode',
                $this->metodeAktif
            )
            ->orderBy(
                'id',
                'asc'
            );
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('proses_greedy')
                ->label('Proses Greedy')
                ->color('success')
                ->schema([
                    Select::make('jadwal_id')
                        ->label(
                            'Kapal dan Jadwal Keberangkatan'
                        )
                        ->options(
                            fn (): array =>
                                $this->getJadwalOptions()
                        )
                        ->default(
                            fn () =>
                                $this->jadwalPerbandingan
                        )
                        ->searchable()
                        ->required()
                        ->helperText(
                            $this->jadwalPerbandingan
                                ? 'Gunakan jadwal yang sama untuk perbandingan Greedy dan FCFS.'
                                : 'Pilih satu kapal dan jadwal yang memiliki data pemesanan.'
                        ),
                ])
                ->modalHeading('Proses Greedy')
                ->modalDescription(
                    'Greedy mengurutkan pemesanan berdasarkan jumlah tiket terbesar. Nilai prioritas sama dengan jumlah tiket yang dipesan.'
                )
                ->modalSubmitActionLabel(
                    'Proses Greedy'
                )
                ->visible(
                    fn (): bool =>
                        auth()
                            ->user()
                            ?->canManageOptimizationData()
                        ?? false
                )
                ->action(
                    function (array $data): void {
                        try {
                            $jadwalId =
                                (int) $data['jadwal_id'];

                            if (
                                $this->jadwalPerbandingan !== null
                                && $this->jadwalPerbandingan !== $jadwalId
                            ) {
                                Notification::make()
                                    ->title(
                                        'Jadwal harus sama'
                                    )
                                    ->body(
                                        'Gunakan kapal dan jadwal yang sama dengan proses sebelumnya.'
                                    )
                                    ->warning()
                                    ->send();

                                return;
                            }

                            $hasil = app(
                                TiketAllocationService::class
                            )->process(
                                $jadwalId,
                                'greedy'
                            );

                            $this->jadwalPerbandingan =
                                $jadwalId;

                            $this->jadwalAktif =
                                $jadwalId;

                            $this->metodeAktif =
                                'greedy';

                            $this->resetTable();

                            Notification::make()
                                ->title(
                                    'Proses Greedy berhasil'
                                )
                                ->body(
                                    'Load Factor Greedy = '
                                    . number_format(
                                        (float) $hasil->load_factor,
                                        2,
                                        ',',
                                        '.'
                                    )
                                    . '%'
                                )
                                ->success()
                                ->send();
                        } catch (Throwable $e) {
                            report($e);

                            Notification::make()
                                ->title(
                                    'Proses Greedy gagal'
                                )
                                ->body(
                                    $e->getMessage()
                                )
                                ->danger()
                                ->send();
                        }
                    }
                ),

            Action::make('proses_fcfs')
                ->label('Proses FCFS')
                ->color('gray')
                ->schema([
                    Select::make('jadwal_id')
                        ->label(
                            'Kapal dan Jadwal Keberangkatan'
                        )
                        ->options(
                            fn (): array =>
                                $this->getJadwalOptions()
                        )
                        ->default(
                            fn () =>
                                $this->jadwalPerbandingan
                        )
                        ->searchable()
                        ->required()
                        ->helperText(
                            $this->jadwalPerbandingan
                                ? 'Gunakan jadwal yang sama untuk perbandingan Greedy dan FCFS.'
                                : 'Pilih satu kapal dan jadwal yang memiliki data pemesanan.'
                        ),
                ])
                ->modalHeading('Proses FCFS')
                ->modalDescription(
                    'FCFS mengurutkan pemesanan berdasarkan waktu pemesanan paling awal. Nilai prioritas tetap sama dengan jumlah tiket yang dipesan.'
                )
                ->modalSubmitActionLabel(
                    'Proses FCFS'
                )
                ->visible(
                    fn (): bool =>
                        auth()
                            ->user()
                            ?->canManageOptimizationData()
                        ?? false
                )
                ->action(
                    function (array $data): void {
                        try {
                            $jadwalId =
                                (int) $data['jadwal_id'];

                            if (
                                $this->jadwalPerbandingan !== null
                                && $this->jadwalPerbandingan !== $jadwalId
                            ) {
                                Notification::make()
                                    ->title(
                                        'Jadwal harus sama'
                                    )
                                    ->body(
                                        'Gunakan kapal dan jadwal yang sama dengan proses sebelumnya.'
                                    )
                                    ->warning()
                                    ->send();

                                return;
                            }

                            $hasil = app(
                                TiketAllocationService::class
                            )->process(
                                $jadwalId,
                                'fcfs'
                            );

                            $this->jadwalPerbandingan =
                                $jadwalId;

                            $this->jadwalAktif =
                                $jadwalId;

                            $this->metodeAktif =
                                'fcfs';

                            $this->resetTable();

                            Notification::make()
                                ->title(
                                    'Proses FCFS berhasil'
                                )
                                ->body(
                                    'Load Factor FCFS = '
                                    . number_format(
                                        (float) $hasil->load_factor,
                                        2,
                                        ',',
                                        '.'
                                    )
                                    . '%'
                                )
                                ->success()
                                ->send();
                        } catch (Throwable $e) {
                            report($e);

                            Notification::make()
                                ->title(
                                    'Proses FCFS gagal'
                                )
                                ->body(
                                    $e->getMessage()
                                )
                                ->danger()
                                ->send();
                        }
                    }
                ),

            Action::make('ganti_jadwal')
                ->label('Ganti Jadwal')
                ->color('warning')
                ->visible(
                    fn (): bool =>
                        $this->jadwalPerbandingan !== null
                )
                ->requiresConfirmation()
                ->action(
                    function (): void {
                        $this->metodeAktif = null;
                        $this->jadwalAktif = null;
                        $this->jadwalPerbandingan = null;

                        $this->resetTable();

                        Notification::make()
                            ->title(
                                'Jadwal berhasil direset'
                            )
                            ->success()
                            ->send();
                    }
                ),
        ];
    }

    private function getJadwalOptions(): array
    {
        $query =
            JadwalKeberangkatan::query()
                ->with([
                    'kapal',
                    'rute',
                ])
                ->withCount(
                    'pemesananTikets'
                )
                ->withSum(
                    'pemesananTikets as total_tiket_diminta_opsi',
                    'jumlah_tiket'
                )
                ->whereHas(
                    'pemesananTikets'
                );

        if (
            $this->jadwalPerbandingan !== null
        ) {
            $query->where(
                'id',
                $this->jadwalPerbandingan
            );
        }

        return $query
            ->orderByDesc(
                'tanggal_berangkat'
            )
            ->orderByDesc(
                'jam_berangkat'
            )
            ->get()
            ->mapWithKeys(
                function (
                    JadwalKeberangkatan $jadwal
                ): array {
                    $tanggal = optional(
                        $jadwal->tanggal_berangkat
                    )->format('d-m-Y') ?? '-';

                    $jam = $jadwal->jam_berangkat
                        ? substr(
                            (string) $jadwal->jam_berangkat,
                            0,
                            8
                        )
                        : '-';

                    $kapal =
                        $jadwal->kapal?->nama_kapal
                        ?? '-';

                    $asal =
                        $jadwal->rute?->pelabuhan_asal
                        ?? '-';

                    $tujuan =
                        $jadwal->rute?->pelabuhan_tujuan
                        ?? '-';

                    $jumlahPemesanan =
                        (int) (
                            $jadwal
                                ->pemesanan_tikets_count
                            ?? 0
                        );

                    $tiketDiminta =
                        (int) (
                            $jadwal
                                ->total_tiket_diminta_opsi
                            ?? 0
                        );

                    $kapasitas =
                        (int) (
                            $jadwal
                                ->kapasitas_total
                            ?? 0
                        );

                    return [
                        $jadwal->id =>
                            "{$kapal} | "
                            . "{$tanggal} | "
                            . "{$jam} | "
                            . "{$asal} → {$tujuan} | "
                            . "{$jumlahPemesanan} pemesanan | "
                            . "{$tiketDiminta} tiket | "
                            . "kapasitas {$kapasitas}",
                    ];
                }
            )
            ->toArray();
    }
}