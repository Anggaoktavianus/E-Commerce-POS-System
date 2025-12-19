@extends('admin.layouts.app')

@section('title', 'Bantuan & Dokumentasi')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Header Section -->
  <div class="card page-header-card mb-4">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center flex-wrap">
        <div>
          <h4 class="mb-1">
            <i class="bx bx-help-circle me-2 text-primary"></i>Bantuan & Dokumentasi
          </h4>
          <p class="text-muted mb-0">Panduan lengkap penggunaan sistem admin</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Search Box -->
  <div class="card mb-4">
    <div class="card-body">
      <div class="input-group">
        <span class="input-group-text"><i class="bx bx-search"></i></span>
        <input type="text" id="searchHelp" class="form-control" placeholder="Cari menu atau fungsi...">
      </div>
    </div>
  </div>

  <!-- Quick Navigation -->
  <div class="row g-4 mb-4">
    <div class="col-md-3">
      <div class="card text-center h-100">
        <div class="card-body">
          <i class="bx bx-home-alt text-primary" style="font-size: 2.5rem;"></i>
          <h6 class="mt-3">Dashboard</h6>
          <a href="#dashboard" class="btn btn-sm btn-outline-primary">Lihat</a>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card text-center h-100">
        <div class="card-body">
          <i class="bx bx-store text-success" style="font-size: 2.5rem;"></i>
          <h6 class="mt-3">E-Commerce</h6>
          <a href="#ecommerce" class="btn btn-sm btn-outline-success">Lihat</a>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card text-center h-100">
        <div class="card-body">
          <i class="bx bx-calculator text-warning" style="font-size: 2.5rem;"></i>
          <h6 class="mt-3">POS & Kasir</h6>
          <a href="#pos" class="btn btn-sm btn-outline-warning">Lihat</a>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card text-center h-100">
        <div class="card-body">
          <i class="bx bx-file text-info" style="font-size: 2.5rem;"></i>
          <h6 class="mt-3">Konten</h6>
          <a href="#content" class="btn btn-sm btn-outline-info">Lihat</a>
        </div>
      </div>
    </div>
  </div>

  <!-- Documentation Sections -->
  <div id="helpContent">
    <!-- Dashboard & Overview -->
    <div class="card mb-4" id="dashboard">
      <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="bx bx-home-alt me-2"></i>Dashboard & Overview</h5>
      </div>
      <div class="card-body">
        <div class="row g-4">
          <div class="col-md-6">
            <div class="border-start border-primary border-3 ps-3">
              <h6 class="text-primary">Dashboard</h6>
              <p class="text-muted mb-2">Halaman utama admin panel yang menampilkan ringkasan data penting.</p>
              <ul class="list-unstyled">
                <li><i class="bx bx-check text-success me-2"></i>Statistik penjualan</li>
                <li><i class="bx bx-check text-success me-2"></i>Grafik penjualan</li>
                <li><i class="bx bx-check text-success me-2"></i>Pesanan terbaru</li>
                <li><i class="bx bx-check text-success me-2"></i>Produk terlaris</li>
              </ul>
              <p class="mb-0"><strong>Cara Menggunakan:</strong> Akses langsung dari menu utama untuk melihat overview sistem.</p>
            </div>
          </div>
          <div class="col-md-6">
            <div class="border-start border-info border-3 ps-3">
              <h6 class="text-info">Statistik</h6>
              <p class="text-muted mb-2">Laporan statistik lengkap tentang performa sistem.</p>
              <ul class="list-unstyled">
                <li><i class="bx bx-check text-success me-2"></i>Statistik penjualan</li>
                <li><i class="bx bx-check text-success me-2"></i>Statistik produk</li>
                <li><i class="bx bx-check text-success me-2"></i>Statistik customer</li>
                <li><i class="bx bx-check text-success me-2"></i>Analytics & insights</li>
              </ul>
              <p class="mb-0"><strong>Cara Menggunakan:</strong> Klik menu "Statistik" untuk melihat detail analitik.</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- E-Commerce -->
    <div class="card mb-4" id="ecommerce">
      <div class="card-header bg-success text-white">
        <h5 class="mb-0"><i class="bx bx-store me-2"></i>E-Commerce</h5>
      </div>
      <div class="card-body">
        <div class="row g-4">
          <div class="col-md-6">
            <div class="border-start border-success border-3 ps-3 mb-4">
              <h6 class="text-success">Manajemen Toko</h6>
              <p class="text-muted mb-2">Kelola toko, outlet, dan struktur bisnis Anda.</p>
              <ul class="list-unstyled">
                <li><strong>Dashboard Toko:</strong> Overview semua toko</li>
                <li><strong>Daftar Toko:</strong> Kelola data toko (tambah, edit, hapus)</li>
                <li><strong>Daftar Outlet:</strong> Kelola outlet per toko</li>
              </ul>
              <p class="mb-0"><strong>Cara Menggunakan:</strong> Pilih menu "Manajemen Toko" → Pilih submenu yang diinginkan.</p>
            </div>
          </div>
          <div class="col-md-6">
            <div class="border-start border-success border-3 ps-3 mb-4">
              <h6 class="text-success">Produk</h6>
              <p class="text-muted mb-2">Kelola katalog produk untuk dijual.</p>
              <ul class="list-unstyled">
                <li><i class="bx bx-check text-success me-2"></i>Tambah produk baru</li>
                <li><i class="bx bx-check text-success me-2"></i>Edit produk existing</li>
                <li><i class="bx bx-check text-success me-2"></i>Kelola stok produk</li>
                <li><i class="bx bx-check text-success me-2"></i>Upload gambar produk</li>
                <li><i class="bx bx-check text-success me-2"></i>Set harga per outlet</li>
              </ul>
              <p class="mb-0"><strong>Cara Menggunakan:</strong> Klik "Produk" → "Tambah Baru" untuk membuat produk, atau klik produk untuk edit.</p>
            </div>
          </div>
          <div class="col-md-6">
            <div class="border-start border-success border-3 ps-3 mb-4">
              <h6 class="text-success">Kategori</h6>
              <p class="text-muted mb-2">Organisir produk berdasarkan kategori.</p>
              <ul class="list-unstyled">
                <li><i class="bx bx-check text-success me-2"></i>Buat kategori baru</li>
                <li><i class="bx bx-check text-success me-2"></i>Edit kategori</li>
                <li><i class="bx bx-check text-success me-2"></i>Hapus kategori</li>
                <li><i class="bx bx-check text-success me-2"></i>Atur hierarki kategori</li>
              </ul>
              <p class="mb-0"><strong>Cara Menggunakan:</strong> Klik "Kategori" → "Tambah Baru" untuk membuat kategori.</p>
            </div>
          </div>
          <div class="col-md-6">
            <div class="border-start border-success border-3 ps-3 mb-4">
              <h6 class="text-success">Koleksi</h6>
              <p class="text-muted mb-2">Buat koleksi produk untuk promosi atau grouping.</p>
              <ul class="list-unstyled">
                <li><i class="bx bx-check text-success me-2"></i>Buat koleksi produk</li>
                <li><i class="bx bx-check text-success me-2"></i>Tambah produk ke koleksi</li>
                <li><i class="bx bx-check text-success me-2"></i>Kelola item koleksi</li>
              </ul>
              <p class="mb-0"><strong>Cara Menggunakan:</strong> Klik "Koleksi" → "Tambah Baru" → Pilih produk untuk ditambahkan.</p>
            </div>
          </div>
          <div class="col-md-6">
            <div class="border-start border-success border-3 ps-3 mb-4">
              <h6 class="text-success">Pesanan Online</h6>
              <p class="text-muted mb-2">Kelola pesanan dari customer online.</p>
              <ul class="list-unstyled">
                <li><i class="bx bx-check text-success me-2"></i>Lihat semua pesanan</li>
                <li><i class="bx bx-check text-success me-2"></i>Update status pesanan</li>
                <li><i class="bx bx-check text-success me-2"></i>Proses pesanan</li>
                <li><i class="bx bx-check text-success me-2"></i>Kirim pesanan</li>
                <li><i class="bx bx-check text-success me-2"></i>Batalkan pesanan</li>
              </ul>
              <p class="mb-0"><strong>Cara Menggunakan:</strong> Klik "Pesanan Online" → Pilih pesanan → Update status sesuai kebutuhan.</p>
            </div>
          </div>
          <div class="col-md-6">
            <div class="border-start border-primary border-3 ps-3 mb-4">
              <h6 class="text-primary">Pengiriman</h6>
              <p class="text-muted mb-2">Kelola metode dan biaya pengiriman.</p>
              <ul class="list-unstyled">
                <li><strong>Metode Pengiriman:</strong> JNE, J&T, Pos Indonesia, dll</li>
                <li><strong>Biaya Pengiriman:</strong> Set tarif per daerah</li>
                <li><i class="bx bx-check text-success me-2"></i>Distance-based shipping</li>
                <li><i class="bx bx-check text-success me-2"></i>Flat rate shipping</li>
              </ul>
              <p class="mb-0"><strong>Cara Menggunakan:</strong> Klik "Pengiriman" → Pilih "Metode" atau "Biaya" untuk mengatur.</p>
            </div>
          </div>
          <div class="col-md-6">
            <div class="border-start border-info border-3 ps-3 mb-4">
              <h6 class="text-info">Riwayat Stok</h6>
              <p class="text-muted mb-2">Tracking pergerakan stok produk.</p>
              <ul class="list-unstyled">
                <li><i class="bx bx-check text-success me-2"></i>Lihat history stok masuk/keluar</li>
                <li><i class="bx bx-check text-success me-2"></i>Audit trail pergerakan stok</li>
                <li><i class="bx bx-check text-success me-2"></i>Filter berdasarkan produk/outlet</li>
              </ul>
              <p class="mb-0"><strong>Cara Menggunakan:</strong> Klik "Riwayat Stok" → Filter sesuai kebutuhan → Lihat detail pergerakan.</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- POS & Kasir -->
    <div class="card mb-4" id="pos">
      <div class="card-header bg-warning text-dark">
        <h5 class="mb-0"><i class="bx bx-calculator me-2"></i>POS & Kasir</h5>
      </div>
      <div class="card-body">
        <div class="row g-4">
          <div class="col-md-6">
            <div class="border-start border-warning border-3 ps-3 mb-4">
              <h6 class="text-warning">Dashboard POS</h6>
              <p class="text-muted mb-2">Halaman utama untuk operasional POS.</p>
              <ul class="list-unstyled">
                <li><i class="bx bx-check text-success me-2"></i>Statistik penjualan hari ini</li>
                <li><i class="bx bx-check text-success me-2"></i>Shift aktif</li>
                <li><i class="bx bx-check text-success me-2"></i>Transaksi terbaru</li>
                <li><i class="bx bx-check text-success me-2"></i>Quick actions</li>
              </ul>
              <p class="mb-0"><strong>Cara Menggunakan:</strong> Akses dari menu "Dashboard POS" untuk melihat overview.</p>
            </div>
          </div>
          <div class="col-md-6">
            <div class="border-start border-warning border-3 ps-3 mb-4">
              <h6 class="text-warning">Kelola Shift</h6>
              <p class="text-muted mb-2">Buka dan tutup shift kasir untuk tracking harian.</p>
              <ul class="list-unstyled">
                <li><strong>Buka Shift:</strong> Set saldo awal kas</li>
                <li><strong>Tutup Shift:</strong> Rekonsiliasi kas harian</li>
                <li><i class="bx bx-check text-success me-2"></i>Lihat history shift</li>
                <li><i class="bx bx-check text-success me-2"></i>Laporan shift</li>
              </ul>
              <p class="mb-0"><strong>Cara Menggunakan:</strong> Klik "Kelola Shift" → "Buka Shift Baru" → Input saldo awal → "Tutup Shift" di akhir hari.</p>
            </div>
          </div>
          <div class="col-md-6">
            <div class="border-start border-warning border-3 ps-3 mb-4">
              <h6 class="text-warning">Transaksi POS</h6>
              <p class="text-muted mb-2">Buat transaksi penjualan offline di outlet.</p>
              <ul class="list-unstyled">
                <li><strong>Buat Transaksi:</strong> Scan/tambah produk → Set customer → Bayar</li>
                <li><i class="bx bx-check text-success me-2"></i>Scan barcode produk</li>
                <li><i class="bx bx-check text-success me-2"></i>Apply discount/coupon</li>
                <li><i class="bx bx-check text-success me-2"></i>Multiple payment methods</li>
                <li><i class="bx bx-check text-success me-2"></i>Print receipt</li>
                <li><i class="bx bx-check text-success me-2"></i>Cancel/Refund transaksi</li>
              </ul>
              <p class="mb-0"><strong>Cara Menggunakan:</strong> Klik "Transaksi POS" → "Buat Transaksi" → Tambah produk → Bayar → Print struk.</p>
            </div>
          </div>
          <div class="col-md-6">
            <div class="border-start border-info border-3 ps-3 mb-4">
              <h6 class="text-info">Laporan POS</h6>
              <p class="text-muted mb-2">Laporan penjualan dari POS system.</p>
              <ul class="list-unstyled">
                <li><strong>Harian:</strong> Laporan penjualan per hari</li>
                <li><strong>Produk:</strong> Penjualan per produk</li>
                <li><strong>Kategori:</strong> Penjualan per kategori</li>
                <li><strong>Pembayaran:</strong> Breakdown metode pembayaran</li>
                <li><strong>Kasir:</strong> Kinerja per kasir</li>
                <li><i class="bx bx-check text-success me-2"></i>Export ke CSV/PDF</li>
              </ul>
              <p class="mb-0"><strong>Cara Menggunakan:</strong> Klik "Laporan POS" → Pilih jenis laporan → Set filter → Export jika perlu.</p>
            </div>
          </div>
          <div class="col-md-6">
            <div class="border-start border-success border-3 ps-3 mb-4">
              <h6 class="text-success">Laporan Terpadu</h6>
              <p class="text-muted mb-2">Gabungan laporan online + POS untuk analisis lengkap.</p>
              <ul class="list-unstyled">
                <li><strong>Dashboard Terpadu:</strong> Overview online vs offline</li>
                <li><strong>Produk Terpadu:</strong> Penjualan produk dari kedua channel</li>
                <li><strong>Kategori Terpadu:</strong> Penjualan kategori dari kedua channel</li>
                <li><i class="bx bx-check text-success me-2"></i>Comparison chart</li>
                <li><i class="bx bx-check text-success me-2"></i>Percentage breakdown</li>
              </ul>
              <p class="mb-0"><strong>Cara Menggunakan:</strong> Klik "Laporan Terpadu" → Pilih periode → Lihat breakdown online vs POS.</p>
            </div>
          </div>
          <div class="col-md-6">
            <div class="border-start border-warning border-3 ps-3 mb-4">
              <h6 class="text-warning">Pengaturan POS</h6>
              <p class="text-muted mb-2">Konfigurasi POS per outlet.</p>
              <ul class="list-unstyled">
                <li><i class="bx bx-check text-success me-2"></i>Tax rate & enable/disable</li>
                <li><i class="bx bx-check text-success me-2"></i>Discount settings</li>
                <li><i class="bx bx-check text-success me-2"></i>Payment methods</li>
                <li><i class="bx bx-check text-success me-2"></i>Loyalty points rate</li>
                <li><i class="bx bx-check text-success me-2"></i>Member discount</li>
                <li><i class="bx bx-check text-success me-2"></i>Receipt settings</li>
              </ul>
              <p class="mb-0"><strong>Cara Menggunakan:</strong> Klik "Pengaturan POS" → Pilih outlet → Atur sesuai kebutuhan → Simpan.</p>
            </div>
          </div>
          <div class="col-md-6">
            <div class="border-start border-info border-3 ps-3 mb-4">
              <h6 class="text-info">Template Struk</h6>
              <p class="text-muted mb-2">Kelola template struk untuk thermal printer.</p>
              <ul class="list-unstyled">
                <li><i class="bx bx-check text-success me-2"></i>Buat template custom</li>
                <li><i class="bx bx-check text-success me-2"></i>Edit template</li>
                <li><i class="bx bx-check text-success me-2"></i>Preview template</li>
                <li><i class="bx bx-check text-success me-2"></i>Set default template</li>
              </ul>
              <p class="mb-0"><strong>Cara Menggunakan:</strong> Klik "Template Struk" → "Tambah Baru" → Edit template → Preview → Simpan.</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Content Management -->
    <div class="card mb-4" id="content">
      <div class="card-header bg-info text-white">
        <h5 class="mb-0"><i class="bx bx-file me-2"></i>Konten</h5>
      </div>
      <div class="card-body">
        <div class="row g-4">
          <div class="col-md-6">
            <div class="border-start border-primary border-3 ps-3 mb-4">
              <h6 class="text-primary">Artikel & Blog</h6>
              <p class="text-muted mb-2">Kelola konten artikel dan blog untuk website.</p>
              <ul class="list-unstyled">
                <li><strong>Artikel:</strong> Buat, edit, publish artikel</li>
                <li><strong>Kategori Artikel:</strong> Organisir artikel per kategori</li>
                <li><i class="bx bx-check text-success me-2"></i>Rich text editor</li>
                <li><i class="bx bx-check text-success me-2"></i>Upload gambar</li>
                <li><i class="bx bx-check text-success me-2"></i>SEO settings</li>
              </ul>
              <p class="mb-0"><strong>Cara Menggunakan:</strong> Klik "Artikel & Blog" → "Artikel" → "Tambah Baru" → Tulis artikel → Publish.</p>
            </div>
          </div>
          <div class="col-md-6">
            <div class="border-start border-warning border-3 ps-3 mb-4">
              <h6 class="text-warning">Konten Website</h6>
              <p class="text-muted mb-2">Kelola elemen konten di halaman website.</p>
              <ul class="list-unstyled">
                <li><strong>Fitur Unggulan:</strong> Highlight fitur utama</li>
                <li><strong>Testimoni:</strong> Review dari customer</li>
                <li><strong>Statistik & Fakta:</strong> Data statistik website</li>
                <li><strong>Media Sosial:</strong> Link social media</li>
              </ul>
              <p class="mb-0"><strong>Cara Menggunakan:</strong> Klik "Konten Website" → Pilih elemen → Tambah/Edit konten.</p>
            </div>
          </div>
          <div class="col-md-6">
            <div class="border-start border-primary border-3 ps-3 mb-4">
              <h6 class="text-primary">Media & Banner</h6>
              <p class="text-muted mb-2">Kelola carousel dan banner untuk homepage.</p>
              <ul class="list-unstyled">
                <li><strong>Carousel:</strong> Slider gambar di homepage</li>
                <li><strong>Banner:</strong> Banner promosi</li>
                <li><i class="bx bx-check text-success me-2"></i>Upload gambar</li>
                <li><i class="bx bx-check text-success me-2"></i>Set link/URL</li>
                <li><i class="bx bx-check text-success me-2"></i>Atur urutan tampil</li>
              </ul>
              <p class="mb-0"><strong>Cara Menggunakan:</strong> Klik "Media & Banner" → "Carousel" atau "Banner" → Tambah → Upload gambar → Simpan.</p>
            </div>
          </div>
          <div class="col-md-6">
            <div class="border-start border-info border-3 ps-3 mb-4">
              <h6 class="text-info">Navigasi & Menu</h6>
              <p class="text-muted mb-2">Kelola menu navigasi website.</p>
              <ul class="list-unstyled">
                <li><strong>Menu Utama:</strong> Header menu website</li>
                <li><strong>Halaman:</strong> Kelola halaman statis (About, Contact, dll)</li>
                <li><i class="bx bx-check text-success me-2"></i>Atur urutan menu</li>
                <li><i class="bx bx-check text-success me-2"></i>Set link menu</li>
              </ul>
              <p class="mb-0"><strong>Cara Menggunakan:</strong> Klik "Navigasi & Menu" → "Menu Utama" untuk atur menu, atau "Halaman" untuk kelola halaman.</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- User Management -->
    <div class="card mb-4" id="users">
      <div class="card-header bg-secondary text-white">
        <h5 class="mb-0"><i class="bx bx-user me-2"></i>Pengguna</h5>
      </div>
      <div class="card-body">
        <div class="row g-4">
          <div class="col-md-6">
            <div class="border-start border-info border-3 ps-3 mb-4">
              <h6 class="text-info">Pengguna</h6>
              <p class="text-muted mb-2">Kelola user account (admin, staff, customer).</p>
              <ul class="list-unstyled">
                <li><i class="bx bx-check text-success me-2"></i>Tambah user baru</li>
                <li><i class="bx bx-check text-success me-2"></i>Edit user</li>
                <li><i class="bx bx-check text-success me-2"></i>Set role & permissions</li>
                <li><i class="bx bx-check text-success me-2"></i>Verify/unverify user</li>
                <li><i class="bx bx-check text-success me-2"></i>Hapus user</li>
              </ul>
              <p class="mb-0"><strong>Cara Menggunakan:</strong> Klik "Pengguna" → "Tambah Baru" → Isi data → Set role → Simpan.</p>
            </div>
          </div>
          <div class="col-md-6">
            <div class="border-start border-success border-3 ps-3 mb-4">
              <h6 class="text-success">Mitra</h6>
              <p class="text-muted mb-2">Kelola akun mitra/partner.</p>
              <ul class="list-unstyled">
                <li><i class="bx bx-check text-success me-2"></i>Lihat daftar mitra</li>
                <li><i class="bx bx-check text-success me-2"></i>Verify/unverify mitra</li>
                <li><i class="bx bx-check text-success me-2"></i>Kelola status mitra</li>
              </ul>
              <p class="mb-0"><strong>Cara Menggunakan:</strong> Klik "Mitra" → Pilih mitra → Verify/Unverify sesuai kebutuhan.</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Settings -->
    <div class="card mb-4" id="settings">
      <div class="card-header bg-danger text-white">
        <h5 class="mb-0"><i class="bx bx-cog me-2"></i>Pengaturan</h5>
      </div>
      <div class="card-body">
        <div class="border-start border-danger border-3 ps-3">
          <h6 class="text-danger">Pengaturan Umum</h6>
          <p class="text-muted mb-2">Konfigurasi umum sistem dan website.</p>
          <ul class="list-unstyled">
            <li><i class="bx bx-check text-success me-2"></i>Nama website</li>
            <li><i class="bx bx-check text-success me-2"></i>Logo & favicon</li>
            <li><i class="bx bx-check text-success me-2"></i>Email settings</li>
            <li><i class="bx bx-check text-success me-2"></i>Social media links</li>
            <li><i class="bx bx-check text-success me-2"></i>Contact information</li>
            <li><i class="bx bx-check text-success me-2"></i>SEO settings</li>
          </ul>
          <p class="mb-0"><strong>Cara Menggunakan:</strong> Klik "Pengaturan Umum" → Edit field yang diinginkan → Simpan perubahan.</p>
        </div>
      </div>
    </div>

    <!-- Tips & Best Practices -->
    <div class="card mb-4" id="tips">
      <div class="card-header bg-gradient" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
        <h5 class="mb-0"><i class="bx bx-bulb me-2"></i>Tips & Best Practices</h5>
      </div>
      <div class="card-body">
        <div class="row g-4">
          <div class="col-md-6">
            <div class="alert alert-info">
              <h6><i class="bx bx-info-circle me-2"></i>Tips Umum</h6>
              <ul class="mb-0">
                <li>Gunakan search box untuk mencari menu dengan cepat</li>
                <li>Simpan draft sebelum publish konten penting</li>
                <li>Backup data secara berkala</li>
                <li>Gunakan filter untuk mencari data spesifik</li>
                <li>Export laporan untuk analisis offline</li>
              </ul>
            </div>
          </div>
          <div class="col-md-6">
            <div class="alert alert-warning">
              <h6><i class="bx bx-error-circle me-2"></i>Penting!</h6>
              <ul class="mb-0">
                <li>Pastikan shift POS dibuka sebelum transaksi</li>
                <li>Tutup shift di akhir hari untuk rekonsiliasi</li>
                <li>Verifikasi stok sebelum update produk</li>
                <li>Backup database sebelum update besar</li>
                <li>Test di staging sebelum deploy ke production</li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Keyboard Shortcuts -->
    <div class="card mb-4" id="shortcuts">
      <div class="card-header bg-dark text-white">
        <h5 class="mb-0"><i class="bx bx-keyboard me-2"></i>Keyboard Shortcuts</h5>
      </div>
      <div class="card-body">
        <div class="row g-4">
          <div class="col-md-6">
            <h6>Navigasi</h6>
            <table class="table table-sm">
              <tr>
                <td><kbd>Ctrl</kbd> + <kbd>K</kbd></td>
                <td>Search menu</td>
              </tr>
              <tr>
                <td><kbd>Esc</kbd></td>
                <td>Close modal/dialog</td>
              </tr>
            </table>
          </div>
          <div class="col-md-6">
            <h6>POS System</h6>
            <table class="table table-sm">
              <tr>
                <td><kbd>F1</kbd></td>
                <td>New transaction</td>
              </tr>
              <tr>
                <td><kbd>F2</kbd></td>
                <td>Search product</td>
              </tr>
              <tr>
                <td><kbd>F3</kbd></td>
                <td>Payment</td>
              </tr>
              <tr>
                <td><kbd>Enter</kbd></td>
                <td>Confirm/Submit</td>
              </tr>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Search functionality
  const searchInput = document.getElementById('searchHelp');
  const helpContent = document.getElementById('helpContent');
  
  if (searchInput) {
    searchInput.addEventListener('input', function(e) {
      const searchTerm = e.target.value.toLowerCase().trim();
      const cards = helpContent.querySelectorAll('.card');
      
      if (searchTerm === '') {
        cards.forEach(card => card.style.display = '');
        return;
      }
      
      cards.forEach(card => {
        const text = card.textContent.toLowerCase();
        if (text.includes(searchTerm)) {
          card.style.display = '';
          // Highlight search term
          card.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        } else {
          card.style.display = 'none';
        }
      });
    });
  }
  
  // Smooth scroll for anchor links
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
      e.preventDefault();
      const target = document.querySelector(this.getAttribute('href'));
      if (target) {
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    });
  });
});
</script>

<style>
.border-start {
  border-left-width: 4px !important;
}

.card-header h5 {
  font-weight: 600;
}

.list-unstyled li {
  margin-bottom: 0.5rem;
}

kbd {
  background-color: #f8f9fa;
  border: 1px solid #dee2e6;
  border-radius: 0.25rem;
  padding: 0.2rem 0.4rem;
  font-size: 0.875rem;
  font-weight: 600;
}

.table-sm td {
  padding: 0.5rem;
  vertical-align: middle;
}
</style>
@endsection
