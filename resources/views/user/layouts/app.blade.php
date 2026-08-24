<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>@yield('title', 'Portal Penumpang') - Sistem Optimasi Tiket Ferry</title>
    <meta name="description" content="Portal penumpang untuk pemesanan tiket kapal ferry, pengecekan jadwal keberangkatan, dan pemantauan status pemesanan.">
    <meta name="keywords" content="tiket ferry, kapal ferry, pemesanan tiket, optimasi kapasitas kapal, portal penumpang">

    <link href="{{ asset('Mentor-1.0.0/assets/img/favicon.png') }}" rel="icon">
    <link href="{{ asset('Mentor-1.0.0/assets/img/apple-touch-icon.png') }}" rel="apple-touch-icon">

    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;500;600;700;800&family=Poppins:wght@300;400;500;600;700;800;900&family=Raleway:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

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

    <div class="page-title" data-aos="fade">
        <div class="heading">
            <div class="container">
                <div class="row d-flex justify-content-center text-center">
                    <div class="col-lg-8">
                        <h1>@yield('page-title', 'Portal Penumpang')</h1>
                        <p class="mb-0">
                            @yield('page-description', 'Kelola pemesanan tiket kapal ferry dan pantau status perjalanan Anda melalui sistem.')
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="container">
            @include('user.partials.alerts')

            @yield('content')
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