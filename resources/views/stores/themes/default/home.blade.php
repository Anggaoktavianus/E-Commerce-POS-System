@extends('layouts.app')

@section('title', $store->name . ' - Beranda')

@section('content')
<div class="container-fluid">
    <!-- Store Header -->
    <div class="bg-primary text-white py-4 mb-4">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-3">
                    @if($store->logo_url)
                        <img src="{{ asset($store->logo_url) }}" alt="{{ $store->name }}" class="img-fluid" style="max-height: 60px;">
                    @else
                        <h3>{{ $store->name }}</h3>
                    @endif
                </div>
                <div class="col-md-9 text-end">
                    <h4 class="mb-0">{{ $store->name }}</h4>
                    <p class="mb-0">{{ $store->address }}, {{ $store->city }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Categories Section -->
        <div class="row mb-4">
            <div class="col-12">
                <h5 class="mb-3">📦 Kategori Produk</h5>
                <div class="row">
                    @if($categories->count() > 0)
                        @foreach($categories as $category)
                            <div class="col-md-3 col-sm-6 mb-3">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <h6>{{ $category->name }}</h6>
                                        <small class="text-muted">{{ $category->products_count ?? 0 }} produk</small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="col-12">
                            <div class="alert alert-info">
                                <i class="bx bx-info-circle"></i> Belum ada kategori tersedia
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Products Section -->
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">🛍️ Produk Terbaru</h5>
                    <a href="{{ route('shop') }}" class="btn btn-outline-primary btn-sm">
                        Lihat Semua
                    </a>
                </div>
                
                @if($products->count() > 0)
                    <div class="row">
                        @foreach($products as $product)
                            <div class="col-md-3 col-sm-6 mb-4">
                                <div class="card h-100">
                                    @if($product->image_url)
                                        <img src="{{ asset($product->image_url) }}" class="card-img-top" alt="{{ $product->name }}" style="height: 200px; object-fit: cover;">
                                    @else
                                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                            <i class="bx bx-image text-muted" style="font-size: 3rem;"></i>
                                        </div>
                                    @endif
                                    <div class="card-body">
                                        <h6 class="card-title">{{ $product->name }}</h6>
                                        <p class="card-text small text-muted">{{ Str::limit($product->description, 50) }}</p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="fw-bold text-primary">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                                            @if($product->stock > 0)
                                                <span class="badge bg-success">Tersedia</span>
                                            @else
                                                <span class="badge bg-danger">Habis</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <a href="{{ route('shop.detail', $product->slug) }}" class="btn btn-primary btn-sm w-100">
                                            <i class="bx bx-search"></i> Detail
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="alert alert-warning">
                        <i class="bx bx-package"></i> Belum ada produk tersedia
                    </div>
                @endif
            </div>
        </div>

        <!-- Store Info Section -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">🏪 Informasi Toko</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Nama:</strong> {{ $store->name }}</p>
                                <p><strong>Pemilik:</strong> {{ $store->owner_name }}</p>
                                <p><strong>Email:</strong> {{ $store->email }}</p>
                                <p><strong>Telepon:</strong> {{ $store->phone }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Alamat:</strong> {{ $store->address }}</p>
                                <p><strong>Kota:</strong> {{ $store->city }}</p>
                                <p><strong>Provinsi:</strong> {{ $store->province }}</p>
                                <p><strong>Kode Pos:</strong> {{ $store->postal_code }}</p>
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <a href="{{ route('pages.show', 'about') }}" class="btn btn-outline-primary me-2">
                                <i class="bx bx-info-circle"></i> Tentang Kami
                            </a>
                            <a href="{{ route('contact') }}" class="btn btn-outline-primary">
                                <i class="bx bx-phone"></i> Kontak
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
