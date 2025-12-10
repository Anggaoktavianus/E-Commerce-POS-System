<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{ route('admin.dashboard') }}" class="app-brand-link d-flex align-items-center gap-2">
            <span class="app-brand-logo demo">
                <i class="bx bx-store fs-4 text-primary"></i>
            </span>
            <span class="app-brand-text demo menu-text fw-bolder ms-2">{{ config('app.name') }}</span>
        </a>
        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
        <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>
    
    <!-- Admin User Info -->
    <div class="menu-inner-shadow"></div>
    <div class="px-3 py-2 border-bottom">
        <div class="d-flex align-items-center">
            <div class="avatar avatar-sm">
                <img src="{{ asset('sneat/assets/img/avatars/1.png') }}" alt class="rounded-circle" />
            </div>
            <div class="ms-3">
                <h6 class="mb-0 small">{{ auth()->user()->name ?? 'Admin User' }}</h6>
                <small class="text-muted">Administrator</small>
            </div>
        </div>
    </div>
    
    <ul class="menu-inner py-1">
        <!-- Dashboard -->
        <li class="menu-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <a href="{{ route('admin.dashboard') }}" class="menu-link d-flex align-items-center gap-2 text-decoration-none">
                <i class='bx bx-home-alt text-primary'></i>
                <span>Dashboard</span>
            </a>
        </li>
        
        <!-- Quick Stats -->
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Ringkasan</span>
        </li>
        <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link d-flex align-items-center gap-2 text-decoration-none">
                <i class='bx bx-chart text-info'></i>
                <span>Statistik</span>
                <span class="badge bg-label-primary rounded-pill ms-auto">12</span>
            </a>
        </li>
        
        <!-- Content Management -->
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Konten</span>
        </li>
        <li class="menu-item {{ request()->routeIs('admin.artikel.*','admin.kategori_artikel.*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle d-flex align-items-center gap-2 text-decoration-none">
                <i class='bx bx-file text-primary'></i>
                <div>Artikel & Blog</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('admin.artikel.index','admin.artikel.create','admin.artikel.edit','admin.artikel.show') ? 'active open' : '' }}">
                    <a href="{{ route('admin.artikel.index') }}" class="menu-link d-flex align-items-center gap-2 text-decoration-none">
                        <i class='bx bx-file text-primary'></i>
                        <span>Artikel</span>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.kategori_artikel.index','admin.kategori_artikel.create','admin.kategori_artikel.edit') ? 'active open' : '' }}">
                    <a href="{{ route('admin.kategori_artikel.index') }}" class="menu-link d-flex align-items-center gap-2 text-decoration-none">
                        <i class='bx bx-category text-primary'></i>
                        <span>Kategori Artikel</span>
                    </a>
                </li>
            </ul>
        </li>
        <li class="menu-item {{ request()->routeIs('admin.pages.*') ? 'active open' : '' }}">
            <a href="{{ route('admin.pages.index') }}" class="menu-link d-flex align-items-center gap-2 text-decoration-none">
                <i class='bx bx-file text-warning'></i>
                <span>Halaman</span>
                <span class="badge bg-label-warning rounded-pill ms-auto">8</span>
            </a>
        </li>
        <li class="menu-item {{ request()->routeIs('admin.categories.*') ? 'active open' : '' }}">
            <a href="{{ route('admin.categories.index') }}" class="menu-link d-flex align-items-center gap-2 text-decoration-none">
                <i class='bx bx-category text-info'></i>
                <span>Kategori</span>
            </a>
        </li>
        
        <!-- Website Management -->
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Pengaturan Website</span>
        </li>
        
        <!-- General Settings -->
        <li class="menu-item {{ request()->routeIs('admin.settings.*') ? 'active open' : '' }}">
            <a href="{{ route('admin.settings.index') }}" class="menu-link d-flex align-items-center gap-2 text-decoration-none">
                <i class='bx bx-cog text-danger'></i>
                <span>Pengaturan Umum</span>
            </a>
        </li>
        
        <!-- E-commerce Management -->
        <!-- <li class="menu-item {{ request()->routeIs('admin.products.*','admin.categories.*','admin.collections.*','admin.orders.*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class='bx bx-shopping-bag text-success'></i>
                <div>E-commerce</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('admin.products.index','admin.products.create','admin.products.edit') ? 'active open' : '' }}">
                    <a href="{{ route('admin.products.index') }}" class="menu-link d-flex align-items-center gap-2 text-decoration-none">
                        <i class='bx bx-package text-success'></i>
                        <span>Produk</span>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.categories.index','admin.categories.create','admin.categories.edit') ? 'active open' : '' }}">
                    <a href="{{ route('admin.categories.index') }}" class="menu-link d-flex align-items-center gap-2 text-decoration-none">
                        <i class='bx bx-category text-success'></i>
                        <span>Kategori</span>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.collections.index','admin.collections.create','admin.collections.edit') ? 'active open' : '' }}">
                    <a href="{{ route('admin.collections.index') }}" class="menu-link d-flex align-items-center gap-2 text-decoration-none">
                        <i class='bx bx-collection text-success'></i>
                        <span>Koleksi</span>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.orders.index','admin.orders.show') ? 'active open' : '' }}">
                    <a href="{{ route('admin.orders.index') }}" class="menu-link d-flex align-items-center gap-2 text-decoration-none">
                        <i class='bx bx-receipt text-success'></i>
                        <span>Pesanan</span>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.shipping.*') ? 'active open' : '' }}">
                    <a href="javascript:void(0);" class="menu-link menu-toggle">
                        <i class='bx bx-truck text-primary'></i>
                        <div>Pengiriman</div>
                    </a>
                    <ul class="menu-sub">
                        <li class="menu-item {{ request()->routeIs('admin.shipping.methods') ? 'active open' : '' }}">
                            <a href="{{ route('admin.shipping.methods') }}" class="menu-link d-flex align-items-center gap-2 text-decoration-none">
                                <i class='bx bx-list-ul text-primary'></i>
                                <span>Metode Pengiriman</span>
                            </a>
                        </li>
                        <li class="menu-item {{ request()->routeIs('admin.shipping.costs') ? 'active open' : '' }}">
                            <a href="{{ route('admin.shipping.costs') }}" class="menu-link d-flex align-items-center gap-2 text-decoration-none">
                                <i class='bx bx-dollar-circle text-primary'></i>
                                <span>Biaya Pengiriman</span>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </li> -->

        <!-- Store Management -->
        <li class="menu-item {{ request()->routeIs('admin.stores.*','admin.outlets.*','admin.products.*','admin.categories.*','admin.collections.*','admin.collection_items.*','admin.orders.*','admin.shipping.*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle d-flex align-items-center gap-2 text-decoration-none">
                <i class='bx bx-store text-success'></i>
                <span> Manajemen Toko</span>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('admin.stores.index') ? 'active open' : '' }}">
                    <a href="{{ route('admin.stores.index') }}" class="menu-link d-flex align-items-center gap-2 text-decoration-none">
                        <i class='bx bx-home-alt text-success'></i>
                        <span>Dashboard Toko</span>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.stores.stores','admin.stores.stores.*','admin.stores.show') ? 'active open' : '' }}">
                    <a href="{{ route('admin.stores.stores') }}" class="menu-link d-flex align-items-center gap-2 text-decoration-none">
                        <i class='bx bx-building text-success'></i>
                        <span>Daftar Toko</span>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.outlets.*') ? 'active open' : '' }}">
                    <a href="{{ route('admin.outlets.index') }}" class="menu-link d-flex align-items-center gap-2 text-decoration-none">
                        <i class='bx bx-map-pin text-success'></i>
                        <span>Daftar Outlet</span>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.products.index','admin.products.create','admin.products.edit') ? 'active open' : '' }}">
                    <a href="{{ route('admin.products.index') }}" class="menu-link d-flex align-items-center gap-2 text-decoration-none">
                        <i class='bx bx-package text-success'></i>
                        <span>Produk</span>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.categories.index','admin.categories.create','admin.categories.edit') ? 'active open' : '' }}">
                    <a href="{{ route('admin.categories.index') }}" class="menu-link d-flex align-items-center gap-2 text-decoration-none">
                        <i class='bx bx-category text-success'></i>
                        <span>Kategori</span>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.collections.index','admin.collections.create','admin.collections.edit') ? 'active open' : '' }}">
                    <a href="{{ route('admin.collections.index') }}" class="menu-link d-flex align-items-center gap-2 text-decoration-none">
                        <i class='bx bx-collection text-success'></i>
                        <span>Koleksi</span>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.orders.index','admin.orders.show') ? 'active open' : '' }}">
                    <a href="{{ route('admin.orders.index') }}" class="menu-link d-flex align-items-center gap-2 text-decoration-none">
                        <i class='bx bx-receipt text-success'></i>
                        <span>Pesanan</span>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.shipping.*') ? 'active open' : '' }}">
                    <a href="javascript:void(0);" class="menu-link menu-toggle">
                        <i class='bx bx-truck text-primary'></i>
                        <div>Pengiriman</div>
                    </a>
                    <ul class="menu-sub">
                        <li class="menu-item {{ request()->routeIs('admin.shipping.methods') ? 'active open' : '' }}">
                            <a href="{{ route('admin.shipping.methods') }}" class="menu-link d-flex align-items-center gap-2 text-decoration-none">
                                <i class='bx bx-list-ul text-primary'></i>
                                <span>Metode Pengiriman</span>
                            </a>
                        </li>
                        <li class="menu-item {{ request()->routeIs('admin.shipping.costs') ? 'active open' : '' }}">
                            <a href="{{ route('admin.shipping.costs') }}" class="menu-link d-flex align-items-center gap-2 text-decoration-none">
                                <i class='bx bx-dollar-circle text-primary'></i>
                                <span>Biaya Pengiriman</span>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </li>

        <!-- Navigation & Menu -->
        <li class="menu-item {{ request()->routeIs('admin.menus.*','admin.links.*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle d-flex align-items-center gap-2 text-decoration-none">
                <i class='bx bx-menu text-info'></i>
                <div>Navigasi & Menu</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('admin.menus.index','admin.menus.create','admin.menus.edit') ? 'active open' : '' }}">
                    <a href="{{ route('admin.menus.index') }}" class="menu-link d-flex align-items-center gap-2 text-decoration-none">
                        <i class='bx bx-list-ol text-info'></i>
                        <span>Menu Utama</span>
                    </a>
                </li>
                <!-- <li class="menu-item {{ request()->routeIs('admin.links.index','admin.links.create','admin.links.edit','admin.links.store','admin.links.update','admin.links.destroy','admin.links.data') ? 'active open' : '' }}">
                    <a href="{{ route('admin.links.index', ['menu' => 'header']) }}" class="menu-link d-flex align-items-center gap-2 text-decoration-none">
                        <i class='bx bx-link text-info'></i>
                        <span>Menu Links</span>
                    </a>
                </li> -->
            </ul>
        </li>
        
        <!-- Content Management -->
        <li class="menu-item {{ request()->routeIs('admin.features.*','admin.testimonials.*','admin.facts.*','admin.social_links.*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle d-flex align-items-center gap-2 text-decoration-none">
                <i class='bx bx-layer text-warning'></i>
                <div>Konten Website</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('admin.features.index','admin.features.create','admin.features.edit') ? 'active open' : '' }}">
                    <a href="{{ route('admin.features.index') }}" class="menu-link d-flex align-items-center gap-2 text-decoration-none">
                        <i class='bx bx-star text-warning'></i>
                        <span>Fitur Unggulan</span>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.testimonials.index','admin.testimonials.create','admin.testimonials.edit') ? 'active open' : '' }}">
                    <a href="{{ route('admin.testimonials.index') }}" class="menu-link d-flex align-items-center gap-2 text-decoration-none">
                        <i class='bx bx-comment-dots text-success'></i>
                        <span>Testimoni</span>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.facts.index','admin.facts.create','admin.facts.edit') ? 'active open' : '' }}">
                    <a href="{{ route('admin.facts.index') }}" class="menu-link d-flex align-items-center gap-2 text-decoration-none">
                        <i class='bx bx-bar-chart-alt text-primary'></i>
                        <span>Statistik & Fakta</span>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.social_links.index','admin.social_links.create','admin.social_links.edit') ? 'active open' : '' }}">
                    <a href="{{ route('admin.social_links.index') }}" class="menu-link d-flex align-items-center gap-2 text-decoration-none">
                        <i class='bx bx-share-alt text-info'></i>
                        <span>Media Sosial</span>
                    </a>
                </li>
            </ul>
        </li>
        
        <!-- Media Management -->
        <li class="menu-item {{ request()->routeIs('admin.carousels.*','admin.slides.*','admin.banners.*','admin.collections.*','admin.collection_items.*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle d-flex align-items-center gap-2 text-decoration-none">
                <i class='bx bx-images text-primary'></i>
                <div>Media & Banner</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('admin.carousels.index','admin.carousels.create','admin.carousels.edit') ? 'active open' : '' }}">
                    <a href="{{ route('admin.carousels.index') }}" class="menu-link d-flex align-items-center gap-2 text-decoration-none">
                        <i class='bx bx-slideshow text-primary'></i>
                        <span>Carousel</span>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.banners.index','admin.banners.create','admin.banners.edit') ? 'active open' : '' }}">
                    <a href="{{ route('admin.banners.index') }}" class="menu-link d-flex align-items-center gap-2 text-decoration-none">
                        <i class='bx bx-image-alt text-warning'></i>
                        <span>Banner</span>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.collections.index','admin.collections.create','admin.collections.edit') ? 'active open' : '' }}">
                    <a href="{{ route('admin.collections.index') }}" class="menu-link d-flex align-items-center gap-2 text-decoration-none">
                        <i class='bx bx-collection text-info'></i>
                        <span>Koleksi Produk</span>
                    </a>
                </li>
            </ul>
        </li>
        
        <!-- User Management -->
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Pengguna</span>
        </li>
        <li class="menu-item {{ request()->routeIs('admin.users.*') ? 'active open' : '' }}">
            <a href="{{ route('admin.users.index') }}" class="menu-link d-flex align-items-center gap-2 text-decoration-none">
                <i class='bx bx-user text-info'></i>
                <span>Pengguna</span>
                <span class="badge bg-label-info rounded-pill ms-auto">5</span>
            </a>
        </li>
        <li class="menu-item {{ request()->routeIs('admin.mitras.*') ? 'active open' : '' }}">
            <a href="{{ route('admin.mitras.index') }}" class="menu-link d-flex align-items-center gap-2 text-decoration-none">
                <i class='bx bx-group text-success'></i>
                <span>Mitra</span>
                <span class="badge bg-label-success rounded-pill ms-auto">12</span>
            </a>
        </li>
        
        <!-- System -->
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Sistem</span>
        </li>
        <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link d-flex align-items-center gap-2 text-decoration-none">
                <i class='bx bx-help-circle text-muted'></i>
                <span>Bantuan</span>
            </a>
        </li>
        <li class="menu-item">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="menu-link d-flex align-items-center gap-2 text-decoration-none border-0 bg-transparent w-100 text-start">
                    <i class='bx bx-power-off text-danger'></i>
                    <span>Keluar</span>
                </button>
            </form>
        </li>
    </ul>
</aside>