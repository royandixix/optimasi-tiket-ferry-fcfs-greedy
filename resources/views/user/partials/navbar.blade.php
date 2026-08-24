<header id="header" class="header d-flex align-items-center sticky-top">
    <div class="container-fluid container-xl position-relative d-flex align-items-center">

        <a href="{{ route('home') }}" class="logo d-flex align-items-center me-auto text-decoration-none">
            <h1 class="sitename">Sistem Optimasi Tiket Ferry</h1>
        </a>

        @php
            $isAuthPage = request()->routeIs('user.login') || request()->routeIs('user.register');
            $isHomePage = request()->routeIs('home') || request()->routeIs('user.home.index');
        @endphp

        @if ($isAuthPage)
            <nav id="navmenu" class="navmenu d-none">
                <ul></ul>
            </nav>

            <i class="mobile-nav-toggle d-none bi bi-list"></i>
        @elseif ($isHomePage)
            <nav id="navmenu" class="navmenu">
                <ul>
                    <li>
                        <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">
                            Home
                        </a>
                    </li>

                    <li>
                        <a href="#tentang">Tentang</a>
                    </li>

                    <li>
                        <a href="#fitur">Fitur</a>
                    </li>

                    <li>
                        <a href="#alur">Alur</a>
                    </li>

                    @auth
                        <li>
                            <a href="{{ route('user.dashboard') }}">
                                Dashboard
                            </a>
                        </li>
                    @else
                        <li>
                            <a href="{{ route('user.login') }}">
                                Login
                            </a>
                        </li>
                    @endauth
                </ul>

                <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
            </nav>

            @auth
                <a class="btn-getstarted" href="{{ route('user.dashboard') }}">
                    Dashboard
                </a>
            @else
                <a class="btn-getstarted" href="{{ route('user.register') }}">
                    Daftar
                </a>
            @endauth
        @else
            <nav id="navmenu" class="navmenu">
                <ul>
                    <li>
                        <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">
                            Home
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('user.dashboard') }}" class="{{ request()->routeIs('user.dashboard') ? 'active' : '' }}">
                            Dashboard
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('user.pemesanan.index', ['rekomendasi' => 1]) }}" class="{{ request()->routeIs('user.pemesanan.*') ? 'active' : '' }}">
                            Pemesanan
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('user.profil.edit') }}" class="{{ request()->routeIs('user.profil.*') ? 'active' : '' }}">
                            Profil
                        </a>
                    </li>

                    <li>
                        <form method="POST" action="{{ route('user.logout') }}">
                            @csrf

                            <button type="submit" class="btn btn-link p-0 text-decoration-none">
                                Keluar
                            </button>
                        </form>
                    </li>
                </ul>

                <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
            </nav>
        @endif

    </div>
</header>