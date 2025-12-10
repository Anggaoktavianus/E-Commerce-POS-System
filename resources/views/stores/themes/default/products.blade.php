@extends('layouts.app')

@section('title', $store->name . ' - Produk')

@section('content')
<div class="container-fluid">
    <!-- Store Header -->
    <div class="bg-primary text-white py-3 mb-4">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-3">
                    @if($store->logo_url)
                        <img src="{{ asset($store->logo_url) }}" alt="{{ $store->name }}" class="img-fluid" style="max-height: 40px;">
                    @else
                        <h4 class="mb-0">{{ $store->name }}</h4>
                    @endif
                </div>
                <div class="col-md-9 text-end">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-end mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('home') }}" class="text-white">Beranda</a>
                            </li>
                            <li class="breadcrumb-item active text-white">Produk</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Search and Filter -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form method="GET" action="{{ route('shop') }}">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="input-group">
                                        <input type="text" name="search" class="form-control" placeholder="Cari produk..." value="{{ request('search') }}">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bx bx-search"></i> Cari
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <select name="category" class="form-select" onchange="this.form.submit()">
                                        <option value="">Semua Kategori</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="row">
            <div class="col-12">
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
                                        @if($product->category)
                                            <small class="text-muted">{{ $product->category->name }}</small>
                                        @endif
                                        <p class="card-text small text-muted">{{ Str::limit($product->description, 80) }}</p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="fw-bold text-primary">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                                            @if($product->stock > 0)
                                                <span class="badge bg-success">Stok: {{ $product->stock }}</span>
                                            @else
                                                <span class="badge bg-danger">Habis</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <div class="btn-group w-100" role="group">
                                            <a href="{{ route('shop.detail', $product->slug) }}" class="btn btn-primary btn-sm">
                                                <i class="bx bx-search"></i> Detail
                                            </a>
                                            @if($product->stock > 0)
                                                <button class="btn btn-success btn-sm" onclick="addToCart({{ $product->id }})">
                                                    <i class="bx bx-cart"></i> +Keranjang
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $products->links() }}
                    </div>
                @else
                    <div class="alert alert-warning text-center">
                        <i class="bx bx-package" style="font-size: 3rem;"></i>
                        <h5 class="mt-3">Tidak Ada Produk</h5>
                        <p class="mb-0">Belum ada produk yang tersedia untuk toko ini.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
function addToCart(productId) {
    // Implement cart functionality
    alert('Produk ditambahkan ke keranjang! (Product ID: ' + productId + ')');
}
</script>
@endsection
