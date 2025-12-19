    <!-- Spinner Start -->
    <div id="spinner" class="w-100 vh-100 bg-white position-fixed translate-middle top-50 start-50  d-flex align-items-center justify-content-center" style="display:none;">
        <div class="spinner-grow text-primary" role="status"></div>
    </div>
    <!-- Spinner End -->

    <!-- Navbar start -->
    <div class="container-fluid fixed-top">
        <div class="container topbar bg-primary d-none d-lg-block">
            <div class="d-flex justify-content-between">
                <div class="top-info ps-2">
                    <small class="me-3"><i class="fas fa-map-marker-alt me-2 text-secondary"></i> <a href="#" class="text-white">{{ $siteSettings['address'] ?? 'Alamat' }}</a></small>
                    <small class="me-3"><i class="fas fa-envelope me-2 text-secondary"></i><a href="#" class="text-white">{{ $siteSettings['email'] ?? '' }}</a></small>
                </div>
                <div class="top-link pe-2">
                    @foreach(($footerMenus ?? collect())->first()->links ?? [] as $link)
                        @php
                            if (!empty($link->page_id) && !empty($link->page_slug)) {
                                $url = route('pages.show', $link->page_slug);
                            } elseif (!empty($link->route_name)) {
                                $url = route($link->route_name);
                            } else {
                                $url = $link->url ?? '#';
                            }
                        @endphp
                        <a href="{{ $url }}" class="text-white"><small class="text-white mx-2">{{ $link->label }}</small>@if(!$loop->last)/@endif</a>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="container px-0" style="position: relative; z-index: 9999;">
            <nav class="navbar navbar-light bg-white navbar-expand-xl" style="position: relative; z-index: 9999; overflow: visible !important;">
                <a href="{{ url('/') }}" class="navbar-brand d-flex align-items-center gap-2">
                    <img src="{{ asset($siteSettings['site_logo'] ?? 'fruitables/img/logo/logo-samsae.png') }}" 
                         alt="Logo" 
                         style="width:{{ $siteSettings['site_logo_width'] ?? 200 }}px;height:{{ $siteSettings['site_logo_height'] ?? 80 }}px;object-fit:{{ $siteSettings['site_logo_object_fit'] ?? 'contain' }};">
                    @if($siteSettings['site_name_logo'] ?? false)
                    <img src="{{ asset($siteSettings['site_name_logo']) }}" 
                         alt="{{ $siteSettings['site_title'] ?? 'Samsae' }}" 
                         style="width:{{ $siteSettings['site_name_logo_width'] ?? 100 }}px;height:{{ $siteSettings['site_name_logo_height'] ?? 50 }}px;object-fit:{{ $siteSettings['site_name_logo_object_fit'] ?? 'contain' }};">
                    @endif
                </a>
                <button class="navbar-toggler py-2 px-3" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                    <span class="fa fa-bars text-primary"></span>
                </button>
                <div class="collapse navbar-collapse bg-white" id="navbarCollapse" style="position: relative; z-index: 9999; overflow: visible !important;">
                    <div class="navbar-nav mx-auto">
                        @include('partials.nav-items', ['items' => $headerLinks ?? collect()])
                    </div>
                    <div class="d-flex m-3 me-0 align-items-center">
                        <button class="btn-search btn border border-secondary btn-md-square rounded-circle bg-white me-4" data-bs-toggle="modal" data-bs-target="#searchModal"><i class="fas fa-search text-primary"></i></button>
                        <a href="{{ route('cart') }}" class="position-relative me-4 my-auto">
                            <i class="fa fa-shopping-bag fa-2x"></i>
                            @php
                                // Get cart count from session (which is synced by LoadUserCart middleware)
                                // For logged-in users, this will be synced from database
                                // For guests, this will be from session
                                $cartCount = count(session('cart', []));
                            @endphp
                            <span class="position-absolute bg-secondary rounded-circle d-flex align-items-center justify-content-center text-dark px-1" style="top: -5px; left: 15px; height: 20px; min-width: 20px;">{{ $cartCount }}</span>
                        </a>

                        @auth
                            <div class="dropdown my-auto" style="position: relative; z-index: 10000 !important; overflow: visible !important;">
                                <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
                                    <i class="fas fa-user-circle fa-2x me-2"></i>
                                    <span class="d-none d-xl-inline">{{ auth()->user()->name }}</span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" style="z-index: 10000 !important; overflow: visible !important;">
                                    @if(auth()->user()->isAdmin())
                                        <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">{{ $siteSettings['nav_admin_dashboard_text'] ?? 'Dashboard Admin' }}</a></li>
                                    @elseif(auth()->user()->isMitra())
                                        <li><a class="dropdown-item" href="{{ route('mitra.dashboard') }}">{{ $siteSettings['nav_mitra_dashboard_text'] ?? 'Dashboard Mitra' }}</a></li>
                                    @else
                                        <li><a class="dropdown-item" href="{{ route('customer.dashboard') }}"><i class="bx bx-home me-2"></i>Dashboard Saya</a></li>
                                        <li><a class="dropdown-item" href="{{ route('customer.profile.index') }}"><i class="bx bx-user me-2"></i>Profil Saya</a></li>
                                        <!-- <li><a class="dropdown-item" href="{{ route('home') }}">{{ $siteSettings['nav_home_text'] ?? 'Beranda' }}</a></li> -->
                                    @endif
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="dropdown-item">{{ $siteSettings['nav_logout_text'] ?? 'Keluar' }}</button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        @else
                            <div class="dropdown my-auto" style="position: relative; z-index: 10000 !important; overflow: visible !important;">
                                <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
                                    <i class="fas fa-user-circle fa-2x me-2"></i>
                                    <span class="d-none d-xl-inline"></span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" style="z-index: 10000 !important; overflow: visible !important;">
                                        <li><a class="dropdown-item" href="{{ route('login') }}">{{ $siteSettings['nav_login_text'] ?? 'Masuk' }}</a></li>
                                   
                                        <li><a class="dropdown-item" href="{{ route('customer.register.form') }}">{{ $siteSettings['nav_register_text'] ?? 'Daftar' }}</a></li>
                                   
                                        <li><a class="dropdown-item" href="{{ route('mitra.register.form') }}">{{ $siteSettings['nav_mitra_register_text'] ?? 'Daftar Mitra' }}</a></li>
                                </ul>
                            </div>
                        @endauth
                    </div>
                </div>
            </nav>
        </div>
    </div>
    <!-- Navbar End -->

    <!-- Modal Search Start -->
    <div class="modal fade" id="searchModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content rounded-0">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Cari berdasarkan kata kunci</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body d-flex align-items-center">
                    <div class="input-group w-75 mx-auto d-flex">
                        <input type="search" class="form-control p-3" placeholder="kata kunci" aria-describedby="search-icon-1">
                        <span id="search-icon-1" class="input-group-text p-3"><i class="fa fa-search"></i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Search End -->
