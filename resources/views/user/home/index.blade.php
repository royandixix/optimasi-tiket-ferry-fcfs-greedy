<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Home - Sistem Optimasi Tiket Ferry</title>
    <meta name="description" content="Sistem informasi optimasi alokasi tiket dan kapasitas kapal ferry.">
    <meta name="keywords" content="tiket ferry, kapal ferry, sistem informasi, optimasi tiket, kapasitas kapal">
    <link href="{{ asset('Mentor-1.0.0/assets/img/favicon.png') }}" rel="icon">
    <link href="{{ asset('Mentor-1.0.0/assets/img/apple-touch-icon.png') }}" rel="apple-touch-icon">
    <link href="https://fonts.googleapis.c  om" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;500;600;700;800&family=Poppins:wght@300;400;500;600;700;800;900&family=Raleway:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <link href="{{ asset('Mentor-1.0.0/assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('Mentor-1.0.0/assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('Mentor-1.0.0/assets/vendor/aos/aos.css') }}" rel="stylesheet">
    <link href="{{ asset('Mentor-1.0.0/assets/vendor/glightbox/css/glightbox.min.css') }}" rel="stylesheet">
    <link href="{{ asset('Mentor-1.0.0/assets/vendor/swiper/swiper-bundle.min.css') }}" rel="stylesheet">
    <link href="{{ asset('Mentor-1.0.0/assets/css/main.css') }}" rel="stylesheet">
</head>

<body class="index-page">
    @include('user.partials.navbar')
    <main class="main">
        <section id="hero" class="hero section dark-background">
            <img src="https://loremflickr.com/1600/900/ferry,ship,sea?lock=12" alt="Kapal Ferry di Laut"
                data-aos="fade-in">
            <div class="container">
                <h2 data-aos="fade-up" data-aos-delay="100">
                    Sistem Informasi Optimasi<br>Alokasi Tiket Ferry
                </h2>
                <p data-aos="fade-up" data-aos-delay="200">
                    Platform digital untuk membantu proses pemesanan tiket, pengelolaan jadwal kapal,
                    dan optimasi kapasitas penumpang pada layanan ferry agar lebih tertata, cepat, dan efisien.
                </p>
                <div class="d-flex mt-4 gap-2" data-aos="fade-up" data-aos-delay="300">
                    @auth
                        <a href="{{ route('user.dashboard') }}" class="btn-get-started">
                            Masuk Dashboard
                        </a>
                    @else
                        <a href="{{ route('user.login') }}" class="btn-get-started">
                            Pesan Tiket
                        </a>
                        <a href="{{ route('user.register') }}" class="btn-get-started">
                            Daftar Akun
                        </a>
                    @endauth
                </div>
            </div>
        </section>
        <section id="tentang" class="about section">
            <div class="container">
                <div class="row gy-4 align-items-center">
                    <div class="col-lg-6 order-1 order-lg-2" data-aos="fade-up" data-aos-delay="100">
                        <img src="https://loremflickr.com/800/600/ferry,harbor,port?lock=7"
                            class="img-fluid rounded-4 shadow-sm" alt="Pelabuhan Ferry">
                    </div>
                    <div class="col-lg-6 order-2 order-lg-1 content" data-aos="fade-up" data-aos-delay="200">
                        <h3>Tentang Sistem</h3>
                        <p class="fst-italic">
                            Sistem ini dirancang untuk membantu pengelolaan pemesanan tiket kapal ferry agar lebih
                            tertata,
                            cepat, dan sesuai dengan kapasitas kapal yang tersedia. Seluruh proses dilakukan secara
                            digital
                            sehingga meminimalkan pencatatan manual dan mempercepat pelayanan penumpang.
                        </p>
                        <ul>
                            <li>
                                <i class="bi bi-check-circle"></i>
                                <span>Penumpang dapat melakukan registrasi dan pemesanan tiket secara online kapan
                                    saja.</span>
                            </li>
                            <li>
                                <i class="bi bi-check-circle"></i>
                                <span>Admin dapat mengelola data kapal, rute, jadwal, dan pemesanan tiket dalam satu
                                    sistem.</span>
                            </li>
                            <li>
                                <i class="bi bi-check-circle"></i>
                                <span>Pimpinan dapat memantau hasil optimasi dan laporan pemanfaatan kapasitas
                                    kapal.</span>
                            </li>
                            <li>
                                <i class="bi bi-check-circle"></i>
                                <span>Proses alokasi tiket dibantu perhitungan sistem agar kapasitas kapal termanfaatkan
                                    maksimal.</span>
                            </li>
                        </ul>
                        @auth
                            <a href="{{ route('user.dashboard') }}" class="read-more">
                                <span>Buka Dashboard</span>
                                <i class="bi bi-arrow-right"></i>
                            </a>
                        @else
                            <a href="{{ route('user.register') }}" class="read-more">
                                <span>Mulai Registrasi</span>
                                <i class="bi bi-arrow-right"></i>
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </section>
        <section id="counts" class="section counts light-background">
            <div class="container" data-aos="fade-up" data-aos-delay="100">
                <div class="row gy-4">
                    <div class="col-lg-3 col-md-6">
                        <div class="stats-item text-center w-100 h-100">
                            <span>4</span>
                            <p>Role Pengguna</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="stats-item text-center w-100 h-100">
                            <span>5</span>
                            <p>Modul Utama</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="stats-item text-center w-100 h-100">
                            <span>2</span>
                            <p>Metode Optimasi</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="stats-item text-center w-100 h-100">
                            <span>24</span>
                            <p>Akses Online</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section id="fitur" class="section why-us">
            <div class="container">
                <div class="row gy-4">
                    <div class="col-lg-4" data-aos="fade-up" data-aos-delay="100">
                        <div class="why-box">
                            <h3>Mengapa Sistem Ini Dibutuhkan?</h3>
                            <p>
                                Sistem ini membantu proses pelayanan tiket ferry agar lebih efisien,
                                mengurangi pencatatan manual, memudahkan pemantauan kapasitas kapal,
                                dan mendukung proses pengambilan keputusan berdasarkan data.
                            </p>
                            <div class="text-center">
                                @auth
                                    <a href="{{ route('user.dashboard') }}" class="more-btn">
                                        <span>Dashboard</span>
                                        <i class="bi bi-chevron-right"></i>
                                    </a>
                                @else
                                    <a href="{{ route('user.login') }}" class="more-btn">
                                        <span>Mulai Gunakan</span>
                                        <i class="bi bi-chevron-right"></i>
                                    </a>
                                @endauth
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-8 d-flex align-items-stretch">
                        <div class="row gy-4" data-aos="fade-up" data-aos-delay="200">
                            <div class="col-xl-4">
                                <div class="icon-box d-flex flex-column justify-content-center align-items-center">
                                    <i class="bi bi-calendar-check"></i>
                                    <h4>Jadwal Kapal</h4>
                                    <p>Informasi jadwal keberangkatan kapal dapat dikelola dan dipantau secara
                                        terstruktur.</p>
                                </div>
                            </div>
                            <div class="col-xl-4" data-aos="fade-up" data-aos-delay="300">
                                <div class="icon-box d-flex flex-column justify-content-center align-items-center">
                                    <i class="bi bi-ticket-perforated"></i>
                                    <h4>Pemesanan Tiket</h4>
                                    <p>Penumpang dapat melakukan pemesanan tiket sesuai jadwal yang tersedia.</p>
                                </div>
                            </div>
                            <div class="col-xl-4" data-aos="fade-up" data-aos-delay="400">
                                <div class="icon-box d-flex flex-column justify-content-center align-items-center">
                                    <i class="bi bi-bar-chart-line"></i>
                                    <h4>Optimasi Kapasitas</h4>
                                    <p>Sistem membantu proses alokasi tiket berdasarkan kapasitas kapal dan data
                                        pemesanan.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section id="metode" class="section why-us light-background">
            <div class="container">
                <div class="section-title" data-aos="fade-up">
                    <h2>Metode Optimasi</h2>
                    <p>Bagaimana Sistem Mengalokasikan Tiket</p>
                </div>
                <div class="row gy-4">
                    <div class="col-lg-6" data-aos="fade-up" data-aos-delay="100">
                        <div class="icon-box d-flex flex-column justify-content-center align-items-center h-100">
                            <i class="bi bi-diagram-3"></i>
                            <h4>Optimasi Kapasitas Kapal</h4>
                            <p>
                                Sistem menghitung jumlah tiket yang dapat dialokasikan pada setiap jadwal
                                keberangkatan berdasarkan kapasitas maksimal kapal. Tujuannya agar penggunaan
                                ruang kapal maksimal tanpa melebihi batas daya angkut yang aman.
                            </p>
                        </div>
                    </div>
                    <div class="col-lg-6" data-aos="fade-up" data-aos-delay="200">
                        <div class="icon-box d-flex flex-column justify-content-center align-items-center h-100">
                            <i class="bi bi-sliders"></i>
                            <h4>Optimasi Alokasi Pemesanan</h4>
                            <p>
                                Sistem mengatur distribusi tiket sesuai permintaan pemesanan dan ketersediaan kursi
                                pada tiap rute. Dengan begitu, sisa kapasitas dapat diminimalkan dan pemesanan
                                terdistribusi lebih merata pada jadwal yang tersedia.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-center mt-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="col-lg-9 text-center">
                        <p class="fst-italic">
                            Kedua metode ini bekerja bersama untuk memastikan setiap keberangkatan berjalan efisien:
                            kapasitas kapal terpakai optimal, dan penumpang mendapatkan kepastian ketersediaan tiket.
                        </p>
                    </div>
                </div>
            </div>
        </section>
        <section id="alur" class="features section">
            <div class="container">
                <div class="section-title" data-aos="fade-up">
                    <h2>Alur Sistem</h2>
                    <p>Cara Kerja Sistem</p>
                </div>
                <div class="row gy-4">
                    <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="100">
                        <div class="features-item">
                            <i class="bi bi-person-plus" style="color: #5fcf80;"></i>
                            <h3><a class="stretched-link">Registrasi Penumpang</a></h3>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="200">
                        <div class="features-item">
                            <i class="bi bi-box-arrow-in-right" style="color: #5578ff;"></i>
                            <h3><a class="stretched-link">Login Sistem</a></h3>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="300">
                        <div class="features-item">
                            <i class="bi bi-ticket-detailed" style="color: #e80368;"></i>
                            <h3><a class="stretched-link">Pemesanan Tiket</a></h3>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="400">
                        <div class="features-item">
                            <i class="bi bi-check-circle" style="color: #29cc61;"></i>
                            <h3><a class="stretched-link">Status Pemesanan</a></h3>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section id="cara-pesan" class="section why-us light-background">
            <div class="container">
                <div class="section-title" data-aos="fade-up">
                    <h2>Cara Pemesanan Tiket</h2>
                    <p>Langkah Mudah Memesan Tiket Ferry</p>
                </div>
                <div class="row gy-4">
                    <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="100">
                        <div class="icon-box d-flex flex-column justify-content-center align-items-center h-100">
                            <i class="bi bi-1-circle"></i>
                            <h4>Buat Akun</h4>
                            <p>Lakukan registrasi sebagai penumpang dengan mengisi data diri yang diperlukan.</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="200">
                        <div class="icon-box d-flex flex-column justify-content-center align-items-center h-100">
                            <i class="bi bi-2-circle"></i>
                            <h4>Pilih Jadwal</h4>
                            <p>Pilih rute dan jadwal keberangkatan kapal sesuai tanggal yang diinginkan.</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="300">
                        <div class="icon-box d-flex flex-column justify-content-center align-items-center h-100">
                            <i class="bi bi-3-circle"></i>
                            <h4>Pesan Tiket</h4>
                            <p>Isi jumlah tiket dan data penumpang, lalu ajukan pemesanan pada sistem.</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="400">
                        <div class="icon-box d-flex flex-column justify-content-center align-items-center h-100">
                            <i class="bi bi-4-circle"></i>
                            <h4>Cek Status</h4>
                            <p>Pantau status pemesanan dan ketersediaan tiket melalui dashboard penumpang.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section id="keunggulan" class="section why-us">
            <div class="container">
                <div class="section-title" data-aos="fade-up">
                    <h2>Keunggulan</h2>
                    <p>Kelebihan Menggunakan Sistem Ini</p>
                </div>
                <div class="row gy-4">
                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                        <div class="icon-box d-flex flex-column justify-content-center align-items-center h-100">
                            <i class="bi bi-lightning-charge"></i>
                            <h4>Proses Cepat</h4>
                            <p>Pemesanan tiket dapat dilakukan secara online tanpa harus antre di loket.</p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                        <div class="icon-box d-flex flex-column justify-content-center align-items-center h-100">
                            <i class="bi bi-database-check"></i>
                            <h4>Data Terpusat</h4>
                            <p>Data kapal, rute, jadwal, dan pemesanan tersimpan rapi dalam satu sistem.</p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
                        <div class="icon-box d-flex flex-column justify-content-center align-items-center h-100">
                            <i class="bi bi-graph-up-arrow"></i>
                            <h4>Optimasi Kapasitas</h4>
                            <p>Alokasi tiket dihitung sistem agar kapasitas kapal termanfaatkan maksimal.</p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="400">
                        <div class="icon-box d-flex flex-column justify-content-center align-items-center h-100">
                            <i class="bi bi-shield-check"></i>
                            <h4>Akses Sesuai Role</h4>
                            <p>Setiap pengguna memiliki hak akses sesuai perannya untuk menjaga keamanan data.</p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="500">
                        <div class="icon-box d-flex flex-column justify-content-center align-items-center h-100">
                            <i class="bi bi-file-earmark-bar-graph"></i>
                            <h4>Laporan & Monitoring</h4>
                            <p>Pimpinan dapat memantau laporan dan hasil optimasi pemanfaatan kapal.</p>
                        </div>
                    </div>


                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="600">
                        <div class="icon-box d-flex flex-column justify-content-center align-items-center h-100">
                            <i class="bi bi-clock-history"></i>
                            <h4>Akses 24 Jam</h4>
                            <p>Sistem dapat diakses kapan saja selama terhubung dengan internet.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section id="jelajah" class="section why-us light-background">
            <div class="container">
                <div class="section-title" data-aos="fade-up">
                    <h2>Jelajahi Sistem</h2>
                    <p>Klik Tiap Menu untuk Melihat Detailnya</p>
                </div>
                <div class="row justify-content-center align-items-start gy-4" data-aos="fade-up"
                    data-aos-delay="100">
                    <div class="col-lg-4">
                        <div class="nav flex-column nav-pills gap-2" id="jelajahTab" role="tablist"
                            aria-orientation="vertical">
                            <button class="nav-link active text-start" id="tab-pesan-btn" data-bs-toggle="pill"
                                data-bs-target="#tab-pesan" type="button" role="tab">
                                <i class="bi bi-ticket-perforated me-2"></i> Pemesanan Tiket
                            </button>
                            <button class="nav-link text-start" id="tab-jadwal-btn" data-bs-toggle="pill"
                                data-bs-target="#tab-jadwal" type="button" role="tab">
                                <i class="bi bi-calendar2-week me-2"></i> Jadwal &amp; Rute
                            </button>
                            <button class="nav-link text-start" id="tab-optimasi-btn" data-bs-toggle="pill"
                                data-bs-target="#tab-optimasi" type="button" role="tab">
                                <i class="bi bi-graph-up-arrow me-2"></i> Optimasi Kapasitas
                            </button>
                            <button class="nav-link text-start" id="tab-laporan-btn" data-bs-toggle="pill"
                                data-bs-target="#tab-laporan" type="button" role="tab">
                                <i class="bi bi-file-earmark-bar-graph me-2"></i> Monitoring &amp; Laporan
                            </button>
                        </div>
                    </div>
                    <div class="col-lg-8">
                        <div class="tab-content bg-white rounded-4 shadow-sm p-4">
                            <div class="tab-pane fade show active" id="tab-pesan" role="tabpanel">
                                <h4><i class="bi bi-ticket-perforated me-2"></i>Pemesanan Tiket</h4>
                                <p>Penumpang dapat memesan tiket kapal ferry secara online, memilih jadwal keberangkatan
                                    yang tersedia, lalu memantau status pemesanannya melalui dashboard.</p>
                                <ul class="list-unstyled">
                                    <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Pemesanan
                                        tanpa perlu antre di loket.</li>
                                    <li class="mb-2"><i
                                            class="bi bi-check-circle text-success me-2"></i>Ketersediaan tiket
                                        ditampilkan sesuai kapasitas kapal.</li>
                                    <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Status
                                        pemesanan dapat dipantau kapan saja.</li>
                                </ul>
                            </div>
                            <div class="tab-pane fade" id="tab-jadwal" role="tabpanel">
                                <h4><i class="bi bi-calendar2-week me-2"></i>Jadwal &amp; Rute</h4>
                                <p>Admin mengelola data kapal, rute pelayaran, dan jadwal keberangkatan agar informasi
                                    yang tampil selalu terbaru dan mudah dipahami penumpang.</p>
                                <ul class="list-unstyled">
                                    <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Pengelolaan
                                        data kapal dan rute terpusat.</li>
                                    <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Jadwal
                                        keberangkatan tersusun rapi per rute.</li>
                                    <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Informasi
                                        selalu diperbarui secara real time.</li>
                                </ul>
                            </div>
                            <div class="tab-pane fade" id="tab-optimasi" role="tabpanel">
                                <h4><i class="bi bi-graph-up-arrow me-2"></i>Optimasi Kapasitas</h4>
                                <p>Sistem membantu mengalokasikan tiket sesuai kapasitas kapal agar penggunaan ruang
                                    kapal maksimal tanpa melebihi batas daya angkut yang aman.</p>
                                <ul class="list-unstyled">
                                    <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Alokasi
                                        tiket dihitung berdasarkan kapasitas kapal.</li>
                                    <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Sisa kursi
                                        diminimalkan pada tiap keberangkatan.</li>
                                    <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Pemesanan
                                        terdistribusi lebih merata.</li>
                                </ul>
                            </div>
                            <div class="tab-pane fade" id="tab-laporan" role="tabpanel">
                                <h4><i class="bi bi-file-earmark-bar-graph me-2"></i>Monitoring &amp; Laporan</h4>
                                <p>Pimpinan dapat memantau hasil optimasi dan pemanfaatan kapasitas kapal melalui
                                    laporan dan dashboard monitoring.</p>
                                <ul class="list-unstyled">
                                    <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Laporan
                                        pemesanan dan pemanfaatan kapal.</li>
                                    <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Ringkasan
                                        hasil optimasi yang mudah dibaca.</li>
                                    <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Mendukung
                                        pengambilan keputusan berbasis data.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section id="galeri" class="section why-us">
            <div class="container">
                <div class="section-title" data-aos="fade-up">
                    <h2>Galeri</h2>
                    <p>Suasana Layanan Ferry</p>
                </div>
                <div class="row justify-content-center" data-aos="fade-up" data-aos-delay="100">
                    <div class="col-lg-9">
                        <div id="galeriCarousel"
                            class="carousel slide carousel-fade rounded-4 overflow-hidden shadow-sm"
                            data-bs-ride="carousel">
                            <div class="carousel-indicators">
                                <button type="button" data-bs-target="#galeriCarousel" data-bs-slide-to="0"
                                    class="active" aria-label="Slide 1"></button>
                                <button type="button" data-bs-target="#galeriCarousel" data-bs-slide-to="1"
                                    aria-label="Slide 2"></button>
                                <button type="button" data-bs-target="#galeriCarousel" data-bs-slide-to="2"
                                    aria-label="Slide 3"></button>
                                <button type="button" data-bs-target="#galeriCarousel" data-bs-slide-to="3"
                                    aria-label="Slide 4"></button>
                            </div>
                            <div class="carousel-inner">
                                <div class="carousel-item active">
                                    <img src="https://loremflickr.com/1200/600/ferry,ship?lock=21"
                                        class="d-block w-100" alt="Kapal Ferry">
                                    <div class="carousel-caption d-none d-md-block">
                                        <h5>Kapal Ferry</h5>
                                        <p>Melayani penyeberangan penumpang antar pelabuhan.</p>
                                    </div>
                                </div>
                                <div class="carousel-item">
                                    <img src="https://loremflickr.com/1200/600/harbor,port,sea?lock=22"
                                        class="d-block w-100" alt="Pelabuhan">
                                    <div class="carousel-caption d-none d-md-block">
                                        <h5>Pelabuhan</h5>
                                        <p>Titik keberangkatan dan kedatangan kapal.</p>
                                    </div>
                                </div>
                                <div class="carousel-item">
                                    <img src="https://loremflickr.com/1200/600/boat,ocean?lock=23"
                                        class="d-block w-100" alt="Perjalanan Laut">
                                    <div class="carousel-caption d-none d-md-block">
                                        <h5>Perjalanan Laut</h5>
                                        <p>Penyeberangan yang aman sesuai kapasitas kapal.</p>
                                    </div>
                                </div>
                                <div class="carousel-item">
                                    <img src="https://loremflickr.com/1200/600/dock,ship,sea?lock=24"
                                        class="d-block w-100" alt="Dermaga">
                                    <div class="carousel-caption d-none d-md-block">
                                        <h5>Dermaga</h5>
                                        <p>Proses naik-turun penumpang yang tertata.</p>
                                    </div>
                                </div>
                            </div>
                            <button class="carousel-control-prev" type="button" data-bs-target="#galeriCarousel"
                                data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Sebelumnya</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#galeriCarousel"
                                data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Berikutnya</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section id="faq" class="section faq light-background">
            <div class="container">
                <div class="section-title" data-aos="fade-up">
                    <h2>FAQ</h2>
                    <p>Pertanyaan yang Sering Diajukan</p>
                </div>
                <div class="row justify-content-center" data-aos="fade-up" data-aos-delay="100">
                    <div class="col-lg-9">
                        <div class="accordion" id="faqAccordion">
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#faq1" aria-expanded="true">
                                        Apakah pemesanan tiket bisa dilakukan secara online?
                                    </button>
                                </h2>
                                <div id="faq1" class="accordion-collapse collapse show"
                                    data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        Bisa. Penumpang cukup membuat akun, login, lalu memilih jadwal dan memesan tiket
                                        langsung melalui sistem tanpa perlu datang ke loket.
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#faq2">
                                        Bagaimana sistem menentukan ketersediaan tiket?
                                    </button>
                                </h2>
                                <div id="faq2" class="accordion-collapse collapse"
                                    data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        Ketersediaan tiket dihitung berdasarkan kapasitas maksimal kapal pada setiap
                                        jadwal.
                                        Sistem akan menyesuaikan sisa kursi sesuai jumlah pemesanan yang telah masuk.
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#faq3">
                                        Apa yang dimaksud dengan optimasi kapasitas?
                                    </button>
                                </h2>
                                <div id="faq3" class="accordion-collapse collapse"
                                    data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        Optimasi kapasitas adalah proses pengaturan alokasi tiket agar ruang kapal
                                        terpakai
                                        seefisien mungkin, tanpa melebihi batas daya angkut yang aman.
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#faq4">
                                        Siapa saja yang dapat mengakses sistem ini?
                                    </button>
                                </h2>
                                <div id="faq4" class="accordion-collapse collapse"
                                    data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        Sistem memiliki empat role, yaitu Super Admin, Admin, Pimpinan, dan Penumpang.
                                        Setiap role memiliki hak akses yang berbeda sesuai fungsinya.
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#faq5">
                                        Bagaimana cara memantau status pemesanan?
                                    </button>
                                </h2>
                                <div id="faq5" class="accordion-collapse collapse"
                                    data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        Setelah melakukan pemesanan, penumpang dapat melihat status pemesanan secara
                                        langsung
                                        melalui dashboard pada akun masing-masing.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="section">
            <div class="container" data-aos="fade-up">
                <div class="row justify-content-center text-center">
                    <div class="col-lg-8">
                        <h3>Mulai Gunakan Sistem Sekarang</h3>
                        <p>
                            Buat akun penumpang untuk melakukan pemesanan tiket kapal ferry secara online
                            dan pantau status pemesanan Anda melalui dashboard.
                        </p>
                        @auth
                            <a href="{{ route('user.dashboard') }}" class="btn btn-success rounded-pill px-4">
                                Buka Dashboard
                            </a>
                        @else
                            <a href="{{ route('user.login') }}" class="btn btn-success rounded-pill px-4 me-2">
                                Login
                            </a>
                            <a href="{{ route('user.register') }}" class="btn btn-outline-success rounded-pill px-4">
                                Daftar
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </section>
    </main>
    @include('user.partials.footer')
    <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center">
        <i class="bi bi-arrow-up-short"></i>
    </a>
    <div id="preloader"></div>
    <script src="{{ asset('Mentor-1.0.0/assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('Mentor-1.0.0/assets/vendor/php-email-form/validate.js') }}"></script>
    <script src="{{ asset('Mentor-1.0.0/assets/vendor/aos/aos.js') }}"></script>
    <script src="{{ asset('Mentor-1.0.0/assets/vendor/glightbox/js/glightbox.min.js') }}"></script>
    <script src="{{ asset('Mentor-1.0.0/assets/vendor/purecounter/purecounter_vanilla.js') }}"></script>
    <script src="{{ asset('Mentor-1.0.0/assets/vendor/swiper/swiper-bundle.min.js') }}"></script>
    <script src="{{ asset('Mentor-1.0.0/assets/js/main.js') }}"></script>
</body>

</html>
