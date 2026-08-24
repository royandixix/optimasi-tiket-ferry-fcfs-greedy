@php
    $jadwalModal = $pemesanan->jadwal;
    $kapalModal = $jadwalModal?->kapal;

    $gambarKapalModal = collect(
        $kapalModal?->gambar_kapal ?? []
    )
        ->filter()
        ->values();

    $gambarUrlsModal = $gambarKapalModal
        ->map(function ($gambar) {
            $path = ltrim(
                preg_replace(
                    '#^(public/|storage/)#',
                    '',
                    $gambar
                ),
                '/'
            );

            return asset('storage/' . $path);
        })
        ->filter()
        ->values();

    $modalId = 'galeri-kapal-' . $pemesanan->id;
    $carouselId = 'carousel-kapal-' . $pemesanan->id;
@endphp

@if ($gambarUrlsModal->count() > 0)
    <div
        class="modal fade"
        id="{{ $modalId }}"
        tabindex="-1"
        aria-labelledby="{{ $modalId }}-label"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <div>
                        <h5
                            class="modal-title fw-bold"
                            id="{{ $modalId }}-label"
                        >
                            {{ $kapalModal?->nama_kapal ?? 'Galeri Kapal' }}
                        </h5>

                        <small class="text-muted">
                            Geser atau gunakan tombol panah untuk melihat gambar lainnya.
                        </small>
                    </div>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Tutup"
                    ></button>
                </div>

                <div class="modal-body p-0">
                    <div
                        id="{{ $carouselId }}"
                        class="carousel slide"
                        data-bs-touch="true"
                        data-bs-interval="false"
                    >
                        @if ($gambarUrlsModal->count() > 1)
                            <div class="carousel-indicators">
                                @foreach ($gambarUrlsModal as $gambarUrlModal)
                                    <button
                                        type="button"
                                        data-bs-target="#{{ $carouselId }}"
                                        data-bs-slide-to="{{ $loop->index }}"
                                        class="{{ $loop->first ? 'active' : '' }}"
                                        aria-current="{{ $loop->first ? 'true' : 'false' }}"
                                        aria-label="Gambar {{ $loop->iteration }}"
                                    ></button>
                                @endforeach
                            </div>
                        @endif

                        <div class="carousel-inner bg-dark">
                            @foreach ($gambarUrlsModal as $gambarUrlModal)
                                <div
                                    class="carousel-item {{ $loop->first ? 'active' : '' }}"
                                >
                                    <div class="ratio ratio-16x9">
                                        <img
                                            src="{{ $gambarUrlModal }}"
                                            class="d-block w-100 h-100 object-fit-contain"
                                            alt="{{ $kapalModal?->nama_kapal ?? 'Kapal' }} gambar {{ $loop->iteration }}"
                                        >
                                    </div>

                                    <div class="carousel-caption">
                                        <span class="badge text-bg-dark">
                                            Gambar {{ $loop->iteration }}
                                            dari {{ $gambarUrlsModal->count() }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if ($gambarUrlsModal->count() > 1)
                            <button
                                class="carousel-control-prev"
                                type="button"
                                data-bs-target="#{{ $carouselId }}"
                                data-bs-slide="prev"
                            >
                                <span
                                    class="carousel-control-prev-icon"
                                    aria-hidden="true"
                                ></span>

                                <span class="visually-hidden">
                                    Gambar sebelumnya
                                </span>
                            </button>

                            <button
                                class="carousel-control-next"
                                type="button"
                                data-bs-target="#{{ $carouselId }}"
                                data-bs-slide="next"
                            >
                                <span
                                    class="carousel-control-next-icon"
                                    aria-hidden="true"
                                ></span>

                                <span class="visually-hidden">
                                    Gambar berikutnya
                                </span>
                            </button>
                        @endif
                    </div>
                </div>

                <div class="modal-footer">
                    <small class="text-muted me-auto">
                        <i class="bi bi-images me-1"></i>
                        {{ $gambarUrlsModal->count() }} gambar kapal
                    </small>

                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal"
                    >
                        Tutup
                    </button>
                </div>

            </div>
        </div>
    </div>
@endif
