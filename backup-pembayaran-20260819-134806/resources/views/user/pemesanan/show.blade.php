@extends('user.layouts.app')

@section('title', 'Detail Pemesanan')
@section('page-title', 'Detail Pemesanan Tiket')
@section(
    'page-description',
    'Lihat informasi lengkap pemesanan tiket, status alokasi, kode booking, dan QR Code tiket Anda.'
)

@section('content')

<div class="row justify-content-center">
    <div class="col-lg-9">

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">

                <div class="d-flex justify-content-between align-items-start mb-4">
                    <div>
                        <h4 class="mb-1">
                            Kode:
                            {{ $pemesanan->kode_pemesanan }}
                        </h4>

                        <p class="text-muted mb-0">
                            Waktu pemesanan:

                            {{ optional($pemesanan->waktu_pemesanan)->format('d/m/Y H:i') ?? '-' }}
                        </p>
                    </div>

                    <div>
                        @if ($pemesanan->status_pemesanan === 'pending')
                            <span class="badge text-bg-warning">
                                Menunggu Konfirmasi
                            </span>
                        @elseif ($pemesanan->status_pemesanan === 'diterima')
                            <span class="badge text-bg-success">
                                Diterima
                            </span>
                        @elseif ($pemesanan->status_pemesanan === 'ditolak')
                            <span class="badge text-bg-danger">
                                Ditolak
                            </span>
                        @else
                            <span class="badge text-bg-secondary">
                                {{ ucfirst($pemesanan->status_pemesanan) }}
                            </span>
                        @endif
                    </div>
                </div>

                <div class="row gy-4">
                    <div class="col-md-6">
                        <h5>Informasi Jadwal</h5>

                        <p class="mb-2">
                            <strong>
                                Tanggal Berangkat:
                            </strong>
                            <br>

                            {{ optional(optional($pemesanan->jadwal)->tanggal_berangkat)->format('d M Y') ?? '-' }}
                        </p>

                        <p class="mb-2">
                            <strong>Jam Berangkat:</strong>
                            <br>

                            {{ optional($pemesanan->jadwal)->jam_berangkat
                                ? substr(
                                    (string) $pemesanan->jadwal->jam_berangkat,
                                    0,
                                    5
                                )
                                : '-' }}
                        </p>

                        <p class="mb-2">
                            <strong>Rute:</strong>
                            <br>

                            {{ optional(optional($pemesanan->jadwal)->rute)->pelabuhan_asal ?? '-' }}

                            →

                            {{ optional(optional($pemesanan->jadwal)->rute)->pelabuhan_tujuan ?? '-' }}
                        </p>

                        <p class="mb-0">
                            <strong>Kapal:</strong>
                            <br>

                            {{ optional(optional($pemesanan->jadwal)->kapal)->nama_kapal ?? '-' }}
                        </p>
                    </div>

                    <div class="col-md-6">
                        <h5>Informasi Pemesanan</h5>

                        <p class="mb-2">
                            <strong>Jumlah Tiket:</strong>
                            <br>

                            {{ $pemesanan->jumlah_tiket }}
                            tiket
                        </p>

                        {{-- TARIF_DROPDOWN_SHOW_START --}}
                        <p class="mb-2">
                            <strong>Jenis Tarif:</strong>
                            <br>
                            {{ $pemesanan->tarif_label ?? 'Penumpang - Dewasa' }}
                        </p>

                        <p class="mb-2">
                            <strong>Harga Satuan:</strong>
                            <br>
                            Rp{{ number_format((float) $pemesanan->harga_satuan, 0, ',', '.') }}
                        </p>

                        <p class="mb-2">
                            <strong>Total Harga:</strong>
                            <br>
                            <span class="text-success fw-bold fs-5">
                                Rp{{ number_format((float) $pemesanan->total_harga, 0, ',', '.') }}
                            </span>
                        </p>
                        {{-- TARIF_DROPDOWN_SHOW_END --}}


                        <p class="mb-2">
                            <strong>Status Penggunaan:</strong>
                            <br>

                            @if ($pemesanan->digunakan_pada)
                                <span class="badge text-bg-danger">
                                    Sudah Digunakan
                                </span>
                            @else
                                <span class="badge text-bg-success">
                                    Belum Digunakan
                                </span>
                            @endif
                        </p>

                        @if ($pemesanan->digunakan_pada)
                            <p class="mb-2">
                                <strong>Waktu Validasi:</strong>
                                <br>

                                {{ optional($pemesanan->digunakan_pada)->format('d M Y H:i') ?? '-' }}
                            </p>
                        @endif

                        <p class="mb-0">
                            <strong>Catatan:</strong>
                            <br>

                            {{ $pemesanan->catatan ?? '-' }}
                        </p>
                    </div>
                </div>

            </div>
        </div>

        @if ($pemesanan->status_pemesanan === 'diterima')
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4 text-center">
                    <h5 class="mb-2">
                        QR Code Tiket
                    </h5>

                    <p class="text-muted mb-4">
                        Tunjukkan QR Code atau kode booking ini kepada petugas.
                    </p>

                    @if ($pemesanan->digunakan_pada)
                        <div class="alert alert-danger mb-4">
                            Tiket ini sudah digunakan dan tidak dapat divalidasi kembali.
                        </div>
                    @endif

                    <div
                        id="ticket-qrcode"
                        class="d-inline-flex justify-content-center rounded border bg-white p-3 mb-3"
                    ></div>

                    <div>
                        <div class="small text-muted">
                            Kode Booking
                        </div>

                        <div class="fs-5 fw-bold">
                            {{ $pemesanan->kode_pemesanan }}
                        </div>
                    </div>

                    <p class="small text-muted mt-3 mb-0">
                        QR Code hanya dapat digunakan satu kali.
                    </p>
                </div>
            </div>

            <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

            <script>
                document.addEventListener(
                    'DOMContentLoaded',
                    function () {
                        const target =
                            document.getElementById(
                                'ticket-qrcode'
                            );

                        if (
                            !target ||
                            typeof QRCode === 'undefined'
                        ) {
                            return;
                        }

                        new QRCode(target, {
                            text: @json($pemesanan->kode_pemesanan),
                            width: 320,
                            height: 320,
                            correctLevel:
                                QRCode.CorrectLevel.M,
                        });
                    }
                );
            </script>
        @else
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h5 class="mb-2">
                        QR Code Tiket
                    </h5>

                    <p class="text-muted mb-0">
                        QR Code akan tampil setelah pemesanan diterima oleh admin.
                    </p>
                </div>
            </div>
        @endif

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <h5 class="mb-3">
                    Status Alokasi Tiket
                </h5>

                @if (
                    $pemesanan->alokasiTikets
                    && $pemesanan->alokasiTikets->count() > 0
                )
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Metode</th>
                                    <th>Jumlah Dialokasikan</th>
                                    <th>Status</th>
                                    <th>Sisa Kapasitas</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($pemesanan->alokasiTikets as $alokasi)
                                    <tr>
                                        <td>
                                            {{ strtoupper($alokasi->metode) }}
                                        </td>

                                        <td>
                                            {{ $alokasi->jumlah_dialokasikan }}
                                        </td>

                                        <td>
                                            <span
                                                class="badge {{ $alokasi->status_alokasi === 'diterima'
                                                    ? 'text-bg-success'
                                                    : 'text-bg-danger' }}"
                                            >
                                                {{ ucfirst($alokasi->status_alokasi) }}
                                            </span>
                                        </td>

                                        <td>
                                            {{ $alokasi->sisa_kapasitas_sebelum }}

                                            ke

                                            {{ $alokasi->sisa_kapasitas_sesudah }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted mb-0">
                        Pemesanan ini belum memiliki data alokasi tiket.
                    </p>
                @endif
            </div>
        </div>

        <div class="d-flex justify-content-between">
            <a
                href="{{ route('user.pemesanan.index') }}"
                class="btn btn-outline-secondary rounded-pill"
            >
                Kembali
            </a>

            @if ($pemesanan->status_pemesanan === 'pending')
                <a
                    href="{{ route('user.pemesanan.edit', $pemesanan) }}"
                    class="btn btn-success rounded-pill"
                >
                    Edit Pemesanan
                </a>
            @endif
        </div>

    </div>
</div>

@endsection