@extends('admin.layouts.app')

@section('title', 'Dashboard - ' . $store->name)

@section('content')
<div class="container-fluid">
    <!-- Store Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-3">
                            @if($store->logo_url)
                                <img src="{{ asset($store->logo_url) }}" alt="{{ $store->name }}" class="img-fluid" style="max-height: 60px;">
                            @else
                                <h3>{{ $store->name }}</h3>
                            @endif
                        </div>
                        <div class="col-md-9">
                            <h4 class="mb-1">Dashboard Admin</h4>
                            <p class="mb-0">{{ $store->name }} - Panel Manajemen Toko</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $stats['total_products'] }}</h4>
                            <p class="mb-0">Total Produk</p>
                        </div>
                        <div>
                            <i class="bx bx-package" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $stats['active_products'] }}</h4>
                            <p class="mb-0">Produk Aktif</p>
                        </div>
                        <div>
                            <i class="bx bx-check-circle" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $stats['total_orders'] }}</h4>
                            <p class="mb-0">Total Pesanan</p>
                        </div>
                        <div>
                            <i class="bx bx-shopping-bag" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $stats['pending_orders'] }}</h4>
                            <p class="mb-0">Pesanan Pending</p>
                        </div>
                        <div>
                            <i class="bx bx-time" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders & Top Products -->
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">📦 Pesanan Terbaru</h5>
                </div>
                <div class="card-body">
                    @if($recentOrders->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Pelanggan</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Tanggal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentOrders as $order)
                                        <tr>
                                            <td>{{ $order->id }}</td>
                                            <td>{{ $order->customer->name ?? 'Guest' }}</td>
                                            <td>Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                                            <td>
                                                <span class="badge bg-{{ $order->status == 'completed' ? 'success' : ($order->status == 'pending' ? 'warning' : 'danger') }}">
                                                    {{ $order->status }}
                                                </span>
                                            </td>
                                            <td>{{ $order->created_at->format('d/m/Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="text-end mt-3">
                            <a href="{{ route('store.admin.orders', $store->code) }}" class="btn btn-primary btn-sm">
                                Lihat Semua
                            </a>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bx bx-shopping-bag text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-2">Belum ada pesanan</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">🏆 Produk Terlaris</h5>
                </div>
                <div class="card-body">
                    @if($topProducts->count() > 0)
                        @foreach($topProducts as $product)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div>
                                    <h6 class="mb-0">{{ $product->name }}</h6>
                                    <small class="text-muted">{{ $product->order_items_count }} terjual</small>
                                </div>
                                <span class="badge bg-primary">{{ $product->order_items_count }}</span>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <i class="bx bx-bar-chart text-muted" style="font-size: 2rem;"></i>
                            <p class="text-muted mt-2">Belum ada data penjualan</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">⚡ Aksi Cepat</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('store.admin.products', $store->code) }}" class="btn btn-outline-primary w-100">
                                <i class="bx bx-package me-2"></i>Kelola Produk
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('store.admin.categories', $store->code) }}" class="btn btn-outline-info w-100">
                                <i class="bx bx-category me-2"></i>Kelola Kategori
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('store.admin.orders', $store->code) }}" class="btn btn-outline-warning w-100">
                                <i class="bx bx-shopping-bag me-2"></i>Kelola Pesanan
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('store.admin.settings', $store->code) }}" class="btn btn-outline-success w-100">
                                <i class="bx bx-cog me-2"></i>Pengaturan
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
