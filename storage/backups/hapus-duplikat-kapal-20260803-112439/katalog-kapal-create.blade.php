<section class="container py-5">
    <div class="mb-4">
        <span class="badge bg-success mb-2">
            Data Kapal Admin
        </span>

        <h2 class="fw-bold mb-1">
            Kapal Ferry Tersedia
        </h2>

        <p class="text-muted mb-0">
            Semua kapal yang tersimpan di admin tetap ditampilkan
            untuk akun baru maupun akun lama.
        </p>
    </div>

    @if (($kapals ?? collect())->isNotEmpty())
        <div class="row g-4">
            @foreach ($kapals as $kapal)
                @php
                    /*
                     * gambar_kapal dapat berupa array, JSON, atau string.
                     */
                    $rawGambar = $kapal->gambar_kapal ?? [];

                    if (is_string($rawGambar)) {
                        $decoded = json_decode(
                            $rawGambar,
                            true
                        );

                        $daftarGambar = is_array($decoded)
                            ? $decoded
                            : [$rawGambar];
                    } elseif (is_array($rawGambar)) {
                        $daftarGambar = $rawGambar;
                    } else {
                        $daftarGambar = [];
                    }

                    $gambarPertama = collect($daftarGambar)
                        ->filter()
                        ->first();

                    $gambarPath = $gambarPertama
                        ? ltrim(
                            preg_replace(
                                '#^(public/|storage/)#',
                                '',
                                (string) $gambarPertama
                            ),
                            '/'
                        )
                        : null;

                    /*
                     * URL relatif agar gambar tetap tampil melalui
                     * localhost maupun Cloudflare.
                     */
                    $gambarUrl = $gambarPath
                        ? '/storage/' . $gambarPath
                        : null;

                    $jadwalAktif = $jadwalsByKapal
                        ->get(
                            $kapal->id,
                            collect()
                        );

                    $kapasitas =
                        $kapal->kapasitas_penumpang
                        ?? $kapal->kapasitas
                        ?? 0;
                @endphp

                <div class="col-12 col-md-6">
                    <div
                        class="card h-100 border-0 shadow-sm
                               rounded-4 overflow-hidden"
                    >
                        <div
                            class="ratio ratio-16x9
                                   bg-light position-relative"
                        >
                            @if ($gambarUrl)
                                <img
                                    src="{{ $gambarUrl }}"
                                    alt="{{ $kapal->nama_kapal }}"
                                    class="w-100 h-100 object-fit-cover"
                                    loading="lazy"
                                    onerror="
                                        this.style.display='none';
                                        this.nextElementSibling
                                            .classList.remove('d-none');
                                        this.nextElementSibling
                                            .classList.add('d-flex');
                                    "
                                >

                                <div
                                    class="d-none w-100 h-100
                                           align-items-center
                                           justify-content-center
                                           text-secondary"
                                >
                                    <div class="text-center">
                                        <i
                                            class="bi bi-image fs-1"
                                        ></i>

                                        <div>
                                            File gambar tidak ditemukan
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div
                                    class="d-flex w-100 h-100
                                           align-items-center
                                           justify-content-center
                                           text-secondary"
                                >
                                    <div class="text-center">
                                        <i
                                            class="bi bi-image fs-1"
                                        ></i>

                                        <div>
                                            Gambar belum tersedia
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div
                                class="position-absolute
                                       top-0 start-0 p-3"
                            >
                                <span class="badge bg-success">
                                    {{ ucfirst(
                                        (string)
                                        ($kapal->status ?? 'aktif')
                                    ) }}
                                </span>
                            </div>
                        </div>

                        <div class="card-body p-4">
                            <small
                                class="text-success fw-semibold"
                            >
                                {{ $kapal->kode_kapal
                                    ?? 'Kapal Ferry' }}
                            </small>

                            <h4 class="fw-bold mt-1 mb-3">
                                {{ $kapal->nama_kapal }}
                            </h4>

                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <div
                                        class="border rounded-3 p-3"
                                    >
                                        <small
                                            class="text-muted d-block"
                                        >
                                            Kapasitas
                                        </small>

                                        <strong>
                                            {{ number_format(
                                                (int) $kapasitas,
                                                0,
                                                ',',
                                                '.'
                                            ) }}
                                        </strong>
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div
                                        class="border rounded-3 p-3"
                                    >
                                        <small
                                            class="text-muted d-block"
                                        >
                                            Jadwal Dapat Dipesan
                                        </small>

                                        <strong>
                                            {{ $jadwalAktif->count() }}
                                        </strong>
                                    </div>
                                </div>
                            </div>

                            @if ($jadwalAktif->isNotEmpty())
                                <div class="d-grid gap-2">
                                    @foreach ($jadwalAktif as $jadwal)
                                        @php
                                            $tanggal = $jadwal
                                                ->tanggal_berangkat;

                                            try {
                                                $tanggalTampil =
                                                    \Illuminate\Support\Carbon::parse(
                                                        $tanggal
                                                    )->format(
                                                        'd-m-Y'
                                                    );
                                            } catch (
                                                \Throwable $exception
                                            ) {
                                                $tanggalTampil =
                                                    (string) $tanggal;
                                            }

                                            $jam = substr(
                                                (string)
                                                $jadwal
                                                    ->jam_berangkat,
                                                0,
                                                5
                                            );

                                            $rute = $jadwal->rute;
                                        @endphp

                                        <a
                                            href="{{ route(
                                                'user.pemesanan.create',
                                                [
                                                    'jadwal_id' =>
                                                        $jadwal->id,
                                                ]
                                            ) }}"
                                            class="btn btn-success
                                                   text-start"
                                        >
                                            <div class="fw-semibold">
                                                {{ $tanggalTampil }}
                                                ·
                                                {{ $jam }}
                                            </div>

                                            <small>
                                                {{ $rute
                                                    ?->pelabuhan_asal
                                                    ?? '-' }}

                                                →

                                                {{ $rute
                                                    ?->pelabuhan_tujuan
                                                    ?? '-' }}

                                                · Sisa
                                                {{ (int)
                                                    $jadwal
                                                        ->sisa_kapasitas }}
                                            </small>
                                        </a>
                                    @endforeach
                                </div>
                            @else
                                <div
                                    class="alert alert-warning mb-0"
                                >
                                    <i
                                        class="bi bi-calendar-x me-1"
                                    ></i>

                                    Kapal tetap ditampilkan, tetapi
                                    belum mempunyai jadwal yang dapat
                                    dipesan.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="alert alert-warning">
            Data kapal belum ditemukan di database.
        </div>
    @endif
</section>
