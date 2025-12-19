@extends('mobile.layouts.app')

@php
use Illuminate\Support\Facades\Storage;
@endphp

@section('title', 'Wishlist')

@section('content')
<div style="background: white; padding: 1rem; margin-bottom: 0.5rem;">
  <div style="display: flex; justify-content: space-between; align-items: center;">
    <h5 style="font-size: 1rem; font-weight: 700; margin: 0; color: #333;">
      <i class="bx bx-heart"></i> Wishlist Saya
    </h5>
    <span style="font-size: 0.875rem; color: #666;">{{ $wishlists->count() }} item</span>
  </div>
</div>

@if($wishlists->count() > 0)
  <div class="product-grid">
    @foreach($wishlists as $wishlist)
      @php($product = $wishlist->product)
      @if($product)
        <a href="{{ route('mobile.shop.detail', $product->slug) }}" class="product-card">
          <div style="position: relative;">
            <img src="{{ $product->main_image_path ? Storage::url($product->main_image_path) : asset('sneat/assets/img/placeholder.png') }}" 
                 alt="{{ $product->name }}" 
                 class="product-image"
                 onerror="this.src='{{ asset('sneat/assets/img/placeholder.png') }}'">
            <button type="button" 
                    onclick="event.preventDefault(); toggleWishlist({{ $product->id }}, this)"
                    style="position: absolute; top: 0.5rem; right: 0.5rem; background: rgba(255,255,255,0.9); border: none; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; z-index: 10;">
              <i class="bx bxs-heart" style="color: #dc3545; font-size: 1.125rem;"></i>
            </button>
          </div>
          <div class="product-info">
            <div class="product-name">{{ $product->name }}</div>
            <div class="product-price">
              <span class="price-current">Rp{{ number_format($product->price, 0, ',', '.') }}</span>
            </div>
            @php
              $reviews = DB::table('product_reviews')->where('product_id', $product->id)->get();
              $avgRating = $reviews->count() > 0 ? round($reviews->avg('rating'), 1) : 0;
            @endphp
            @if($avgRating > 0)
              <div style="display: flex; align-items: center; gap: 0.25rem; margin-top: 0.25rem;">
                @for($i = 1; $i <= 5; $i++)
                  <i class="bx {{ $i <= round($avgRating) ? 'bxs-star' : 'bx-star' }}" 
                     style="color: {{ $i <= round($avgRating) ? '#ffc107' : '#ddd' }}; font-size: 0.75rem;"></i>
                @endfor
                <span style="font-size: 0.7rem; color: #666; margin-left: 0.25rem;">{{ number_format($avgRating, 1) }}</span>
              </div>
            @endif
          </div>
        </a>
      @endif
    @endforeach
  </div>
@else
  <div class="empty-state">
    <i class="bx bx-heart" style="font-size: 4rem; color: #ddd; margin-bottom: 1rem;"></i>
    <p>Wishlist Anda kosong</p>
    <a href="{{ route('mobile.shop') }}" 
       style="display: inline-block; margin-top: 1rem; padding: 0.75rem 1.5rem; background: #147440; color: white; border-radius: 8px; text-decoration: none; font-weight: 600;">
      Mulai Belanja
    </a>
  </div>
@endif
@endsection

@push('scripts')
<script>
  function toggleWishlist(productId, button) {
    fetch('{{ route("mobile.wishlist.toggle") }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: JSON.stringify({ product_id: productId })
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        MobileNotification.success(data.message);
        // Remove card from view
        button.closest('.product-card').parentElement.remove();
        // Update count
        const countEl = document.querySelector('span[style*="color: #666"]');
        if (countEl) {
          const newCount = parseInt(countEl.textContent) - 1;
          countEl.textContent = newCount + ' item';
        }
        // Update wishlist badge
        updateWishlistBadge(data.count);
      } else {
        MobileNotification.error(data.message || 'Gagal mengupdate wishlist');
      }
    })
    .catch(error => {
      MobileErrorHandler.handle(error);
    });
  }
  
  function updateWishlistBadge(count) {
    // Update badge if exists in header
    const badge = document.getElementById('wishlistBadge');
    if (badge) {
      if (count > 0) {
        badge.textContent = count > 99 ? '99+' : count;
        badge.style.display = 'flex';
      } else {
        badge.style.display = 'none';
      }
    }
  }
</script>
@endpush
