@extends('admin.layouts.app')

@section('title','Dashboard Admin')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Header Section -->
  <div class="card mb-4">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h4 class="mb-1">
            <i class="bx bx-home-alt me-2"></i>Dashboard Admin
          </h4>
          <p class="text-muted mb-0">Selamat datang di panel administrasi {{ config('app.name') }}</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Quick Stats -->
  <div class="row mb-4">
    <div class="col-md-3">
      <div class="card bg-primary text-white">
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <div>
              <h6 class="card-title">Total Produk</h6>
              <h3 class="mb-0">{{ App\Models\Product::count() }}</h3>
            </div>
            <i class="bx bx-package bx-lg"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card bg-success text-white">
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <div>
              <h6 class="card-title">Total Pesanan</h6>
              <h3 class="mb-0">{{ App\Models\Order::count() }}</h3>
            </div>
            <i class="bx bx-shopping-bag bx-lg"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card bg-info text-white">
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <div>
              <h6 class="card-title">Total Users</h6>
              <h3 class="mb-0">{{ App\Models\User::count() }}</h3>
            </div>
            <i class="bx bx-user bx-lg"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card bg-warning text-white">
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <div>
              <h6 class="card-title">Revenue</h6>
              <h3 class="mb-0">IDR {{ number_format(App\Models\Order::where('status', 'paid')->sum('total_amount'), 0, ',', '.') }}</h3>
            </div>
            <i class="bx bx-dollar-circle bx-lg"></i>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-lg-8 mb-4 order-0">
      <div class="card">
        <div class="d-flex align-items-end row">
          <div class="col-sm-7">
            <div class="card-body">
              <h5 class="card-title text-primary">Selamat datang di Dashboard Admin 🎉</h5>
              <p class="mb-4">Dari sini Anda dapat mengelola <span class="fw-bold">produk, kategori, halaman, banner, menu, testimoni, dan mitra</span>, serta memantau performa penjualan.</p>
              <a href="{{ route('admin.products.index') }}" class="btn btn-sm btn-outline-primary">Kelola Produk</a>
              <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-success ms-2">Kelola Pesanan</a>
            </div>
          </div>
          <div class="col-sm-5 text-center text-sm-left">
            <div class="card-body pb-0 px-0 px-md-4">
              <img src="{{ asset('sneat/assets/img/illustrations/man-with-laptop-light.png') }}" height="140" alt="View Badge User" />
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Recent Orders -->
    <div class="col-lg-4 mb-4 order-1">
      <div class="card h-100">
        <div class="card-header">
          <h5 class="card-title mb-0">Pesanan Terbaru</h5>
        </div>
        <div class="card-body">
          @if(App\Models\Order::count() > 0)
            @php
              $recentOrders = App\Models\Order::with('user')->latest()->take(3)->get();
            @endphp
            @foreach($recentOrders as $order)
            <div class="d-flex justify-content-between align-items-center mb-3">
              <div>
                <h6 class="mb-0">{{ $order->order_number }}</h6>
                <small class="text-muted">{{ $order->user?->name ?? 'Guest' }}</small>
              </div>
              <div class="text-end">
                <span class="badge bg-{{ $order->status_color }} mb-1">{{ $order->formatted_status }}</span><br>
                <small class="text-muted">{{ $order->formatted_total }}</small>
              </div>
            </div>
            @endforeach
            <div class="text-center mt-3">
              <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
            </div>
          @else
            <div class="text-center py-4">
              <i class="bx bx-shopping-bag text-muted" style="font-size: 2rem;"></i>
              <h6 class="text-muted mt-3">Belum ada pesanan</h6>
              <p class="text-muted small">Pesanan akan muncul di sini</p>
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>

  <!-- Payment Methods Info -->
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h5 class="card-title mb-0">
            <i class="bx bx-credit-card me-2"></i>Midtrans Payment Gateway
          </h5>
        </div>
        <div class="card-body">
          <div class="row align-items-center">
            <div class="col-md-8">
              <h6>Sistem Pembayaran Terpercaya</h6>
              <p class="text-muted mb-3">Midtrans telah diintegrasikan untuk menyediakan berbagai metode pembayaran yang aman dan mudah bagi pelanggan Anda.</p>
              <div class="d-flex flex-wrap gap-2">
                <span class="badge bg-primary">Credit Card</span>
                <span class="badge bg-success">GoPay</span>
                <span class="badge bg-warning">ShopeePay</span>
                <span class="badge bg-info">QRIS</span>
                <span class="badge bg-secondary">BCA VA</span>
                <span class="badge bg-secondary">BNI VA</span>
                <span class="badge bg-secondary">BRI VA</span>
                <span class="badge bg-secondary">Mandiri</span>
              </div>
            </div>
            <div class="col-md-4 text-center">
              <img src="https://midtrans.com/wp-content/uploads/2021/09/midtrans-logo.png" alt="Midtrans" style="max-width: 200px;">
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection