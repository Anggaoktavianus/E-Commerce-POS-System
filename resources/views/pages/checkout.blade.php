@extends('layouts.app')

@section('title', 'Checkout - ' . config('app.name'))

@section('meta_description', 'Lengkapi data dan pilih metode pengiriman untuk menyelesaikan pesanan Anda di ' . config('app.name'))

@section('meta_keywords', 'checkout, pembayaran, pengiriman, pesanan, ' . config('app.name') . ', toko online')

@push('styles')
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder@1.13.0/dist/Control.Geocoder.css" />
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<style>
    .checkout-page {
        background: linear-gradient(135deg, #f0f9f4 0%, #d4edda 100%);
        min-height: 100vh;
        padding: 2rem 0;
    }

    .section-header {
        border-left: 4px solid;
        padding-left: 1rem;
        margin-bottom: 1.5rem;
        font-weight: 600;
    }

    .section-header.payment {
        border-color: #28a745;
        color: #28a745;
    }

    .section-header.shipping {
        border-color: #20c997;
        color: #20c997;
    }

    .section-header.payment-method {
        border-color: #ffc107;
        color: #ff9800;
    }

    .form-control, .form-select {
        border-radius: 8px;
        border: 2px solid #e9ecef;
        padding: 0.75rem;
        transition: all 0.3s ease;
    }

    .form-control:focus, .form-select:focus {
        border-color: #28a745;
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
    }

    .form-label {
        font-weight: 500;
        color: #495057;
        margin-bottom: 0.5rem;
    }

    .required-field::after {
        content: " *";
        color: #dc3545;
    }

    .order-summary-card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        overflow: hidden;
        position: sticky;
        top: 20px;
    }

    .order-summary-header {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: white;
        padding: 1.25rem;
    }

    .shipping-method-card {
        cursor: pointer;
        transition: all 0.3s ease;
        border: 2px solid #e9ecef;
        border-radius: 12px;
        margin-bottom: 1rem;
        background: white;
    }

    .shipping-method-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        border-color: #28a745;
    }

    .shipping-method-card.selected {
        background: linear-gradient(135deg, #f8fff9 0%, #e8f5e9 100%);
        border-color: #28a745 !important;
        box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
    }

    .shipping-method-card.selected::after {
        content: '✓';
        position: absolute;
        top: 10px;
        right: 15px;
        background: #28a745;
        color: white;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        font-weight: bold;
    }

    .pickup-info-card {
        background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
        border: 2px solid #4caf50;
        border-radius: 12px;
        padding: 1.5rem;
    }

    .product-item-row {
        border-bottom: 1px solid #e9ecef;
        padding: 0.75rem 0;
    }

    .product-item-row:last-child {
        border-bottom: none;
    }

    .product-thumbnail {
        width: 40px;
        height: 40px;
        object-fit: cover;
        border-radius: 6px;
    }

    .btn-checkout {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        border: none;
        color: white;
        padding: 1rem;
        font-weight: 600;
        border-radius: 10px;
        transition: all 0.3s ease;
    }

    .btn-checkout:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
        color: white;
    }

    .btn-checkout:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .total-amount {
        font-size: 1.5rem;
        font-weight: 700;
        color: #28a745;
    }

    @media (max-width: 768px) {
        .order-summary-card {
            position: relative;
            top: 0;
            margin-top: 2rem;
        }

        .section-header {
            font-size: 1.1rem;
        }
    }

    .notification {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 10000;
        min-width: 300px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        animation: slideInRight 0.3s ease;
    }

    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
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
        'pageTitle' => $siteSettings['checkout_page_title'] ?? 'Checkout',
        'breadcrumbItems' => [
            ['label' => 'Beranda', 'url' => url('/')],
            ['label' => 'Toko', 'url' => route('shop')],
            ['label' => 'Keranjang', 'url' => route('cart')],
            ['label' => 'Checkout', 'url' => null]
        ]
    ])

<div class="checkout-page" style="background: linear-gradient(180deg, #f8f9fa 0%, #ffffff 100%);">
        <div class="container py-5">
            <form id="checkoutForm" method="POST" action="{{ route('checkout.process') }}">
                @csrf
            <div class="row g-4">
                <!-- Left Column: Forms -->
                    <div class="col-lg-8">
                    <!-- Alamat Pengiriman -->
                    <div class="card border-0 shadow-lg mb-4">
                        <div class="card-body p-4">
                            <h4 class="section-header payment mb-3">
                                <i class="bx bx-map-pin me-2"></i>Alamat Pengiriman
                            </h4>
                            
                            @if($selectedAddress)
                            <!-- Display Selected Address -->
                            <div class="address-card mb-3 p-3 border rounded" style="background: #f8f9fa;">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1 fw-bold">{{ $selectedAddress->recipient_name }}</h6>
                                        <p class="mb-1 text-muted small">(+62) {{ $selectedAddress->recipient_phone }}</p>
                                        <p class="mb-0 small">
                                            {{ $selectedAddress->address }}
                                            @if($selectedAddress->notes), {{ $selectedAddress->notes }}@endif
                                            , {{ $selectedAddress->loc_kecamatan_name ?? '' }}, {{ $selectedAddress->loc_kabkota_name ?? $selectedAddress->city }}
                                            , {{ $selectedAddress->loc_provinsi_name ?? $selectedAddress->province }}
                                            @if($selectedAddress->postal_code) {{ $selectedAddress->postal_code }}@endif
                                        </p>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        @if($selectedAddress->is_primary)
                                        <span class="badge bg-danger" style="font-size: 0.7rem;">Utama</span>
                                        @endif
                                        <button type="button" class="btn btn-sm btn-link text-primary p-0" id="btn-change-address">
                                            Ubah
                                        </button>
                                    </div>
                                </div>
                                <!-- <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" id="send_as_dropshipper" name="send_as_dropshipper" value="1">
                                    <label class="form-check-label small" for="send_as_dropshipper">
                                        Kirim sebagai Dropshipper
                                    </label>
                                </div> -->
                            </div>
                            @elseif($user)
                            <!-- Fallback to user table data if no address -->
                            <div class="address-card mb-3 p-3 border rounded" style="background: #f8f9fa;">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1 fw-bold">{{ $user['name'] ?? 'N/A' }}</h6>
                                        <p class="mb-1 text-muted small">(+62) {{ $user['phone'] ?? 'N/A' }}</p>
                                        <p class="mb-0 small">
                                            {{ $user['address'] ?? 'Alamat belum diisi' }}
                                            , {{ $user['city'] ?? 'Semarang' }}
                                        </p>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <button type="button" class="btn btn-sm btn-link text-primary p-0" id="btn-change-address">
                                            Ubah
                                        </button>
                                    </div>
                                </div>
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" id="send_as_dropshipper" name="send_as_dropshipper" value="1">
                                    <label class="form-check-label small" for="send_as_dropshipper">
                                        Kirim sebagai Dropshipper
                                    </label>
                                </div>
                            </div>
                            @endif
                            
                            <!-- Address Form (Hidden by default, shown when "Ubah" clicked or no address) -->
                            <div id="address-form" style="display: {{ ($selectedAddress || ($user && isset($user['address']) && $user['address'])) ? 'none' : 'block' }};">
                            <div class="row g-3">
                                    <div class="col-12">
                                        <label for="form_recipient_name" class="form-label required-field">Nama Penerima</label>
                                        <input type="text" class="form-control" id="form_recipient_name" name="form_recipient_name"
                                               value="{{ old('form_recipient_name', $selectedAddress->recipient_name ?? $user['name'] ?? '') }}" required>
                                    </div>
                                    <div class="col-12">
                                        <label for="form_recipient_phone" class="form-label required-field">Nomor Telepon</label>
                                        <input type="tel" class="form-control" id="form_recipient_phone" name="form_recipient_phone"
                                               value="{{ old('form_recipient_phone', $selectedAddress->recipient_phone ?? $user['phone'] ?? '') }}" required>
                                    </div>
                                    <div class="col-12">
                                        <label for="form_address" class="form-label required-field">Alamat Lengkap</label>
                                        <textarea class="form-control" id="form_address" name="form_address" rows="3" required>{{ old('form_address', $selectedAddress->address ?? $user['address'] ?? '') }}</textarea>
                                        <small class="text-muted">Masukkan alamat lengkap untuk perhitungan ongkos kirim instan</small>
                                    </div>
                                <div class="col-md-6">
                                        <label for="form_city" class="form-label required-field">Kota</label>
                                        <input type="text" class="form-control" id="form_city" name="form_city"
                                               value="{{ old('form_city', $selectedAddress->loc_kabkota_name ?? $selectedAddress->city ?? $user['city'] ?? '') }}" required>
                                </div>
                                <div class="col-md-6">
                                        <label for="form_postal_code" class="form-label">Kode Pos</label>
                                        <input type="text" class="form-control" id="form_postal_code" name="form_postal_code"
                                               value="{{ old('form_postal_code', $selectedAddress->postal_code ?? $user['postal_code'] ?? '') }}">
                                </div>
                                @if(($selectedAddress && $selectedAddress->latitude && $selectedAddress->longitude) || (isset($user['latitude']) && isset($user['longitude']) && $user['latitude'] && $user['longitude']))
                                <div class="col-12">
                                    <div class="alert alert-info border-0">
                                        <i class="bx bx-info-circle me-2"></i>
                                        <strong>Alamat tersimpan:</strong> Koordinat lokasi Anda sudah tersimpan dari profil.
                                        Ongkos kirim akan dihitung otomatis berdasarkan lokasi Anda.
                                </div>
                                </div>
                                @else
                                <div class="col-12">
                                    <div class="card bg-light border-0 p-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="use-manual-coordinates">
                                            <label class="form-check-label" for="use-manual-coordinates">
                                                <small>Gunakan koordinat manual (opsional, untuk akurasi lebih baik)</small>
                                            </label>
                                </div>
                                        <div id="manual-coordinates" style="display:none;" class="mt-3">
                                            <div class="row g-2">
                                <div class="col-md-6">
                                                    <label class="form-label small">Latitude</label>
                                                    <input type="number" step="0.00000001" class="form-control form-control-sm" id="customer_latitude" placeholder="-7.0051">
                                </div>
                                <div class="col-md-6">
                                                    <label class="form-label small">Longitude</label>
                                                    <input type="number" step="0.00000001" class="form-control form-control-sm" id="customer_longitude" placeholder="110.4381">
                                </div>
                                            </div>
                                            <small class="text-muted">Dapatkan koordinat dari Google Maps: Klik kanan pada lokasi → Koordinat</small>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                <div class="col-12">
                                    <label for="country" class="form-label required-field">Negara</label>
                                    <input type="text" class="form-control" id="country" name="country"
                                           value="Indonesia" readonly>
                                </div>
                                    <div class="col-12">
                                    <button type="button" class="btn btn-success" id="btn-save-address-form">
                                        <i class="bx bx-check me-1"></i>Gunakan Alamat Ini
                                    </button>
                                    @if($selectedAddress || ($user && isset($user['address']) && $user['address']))
                                    <button type="button" class="btn btn-secondary" id="btn-cancel-address-form">
                                        Batal
                                    </button>
                                    @endif
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Hidden fields for form data (will be populated from selected address or form) -->
                            <input type="hidden" id="selected_address_id" name="address_id" value="{{ $selectedAddress->id ?? '' }}">
                            <input type="hidden" id="first_name" name="first_name" value="{{ old('first_name', $selectedAddress->recipient_name ?? $user['name'] ?? '') }}">
                            <input type="hidden" id="email" name="email" value="{{ old('email', $user['email'] ?? '') }}">
                            <input type="hidden" id="phone" name="phone" value="{{ old('phone', $selectedAddress->recipient_phone ?? $user['phone'] ?? '') }}">
                            <input type="hidden" id="address" name="address" value="{{ old('address', $selectedAddress->address ?? $user['address'] ?? '') }}">
                            <input type="hidden" id="city" name="city" value="{{ old('city', $selectedAddress->loc_kabkota_name ?? $selectedAddress->city ?? $user['city'] ?? '') }}">
                            <input type="hidden" id="postal_code" name="postal_code" value="{{ old('postal_code', $selectedAddress->postal_code ?? $user['postal_code'] ?? '') }}">
                            <input type="hidden" id="country" name="country" value="Indonesia">
                            <input type="hidden" id="customer_latitude" name="customer_latitude" value="{{ $selectedAddress->latitude ?? $user['latitude'] ?? '' }}">
                            <input type="hidden" id="customer_longitude" name="customer_longitude" value="{{ $selectedAddress->longitude ?? $user['longitude'] ?? '' }}">
                            </div>
                        </div>

                        <!-- Shipping Method -->
                    <div class="card border-0 shadow-lg mb-4">
                        <div class="card-body p-4">
                            <h4 class="section-header shipping">
                                <i class="bx bx-truck me-2"></i>Metode Pengiriman
                            </h4>
                            
                            <!-- Shipping Type Selection -->
                            <div class="mb-4">
                                <label class="form-label required-field">Tipe Pengiriman</label>
                                    <select id="shipping_type" class="form-select" required>
                                        <option value="">Pilih Metode</option>
                                    <option value="pickup">🏪 Ambil Sendiri (Store/Outlet Terdekat)</option>
                                        <option value="instant">⚡ Pengiriman Instan (Berdasarkan Jarak)</option>
                                        <option value="delivery">🚚 Gunakan Jasa Pengiriman (Luar Kota)</option>
                                    </select>
                                </div>

                            <!-- Store/Outlet Selection (for pickup) -->
                            <div id="pickup-location-select" class="mb-4" style="display:none;">
                                <label class="form-label required-field">Pilih Lokasi Terdekat</label>
                                <div id="pickup-locations-list" class="row g-3">
                                    <!-- Will be populated by JavaScript with stores and outlets sorted by distance -->
                                </div>
                                <small class="text-muted">Lokasi diurutkan berdasarkan jarak terdekat dari alamat Anda</small>
                            </div>
                            
                            <!-- Instant Delivery Options (No city selection needed) -->
                            <div id="instant-options" style="display:none;">
                                <div class="mb-4">
                                    <label class="form-label required-field">Pilih Store/Outlet Pengirim (Terdekat)</label>
                                    <select id="instant_store_outlet" class="form-select">
                                        <option value="">Pilih Store/Outlet</option>
                                        <!-- Stores -->
                                        @foreach($stores ?? [] as $store)
                                            @if($store->latitude && $store->longitude)
                                            <option value="store_{{ $store->id }}"
                                                    data-type="store"
                                                    data-id="{{ $store->id }}"
                                                    data-lat="{{ $store->latitude }}"
                                                    data-lng="{{ $store->longitude }}">
                                                🏪 Store: {{ $store->name }}
                                            </option>
                                            @endif
                                        @endforeach
                                        <!-- Outlets -->
                                        @foreach($outlets ?? [] as $outlet)
                                            @if($outlet->latitude && $outlet->longitude)
                                            <option value="outlet_{{ $outlet->id }}"
                                                    data-type="outlet"
                                                    data-id="{{ $outlet->id }}"
                                                    data-lat="{{ $outlet->latitude }}"
                                                    data-lng="{{ $outlet->longitude }}">
                                                📍 Outlet: {{ $outlet->name }} @if($outlet->store) ({{ $outlet->store->name }}) @endif
                                            </option>
                                            @endif
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Pilih store atau outlet yang akan mengirimkan pesanan. Daftar diurutkan berdasarkan jarak terdekat dari alamat Anda.</small>
                                </div>

                                <!-- Instant Delivery Distance Calculation -->
                                <div id="instant-delivery-calculation" class="card bg-light border-success mb-3">
                                    <div class="card-body p-3">
                                        <h6 class="mb-2 text-success">
                                            <i class="bx bx-map me-2"></i>Perhitungan Ongkos Kirim Instan
                                        </h6>
                                        <p class="small text-muted mb-2">
                                            <i class="bx bx-info-circle me-1"></i>
                                            Ongkos kirim instan dihitung berdasarkan jarak terdekat dari lokasi anda
                                        </p>
                                        <div id="distance-info" class="mb-2">
                                            <small class="text-muted">Pilih store/outlet dan isi alamat lengkap di atas untuk menghitung jarak dan ongkos kirim</small>
                                        </div>
                                        <button type="button" id="btn-calculate-distance" class="btn btn-sm btn-success" style="display:none;">
                                            <i class="bx bx-calculator me-1"></i>Hitung Ulang
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Delivery Options (For out-of-city delivery) -->
                            <div id="delivery-options" style="display:none;">
                                <div class="mb-4">
                                    <label class="form-label required-field">Pilih Store/Outlet Pengirim (Terdekat)</label>
                                    <select id="delivery_store_outlet" class="form-select">
                                        <option value="">Pilih Store/Outlet</option>
                                        <!-- Stores -->
                                        @foreach($stores ?? [] as $store)
                                            @if($store->latitude && $store->longitude)
                                            <option value="store_{{ $store->id }}"
                                                    data-type="store"
                                                    data-id="{{ $store->id }}"
                                                    data-lat="{{ $store->latitude }}"
                                                    data-lng="{{ $store->longitude }}">
                                                🏪 Store: {{ $store->name }}
                                            </option>
                                            @endif
                                        @endforeach
                                        <!-- Outlets -->
                                        @foreach($outlets ?? [] as $outlet)
                                            @if($outlet->latitude && $outlet->longitude)
                                            <option value="outlet_{{ $outlet->id }}"
                                                    data-type="outlet"
                                                    data-id="{{ $outlet->id }}"
                                                    data-lat="{{ $outlet->latitude }}"
                                                    data-lng="{{ $outlet->longitude }}">
                                                📍 Outlet: {{ $outlet->name }} @if($outlet->store) ({{ $outlet->store->name }}) @endif
                                            </option>
                                            @endif
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Pilih store atau outlet yang akan mengirimkan pesanan. Daftar diurutkan berdasarkan jarak terdekat dari alamat Anda.</small>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label required-field">Kota Tujuan</label>
                                    <select id="destination_city" class="form-select">
                                            <option value="">Pilih Kota</option>
                                            <option value="Jakarta">Jakarta (Same Day Tersedia)</option>
                                            <option value="Surabaya">Surabaya (Regular)</option>
                                            <option value="Bandung">Bandung (Regular)</option>
                                            <option value="Medan">Medan (Regular)</option>
                                        </select>
                                </div>
                            </div>
                            
                            <!-- Pickup Info (will be shown when location is selected) -->
                            <div id="pickup-info" style="display:none;">
                                <div class="pickup-info-card">
                                    <h5 class="mb-3">
                                        <i class="bx bx-store me-2"></i><span id="pickup-location-name">Pilih Lokasi</span>
                                    </h5>
                                    <div id="pickup-location-details">
                                    <p class="mb-2">
                                            <strong><i class="bx bx-map me-1"></i>Alamat:</strong><br>
                                            <span id="pickup-location-address">-</span>
                                        </p>
                                        <p class="mb-2">
                                            <strong><i class="bx bx-phone me-1"></i>Telepon:</strong>
                                            <span id="pickup-location-phone">-</span>
                                        </p>
                                        <p class="mb-2">
                                            <strong><i class="bx bx-map-pin me-1"></i>Jarak:</strong>
                                            <span id="pickup-location-distance" class="text-success fw-bold">-</span>
                                        </p>
                                        <p class="mb-3" id="pickup-location-hours-container" style="display:none;">
                                            <strong><i class="bx bx-time me-1"></i>Jam Buka:</strong><br>
                                            <span id="pickup-location-hours">-</span>
                                        </p>
                                    </div>
                                    <hr>
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <small><strong>✅ GRATIS</strong><br>Biaya pengiriman Rp 0</small>
                                        </div>
                                        <div class="col-6">
                                            <small><strong>✅ INSTAN</strong><br>Produk langsung diambil</small>
                                        </div>
                                        <div class="col-6">
                                            <small><strong>✅ SEGAR</strong><br>Kualitas produk terjamin</small>
                                        </div>
                                        <div class="col-6">
                                            <small><strong>✅ FLEXIBLE</strong><br>Ambil kapan saja</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Loading -->
                            <div id="shipping-loading" class="text-center py-4" style="display:none;">
                                <div class="spinner-border text-success" role="status">
                                    <span class="visually-hidden">Memuat...</span>
                                </div>
                                <p class="mt-2 text-muted">Memuat metode pengiriman...</p>
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
                            <input type="hidden" id="pickup_location_type" name="pickup_location_type" value="">
                            <input type="hidden" id="pickup_location_id" name="pickup_location_id" value="">
                            <input type="hidden" id="outlet_id" name="outlet_id" value="">
                        </div>
                        </div>

                        <!-- Payment Method -->
                    <div class="card border-0 shadow-lg">
                        <div class="card-body p-4">
                            <h4 class="section-header payment-method">
                                <i class="bx bx-credit-card me-2"></i>Metode Pembayaran
                            </h4>
                            <div class="alert alert-info border-0">
                                <i class="bx bx-info-circle me-2"></i>
                                Anda akan dialihkan ke halaman pembayaran yang aman setelah menyelesaikan pesanan.
                            </div>
                            <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_type" id="payment_full" value="full" checked>
                                        <label class="form-check-label" for="payment_full">
                                            <i class="bx bx-credit-card me-2"></i>Bayar Penuh Sekarang
                                        </label>
                                </div>
                            </div>
                        </div>
                    </div>

                <!-- Right Column: Order Summary -->
                    <div class="col-lg-4">
                    <div class="order-summary-card">
                        <div class="order-summary-header">
                            <h5 class="mb-0 text-white">
                                <i class="bx bx-receipt me-2"></i>Ringkasan Pesanan
                            </h5>
                            </div>
                        <div class="card-body p-4">
                                <!-- Cart Items -->
                                <div class="mb-4">
                                <h6 class="mb-3 fw-bold">Produk yang Dipesan</h6>
                                    <div class="table-responsive">
                                    <table class="table table-sm table-borderless">
                                            <thead>
                                                <tr>
                                                    <th>Produk</th>
                                                    <th class="text-center">Jumlah</th>
                                                    <th class="text-end">Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse(($cart ?? []) as $item)
                                            <tr class="product-item-row">
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <img src="{{ $item['image'] ? asset('storage/' . $item['image']) : asset('fruitables/img/vegetable-item-3.png') }}" 
                                                             class="product-thumbnail me-2" alt="">
                                                            <div>
                                                            <small class="mb-0 d-block">{{ $item['name'] }}</small>
                                                                @if(isset($item['product']) && $item['product']->shelf_life_days <= 7)
                                                                <span class="badge bg-warning text-dark" style="font-size: 0.65rem;">
                                                                    <i class="bx bx-time me-1"></i>Masa Simpan Terbatas
                                                                </span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="text-center">{{ $item['qty'] }}</td>
                                                    <td class="text-end">IDR {{ number_format($item['price'] * $item['qty'], 0, ',', '.') }}</td>
                                                </tr>
                                                @empty
                                                <tr>
                                                <td colspan="3" class="text-center text-muted">Keranjang kosong</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                
                                <!-- Price Summary -->
                                <div class="mb-4">
                                    <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Subtotal:</span>
                                    <span id="order-subtotal" class="fw-bold">IDR {{ number_format($totals['subtotal'] ?? 0, 0, ',', '.') }}</span>
                                    </div>
                                    @if(($totals['discount'] ?? 0) > 0)
                                    <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Diskon:</span>
                                    <span id="order-discount" class="text-danger fw-bold">- IDR {{ number_format($totals['discount'] ?? 0, 0, ',', '.') }}</span>
                                    </div>
                                    @endif
                                    <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Metode Pengiriman:</span>
                                    <span id="order-shipping-method" class="text-success small">-</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Pengiriman:</span>
                                    <span id="order-shipping" class="text-success fw-bold">-</span>
                                    </div>
                                    <hr>
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">Total:</h5>
                                    <h5 id="order-total" class="mb-0 total-amount">IDR {{ number_format($totals['subtotal'] ?? 0, 0, ',', '.') }}</h5>
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-checkout btn-lg" id="place-order-btn">
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

<!-- Loading Overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="loading-spinner">
        <div class="spinner-border text-success mb-3" role="status">
            <span class="visually-hidden">Memuat...</span>
        </div>
        <p class="mb-0">Memproses pesanan...</p>
    </div>
</div>

<!-- Modal Pilih/Ubah Alamat -->
<div class="modal fade" id="addressModal" tabindex="-1" aria-labelledby="addressModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="addressModalLabel">
                    <i class="bx bx-map-pin me-2"></i>Pilih Alamat Pengiriman
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div id="address-list" class="mb-3">
                    @forelse($userAddresses ?? [] as $addr)
                    <div class="card mb-2 address-option" data-address-id="{{ $addr->id }}" style="cursor: pointer; transition: all 0.3s;">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <h6 class="mb-0 fw-bold">{{ $addr->recipient_name }}</h6>
                                        @if($addr->is_primary)
                                        <span class="badge bg-danger" style="font-size: 0.7rem;">Utama</span>
                                        @endif
                                        @if($addr->label)
                                        <span class="badge bg-secondary" style="font-size: 0.7rem;">{{ $addr->label }}</span>
                                        @endif
                                    </div>
                                    <p class="mb-1 small text-muted">(+62) {{ $addr->recipient_phone }}</p>
                                    <p class="mb-0 small">
                                        {{ $addr->address }}
                                        @if($addr->notes), {{ $addr->notes }}@endif
                                        , {{ $addr->loc_kecamatan_name ?? '' }}, {{ $addr->loc_kabkota_name ?? $addr->city }}
                                        , {{ $addr->loc_provinsi_name ?? $addr->province }}
                                        @if($addr->postal_code) {{ $addr->postal_code }}@endif
                                    </p>
                                </div>
                                <div>
                                    <input type="radio" name="selected_address" value="{{ $addr->id }}" 
                                           {{ ($selectedAddress && $selectedAddress->id == $addr->id) ? 'checked' : '' }}>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="alert alert-info">
                        <i class="bx bx-info-circle me-2"></i>Belum ada alamat tersimpan. Silakan isi form di bawah.
                    </div>
                    @endforelse
                </div>
                
                <hr>
                
                <div class="mt-3">
                    <h6 class="mb-3">Tambah Alamat Baru</h6>
                    <form id="new-address-form">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label required-field">Label Alamat</label>
                                <input type="text" class="form-control" id="new_address_label" name="label" placeholder="Rumah, Kantor, dll">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label required-field">Nama Penerima</label>
                                <input type="text" class="form-control" id="new_recipient_name" name="recipient_name" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label required-field">Nomor Telepon</label>
                                <input type="tel" class="form-control" id="new_recipient_phone" name="recipient_phone" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label required-field">Alamat Lengkap</label>
                                <textarea class="form-control" id="new_address" name="address" rows="2" required></textarea>
                            </div>
                            
                            <!-- Location References -->
                            <div class="col-md-6">
                                <label class="form-label required-field">Provinsi (Ref)</label>
                                <select id="new_loc_provinsi_id" name="loc_provinsi_id" class="form-select" required>
                                    <option value="">Pilih Provinsi</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label required-field">Kab/Kota (Ref)</label>
                                <select id="new_loc_kabkota_id" name="loc_kabkota_id" class="form-select" required>
                                    <option value="">Pilih Kab/Kota</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label required-field">Kecamatan (Ref)</label>
                                <select id="new_loc_kecamatan_id" name="loc_kecamatan_id" class="form-select" required>
                                    <option value="">Pilih Kecamatan</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label required-field">Desa/Kelurahan (Ref)</label>
                                <select id="new_loc_desa_id" name="loc_desa_id" class="form-select" required>
                                    <option value="">Pilih Desa/Kelurahan</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Kota (Teks)</label>
                                <input type="text" class="form-control" id="new_city" name="city">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Kode Pos</label>
                                <input type="text" class="form-control" id="new_postal_code" name="postal_code">
                            </div>
                            
                            <!-- Pinpoint Lokasi di Peta -->
                            <div class="col-12">
                                <hr>
                                <h6 class="mb-3"><i class="bx bx-map-pin me-2"></i>Pinpoint Lokasi di Peta</h6>
                                
                                <div class="mb-3">
                                    <button type="button" id="btn-get-location-modal" class="btn btn-sm btn-success">
                                        <i class="bx bx-crosshair me-1"></i>Gunakan Lokasi GPS Saya
                                    </button>
                                    <span id="location-status-modal" class="ms-2 small text-muted"></span>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="bx bx-search"></i>
                                        </span>
                                        <input type="text" 
                                               id="address-search-modal" 
                                               class="form-control" 
                                               placeholder="Cari alamat (contoh: Jl. Sudirman No. 123, Semarang)" 
                                               autocomplete="off">
                                        <button type="button" id="btn-clear-search-modal" class="btn btn-outline-secondary" style="display:none;">
                                            <i class="bx bx-x"></i>
                                        </button>
                                    </div>
                                    <small class="text-muted d-block mt-1">
                                        <i class="bx bx-info-circle me-1"></i>
                                        Ketik alamat lengkap untuk mencari lokasi di peta
                                    </small>
                                </div>
                                
                                <div id="map-modal" style="height: 400px; width: 100%; border-radius: 12px; border: 2px solid #e9ecef;"></div>
                                <small class="text-muted d-block mt-2">
                                    <i class="bx bx-info-circle me-1"></i>
                                    Gunakan GPS, cari alamat, atau klik pada peta untuk menentukan lokasi. Pastikan marker berada di lokasi yang tepat.
                                </small>
                                
                                <!-- Hidden fields for coordinates and location references -->
                                <input type="hidden" id="new_latitude" name="latitude">
                                <input type="hidden" id="new_longitude" name="longitude">
                            </div>
                            
                            <div class="col-12">
                                <label class="form-label">Catatan</label>
                                <input type="text" class="form-control" id="new_notes" name="notes" placeholder="Samping SMA 7, dll">
                            </div>
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="new_is_primary" name="is_primary" value="1">
                                    <label class="form-check-label" for="new_is_primary">
                                        Jadikan sebagai alamat utama
                                    </label>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success" id="btn-save-new-address">
                    <i class="bx bx-plus me-1"></i>Tambah Alamat
                </button>
                <button type="button" class="btn btn-primary" id="btn-use-selected-address">
                    <i class="bx bx-check me-1"></i>Gunakan Alamat yang Dipilih
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Midtrans Snap.js -->
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-control-geocoder@1.13.0/dist/Control.Geocoder.js"></script>

<script>
$(document).ready(function() {
    // Initialize: Remove required from all shipping selects on page load
    // They will be set dynamically based on selected shipping type
    $('#instant_store_outlet').prop('required', false);
    $('#delivery_store_outlet').prop('required', false);
    $('#destination_city').prop('required', false);
    
    // Initialize map for new address form in modal
    let mapModal = null;
    let markerModal = null;
    const defaultLat = -7.0051;
    const defaultLng = 110.4381;
    
    // Initialize location dropdowns for new address form
    function initNewAddressLocationDropdowns() {
        const provSel = document.getElementById('new_loc_provinsi_id');
        const kabSel = document.getElementById('new_loc_kabkota_id');
        const kecSel = document.getElementById('new_loc_kecamatan_id');
        const desaSel = document.getElementById('new_loc_desa_id');
        
        fetch('{{ route('api.locations.provinsis') }}')
            .then(r => r.json()).then(json => {
                const $prov = $('#new_loc_provinsi_id');
                (json.data||[]).forEach(it=>{ $prov.append(new Option(it.name, it.id, false, false)); });
                if (window.jQuery && $.fn.select2) { 
                    try { $prov.select2('destroy'); } catch(e){} 
                    $prov.select2({ width: '100%' }); 
                }
            });
        
        $('#new_loc_provinsi_id').on('change select2:select', function(){ loadKabModal(this.value,false); });
        $('#new_loc_kabkota_id').on('change select2:select', function(){ loadKecModal(this.value,false); });
        $('#new_loc_kecamatan_id').on('change select2:select', function(){ loadDesaModal(this.value,false); });
        
        function resetSelect(sel, ph){ sel.innerHTML = `<option value="">${ph}</option>`; }
        
        function loadKabModal(pid, restoring){
            resetSelect(kabSel,'Pilih Kab/Kota'); resetSelect(kecSel,'Pilih Kecamatan'); resetSelect(desaSel,'Pilih Desa/Kelurahan');
            if(!pid) return;
            fetch(`{{ url('/api/locations/kabkotas') }}/${pid}`).then(r=>r.json()).then(j=>{
                const $kab = $('#new_loc_kabkota_id');
                (j.data||[]).forEach(it=>{ $kab.append(new Option(it.name, it.id, false, false)); });
                if (window.jQuery && $.fn.select2) { try { $kab.select2('destroy'); } catch(e){} $kab.select2({ width: '100%' }); }
            });
        }
        
        function loadKecModal(kid, restoring){
            resetSelect(kecSel,'Pilih Kecamatan'); resetSelect(desaSel,'Pilih Desa/Kelurahan');
            if(!kid) return;
            fetch(`{{ url('/api/locations/kecamatans') }}/${kid}`).then(r=>r.json()).then(j=>{
                const $kec = $('#new_loc_kecamatan_id');
                (j.data||[]).forEach(it=>{ $kec.append(new Option(it.name, it.id, false, false)); });
                if (window.jQuery && $.fn.select2) { try { $kec.select2('destroy'); } catch(e){} $kec.select2({ width: '100%' }); }
            });
        }
        
        function loadDesaModal(did, restoring){
            resetSelect(desaSel,'Pilih Desa/Kelurahan');
            if(!did) return;
            fetch(`{{ url('/api/locations/desas') }}/${did}`).then(r=>r.json()).then(j=>{
                const $desa = $('#new_loc_desa_id');
                (j.data||[]).forEach(it=>{ $desa.append(new Option(it.name, it.id, false, false)); });
                if (window.jQuery && $.fn.select2) { try { $desa.select2('destroy'); } catch(e){} $desa.select2({ width: '100%' }); }
            });
        }
        
        $('#new_loc_provinsi_id, #new_loc_kabkota_id, #new_loc_kecamatan_id, #new_loc_desa_id').select2({ width: '100%' });
    }
    
    // Initialize map for modal
    function initMapModal() {
        if (mapModal) {
            mapModal.remove();
        }
        
        const initialLat = parseFloat(document.getElementById('new_latitude').value) || defaultLat;
        const initialLng = parseFloat(document.getElementById('new_longitude').value) || defaultLng;
        
        mapModal = L.map('map-modal').setView([initialLat, initialLng], 15);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19
        }).addTo(mapModal);
        
        const customIcon = L.icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png',
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        });
        
        markerModal = L.marker([initialLat, initialLng], {
            draggable: true,
            icon: customIcon
        }).addTo(mapModal);
        
        markerModal.on('dragend', function() {
            const position = markerModal.getLatLng();
            document.getElementById('new_latitude').value = position.lat;
            document.getElementById('new_longitude').value = position.lng;
            updateLocationStatusModal('Lokasi diperbarui');
        });
        
        mapModal.on('click', function(event) {
            const clickedLocation = event.latlng;
            markerModal.setLatLng(clickedLocation);
            document.getElementById('new_latitude').value = clickedLocation.lat;
            document.getElementById('new_longitude').value = clickedLocation.lng;
            updateLocationStatusModal('Lokasi dipilih');
        });
        
        // Address search for modal
        let geocoderModal = null;
        if (typeof L.Control.Geocoder !== 'undefined') {
            geocoderModal = L.Control.Geocoder.nominatim();
        }
        
        const addressSearchInputModal = document.getElementById('address-search-modal');
        const clearSearchBtnModal = document.getElementById('btn-clear-search-modal');
        let searchTimeoutModal;
        
        if (addressSearchInputModal) {
            addressSearchInputModal.addEventListener('input', function() {
                const query = this.value.trim();
                if (query.length < 3) {
                    clearSearchBtnModal.style.display = 'none';
                    return;
                }
                clearSearchBtnModal.style.display = 'inline-block';
                clearTimeout(searchTimeoutModal);
                searchTimeoutModal = setTimeout(function() {
                    if (geocoderModal && query.length >= 3) {
                        performGeocodeSearchModal(query);
                    }
                }, 500);
            });
            
            addressSearchInputModal.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const query = this.value.trim();
                    if (query.length >= 3) {
                        performGeocodeSearchModal(query);
                    }
                }
            });
            
            clearSearchBtnModal.addEventListener('click', function() {
                addressSearchInputModal.value = '';
                this.style.display = 'none';
            });
        }
        
        function performGeocodeSearchModal(query) {
            if (!geocoderModal) return;
            
            geocoderModal.geocode(query, function(results) {
                if (results && results.length > 0) {
                    const result = results[0];
                    const lat = result.center.lat;
                    const lng = result.center.lng;
                    
                    markerModal.setLatLng([lat, lng]);
                    mapModal.setView([lat, lng], 15);
                    document.getElementById('new_latitude').value = lat;
                    document.getElementById('new_longitude').value = lng;
                    updateLocationStatusModal('Lokasi ditemukan');
                }
            });
        }
    }
    
    function updateLocationStatusModal(message, type = 'info') {
        const statusEl = document.getElementById('location-status-modal');
        if (statusEl) {
            statusEl.textContent = message;
            statusEl.className = 'ms-2 small ' + (type === 'error' ? 'text-danger' : 'text-success');
        }
    }
    
    // GPS button for modal
    document.getElementById('btn-get-location-modal').addEventListener('click', function() {
        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<i class="bx bx-loader-alt bx-spin me-1"></i>Mengambil lokasi...';
        updateLocationStatusModal('Mengambil lokasi GPS...');
        
        if (!navigator.geolocation) {
            updateLocationStatusModal('Geolocation tidak didukung oleh browser Anda', 'error');
            btn.disabled = false;
            btn.innerHTML = '<i class="bx bx-crosshair me-1"></i>Gunakan Lokasi GPS Saya';
            return;
        }
        
        navigator.geolocation.getCurrentPosition(
            function(position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                
                if (markerModal) {
                    markerModal.setLatLng([lat, lng]);
                    mapModal.setView([lat, lng], 15);
                }
                document.getElementById('new_latitude').value = lat;
                document.getElementById('new_longitude').value = lng;
                
                updateLocationStatusModal('Lokasi GPS berhasil diambil');
                btn.disabled = false;
                btn.innerHTML = '<i class="bx bx-crosshair me-1"></i>Gunakan Lokasi GPS Saya';
            },
            function(error) {
                updateLocationStatusModal('Gagal mengambil lokasi GPS: ' + error.message, 'error');
                btn.disabled = false;
                btn.innerHTML = '<i class="bx bx-crosshair me-1"></i>Gunakan Lokasi GPS Saya';
            },
            {
                enableHighAccuracy: false,
                timeout: 15000,
                maximumAge: 60000
            }
        );
    });
    
    // Initialize when modal is shown
    $('#addressModal').on('shown.bs.modal', function() {
        // Initialize location dropdowns
        initNewAddressLocationDropdowns();
        
        // Initialize map
        setTimeout(function() {
            if (typeof L !== 'undefined') {
                initMapModal();
            } else {
                const checkLeaflet = setInterval(function() {
                    if (typeof L !== 'undefined') {
                        clearInterval(checkLeaflet);
                        initMapModal();
                    }
                }, 100);
            }
        }, 300);
    });
    
    // Clean up map when modal is hidden
    $('#addressModal').on('hidden.bs.modal', function() {
        if (mapModal) {
            mapModal.remove();
            mapModal = null;
            markerModal = null;
        }
    });
    
    // Address Management
    $('#btn-change-address').on('click', function() {
        // Show modal to select/change address
        $('#addressModal').modal('show');
    });
    
    // Update hidden fields when address is selected
    function updateFormFieldsFromAddress(address) {
        if (address) {
            const defaultUser = {
                name: {!! json_encode($user['name'] ?? '') !!},
                phone: {!! json_encode($user['phone'] ?? '') !!},
                address: {!! json_encode($user['address'] ?? '') !!},
                city: {!! json_encode($user['city'] ?? '') !!},
                postal_code: {!! json_encode($user['postal_code'] ?? '') !!},
                latitude: {!! json_encode($user['latitude'] ?? '') !!},
                longitude: {!! json_encode($user['longitude'] ?? '') !!}
            };
            
            $('#first_name').val(address.recipient_name || defaultUser.name);
            $('#phone').val(address.recipient_phone || defaultUser.phone);
            $('#address').val(address.address || defaultUser.address);
            $('#city').val(address.loc_kabkota_name || address.city || defaultUser.city);
            $('#postal_code').val(address.postal_code || defaultUser.postal_code);
            $('#selected_address_id').val(address.id || '');
            $('#customer_latitude').val(address.latitude || defaultUser.latitude);
            $('#customer_longitude').val(address.longitude || defaultUser.longitude);
        }
    }
    
    // Address option click
    $('.address-option').on('click', function() {
        const addressId = $(this).data('address-id');
        $('input[name="selected_address"][value="' + addressId + '"]').prop('checked', true);
        $('.address-option').removeClass('border-primary');
        $(this).addClass('border-primary');
    });
    
    // Use selected address
    $('#btn-use-selected-address').on('click', function() {
        const selectedId = $('input[name="selected_address"]:checked').val();
        if (selectedId) {
            // Get address data from the selected card
            const addressCard = $('.address-option[data-address-id="' + selectedId + '"]');
            const addressData = {
                id: selectedId,
                recipient_name: addressCard.find('h6').text().trim(),
                recipient_phone: addressCard.find('p.text-muted').text().replace('(+62)', '').trim(),
                address: addressCard.find('p.small').text().split(',')[0].trim(),
                // You might need to parse more data from the card or fetch from API
            };
            
            // Update form fields
            updateFormFieldsFromAddress(addressData);
            
            // Reload page with selected address
            window.location.href = '{{ route("checkout") }}?address_id=' + selectedId;
        } else {
            alert('Silakan pilih alamat terlebih dahulu');
        }
    });
    
    // Save new address
    $('#btn-save-new-address').on('click', function() {
        const formData = {
            _token: '{{ csrf_token() }}',
            label: $('#new_address_label').val(),
            recipient_name: $('#new_recipient_name').val(),
            recipient_phone: $('#new_recipient_phone').val(),
            address: $('#new_address').val(),
            city: $('#new_city').val(),
            postal_code: $('#new_postal_code').val(),
            notes: $('#new_notes').val(),
            is_primary: $('#new_is_primary').is(':checked') ? 1 : 0,
            loc_provinsi_id: $('#new_loc_provinsi_id').val(),
            loc_kabkota_id: $('#new_loc_kabkota_id').val(),
            loc_kecamatan_id: $('#new_loc_kecamatan_id').val(),
            loc_desa_id: $('#new_loc_desa_id').val(),
            latitude: $('#new_latitude').val(),
            longitude: $('#new_longitude').val(),
        };
        
        if (!formData.recipient_name || !formData.recipient_phone || !formData.address) {
            alert('Silakan lengkapi nama penerima, nomor telepon, dan alamat');
            return;
        }
        
        if (!formData.loc_provinsi_id || !formData.loc_kabkota_id || !formData.loc_kecamatan_id || !formData.loc_desa_id) {
            alert('Silakan lengkapi semua field lokasi (Provinsi, Kab/Kota, Kecamatan, Desa/Kelurahan)');
            return;
        }
        
        if (!formData.latitude || !formData.longitude) {
            alert('Silakan tentukan lokasi di peta dengan menggunakan GPS, mencari alamat, atau klik pada peta');
            return;
        }
        
        $.ajax({
            url: '{{ route("user.addresses.store") }}',
            method: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    window.location.reload();
                } else {
                    alert(response.message || 'Gagal menyimpan alamat');
                }
            },
            error: function(xhr) {
                let errorMessage = 'Terjadi kesalahan saat menyimpan alamat';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                alert(errorMessage);
            }
        });
    });
    
    // Toggle address form (alternative to modal)
    $('#btn-cancel-address-form').on('click', function() {
        $('#address-form').slideUp();
        $('.address-card').slideDown();
    });
    
    $('#btn-save-address-form').on('click', function() {
        // Validate form
        const recipientName = $('#form_recipient_name').val();
        const recipientPhone = $('#form_recipient_phone').val();
        const address = $('#form_address').val();
        const city = $('#form_city').val();
        
        if (!recipientName || !recipientPhone || !address || !city) {
            alert('Silakan lengkapi semua field yang diperlukan');
            return;
        }
        
        // Update hidden fields
        $('#first_name').val(recipientName);
        $('#phone').val(recipientPhone);
        $('#address').val(address);
        $('#city').val(city);
        $('#postal_code').val($('#form_postal_code').val() || '');
        
        // Save as new address
        const formData = {
            _token: '{{ csrf_token() }}',
            recipient_name: recipientName,
            recipient_phone: recipientPhone,
            address: address,
            city: city,
            postal_code: $('#form_postal_code').val() || '',
            is_primary: true,
        };
        
        $.ajax({
            url: '{{ route("user.addresses.store") }}',
            method: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    window.location.reload();
                } else {
                    alert(response.message || 'Gagal menyimpan alamat');
                }
            },
            error: function(xhr) {
                alert('Terjadi kesalahan saat menyimpan alamat');
            }
        });
    });
    
    // Initialize checkout shipping manager
    // Initialize checkout shipping manager
    window.checkoutShippingManager = new CheckoutShippingManager();
    const storeId = {{ isset($current_store) ? (int)$current_store->id : 1 }};

    // Toggle manual coordinates
    $('#use-manual-coordinates').on('change', function() {
        if ($(this).is(':checked')) {
            $('#manual-coordinates').slideDown();
        } else {
            $('#manual-coordinates').slideUp();
            $('#customer_latitude').val('');
            $('#customer_longitude').val('');
        }
    });

    // Toggle pickup location select based on shipping type
    $('#shipping_type').on('change', function() {
        const val = $(this).val();
        if (val === 'pickup') {
            $('#pickup-location-select').show();
            $('#pickup-info').hide();
            $('#instant-options').hide();
            $('#delivery-options').hide();
            $('#instant-delivery-calculation').hide();
            $('#shipping-methods').hide();
            loadPickupLocations();
        } else if (val === 'instant') {
            $('#pickup-location-select').hide();
            $('#pickup-info').hide();
            $('#instant-options').show();
            $('#delivery-options').hide();
            $('#instant-delivery-calculation').show();
            $('#shipping-methods').hide(); // Hide shipping methods until store/outlet is selected
            // Set required attribute when visible
            $('#instant_store_outlet').prop('required', true);
            // Remove required from other selects
            $('#delivery_store_outlet').prop('required', false);
            $('#destination_city').prop('required', false);
            $('#pickup_location_type').val('');
            $('#pickup_location_id').val('');
            $('#outlet_id').val('');
            // Don't load shipping methods yet - wait for store/outlet selection
        } else if (val === 'delivery') {
            $('#pickup-location-select').hide();
            $('#pickup-info').hide();
            $('#instant-options').hide();
            $('#delivery-options').show();
            $('#instant-delivery-calculation').hide();
            // Set required attribute when visible
            $('#delivery_store_outlet').prop('required', true);
            $('#destination_city').prop('required', true);
            // Remove required from other selects
            $('#instant_store_outlet').prop('required', false);
            $('#pickup_location_type').val('');
            $('#pickup_location_id').val('');
            $('#outlet_id').val('');
        } else {
            $('#pickup-location-select').hide();
            $('#pickup-info').hide();
            $('#instant-options').hide();
            $('#delivery-options').hide();
            $('#instant-delivery-calculation').hide();
            $('#shipping-methods').hide();
            // Remove required from all shipping selects when no type selected
            $('#instant_store_outlet').prop('required', false);
            $('#delivery_store_outlet').prop('required', false);
            $('#destination_city').prop('required', false);
        }
    });

    // Function to calculate distance using Haversine formula
    function calculateDistance(lat1, lon1, lat2, lon2) {
        const R = 6371; // Earth radius in km
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLon = (lon2 - lon1) * Math.PI / 180;
        const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                  Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                  Math.sin(dLon/2) * Math.sin(dLon/2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        return R * c;
    }

    // Function to load and display pickup locations (stores and outlets) sorted by distance
    function loadPickupLocations() {
        if (!userCoordinates) {
            $('#pickup-locations-list').html(`
                <div class="col-12">
                    <div class="alert alert-warning">
                        <i class="bx bx-info-circle me-2"></i>
                        Koordinat alamat Anda tidak tersedia. Silakan isi alamat lengkap dan koordinat untuk melihat lokasi terdekat.
                    </div>
                </div>
            `);
            return;
        }

        const locations = [];

        // Add stores
        storesData.forEach(store => {
            if (store.latitude && store.longitude) {
                const distance = calculateDistance(
                    userCoordinates.lat,
                    userCoordinates.lng,
                    parseFloat(store.latitude),
                    parseFloat(store.longitude)
                );
                locations.push({
                    type: 'store',
                    id: store.id,
                    name: store.name || '',
                    address: store.full_address || (store.address || '') + ', ' + (store.city || ''),
                    phone: store.phone || '-',
                    distance: distance,
                    latitude: parseFloat(store.latitude),
                    longitude: parseFloat(store.longitude),
                    operating_hours: null
                });
            }
        });

        // Add outlets
        outletsData.forEach(outlet => {
            if (outlet.latitude && outlet.longitude) {
                const distance = calculateDistance(
                    userCoordinates.lat,
                    userCoordinates.lng,
                    parseFloat(outlet.latitude),
                    parseFloat(outlet.longitude)
                );
                locations.push({
                    type: 'outlet',
                    id: outlet.id,
                    name: outlet.name || '',
                    address: outlet.full_address || (outlet.address || '') + ', ' + (outlet.city || ''),
                    phone: outlet.phone || '-',
                    distance: distance,
                    latitude: parseFloat(outlet.latitude),
                    longitude: parseFloat(outlet.longitude),
                    operating_hours: outlet.operating_hours,
                    store_name: outlet.store ? (outlet.store.name || '') : ''
                });
            }
        });

        // Sort by distance
        locations.sort((a, b) => a.distance - b.distance);

        // Display locations
        if (locations.length === 0) {
            $('#pickup-locations-list').html(`
                <div class="col-12">
                    <div class="alert alert-info">
                        <i class="bx bx-info-circle me-2"></i>
                        Tidak ada store atau outlet yang tersedia dengan koordinat.
                    </div>
                </div>
            `);
            return;
        }

        let html = '';
        locations.forEach((location, index) => {
            const distanceText = location.distance < 1
                ? (location.distance * 1000).toFixed(0) + ' m'
                : location.distance.toFixed(2) + ' km';

            const badgeColor = index === 0 ? 'success' : 'secondary';
            const badgeText = index === 0 ? 'TERDEKAT' : '';
            
            // Escape HTML to prevent XSS and syntax errors
            const escapeHtml = (text) => {
                if (!text) return '';
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            };
            
            const locationName = escapeHtml(location.name || '');
            const locationAddress = escapeHtml(location.address || '');
            const locationPhone = escapeHtml(location.phone || '-');
            const storeName = escapeHtml(location.store_name || '');
            const iconClass = location.type === 'store' ? 'bx-store' : 'bx-map-pin';
            const storeNameHtml = location.type === 'outlet' && storeName ? '<small class="text-muted">' + storeName + '</small>' : '';
            const badgeHtml = badgeText ? '<span class="badge bg-' + badgeColor + '">' + badgeText + '</span>' : '';

            html += '<div class="col-12">' +
                '<div class="card pickup-location-card" ' +
                'data-location-type="' + location.type + '" ' +
                'data-location-id="' + location.id + '" ' +
                'style="cursor: pointer; border: 2px solid #e9ecef; transition: all 0.3s;">' +
                '<div class="card-body p-3">' +
                '<div class="d-flex align-items-start">' +
                '<div class="me-3">' +
                '<div class="bg-light rounded p-2" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">' +
                '<i class="bx ' + iconClass + ' fs-4 text-success"></i>' +
                '</div>' +
                '</div>' +
                '<div class="flex-grow-1">' +
                '<div class="d-flex justify-content-between align-items-start mb-2">' +
                '<div>' +
                '<h6 class="mb-1 fw-bold">' + locationName + '</h6>' +
                storeNameHtml +
                '</div>' +
                badgeHtml +
                '</div>' +
                '<p class="mb-1 small text-muted">' +
                '<i class="bx bx-map me-1"></i>' + locationAddress +
                '</p>' +
                '<p class="mb-1 small text-muted">' +
                '<i class="bx bx-phone me-1"></i>' + locationPhone +
                '</p>' +
                '<p class="mb-0">' +
                '<span class="badge bg-success">' +
                '<i class="bx bx-map-pin me-1"></i>' + distanceText +
                '</span>' +
                '</p>' +
                '</div>' +
                '</div>' +
                '</div>' +
                '</div>';
        });

        $('#pickup-locations-list').html(html);

        // Bind click events
        $('.pickup-location-card').on('click', function() {
            $('.pickup-location-card').css('border-color', '#e9ecef').removeClass('selected');
            $(this).css('border-color', '#28a745').addClass('selected');

            const locationType = $(this).data('location-type');
            const locationId = $(this).data('location-id');
            const location = locations.find(l => l.type === locationType && l.id === locationId);

            if (location) {
                $('#pickup_location_type').val(locationType);
                $('#pickup_location_id').val(locationId);
                $('#outlet_id').val(locationType === 'outlet' ? locationId : '');

                // Update pickup info
                $('#pickup-location-name').text(location.name);
                $('#pickup-location-address').text(location.address);
                $('#pickup-location-phone').text(location.phone);
                const distanceText = location.distance < 1
                    ? (location.distance * 1000).toFixed(0) + ' meter'
                    : location.distance.toFixed(2) + ' km';
                $('#pickup-location-distance').text(distanceText);

                if (location.operating_hours && typeof location.operating_hours === 'object') {
                    let hoursText = '';
                    Object.keys(location.operating_hours).forEach(day => {
                        const hours = location.operating_hours[day];
                        if (hours.open && hours.close) {
                            const dayName = day.charAt(0).toUpperCase() + day.slice(1);
                            hoursText += dayName + ': ' + hours.open + ' - ' + hours.close + '<br>';
                        }
                    });
                    if (hoursText) {
                        $('#pickup-location-hours').html(hoursText);
                        $('#pickup-location-hours-container').show();
                    } else {
                        $('#pickup-location-hours-container').hide();
                    }
                } else {
                    $('#pickup-location-hours-container').hide();
                }

                $('#pickup-info').show();

                // Update shipping cost to 0 for pickup
                $('#shipping-cost').val(0);
                // Get pickup shipping method ID from PHP variable
                const pickupMethodId = {{ $pickupMethod->id ?? 'null' }};
                if (pickupMethodId) {
                    $('#shipping-method-id').val(pickupMethodId);
                } else {
                    // Fallback: set to 'pickup' string, backend will handle conversion
                    $('#shipping-method-id').val('pickup');
                }
                if (window.checkoutShippingManager) {
                    window.checkoutShippingManager.updateOrderTotal(0);
                }
                $('#order-shipping-method').text('Ambil Sendiri - ' + location.name);
            }
        });
    }

    // Calculate distance for instant delivery (global scope)
    window.calculatedDistance = null;
    window.calculatedShippingCost = null;
    let calculatedDistance = window.calculatedDistance;
    let calculatedShippingCost = window.calculatedShippingCost;

    // Store stores and outlets data
    const storesData = @json($stores ?? []);
    const outletsData = @json($outlets ?? []);
    const userCoordinates = @if(isset($user['latitude']) && isset($user['longitude']) && $user['latitude'] && $user['longitude'])
        { lat: {!! json_encode($user['latitude']) !!}, lng: {!! json_encode($user['longitude']) !!} }
    @else
        null
    @endif;

    // Auto-calculate distance if user has saved coordinates
    @if(isset($user['latitude']) && isset($user['longitude']) && $user['latitude'] && $user['longitude'])
    $(document).ready(function() {
        // Auto-calculate on page load if user has coordinates
        setTimeout(function() {
            $('#btn-calculate-distance').click();
        }, 500);
    });
    @endif

    // Function to calculate distance and shipping cost for instant delivery
    function calculateDistanceAndShippingInstant() {
        const address = $('#address').val();
        const city = $('#city').val();
        const storeOutlet = $('#instant_store_outlet').val();

        if (!storeOutlet) {
            $('#distance-info').html('<small class="text-muted">Pilih store/outlet dan isi alamat lengkap di atas untuk menghitung jarak dan ongkos kirim</small>');
            return;
        }

        if (!address || !city) {
            $('#distance-info').html('<div class="alert alert-warning mb-0"><i class="bx bx-info-circle me-1"></i>Silakan isi alamat lengkap dan kota terlebih dahulu</div>');
            return;
        }

        // Get selected store/outlet coordinates
        const selectedOption = $('#instant_store_outlet option:selected');
        const storeLat = selectedOption.data('lat');
        const storeLng = selectedOption.data('lng');
        const storeType = selectedOption.data('type');
        const storeId = selectedOption.data('id');

        if (!storeLat || !storeLng) {
            $('#distance-info').html('<div class="alert alert-warning mb-0"><i class="bx bx-info-circle me-1"></i>Koordinat store/outlet tidak ditemukan</div>');
            return;
        }

        @if(isset($user['latitude']) && isset($user['longitude']) && $user['latitude'] && $user['longitude'])
        const latitude = {!! json_encode($user['latitude']) !!};
        const longitude = {!! json_encode($user['longitude']) !!};
        @else
        const latitude = $('#customer_latitude').val();
        const longitude = $('#customer_longitude').val();
        @endif

        if (!latitude || !longitude) {
            $('#distance-info').html('<div class="alert alert-warning mb-0"><i class="bx bx-info-circle me-1"></i>Koordinat alamat Anda tidak tersedia. Pastikan alamat sudah diisi dengan lengkap.</div>');
            return;
        }

        // Show loading state
        $('#distance-info').html('<div class="alert alert-info mb-0"><i class="bx bx-loader-alt bx-spin me-1"></i>Menghitung jarak dan ongkos kirim...</div>');

        $.ajax({
            url: '{{ route("distance.calculate_instant_shipping") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                address: address,
                city: city,
                latitude: latitude || null,
                longitude: longitude || null,
                store_type: storeType,
                store_id: storeId,
                store_latitude: storeLat,
                store_longitude: storeLng
            },
            success: function(response) {
                if (response.success) {
                    window.calculatedDistance = response.distance_km;
                    window.calculatedShippingCost = response.shipping_cost;
                    calculatedDistance = window.calculatedDistance;
                    calculatedShippingCost = window.calculatedShippingCost;

                    const distanceInfo = '<div class="alert alert-success mb-0">' +
                        '<div class="row align-items-center">' +
                        '<div class="col-md-6">' +
                        '<strong><i class="bx bx-map me-1"></i>Jarak:</strong> ' + response.formatted_distance +
                        '</div>' +
                        '<div class="col-md-6">' +
                        '<strong><i class="bx bx-dollar me-1"></i>Ongkos Kirim:</strong> ' + response.formatted_cost +
                        '</div>' +
                        '</div>' +
                        '<small class="text-muted d-block mt-2">' +
                        'Tarif: Rp 5.000 per kilometer | ' +
                        '<strong>Total: ' + response.formatted_cost + '</strong>' +
                        '</small>' +
                        '</div>';
                    $('#distance-info').html(distanceInfo);

                    // Load instant shipping methods after calculation and show the container
                    if (window.checkoutShippingManager) {
                        window.checkoutShippingManager.loadInstantShippingMethods();
                        $('#shipping-methods').show(); // Show shipping methods after calculation
                    }
                } else {
                    $('#distance-info').html('<div class="alert alert-warning mb-0">' +
                        '<i class="bx bx-info-circle me-1"></i>' + response.error +
                        (response.suggestion ? '<br><small>' + response.suggestion + '</small>' : '') +
                        '</div>');
                    // Keep shipping methods hidden if calculation failed
                    $('#shipping-methods').hide();
                }
            },
            error: function(xhr) {
                let errorMessage = 'Terjadi kesalahan saat menghitung jarak.';
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMessage = xhr.responseJSON.error;
                }
                $('#distance-info').html('<div class="alert alert-danger mb-0"><i class="bx bx-error me-1"></i>' + errorMessage + '</div>');
                // Keep shipping methods hidden on error
                $('#shipping-methods').hide();
            }
        });
    }

    // Function to calculate distance and shipping cost
    function calculateDistanceAndShipping() {
        const address = $('#address').val();
        const city = $('#city').val();
        const storeOutlet = $('#delivery_store_outlet').val();

        if (!storeOutlet) {
            $('#distance-info').html('<small class="text-muted">Pilih store/outlet dan isi alamat lengkap di atas untuk menghitung jarak dan ongkos kirim</small>');
            return;
        }

        if (!address || !city) {
            $('#distance-info').html('<div class="alert alert-warning mb-0"><i class="bx bx-info-circle me-1"></i>Silakan isi alamat lengkap dan kota terlebih dahulu</div>');
            return;
        }

        // Get selected store/outlet coordinates
        const selectedOption = $('#delivery_store_outlet option:selected');
        const storeLat = selectedOption.data('lat');
        const storeLng = selectedOption.data('lng');
        const storeType = selectedOption.data('type');
        const storeId = selectedOption.data('id');

        if (!storeLat || !storeLng) {
            $('#distance-info').html('<div class="alert alert-warning mb-0"><i class="bx bx-info-circle me-1"></i>Koordinat store/outlet tidak ditemukan</div>');
            return;
        }

        @if(isset($user['latitude']) && isset($user['longitude']) && $user['latitude'] && $user['longitude'])
        const latitude = {!! json_encode($user['latitude']) !!};
        const longitude = {!! json_encode($user['longitude']) !!};
        @else
        const latitude = $('#customer_latitude').val();
        const longitude = $('#customer_longitude').val();
        @endif

        if (!latitude || !longitude) {
            $('#distance-info').html('<div class="alert alert-warning mb-0"><i class="bx bx-info-circle me-1"></i>Koordinat alamat Anda tidak tersedia. Pastikan alamat sudah diisi dengan lengkap.</div>');
            return;
        }

        // Show loading state
        $('#distance-info').html('<div class="alert alert-info mb-0"><i class="bx bx-loader-alt bx-spin me-1"></i>Menghitung jarak dan ongkos kirim...</div>');

        $.ajax({
            url: '{{ route("distance.calculate_instant_shipping") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                address: address,
                city: city,
                latitude: latitude || null,
                longitude: longitude || null,
                store_type: storeType,
                store_id: storeId,
                store_latitude: storeLat,
                store_longitude: storeLng
            },
            success: function(response) {
                if (response.success) {
                    window.calculatedDistance = response.distance_km;
                    window.calculatedShippingCost = response.shipping_cost;
                    calculatedDistance = window.calculatedDistance;
                    calculatedShippingCost = window.calculatedShippingCost;

                    const distanceInfo = '<div class="alert alert-success mb-0">' +
                        '<div class="row align-items-center">' +
                        '<div class="col-md-6">' +
                        '<strong><i class="bx bx-map me-1"></i>Jarak:</strong> ' + response.formatted_distance +
                        '</div>' +
                        '<div class="col-md-6">' +
                        '<strong><i class="bx bx-dollar me-1"></i>Ongkos Kirim:</strong> ' + response.formatted_cost +
                        '</div>' +
                        '</div>' +
                        '<small class="text-muted d-block mt-2">' +
                        'Tarif: Rp 5.000 per kilometer | ' +
                        '<strong>Total: ' + response.formatted_cost + '</strong>' +
                        '</small>' +
                        '</div>';
                    $('#distance-info').html(distanceInfo);

                    // Update instant delivery method cost if exists
                    updateInstantDeliveryCost(response.shipping_cost);
                    
                    // Reload shipping methods to update cost
                    if (window.checkoutShippingManager && $('#shipping_type').val() === 'instant') {
                        window.checkoutShippingManager.loadInstantShippingMethods();
                    }
                } else {
                    $('#distance-info').html('<div class="alert alert-warning mb-0">' +
                        '<i class="bx bx-info-circle me-1"></i>' + response.error +
                        (response.suggestion ? '<br><small>' + response.suggestion + '</small>' : '') +
                        '</div>');
                }
            },
            error: function(xhr) {
                let errorMessage = 'Terjadi kesalahan saat menghitung jarak.';
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMessage = xhr.responseJSON.error;
                }
                $('#distance-info').html('<div class="alert alert-danger mb-0"><i class="bx bx-error me-1"></i>' + errorMessage + '</div>');
            }
        });
    }

    // Auto-calculate when store/outlet is selected (for instant)
    $('#instant_store_outlet').on('change', function() {
        const selected = $(this).val();
        if (selected) {
            // Hide shipping methods until calculation is complete
            $('#shipping-methods').hide();
            // Automatically calculate distance and shipping cost
            calculateDistanceAndShippingInstant();
        } else {
            $('#distance-info').html('<small class="text-muted">Pilih store/outlet dan isi alamat lengkap di atas untuk menghitung jarak dan ongkos kirim</small>');
            $('#shipping-methods').hide(); // Hide shipping methods if store/outlet is deselected
        }
    });
    
    // Auto-calculate when store/outlet is selected (for delivery)
    $('#delivery_store_outlet').on('change', function() {
        const selected = $(this).val();
        if (selected) {
            // Automatically calculate distance and shipping cost
            calculateDistanceAndShipping();
        } else {
            $('#distance-info').html('<small class="text-muted">Pilih store/outlet dan isi alamat lengkap di atas untuk menghitung jarak dan ongkos kirim</small>');
        }
    });

    // Also calculate when address or city changes (if store/outlet is already selected)
    $('#address, #city').on('change blur', function() {
        if ($('#shipping_type').val() === 'instant' && $('#instant_store_outlet').val()) {
            calculateDistanceAndShippingInstant();
        } else if ($('#shipping_type').val() === 'delivery' && $('#delivery_store_outlet').val()) {
            calculateDistanceAndShipping();
        }
    });
    
    // Update dropdown with distance when coordinates change
    $('#customer_latitude, #customer_longitude').on('change blur', function() {
        if ($('#shipping_type').val() === 'instant') {
            if (window.checkoutShippingManager) {
                window.checkoutShippingManager.updateStoreOutletDropdownWithDistance('instant_store_outlet');
            }
        } else if ($('#shipping_type').val() === 'delivery') {
            if (window.checkoutShippingManager) {
                window.checkoutShippingManager.updateStoreOutletDropdownWithDistance('delivery_store_outlet');
            }
        }
    });

    // Manual trigger button (optional, can be hidden)
    $('#btn-calculate-distance').on('click', function() {
        if ($('#shipping_type').val() === 'instant') {
            calculateDistanceAndShippingInstant();
        } else {
            calculateDistanceAndShipping();
        }
    });

    // Function to update instant delivery method cost
    function updateInstantDeliveryCost(cost) {
        // Find instant delivery method card and update its cost
        const instantMethodCard = document.querySelector('.shipping-method-card[data-method-type="instant"]');
        if (instantMethodCard) {
            instantMethodCard.dataset.cost = cost;
            const costElement = instantMethodCard.querySelector('.text-success.fw-bold');
            if (costElement) {
                costElement.textContent = 'IDR ' + cost.toLocaleString('id-ID');
            }

            // If this method is selected, update the form
            if (instantMethodCard.classList.contains('selected')) {
                document.getElementById('shipping-cost').value = cost;
                if (window.checkoutShippingManager) {
                    window.checkoutShippingManager.updateOrderTotal(cost);
                }
            }
        } else {
            // If method card doesn't exist yet, reload shipping methods
            if (window.checkoutShippingManager && $('#destination_city').val() === 'Semarang') {
                window.checkoutShippingManager.loadShippingMethods();
            }
        }
    }

    // Add CSS for pickup location cards
    $('<style>').prop('type', 'text/css').html(`
        .pickup-location-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            border-color: #28a745 !important;
        }
        .pickup-location-card.selected {
            background: linear-gradient(135deg, #f8fff9 0%, #e8f5e9 100%);
            border-color: #28a745 !important;
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
        }
    `).appendTo('head');
    
    // Form validation
    $('#checkoutForm').on('submit', function(e) {
        e.preventDefault();
        
        // Remove required from hidden selects before validation
        $('#instant_store_outlet').prop('required', false);
        $('#delivery_store_outlet').prop('required', false);
        $('#destination_city').prop('required', false);
        
        // Set required based on selected shipping type
        const shippingType = $('#shipping_type').val();
        if (!shippingType) {
            showNotification('Silakan pilih metode pengiriman', 'warning');
            return;
        }

        if (shippingType === 'instant') {
            $('#instant_store_outlet').prop('required', true);
        } else if (shippingType === 'delivery') {
            $('#delivery_store_outlet').prop('required', true);
            $('#destination_city').prop('required', true);
        }

        if (shippingType === 'instant') {
            if (!$('#instant_store_outlet').val()) {
                showNotification('Silakan pilih store/outlet pengirim terlebih dahulu', 'warning');
                return;
            }
            if (!window.calculatedShippingCost || window.calculatedShippingCost === null) {
                showNotification('Silakan tunggu perhitungan ongkos kirim selesai', 'warning');
                return;
            }
            // Set hidden fields for instant delivery
            const instantStoreOutlet = $('#instant_store_outlet').val();
            const selectedOption = $('#instant_store_outlet option:selected');
            const storeType = selectedOption.data('type');
            const storeId = selectedOption.data('id');
            $('#instant_store_outlet_hidden').val(storeType);
            $('#instant_store_outlet_id_hidden').val(storeId);
            if (storeType === 'outlet') {
                $('#outlet_id').val(storeId);
            }
        } else if (shippingType === 'delivery') {
            if (!$('#delivery_store_outlet').val()) {
                showNotification('Silakan pilih store/outlet pengirim terlebih dahulu', 'warning');
                return;
            }
            if (!$('#destination_city').val()) {
            showNotification('Silakan pilih kota tujuan', 'warning');
            return;
        }
        } else if (shippingType === 'pickup') {
            const locationType = $('#pickup_location_type').val();
            const locationId = $('#pickup_location_id').val();
            if (!locationType || !locationId) {
                showNotification('Silakan pilih lokasi store/outlet untuk pickup', 'warning');
                return;
            }
            if (locationType === 'outlet') {
                $('#outlet_id').val(locationId);
            }
        }
        
        if (!window.checkoutShippingManager.selectedMethod && shippingType !== 'pickup') {
            showNotification('Silakan pilih metode pengiriman', 'warning');
            return;
        }
        
        // Final check: Remove required from all hidden selects before submit
        if (shippingType === 'pickup') {
            $('#instant_store_outlet').prop('required', false);
            $('#delivery_store_outlet').prop('required', false);
            $('#destination_city').prop('required', false);
        } else if (shippingType === 'instant') {
            $('#delivery_store_outlet').prop('required', false);
            $('#destination_city').prop('required', false);
        } else if (shippingType === 'delivery') {
            $('#instant_store_outlet').prop('required', false);
        }
        
        // Submit order via AJAX
        showLoading();
        $('#place-order-btn')
            .html('<i class="bx bx-loader-alt bx-spin me-2"></i>Memproses Pesanan...')
            .prop('disabled', true);

        $.ajax({
            url: '{{ route("checkout.process") }}',
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                hideLoading();
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
                hideLoading();
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

    function showLoading() {
        $('#loadingOverlay').fadeIn();
    }

    function hideLoading() {
        $('#loadingOverlay').fadeOut();
    }

    // Notification function
    function showNotification(message, type) {
        // Remove existing notifications
        $('.notification').remove();
        
        const notificationHtml = `
            <div class="notification alert alert-${type} alert-dismissible fade show">
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
        this.originCity = 'Semarang';
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
        
        // Shipping method selection
        $(document).on('click', '.shipping-method-card', (e) => this.selectShippingMethod(e));
    }

    async onCityChange() {
        this.destinationCity = $('#destination_city').val();
        
        if (this.destinationCity) {
            await this.loadShippingMethods();
        }
    }

    async loadInstantShippingMethods() {
        // For instant delivery, also load from API to get all instant methods (GoSend, GrabExpress, etc.)
        // But prioritize distance-based calculation if available
        try {
            this.showLoading();
            
            const destinationCity = 'Semarang'; // Instant delivery is only for Semarang
            const originCity = this.originCity || 'Semarang';

            // Fetch shipping methods from API
            const response = await fetch(`/api/shipping/methods?destination_city=${encodeURIComponent(destinationCity)}&origin_city=${encodeURIComponent(originCity)}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();
            
            if (data.success && data.data && data.data.methods) {
                // Filter only instant methods and remove duplicates by code
                const seenCodes = new Set();
                let methods = data.data.methods
                    .filter(method => method.type === 'instant')
                    .filter(method => {
                        if (seenCodes.has(method.code)) {
                            return false; // Skip duplicate
                        }
                        seenCodes.add(method.code);
                        return true;
                    });
                
                // Handle distance-based calculation for instant_delivery
                methods = methods.map(method => {
                    // If instant delivery and distance calculated, update cost for distance-based methods
                    if (method.code === 'instant_delivery' && method.is_distance_based && 
                        window.calculatedShippingCost !== null && window.calculatedShippingCost !== undefined) {
                        method.cost = window.calculatedShippingCost;
                        method.formatted_cost = 'IDR ' + window.calculatedShippingCost.toLocaleString('id-ID');
                        const distanceText = window.calculatedDistance ? ` | Jarak: ${window.calculatedDistance.toFixed(2)} km` : '';
                        method.estimated_text = '1-2 Jam' + distanceText;
                        method.distance_based = true;
                    } else if (method.type === 'instant' && !method.estimated_text) {
                        method.estimated_text = method.estimated_text || '1-2 Jam';
                    }
                    return method;
                });

                // If distance-based instant delivery is not in the list and we have calculated cost, add it
                if (window.calculatedShippingCost !== null && 
                    window.calculatedShippingCost !== undefined &&
                    !methods.find(m => m.code === 'instant_delivery')) {
                    
                    const distanceText = window.calculatedDistance ? ` (${window.calculatedDistance.toFixed(2)} km)` : '';
                    methods.unshift({
                        id: 1,
                        name: 'Pengiriman Instan (Berdasarkan Jarak)',
                        code: 'instant_delivery',
                        type: 'instant',
                        cost: window.calculatedShippingCost,
                        formatted_cost: 'IDR ' + window.calculatedShippingCost.toLocaleString('id-ID'),
                        estimated_days: '1-2 Jam',
                        estimated_text: '1-2 Jam' + (distanceText ? ' | Jarak: ' + distanceText.replace(/[()]/g, '') : ''),
                        fresh_product_score: 100,
                        is_fresh_friendly: true,
                        badge: '🌟 SANGAT BAGUS untuk Kecepatan Pengiriman',
                        badge_type: 'success',
                        distance_based: true
                    });
                }

                this.displayShippingMethods({ methods: methods });
            } else {
                // Fallback to single method if API fails
                const instantCost = window.calculatedShippingCost !== null && window.calculatedShippingCost !== undefined 
                    ? window.calculatedShippingCost 
                    : 22000;
                const distanceText = window.calculatedDistance ? ` (${window.calculatedDistance.toFixed(2)} km)` : '';
                
                this.displayShippingMethods({
                    methods: [{
                        id: 1,
                        name: 'Pengiriman Instan (Berdasarkan Jarak)',
                        code: 'instant_delivery',
                        type: 'instant',
                        cost: instantCost,
                        formatted_cost: 'IDR ' + instantCost.toLocaleString('id-ID'),
                        estimated_days: '1-2 Jam',
                        estimated_text: '1-2 Jam' + (distanceText ? ' | Jarak: ' + distanceText.replace(/[()]/g, '') : ''),
                        fresh_product_score: 100,
                        is_fresh_friendly: true,
                        badge: '🌟 SANGAT BAGUS untuk Kecepatan Pengiriman',
                        badge_type: 'success',
                        distance_based: (window.calculatedShippingCost !== null && window.calculatedShippingCost !== undefined)
                    }]
                });
            }
        } catch (error) {
            console.error('Error loading instant shipping methods:', error);
            this.showError('Failed to load instant shipping methods');
        } finally {
            this.hideLoading();
        }
    }

    async loadShippingMethods() {
        try {
            this.showLoading();
            
            const destinationCity = this.destinationCity;
            const originCity = this.originCity || 'Semarang';

            if (!destinationCity) {
                this.showError('Silakan pilih kota tujuan terlebih dahulu');
                this.hideLoading();
                return;
            }

            // If Semarang and distance calculated, use distance-based cost
            if (destinationCity === 'Semarang' && window.calculatedShippingCost !== null && window.calculatedShippingCost !== undefined) {
                // Wait a bit for distance calculation to complete
                await new Promise(resolve => setTimeout(resolve, 100));
            }

            // Fetch shipping methods from API
            const response = await fetch(`/api/shipping/methods?destination_city=${encodeURIComponent(destinationCity)}&origin_city=${encodeURIComponent(originCity)}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();
            
            if (data.success && data.data && data.data.methods) {
                // Remove duplicates by code (keep first occurrence)
                const seenCodes = new Set();
                let methods = data.data.methods.filter(method => {
                    if (seenCodes.has(method.code)) {
                        return false; // Skip duplicate
                    }
                    seenCodes.add(method.code);
                    return true;
                });

                // Handle instant delivery with distance-based calculation
                methods = methods.map(method => {
                    // If instant delivery and distance calculated, update cost for distance-based methods
                    if (method.type === 'instant' && method.is_distance_based && 
                        window.calculatedShippingCost !== null && window.calculatedShippingCost !== undefined) {
                        method.cost = window.calculatedShippingCost;
                        method.formatted_cost = 'IDR ' + window.calculatedShippingCost.toLocaleString('id-ID');
                        const distanceText = window.calculatedDistance ? ` | Jarak: ${window.calculatedDistance.toFixed(2)} km` : '';
                        method.estimated_text = '1-2 Jam' + distanceText;
                        method.distance_based = true;
                    } else if (method.type === 'instant' && !method.estimated_text) {
                        // Ensure all instant methods have estimated_text
                        method.estimated_text = method.estimated_text || '1-2 Jam';
                    }
                    return method;
                });

                // If instant delivery (distance-based) is not in the list and we have calculated cost, add it
                // Distance-based methods don't have shipping_costs, so they won't appear in API response
                if (destinationCity === 'Semarang' && 
                    window.calculatedShippingCost !== null && 
                    window.calculatedShippingCost !== undefined &&
                    !methods.find(m => m.code === 'instant_delivery')) {
                    
                    const distanceText = window.calculatedDistance ? ` (${window.calculatedDistance.toFixed(2)} km)` : '';
                    methods.unshift({
                        id: 1,
                        name: 'Pengiriman Instan (Berdasarkan Jarak)',
                        code: 'instant_delivery',
                        type: 'instant',
                        cost: window.calculatedShippingCost,
                        formatted_cost: 'IDR ' + window.calculatedShippingCost.toLocaleString('id-ID'),
                        estimated_days: '1-2 Jam',
                        estimated_text: '1-2 Jam' + (distanceText ? ' | Jarak: ' + distanceText.replace(/[()]/g, '') : ''),
                        fresh_product_score: 100,
                        is_fresh_friendly: true,
                        badge: '🌟 SANGAT BAGUS untuk Kecepatan Pengiriman',
                        badge_type: 'success',
                        distance_based: true
                    });
                }

                this.displayShippingMethods({ methods: methods });
            } else {
                // Fallback to mock data if API fails
                console.warn('API failed, using fallback mock data');
                const mockResponse = await this.getMockShippingResponse(destinationCity);
                if (mockResponse.success) {
                    this.displayShippingMethods(mockResponse.data);
                } else {
                    this.showError(data.message || 'Gagal memuat metode pengiriman');
                }
            }
        } catch (error) {
            console.error('Error loading shipping methods:', error);
            // Fallback to mock data on error
            try {
                const mockResponse = await this.getMockShippingResponse(this.destinationCity);
                if (mockResponse.success) {
                    this.displayShippingMethods(mockResponse.data);
                } else {
                    this.showError('Failed to load shipping methods');
                }
            } catch (fallbackError) {
                this.showError('Failed to load shipping methods');
            }
        } finally {
            this.hideLoading();
        }
    }

    async getMockShippingResponse(destinationCity) {
        // Check if distance has been calculated
        let instantCost = 22000; // Default cost
        let distanceText = '';

        if (window.calculatedShippingCost !== null && window.calculatedShippingCost !== undefined) {
            instantCost = window.calculatedShippingCost;
            distanceText = window.calculatedDistance ? ` (${window.calculatedDistance.toFixed(2)} km)` : '';
        }

        // Mock data based on destination
        const responses = {
            'Semarang': {
                success: true,
                data: {
                    methods: [
                        {
                            id: 1,
                            name: 'Pengiriman Instan (Berdasarkan Jarak)',
                            code: 'instant_delivery',
                            type: 'instant',
                            cost: instantCost,
                            formatted_cost: 'IDR ' + instantCost.toLocaleString('id-ID'),
                            estimated_days: '1-2 Jam',
                            estimated_text: '1-2 Jam' + (distanceText ? ' | Jarak: ' + distanceText.replace(/[()]/g, '') : ''),
                            fresh_product_score: 100,
                            is_fresh_friendly: true,
                            badge: '🌟 SANGAT BAGUS untuk Produk Masa Simpan Terbatas',
                            badge_type: 'success',
                            distance_based: (window.calculatedShippingCost !== null && window.calculatedShippingCost !== undefined)
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
                            badge: '✅ BAGUS untuk Produk Masa Simpan Terbatas',
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
                            badge: '✅ BAGUS untuk Produk Masa Simpan Terbatas',
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
                        warning: '⚠️ Risiko tinggi untuk produk masa simpan terbatas',
                        warning_type: 'danger'
                    }
                ]
            }
        };
    }

    displayShippingMethods(data) {
        const container = document.getElementById('shipping-methods');
        
        let html = '<h6 class="mb-3 fw-bold">Metode Pengiriman Tersedia</h6>';
        html += '<div class="row">';
        
        data.methods.forEach(method => {
            const isSelected = this.selectedMethod && this.selectedMethod.id == method.id;
            const selectedClass = isSelected ? 'selected' : '';
            
            html += `
                <div class="col-12 mb-3">
                    <div class="card shipping-method-card ${selectedClass}" 
                         data-method-id="${method.id}" 
                         data-method-type="${method.type}"
                         data-cost="${method.cost}"
                         style="position: relative;">
                        <div class="card-body p-3">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <div class="bg-light rounded p-2" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                                        <i class="bx bx-truck fs-4 text-success"></i>
                                    </div>
                                </div>
                                <div class="col">
                                    <h6 class="mb-1 fw-bold">${method.name}</h6>
                                    <small class="text-muted d-block">${method.estimated_text}</small>
                                    ${method.distance_based ? '<small class="text-info d-block"><i class="bx bx-map me-1"></i>Berdasarkan jarak aktual</small>' : ''}
                                    ${method.badge ? '<div class="mt-1"><span class="badge bg-' + method.badge_type + '">' + method.badge + '</span></div>' : ''}
                                    ${method.warning ? '<div class="mt-1"><span class="badge bg-' + method.warning_type + '">' + method.warning + '</span></div>' : ''}
                                </div>
                                <div class="col-auto text-end">
                                    <h6 class="mb-0 text-success fw-bold">${method.formatted_cost}</h6>
                                    ${method.distance_based ? '<small class="text-muted d-block">Rp 5.000/km</small>' : ''}
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
        const methodType = card.dataset.methodType;
        let methodCost = parseInt(card.dataset.cost);
        const methodName = card.querySelector('h6').textContent;

        // If instant delivery and distance calculated, use calculated cost
        if (methodType === 'instant' && window.calculatedShippingCost !== null && window.calculatedShippingCost !== undefined) {
            methodCost = window.calculatedShippingCost;
            // Update the card's data attribute
            card.dataset.cost = methodCost;
        }
        
        // Update selected method
        this.selectedMethod = {
            id: methodId,
            cost: methodCost,
            name: methodName,
            type: methodType
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
        const total = subtotal - discount + (shippingCost || 0);
        
        // Update Order Summary by ID
        const shippingElement = document.getElementById('order-shipping');
        if (shippingElement) {
            if (shippingCost === null || shippingCost === undefined) {
                shippingElement.textContent = '-';
            } else if (shippingCost === 0) {
                shippingElement.textContent = 'GRATIS';
            } else {
                shippingElement.textContent = 'IDR ' + shippingCost.toLocaleString('id-ID');
            }
        }
        
        const totalElement = document.getElementById('order-total');
        if (totalElement) {
            totalElement.textContent = 'IDR ' + total.toLocaleString('id-ID');
        }
        
        // Also update hidden form fields
        const shippingCostField = document.getElementById('shipping-cost');
        if (shippingCostField) {
            shippingCostField.value = shippingCost || 0;
        }
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
}

// Checkout shipping integration
class CheckoutShippingManager extends ShippingManager {
    constructor() {
        super();
        this.originCity = 'Semarang';
        this.initCheckoutSpecific();
        
        // Initialize order summary - set shipping to empty if no method selected
        this.initializeOrderSummary();

        // Initialize distance calculation variables
        if (typeof calculatedDistance === 'undefined') {
            window.calculatedDistance = null;
            window.calculatedShippingCost = null;
        }
    }

    initCheckoutSpecific() {
        this.setupPickupLogic();
        this.bindCheckoutEvents();
    }
    
    initializeOrderSummary() {
        // Reset shipping cost and method to empty if no method is selected
        if (!this.selectedMethod) {
            const shippingElement = document.getElementById('order-shipping');
            if (shippingElement) {
                shippingElement.textContent = '-';
            }
            const methodElement = document.getElementById('order-shipping-method');
            if (methodElement) {
                methodElement.textContent = '-';
            }
            // Update total to subtotal only (no shipping)
            const subtotal = {{ $totals['subtotal'] ?? 0 }};
            const discount = {{ $totals['discount'] ?? 0 }};
            const total = subtotal - discount;
            const totalElement = document.getElementById('order-total');
            if (totalElement) {
                totalElement.textContent = 'IDR ' + total.toLocaleString('id-ID');
            }
            // Reset hidden shipping cost field
            const shippingCostField = document.getElementById('shipping-cost');
            if (shippingCostField) {
                shippingCostField.value = 0;
            }
        }
    }

    setupPickupLogic() {
        $('#shipping_type').on('change', (e) => {
            const shippingType = e.target.value;
            
            if (shippingType === 'pickup') {
                this.showPickupOption();
            } else if (shippingType === 'instant') {
                this.showInstantOption();
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
        
        // Remove required from all shipping selects
        $('#instant_store_outlet').prop('required', false);
        $('#delivery_store_outlet').prop('required', false);
        $('#destination_city').prop('required', false);
        
        this.selectPickupMethod();
    }

    showInstantOption() {
        $('#pickup-info').hide();
        $('#pickup-location-select').hide();
        $('#instant-options').show();
        $('#delivery-options').hide();
        $('#instant-delivery-calculation').show();
        $('#shipping-methods').hide(); // Hide shipping methods until store/outlet is selected
        
        // Set required attribute when visible
        $('#instant_store_outlet').prop('required', true);
        // Remove required from other selects
        $('#delivery_store_outlet').prop('required', false);
        $('#destination_city').prop('required', false);
        
        this.destinationCity = 'Semarang'; // Set as Semarang for instant
        this.selectedMethod = null;
        
        // Reset shipping cost to empty (not 0)
        const shippingElement = document.getElementById('order-shipping');
        if (shippingElement) {
            shippingElement.textContent = '-';
        }
        const methodElement = document.getElementById('order-shipping-method');
        if (methodElement) {
            methodElement.textContent = '-';
        }
        this.updateOrderTotal(null); // Pass null to indicate no shipping method selected

        // Reset distance calculation
        window.calculatedDistance = null;
        window.calculatedShippingCost = null;

        // Reset store/outlet selection
        $('#instant_store_outlet').val('');
        $('#distance-info').html('<small class="text-muted">Pilih store/outlet dan isi alamat lengkap di atas untuk menghitung jarak dan ongkos kirim</small>');
        
        // Update dropdown with distance labels
        this.updateStoreOutletDropdownWithDistance('instant_store_outlet');
        
        // Don't load shipping methods yet - wait for store/outlet selection
    }

    showDeliveryOption() {
        $('#pickup-info').hide();
        $('#pickup-location-select').hide();
        $('#instant-options').hide();
        $('#delivery-options').show();
        $('#instant-delivery-calculation').hide(); // Hide until city is selected
        $('#shipping-methods').hide();
        
        // Set required attribute when visible
        $('#delivery_store_outlet').prop('required', true);
        $('#destination_city').prop('required', true);
        // Remove required from other selects
        $('#instant_store_outlet').prop('required', false);
        
        this.destinationCity = null;
        this.selectedMethod = null;
        
        // Reset shipping cost to empty (not 0)
        const shippingElement = document.getElementById('order-shipping');
        if (shippingElement) {
            shippingElement.textContent = '-';
        }
        const methodElement = document.getElementById('order-shipping-method');
        if (methodElement) {
            methodElement.textContent = '-';
        }
        this.updateOrderTotal(null); // Pass null to indicate no shipping method selected

        // Reset distance calculation
        window.calculatedDistance = null;
        window.calculatedShippingCost = null;

        // Reset store/outlet selection
        $('#delivery_store_outlet').val('');
        $('#distance-info').html('<small class="text-muted">Pilih store/outlet dan isi alamat lengkap di atas untuk menghitung jarak dan ongkos kirim</small>');
        
        // Update dropdown with distance labels
        this.updateStoreOutletDropdownWithDistance('delivery_store_outlet');
    }

    hideAllOptions() {
        $('#pickup-info').hide();
        $('#instant-options').hide();
        $('#delivery-options').hide();
        $('#shipping-methods').hide();
        
        // Remove required from all shipping selects
        $('#instant_store_outlet').prop('required', false);
        $('#delivery_store_outlet').prop('required', false);
        $('#destination_city').prop('required', false);
        
        // Reset shipping cost to empty
        this.selectedMethod = null;
        const shippingElement = document.getElementById('order-shipping');
        if (shippingElement) {
            shippingElement.textContent = '-';
        }
        const methodElement = document.getElementById('order-shipping-method');
        if (methodElement) {
            methodElement.textContent = '-';
        }
        this.updateOrderTotal(null);
    }

    updateStoreOutletDropdownWithDistance(dropdownId) {
        const dropdown = $('#' + dropdownId);
        if (!dropdown.length) return;
        
        // Get user coordinates
        @if(isset($user['latitude']) && isset($user['longitude']) && $user['latitude'] && $user['longitude'])
        const userLat = {!! json_encode($user['latitude']) !!};
        const userLng = {!! json_encode($user['longitude']) !!};
        @else
        const userLat = $('#customer_latitude').val();
        const userLng = $('#customer_longitude').val();
        @endif
        
        if (!userLat || !userLng) {
            // If no coordinates, keep original options without distance
            return;
        }
        
        // Define calculateDistance function if not available
        const calcDist = function(lat1, lon1, lat2, lon2) {
            const R = 6371; // Earth radius in km
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLon = (lon2 - lon1) * Math.PI / 180;
            const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                      Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                      Math.sin(dLon/2) * Math.sin(dLon/2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
            return R * c;
        };
        
        // Get all options except the first (placeholder)
        const options = [];
        dropdown.find('option').each(function(index) {
            if (index === 0) return; // Skip placeholder
            const $option = $(this);
            const lat = parseFloat($option.data('lat'));
            const lng = parseFloat($option.data('lng'));
            
            if (lat && lng) {
                // Calculate distance using Haversine formula
                const distance = calcDist(parseFloat(userLat), parseFloat(userLng), lat, lng);
                options.push({
                    element: $option,
                    distance: distance,
                    value: $option.val(),
                    text: $option.text(),
                    type: $option.data('type'),
                    id: $option.data('id'),
                    lat: lat,
                    lng: lng
                });
            } else {
                options.push({
                    element: $option,
                    distance: Infinity,
                    value: $option.val(),
                    text: $option.text(),
                    type: $option.data('type'),
                    id: $option.data('id'),
                    lat: null,
                    lng: null
                });
            }
        });
        
        // Sort by distance
        options.sort((a, b) => a.distance - b.distance);
        
        // Clear dropdown (keep placeholder)
        dropdown.find('option:not(:first)').remove();
        
        // Add sorted options with distance labels
        options.forEach((option, index) => {
            let distanceText = '';
            if (option.distance !== Infinity) {
                if (option.distance < 1) {
                    distanceText = ` (${(option.distance * 1000).toFixed(0)} m)`;
                } else {
                    distanceText = ` (${option.distance.toFixed(2)} km)`;
                }
            }
            
            const isNearest = index === 0 && option.distance !== Infinity;
            const nearestLabel = isNearest ? ' ⭐ TERDEKAT' : '';
            
            // Extract original text without existing distance labels
            let originalText = option.text;
            // Remove any existing distance labels (format: " (X km)" or " (X m)")
            originalText = originalText.replace(/\s*\([\d.]+?\s*(km|m)\)/g, '');
            originalText = originalText.replace(/\s*⭐\s*TERDEKAT/g, '');
            
            const newOption = $('<option></option>')
                .attr('value', option.value)
                .attr('data-type', option.type)
                .attr('data-id', option.id)
                .attr('data-lat', option.lat)
                .attr('data-lng', option.lng)
                .text(originalText + distanceText + nearestLabel);
            
            dropdown.append(newOption);
        });
    }

    selectPickupMethod() {
        // Get pickup shipping method ID from PHP variable
        const pickupMethodId = {{ $pickupMethod->id ?? 'null' }};
        
        const pickupMethod = {
            id: pickupMethodId || 'pickup', // Fallback to 'pickup' string if not found
            name: 'Ambil Sendiri ke Store',
            cost: 0,
            formatted_cost: 'GRATIS',
            type: 'pickup'
        };
        
        this.selectedMethod = pickupMethod;
        // Use numeric ID if available, otherwise backend will handle conversion
        document.getElementById('shipping-method-id').value = pickupMethodId || 'pickup';
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
                // If Semarang, show instant delivery calculation
                if (this.destinationCity === 'Semarang') {
                    $('#instant-delivery-calculation').show();
                    // Auto-calculate if store/outlet is already selected
                    const storeOutlet = $('#delivery_store_outlet').val();
                    if (storeOutlet) {
                        calculateDistanceAndShipping();
                    }
                } else {
                    $('#instant-delivery-calculation').hide();
                }
                this.loadShippingMethods();
            }
        });
    }
}
</script>
@endpush
