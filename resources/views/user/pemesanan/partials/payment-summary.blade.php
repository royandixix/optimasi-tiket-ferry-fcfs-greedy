@php
    $pembayaran = $pemesanan->pembayaran;

    $statusClass = match ($pembayaran?->status_pembayaran) {
        'menunggu_pembayaran' => 'warning',
        'menunggu_verifikasi' => 'info',
        'dibayar' => 'success',
        'ditolak' => 'danger',
        default => 'secondary',
    };

    $statusLabel = match ($pembayaran?->status_pembayaran) {
        'menunggu_pembayaran' => 'Menunggu Pembayaran',
        'menunggu_verifikasi' => 'Menunggu Verifikasi',
        'dibayar' => 'Dibayar',
        'ditolak' => 'Ditolak',
        default => '-',
    };
@endphp

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <h5 class="mb-1">Informasi Pembayaran</h5>
                <div class="text-muted small">Detail metode dan status pembayaran tiket.</div>
            </div>

            @if ($pembayaran)
                <span class="badge text-bg-{{ $statusClass }}">{{ $statusLabel }}</span>
            @endif
        </div>

        @if ($pembayaran)
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="border rounded-3 p-3 h-100">
                        <div class="small text-muted">Kode Pembayaran</div>
                        <div class="fw-bold fs-5">{{ $pembayaran->kode_pembayaran }}</div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="border rounded-3 p-3 h-100">
                        <div class="small text-muted">Metode Pembayaran</div>
                        <div class="fw-semibold">
                            {{ $pembayaran->metode_pembayaran === 'cash' ? 'Cash di Tempat' : 'Transfer Bank' }}
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="border rounded-3 p-3 h-100">
                        <div class="small text-muted">Total Bayar</div>
                        <div class="fw-bold text-success fs-5">
                            Rp{{ number_format((float) $pembayaran->total_bayar, 0, ',', '.') }}
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="border rounded-3 p-3 h-100">
                        <div class="small text-muted">Status</div>
                        <span class="badge text-bg-{{ $statusClass }} mt-1">{{ $statusLabel }}</span>
                    </div>
                </div>
            </div>

            @if ($pembayaran->metode_pembayaran === 'cash')
                <div class="alert alert-warning rounded-4 mt-4 mb-0">
                    Tunjukkan kode pembayaran <strong>{{ $pembayaran->kode_pembayaran }}</strong> saat melakukan pembayaran di tempat.
                </div>
            @endif

            @if ($pembayaran->metode_pembayaran === 'transfer_bank')
                <div class="alert alert-info rounded-4 mt-4">
                    <div class="fw-semibold mb-2">Transfer Bank</div>
                    <div>Bank: {{ config('ferry_payment.bank_name') }}</div>
                    <div>No. Rekening: {{ config('ferry_payment.account_number') }}</div>
                    <div>Atas Nama: {{ config('ferry_payment.account_name') }}</div>
                </div>

                @if ($pembayaran->bukti_transfer)
                    <div>
                        <div class="fw-semibold mb-2">Bukti Transfer</div>
                        <a href="{{ asset('storage/' . $pembayaran->bukti_transfer) }}" target="_blank" rel="noopener">
                            <img
                                src="{{ asset('storage/' . $pembayaran->bukti_transfer) }}"
                                alt="Bukti Transfer"
                                class="img-fluid rounded-4 border"
                                style="max-height: 320px;"
                            >
                        </a>
                    </div>
                @endif
            @endif

            @if ($pembayaran->catatan_admin)
                <div class="alert alert-light border rounded-4 mt-4 mb-0">
                    <div class="fw-semibold">Catatan Admin</div>
                    <div>{{ $pembayaran->catatan_admin }}</div>
                </div>
            @endif
        @else
            <div class="alert alert-secondary mb-0">Data pembayaran belum tersedia untuk pemesanan ini.</div>
        @endif
    </div>
</div>
