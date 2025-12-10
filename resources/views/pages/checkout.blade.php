@extends('layouts.app')

@section('title', 'Checkout - ' . config('app.name'))

@section('meta_description', 'Lengkapi data dan pilih metode pengiriman untuk menyelesaikan pesanan Anda di ' . config('app.name'))

@section('meta_keywords', 'checkout, pembayaran, pengiriman, pesanan, ' . config('app.name') . ', toko online')

@section('content')
    <!-- Single Page Header start -->
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">Checkout</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Beranda</a></li>
            <li class="breadcrumb-item"><a href="{{ route('cart') }}">Keranjang</a></li>
            <li class="breadcrumb-item active text-white">Checkout</li>
        </ol>
    </div>
    <!-- Single Page Header End -->

    <!-- Checkout Page Start -->
    <div class="container-fluid py-5">
        <div class="container py-5">
            <form id="checkoutForm" method="POST" action="{{ route('checkout.process') }}">
                @csrf
                <div class="row g-5">
                    <!-- Billing Information -->
                    <div class="col-lg-8">
                        <div class="border-start border-3 border-primary ps-4 mb-5">
                            <h4 class="mb-4">Informasi Pembayaran</h4>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="first_name" class="form-label">Nama Depan *</label>
                                    <input type="text" class="form-control" id="first_name" name="first_name" value="{{ old('first_name', auth()->user()->name ?? '') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="last_name" class="form-label">Nama Belakang</label>
                                    <input type="text" class="form-control" id="last_name" name="last_name" value="{{ old('last_name') }}">
                                </div>
                                <div class="col-12">
                                    <label for="email" class="form-label">Email *</label>
                                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email', auth()->user()->email ?? '') }}" required>
                                </div>
                                <div class="col-12">
                                    <label for="phone" class="form-label">Nomor Telepon *</label>
                                    <input type="tel" class="form-control" id="phone" name="phone" value="{{ old('phone') }}" required>
                                </div>
                                <div class="col-12">
                                    <label for="address" class="form-label">Alamat Lengkap *</label>
                                    <textarea class="form-control" id="address" name="address" rows="3" required>{{ old('address') }}</textarea>
                                </div>
                                <div class="col-md-6">
                                    <label for="city" class="form-label">Kota *</label>
                                    <input type="text" class="form-control" id="city" name="city" value="{{ old('city') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="postal_code" class="form-label">Kode Pos *</label>
                                    <input type="text" class="form-control" id="postal_code" name="postal_code" value="{{ old('postal_code') }}" required>
                                </div>
                                <div class="col-12">
                                    <label for="country" class="form-label">Negara *</label>
                                    <input type="text" class="form-control" id="country" name="country" value="Indonesia" required>
                                </div>
                                <div id="pickup-outlet-select" class="row mt-3" style="display:none;">
                                    <div class="col-12">
                                        <label class="form-label">Pilih Outlet (Pickup)</label>
                                        <select id="pickup_outlet_id" class="form-select">
                                            <option value="">Pilih Outlet</option>
                                        </select>
                                        <small class="text-muted">Wajib dipilih untuk metode Ambil Sendiri</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Shipping Method -->
                        <div class="border-start border-3 border-success ps-4 mb-5">
                            <h4 class="mb-4">Metode Pengiriman</h4>
                            
                            <!-- Shipping Type Selection -->
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label class="form-label">Metode Pengiriman *</label>
                                    <select id="shipping_type" class="form-select" required>
                                        <option value="">Pilih Metode</option>
                                        <option value="pickup">🏪 Ambil Sendiri ke Store (Semarang)</option>
                                        <option value="delivery">🚚 Gunakan Jasa Pengiriman</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div id="delivery-options" style="display:none;">
                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <label class="form-label">Kota Tujuan *</label>
                                        <select id="destination_city" class="form-select" required>
                                            <option value="">Pilih Kota</option>
                                            <option value="Semarang">Semarang (Pengiriman Instan Tersedia)</option>
                                            <option value="Jakarta">Jakarta (Same Day Tersedia)</option>
                                            <option value="Surabaya">Surabaya (Regular)</option>
                                            <option value="Bandung">Bandung (Regular)</option>
                                            <option value="Medan">Medan (Regular)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div id="pickup-info" style="display:none;">
                                <div class="alert alert-success">
                                    <h5 class="alert-heading">🏪 Ambil Sendiri ke Store</h5>
                                    <p class="mb-2">
                                        <strong>Alamat Store:</strong><br>
                                        📍 Jl. Soekarno Hatta No. 123, Semarang, Jawa Tengah<br>
                                        📞 (024) 123-4567<br>
                                        🕐 Buka: 08:00 - 20:00 WIB
                                    </p>
                                    <hr>
                                    <p class="mb-0">
                                        <strong>Keuntungan:</strong><br>
                                        ✅ <strong>GRATIS</strong> - Biaya pengiriman Rp 0<br>
                                        ✅ <strong>INSTAN</strong> - Produk langsung diambil<br>
                                        ✅ <strong>SEGAR</strong> - Kualitas produk terjamin<br>
                                        ✅ <strong>FLEXIBLE</strong> - Ambil kapan saja
                                    </p>
                                </div>
                            </div>
                            
                            <!-- Loading -->
                            <div id="shipping-loading" class="text-center py-4" style="display:none;">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Memuat...</span>
                                </div>
                                <p class="mt-2">Memuat metode pengiriman...</p>
                            </div>
                            
                            <!-- Error -->
                            <div id="shipping-error" style="display:none;"></div>
                            
                            <!-- Shipping Methods -->
                            <div id="shipping-methods" style="display:none;">
                                <!-- Will be populated by JavaScript -->
                            </div>
                            
                            <!-- Hidden fields for shipping -->
                            <input type="hidden" id="shipping-cost" name="shipping_cost" value="0">
                            <input type="hidden" id="shipping-method-id" name="shipping_method_id" value="">
                            <input type="hidden" id="outlet_id" name="outlet_id" value="">
                        </div>

                        <!-- Payment Method -->
                        <div class="border-start border-3 border-warning ps-4">
                            <h4 class="mb-4">Metode Pembayaran</h4>
                            <div class="alert alert-info">
                                <i class="bx bx-info-circle"></i>
                                Anda akan dialihkan ke halaman pembayaran yang aman setelah menyelesaikan pesanan.
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="payment_type" id="payment_full" value="full" checked>
                                        <label class="form-check-label" for="payment_full">
                                            <i class="bx bx-credit-card me-2"></i>Bayar Penuh Sekarang
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Summary -->
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">📋 Ringkasan Pesanan</h5>
                            </div>
                            <div class="card-body">
                                <!-- Cart Items -->
                                <div class="mb-4">
                                    <h6 class="mb-3">Produk yang Dipesan</h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Produk</th>
                                                    <th class="text-center">Qty</th>
                                                    <th class="text-end">Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse(($cart ?? []) as $item)
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <img src="{{ $item['image'] ? asset('storage/' . $item['image']) : asset('fruitables/img/vegetable-item-3.png') }}" 
                                                                 class="img-fluid me-2 rounded" style="width: 30px; height: 30px; object-fit: cover;" alt="">
                                                            <div>
                                                                <small class="mb-0">{{ $item['name'] }}</small>
                                                                @if(isset($item['product']) && $item['product']->shelf_life_days <= 7)
                                                                    <div><span class="badge bg-warning">🥬 Produk Segar</span></div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="text-center">{{ $item['qty'] }}</td>
                                                    <td class="text-end">IDR {{ number_format($item['price'] * $item['qty'], 0, ',', '.') }}</td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="3" class="text-center">Keranjang kosong</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                
                                <!-- Price Summary -->
                                <div class="mb-4">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Subtotal:</span>
                                        <span id="order-subtotal">IDR {{ number_format($totals['subtotal'] ?? 0, 0, ',', '.') }}</span>
                                    </div>
                                    @if(($totals['discount'] ?? 0) > 0)
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Diskon:</span>
                                        <span id="order-discount" class="text-danger">- IDR {{ number_format($totals['discount'] ?? 0, 0, ',', '.') }}</span>
                                    </div>
                                    @endif
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Metode Pengiriman:</span>
                                        <span id="order-shipping-method" class="text-info">-</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Pengiriman:</span>
                                        <span id="order-shipping" class="text-primary">IDR {{ number_format($totals['shipping'] ?? 0, 0, ',', '.') }}</span>
                                    </div>
                                    <hr>
                                    <div class="d-flex justify-content-between">
                                        <h5>Total:</h5>
                                        <h5 id="order-total" class="text-success">IDR {{ number_format($totals['total'] ?? 0, 0, ',', '.') }}</h5>
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary btn-lg" id="place-order-btn">
                                        <i class="bx bx-lock-alt me-2"></i>Buat Pesanan & Bayar
                                    </button>
                                    <a href="{{ route('cart') }}" class="btn btn-outline-secondary">
                                        <i class="bx bx-arrow-back me-2"></i>Kembali ke Keranjang
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- Checkout Page End -->

@endsection

@push('styles')
<style>
.shipping-method-card {
    cursor: pointer;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.shipping-method-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    border-color: #007bff;
}

.shipping-method-card.selected {
    background-color: #f8fff9;
    border-color: #28a745 !important;
    box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
}

.shipping-method-card.selected .card-body {
    position: relative;
}

.shipping-method-card.selected .card-body::before {
    content: '✓';
    position: absolute;
    top: 10px;
    right: 10px;
    background: #28a745;
    color: white;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: bold;
}

.badge {
    font-size: 0.75em;
}

.notification {
    z-index: 9999;
}
</style>
@endpush

@push('scripts')
<!-- Midtrans Snap.js -->
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
<script>
$(document).ready(function() {
    // Initialize checkout shipping manager
    window.checkoutShippingManager = new CheckoutShippingManager();
    const storeId = {{ isset($current_store) ? (int)$current_store->id : 1 }};

    // Toggle pickup outlet select based on shipping type
    $('#shipping_type').on('change', function() {
        const val = $(this).val();
        if (val === 'pickup') {
            $('#pickup-outlet-select').show();
            loadOutlets();
        } else {
            $('#pickup-outlet-select').hide();
            $('#pickup_outlet_id').val('');
            $('#outlet_id').val('');
        }
    });

    // Load outlets for current store
    function loadOutlets() {
        $.get('{{ route('api.outlets.by-store', ['storeId' => 'STORE_ID']) }}'.replace('STORE_ID', storeId), function(resp){
            const sel = $('#pickup_outlet_id');
            sel.empty();
            sel.append('<option value="">Pilih Outlet</option>');
            if (resp && resp.success && Array.isArray(resp.outlets)) {
                resp.outlets.forEach(function(o){
                    sel.append('<option value="'+o.id+'">'+o.name+' ('+o.code+')</option>');
                });
            }
        });
    }
    
    // Form validation
    $('#checkoutForm').on('submit', function(e) {
        e.preventDefault();
        
        // Validate shipping selection
        const shippingType = $('#shipping_type').val();
        if (!shippingType) {
            showNotification('Silakan pilih metode pengiriman', 'warning');
            return;
        }

        if (shippingType === 'delivery' && !$('#destination_city').val()) {
            showNotification('Silakan pilih kota tujuan', 'warning');
            return;
        }

        if (shippingType === 'pickup') {
            const outlet = $('#pickup_outlet_id').val();
            if (!outlet) {
                showNotification('Silakan pilih outlet untuk pickup', 'warning');
                return;
            }
            $('#outlet_id').val(outlet);
        }
        
        if (!window.checkoutShippingManager.selectedMethod && shippingType !== 'pickup') {
            showNotification('Silakan pilih metode pengiriman', 'warning');
            return;
        }
        
        // Submit order via AJAX
        $('#place-order-btn')
            .html('<i class="bx bx-loader-alt bx-spin me-2"></i>Memproses Pesanan...')
            .prop('disabled', true);

        $.ajax({
            url: '{{ route("checkout.process") }}',
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    // Success - Open Midtrans Snap
                    $('#place-order-btn')
                        .html('<i class="bx bx-check-circle me-2"></i>Membuka Pembayaran...')
                        .prop('disabled', true);

                    // Open Midtrans Snap
                    snap.pay(response.snap_token, {
                        onSuccess: function(result) {
                            console.log('Payment success:', result);
                            window.location.href = '/payment/finish?order_id=' + response.order_id;
                        },
                        onPending: function(result) {
                            console.log('Payment pending:', result);
                            window.location.href = '/payment/finish?order_id=' + response.order_id + '&status=pending';
                        },
                        onError: function(result) {
                            console.log('Payment error:', result);
                            window.location.href = '/payment/finish?order_id=' + response.order_id + '&status=error';
                        },
                        onClose: function() {
                            console.log('Customer closed the popup without finishing the payment');
                            $('#place-order-btn')
                                .html('<i class="bx bx-lock-alt me-2"></i>Buat Pesanan & Bayar')
                                .prop('disabled', false);
                            
                            showNotification('Pembayaran dibatalkan. Silakan coba lagi.', 'warning');
                        }
                    });
                } else {
                    // Error
                    showNotification(response.error || 'Terjadi kesalahan saat memproses pesanan Anda.', 'danger');
                    $('#place-order-btn')
                        .html('<i class="bx bx-lock-alt me-2"></i>Buat Pesanan & Bayar')
                        .prop('disabled', false);
                }
            },
            error: function(xhr) {
                let errorMessage = 'Terjadi kesalahan saat memproses pesanan Anda.';
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMessage = xhr.responseJSON.error;
                } else if (xhr.status === 422) {
                    errorMessage = 'Silakan isi semua field yang diperlukan dengan benar.';
                }
                
                showNotification(errorMessage, 'danger');
                $('#place-order-btn')
                    .html('<i class="bx bx-lock-alt me-2"></i>Buat Pesanan & Bayar')
                    .prop('disabled', false);
            }
        });
    });

    // Notification function
    function showNotification(message, type) {
        // Remove existing notifications
        $('.notification').remove();
        
        const notificationHtml = `
            <div class="notification alert alert-${type} alert-dismissible fade show position-fixed" 
                 style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
                <i class="bx ${type === 'success' ? 'bx-check-circle' : type === 'danger' ? 'bx-x-circle' : 'bx-info-circle'} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        $('body').append(notificationHtml);
        
        // Auto dismiss after 5 seconds
        setTimeout(function() {
            $('.notification').fadeOut('slow', function() {
                $(this).remove();
            });
        }, 5000);
    }
});

// Base ShippingManager class
class ShippingManager {
    constructor() {
        this.selectedMethod = null;
        this.cartItems = [];
        this.originCity = 'Jakarta';
        this.destinationCity = null;
        this.init();
    }

    init() {
        this.bindEvents();
        this.loadCartFromSession();
    }

    bindEvents() {
        // City change events
        $('#destination_city').on('change', () => this.onCityChange());
        $('#origin_city').on('change', () => this.onCityChange());
        
        // Shipping method selection
        $(document).on('click', '.shipping-method-card', (e) => this.selectShippingMethod(e));
        
        // Cart update events
        $(document).on('cart-updated', () => this.onCartUpdate());
    }

    async onCityChange() {
        this.destinationCity = $('#destination_city').val();
        this.originCity = $('#origin_city').val();
        
        if (this.destinationCity) {
            await this.loadShippingMethods();
        }
    }

    async loadShippingMethods() {
        try {
            this.showLoading();
            
            const destinationCity = this.destinationCity;
            const mockResponse = await this.getMockShippingResponse(destinationCity);
            
            if (mockResponse.success) {
                this.displayShippingMethods(mockResponse.data);
            } else {
                this.showError(mockResponse.message);
            }
        } catch (error) {
            console.error('Error loading shipping methods:', error);
            this.showError('Failed to load shipping methods');
        } finally {
            this.hideLoading();
        }
    }

    async getMockShippingResponse(destinationCity) {
        // Mock data based on destination
        const responses = {
            'Semarang': {
                success: true,
                data: {
                    methods: [
                        {
                            id: 1,
                            name: 'GoSend Instant',
                            code: 'gosend_instant',
                            type: 'instant',
                            cost: 22000,
                            formatted_cost: 'Rp 22.000',
                            estimated_days: '60 menit',
                            estimated_text: '60 menit',
                            fresh_product_score: 100,
                            is_fresh_friendly: true,
                            badge: '🌟 SANGAT BAGUS untuk Produk Segar',
                            badge_type: 'success'
                        },
                        {
                            id: 5,
                            name: 'SiCepat Same Day',
                            code: 'sicepat_same_day',
                            type: 'same_day',
                            cost: 18000,
                            formatted_cost: 'Rp 18.000',
                            estimated_days: '6 jam',
                            estimated_text: '6 jam',
                            fresh_product_score: 80,
                            is_fresh_friendly: true,
                            badge: '✅ BAGUS untuk Produk Segar',
                            badge_type: 'info'
                        }
                    ]
                }
            },
            'Jakarta': {
                success: true,
                data: {
                    methods: [
                        {
                            id: 5,
                            name: 'SiCepat Same Day',
                            code: 'sicepat_same_day',
                            type: 'same_day',
                            cost: 75000,
                            formatted_cost: 'Rp 75.000',
                            estimated_days: '12 jam',
                            estimated_text: '12 jam',
                            fresh_product_score: 80,
                            is_fresh_friendly: true,
                            badge: '✅ BAGUS untuk Produk Segar',
                            badge_type: 'warning'
                        }
                    ]
                }
            }
        };

        return responses[destinationCity] || {
            success: true,
            data: {
                methods: [
                    {
                        id: 8,
                        name: 'JNE REG',
                        code: 'jne_reg',
                        type: 'regular',
                        cost: 15000,
                        formatted_cost: 'Rp 15.000',
                        estimated_days: '2-3',
                        estimated_text: '2-3 hari',
                        fresh_product_score: 40,
                        is_fresh_friendly: false,
                        warning: '⚠️ Risiko tinggi untuk produk segar',
                        warning_type: 'danger'
                    }
                ]
            }
        };
    }

    displayShippingMethods(data) {
        const container = document.getElementById('shipping-methods');
        
        let html = '<h5 class="mb-3">Available Shipping Methods</h5>';
        html += '<div class="row">';
        
        data.methods.forEach(method => {
            const isSelected = this.selectedMethod && this.selectedMethod.id == method.id;
            const selectedClass = isSelected ? 'selected' : '';
            
            html += `
                <div class="col-12 mb-3">
                    <div class="card shipping-method-card ${selectedClass}" 
                         data-method-id="${method.id}" 
                         data-cost="${method.cost}">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <img src="${method.logo || 'https://via.placeholder.com/40'}" 
                                         alt="${method.name}" style="width: 40px; height: 40px;">
                                </div>
                                <div class="col">
                                    <h6 class="mb-1">${method.name}</h6>
                                    <small class="text-muted">${method.estimated_text}</small>
                                    ${method.badge ? `<div><span class="badge bg-${method.badge_type}">${method.badge}</span></div>` : ''}
                                    ${method.warning ? `<div><span class="badge bg-${method.warning_type}">${method.warning}</span></div>` : ''}
                                </div>
                                <div class="col-auto text-end">
                                    <h6 class="mb-0 text-primary">${method.formatted_cost}</h6>
                                    ${isSelected ? '<small class="text-success">Selected</small>' : ''}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        
        html += '</div>';
        container.innerHTML = html;
        container.style.display = 'block';
        
        // Bind click events after rendering
        this.bindShippingMethodEvents();
    }

    bindShippingMethodEvents() {
        // Remove existing event listeners
        document.querySelectorAll('.shipping-method-card').forEach(card => {
            card.replaceWith(card.cloneNode(true));
        });
        
        // Add new event listeners
        document.querySelectorAll('.shipping-method-card').forEach(card => {
            card.addEventListener('click', (e) => {
                e.preventDefault();
                this.selectShippingMethod(e);
            });
        });
    }

    selectShippingMethod(e) {
        // Remove previous selection
        document.querySelectorAll('.shipping-method-card').forEach(card => {
            card.classList.remove('selected');
        });
        
        // Add selection to clicked card
        const card = e.currentTarget;
        card.classList.add('selected');
        
        // Get method details
        const methodId = card.dataset.methodId;
        const methodCost = parseInt(card.dataset.cost);
        const methodName = card.querySelector('h6').textContent;
        
        // Update selected method
        this.selectedMethod = {
            id: methodId,
            cost: methodCost,
            name: methodName
        };
        
        // Update form fields
        document.getElementById('shipping-method-id').value = this.selectedMethod.id;
        document.getElementById('shipping-cost').value = this.selectedMethod.cost;
        
        // Update Order Summary
        this.updateOrderTotal(this.selectedMethod.cost);
        
        // Update shipping method name
        const methodElement = document.getElementById('order-shipping-method');
        if (methodElement) {
            methodElement.textContent = this.selectedMethod.name;
        }
        
        console.log('Shipping method selected:', this.selectedMethod);
    }

    updateOrderTotal(shippingCost) {
        const subtotal = {{ $totals['subtotal'] ?? 0 }};
        const discount = {{ $totals['discount'] ?? 0 }};
        const total = subtotal - discount + shippingCost;
        
        // Update Order Summary by ID
        const shippingElement = document.getElementById('order-shipping');
        if (shippingElement) {
            shippingElement.textContent = shippingCost === 0 ? 'GRATIS' : 'IDR ' + shippingCost.toLocaleString('id-ID');
        }
        
        const totalElement = document.getElementById('order-total');
        if (totalElement) {
            totalElement.textContent = 'IDR ' + total.toLocaleString('id-ID');
        }
        
        // Also update hidden form fields
        const shippingCostField = document.getElementById('shipping-cost');
        if (shippingCostField) {
            shippingCostField.value = shippingCost;
        }
        
        console.log('Order total updated:', {
            shipping: shippingCost,
            subtotal: subtotal,
            discount: discount,
            total: total
        });
    }

    showLoading() {
        document.getElementById('shipping-loading').style.display = 'block';
        document.getElementById('shipping-methods').style.display = 'none';
        document.getElementById('shipping-error').style.display = 'none';
    }

    hideLoading() {
        document.getElementById('shipping-loading').style.display = 'none';
    }

    showError(message) {
        const errorDiv = document.getElementById('shipping-error');
        errorDiv.innerHTML = `<div class="alert alert-danger">${message}</div>`;
        errorDiv.style.display = 'block';
    }

    loadCartFromSession() {
        // Implementation for loading cart from session
    }

    onCartUpdate() {
        // Implementation for cart update events
    }
}

// Checkout shipping integration
class CheckoutShippingManager extends ShippingManager {
    constructor() {
        super();
        this.originCity = 'Semarang';
        this.initCheckoutSpecific();
    }

    initCheckoutSpecific() {
        this.setupPickupLogic();
        this.bindCheckoutEvents();
    }

    setupPickupLogic() {
        $('#shipping_type').on('change', (e) => {
            const shippingType = e.target.value;
            
            if (shippingType === 'pickup') {
                this.showPickupOption();
            } else if (shippingType === 'delivery') {
                this.showDeliveryOption();
            } else {
                this.hideAllOptions();
            }
        });
    }

    showPickupOption() {
        $('#pickup-info').show();
        $('#delivery-options').hide();
        $('#shipping-methods').hide();
        
        this.selectPickupMethod();
    }

    showDeliveryOption() {
        $('#pickup-info').hide();
        $('#delivery-options').show();
        $('#shipping-methods').hide();
        
        this.destinationCity = null;
        this.selectedMethod = null;
        this.updateOrderTotal(0);
    }

    hideAllOptions() {
        $('#pickup-info').hide();
        $('#delivery-options').hide();
        $('#shipping-methods').hide();
    }

    selectPickupMethod() {
        const pickupMethod = {
            id: 'pickup',
            name: 'Ambil Sendiri ke Store',
            cost: 0,
            formatted_cost: 'GRATIS',
            type: 'pickup'
        };
        
        this.selectedMethod = pickupMethod;
        document.getElementById('shipping-method-id').value = pickupMethod.id;
        document.getElementById('shipping-cost').value = pickupMethod.cost;
        
        // Update Order Summary
        this.updateOrderTotal(pickupMethod.cost);
        
        // Update shipping method name
        const methodElement = document.getElementById('order-shipping-method');
        if (methodElement) {
            methodElement.textContent = pickupMethod.name;
        }
        
        console.log('Pickup method selected:', pickupMethod);
    }

    bindCheckoutEvents() {
        $('#destination_city').on('change', () => {
            this.destinationCity = $('#destination_city').val();
            if (this.destinationCity) {
                this.loadShippingMethods();
            }
        });
    }

    async getMockShippingResponse(destinationCity) {
        // Mock data based on destination
        const responses = {
            'Semarang': {
                success: true,
                data: {
                    methods: [
                        {
                            id: 1,
                            name: 'GoSend Instant',
                            code: 'gosend_instant',
                            type: 'instant',
                            cost: 22000,
                            formatted_cost: 'Rp 22.000',
                            estimated_days: '60 menit',
                            estimated_text: '60 menit',
                            fresh_product_score: 100,
                            is_fresh_friendly: true,
                            badge: '🌟 SANGAT BAGUS untuk Produk Segar',
                            badge_type: 'success'
                        },
                        {
                            id: 5,
                            name: 'SiCepat Same Day',
                            code: 'sicepat_same_day',
                            type: 'same_day',
                            cost: 18000,
                            formatted_cost: 'Rp 18.000',
                            estimated_days: '6 jam',
                            estimated_text: '6 jam',
                            fresh_product_score: 80,
                            is_fresh_friendly: true,
                            badge: '✅ BAGUS untuk Produk Segar',
                            badge_type: 'info'
                        }
                    ]
                }
            },
            'Jakarta': {
                success: true,
                data: {
                    methods: [
                        {
                            id: 5,
                            name: 'SiCepat Same Day',
                            code: 'sicepat_same_day',
                            type: 'same_day',
                            cost: 75000,
                            formatted_cost: 'Rp 75.000',
                            estimated_days: '12 jam',
                            estimated_text: '12 jam',
                            fresh_product_score: 80,
                            is_fresh_friendly: true,
                            badge: '✅ BAGUS untuk Produk Segar',
                            badge_type: 'warning'
                        }
                    ]
                }
            }
        };

        return responses[destinationCity] || {
            success: true,
            data: {
                methods: [
                    {
                        id: 8,
                        name: 'JNE REG',
                        code: 'jne_reg',
                        type: 'regular',
                        cost: 15000,
                        formatted_cost: 'Rp 15.000',
                        estimated_days: '2-3',
                        estimated_text: '2-3 hari',
                        fresh_product_score: 40,
                        is_fresh_friendly: false,
                        warning: '⚠️ Risiko tinggi untuk produk segar',
                        warning_type: 'danger'
                    }
                ]
            }
        };
    }
}
</script>
@endpush
