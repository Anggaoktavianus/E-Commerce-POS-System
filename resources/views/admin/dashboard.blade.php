@extends('admin.layouts.app')

@section('title','Dashboard Admin')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Header Section -->
  <div class="row mb-4">
    <div class="col-12">
      <div class="card page-header-card">
        <div class="card-body p-4">
          <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div>
              <h4 class="mb-2 fw-bold">
                <i class="bx bx-home-alt me-2 text-primary"></i>Dashboard Admin
              </h4>
              <p class="text-muted mb-0">
                <i class="bx bx-time me-1"></i>
                {{ now()->format('l, d F Y') }} - Selamat datang di panel administrasi
              </p>
            </div>
            <div class="mt-2 mt-md-0">
              <a href="{{ route('admin.products.index') }}" class="btn btn-primary">
                <i class="bx bx-package me-1"></i>Kelola Produk
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Low Stock Alert -->
  @if($lowStockCount > 0 || $outOfStockProducts > 0)
  <div class="row mb-4">
    <div class="col-12">
      <div class="alert alert-modern alert-warning border-warning">
        <div class="d-flex align-items-start">
          <div class="flex-shrink-0">
            <i class="bx bx-error-circle fs-4"></i>
          </div>
          <div class="flex-grow-1 ms-3">
            <h5 class="alert-heading mb-2 text-dark">
              Peringatan Stok!
            </h5>
            <p class="mb-2 text-primary">
              <strong class="text-danger">{{ $lowStockCount }}</strong> produk dengan stok menipis (≤ 10) dan
              <strong class="text-danger">{{ $outOfStockProducts }}</strong> produk habis.
            </p>
            <hr>
            <div class="d-flex gap-2 flex-wrap">
              <a href="{{ route('admin.products.index') }}" class="btn btn-sm btn-warning">
                <i class="bx bx-package me-1"></i>Kelola Produk
              </a>
              <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="alert">
                <i class="bx bx-x me-1"></i>Tutup
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  @endif

  <!-- Quick Stats -->
  <div class="row g-4 mb-4">
    <div class="col-6 col-md-3">
      <div class="card stat-card bg-primary text-white">
        <div class="card-body p-4">
          <div class="d-flex justify-content-between align-items-start">
            <div class="flex-grow-1">
              <p class="stat-label mb-2">Total Produk</p>
              <h3 class="stat-value mb-0 text-dark">{{ number_format($stats['total_products'], 0, ',', '.') }}</h3>
              <small class="stat-change d-block mt-2">
                <i class="bx bx-package"></i> Produk aktif
              </small>
            </div>
            <div class="stat-icon">
              <i class="bx bx-package"></i>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-6 col-md-3">
      <div class="card stat-card bg-success text-white">
        <div class="card-body p-4">
          <div class="d-flex justify-content-between align-items-start">
            <div class="flex-grow-1">
              <p class="stat-label mb-2">Total Pesanan</p>
              <h3 class="stat-value mb-0 text-dark">{{ number_format($stats['total_orders'], 0, ',', '.') }}</h3>
              <small class="stat-change d-block mt-2">
                <i class="bx bx-shopping-bag"></i> Semua pesanan
              </small>
            </div>
            <div class="stat-icon">
              <i class="bx bx-shopping-bag"></i>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-6 col-md-3">
      <div class="card stat-card bg-info text-white">
        <div class="card-body p-4">
          <div class="d-flex justify-content-between align-items-start">
            <div class="flex-grow-1">
              <p class="stat-label mb-2">Total Users</p>
              <h3 class="stat-value mb-0 text-dark">{{ number_format($stats['total_users'], 0, ',', '.') }}</h3>
              <small class="stat-change d-block mt-2">
                <i class="bx bx-user"></i> Pengguna terdaftar
              </small>
            </div>
            <div class="stat-icon">
              <i class="bx bx-user"></i>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-6 col-md-3">
      <div class="card stat-card {{ ($lowStockCount > 0 || $outOfStockProducts > 0) ? 'bg-danger' : 'bg-warning' }} text-white">
        <div class="card-body p-4">
          <div class="d-flex justify-content-between align-items-start">
            <div class="flex-grow-1">
              <p class="stat-label mb-2">Stok Menipis</p>
              <h3 class="stat-value mb-0 text-dark">{{ $lowStockCount + $outOfStockProducts }}</h3>
              <small class="stat-change d-block mt-2">
                <i class="bx bx-error-circle"></i> {{ $outOfStockProducts }} habis
              </small>
            </div>
            <div class="stat-icon">
              <i class="bx bx-error-circle"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Revenue Card -->
  <div class="row mb-4">
    <div class="col-12">
      <div class="card payment-card text-white shadow-lg">
        <div class="card-body p-4">
          <div class="row align-items-center">
            <div class="col-md-8">
              <h6 class="mb-2 opacity-100 text-dark">
                <i class="bx bx-dollar-circle me-2 "></i>Total Revenue
              </h6>
              <h2 class="mb-2 fw-bold text-dark">IDR {{ number_format($stats['revenue'], 0, ',', '.') }}</h2>
              <p class="mb-0 opacity-75">
                <i class="bx bx-check-circle me-1"></i>Dari pesanan yang sudah dibayar
              </p>
            </div>
            <div class="col-md-4 text-center text-md-end">
              <i class="bx bx-dollar-circle" style="font-size: 5rem; opacity: 0.3;"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Main Content Row -->
  <div class="row g-4">
    <!-- Welcome Card -->
    <div class="col-lg-8 mb-4">
      <div class="card welcome-card shadow-sm border-0">
        <div class="card-body p-4">
          <div class="row align-items-center">
            <div class="col-md-7">
              <h5 class="card-title mb-3 fw-bold">Selamat datang di Dashboard Admin 🎉</h5>
              <p class="mb-4 opacity-90">
                Dari sini Anda dapat mengelola <strong>produk, kategori, halaman, banner, menu, testimoni, dan mitra</strong>,
                serta memantau performa penjualan secara real-time.
              </p>
              <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('admin.products.index') }}" class="btn btn-light btn-sm">
                  <i class="bx bx-package me-1"></i>Kelola Produk
                </a>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-light btn-sm">
                  <i class="bx bx-shopping-bag me-1"></i>Kelola Pesanan
                </a>
                <a href="{{ route('admin.stock_movements.index') }}" class="btn btn-light btn-sm">
                  <i class="bx bx-bar-chart me-1"></i>Riwayat Stok
                </a>
              </div>
            </div>
            <div class="col-md-5 text-center mt-3 mt-md-0">
              <img src="{{ asset('sneat/assets/img/illustrations/man-with-laptop-light.png') }}"
                   height="160"
                   alt="Dashboard Illustration"
                   class="img-fluid"
                   style="filter: drop-shadow(0 4px 8px rgba(0,0,0,0.1));">
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Recent Orders -->
    <div class="col-lg-4 mb-4">
      <div class="card shadow-sm border-0 h-100">
        <div class="card-header bg-white border-bottom pb-3">
          <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0 fw-bold">
              <i class="bx bx-time-five me-2 text-primary"></i>Pesanan Terbaru
            </h5>
            <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-link text-primary p-0">
              <i class="bx bx-right-arrow-alt"></i>
            </a>
          </div>
        </div>
        <div class="card-body">
          @if($recentOrders->count() > 0)
            <div class="list-group list-group-flush">
              @foreach($recentOrders as $order)
              <div class="order-item">
                <div class="d-flex justify-content-between align-items-start mb-2">
                  <div class="flex-grow-1">
                    <h6 class="mb-1 fw-bold">{{ $order->order_number }}</h6>
                    <small class="text-muted d-block">
                      <i class="bx bx-user me-1"></i>{{ $order->user?->name ?? 'Guest' }}
                    </small>
                  </div>
                  <span class="badge bg-{{ $order->status_color }} ms-2">
                    {{ $order->formatted_status }}
                  </span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                  <small class="text-muted">
                    <i class="bx bx-calendar me-1"></i>{{ $order->created_at->format('d M Y') }}
                  </small>
                  <strong class="text-primary">{{ $order->formatted_total }}</strong>
                </div>
              </div>
              @endforeach
            </div>
            <div class="text-center mt-3">
              <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-primary">
                <i class="bx bx-list-ul me-1"></i>Lihat Semua Pesanan
              </a>
            </div>
          @else
            <div class="empty-state">
              <i class="bx bx-shopping-bag text-muted"></i>
              <h6 class="text-muted mt-3">Belum ada pesanan</h6>
              <p class="text-muted small mb-0">Pesanan akan muncul di sini</p>
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>

  <!-- Low Stock Products Alert -->
  @if($lowStockProducts->count() > 0)
  <div class="row mt-4">
    <div class="col-12">
      <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center flex-wrap">
          <h5 class="card-title mb-0 fw-bold">
            <i class="bx bx-error-circle text-warning me-2"></i>Produk dengan Stok Menipis
          </h5>
          <a href="{{ route('admin.products.index') }}" class="btn btn-sm btn-primary mt-2 mt-md-0">
            <i class="bx bx-package me-1"></i>Kelola Produk
          </a>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover table-modern mb-0">
              <thead>
                <tr>
                  <th class="ps-4 text-white" >Produk</th>
                  <th class="text-white">Stok Tersedia</th>
                  <th class="text-white">Status</th>
                  <th class="text-end pe-4 text-white">Aksi</th>
                </tr>
              </thead>
              <tbody>
                @foreach($lowStockProducts as $product)
                <tr class="{{ ($product->stock_qty ?? 0) <= 0 ? 'table-danger' : 'table-warning' }}">
                  <td class="ps-4">
                    <div>
                      <strong>{{ $product->name }}</strong>
                      @if($product->sku)
                        <br><small class="text-muted">SKU: {{ $product->sku }}</small>
                      @endif
                    </div>
                  </td>
                  <td>
                    <span class="badge {{ ($product->stock_qty ?? 0) <= 0 ? 'bg-danger' : 'bg-warning text-dark' }} fs-6">
                      {{ number_format($product->stock_qty ?? 0, 0, ',', '.') }} {{ $product->unit ?? 'pcs' }}
                    </span>
                  </td>
                  <td>
                    @if(($product->stock_qty ?? 0) <= 0)
                      <span class="badge bg-danger">
                        <i class="bx bx-x-circle me-1"></i>Habis
                      </span>
                    @else
                      <span class="badge bg-warning text-dark">
                        <i class="bx bx-error-circle me-1"></i>Terbatas
                      </span>
                    @endif
                  </td>
                  <td class="text-end pe-4">
                    <a href="{{ route('admin.products.edit', encode_id($product->id)) }}"
                       class="btn btn-sm btn-outline-primary">
                      <i class="bx bx-edit me-1"></i>Edit
                    </a>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
          @if($lowStockCount > 10)
          <div class="card-footer bg-white text-center">
            <p class="text-muted mb-2">Menampilkan 10 dari {{ $lowStockCount }} produk dengan stok menipis</p>
            <a href="{{ route('admin.products.index') }}" class="btn btn-sm btn-primary">
              <i class="bx bx-list-ul me-1"></i>Lihat Semua Produk
            </a>
          </div>
          @endif
        </div>
      </div>
    </div>
  </div>
  @endif

  <!-- Payment Methods Info -->
  <div class="row mt-4">
    <div class="col-12">
      <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-bottom">
          <h5 class="card-title mb-0 fw-bold">
            <i class="bx bx-credit-card me-2 text-primary"></i>Midtrans Payment Gateway
          </h5>
        </div>
        <div class="card-body">
          <div class="row align-items-center">
            <div class="col-md-8">
              <h6 class="mb-3 fw-bold">Sistem Pembayaran Terpercaya</h6>
              <p class="text-muted mb-3">
                Midtrans telah diintegrasikan untuk menyediakan berbagai metode pembayaran yang aman dan mudah bagi pelanggan Anda.
              </p>
              <div class="d-flex flex-wrap gap-2">
                <span class="badge bg-primary p-2">
                  <i class="bx bx-credit-card me-1"></i>Credit Card
                </span>
                <span class="badge bg-success p-2">
                  <i class="bx bx-mobile me-1"></i>GoPay
                </span>
                <span class="badge bg-warning text-dark p-2">
                  <i class="bx bx-mobile me-1"></i>ShopeePay
                </span>
                <span class="badge bg-info p-2">
                  <i class="bx bx-qr-scan me-1"></i>QRIS
                </span>
                <span class="badge bg-secondary p-2">
                  <i class="bx bx-building me-1"></i>BCA VA
                </span>
                <span class="badge bg-secondary p-2">
                  <i class="bx bx-building me-1"></i>BNI VA
                </span>
                <span class="badge bg-secondary p-2">
                  <i class="bx bx-building me-1"></i>BRI VA
                </span>
                <span class="badge bg-secondary p-2">
                  <i class="bx bx-building me-1"></i>Mandiri
                </span>
              </div>
            </div>
            <div class="col-md-4 text-center mt-3 mt-md-0">
              <img src="https://midtrans.com/wp-content/uploads/2021/09/midtrans-logo.png"
                   alt="Midtrans"
                   class="img-fluid"
                   style="max-width: 200px; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));">
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
