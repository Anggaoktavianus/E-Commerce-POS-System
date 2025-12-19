@extends('layouts.app')

@section('title', 'Keranjang Belanja - ' . config('app.name'))

@section('meta_description', 'Lihat dan kelola keranjang belanja Anda di ' . config('app.name') . '. Belanja produk berkualitas dengan mudah dan aman.')

@section('meta_keywords', 'keranjang belanja, shopping cart, belanja, checkout, ' . config('app.name') . ', toko online')

@section('og_image', asset('storage/defaults/og-cart.jpg'))

@push('styles')
<style>
    .cart-page {
        background: linear-gradient(135deg, #f0f9f4 0%, #d4edda 100%);
        min-height: 100vh;
        padding: 2rem 0;
    }
    
    .cart-header {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: white;
        padding: 1.5rem;
        border-radius: 15px 15px 0 0;
        box-shadow: 0 4px 15px rgba(40, 167, 69, 0.2);
    }
    
    .cart-item-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        border: 1px solid #e9ecef;
    }
    
    .cart-item-card:hover {
        box-shadow: 0 4px 20px rgba(0,0,0,0.12);
        transform: translateY(-2px);
    }
    
    .product-image {
        width: 100%;
        height: 120px;
        object-fit: cover;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .summary-card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        overflow: hidden;
        position: sticky;
        top: 20px;
    }
    
    .summary-header {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: white;
        padding: 1.25rem;
    }
    
    
    .fresh-product-alert {
        background: linear-gradient(135deg, #ffeaa7 0%, #fdcb6e 100%);
        border: none;
        border-radius: 12px;
        padding: 1rem;
        margin-top: 1rem;
    }
    
    .btn-update {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        border: none;
        color: white;
        transition: all 0.3s ease;
    }
    
    .btn-update:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(40, 167, 69, 0.4);
        color: white;
    }
    
    .btn-delete {
        background: linear-gradient(135deg, #ee0979 0%, #ff6a00 100%);
        border: none;
        color: white;
        transition: all 0.3s ease;
    }
    
    .btn-delete:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(238, 9, 121, 0.4);
        color: white;
    }
    
    .stock-badge {
        font-size: 0.75rem;
        padding: 0.35rem 0.65rem;
        border-radius: 20px;
    }
    
    .empty-cart {
        text-align: center;
        padding: 4rem 2rem;
        background: white;
        border-radius: 15px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    }
    
    .empty-cart-icon {
        font-size: 5rem;
        color: #dee2e6;
        margin-bottom: 1.5rem;
    }
    
    .quantity-input {
        width: 80px;
        text-align: center;
        border: 2px solid #e9ecef;
        border-radius: 8px;
        padding: 0.5rem;
        font-weight: 600;
    }
    
    .quantity-input:focus {
        border-color: #28a745;
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
    }
    
    @media (max-width: 768px) {
        .cart-item-card {
            padding: 1rem;
        }
        
        .product-image {
            height: 100px;
        }
        
        .summary-card {
            position: relative;
            top: 0;
            margin-top: 2rem;
        }
    }
    
    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        display: none;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }
    
    .loading-spinner {
        background: white;
        padding: 2rem;
        border-radius: 15px;
        text-align: center;
    }
</style>
@endpush

@section('content')
    @include('partials.modern-page-header', [
        'pageTitle' => $siteSettings['cart_page_title'] ?? 'Keranjang Belanja',
        'breadcrumbItems' => [
            ['label' => 'Beranda', 'url' => url('/')],
            ['label' => 'Toko', 'url' => route('shop')],
            ['label' => 'Keranjang', 'url' => null]
        ]
    ])

<div class="cart-page" style="background: linear-gradient(180deg, #f8f9fa 0%, #ffffff 100%);">
    <div class="container py-5">
        <div class="row">
            <!-- Cart Items -->
            <div class="col-lg-8 mb-4">
                <div class="card border-0 shadow-lg">
                    <div class="cart-header">
                        <h4 class="mb-0 text-white">
                            <i class="bx bx-cart-alt me-2"></i>Keranjang Belanja
                        </h4>
                    </div>
                    <div class="card-body p-4">
                        @forelse(($cart ?? []) as $item)
                        <div class="cart-item-card" data-item-id="{{ $item['id'] }}">
                            <div class="row align-items-center">
                                <div class="col-md-2 mb-3 mb-md-0">
                                    <img src="{{ $item['image'] ? asset('storage/' . $item['image']) : asset('fruitables/img/vegetable-item-3.png') }}" 
                                         class="product-image" alt="{{ $item['name'] }}">
                                </div>
                                <div class="col-md-5">
                                    <h6 class="mb-2 fw-bold">{{ $item['name'] }}</h6>
                                    @if(isset($item['product']) && $item['product']->shelf_life_days <= 7)
                                        <span class="badge bg-warning text-dark mb-2">
                                            <i class="bx bx-time me-1"></i>Masa Simpan Terbatas
                                        </span>
                                    @endif
                                    <p class="text-muted small mb-2">{{ $item['description'] ?? '' }}</p>
                                    <p class="mb-2 fw-bold text-success">
                                        {{ $item['formatted_price'] ?? 'IDR ' . number_format($item['price'], 0, ',', '.') }}
                                    </p>
                                    @if(isset($item['product']))
                                        @php
                                            $stockQty = $item['product']->stock_qty ?? 0;
                                            $isLowStock = $stockQty > 0 && $stockQty <= 10;
                                            $isOutOfStock = $stockQty <= 0;
                                        @endphp
                                        <div class="d-flex align-items-center gap-2">
                                            <small class="text-muted">Stok tersedia:</small>
                                            <strong class="{{ $isOutOfStock ? 'text-danger' : ($isLowStock ? 'text-warning' : 'text-success') }}">
                                                {{ $stockQty }} {{ $item['product']->unit ?? 'pcs' }}
                                            </strong>
                                            @if($isLowStock)
                                                <span class="stock-badge bg-warning text-dark">Terbatas</span>
                                            @elseif($isOutOfStock)
                                                <span class="stock-badge bg-danger text-white">Habis</span>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                                <div class="col-md-5">
                                    <form method="POST" action="{{ route('cart.update') }}" class="cart-update-form">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $item['id'] }}">
                                        <div class="d-flex align-items-center gap-2 mb-3">
                                            <label class="small text-muted mb-0">Jumlah:</label>
                                            @php
                                                $maxQty = isset($item['product']) ? ($item['product']->stock_qty ?? 0) : 99;
                                            @endphp
                                            <input type="number" name="qty" value="{{ $item['qty'] }}" 
                                                   min="1" max="{{ $maxQty }}" 
                                                   class="quantity-input form-control form-control-sm" 
                                                   {{ $maxQty <= 0 ? 'disabled' : '' }}
                                                   data-product-id="{{ $item['id'] }}">
                                            <button type="submit" class="btn btn-sm btn-update d-none" {{ $maxQty <= 0 ? 'disabled' : '' }}>
                                                <i class="bx bx-refresh"></i> Perbarui
                                            </button>
                                            <small class="text-muted d-block mt-1">
                                                <i class="bx bx-info-circle me-1"></i>Update otomatis saat diubah
                                            </small>
                                        </div>
                                    </form>
                                    <form method="POST" action="{{ route('cart.remove') }}" class="cart-remove-form">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $item['id'] }}">
                                        <button type="submit" class="btn btn-sm btn-delete w-100">
                                            <i class="bx bx-trash me-1"></i> Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="empty-cart">
                            <div class="empty-cart-icon">
                                <i class="bx bx-cart-alt"></i>
                            </div>
                            <h4 class="mb-3">Keranjang Anda kosong</h4>
                            <p class="text-muted mb-4">Tambahkan beberapa produk untuk memulai belanja!</p>
                            <a href="{{ url('/') }}" class="btn btn-success btn-lg">
                                <i class="bx bx-shopping-bag me-2"></i>Lanjut Belanja
                            </a>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Cart Summary -->
            <div class="col-lg-4">
                <div class="summary-card">
                    <div class="summary-header">
                        <h5 class="mb-0 text-white">
                            <i class="bx bx-receipt me-2"></i>Ringkasan Keranjang
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Subtotal:</span>
                                <span class="fw-bold">IDR {{ number_format($subtotal ?? 0, 0, ',', '.') }}</span>
                            </div>
                            @if(($discount ?? 0) > 0)
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Diskon:</span>
                                <span class="text-danger fw-bold">- IDR {{ number_format($discount, 0, ',', '.') }}</span>
                            </div>
                            @endif
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Pengiriman:</span>
                                <span class="text-success fw-bold">Dihitung di checkout</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between">
                                <h5 class="mb-0">Total:</h5>
                                <h5 class="mb-0 text-success">IDR {{ number_format($total ?? 0, 0, ',', '.') }}</h5>
                            </div>
                        </div>
                        
                        @if(count($cart ?? []) > 0)
                        <!-- Fresh Product Warnings -->
                        <div id="fresh-warnings" class="fresh-product-alert" style="display:none;">
                            <!-- Will be populated by JavaScript -->
                        </div>
                        
                        <div class="d-grid gap-2 mt-3">
                            <a href="{{ route('checkout') }}" class="btn btn-success btn-lg">
                                <i class="bx bx-lock-alt me-2"></i>Lanjut ke Checkout
                            </a>
                            <a href="{{ url('/') }}" class="btn btn-warning">
                                <i class="bx bx-arrow-back me-2"></i>Lanjut Belanja
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="loading-spinner">
        <div class="spinner-border text-success mb-3" role="status">
            <span class="visually-hidden">Memuat...</span>
        </div>
        <p class="mb-0">Memproses...</p>
    </div>
</div>
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
                <div class="d-flex align-items-start">
                    <i class="bx bx-info-circle fs-5 me-2"></i>
                    <div>
                        <strong><i class="bx bx-time me-1"></i>Perhatian: Masa Simpan Terbatas</strong><br>
                        <small>Anda memiliki ${freshProducts} produk dengan masa simpan terbatas (sekitar 7 hari) di keranjang. Pengiriman instan direkomendasikan untuk menjaga kualitas produk.</small>
                    </div>
                </div>
            `;
            $('#fresh-warnings').html(warningsHtml).show();
        }
    }
    
    // Auto-update cart quantity when input changes
    let updateTimeouts = {};
    
    // Store original values
    $('.quantity-input').each(function() {
        $(this).data('original-value', $(this).val());
    });
    
    $('.quantity-input').on('input change', function() {
        const input = $(this);
        const form = input.closest('.cart-update-form');
        const productId = form.find('input[name="id"]').val();
        const originalValue = parseInt(input.data('original-value')) || 1;
        const newQty = parseInt(input.val()) || 1;
        const maxQty = parseInt(input.attr('max')) || 999;
        
        // Validate quantity
        if (newQty < 1) {
            input.val(1);
            return;
        }
        if (newQty > maxQty) {
            input.val(maxQty);
            Swal.fire({
                icon: 'warning',
                title: 'Stok Terbatas',
                text: `Stok tersedia hanya ${maxQty} pcs`,
                confirmButtonColor: '#ffc107',
                timer: 2000,
                timerProgressBar: true
            });
            return;
        }
        
        // Only update if value actually changed
        if (newQty === originalValue) {
            return;
        }
        
        // Clear previous timeout for this product
        if (updateTimeouts[productId]) {
            clearTimeout(updateTimeouts[productId]);
        }
        
        // Debounce: wait 800ms after user stops typing
        updateTimeouts[productId] = setTimeout(function() {
            updateCartQuantity(form, productId, newQty);
            // Update original value after successful update
            input.data('original-value', newQty);
        }, 800);
    });
    
    // Also update on blur (when user leaves the field)
    $('.quantity-input').on('blur', function() {
        const input = $(this);
        const form = input.closest('.cart-update-form');
        const productId = form.find('input[name="id"]').val();
        const originalValue = parseInt(input.data('original-value')) || 1;
        const newQty = parseInt(input.val()) || 1;
        
        // Clear any pending timeout
        if (updateTimeouts[productId]) {
            clearTimeout(updateTimeouts[productId]);
        }
        
        // Update immediately if value changed
        if (newQty !== originalValue && newQty >= 1) {
            updateCartQuantity(form, productId, newQty);
            input.data('original-value', newQty);
        }
    });
    
    // Function to update cart quantity
    function updateCartQuantity(form, productId, qty) {
        // Show subtle loading indicator on the input
        const input = form.find('.quantity-input');
        input.prop('disabled', true).css('opacity', '0.6');
        
        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: {
                _token: form.find('input[name="_token"]').val(),
                id: productId,
                qty: qty
            },
            success: function(response) {
                input.prop('disabled', false).css('opacity', '1');
                
                // Show subtle success indicator
                const itemCard = form.closest('.cart-item-card');
                const productTitle = itemCard.find('h6');
                
                // Remove any existing success badge first
                productTitle.find('.badge.bg-success').remove();
                
                // Add new success badge
                const successBadge = $('<span class="badge bg-success ms-2"><i class="bx bx-check"></i> Updated</span>');
                productTitle.append(successBadge);
                
                // Remove badge after 2 seconds
                setTimeout(function() {
                    successBadge.fadeOut(function() {
                        $(this).remove();
                    });
                }, 2000);
                
                // Reload page to update totals after a short delay
                setTimeout(function() {
                    if (response.redirect) {
                        window.location.href = response.redirect;
                    } else {
                        location.reload();
                    }
                }, 1000);
            },
            error: function(xhr) {
                input.prop('disabled', false).css('opacity', '1');
                
                let message = 'Terjadi kesalahan saat mengupdate keranjang.';
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    message = xhr.responseJSON.error;
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: message,
                    confirmButtonColor: '#dc3545',
                    timer: 3000,
                    timerProgressBar: true
                });
                
                // Restore original value
                const originalValue = input.data('original-value') || 1;
                input.val(originalValue);
            }
        });
    }
    
    // Keep form submit handler as fallback (if button is clicked)
    $('.cart-update-form').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const productId = form.find('input[name="id"]').val();
        const qty = parseInt(form.find('input[name="qty"]').val()) || 1;
        updateCartQuantity(form, productId, qty);
    });
    
    // Handle cart remove with SweetAlert confirmation
    $('.cart-remove-form').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const itemName = form.closest('.cart-item-card').find('h6').text() || 'item ini';
        
        Swal.fire({
            title: 'Hapus dari Keranjang?',
            html: `Apakah Anda yakin ingin menghapus <strong>${itemName}</strong> dari keranjang?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="bx bx-trash me-1"></i>Ya, Hapus',
            cancelButtonText: '<i class="bx bx-x me-1"></i>Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                showLoading();
                
                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: form.serialize(),
                    success: function(response) {
                        hideLoading();
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Item berhasil dihapus dari keranjang',
                            confirmButtonColor: '#28a745',
                            timer: 2000,
                            timerProgressBar: true
                        }).then(() => {
                            if (response.redirect) {
                                window.location.href = response.redirect;
                            } else {
                                location.reload();
                            }
                        });
                    },
                    error: function(xhr) {
                        hideLoading();
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Terjadi kesalahan saat menghapus item.',
                            confirmButtonColor: '#dc3545'
                        });
                    }
                });
            }
        });
    });
    
    
    function showLoading() {
        $('#loadingOverlay').fadeIn();
    }
    
    function hideLoading() {
        $('#loadingOverlay').fadeOut();
    }
    
    function showNotification(message, type) {
        const notification = $(`
            <div class="alert alert-${type} alert-dismissible fade show position-fixed" 
                 style="top: 20px; right: 20px; z-index: 10000; min-width: 300px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                <i class="bx ${type === 'success' ? 'bx-check-circle' : type === 'danger' ? 'bx-x-circle' : 'bx-info-circle'} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `);
        
        $('body').append(notification);
        
        setTimeout(function() {
            notification.fadeOut('slow', function() {
                $(this).remove();
            });
        }, 5000);
    }
});
</script>
@endpush
