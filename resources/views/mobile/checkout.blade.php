@extends('mobile.layouts.app')

@php
use Illuminate\Support\Facades\Storage;
@endphp

@section('title', 'Checkout')

@section('content')
<style>
  #checkoutForm {
    padding-bottom: 200px; /* Space for fixed checkout button (80px) + bottom nav (70px) + extra margin (50px) */
    min-height: calc(100vh - 140px);
  }
</style>
<form id="checkoutForm">
  @csrf
  
  <!-- Shipping Address Section -->
  <div style="background: white; padding: 1rem; margin-bottom: 0.5rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
      <h5 style="font-size: 1rem; font-weight: 700; margin: 0; color: #333;">
        <i class="bx bx-map"></i> Alamat Pengiriman
      </h5>
      <button type="button" onclick="openAddressModal()" 
              style="background: #147440; color: white; border: none; padding: 0.5rem 1rem; border-radius: 8px; font-size: 0.875rem; cursor: pointer;">
        <i class="bx bx-edit"></i> Ubah
      </button>
    </div>
    
    @if($selectedAddress)
      <!-- Display Selected Address -->
      <div style="padding: 1rem; background: #f8f9fa; border-radius: 8px; margin-bottom: 1rem;">
        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.5rem;">
          <div style="flex: 1;">
            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
              <h6 style="font-size: 0.875rem; font-weight: 600; margin: 0;">{{ $selectedAddress->recipient_name }}</h6>
              @if($selectedAddress->is_primary)
                <span style="background: #dc3545; color: white; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.7rem;">Utama</span>
              @endif
            </div>
            <p style="font-size: 0.75rem; color: #666; margin: 0.25rem 0;">(+62) {{ $selectedAddress->recipient_phone }}</p>
            <p style="font-size: 0.75rem; color: #666; margin: 0;">
              {{ $selectedAddress->address }}
              @if($selectedAddress->notes), {{ $selectedAddress->notes }}@endif
              , {{ $selectedAddress->loc_kecamatan_name ?? '' }}, {{ $selectedAddress->loc_kabkota_name ?? $selectedAddress->city }}
              , {{ $selectedAddress->loc_provinsi_name ?? $selectedAddress->province }}
              @if($selectedAddress->postal_code) {{ $selectedAddress->postal_code }}@endif
            </p>
          </div>
        </div>
      </div>
    @elseif(isset($user) && $user)
      <!-- Fallback to user table data -->
      <div style="padding: 1rem; background: #f8f9fa; border-radius: 8px; margin-bottom: 1rem;">
        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.5rem;">
          <div style="flex: 1;">
            <h6 style="font-size: 0.875rem; font-weight: 600; margin: 0 0 0.5rem 0;">{{ $user['name'] ?? 'N/A' }}</h6>
            <p style="font-size: 0.75rem; color: #666; margin: 0.25rem 0;">(+62) {{ $user['phone'] ?? 'N/A' }}</p>
            <p style="font-size: 0.75rem; color: #666; margin: 0;">
              {{ $user['address'] ?? 'Alamat belum diisi' }}, {{ $user['city'] ?? 'Semarang' }}
            </p>
          </div>
        </div>
      </div>
    @endif
    
    @if(isset($userAddresses) && $userAddresses->count() > 0)
      <input type="hidden" name="address_id" id="selected_address_id" value="{{ $selectedAddress->id ?? '' }}">
    @endif
    
    <div style="margin-bottom: 0.75rem;">
      <label style="font-size: 0.875rem; font-weight: 500; color: #666; margin-bottom: 0.5rem; display: block;">
        Nama Penerima <span style="color: #dc3545;">*</span>
      </label>
      <input type="text" 
             name="first_name" 
             id="form_recipient_name"
             value="{{ $selectedAddress->recipient_name ?? $user['name'] ?? '' }}"
             required
             style="width: 100%; padding: 0.75rem; border: 1px solid #e0e0e0; border-radius: 8px; font-size: 0.875rem;">
    </div>
    
    <input type="hidden" id="first_name" name="first_name" value="{{ $selectedAddress->recipient_name ?? $user['name'] ?? '' }}">
    
    <div style="margin-bottom: 0.75rem;">
      <label style="font-size: 0.875rem; font-weight: 500; color: #666; margin-bottom: 0.5rem; display: block;">
        Email <span style="color: #dc3545;">*</span>
      </label>
      <input type="email" 
             name="email" 
             id="email"
             value="{{ $user['email'] ?? '' }}"
             required
             style="width: 100%; padding: 0.75rem; border: 1px solid #e0e0e0; border-radius: 8px; font-size: 0.875rem;">
    </div>
    
    <div style="margin-bottom: 0.75rem;">
      <label style="font-size: 0.875rem; font-weight: 500; color: #666; margin-bottom: 0.5rem; display: block;">
        No. Telepon <span style="color: #dc3545;">*</span>
      </label>
      <input type="tel" 
             name="phone" 
             id="phone"
             value="{{ $selectedAddress->recipient_phone ?? $user['phone'] ?? '' }}"
             required
             style="width: 100%; padding: 0.75rem; border: 1px solid #e0e0e0; border-radius: 8px; font-size: 0.875rem;">
    </div>
    
    <div style="margin-bottom: 0.75rem;">
      <label style="font-size: 0.875rem; font-weight: 500; color: #666; margin-bottom: 0.5rem; display: block;">
        Alamat Lengkap <span style="color: #dc3545;">*</span>
      </label>
      <textarea name="address" 
                id="address"
                rows="3"
                required
                style="width: 100%; padding: 0.75rem; border: 1px solid #e0e0e0; border-radius: 8px; font-size: 0.875rem; resize: vertical;">{{ $selectedAddress->address ?? $user['address'] ?? '' }}</textarea>
    </div>
    
    <div style="margin-bottom: 0.75rem;">
      <label style="font-size: 0.875rem; font-weight: 500; color: #666; margin-bottom: 0.5rem; display: block;">
        Kota <span style="color: #dc3545;">*</span>
      </label>
      <select name="city" 
              id="destination_city"
              required
              style="width: 100%; padding: 0.75rem; border: 1px solid #e0e0e0; border-radius: 8px; font-size: 0.875rem; background: white;">
        <option value="">Pilih Kota</option>
        <option value="Jakarta" {{ ($selectedAddress->loc_kabkota_name ?? $selectedAddress->city ?? $user['city'] ?? '') == 'Jakarta' ? 'selected' : '' }}>Jakarta</option>
        <option value="Surabaya" {{ ($selectedAddress->loc_kabkota_name ?? $selectedAddress->city ?? $user['city'] ?? '') == 'Surabaya' ? 'selected' : '' }}>Surabaya</option>
        <option value="Bandung" {{ ($selectedAddress->loc_kabkota_name ?? $selectedAddress->city ?? $user['city'] ?? '') == 'Bandung' ? 'selected' : '' }}>Bandung</option>
        <option value="Medan" {{ ($selectedAddress->loc_kabkota_name ?? $selectedAddress->city ?? $user['city'] ?? '') == 'Medan' ? 'selected' : '' }}>Medan</option>
        <option value="Semarang" {{ ($selectedAddress->loc_kabkota_name ?? $selectedAddress->city ?? $user['city'] ?? '') == 'Semarang' ? 'selected' : '' }}>Semarang</option>
        <option value="Yogyakarta" {{ ($selectedAddress->loc_kabkota_name ?? $selectedAddress->city ?? $user['city'] ?? '') == 'Yogyakarta' ? 'selected' : '' }}>Yogyakarta</option>
        <option value="Makassar" {{ ($selectedAddress->loc_kabkota_name ?? $selectedAddress->city ?? $user['city'] ?? '') == 'Makassar' ? 'selected' : '' }}>Makassar</option>
        <option value="Palembang" {{ ($selectedAddress->loc_kabkota_name ?? $selectedAddress->city ?? $user['city'] ?? '') == 'Palembang' ? 'selected' : '' }}>Palembang</option>
        <option value="Denpasar" {{ ($selectedAddress->loc_kabkota_name ?? $selectedAddress->city ?? $user['city'] ?? '') == 'Denpasar' ? 'selected' : '' }}>Denpasar</option>
        <option value="Malang" {{ ($selectedAddress->loc_kabkota_name ?? $selectedAddress->city ?? $user['city'] ?? '') == 'Malang' ? 'selected' : '' }}>Malang</option>
        @php
          $currentCity = $selectedAddress->loc_kabkota_name ?? $selectedAddress->city ?? $user['city'] ?? '';
        @endphp
        @if($currentCity && !in_array($currentCity, ['Jakarta', 'Surabaya', 'Bandung', 'Medan', 'Semarang', 'Yogyakarta', 'Makassar', 'Palembang', 'Denpasar', 'Malang']))
          <option value="{{ $currentCity }}" selected>{{ $currentCity }}</option>
        @endif
      </select>
    </div>
    
    <input type="hidden" id="city" name="city" value="{{ $selectedAddress->loc_kabkota_name ?? $selectedAddress->city ?? $user['city'] ?? '' }}">
    
    <div style="margin-bottom: 0.75rem;">
      <label style="font-size: 0.875rem; font-weight: 500; color: #666; margin-bottom: 0.5rem; display: block;">
        Kode Pos
      </label>
      <input type="text" 
             name="postal_code" 
             id="postal_code"
             value="{{ $selectedAddress->postal_code ?? $user['postal_code'] ?? '' }}"
             style="width: 100%; padding: 0.75rem; border: 1px solid #e0e0e0; border-radius: 8px; font-size: 0.875rem;">
    </div>
    
    <input type="hidden" name="country" value="Indonesia">
    
    <!-- Hidden fields for coordinates -->
    <input type="hidden" id="customer_latitude" name="customer_latitude" value="{{ $selectedAddress->latitude ?? $user['latitude'] ?? '' }}">
    <input type="hidden" id="customer_longitude" name="customer_longitude" value="{{ $selectedAddress->longitude ?? $user['longitude'] ?? '' }}">
    
    @if(!($selectedAddress && $selectedAddress->latitude && $selectedAddress->longitude) && !(isset($user['latitude']) && isset($user['longitude']) && $user['latitude'] && $user['longitude']))
      <!-- Manual Coordinates Toggle -->
      <div style="margin-top: 0.75rem; padding: 0.75rem; background: #f8f9fa; border-radius: 8px;">
        <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; cursor: pointer;">
          <input type="checkbox" id="use-manual-coordinates" style="width: 18px; height: 18px;">
          <span>Gunakan koordinat manual (opsional, untuk akurasi lebih baik)</span>
        </label>
        <div id="manual-coordinates" style="display: none; margin-top: 0.75rem;">
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem;">
            <div>
              <label style="font-size: 0.75rem; color: #666; margin-bottom: 0.25rem; display: block;">Latitude</label>
              <input type="number" step="0.00000001" id="manual_latitude" placeholder="-7.0051"
                     style="width: 100%; padding: 0.5rem; border: 1px solid #e0e0e0; border-radius: 6px; font-size: 0.875rem;">
            </div>
            <div>
              <label style="font-size: 0.75rem; color: #666; margin-bottom: 0.25rem; display: block;">Longitude</label>
              <input type="number" step="0.00000001" id="manual_longitude" placeholder="110.4381"
                     style="width: 100%; padding: 0.5rem; border: 1px solid #e0e0e0; border-radius: 6px; font-size: 0.875rem;">
            </div>
          </div>
          <small style="font-size: 0.75rem; color: #666; display: block; margin-top: 0.5rem;">
            <i class="bx bx-info-circle"></i> Dapatkan koordinat dari Google Maps: Klik kanan pada lokasi → Koordinat
          </small>
        </div>
      </div>
    @else
      <div style="margin-top: 0.75rem; padding: 0.75rem; background: #d1ecf1; border: 1px solid #bee5eb; border-radius: 8px; font-size: 0.875rem; color: #0c5460;">
        <i class="bx bx-info-circle"></i> <strong>Alamat tersimpan:</strong> Koordinat lokasi Anda sudah tersimpan dari profil. Ongkos kirim akan dihitung otomatis berdasarkan lokasi Anda.
      </div>
    @endif
  </div>
  
  <!-- Shipping Method Section -->
  <div style="background: white; padding: 1rem; margin-bottom: 0.5rem;">
    <h5 style="font-size: 1rem; font-weight: 700; margin-bottom: 1rem; color: #333;">
      <i class="bx bx-truck"></i> Metode Pengiriman
    </h5>
    
    <!-- Shipping Type Selection -->
    <div style="margin-bottom: 1rem;">
      <label style="font-size: 0.875rem; font-weight: 500; color: #666; margin-bottom: 0.5rem; display: block;">
        Tipe Pengiriman <span style="color: #dc3545;">*</span>
      </label>
      <select id="shipping_type" 
              required
              style="width: 100%; padding: 0.75rem; border: 1px solid #e0e0e0; border-radius: 8px; font-size: 0.875rem; background: white;">
        <option value="">Pilih Metode</option>
        <option value="pickup">🏪 Ambil Sendiri (Store/Outlet Terdekat)</option>
        <option value="instant">⚡ Pengiriman Instan (Berdasarkan Jarak)</option>
        <option value="delivery">🚚 Gunakan Jasa Pengiriman (Luar Kota)</option>
      </select>
    </div>
    
    <!-- Pickup Location Selection (hidden by default) -->
    <div id="pickup-location-select" style="display: none; margin-bottom: 1rem;">
      <label style="font-size: 0.875rem; font-weight: 500; color: #666; margin-bottom: 0.5rem; display: block;">
        Pilih Lokasi Terdekat <span style="color: #dc3545;">*</span>
      </label>
      <div id="pickup-locations-list">
        <!-- Will be populated by JavaScript -->
      </div>
    </div>
    
    <!-- Instant Delivery Options (hidden by default) -->
    <div id="instant-options" style="display: none; margin-bottom: 1rem;">
      <label style="font-size: 0.875rem; font-weight: 500; color: #666; margin-bottom: 0.5rem; display: block;">
        Pilih Store/Outlet Pengirim <span style="color: #dc3545;">*</span>
      </label>
      <select id="instant_store_outlet" 
              style="width: 100%; padding: 0.75rem; border: 1px solid #e0e0e0; border-radius: 8px; font-size: 0.875rem; background: white;">
        <option value="">Pilih Store/Outlet</option>
      </select>
      <small style="font-size: 0.75rem; color: #666; margin-top: 0.25rem; display: block;">
        Daftar diurutkan berdasarkan jarak terdekat dari alamat Anda
      </small>
        @if(isset($stores))
          @foreach($stores as $store)
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
        @endif
        @if(isset($outlets))
          @foreach($outlets as $outlet)
            @if($outlet->latitude && $outlet->longitude)
              <option value="outlet_{{ $outlet->id }}"
                      data-type="outlet"
                      data-id="{{ $outlet->id }}"
                      data-lat="{{ $outlet->latitude }}"
                      data-lng="{{ $outlet->longitude }}">
                📍 Outlet: {{ $outlet->name }}@if($outlet->store) ({{ $outlet->store->name }})@endif
              </option>
            @endif
          @endforeach
        @endif
      </select>
    </div>
    
    <!-- Delivery Options (hidden by default) -->
    <div id="delivery-options" style="display: none; margin-bottom: 1rem;">
      <label style="font-size: 0.875rem; font-weight: 500; color: #666; margin-bottom: 0.5rem; display: block;">
        Pilih Store/Outlet Pengirim <span style="color: #dc3545;">*</span>
      </label>
      <select id="delivery_store_outlet" 
              style="width: 100%; padding: 0.75rem; border: 1px solid #e0e0e0; border-radius: 8px; font-size: 0.875rem; background: white;">
        <option value="">Pilih Store/Outlet</option>
      </select>
      <small style="font-size: 0.75rem; color: #666; margin-top: 0.25rem; display: block;">
        Daftar diurutkan berdasarkan jarak terdekat dari alamat Anda
      </small>
        @if(isset($stores))
          @foreach($stores as $store)
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
        @endif
        @if(isset($outlets))
          @foreach($outlets as $outlet)
            @if($outlet->latitude && $outlet->longitude)
              <option value="outlet_{{ $outlet->id }}"
                      data-type="outlet"
                      data-id="{{ $outlet->id }}"
                      data-lat="{{ $outlet->latitude }}"
                      data-lng="{{ $outlet->longitude }}">
                📍 Outlet: {{ $outlet->name }}@if($outlet->store) ({{ $outlet->store->name }})@endif
              </option>
            @endif
          @endforeach
        @endif
      </select>
    </div>
    
    <!-- Shipping Methods Container -->
    <div id="shippingMethods" style="margin-top: 1rem;">
      <!-- Shipping methods will be loaded here -->
      <div style="text-align: center; padding: 2rem; color: #999;">
        <i class="bx bx-info-circle" style="font-size: 2rem;"></i>
        <p style="margin-top: 0.5rem; font-size: 0.875rem;">Pilih tipe pengiriman dan isi alamat terlebih dahulu</p>
      </div>
    </div>
    
    <!-- Loading State -->
    <div id="shipping-loading" style="display: none; text-align: center; padding: 2rem;">
      <i class="bx bx-loader-alt bx-spin" style="font-size: 2rem; color: #147440;"></i>
      <p style="margin-top: 0.5rem; color: #666; font-size: 0.875rem;">Memuat metode pengiriman...</p>
    </div>
    
    <!-- Error State -->
    <div id="shipping-error" style="display: none;"></div>
    
    <input type="hidden" name="shipping_method_id" id="shipping_method_id">
    <input type="hidden" name="shipping_cost" id="shipping_cost" value="0">
    <input type="hidden" name="pickup_location_type" id="pickup_location_type">
    <input type="hidden" name="pickup_location_id" id="pickup_location_id">
    <input type="hidden" name="outlet_id" id="outlet_id">
  </div>
  
  <!-- Payment Method Section -->
  <div style="background: white; padding: 1rem; margin-bottom: 0.5rem;">
    <h5 style="font-size: 1rem; font-weight: 700; margin-bottom: 1rem; color: #333;">
      <i class="bx bx-credit-card"></i> Metode Pembayaran
    </h5>
    
    <div style="padding: 0.75rem; background: #e7f3ff; border: 1px solid #b3d9ff; border-radius: 8px; margin-bottom: 1rem; font-size: 0.875rem; color: #004085;">
      <i class="bx bx-info-circle"></i> Anda akan dialihkan ke halaman pembayaran yang aman setelah menyelesaikan pesanan.
    </div>
    
    <div style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem; background: #f8f9fa; border-radius: 8px;">
      <input type="radio" name="payment_type" id="payment_full" value="full" checked 
             style="width: 20px; height: 20px; cursor: pointer;">
      <label for="payment_full" style="flex: 1; margin: 0; font-size: 0.875rem; cursor: pointer; display: flex; align-items: center; gap: 0.5rem;">
        <i class="bx bx-credit-card" style="font-size: 1.125rem; color: #147440;"></i>
        <span>Bayar Penuh Sekarang</span>
      </label>
    </div>
  </div>
  
  <!-- Order Summary -->
  <div style="background: white; padding: 1rem; margin-bottom: 1rem;">
    <h5 style="font-size: 1rem; font-weight: 700; margin-bottom: 1rem; color: #333;">
      <i class="bx bx-receipt"></i> Ringkasan Pesanan
    </h5>
    
    <div style="max-height: 200px; overflow-y: auto; margin-bottom: 1rem;">
      @foreach($cart as $id => $item)
        @php
          $product = $item['product'] ?? \App\Models\Product::find($id);
          $imagePath = $item['image'] ?? ($product->main_image_path ?? null);
          $qty = $item['qty'] ?? $item['quantity'] ?? 1;
          $price = $item['price'] ?? ($product->price ?? 0);
        @endphp
        <div style="display: flex; gap: 0.75rem; padding: 0.75rem 0; border-bottom: 1px solid #f0f0f0;">
          <img src="{{ $imagePath ? Storage::url($imagePath) : asset('sneat/assets/img/placeholder.png') }}" 
               alt="{{ $item['name'] ?? $product->name ?? 'Product' }}"
               style="width: 50px; height: 50px; object-fit: cover; border-radius: 6px;"
               onerror="this.src='{{ asset('sneat/assets/img/placeholder.png') }}'">
          <div style="flex: 1;">
            <div style="font-size: 0.875rem; font-weight: 500; margin-bottom: 0.25rem;">{{ $item['name'] ?? $product->name ?? 'Product' }}</div>
            <div style="font-size: 0.75rem; color: #666;">{{ $qty }}x Rp{{ number_format($price, 0, ',', '.') }}</div>
          </div>
          <div style="font-size: 0.875rem; font-weight: 600; color: #147440;">
            Rp{{ number_format($price * $qty, 0, ',', '.') }}
          </div>
        </div>
      @endforeach
    </div>
    
    <div style="border-top: 1px solid #e0e0e0; padding-top: 1rem; margin-top: 1rem;">
      <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; font-size: 0.875rem;">
        <span style="color: #666;">Subtotal</span>
        <span style="font-weight: 600;">Rp{{ number_format($totals['subtotal'], 0, ',', '.') }}</span>
      </div>
      @if($totals['discount'] > 0)
        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; font-size: 0.875rem;">
          <span style="color: #666;">Diskon</span>
          <span style="font-weight: 600; color: #28a745;">-Rp{{ number_format($totals['discount'], 0, ',', '.') }}</span>
        </div>
      @endif
      <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; font-size: 0.875rem;">
        <span style="color: #666;">Ongkir</span>
        <span style="font-weight: 600;" id="shippingCostDisplay">Rp0</span>
      </div>
      <div style="border-top: 2px solid #147440; padding-top: 0.75rem; margin-top: 0.75rem; margin-bottom: 1rem; display: flex; justify-content: space-between; align-items: center;">
        <span style="font-size: 1.125rem; font-weight: 700; color: #333;">Total</span>
        <span style="font-size: 1.25rem; font-weight: 700; color: #147440;" id="totalAmountDisplay">Rp{{ number_format($totals['total'], 0, ',', '.') }}</span>
      </div>
    </div>
  </div>
  
  <!-- Extra spacing to ensure all content is visible -->
  <div style="height: 20px;"></div>
</form>

<!-- Address Modal (Mobile-friendly) -->
<div id="addressModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 10000; overflow-y: auto;">
  <div style="background: white; margin: 1rem; border-radius: 12px; padding: 1rem; max-height: 90vh; overflow-y: auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; padding-bottom: 1rem; border-bottom: 1px solid #e0e0e0;">
      <h5 style="font-size: 1.125rem; font-weight: 700; color: #333; margin: 0;">
        <i class="bx bx-map-pin"></i> Pilih/Ubah Alamat
      </h5>
      <button onclick="closeAddressModal()" style="background: none; border: none; font-size: 1.5rem; color: #666; cursor: pointer;">
        <i class="bx bx-x"></i>
      </button>
    </div>
    
    <!-- Address List -->
    <div id="address-list" style="margin-bottom: 1.5rem;">
      @forelse($userAddresses ?? [] as $addr)
        <div class="address-option" 
             data-address-id="{{ $addr->id }}"
             onclick="selectAddress({{ $addr->id }})"
             style="padding: 1rem; border: 2px solid #e0e0e0; border-radius: 8px; margin-bottom: 0.75rem; cursor: pointer; background: white;">
          <div style="display: flex; justify-content: space-between; align-items: start;">
            <div style="flex: 1;">
              <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                <h6 style="font-size: 0.875rem; font-weight: 600; margin: 0;">{{ $addr->recipient_name }}</h6>
                @if($addr->is_primary)
                  <span style="background: #dc3545; color: white; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.7rem;">Utama</span>
                @endif
              </div>
              <p style="font-size: 0.75rem; color: #666; margin: 0.25rem 0;">(+62) {{ $addr->recipient_phone }}</p>
              <p style="font-size: 0.75rem; color: #666; margin: 0;">
                {{ $addr->address }}
                @if($addr->notes), {{ $addr->notes }}@endif
                , {{ $addr->loc_kecamatan_name ?? '' }}, {{ $addr->loc_kabkota_name ?? $addr->city }}
                , {{ $addr->loc_provinsi_name ?? $addr->province }}
                @if($addr->postal_code) {{ $addr->postal_code }}@endif
              </p>
            </div>
            <input type="radio" name="selected_address" value="{{ $addr->id }}" 
                   {{ ($selectedAddress && $selectedAddress->id == $addr->id) ? 'checked' : '' }}
                   style="margin-left: 0.5rem;">
          </div>
        </div>
      @empty
        <div style="padding: 1rem; background: #e7f3ff; border: 1px solid #b3d9ff; border-radius: 8px; color: #004085; font-size: 0.875rem;">
          <i class="bx bx-info-circle"></i> Belum ada alamat tersimpan. Silakan isi form di bawah.
        </div>
      @endforelse
    </div>
    
    <hr style="margin: 1.5rem 0;">
    
    <!-- New Address Form -->
    <div>
      <h6 style="font-size: 1rem; font-weight: 600; margin-bottom: 1rem;">Tambah Alamat Baru</h6>
      <form id="new-address-form">
        <div style="margin-bottom: 0.75rem;">
          <label style="font-size: 0.875rem; font-weight: 500; color: #666; margin-bottom: 0.5rem; display: block;">
            Label Alamat
          </label>
          <input type="text" id="new_address_label" name="label" placeholder="Rumah, Kantor, dll"
                 style="width: 100%; padding: 0.75rem; border: 1px solid #e0e0e0; border-radius: 8px; font-size: 0.875rem;">
        </div>
        
        <div style="margin-bottom: 0.75rem;">
          <label style="font-size: 0.875rem; font-weight: 500; color: #666; margin-bottom: 0.5rem; display: block;">
            Nama Penerima <span style="color: #dc3545;">*</span>
          </label>
          <input type="text" id="new_recipient_name" name="recipient_name" required
                 style="width: 100%; padding: 0.75rem; border: 1px solid #e0e0e0; border-radius: 8px; font-size: 0.875rem;">
        </div>
        
        <div style="margin-bottom: 0.75rem;">
          <label style="font-size: 0.875rem; font-weight: 500; color: #666; margin-bottom: 0.5rem; display: block;">
            Nomor Telepon <span style="color: #dc3545;">*</span>
          </label>
          <input type="tel" id="new_recipient_phone" name="recipient_phone" required
                 style="width: 100%; padding: 0.75rem; border: 1px solid #e0e0e0; border-radius: 8px; font-size: 0.875rem;">
        </div>
        
        <div style="margin-bottom: 0.75rem;">
          <label style="font-size: 0.875rem; font-weight: 500; color: #666; margin-bottom: 0.5rem; display: block;">
            Alamat Lengkap <span style="color: #dc3545;">*</span>
          </label>
          <textarea id="new_address" name="address" rows="2" required
                    style="width: 100%; padding: 0.75rem; border: 1px solid #e0e0e0; border-radius: 8px; font-size: 0.875rem; resize: vertical;"></textarea>
        </div>
        
        <!-- Location Dropdowns -->
        <div style="margin-bottom: 0.75rem;">
          <label style="font-size: 0.875rem; font-weight: 500; color: #666; margin-bottom: 0.5rem; display: block;">
            Provinsi <span style="color: #dc3545;">*</span>
          </label>
          <select id="new_loc_provinsi_id" name="loc_provinsi_id" required
                  style="width: 100%; padding: 0.75rem; border: 1px solid #e0e0e0; border-radius: 8px; font-size: 0.875rem; background: white;">
            <option value="">Pilih Provinsi</option>
          </select>
        </div>
        
        <div style="margin-bottom: 0.75rem;">
          <label style="font-size: 0.875rem; font-weight: 500; color: #666; margin-bottom: 0.5rem; display: block;">
            Kab/Kota <span style="color: #dc3545;">*</span>
          </label>
          <select id="new_loc_kabkota_id" name="loc_kabkota_id" required
                  style="width: 100%; padding: 0.75rem; border: 1px solid #e0e0e0; border-radius: 8px; font-size: 0.875rem; background: white;">
            <option value="">Pilih Kab/Kota</option>
          </select>
        </div>
        
        <div style="margin-bottom: 0.75rem;">
          <label style="font-size: 0.875rem; font-weight: 500; color: #666; margin-bottom: 0.5rem; display: block;">
            Kecamatan <span style="color: #dc3545;">*</span>
          </label>
          <select id="new_loc_kecamatan_id" name="loc_kecamatan_id" required
                  style="width: 100%; padding: 0.75rem; border: 1px solid #e0e0e0; border-radius: 8px; font-size: 0.875rem; background: white;">
            <option value="">Pilih Kecamatan</option>
          </select>
        </div>
        
        <div style="margin-bottom: 0.75rem;">
          <label style="font-size: 0.875rem; font-weight: 500; color: #666; margin-bottom: 0.5rem; display: block;">
            Desa/Kelurahan <span style="color: #dc3545;">*</span>
          </label>
          <select id="new_loc_desa_id" name="loc_desa_id" required
                  style="width: 100%; padding: 0.75rem; border: 1px solid #e0e0e0; border-radius: 8px; font-size: 0.875rem; background: white;">
            <option value="">Pilih Desa/Kelurahan</option>
          </select>
        </div>
        
        <div style="margin-bottom: 0.75rem;">
          <label style="font-size: 0.875rem; font-weight: 500; color: #666; margin-bottom: 0.5rem; display: block;">
            Kota (Teks)
          </label>
          <input type="text" id="new_city" name="city"
                 style="width: 100%; padding: 0.75rem; border: 1px solid #e0e0e0; border-radius: 8px; font-size: 0.875rem;">
        </div>
        
        <div style="margin-bottom: 0.75rem;">
          <label style="font-size: 0.875rem; font-weight: 500; color: #666; margin-bottom: 0.5rem; display: block;">
            Kode Pos
          </label>
          <input type="text" id="new_postal_code" name="postal_code"
                 style="width: 100%; padding: 0.75rem; border: 1px solid #e0e0e0; border-radius: 8px; font-size: 0.875rem;">
        </div>
        
        <!-- Map Section -->
        <div style="margin-bottom: 0.75rem;">
          <hr style="margin: 1rem 0;">
          <h6 style="font-size: 0.875rem; font-weight: 600; margin-bottom: 0.75rem;">
            <i class="bx bx-map-pin"></i> Pinpoint Lokasi di Peta
          </h6>
          
          <div style="margin-bottom: 0.75rem;">
            <button type="button" id="btn-get-location-modal" 
                    style="background: #147440; color: white; border: none; padding: 0.5rem 1rem; border-radius: 8px; font-size: 0.875rem; cursor: pointer;">
              <i class="bx bx-crosshair"></i> Gunakan Lokasi GPS Saya
            </button>
            <span id="location-status-modal" style="margin-left: 0.5rem; font-size: 0.75rem; color: #666;"></span>
          </div>
          
          <div style="margin-bottom: 0.75rem;">
            <div style="display: flex; gap: 0.5rem;">
              <input type="text" id="address-search-modal" 
                     placeholder="Cari alamat (contoh: Jl. Sudirman No. 123, Semarang)" 
                     autocomplete="off"
                     style="flex: 1; padding: 0.75rem; border: 1px solid #e0e0e0; border-radius: 8px; font-size: 0.875rem;">
              <button type="button" id="btn-clear-search-modal" 
                      style="display: none; background: #f0f0f0; border: 1px solid #e0e0e0; padding: 0.75rem; border-radius: 8px; cursor: pointer;">
                <i class="bx bx-x"></i>
              </button>
            </div>
            <small style="font-size: 0.75rem; color: #666; display: block; margin-top: 0.25rem;">
              <i class="bx bx-info-circle"></i> Ketik alamat lengkap untuk mencari lokasi di peta
            </small>
          </div>
          
          <div id="map-modal" style="height: 300px; width: 100%; border-radius: 8px; border: 2px solid #e0e0e0; margin-bottom: 0.5rem;"></div>
          <small style="font-size: 0.75rem; color: #666; display: block;">
            <i class="bx bx-info-circle"></i> Gunakan GPS, cari alamat, atau klik pada peta untuk menentukan lokasi
          </small>
          
          <input type="hidden" id="new_latitude" name="latitude">
          <input type="hidden" id="new_longitude" name="longitude">
        </div>
        
        <div style="margin-bottom: 0.75rem;">
          <label style="font-size: 0.875rem; font-weight: 500; color: #666; margin-bottom: 0.5rem; display: block;">
            Catatan
          </label>
          <input type="text" id="new_notes" name="notes" placeholder="Samping SMA 7, dll"
                 style="width: 100%; padding: 0.75rem; border: 1px solid #e0e0e0; border-radius: 8px; font-size: 0.875rem;">
        </div>
        
        <div style="margin-bottom: 1rem;">
          <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; cursor: pointer;">
            <input type="checkbox" id="new_is_primary" name="is_primary" value="1" style="width: 18px; height: 18px;">
            <span>Jadikan sebagai alamat utama</span>
          </label>
        </div>
      </form>
    </div>
    
    <!-- Modal Footer -->
    <div style="display: flex; gap: 0.5rem; padding-top: 1rem; border-top: 1px solid #e0e0e0;">
      <button type="button" onclick="closeAddressModal()" 
              style="flex: 1; background: #f0f0f0; color: #333; border: none; padding: 0.75rem; border-radius: 8px; font-weight: 600; cursor: pointer;">
        Batal
      </button>
      <button type="button" id="btn-save-new-address" 
              style="flex: 1; background: #147440; color: white; border: none; padding: 0.75rem; border-radius: 8px; font-weight: 600; cursor: pointer;">
        <i class="bx bx-plus"></i> Tambah Alamat
      </button>
      <button type="button" id="btn-use-selected-address" 
              style="flex: 1; background: #28a745; color: white; border: none; padding: 0.75rem; border-radius: 8px; font-weight: 600; cursor: pointer;">
        <i class="bx bx-check"></i> Gunakan Alamat
      </button>
    </div>
  </div>
</div>

<!-- Checkout Button -->
<div style="position: fixed; bottom: 70px; left: 0; right: 0; background: white; padding: 1rem; border-top: 1px solid #e0e0e0; z-index: 999; box-shadow: 0 -2px 8px rgba(0,0,0,0.1);">
  <button type="submit" 
          form="checkoutForm"
          id="checkoutBtn"
          style="width: 100%; background: #147440; color: white; border: none; padding: 1rem; border-radius: 12px; font-weight: 700; font-size: 1rem; cursor: pointer;">
    <i class="bx bx-credit-card"></i> Bayar Sekarang
  </button>
</div>
@endsection

@push('scripts')
<!-- Leaflet JS for Map -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-control-geocoder@1.13.0/dist/Control.Geocoder.js"></script>
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
<script>
  // Address Modal Functions
  function openAddressModal() {
    document.getElementById('addressModal').style.display = 'block';
    document.body.style.overflow = 'hidden';
    // Initialize map and location dropdowns when modal opens
    if (!window.mapModalInitialized) {
      initMapModal();
      initNewAddressLocationDropdowns();
      window.mapModalInitialized = true;
    }
  }
  
  function closeAddressModal() {
    document.getElementById('addressModal').style.display = 'none';
    document.body.style.overflow = '';
  }
  
  // Close modal when clicking outside
  document.getElementById('addressModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
      closeAddressModal();
    }
  });
  
  // Initialize location dropdowns for new address form
  function initNewAddressLocationDropdowns() {
    // Load provinces
    fetch('{{ route("api.locations.provinsis") }}')
      .then(r => r.json())
      .then(json => {
        const provSelect = document.getElementById('new_loc_provinsi_id');
        provSelect.innerHTML = '<option value="">Pilih Provinsi</option>';
        (json.data || []).forEach(item => {
          const option = document.createElement('option');
          option.value = item.id;
          option.textContent = item.name;
          provSelect.appendChild(option);
        });
      });
    
    // Province change
    document.getElementById('new_loc_provinsi_id').addEventListener('change', function() {
      const provId = this.value;
      const kabSelect = document.getElementById('new_loc_kabkota_id');
      const kecSelect = document.getElementById('new_loc_kecamatan_id');
      const desaSelect = document.getElementById('new_loc_desa_id');
      
      kabSelect.innerHTML = '<option value="">Pilih Kab/Kota</option>';
      kecSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';
      desaSelect.innerHTML = '<option value="">Pilih Desa/Kelurahan</option>';
      
      if (provId) {
        fetch(`{{ url('/api/locations/kabkotas') }}/${provId}`)
          .then(r => r.json())
          .then(j => {
            (j.data || []).forEach(item => {
              const option = document.createElement('option');
              option.value = item.id;
              option.textContent = item.name;
              kabSelect.appendChild(option);
            });
          });
      }
    });
    
    // Kab/Kota change
    document.getElementById('new_loc_kabkota_id').addEventListener('change', function() {
      const kabId = this.value;
      const kecSelect = document.getElementById('new_loc_kecamatan_id');
      const desaSelect = document.getElementById('new_loc_desa_id');
      
      kecSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';
      desaSelect.innerHTML = '<option value="">Pilih Desa/Kelurahan</option>';
      
      if (kabId) {
        fetch(`{{ url('/api/locations/kecamatans') }}/${kabId}`)
          .then(r => r.json())
          .then(j => {
            (j.data || []).forEach(item => {
              const option = document.createElement('option');
              option.value = item.id;
              option.textContent = item.name;
              kecSelect.appendChild(option);
            });
          });
      }
    });
    
    // Kecamatan change
    document.getElementById('new_loc_kecamatan_id').addEventListener('change', function() {
      const kecId = this.value;
      const desaSelect = document.getElementById('new_loc_desa_id');
      
      desaSelect.innerHTML = '<option value="">Pilih Desa/Kelurahan</option>';
      
      if (kecId) {
        fetch(`{{ url('/api/locations/desas') }}/${kecId}`)
          .then(r => r.json())
          .then(j => {
            (j.data || []).forEach(item => {
              const option = document.createElement('option');
              option.value = item.id;
              option.textContent = item.name;
              desaSelect.appendChild(option);
            });
          });
      }
    });
  }
  
  // Initialize map for modal
  let mapModal = null;
  let markerModal = null;
  const defaultLat = -7.0051;
  const defaultLng = 110.4381;
  
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
    
    // Address search
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
      statusEl.style.color = type === 'error' ? '#dc3545' : '#28a745';
    }
  }
  
  // GPS button for modal
  document.getElementById('btn-get-location-modal')?.addEventListener('click', function() {
    const btn = this;
    btn.disabled = true;
    btn.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Mengambil lokasi...';
    
    if (!navigator.geolocation) {
      updateLocationStatusModal('Browser tidak mendukung GPS', 'error');
      btn.disabled = false;
      btn.innerHTML = '<i class="bx bx-crosshair"></i> Gunakan Lokasi GPS Saya';
      return;
    }
    
    navigator.geolocation.getCurrentPosition(
      function(position) {
        const lat = position.coords.latitude;
        const lng = position.coords.longitude;
        
        markerModal.setLatLng([lat, lng]);
        mapModal.setView([lat, lng], 15);
        document.getElementById('new_latitude').value = lat;
        document.getElementById('new_longitude').value = lng;
        updateLocationStatusModal('Lokasi GPS berhasil didapatkan');
        
        btn.disabled = false;
        btn.innerHTML = '<i class="bx bx-crosshair"></i> Gunakan Lokasi GPS Saya';
      },
      function(error) {
        updateLocationStatusModal('Gagal mendapatkan lokasi GPS', 'error');
        btn.disabled = false;
        btn.innerHTML = '<i class="bx bx-crosshair"></i> Gunakan Lokasi GPS Saya';
      }
    );
  });
  
  // Select address function
  function selectAddress(addressId) {
    document.querySelectorAll('.address-option').forEach(opt => {
      opt.style.borderColor = '#e0e0e0';
      opt.style.background = 'white';
    });
    
    const selectedOption = document.querySelector(`.address-option[data-address-id="${addressId}"]`);
    if (selectedOption) {
      selectedOption.style.borderColor = '#147440';
      selectedOption.style.background = '#e8f5e9';
      const radio = selectedOption.querySelector(`input[value="${addressId}"]`);
      if (radio) radio.checked = true;
    }
  }
  
  // Use selected address
  document.getElementById('btn-use-selected-address')?.addEventListener('click', function() {
    const selectedRadio = document.querySelector('input[name="selected_address"]:checked');
    if (selectedRadio) {
      const addressId = selectedRadio.value;
      // Reload page with selected address
      window.location.href = '{{ route("mobile.checkout") }}?address_id=' + addressId;
    } else {
      Swal.fire({
        icon: 'warning',
        title: 'Pilih Alamat',
        text: 'Silakan pilih alamat terlebih dahulu',
        confirmButtonColor: '#147440',
        confirmButtonText: 'Mengerti',
        width: '90%',
        customClass: {
          popup: 'mobile-swal-popup'
        }
      });
    }
  });
  
  // Save new address
  document.getElementById('btn-save-new-address')?.addEventListener('click', function() {
    const formData = {
      _token: '{{ csrf_token() }}',
      label: document.getElementById('new_address_label').value,
      recipient_name: document.getElementById('new_recipient_name').value,
      recipient_phone: document.getElementById('new_recipient_phone').value,
      address: document.getElementById('new_address').value,
      city: document.getElementById('new_city').value,
      postal_code: document.getElementById('new_postal_code').value,
      notes: document.getElementById('new_notes').value,
      is_primary: document.getElementById('new_is_primary').checked ? 1 : 0,
      loc_provinsi_id: document.getElementById('new_loc_provinsi_id').value,
      loc_kabkota_id: document.getElementById('new_loc_kabkota_id').value,
      loc_kecamatan_id: document.getElementById('new_loc_kecamatan_id').value,
      loc_desa_id: document.getElementById('new_loc_desa_id').value,
      latitude: document.getElementById('new_latitude').value,
      longitude: document.getElementById('new_longitude').value,
    };
    
    if (!formData.recipient_name || !formData.recipient_phone || !formData.address) {
      Swal.fire({
        icon: 'warning',
        title: 'Data Tidak Lengkap',
        text: 'Silakan lengkapi nama penerima, nomor telepon, dan alamat',
        confirmButtonColor: '#147440',
        confirmButtonText: 'Mengerti',
        width: '90%',
        customClass: {
          popup: 'mobile-swal-popup'
        }
      });
      return;
    }
    
    if (!formData.loc_provinsi_id || !formData.loc_kabkota_id || !formData.loc_kecamatan_id || !formData.loc_desa_id) {
      Swal.fire({
        icon: 'warning',
        title: 'Lokasi Tidak Lengkap',
        text: 'Silakan lengkapi semua field lokasi (Provinsi, Kab/Kota, Kecamatan, Desa/Kelurahan)',
        confirmButtonColor: '#147440',
        confirmButtonText: 'Mengerti',
        width: '90%',
        customClass: {
          popup: 'mobile-swal-popup'
        }
      });
      return;
    }
    
    if (!formData.latitude || !formData.longitude) {
      Swal.fire({
        icon: 'warning',
        title: 'Lokasi Belum Ditentukan',
        text: 'Silakan tentukan lokasi di peta dengan menggunakan GPS, mencari alamat, atau klik pada peta',
        confirmButtonColor: '#147440',
        confirmButtonText: 'Mengerti',
        width: '90%',
        customClass: {
          popup: 'mobile-swal-popup'
        }
      });
      return;
    }
    
    MobileLoading.show('Menyimpan alamat...');
    
    fetch('{{ route("user.addresses.store") }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
      },
      body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
      MobileLoading.hide();
      if (data.success) {
        MobileNotification.success('Alamat berhasil disimpan');
        setTimeout(() => {
          window.location.reload();
        }, 1000);
      } else {
        MobileNotification.error(data.message || 'Gagal menyimpan alamat');
      }
    })
    .catch(error => {
      MobileLoading.hide();
      MobileErrorHandler.handle(error, 'Save Address');
    });
  });
  // User coordinates (from address or GPS)
  let userCoordinates = null;
  @if(isset($user['latitude']) && isset($user['longitude']))
    userCoordinates = {
      lat: {{ $user['latitude'] }},
      lng: {{ $user['longitude'] }}
    };
  @endif
  
  // Initialize calculated shipping cost
  window.calculatedShippingCost = null;
  window.calculatedDistance = null;
  
  // Call updateTotals on page load to ensure initial display is correct
  document.addEventListener('DOMContentLoaded', function() {
    updateTotals();
  });
  
  // Stores and outlets data
  @php
    $storesArray = isset($stores) ? $stores->map(function($s) {
      return [
        'id' => $s->id,
        'name' => $s->name,
        'latitude' => $s->latitude,
        'longitude' => $s->longitude,
        'address' => $s->address ?? '',
        'phone' => $s->phone ?? ''
      ];
    })->values()->toArray() : [];
    
    $outletsArray = isset($outlets) ? $outlets->map(function($o) {
      return [
        'id' => $o->id,
        'name' => $o->name,
        'latitude' => $o->latitude,
        'longitude' => $o->longitude,
        'address' => $o->address ?? '',
        'phone' => $o->phone ?? '',
        'store_id' => $o->store_id ?? null
      ];
    })->values()->toArray() : [];
  @endphp
  const storesData = @json($storesArray);
  const outletsData = @json($outletsArray);
  
  // Initialize store/outlet dropdowns with data
  function initializeStoreOutletDropdowns() {
    // Initialize instant dropdown
    const instantDropdown = document.getElementById('instant_store_outlet');
    if (instantDropdown) {
      storesData.forEach(store => {
        if (store.latitude && store.longitude) {
          const option = document.createElement('option');
          option.value = `store_${store.id}`;
          option.dataset.type = 'store';
          option.dataset.id = store.id;
          option.dataset.lat = store.latitude;
          option.dataset.lng = store.longitude;
          option.textContent = `🏪 Store: ${store.name}`;
          instantDropdown.appendChild(option);
        }
      });
      
      outletsData.forEach(outlet => {
        if (outlet.latitude && outlet.longitude) {
          const option = document.createElement('option');
          option.value = `outlet_${outlet.id}`;
          option.dataset.type = 'outlet';
          option.dataset.id = outlet.id;
          option.dataset.lat = outlet.latitude;
          option.dataset.lng = outlet.longitude;
          option.textContent = `📍 Outlet: ${outlet.name}`;
          instantDropdown.appendChild(option);
        }
      });
    }
    
    // Initialize delivery dropdown
    const deliveryDropdown = document.getElementById('delivery_store_outlet');
    if (deliveryDropdown) {
      storesData.forEach(store => {
        if (store.latitude && store.longitude) {
          const option = document.createElement('option');
          option.value = `store_${store.id}`;
          option.dataset.type = 'store';
          option.dataset.id = store.id;
          option.dataset.lat = store.latitude;
          option.dataset.lng = store.longitude;
          option.textContent = `🏪 Store: ${store.name}`;
          deliveryDropdown.appendChild(option);
        }
      });
      
      outletsData.forEach(outlet => {
        if (outlet.latitude && outlet.longitude) {
          const option = document.createElement('option');
          option.value = `outlet_${outlet.id}`;
          option.dataset.type = 'outlet';
          option.dataset.id = outlet.id;
          option.dataset.lat = outlet.latitude;
          option.dataset.lng = outlet.longitude;
          option.textContent = `📍 Outlet: ${outlet.name}`;
          deliveryDropdown.appendChild(option);
        }
      });
    }
    
    // Update with distance sorting if coordinates available
    if (userCoordinates && userCoordinates.lat && userCoordinates.lng) {
      updateStoreOutletDropdownWithDistance('instant_store_outlet');
      updateStoreOutletDropdownWithDistance('delivery_store_outlet');
    }
  }
  
  // Initialize dropdowns on page load
  initializeStoreOutletDropdowns();
  
  // Calculate distance between two coordinates (Haversine formula)
  function calculateDistance(lat1, lon1, lat2, lon2) {
    const R = 6371; // Radius of the Earth in km
    const dLat = (lat2 - lat1) * Math.PI / 180;
    const dLon = (lon2 - lon1) * Math.PI / 180;
    const a = 
      Math.sin(dLat/2) * Math.sin(dLat/2) +
      Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
      Math.sin(dLon/2) * Math.sin(dLon/2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    return R * c; // Distance in km
  }
  
  // Function to update store/outlet dropdown with distance sorting
  function updateStoreOutletDropdownWithDistance(dropdownId) {
    const dropdown = document.getElementById(dropdownId);
    if (!dropdown || !userCoordinates) {
      return; // If no coordinates, keep original order
    }
    
    // Get all options except the first (placeholder)
    const options = [];
    for (let i = 1; i < dropdown.options.length; i++) {
      const option = dropdown.options[i];
      const lat = parseFloat(option.dataset.lat);
      const lng = parseFloat(option.dataset.lng);
      
      if (lat && lng) {
        // Calculate distance
        const distance = calculateDistance(
          userCoordinates.lat,
          userCoordinates.lng,
          lat,
          lng
        );
        options.push({
          element: option,
          distance: distance,
          value: option.value,
          text: option.textContent,
          type: option.dataset.type,
          id: option.dataset.id,
          lat: lat,
          lng: lng
        });
      } else {
        options.push({
          element: option,
          distance: Infinity,
          value: option.value,
          text: option.textContent,
          type: option.dataset.type,
          id: option.dataset.id,
          lat: null,
          lng: null
        });
      }
    }
    
    // Sort by distance
    options.sort((a, b) => a.distance - b.distance);
    
    // Clear dropdown (keep placeholder)
    while (dropdown.options.length > 1) {
      dropdown.remove(1);
    }
    
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
      // Remove any existing distance labels
      originalText = originalText.replace(/\s*\([\d.]+?\s*(km|m)\)/g, '');
      originalText = originalText.replace(/\s*⭐\s*TERDEKAT/g, '');
      
      const newOption = document.createElement('option');
      newOption.value = option.value;
      newOption.dataset.type = option.type;
      newOption.dataset.id = option.id;
      newOption.dataset.lat = option.lat;
      newOption.dataset.lng = option.lng;
      newOption.textContent = originalText + distanceText + nearestLabel;
      
      dropdown.appendChild(newOption);
    });
  }
  
  // Shipping type change handler
  document.getElementById('shipping_type').addEventListener('change', function() {
    const shippingType = this.value;
    
    // Hide all options first
    document.getElementById('pickup-location-select').style.display = 'none';
    document.getElementById('instant-options').style.display = 'none';
    document.getElementById('delivery-options').style.display = 'none';
    document.getElementById('shippingMethods').style.display = 'none';
    document.getElementById('shipping-loading').style.display = 'none';
    
    // Clear shipping method selection
    document.getElementById('shipping_method_id').value = '';
    document.getElementById('shipping_cost').value = '0';
    document.getElementById('pickup_location_type').value = '';
    document.getElementById('pickup_location_id').value = '';
    document.getElementById('outlet_id').value = '';
    updateTotals();
    
    if (shippingType === 'pickup') {
      document.getElementById('pickup-location-select').style.display = 'block';
      loadPickupLocations();
    } else if (shippingType === 'instant') {
      document.getElementById('instant-options').style.display = 'block';
      // Update dropdown with distance sorting
      updateStoreOutletDropdownWithDistance('instant_store_outlet');
      // Check if store/outlet already selected
      const instantStoreOutlet = document.getElementById('instant_store_outlet').value;
      if (instantStoreOutlet && userCoordinates) {
        // Trigger change event to calculate distance and load methods
        document.getElementById('instant_store_outlet').dispatchEvent(new Event('change'));
      }
    } else if (shippingType === 'delivery') {
      document.getElementById('delivery-options').style.display = 'block';
      // Update dropdown with distance sorting
      updateStoreOutletDropdownWithDistance('delivery_store_outlet');
      // Check if store/outlet and city already selected
      const deliveryStoreOutlet = document.getElementById('delivery_store_outlet').value;
      const destinationCity = document.getElementById('destination_city').value;
      if (deliveryStoreOutlet && destinationCity) {
        // Load shipping methods immediately
        loadShippingMethods();
      }
    }
  });
  
  // Load pickup locations
  function loadPickupLocations() {
    const container = document.getElementById('pickup-locations-list');
    
    if (!userCoordinates) {
      container.innerHTML = `
        <div style="padding: 1rem; background: #fff3cd; border: 1px solid #ffc107; border-radius: 8px; color: #856404; font-size: 0.875rem;">
          <i class="bx bx-info-circle"></i> Koordinat alamat Anda tidak tersedia. Silakan isi alamat lengkap dan koordinat untuk melihat lokasi terdekat.
        </div>
      `;
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
          name: store.name,
          address: store.address || '',
          phone: store.phone || '',
          distance: distance
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
          name: outlet.name,
          address: outlet.address || '',
          phone: outlet.phone || '',
          distance: distance
        });
      }
    });
    
    // Sort by distance
    locations.sort((a, b) => a.distance - b.distance);
    
    if (locations.length === 0) {
      container.innerHTML = `
        <div style="padding: 1rem; background: #f8d7da; border: 1px solid #dc3545; border-radius: 8px; color: #721c24; font-size: 0.875rem;">
          <i class="bx bx-error-circle"></i> Tidak ada lokasi pickup tersedia.
        </div>
      `;
      return;
    }
    
    container.innerHTML = '';
    locations.forEach(location => {
      const card = document.createElement('div');
      card.className = 'pickup-location-card';
      card.style.cssText = 'padding: 1rem; border: 2px solid #e0e0e0; border-radius: 8px; margin-bottom: 0.75rem; cursor: pointer; background: white;';
      card.dataset.type = location.type;
      card.dataset.id = location.id;
      
      card.innerHTML = `
        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.5rem;">
          <div style="flex: 1;">
            <div style="font-weight: 600; margin-bottom: 0.25rem;">
              ${location.type === 'store' ? '🏪' : '📍'} ${location.name}
            </div>
            ${location.address ? `<div style="font-size: 0.75rem; color: #666; margin-bottom: 0.25rem;">${location.address}</div>` : ''}
            ${location.phone ? `<div style="font-size: 0.75rem; color: #666;">📞 ${location.phone}</div>` : ''}
          </div>
          <div style="font-weight: 600; color: #147440; margin-left: 1rem;">
            ${location.distance.toFixed(2)} km
          </div>
        </div>
        <div style="padding: 0.5rem; background: #e8f5e9; border-radius: 6px; font-size: 0.75rem; color: #147440; text-align: center;">
          ✅ GRATIS - Ambil langsung di lokasi
        </div>
      `;
      
      card.addEventListener('click', function() {
        document.querySelectorAll('.pickup-location-card').forEach(c => {
          c.style.borderColor = '#e0e0e0';
          c.style.background = 'white';
        });
        
        this.style.borderColor = '#147440';
        this.style.background = '#e8f5e9';
        
        document.getElementById('pickup_location_type').value = this.dataset.type;
        document.getElementById('pickup_location_id').value = this.dataset.id;
        document.getElementById('shipping_method_id').value = 'pickup';
        document.getElementById('shipping_cost').value = '0';
        
        // Force update totals
        updateTotals();
      });
      
      container.appendChild(card);
    });
  }
  
  // Load shipping methods for delivery
  function loadShippingMethods() {
    const shippingType = document.getElementById('shipping_type').value;
    if (shippingType !== 'delivery') {
      return; // Only load for delivery type
    }
    
    const city = document.getElementById('destination_city').value;
    const storeOutlet = document.getElementById('delivery_store_outlet').value;
    
    if (!city) {
      document.getElementById('shippingMethods').innerHTML = `
        <div style="text-align: center; padding: 2rem; color: #999;">
          <i class="bx bx-info-circle" style="font-size: 2rem;"></i>
          <p style="margin-top: 0.5rem; font-size: 0.875rem;">Pilih kota tujuan terlebih dahulu</p>
        </div>
      `;
      document.getElementById('shippingMethods').style.display = 'block';
      return;
    }
    
    if (!storeOutlet) {
      document.getElementById('shippingMethods').innerHTML = `
        <div style="text-align: center; padding: 2rem; color: #999;">
          <i class="bx bx-info-circle" style="font-size: 2rem;"></i>
          <p style="margin-top: 0.5rem; font-size: 0.875rem;">Pilih store/outlet pengirim terlebih dahulu</p>
        </div>
      `;
      document.getElementById('shippingMethods').style.display = 'block';
      return;
    }
    
    document.getElementById('shipping-loading').style.display = 'block';
    document.getElementById('shippingMethods').style.display = 'none';
    document.getElementById('shipping-error').style.display = 'none';
    
    fetch('{{ route("api.shipping.methods") }}?destination_city=' + encodeURIComponent(city), {
      method: 'GET',
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Content-Type': 'application/json'
      }
    })
    .then(response => {
      if (!response.ok) {
        return response.json().then(data => {
          throw { response: { status: response.status, data: data } };
        });
      }
      return response.json();
    })
    .then(data => {
      document.getElementById('shipping-loading').style.display = 'none';
      const container = document.getElementById('shippingMethods');
      container.style.display = 'block';
      container.innerHTML = '';
      
      if (data.success && data.data && data.data.methods && data.data.methods.length > 0) {
        data.data.methods.forEach(method => {
          const methodCard = document.createElement('div');
          methodCard.className = 'shipping-method-card';
          methodCard.style.cssText = 'padding: 1rem; border: 2px solid #e0e0e0; border-radius: 8px; margin-bottom: 0.75rem; cursor: pointer; background: white;';
          methodCard.dataset.methodId = method.id;
          methodCard.dataset.cost = method.cost || method.default_cost || 0;
          methodCard.dataset.type = method.type || '';
          
          methodCard.innerHTML = `
            <div style="display: flex; justify-content: space-between; align-items: center;">
              <div style="flex: 1;">
                <div style="font-weight: 600; margin-bottom: 0.25rem;">${method.name}</div>
                <div style="font-size: 0.75rem; color: #666;">${method.description || method.formatted_type || ''}</div>
              </div>
              <div style="font-weight: 700; color: #147440; margin-left: 1rem;">
                ${method.cost > 0 ? 'Rp' + new Intl.NumberFormat('id-ID').format(method.cost) : (method.default_cost > 0 ? 'Rp' + new Intl.NumberFormat('id-ID').format(method.default_cost) : 'Gratis')}
              </div>
            </div>
          `;
          
          methodCard.addEventListener('click', function() {
            document.querySelectorAll('.shipping-method-card').forEach(card => {
              card.style.borderColor = '#e0e0e0';
              card.style.background = 'white';
            });
            
            this.style.borderColor = '#147440';
            this.style.background = '#e8f5e9';
            
            const cost = parseFloat(this.dataset.cost) || 0;
            document.getElementById('shipping_method_id').value = this.dataset.methodId;
            document.getElementById('shipping_cost').value = cost;
            
            // Force update totals
            updateTotals();
          });
          
          container.appendChild(methodCard);
        });
      } else {
        container.innerHTML = '<p style="text-align: center; color: #999; padding: 2rem;">Tidak ada metode pengiriman tersedia untuk kota ini.</p>';
      }
    })
    .catch(error => {
      document.getElementById('shipping-loading').style.display = 'none';
      const errorDiv = document.getElementById('shipping-error');
      errorDiv.style.display = 'block';
      errorDiv.innerHTML = `
        <div style="padding: 1rem; background: #f8d7da; border: 1px solid #dc3545; border-radius: 8px; color: #721c24; font-size: 0.875rem;">
          <i class="bx bx-error-circle"></i> Gagal memuat metode pengiriman. Pastikan kota sudah diisi dan coba lagi.
        </div>
      `;
      MobileErrorHandler.handle(error, 'Load Shipping Methods');
    });
  }
  
  // Load instant shipping methods (after distance calculation)
  function loadInstantShippingMethods() {
    const shippingType = document.getElementById('shipping_type').value;
    if (shippingType !== 'instant') {
      return; // Only load for instant type
    }
    
    const instantCost = window.calculatedShippingCost !== null && window.calculatedShippingCost !== undefined
      ? window.calculatedShippingCost
      : 22000; // Default cost
    const distanceText = window.calculatedDistance ? ` (${window.calculatedDistance.toFixed(2)} km)` : '';
    
    const container = document.getElementById('shippingMethods');
    container.style.display = 'block';
    container.innerHTML = '';
    
    const methodCard = document.createElement('div');
    methodCard.className = 'shipping-method-card';
    methodCard.style.cssText = 'padding: 1rem; border: 2px solid #147440; border-radius: 8px; margin-bottom: 0.75rem; cursor: pointer; background: #e8f5e9;';
    methodCard.dataset.methodId = @if($instantMethodId){{ $instantMethodId }}@else'instant'@endif;
    methodCard.dataset.cost = instantCost;
    methodCard.dataset.type = 'instant';
    
    methodCard.innerHTML = `
      <div style="display: flex; justify-content: space-between; align-items: center;">
        <div style="flex: 1;">
          <div style="font-weight: 600; margin-bottom: 0.25rem;">⚡ Pengiriman Instan${distanceText}</div>
          <div style="font-size: 0.75rem; color: #666;">Pengiriman berdasarkan jarak</div>
        </div>
        <div style="font-weight: 700; color: #147440; margin-left: 1rem;">
          Rp${new Intl.NumberFormat('id-ID').format(instantCost)}
        </div>
      </div>
    `;
    
    methodCard.addEventListener('click', function() {
      document.querySelectorAll('.shipping-method-card').forEach(card => {
        card.style.borderColor = '#e0e0e0';
        card.style.background = 'white';
      });
      
      this.style.borderColor = '#147440';
      this.style.background = '#e8f5e9';
      
      // Get cost from dataset or calculated shipping cost
      let cost = parseFloat(this.dataset.cost) || 0;
      if (cost === 0 && window.calculatedShippingCost && window.calculatedShippingCost > 0) {
        cost = window.calculatedShippingCost;
      }
      
      document.getElementById('shipping_method_id').value = @if($instantMethodId){{ $instantMethodId }}@else'instant'@endif;
      document.getElementById('shipping_cost').value = cost;
      
      // Force update totals
      updateTotals();
    });
    
    container.appendChild(methodCard);
    
    // Auto-select the instant shipping method after calculation
    // Set values directly and update totals
    if (instantCost > 0) {
      document.getElementById('shipping_method_id').value = @if($instantMethodId){{ $instantMethodId }}@else'instant'@endif;
      document.getElementById('shipping_cost').value = instantCost;
      
      // Update card appearance to show it's selected
      methodCard.style.borderColor = '#147440';
      methodCard.style.background = '#e8f5e9';
      
      // Update totals
      updateTotals();
    }
  }
  
  // Handle delivery store/outlet and city change
  const deliveryStoreOutlet = document.getElementById('delivery_store_outlet');
  if (deliveryStoreOutlet) {
    deliveryStoreOutlet.addEventListener('change', function() {
      const city = document.getElementById('destination_city').value;
      const shippingType = document.getElementById('shipping_type').value;
      if (shippingType === 'delivery' && city && this.value) {
        // Reset shipping method selection
        document.getElementById('shipping_method_id').value = '';
        document.getElementById('shipping_cost').value = '0';
        updateTotals();
        // Load shipping methods
        loadShippingMethods();
      }
    });
  }
  
  const destinationCity = document.getElementById('destination_city');
  if (destinationCity) {
    destinationCity.addEventListener('change', function() {
      const shippingType = document.getElementById('shipping_type').value;
      if (shippingType === 'delivery') {
        const storeOutlet = document.getElementById('delivery_store_outlet').value;
        if (this.value && storeOutlet) {
          // Reset shipping method selection
          document.getElementById('shipping_method_id').value = '';
          document.getElementById('shipping_cost').value = '0';
          updateTotals();
          // Load shipping methods
          loadShippingMethods();
        } else if (this.value && !storeOutlet) {
          // Show message to select store/outlet first
          document.getElementById('shippingMethods').innerHTML = `
            <div style="text-align: center; padding: 2rem; color: #999;">
              <i class="bx bx-info-circle" style="font-size: 2rem;"></i>
              <p style="margin-top: 0.5rem; font-size: 0.875rem;">Pilih store/outlet pengirim terlebih dahulu</p>
            </div>
          `;
        }
      }
    });
  }
  
  // Function to calculate distance and shipping cost for instant delivery (using API)
  function calculateDistanceAndShippingInstant() {
    const address = document.querySelector('textarea[name="address"]').value;
    const city = document.getElementById('destination_city').value;
    const storeOutlet = document.getElementById('instant_store_outlet').value;
    
    if (!storeOutlet) {
      return;
    }
    
    if (!address || !city) {
      MobileNotification.warning('Silakan isi alamat lengkap dan kota terlebih dahulu');
      return;
    }
    
    // Get selected store/outlet coordinates
    const selectedOption = document.getElementById('instant_store_outlet').options[document.getElementById('instant_store_outlet').selectedIndex];
    const storeLat = parseFloat(selectedOption.dataset.lat);
    const storeLng = parseFloat(selectedOption.dataset.lng);
    const storeType = selectedOption.dataset.type;
    const storeId = selectedOption.dataset.id;
    
    if (!storeLat || !storeLng) {
      MobileNotification.warning('Koordinat store/outlet tidak ditemukan');
      return;
    }
    
    // Get user coordinates (prioritize manual coordinates if enabled)
    let latitude = null;
    let longitude = null;
    
    const useManual = document.getElementById('use-manual-coordinates')?.checked;
    if (useManual) {
      const manualLat = document.getElementById('manual_latitude')?.value;
      const manualLng = document.getElementById('manual_longitude')?.value;
      if (manualLat && manualLng) {
        latitude = parseFloat(manualLat);
        longitude = parseFloat(manualLng);
      }
    }
    
    // Fallback to stored coordinates
    if (!latitude || !longitude) {
      @if(isset($selectedAddress) && $selectedAddress->latitude && $selectedAddress->longitude)
        latitude = {{ $selectedAddress->latitude }};
        longitude = {{ $selectedAddress->longitude }};
      @elseif(isset($user['latitude']) && isset($user['longitude']) && $user['latitude'] && $user['longitude'])
        latitude = {{ $user['latitude'] }};
        longitude = {{ $user['longitude'] }};
      @else
        // Try to get from hidden fields if available
        const latInput = document.getElementById('customer_latitude');
        const lngInput = document.getElementById('customer_longitude');
        if (latInput && lngInput && latInput.value && lngInput.value) {
          latitude = parseFloat(latInput.value);
          longitude = parseFloat(lngInput.value);
        } else if (userCoordinates && userCoordinates.lat && userCoordinates.lng) {
          latitude = userCoordinates.lat;
          longitude = userCoordinates.lng;
        }
      @endif
    }
    
    if (!latitude || !longitude) {
      MobileNotification.warning('Koordinat alamat Anda tidak tersedia. Pastikan alamat sudah diisi dengan lengkap.');
      return;
    }
    
    // Show loading
    MobileLoading.show('Menghitung jarak dan ongkos kirim...');
    
    // Hide shipping methods until calculation is complete
    document.getElementById('shippingMethods').style.display = 'none';
    
    // Call API to calculate distance and shipping cost
    fetch('{{ route("distance.calculate_instant_shipping") }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
      },
      body: JSON.stringify({
        address: address,
        city: city,
        latitude: latitude,
        longitude: longitude,
        store_type: storeType,
        store_id: storeId,
        store_latitude: storeLat,
        store_longitude: storeLng
      })
    })
    .then(response => response.json())
    .then(data => {
      MobileLoading.hide();
      
      if (data.success) {
        window.calculatedDistance = data.distance_km;
        window.calculatedShippingCost = parseFloat(data.shipping_cost) || 0;
        
        // Set outlet_id if outlet
        if (storeType === 'outlet') {
          document.getElementById('outlet_id').value = storeId;
        } else {
          document.getElementById('outlet_id').value = '';
        }
        
        // Load instant shipping methods with calculated cost
        loadInstantShippingMethods();
        
        // Ensure shipping cost is set immediately after calculation
        const shippingCostInput = document.getElementById('shipping_cost');
        if (shippingCostInput && window.calculatedShippingCost > 0) {
          shippingCostInput.value = window.calculatedShippingCost;
          // Also set shipping method id to instant
          document.getElementById('shipping_method_id').value = @if($instantMethodId){{ $instantMethodId }}@else'instant'@endif;
          // Update totals immediately
          updateTotals();
        }
      } else {
        MobileNotification.error(data.error || 'Gagal menghitung jarak dan ongkos kirim');
        document.getElementById('shippingMethods').innerHTML = `
          <div style="text-align: center; padding: 2rem; color: #999;">
            <i class="bx bx-error-circle" style="font-size: 2rem;"></i>
            <p style="margin-top: 0.5rem; font-size: 0.875rem;">${data.error || 'Gagal menghitung jarak'}</p>
          </div>
        `;
        document.getElementById('shippingMethods').style.display = 'block';
      }
    })
    .catch(error => {
      MobileLoading.hide();
      MobileErrorHandler.handle(error, 'Calculate Distance');
      document.getElementById('shippingMethods').innerHTML = `
        <div style="text-align: center; padding: 2rem; color: #999;">
          <i class="bx bx-error-circle" style="font-size: 2rem;"></i>
          <p style="margin-top: 0.5rem; font-size: 0.875rem;">Terjadi kesalahan saat menghitung jarak</p>
        </div>
      `;
      document.getElementById('shippingMethods').style.display = 'block';
    });
  }
  
  // Handle instant store/outlet change - calculate distance using API
  const instantStoreOutlet = document.getElementById('instant_store_outlet');
  if (instantStoreOutlet) {
    instantStoreOutlet.addEventListener('change', function() {
      const shippingType = document.getElementById('shipping_type').value;
      if (shippingType !== 'instant') return;
      
      if (!this.value) {
        document.getElementById('shippingMethods').style.display = 'none';
        document.getElementById('shipping_method_id').value = '';
        document.getElementById('shipping_cost').value = '0';
        updateTotals();
        return;
      }
      
      // Use API to calculate distance and shipping cost
      calculateDistanceAndShippingInstant();
    });
  }
  
  // Also calculate when address or city changes (if store/outlet is already selected)
  const addressTextarea = document.querySelector('textarea[name="address"]');
  if (addressTextarea) {
    addressTextarea.addEventListener('blur', function() {
      const shippingType = document.getElementById('shipping_type').value;
      if (shippingType === 'instant' && document.getElementById('instant_store_outlet').value) {
        calculateDistanceAndShippingInstant();
      }
    });
  }
  
  if (destinationCity) {
    destinationCity.addEventListener('change', function() {
      const shippingType = document.getElementById('shipping_type').value;
      if (shippingType === 'instant' && document.getElementById('instant_store_outlet').value) {
        calculateDistanceAndShippingInstant();
      }
    });
  }
  
  function updateTotals() {
    const subtotal = {{ $totals['subtotal'] }};
    const discount = {{ $totals['discount'] }};
    
    // Get shipping cost from hidden field
    const shippingCostInput = document.getElementById('shipping_cost');
    let shippingCost = 0;
    
    if (shippingCostInput) {
      shippingCost = parseFloat(shippingCostInput.value) || 0;
    }
    
    // If shipping cost is 0 but we have calculated shipping cost for instant, use that
    if (shippingCost === 0 && window.calculatedShippingCost && window.calculatedShippingCost > 0) {
      const shippingType = document.getElementById('shipping_type').value;
      if (shippingType === 'instant') {
        shippingCost = window.calculatedShippingCost;
        if (shippingCostInput) {
          shippingCostInput.value = shippingCost;
        }
      }
    }
    
    const total = subtotal - discount + shippingCost;
    
    // Update display
    const shippingCostDisplay = document.getElementById('shippingCostDisplay');
    const totalAmountDisplay = document.getElementById('totalAmountDisplay');
    
    if (shippingCostDisplay) {
      shippingCostDisplay.textContent = 'Rp' + new Intl.NumberFormat('id-ID').format(shippingCost);
    }
    
    if (totalAmountDisplay) {
      totalAmountDisplay.textContent = 'Rp' + new Intl.NumberFormat('id-ID').format(total);
    }
  }
  
  // Toggle manual coordinates
  const useManualCoordinates = document.getElementById('use-manual-coordinates');
  if (useManualCoordinates) {
    useManualCoordinates.addEventListener('change', function() {
      const manualCoordsDiv = document.getElementById('manual-coordinates');
      if (this.checked) {
        manualCoordsDiv.style.display = 'block';
      } else {
        manualCoordsDiv.style.display = 'none';
        document.getElementById('manual_latitude').value = '';
        document.getElementById('manual_longitude').value = '';
        // Restore original coordinates
        const latInput = document.getElementById('customer_latitude');
        const lngInput = document.getElementById('customer_longitude');
        @if(isset($selectedAddress) && $selectedAddress->latitude && $selectedAddress->longitude)
          if (latInput) latInput.value = '{{ $selectedAddress->latitude }}';
          if (lngInput) lngInput.value = '{{ $selectedAddress->longitude }}';
        @elseif(isset($user['latitude']) && isset($user['longitude']) && $user['latitude'] && $user['longitude'])
          if (latInput) latInput.value = '{{ $user['latitude'] }}';
          if (lngInput) lngInput.value = '{{ $user['longitude'] }}';
        @endif
      }
    });
    
    // Update hidden coordinates when manual coordinates change
    document.getElementById('manual_latitude')?.addEventListener('change', function() {
      if (useManualCoordinates.checked && this.value) {
        document.getElementById('customer_latitude').value = this.value;
        // Update userCoordinates for distance calculation
        const lng = document.getElementById('manual_longitude').value;
        if (lng) {
          userCoordinates = {
            lat: parseFloat(this.value),
            lng: parseFloat(lng)
          };
          // Update dropdowns if shipping type is selected
          const shippingType = document.getElementById('shipping_type').value;
          if (shippingType === 'instant') {
            updateStoreOutletDropdownWithDistance('instant_store_outlet');
            const instantStoreOutlet = document.getElementById('instant_store_outlet').value;
            if (instantStoreOutlet) {
              calculateDistanceAndShippingInstant();
            }
          } else if (shippingType === 'delivery') {
            updateStoreOutletDropdownWithDistance('delivery_store_outlet');
          } else if (shippingType === 'pickup') {
            loadPickupLocations();
          }
        }
      }
    });
    
    document.getElementById('manual_longitude')?.addEventListener('change', function() {
      if (useManualCoordinates.checked && this.value) {
        document.getElementById('customer_longitude').value = this.value;
        // Update userCoordinates for distance calculation
        const lat = document.getElementById('manual_latitude').value;
        if (lat) {
          userCoordinates = {
            lat: parseFloat(lat),
            lng: parseFloat(this.value)
          };
          // Update dropdowns if shipping type is selected
          const shippingType = document.getElementById('shipping_type').value;
          if (shippingType === 'instant') {
            updateStoreOutletDropdownWithDistance('instant_store_outlet');
            const instantStoreOutlet = document.getElementById('instant_store_outlet').value;
            if (instantStoreOutlet) {
              calculateDistanceAndShippingInstant();
            }
          } else if (shippingType === 'delivery') {
            updateStoreOutletDropdownWithDistance('delivery_store_outlet');
          } else if (shippingType === 'pickup') {
            loadPickupLocations();
          }
        }
      }
    });
  }
  
  // Update hidden fields when form fields change
  document.getElementById('form_recipient_name')?.addEventListener('change', function() {
    document.getElementById('first_name').value = this.value;
  });
  
  document.getElementById('destination_city')?.addEventListener('change', function() {
    document.getElementById('city').value = this.value;
  });
  
  // Update user coordinates when address changes
  @if(isset($selectedAddress) && $selectedAddress->latitude && $selectedAddress->longitude)
    userCoordinates = {
      lat: {{ $selectedAddress->latitude }},
      lng: {{ $selectedAddress->longitude }}
    };
  @elseif(isset($user['latitude']) && isset($user['longitude']) && $user['latitude'] && $user['longitude'])
    userCoordinates = {
      lat: {{ $user['latitude'] }},
      lng: {{ $user['longitude'] }}
    };
  @endif
  
  // Handle address selection from dropdown (if exists)
  const addressSelect = document.getElementById('addressSelect');
  if (addressSelect) {
    addressSelect.addEventListener('change', function() {
      if (this.value) {
        window.location.href = '{{ route("mobile.checkout") }}?address_id=' + this.value;
      }
    });
  }
  
  // Handle form submission
  document.getElementById('checkoutForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const shippingType = document.getElementById('shipping_type').value;
    if (!shippingType) {
      Swal.fire({
        icon: 'warning',
        title: 'Tipe Pengiriman',
        text: 'Silakan pilih tipe pengiriman terlebih dahulu',
        confirmButtonColor: '#147440',
        confirmButtonText: 'Mengerti',
        width: '90%',
        customClass: {
          popup: 'mobile-swal-popup'
        }
      });
      return;
    }
    
    // Validate based on shipping type
    if (shippingType === 'pickup') {
      const pickupLocationId = document.getElementById('pickup_location_id').value;
      if (!pickupLocationId) {
        Swal.fire({
          icon: 'warning',
          title: 'Lokasi Pickup',
          text: 'Silakan pilih lokasi pickup terlebih dahulu',
          confirmButtonColor: '#147440',
          confirmButtonText: 'Mengerti',
          width: '90%',
          customClass: {
            popup: 'mobile-swal-popup'
          }
        });
        return;
      }
    } else if (shippingType === 'instant') {
      const instantStoreOutlet = document.getElementById('instant_store_outlet').value;
      if (!instantStoreOutlet) {
        Swal.fire({
          icon: 'warning',
          title: 'Store/Outlet',
          text: 'Silakan pilih store/outlet pengirim terlebih dahulu',
          confirmButtonColor: '#147440',
          confirmButtonText: 'Mengerti',
          width: '90%',
          customClass: {
            popup: 'mobile-swal-popup'
          }
        });
        return;
      }
    } else if (shippingType === 'delivery') {
      const deliveryStoreOutlet = document.getElementById('delivery_store_outlet').value;
      const destinationCity = document.getElementById('destination_city').value;
      if (!deliveryStoreOutlet || !destinationCity) {
        Swal.fire({
          icon: 'warning',
          title: 'Data Pengiriman',
          text: 'Silakan pilih store/outlet dan kota tujuan terlebih dahulu',
          confirmButtonColor: '#147440',
          confirmButtonText: 'Mengerti',
          width: '90%',
          customClass: {
            popup: 'mobile-swal-popup'
          }
        });
        return;
      }
      
      const shippingMethodId = document.getElementById('shipping_method_id').value;
      if (!shippingMethodId) {
        Swal.fire({
          icon: 'warning',
          title: 'Metode Pengiriman',
          text: 'Silakan pilih metode pengiriman terlebih dahulu',
          confirmButtonColor: '#147440',
          confirmButtonText: 'Mengerti',
          width: '90%',
          customClass: {
            popup: 'mobile-swal-popup'
          }
        });
        return;
      }
    }
    
    // Update hidden fields from visible form fields before submit
    const formRecipientName = document.getElementById('form_recipient_name');
    if (formRecipientName) {
      document.getElementById('first_name').value = formRecipientName.value;
    }
    
    const destinationCitySelect = document.getElementById('destination_city');
    if (destinationCitySelect) {
      document.getElementById('city').value = destinationCitySelect.value;
    }
    
    // Update coordinates from manual input if enabled
    const useManual = document.getElementById('use-manual-coordinates')?.checked;
    if (useManual) {
      const manualLat = document.getElementById('manual_latitude')?.value;
      const manualLng = document.getElementById('manual_longitude')?.value;
      if (manualLat && manualLng) {
        document.getElementById('customer_latitude').value = manualLat;
        document.getElementById('customer_longitude').value = manualLng;
      }
    }
    
    // Create form data and add shipping_type
    const formData = new FormData(this);
    formData.set('shipping_type', shippingType);
    
    // Add shipping type specific data
    if (shippingType === 'pickup') {
      const pickupLocationType = document.getElementById('pickup_location_type').value;
      const pickupLocationId = document.getElementById('pickup_location_id').value;
      formData.set('pickup_location_type', pickupLocationType);
      formData.set('pickup_location_id', pickupLocationId);
      
      // Set outlet_id if pickup location is outlet
      if (pickupLocationType === 'outlet') {
        formData.set('outlet_id', pickupLocationId);
      } else {
        formData.delete('outlet_id');
      }
      
      // Ensure shipping method is set for pickup
      formData.set('shipping_method_id', 'pickup');
      formData.set('shipping_cost', '0');
    } else if (shippingType === 'instant') {
      const instantStoreOutlet = document.getElementById('instant_store_outlet').value;
      if (instantStoreOutlet) {
        const option = document.getElementById('instant_store_outlet').options[document.getElementById('instant_store_outlet').selectedIndex];
        const storeType = option.dataset.type;
        const storeId = option.dataset.id;
        
        // Add hidden fields for instant delivery (similar to web version)
        formData.set('instant_store_outlet_hidden', storeType);
        formData.set('instant_store_outlet_id_hidden', storeId);
        
        if (storeType === 'outlet') {
          formData.set('outlet_id', storeId);
        } else {
          formData.delete('outlet_id');
        }
      }
    } else if (shippingType === 'delivery') {
      const deliveryStoreOutlet = document.getElementById('delivery_store_outlet').value;
      if (deliveryStoreOutlet) {
        const option = document.getElementById('delivery_store_outlet').options[document.getElementById('delivery_store_outlet').selectedIndex];
        if (option.dataset.type === 'outlet') {
          formData.set('outlet_id', option.dataset.id);
        } else {
          formData.delete('outlet_id');
        }
      }
    }
    
    // Final check: Ensure shipping_method_id and shipping_cost are set
    const shippingMethodId = document.getElementById('shipping_method_id').value;
    const shippingCost = document.getElementById('shipping_cost').value;
    
    if (!shippingMethodId) {
      Swal.fire({
        icon: 'warning',
        title: 'Metode Pengiriman',
        text: 'Silakan pilih metode pengiriman terlebih dahulu',
        confirmButtonColor: '#147440',
        confirmButtonText: 'Mengerti',
        width: '90%',
        customClass: {
          popup: 'mobile-swal-popup'
        }
      });
      btn.disabled = false;
      btn.innerHTML = '<i class="bx bx-credit-card"></i> Bayar Sekarang';
      MobileLoading.hide();
      return;
    }
    
    formData.set('shipping_method_id', shippingMethodId);
    formData.set('shipping_cost', shippingCost || '0');
    
    // Show loading
    const btn = document.getElementById('checkoutBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Memproses...';
    
    MobileLoading.show('Memproses checkout...');
    
    fetch('{{ route("checkout.process") }}', {
      method: 'POST',
      body: formData,
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
      }
    })
    .then(response => {
      if (!response.ok) {
        return response.json().then(data => {
          throw { response: { status: response.status, data: data } };
        });
      }
      return response.json();
    })
    .then(data => {
      if (data.success) {
        // Initialize Midtrans Snap
        snap.pay(data.snap_token, {
          onSuccess: function(result) {
            window.location.href = '{{ route("payment.finish") }}?order_id=' + data.order_id;
          },
          onPending: function(result) {
            window.location.href = '{{ route("payment.finish") }}?order_id=' + data.order_id + '&status=unfinish';
          },
          onError: function(result) {
            window.location.href = '{{ route("payment.finish") }}?order_id=' + data.order_id + '&status=error';
          }
        });
      } else {
        Swal.fire({
          icon: 'error',
          title: 'Gagal Checkout',
          text: data.error || 'Gagal memproses checkout',
          confirmButtonColor: '#dc3545',
          confirmButtonText: 'Mengerti',
          width: '90%',
          customClass: {
            popup: 'mobile-swal-popup'
          }
        });
        btn.disabled = false;
        btn.innerHTML = '<i class="bx bx-credit-card"></i> Bayar Sekarang';
      }
    })
    .catch(error => {
      MobileErrorHandler.handle(error, 'Checkout');
      btn.disabled = false;
      btn.innerHTML = '<i class="bx bx-credit-card"></i> Bayar Sekarang';
    })
    .finally(() => {
      MobileLoading.hide();
    });
  });
  
  // City change is already handled in destination_city change event above
</script>
@endpush
