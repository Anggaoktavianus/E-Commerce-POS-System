@extends('mobile.layouts.app')

@php
use Illuminate\Support\Facades\DB;
@endphp

@php
use Illuminate\Support\Facades\Storage;
@endphp

@section('title', 'Home')

@push('scripts')
<script>
  // Pull to refresh handler
  document.getElementById('mainContent').addEventListener('pulltorefresh', function(e) {
    MobileLoading.show('Memuat ulang...');
    
    // Reload page data
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
      // You can update specific parts of the page here
      // For now, just reload
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
<!-- Promotional Carousel -->
<div class="promo-carousel">
  <div class="scroll-container">
    @if(isset($promoBanners) && $promoBanners->count() > 0)
      @foreach($promoBanners as $banner)
        <div class="promo-card">
          <div class="promo-icon">P</div>
          <div class="promo-content">
            <div class="promo-title">{{ $banner->title ?? 'Diskon +5%' }}</div>
            <div class="promo-action">
              Klaim Hari Ini <i class="bx bx-chevron-right"></i>
            </div>
          </div>
        </div>
      @endforeach
    @else
      <div class="promo-card">
        <div class="promo-icon">P</div>
        <div class="promo-content">
          <div class="promo-title">Diskon +5%</div>
          <div class="promo-action">
            Klaim Hari Ini <i class="bx bx-chevron-right"></i>
          </div>
        </div>
      </div>
    @endif
    
   
  </div>
</div>

<!-- Category Navigation -->
<div class="category-tabs">
  <a href="{{ route('mobile.home') }}" class="category-tab active">For You</a>
  <a href="{{ route('mobile.shop', ['category' => 'mall']) }}" class="category-tab">
    <i class="bx bx-check-circle"></i> All
  </a>
  @if(isset($categories))
    @foreach($categories->take(5) as $category)
      <a href="{{ route('mobile.shop', ['category' => $category->slug]) }}" class="category-tab">
        {{ $category->name }}
      </a>
    @endforeach
  @endif
</div>

<!-- Flash Sale Section -->
<div class="section-title">
  <h5>Flash Sale</h5>
  <div class="countdown">08:20:40</div>
</div>

<div class="product-grid">
  @if(isset($flashSaleProducts) && $flashSaleProducts->count() > 0)
    @foreach($flashSaleProducts->take(6) as $product)
      <a href="{{ route('mobile.shop.detail', $product->slug) }}" class="product-card">
        <div style="position: relative;">
          <img src="{{ $product->main_image_path ? Storage::url($product->main_image_path) : asset('sneat/assets/img/placeholder.png') }}" 
               alt="{{ $product->name }}" 
               class="product-image"
               onerror="this.src='{{ asset('sneat/assets/img/placeholder.png') }}'">
          @if($product->is_featured || $product->is_bestseller)
            <div class="product-badge">
              {{ $product->is_bestseller ? 'HOT' : 'NEW' }}
            </div>
          @endif
        </div>
        <div class="product-info">
          <div class="product-name">{{ $product->name }}</div>
          <div class="product-price">
            <span class="price-current">Rp{{ number_format($product->price, 0, ',', '.') }}</span>
            @if($product->price > 0)
              <span class="price-original">Rp{{ number_format($product->price * 1.2, 0, ',', '.') }}</span>
            @endif
          </div>
          <div class="product-rating">
            @php
              $reviews = DB::table('product_reviews')->where('product_id', $product->id)->get();
              $avgRating = $reviews->count() > 0 ? round($reviews->avg('rating'), 1) : 0;
              $totalSold = DB::table('order_items')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->where('order_items.product_id', $product->id)
                ->whereIn('orders.status', ['delivered', 'completed'])
                ->sum('order_items.quantity');
            @endphp
            @if($avgRating > 0)
              <div style="display: flex; align-items: center; gap: 0.25rem;">
                @for($i = 1; $i <= 5; $i++)
                  <i class="bx {{ $i <= round($avgRating) ? 'bxs-star' : 'bx-star' }}" 
                     style="color: {{ $i <= round($avgRating) ? '#ffc107' : '#ddd' }}; font-size: 0.75rem;"></i>
                @endfor
                <span style="font-size: 0.75rem; color: #666; margin-left: 0.25rem;">{{ number_format($avgRating, 1) }}</span>
              </div>
            @else
              <div style="display: flex; align-items: center; gap: 0.25rem;">
                @for($i = 1; $i <= 5; $i++)
                  <i class="bx bx-star" style="color: #ddd; font-size: 0.75rem;"></i>
                @endfor
                <span style="font-size: 0.75rem; color: #999; margin-left: 0.25rem;">Baru</span>
              </div>
            @endif
            @if($totalSold > 0)
              <span style="font-size: 0.75rem; color: #666; margin-left: 0.5rem;">
                @if($totalSold >= 1000)
                  {{ number_format($totalSold / 1000, 1) }}rb+ terjual
                @else
                  {{ $totalSold }}+ terjual
                @endif
              </span>
            @else
              <span style="font-size: 0.75rem; color: #999; margin-left: 0.5rem;">Belum terjual</span>
            @endif
          </div>
        </div>
      </a>
    @endforeach
  @else
    <div class="empty-state">
      <i class="bx bx-package"></i>
      <p>Tidak ada produk flash sale</p>
    </div>
  @endif
</div>

<!-- Featured Products Section -->
@if(isset($featuredProducts) && $featuredProducts->count() > 0)
<div class="section-title">
  <h5>Produk Unggulan</h5>
</div>

<div class="product-grid">
  @foreach($featuredProducts->take(8) as $product)
    <a href="{{ route('mobile.shop.detail', $product->slug) }}" class="product-card">
      <div style="position: relative;">
        <img src="{{ $product->main_image_path ? Storage::url($product->main_image_path) : asset('sneat/assets/img/placeholder.png') }}" 
             alt="{{ $product->name }}" 
             class="product-image"
             onerror="this.src='{{ asset('sneat/assets/img/placeholder.png') }}'">
        @if($product->is_featured)
          <div class="product-badge">NEW</div>
        @endif
      </div>
      <div class="product-info">
        <div class="product-name">{{ $product->name }}</div>
        <div class="product-price">
          <span class="price-current">Rp{{ number_format($product->price, 0, ',', '.') }}</span>
        </div>
        <div class="product-rating">
          @php
            $reviews = DB::table('product_reviews')->where('product_id', $product->id)->get();
            $avgRating = $reviews->count() > 0 ? round($reviews->avg('rating'), 1) : 0;
            $totalSold = DB::table('order_items')
              ->join('orders', 'order_items.order_id', '=', 'orders.id')
              ->where('order_items.product_id', $product->id)
              ->whereIn('orders.status', ['delivered', 'completed'])
              ->sum('order_items.quantity');
          @endphp
          @if($avgRating > 0)
            <div style="display: flex; align-items: center; gap: 0.25rem;">
              @for($i = 1; $i <= 5; $i++)
                <i class="bx {{ $i <= round($avgRating) ? 'bxs-star' : 'bx-star' }}" 
                   style="color: {{ $i <= round($avgRating) ? '#ffc107' : '#ddd' }}; font-size: 0.75rem;"></i>
              @endfor
              <span style="font-size: 0.75rem; color: #666; margin-left: 0.25rem;">{{ number_format($avgRating, 1) }}</span>
            </div>
          @else
            <div style="display: flex; align-items: center; gap: 0.25rem;">
              @for($i = 1; $i <= 5; $i++)
                <i class="bx bx-star" style="color: #ddd; font-size: 0.75rem;"></i>
              @endfor
              <span style="font-size: 0.75rem; color: #999; margin-left: 0.25rem;">Baru</span>
            </div>
          @endif
          @if($totalSold > 0)
            <span style="font-size: 0.75rem; color: #666; margin-left: 0.5rem;">
              @if($totalSold >= 1000)
                {{ number_format($totalSold / 1000, 1) }}rb+ terjual
              @else
                {{ $totalSold }}+ terjual
              @endif
            </span>
          @else
            <span style="font-size: 0.75rem; color: #999; margin-left: 0.5rem;">Belum terjual</span>
          @endif
        </div>
      </div>
    </a>
  @endforeach
</div>
@endif

<!-- Promotional Banner -->
<div class="promo-banner">
  <div class="promo-text">
    <strong>PLUS</strong>
    <small>Pakai promonya tiap belanja Kupon Diskon s.d 5% buatmu!</small>
  </div>
  <button class="promo-btn">Klaim</button>
</div>

<!-- Bestseller Products -->
@if(isset($bestsellerProducts) && $bestsellerProducts->count() > 0)
<div class="section-title">
  <h5>Produk Terlaris</h5>
</div>

<div class="product-grid">
  @foreach($bestsellerProducts->take(8) as $product)
    <a href="{{ route('mobile.shop.detail', $product->slug) }}" class="product-card">
      <div style="position: relative;">
        <img src="{{ $product->main_image_path ? Storage::url($product->main_image_path) : asset('sneat/assets/img/placeholder.png') }}" 
             alt="{{ $product->name }}" 
             class="product-image"
             onerror="this.src='{{ asset('sneat/assets/img/placeholder.png') }}'">
        @if($product->is_bestseller)
          <div class="product-badge">HOT</div>
        @endif
      </div>
      <div class="product-info">
        <div class="product-name">{{ $product->name }}</div>
        <div class="product-price">
          <span class="price-current">Rp{{ number_format($product->price, 0, ',', '.') }}</span>
        </div>
        <div class="product-rating">
          @php
            $reviews = DB::table('product_reviews')->where('product_id', $product->id)->get();
            $avgRating = $reviews->count() > 0 ? round($reviews->avg('rating'), 1) : 0;
            $totalSold = DB::table('order_items')
              ->join('orders', 'order_items.order_id', '=', 'orders.id')
              ->where('order_items.product_id', $product->id)
              ->whereIn('orders.status', ['delivered', 'completed'])
              ->sum('order_items.quantity');
          @endphp
          @if($avgRating > 0)
            <div style="display: flex; align-items: center; gap: 0.25rem;">
              @for($i = 1; $i <= 5; $i++)
                <i class="bx {{ $i <= round($avgRating) ? 'bxs-star' : 'bx-star' }}" 
                   style="color: {{ $i <= round($avgRating) ? '#ffc107' : '#ddd' }}; font-size: 0.75rem;"></i>
              @endfor
              <span style="font-size: 0.75rem; color: #666; margin-left: 0.25rem;">{{ number_format($avgRating, 1) }}</span>
            </div>
          @else
            <div style="display: flex; align-items: center; gap: 0.25rem;">
              @for($i = 1; $i <= 5; $i++)
                <i class="bx bx-star" style="color: #ddd; font-size: 0.75rem;"></i>
              @endfor
              <span style="font-size: 0.75rem; color: #999; margin-left: 0.25rem;">Baru</span>
            </div>
          @endif
          @if($totalSold > 0)
            <span style="font-size: 0.75rem; color: #666; margin-left: 0.5rem;">
              @if($totalSold >= 1000)
                {{ number_format($totalSold / 1000, 1) }}rb+ terjual
              @else
                {{ $totalSold }}+ terjual
              @endif
            </span>
          @else
            <span style="font-size: 0.75rem; color: #999; margin-left: 0.5rem;">Belum terjual</span>
          @endif
        </div>
      </div>
    </a>
  @endforeach
</div>
@endif

<!-- Recently Viewed Products -->
<div id="recentlyViewedSection" style="display: none; background: white; padding: 1rem; margin-top: 0.5rem;">
  <div class="section-title">
    <h5><i class="bx bx-time"></i> Baru Dilihat</h5>
  </div>
  <div class="product-grid" id="recentlyViewedGrid">
    <!-- Will be populated by JavaScript -->
  </div>
</div>
@endsection

@push('scripts')
<script>
  // Load Recently Viewed Products
  (function() {
    const recentlyViewed = JSON.parse(localStorage.getItem('recentlyViewed') || '[]');
    const section = document.getElementById('recentlyViewedSection');
    const grid = document.getElementById('recentlyViewedGrid');
    
    if (recentlyViewed.length > 0) {
      section.style.display = 'block';
      grid.innerHTML = recentlyViewed.slice(0, 8).map(product => `
        <a href="/m/shop/${product.slug}" class="product-card">
          <div style="position: relative;">
            <img src="${product.image}" 
                 alt="${product.name}" 
                 class="product-image"
                 onerror="this.src='{{ asset('sneat/assets/img/placeholder.png') }}'">
          </div>
          <div class="product-info">
            <div class="product-name">${product.name}</div>
            <div class="product-price">
              <span class="price-current">Rp${new Intl.NumberFormat('id-ID').format(product.price)}</span>
            </div>
          </div>
        </a>
      `).join('');
    }
  })();
</script>
@endpush

@push('styles')
<style>
  .product-card {
    position: relative;
  }
</style>
@endpush
