<style>
/* Force remove all list markers and bullets from submenu */
#layout-menu .menu-sub,
#layout-menu .menu-sub ul,
#layout-menu .menu-sub li,
#layout-menu .menu-sub .menu-item {
  list-style: none !important;
  list-style-type: none !important;
}

#layout-menu .menu-sub li::before,
#layout-menu .menu-sub li::after,
#layout-menu .menu-sub .menu-item::before,
#layout-menu .menu-sub .menu-item::after {
  content: none !important;
  display: none !important;
}

#layout-menu .menu-sub li::marker,
#layout-menu .menu-sub .menu-item::marker {
  display: none !important;
  content: '' !important;
}

/* CRITICAL: Remove bullet from menu-link::before (Sneat framework default) */
#layout-menu .menu-sub > .menu-item > .menu-link::before,
#layout-menu .menu-sub .menu-item .menu-link::before,
#layout-menu .menu-sub .menu-item > .menu-link::before {
  content: none !important;
  display: none !important;
  background: none !important;
  width: 0 !important;
  height: 0 !important;
  border: none !important;
  position: absolute !important;
  left: -9999px !important;
}

/* Additional override for any framework styles */
.menu-vertical .menu-sub,
.menu-vertical .menu-sub ul {
  list-style: none !important;
  list-style-type: none !important;
}

.menu-vertical .menu-sub li,
.menu-vertical .menu-sub .menu-item {
  list-style: none !important;
  list-style-type: none !important;
}

.menu-vertical .menu-sub .menu-item .menu-link::before {
  content: none !important;
  display: none !important;
}
</style>

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
        <!-- ============================================ -->
        <!-- DASHBOARD & OVERVIEW -->
        <!-- ============================================ -->
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Dashboard</span>
        </li>
        <li class="menu-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <a href="{{ route('admin.dashboard') }}" class="menu-link d-flex align-items-center gap-2 text-decoration-none">
                <i class='bx bx-home-alt text-primary'></i>
                <span>Dashboard</span>
            </a>
        </li>
        <li class="menu-item {{ request()->routeIs('admin.statistics') ? 'active' : '' }}">
            <a href="{{ route('admin.statistics') }}" class="menu-link d-flex align-items-center gap-2 text-decoration-none">
                <i class='bx bx-chart text-info'></i>
                <span>Statistik</span>
            </a>
        </li>

        <!-- ============================================ -->
        <!-- E-COMMERCE -->
        <!-- ============================================ -->
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">E-Commerce</span>
        </li>
        <li class="menu-item {{ request()->routeIs('admin.stores.*','admin.outlets.*','admin.products.*','admin.categories.*','admin.collections.*','admin.orders.*','admin.shipping.*','admin.stock_movements.*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle d-flex align-items-center gap-2 text-decoration-none">
                <i class='bx bx-store text-success'></i>
                <span>Manajemen Toko</span>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('admin.stores.index') ? 'active' : '' }}">
                    <a href="{{ route('admin.stores.index') }}" class="menu-link d-flex align-items-center gap-2 text-decoration-none">
                        <i class='bx bx-home-alt text-success'></i>
                        <span>Dashboard Toko</span>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.stores.stores','admin.stores.stores.*','admin.stores.show') ? 'active' : '' }}">
                    <a href="{{ route('admin.stores.stores') }}" class="menu-link d-flex align-items-center gap-2 text-decoration-none">
                        <i class='bx bx-building text-success'></i>
                        <span>Daftar Toko</span>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.outlets.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.outlets.index') }}" class="menu-link d-flex align-items-center gap-2 text-decoration-none">
                        <i class='bx bx-map-pin text-success'></i>
                        <span>Daftar Outlet</span>
                    </a>
                </li>
            </ul>
        </li>
        <li class="menu-item {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
            <a href="{{ route('admin.products.index') }}" class="menu-link d-flex align-items-center gap-2 text-decoration-none">
                <i class='bx bx-package text-success'></i>
                <span>Produk</span>
            </a>
        </li>
        <li class="menu-item {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
            <a href="{{ route('admin.categories.index') }}" class="menu-link d-flex align-items-center gap-2 text-decoration-none">
                <i class='bx bx-category text-success'></i>
                <span>Kategori</span>
            </a>
        </li>
        <li class="menu-item {{ request()->routeIs('admin.collections.*','admin.collection_items.*') ? 'active' : '' }}">
            <a href="{{ route('admin.collections.index') }}" class="menu-link d-flex align-items-center gap-2 text-decoration-none">
                <i class='bx bx-collection text-success'></i>
                <span>Koleksi</span>
            </a>
        </li>
        <li class="menu-item {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
            <a href="{{ route('admin.orders.index') }}" class="menu-link d-flex align-items-center gap-2 text-decoration-none">
                <i class='bx bx-receipt text-success'></i>
                <span>Pesanan Online</span>
            </a>
        </li>
        <li class="menu-item {{ request()->routeIs('admin.shipping.*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle d-flex align-items-center gap-2 text-decoration-none">
                <i class='bx bx-truck text-primary'></i>
                <span>Pengiriman</span>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('admin.shipping.methods') ? 'active' : '' }}">
                    <a href="{{ route('admin.shipping.methods') }}" class="menu-link d-flex align-items-center gap-2 text-decoration-none">
                        <i class='bx bx-list-ul text-primary'></i>
                        <span>Metode Pengiriman</span>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.shipping.costs') ? 'active' : '' }}">
                    <a href="{{ route('admin.shipping.costs') }}" class="menu-link d-flex align-items-center gap-2 text-decoration-none">
                        <i class='bx bx-dollar-circle text-primary'></i>
                        <span>Biaya Pengiriman</span>
                    </a>
                </li>
            </ul>
        </li>
        <li class="menu-item {{ request()->routeIs('admin.stock_movements.*') ? 'active' : '' }}">
            <a href="{{ route('admin.stock_movements.index') }}" class="menu-link d-flex align-items-center gap-2 text-decoration-none">
                <i class='bx bx-history text-info'></i>
                <span>Riwayat Stok</span>
            </a>
        </li>

        <!-- ============================================ -->
        <!-- POS & KASIR -->
        <!-- ============================================ -->
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">POS & Kasir</span>
        </li>
        <li class="menu-item {{ request()->routeIs('admin.pos.dashboard') ? 'active' : '' }}">
            <a href="{{ route('admin.pos.dashboard') }}" class="menu-link d-flex align-items-center gap-2 text-decoration-none">
                <i class='bx bx-calculator text-warning'></i>
                <span>Dashboard POS</span>
            </a>
        </li>
        <li class="menu-item {{ request()->routeIs('admin.pos.shifts.*') ? 'active' : '' }}">
            <a href="{{ route('admin.pos.shifts.index') }}" class="menu-link d-flex align-items-center gap-2 text-decoration-none">
                <i class='bx bx-time text-warning'></i>
                <span>Kelola Shift</span>
            </a>
        </li>
        <li class="menu-item {{ request()->routeIs('admin.pos.transactions.*') ? 'active' : '' }}">
            <a href="{{ route('admin.pos.transactions.index') }}" class="menu-link d-flex align-items-center gap-2 text-decoration-none">
                <i class='bx bx-receipt text-warning'></i>
                <span>Transaksi POS</span>
            </a>
        </li>
        <li class="menu-item {{ request()->routeIs('admin.pos.reports.*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle d-flex align-items-center gap-2 text-decoration-none">
                <i class='bx bx-bar-chart text-info'></i>
                <span>Laporan POS</span>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('admin.pos.reports.daily') ? 'active' : '' }}">
                    <a href="{{ route('admin.pos.reports.daily') }}" class="menu-link d-flex align-items-center gap-2 text-decoration-none">
                        <i class='bx bx-calendar text-info'></i>
                        <span>Harian</span>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.pos.reports.product') ? 'active' : '' }}">
                    <a href="{{ route('admin.pos.reports.product') }}" class="menu-link d-flex align-items-center gap-2 text-decoration-none">
                        <i class='bx bx-package text-info'></i>
                        <span>Produk</span>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.pos.reports.category') ? 'active' : '' }}">
                    <a href="{{ route('admin.pos.reports.category') }}" class="menu-link d-flex align-items-center gap-2 text-decoration-none">
                        <i class='bx bx-category text-info'></i>
                        <span>Kategori</span>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.pos.reports.payment') ? 'active' : '' }}">
                    <a href="{{ route('admin.pos.reports.payment') }}" class="menu-link d-flex align-items-center gap-2 text-decoration-none">
                        <i class='bx bx-credit-card text-info'></i>
                        <span>Pembayaran</span>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.pos.reports.cashier') ? 'active' : '' }}">
                    <a href="{{ route('admin.pos.reports.cashier') }}" class="menu-link d-flex align-items-center gap-2 text-decoration-none">
                        <i class='bx bx-user text-info'></i>
                        <span>Kinerja Kasir</span>
                    </a>
                </li>
            </ul>
        </li>
        <li class="menu-item {{ request()->routeIs('admin.reports.unified.*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle d-flex align-items-center gap-2 text-decoration-none">
                <i class='bx bx-bar-chart-alt-2 text-success'></i>
                <span>Laporan Terpadu</span>
                <span class="badge bg-label-success rounded-pill ms-auto">Baru</span>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('admin.reports.unified.index') ? 'active' : '' }}">
                    <a href="{{ route('admin.reports.unified.index') }}" class="menu-link d-flex align-items-center gap-2 text-decoration-none">
                        <i class='bx bx-trending-up text-success'></i>
                        <span>Dashboard Terpadu</span>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.reports.unified.products') ? 'active' : '' }}">
                    <a href="{{ route('admin.reports.unified.products') }}" class="menu-link d-flex align-items-center gap-2 text-decoration-none">
                        <i class='bx bx-package text-success'></i>
                        <span>Produk Terpadu</span>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.reports.unified.categories') ? 'active' : '' }}">
                    <a href="{{ route('admin.reports.unified.categories') }}" class="menu-link d-flex align-items-center gap-2 text-decoration-none">
                        <i class='bx bx-category text-success'></i>
                        <span>Kategori Terpadu</span>
                    </a>
                </li>
            </ul>
        </li>
        <li class="menu-item {{ request()->routeIs('admin.pos.settings.*') ? 'active' : '' }}">
            <a href="{{ route('admin.pos.settings.index') }}" class="menu-link d-flex align-items-center gap-2 text-decoration-none">
                <i class='bx bx-cog text-warning'></i>
                <span>Pengaturan POS</span>
            </a>
        </li>
        <li class="menu-item {{ request()->routeIs('admin.pos.receipt-templates.*') ? 'active' : '' }}">
            <a href="{{ route('admin.pos.receipt-templates.index') }}" class="menu-link d-flex align-items-center gap-2 text-decoration-none">
                <i class='bx bx-receipt text-info'></i>
                <span>Template Struk</span>
            </a>
        </li>

        <!-- ============================================ -->
        <!-- CONTENT MANAGEMENT -->
        <!-- ============================================ -->
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Konten</span>
        </li>
        <li class="menu-item {{ request()->routeIs('admin.artikel.*','admin.kategori_artikel.*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle d-flex align-items-center gap-2 text-decoration-none">
                <i class='bx bx-file text-primary'></i>
                <span>Artikel & Blog</span>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('admin.artikel.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.artikel.index') }}" class="menu-link d-flex align-items-center gap-2 text-decoration-none">
                        <i class='bx bx-file text-primary'></i>
                        <span>Artikel</span>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.kategori_artikel.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.kategori_artikel.index') }}" class="menu-link d-flex align-items-center gap-2 text-decoration-none">
                        <i class='bx bx-category text-primary'></i>
                        <span>Kategori Artikel</span>
                    </a>
                </li>
            </ul>
        </li>
        <li class="menu-item {{ request()->routeIs('admin.features.*','admin.testimonials.*','admin.facts.*','admin.social_links.*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle d-flex align-items-center gap-2 text-decoration-none">
                <i class='bx bx-layer text-warning'></i>
                <span>Konten Website</span>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('admin.features.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.features.index') }}" class="menu-link d-flex align-items-center gap-2 text-decoration-none">
                        <i class='bx bx-star text-warning'></i>
                        <span>Fitur Unggulan</span>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.testimonials.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.testimonials.index') }}" class="menu-link d-flex align-items-center gap-2 text-decoration-none">
                        <i class='bx bx-comment-dots text-success'></i>
                        <span>Testimoni</span>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.facts.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.facts.index') }}" class="menu-link d-flex align-items-center gap-2 text-decoration-none">
                        <i class='bx bx-bar-chart-alt text-primary'></i>
                        <span>Statistik & Fakta</span>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.social_links.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.social_links.index') }}" class="menu-link d-flex align-items-center gap-2 text-decoration-none">
                        <i class='bx bx-share-alt text-info'></i>
                        <span>Media Sosial</span>
                    </a>
                </li>
            </ul>
        </li>
        <li class="menu-item {{ request()->routeIs('admin.carousels.*','admin.slides.*','admin.banners.*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle d-flex align-items-center gap-2 text-decoration-none">
                <i class='bx bx-images text-primary'></i>
                <span>Media & Banner</span>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('admin.carousels.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.carousels.index') }}" class="menu-link d-flex align-items-center gap-2 text-decoration-none">
                        <i class='bx bx-slideshow text-primary'></i>
                        <span>Carousel</span>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.banners.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.banners.index') }}" class="menu-link d-flex align-items-center gap-2 text-decoration-none">
                        <i class='bx bx-image-alt text-warning'></i>
                        <span>Banner</span>
                    </a>
                </li>
            </ul>
        </li>
        <li class="menu-item {{ request()->routeIs('admin.menus.*','admin.links.*','admin.pages.*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle d-flex align-items-center gap-2 text-decoration-none">
                <i class='bx bx-menu text-info'></i>
                <span>Navigasi & Menu</span>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('admin.menus.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.menus.index') }}" class="menu-link d-flex align-items-center gap-2 text-decoration-none">
                        <i class='bx bx-list-ol text-info'></i>
                        <span>Menu Utama</span>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.pages.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.pages.index') }}" class="menu-link d-flex align-items-center gap-2 text-decoration-none">
                        <i class='bx bx-file-blank text-success'></i>
                        <span>Halaman</span>
                    </a>
                </li>
            </ul>
        </li>

        <!-- ============================================ -->
        <!-- USER MANAGEMENT -->
        <!-- ============================================ -->
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Pengguna</span>
        </li>
        <li class="menu-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
            <a href="{{ route('admin.users.index') }}" class="menu-link d-flex align-items-center gap-2 text-decoration-none">
                <i class='bx bx-user text-info'></i>
                <span>Pengguna</span>
            </a>
        </li>
        <li class="menu-item {{ request()->routeIs('admin.mitras.*') ? 'active' : '' }}">
            <a href="{{ route('admin.mitras.index') }}" class="menu-link d-flex align-items-center gap-2 text-decoration-none">
                <i class='bx bx-group text-success'></i>
                <span>Mitra</span>
            </a>
        </li>

        <!-- ============================================ -->
        <!-- SETTINGS & CONFIGURATION -->
        <!-- ============================================ -->
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Pengaturan</span>
        </li>
        <li class="menu-item {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
            <a href="{{ route('admin.settings.index') }}" class="menu-link d-flex align-items-center gap-2 text-decoration-none">
                <i class='bx bx-cog text-danger'></i>
                <span>Pengaturan Umum</span>
            </a>
        </li>

        <!-- ============================================ -->
        <!-- SYSTEM -->
        <!-- ============================================ -->
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Sistem</span>
        </li>
        <li class="menu-item {{ request()->routeIs('admin.help.*') ? 'active' : '' }}">
            <a href="{{ route('admin.help.index') }}" class="menu-link d-flex align-items-center gap-2 text-decoration-none">
                <i class='bx bx-help-circle text-info'></i>
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
