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

    <section id="hero" class="hero section dark-background">
        <img src="{{ asset('images/WhatsApp Image 2026-08-04 at 21.51.34.jpeg') }}" alt="Wallpaper login dan register" data-aos="fade-in">

        <div class="container">
            <div class="row justify-content-center align-items-center">
                <div class="col-lg-5 col-md-8" data-aos="fade-up" data-aos-delay="100">
                    @include('user.partials.alerts')

                    @yield('content')
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