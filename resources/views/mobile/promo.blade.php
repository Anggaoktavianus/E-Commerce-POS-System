@extends('mobile.layouts.app')

@php
use Illuminate\Support\Facades\Storage;
@endphp

@section('title', 'Promo & Diskon')

@push('scripts')
<script>
  // Pull to refresh handler
  document.getElementById('mainContent').addEventListener('pulltorefresh', function(e) {
    MobileLoading.show('Memuat ulang promo...');
    
    fetch(window.location.href, {
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'X-Pull-To-Refresh': 'true'
      }
    })
    .then(response => {
      if (response.ok) {
        return response.text();
      }
      throw new Error('Gagal memuat data');
    })
    .then(html => {
      setTimeout(() => {
        location.reload();
      }, 500);
    })
    .catch(error => {
      MobileErrorHandler.handle(error);
      e.detail.callback();
    })
    .finally(() => {
      MobileLoading.hide();
    });
  });
</script>
@endpush

@section('content')
<!-- Promo Carousel -->
@if(isset($promoSlides) && $promoSlides->count() > 0)
<div class="promo-carousel">
  <div class="scroll-container">
    @foreach($promoSlides as $slide)
      <div class="promo-card" style="min-width: 300px;">
        <div class="promo-icon">
          <i class="bx bx-purchase-tag"></i>
        </div>
        <div class="promo-content">
          <div class="promo-title">{{ $slide->title ?? 'Promo Spesial' }}</div>
          <div class="promo-action">
            {{ $slide->subtitle ?? 'Lihat Detail' }} <i class="bx bx-chevron-right"></i>
          </div>
        </div>
        @if($slide->image_path)
          <img src="{{ Storage::url($slide->image_path) }}" 
               alt="{{ $slide->title }}"
               style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px; margin-left: 1rem;">
        @endif
      </div>
    @endforeach
  </div>
</div>
@endif

<!-- Promotional Banners -->
@if(isset($promoBanners) && $promoBanners->count() > 0)
<div style="padding: 0.75rem;">
  <h5 style="font-size: 1rem; font-weight: 700; color: #333; margin-bottom: 0.75rem;">
    <i class="bx bx-purchase-tag"></i> Promo & Penawaran
  </h5>
  
  @foreach($promoBanners as $banner)
    <div class="promo-banner" style="margin-bottom: 0.75rem; cursor: pointer;" 
         onclick="window.location.href='{{ $banner->link_url ?? '#' }}'">
      @if($banner->image_path)
        <img src="{{ Storage::url($banner->image_path) }}" 
             alt="{{ $banner->title }}"
             style="width: 100%; height: 150px; object-fit: cover; border-radius: 12px;">
      @else
        <div class="promo-text">
          <strong>{{ $banner->title ?? 'Promo Spesial' }}</strong>
          <small>{{ $banner->subtitle ?? 'Dapatkan penawaran menarik sekarang!' }}</small>
        </div>
        <button class="promo-btn">Klaim</button>
      @endif
    </div>
  @endforeach
</div>
@endif

<!-- Promo Products -->
@if(isset($promoProducts) && $promoProducts->count() > 0)
<div class="section-title">
  <h5>Produk Promo</h5>
</div>

<div class="product-grid">
  @foreach($promoProducts as $product)
    <a href="{{ route('mobile.shop.detail', $product->slug) }}" class="product-card">
      <div style="position: relative;">
        <img src="{{ $product->main_image_path ? Storage::url($product->main_image_path) : asset('sneat/assets/img/placeholder.png') }}" 
             alt="{{ $product->name }}" 
             class="product-image"
             onerror="this.src='{{ asset('sneat/assets/img/placeholder.png') }}'">
        @if($product->is_featured)
          <div class="product-badge">NEW</div>
        @endif
        @if($product->is_bestseller)
          <div class="product-badge" style="background: #ff6b35;">HOT</div>
        @endif
        @if($product->is_featured || $product->is_bestseller)
          <div class="product-badge" style="background: #dc3545; top: auto; bottom: 0.5rem; right: 0.5rem;">
            PROMO
          </div>
        @endif
      </div>
      <div class="product-info">
        <div class="product-name">{{ $product->name }}</div>
        <div class="product-price">
          <span class="price-current">Rp{{ number_format($product->price, 0, ',', '.') }}</span>
          @if($product->unit)
            <small style="font-size: 0.7rem; color: #999;">/ {{ $product->unit }}</small>
          @endif
        </div>
        @if($product->stock_qty > 0)
          <div style="font-size: 0.7rem; color: #147440; margin-top: 0.25rem;">
            <i class="bx bx-check-circle"></i> Stok tersedia
          </div>
        @else
          <div style="font-size: 0.7rem; color: #dc3545; margin-top: 0.25rem;">
            <i class="bx bx-x-circle"></i> Stok habis
          </div>
        @endif
      </div>
    </a>
  @endforeach
</div>
@endif

<!-- Empty State -->
@if((!isset($promoBanners) || $promoBanners->count() == 0) && 
    (!isset($promoProducts) || $promoProducts->count() == 0) && 
    (!isset($promoSlides) || $promoSlides->count() == 0))
<div class="empty-state">
  <i class="bx bx-purchase-tag"></i>
  <p>Belum ada promo tersedia saat ini</p>
  <a href="{{ route('mobile.shop') }}" 
     style="display: inline-block; background: #147440; color: white; padding: 0.75rem 1.5rem; border-radius: 8px; text-decoration: none; font-weight: 600; margin-top: 1rem;">
    <i class="bx bx-shopping-bag"></i> Lihat Semua Produk
  </a>
</div>
@endif
@endsection
