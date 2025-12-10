@extends('layouts.app')

@section('title', 'Keranjang Belanja - ' . config('app.name'))

@section('meta_description', 'Lihat dan kelola keranjang belanja Anda di ' . config('app.name') . '. Belanja produk berkualitas dengan mudah dan aman.')

@section('meta_keywords', 'keranjang belanja, shopping cart, belanja, checkout, ' . config('app.name') . ', toko online')

@section('og_image', asset('storage/defaults/og-cart.jpg'))

@section('content')
    <!-- Single Page Header start -->
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">Keranjang</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Beranda</a></li>
            <li class="breadcrumb-item active text-white">Keranjang</li>
        </ol>
    </div>
    <!-- Single Page Header End -->

    <!-- Cart Page Start -->
    <div class="container-fluid py-5">
        <div class="container py-5">
            <div class="row">
                <!-- Cart Items -->
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">🛒 Keranjang Belanja</h5>
                        </div>
                        <div class="card-body">
                            @forelse(($cart ?? []) as $item)
                            <div class="row mb-4 pb-3 border-bottom">
                                <div class="col-md-2">
                                    <img src="{{ $item['image'] ? asset('storage/' . $item['image']) : asset('fruitables/img/vegetable-item-3.png') }}" 
                                         class="img-fluid rounded" alt="{{ $item['name'] }}">
                                </div>
                                <div class="col-md-6">
                                    <h6 class="mb-1">
                                        {{ $item['name'] }}
                                        @if(isset($item['product']) && $item['product']->shelf_life_days <= 7)
                                            <span class="badge bg-warning ms-2">🥬 Produk Segar</span>
                                        @endif
                                    </h6>
                                    <p class="text-muted mb-2">{{ $item['description'] ?? '' }}</p>
                                    <p class="mb-0 fw-bold">{{ $item['formatted_price'] ?? 'IDR ' . number_format($item['price'], 0, ',', '.') }}</p>
                                </div>
                                <div class="col-md-4">
                                    <div class="d-flex align-items-center mb-2">
                                        <form method="POST" action="{{ route('cart.update') }}" class="d-flex">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $item['id'] }}">
                                            <input type="number" name="qty" value="{{ $item['qty'] }}" min="1" max="99" class="form-control form-control-sm me-2" style="width: 70px;">
                                            <button type="submit" class="btn btn-sm btn-outline-primary">
                                                <i class="bx bx-refresh"></i> Update
                                            </button>
                                        </form>
                                    </div>
                                    <form method="POST" action="{{ route('cart.remove') }}" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $item['id'] }}">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bx bx-trash"></i> Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-5">
                                <i class="bx bx-cart-alt display-1 text-muted"></i>
                                <h4 class="mt-3">Keranjang Anda kosong</h4>
                                <p class="text-muted">Tambahkan beberapa produk untuk memulai!</p>
                                <a href="{{ url('/') }}" class="btn btn-primary">
                                    <i class="bx bx-shopping-bag me-2"></i>Lanjut Belanja
                                </a>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Cart Summary -->
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">📋 Ringkasan Keranjang</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal:</span>
                                <span class="fw-bold">IDR {{ number_format($subtotal ?? 0, 0, ',', '.') }}</span>
                            </div>
                            @if(($discount ?? 0) > 0)
                            <div class="d-flex justify-content-between mb-2">
                                <span>Diskon:</span>
                                <span class="text-danger fw-bold">- IDR {{ number_format($discount, 0, ',', '.') }}</span>
                            </div>
                            @endif
                            <div class="d-flex justify-content-between mb-2">
                                <span>Pengiriman:</span>
                                <span class="text-primary fw-bold">Dihitung di checkout</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between">
                                <h5>Total:</h5>
                                <h5 class="text-success">IDR {{ number_format($total ?? 0, 0, ',', '.') }}</h5>
                            </div>
                            
                            @if(count($cart ?? []) > 0)
                            <!-- Fresh Product Warnings -->
                            <div id="fresh-warnings" class="mt-3" style="display:none;">
                                <!-- Will be populated by JavaScript -->
                            </div>
                            
                            <div class="d-grid gap-2 mt-3">
                                <a href="{{ route('checkout') }}" class="btn btn-primary btn-lg">
                                    <i class="bx bx-lock-alt me-2"></i>Lanjut ke Checkout
                                </a>
                                <a href="{{ url('/') }}" class="btn btn-outline-secondary">
                                    <i class="bx bx-arrow-back me-2"></i>Lanjut Belanja
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Coupon Section -->
                    @if(count($cart ?? []) > 0)
                    <div class="card mt-3">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0">🎫 Kode Kupon</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('cart.coupon') }}" class="d-flex">
                                @csrf
                                <input type="text" name="coupon_code" class="form-control me-2" placeholder="Masukkan kode kupon" value="{{ $coupon->code ?? '' }}">
                                <button type="submit" class="btn btn-warning">
                                    {{ $coupon ? 'Hapus' : 'Gunakan' }}
                                </button>
                            </form>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <!-- Cart Page End -->

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Show fresh product warnings
    showFreshProductWarnings();
    
    function showFreshProductWarnings() {
        const freshProducts = {{ collect($cart ?? [])->filter(function($item) { return isset($item['product']) && $item['product']->shelf_life_days <= 7; })->count() }};
        
        if (freshProducts > 0) {
            const warningsHtml = `
                <div class="alert alert-warning alert-sm">
                    <i class="bx bx-info-circle me-2"></i>
                    <strong>🥬 Perhatian Produk Segar:</strong><br>
                    Anda memiliki ${freshProducts} produk segar di keranjang. 
                    Pengiriman instan direkomendasikan untuk kualitas terbaik.
                </div>
            `;
            $('#fresh-warnings').html(warningsHtml).show();
        }
    }
});
</script>
@endpush
