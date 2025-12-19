@extends('mobile.layouts.app')

@php
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
@endphp

@section('title', $product->name ?? 'Product Detail')

@section('content')
<!-- Product Images Carousel with Zoom -->
<div style="background: white; position: relative;">
  <div id="productCarousel" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-inner">
      @if(isset($images) && $images->count() > 0)
        @foreach($images as $index => $image)
          <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
            <img src="{{ Storage::url($image->image_path) }}" 
                 class="d-block w-100 product-image-zoom" 
                 alt="{{ $product->name }}"
                 data-image="{{ Storage::url($image->image_path) }}"
                 style="height: 300px; object-fit: cover; cursor: pointer;">
          </div>
        @endforeach
      @else
        <div class="carousel-item active">
          <img src="{{ $product->main_image_path ? Storage::url($product->main_image_path) : asset('sneat/assets/img/placeholder.png') }}" 
               class="d-block w-100 product-image-zoom" 
               alt="{{ $product->name }}"
               data-image="{{ $product->main_image_path ? Storage::url($product->main_image_path) : asset('sneat/assets/img/placeholder.png') }}"
               style="height: 300px; object-fit: cover; cursor: pointer;"
               onerror="this.src='{{ asset('sneat/assets/img/placeholder.png') }}'">
        </div>
      @endif
    </div>
    @if(isset($images) && $images->count() > 1)
      <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span>
      </button>
      <button class="carousel-control-next" type="button" data-bs-target="#productCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon"></span>
      </button>
    @endif
  </div>
  
  <!-- Image Gallery Thumbnails -->
  @if(isset($images) && $images->count() > 1)
    <div style="padding: 0.5rem; display: flex; gap: 0.5rem; overflow-x: auto; background: #f8f9fa;">
      @foreach($images as $index => $image)
        <img src="{{ Storage::url($image->image_path) }}" 
             class="thumbnail-image {{ $index === 0 ? 'active' : '' }}"
             data-bs-target="#productCarousel"
             data-bs-slide-to="{{ $index }}"
             style="width: 60px; height: 60px; object-fit: cover; border-radius: 6px; border: 2px solid {{ $index === 0 ? '#147440' : 'transparent' }}; cursor: pointer; flex-shrink: 0;"
             onclick="document.querySelector('#productCarousel').carousel({{ $index }})">
      @endforeach
    </div>
  @endif
</div>

<!-- Image Zoom Modal -->
<div id="imageZoomModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.95); z-index: 10000; overflow: hidden;">
  <div style="position: relative; width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;">
    <button onclick="closeImageZoom()" 
            style="position: absolute; top: 1rem; right: 1rem; background: rgba(255,255,255,0.2); border: none; color: white; width: 40px; height: 40px; border-radius: 50%; font-size: 1.5rem; cursor: pointer; z-index: 10001;">
      <i class="bx bx-x"></i>
    </button>
    <img id="zoomedImage" 
         src="" 
         alt="Product Image"
         style="max-width: 100%; max-height: 100%; object-fit: contain; user-select: none; -webkit-user-drag: none;">
    <div style="position: absolute; bottom: 1rem; left: 50%; transform: translateX(-50%); color: white; font-size: 0.875rem; text-align: center;">
      <i class="bx bx-info-circle"></i> Pinch untuk zoom atau geser untuk melihat gambar lain
    </div>
  </div>
</div>

<!-- Product Info -->
<div style="background: white; padding: 1rem; margin-top: 0.5rem;">
  <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.5rem;">
    <h4 style="font-size: 1.125rem; font-weight: 700; margin: 0; color: #333; flex: 1;">
      {{ $product->name }}
    </h4>
    <!-- Share Button -->
    <button type="button" 
            onclick="shareProduct()"
            style="background: #f0f0f0; border: none; padding: 0.5rem; border-radius: 8px; cursor: pointer; margin-left: 0.5rem;">
      <i class="bx bx-share-alt" style="font-size: 1.25rem; color: #147440;"></i>
    </button>
    <!-- Wishlist Button -->
    <button type="button" 
            id="wishlistBtn"
            onclick="toggleWishlist()"
            style="background: #f0f0f0; border: none; padding: 0.5rem; border-radius: 8px; cursor: pointer; margin-left: 0.5rem;">
      <i class="bx bx-heart" style="font-size: 1.25rem; color: #666;"></i>
    </button>
  </div>
  
  <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem; flex-wrap: wrap;">
    @if(isset($averageRating) && $averageRating > 0)
      <div style="display: flex; align-items: center; gap: 0.25rem;">
        @for($i = 1; $i <= 5; $i++)
          <i class="bx {{ $i <= round($averageRating) ? 'bxs-star' : 'bx-star' }}" 
             style="color: {{ $i <= round($averageRating) ? '#ffc107' : '#ddd' }}; font-size: 1rem;"></i>
        @endfor
        <span style="font-weight: 600; margin-left: 0.25rem;">{{ number_format($averageRating, 1) }}</span>
        @if(isset($totalReviews) && $totalReviews > 0)
          <span style="color: #666; font-size: 0.875rem;">({{ $totalReviews }} ulasan)</span>
        @endif
      </div>
    @else
      <div style="display: flex; align-items: center; gap: 0.25rem;">
        @for($i = 1; $i <= 5; $i++)
          <i class="bx bx-star" style="color: #ddd; font-size: 1rem;"></i>
        @endfor
        <span style="color: #666; font-size: 0.875rem; margin-left: 0.25rem;">Belum ada ulasan</span>
      </div>
    @endif
  </div>
  
  <div style="margin-bottom: 1rem;">
    <div style="font-size: 1.5rem; font-weight: 700; color: #147440;">
      Rp{{ number_format($product->price, 0, ',', '.') }}
    </div>
    @if($product->unit)
      <small style="color: #666;">/ {{ $product->unit }}</small>
    @endif
  </div>
  
  @if($product->stock_qty > 0)
    <div style="padding: 0.75rem; background: #e8f5e9; border-radius: 8px; margin-bottom: 1rem;">
      <div style="display: flex; align-items: center; gap: 0.5rem; color: #147440;">
        <i class="bx bx-check-circle"></i>
        <span style="font-weight: 600;">Stok tersedia ({{ number_format($product->stock_qty, 0, ',', '.') }} {{ $product->unit ?? 'pcs' }})</span>
      </div>
    </div>
  @else
    <div style="padding: 0.75rem; background: #ffebee; border-radius: 8px; margin-bottom: 1rem;">
      <div style="display: flex; align-items: center; gap: 0.5rem; color: #dc3545;">
        <i class="bx bx-x-circle"></i>
        <span style="font-weight: 600;">Stok habis</span>
      </div>
    </div>
  @endif
  
  @if($product->short_description)
    <div style="margin-bottom: 1rem;">
      <p style="color: #666; font-size: 0.875rem; line-height: 1.6;">
        {{ $product->short_description }}
      </p>
    </div>
  @endif
</div>

<!-- Product Description -->
@if($product->description)
<div style="background: white; padding: 1rem; margin-top: 0.5rem;">
  <h5 style="font-size: 1rem; font-weight: 700; margin-bottom: 0.75rem;">Deskripsi Produk</h5>
  <div style="color: #666; font-size: 0.875rem; line-height: 1.6;">
    {!! nl2br(e($product->description)) !!}
  </div>
</div>
@endif

<!-- Add to Cart Button -->
<div style="position: fixed; bottom: 70px; left: 0; right: 0; background: white; padding: 1rem; border-top: 1px solid #e0e0e0; z-index: 999; box-shadow: 0 -2px 10px rgba(0,0,0,0.1);">
  <form action="{{ route('cart.add') }}" method="POST" id="addToCartForm">
    @csrf
    <input type="hidden" name="id" value="{{ (string)$product->id }}">
    <input type="hidden" name="name" value="{{ $product->name }}">
    <input type="hidden" name="price" value="{{ $product->price }}">
    <input type="hidden" name="image" value="{{ $product->main_image_path ?? '' }}">
    <input type="hidden" name="qty" value="1" id="quantityInput">
    
    <div style="display: flex; gap: 0.75rem; align-items: center;">
      <div style="display: flex; align-items: center; gap: 0.5rem; border: 1px solid #e0e0e0; border-radius: 8px; padding: 0.5rem;">
        <button type="button" class="btn-qty" data-action="decrease" style="border: none; background: none; font-size: 1.25rem; color: #147440; padding: 0 0.5rem;">
          <i class="bx bx-minus"></i>
        </button>
        <span id="quantityDisplay" style="min-width: 30px; text-align: center; font-weight: 600;">1</span>
        <button type="button" class="btn-qty" data-action="increase" style="border: none; background: none; font-size: 1.25rem; color: #147440; padding: 0 0.5rem;">
          <i class="bx bx-plus"></i>
        </button>
      </div>
      
      <button type="submit" 
              class="btn-add-cart" 
              style="flex: 1; background: #147440; color: white; border: none; padding: 0.75rem; border-radius: 8px; font-weight: 600; font-size: 0.875rem;"
              {{ $product->stock_qty <= 0 ? 'disabled' : '' }}>
        <i class="bx bx-cart"></i> Tambah ke Keranjang
      </button>
    </div>
  </form>
</div>

<!-- Product Reviews Section -->
<div style="background: white; padding: 1rem; margin-top: 0.5rem;">
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
    <h5 style="font-size: 1rem; font-weight: 700; margin: 0; color: #333;">
      <i class="bx bx-star"></i> Ulasan & Rating
    </h5>
    @if(isset($totalReviews) && $totalReviews > 0)
      <span style="color: #666; font-size: 0.875rem;">{{ $totalReviews }} ulasan</span>
    @endif
  </div>
  
  @if(isset($reviews) && $reviews->count() > 0)
    <div style="max-height: 400px; overflow-y: auto; margin-bottom: 1rem;">
      @foreach($reviews as $review)
        <div style="padding: 1rem; border-bottom: 1px solid #f0f0f0; margin-bottom: 0.75rem;">
          <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.5rem;">
            <div style="flex: 1;">
              <div style="font-weight: 600; font-size: 0.875rem; margin-bottom: 0.25rem;">{{ $review->name }}</div>
              <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                @for($i = 1; $i <= 5; $i++)
                  <i class="bx {{ $i <= $review->rating ? 'bxs-star' : 'bx-star' }}" 
                     style="color: {{ $i <= $review->rating ? '#ffc107' : '#ddd' }}; font-size: 0.875rem;"></i>
                @endfor
                <span style="color: #666; font-size: 0.75rem; margin-left: 0.25rem;">
                  {{ \Carbon\Carbon::parse($review->created_at)->format('d M Y') }}
                </span>
              </div>
            </div>
          </div>
          <p style="color: #666; font-size: 0.875rem; line-height: 1.6; margin: 0;">{{ $review->content }}</p>
        </div>
      @endforeach
    </div>
  @else
    <div style="text-align: center; padding: 2rem; color: #999;">
      <i class="bx bx-star" style="font-size: 3rem; margin-bottom: 0.5rem; opacity: 0.3;"></i>
      <p style="font-size: 0.875rem; margin: 0;">Belum ada ulasan untuk produk ini.</p>
    </div>
  @endif
  
  <!-- Review Form -->
  @if(auth()->check())
    @if(isset($canReview) && $canReview)
      @if(isset($hasReviewed) && !$hasReviewed)
        <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 2px solid #f0f0f0;">
          <h6 style="font-size: 0.875rem; font-weight: 700; margin-bottom: 1rem; color: #333;">
            <i class="bx bx-edit"></i> Tulis Ulasan
          </h6>
          <form id="reviewForm" action="{{ route('mobile.shop.reviews.store', $product->slug) }}" method="POST">
            @csrf
            <div style="margin-bottom: 0.75rem;">
              <label style="font-size: 0.875rem; font-weight: 500; color: #666; margin-bottom: 0.5rem; display: block;">
                Rating <span style="color: #dc3545;">*</span>
              </label>
              <div style="display: flex; align-items: center; gap: 0.5rem;">
                <div id="ratingStars" style="display: flex; gap: 0.25rem;">
                  @for($i = 1; $i <= 5; $i++)
                    <label style="cursor: pointer; font-size: 1.5rem; color: #ddd;">
                      <input type="radio" name="rating" value="{{ $i }}" class="d-none" {{ (int)old('rating', 5) === $i ? 'checked' : '' }}>
                      <i class="bx bx-star rating-star" data-rating="{{ $i }}"></i>
                    </label>
                  @endfor
                </div>
                <span id="ratingText" style="font-size: 0.875rem; color: #666; margin-left: 0.5rem;">Pilih rating</span>
              </div>
            </div>
            
            <div style="margin-bottom: 0.75rem;">
              <label style="font-size: 0.875rem; font-weight: 500; color: #666; margin-bottom: 0.5rem; display: block;">
                Ulasan <span style="color: #dc3545;">*</span>
              </label>
              <textarea name="content" 
                        rows="4"
                        required
                        minlength="10"
                        placeholder="Tulis ulasan Anda tentang produk ini (minimal 10 karakter)..."
                        style="width: 100%; padding: 0.75rem; border: 1px solid #e0e0e0; border-radius: 8px; font-size: 0.875rem; resize: vertical;">{{ old('content') }}</textarea>
            </div>
            
            <button type="submit" 
                    style="width: 100%; background: #147440; color: white; border: none; padding: 0.75rem; border-radius: 8px; font-weight: 600; font-size: 0.875rem; cursor: pointer;">
              <i class="bx bx-send"></i> Kirim Ulasan
            </button>
          </form>
        </div>
      @elseif(isset($hasReviewed) && $hasReviewed)
        <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 2px solid #f0f0f0;">
          <div style="background: #d4edda; padding: 1rem; border-radius: 8px; text-align: center;">
            <i class="bx bx-check-circle" style="font-size: 2rem; color: #28a745; margin-bottom: 0.5rem;"></i>
            <p style="font-size: 0.875rem; color: #155724; margin: 0;">
              Anda sudah memberikan ulasan untuk produk ini.
            </p>
          </div>
        </div>
      @endif
    @else
      <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 2px solid #f0f0f0;">
        <div style="background: #fff3cd; padding: 1rem; border-radius: 8px; text-align: center;">
          <i class="bx bx-info-circle" style="font-size: 2rem; color: #856404; margin-bottom: 0.5rem;"></i>
          <p style="font-size: 0.875rem; color: #856404; margin: 0;">
            Anda hanya dapat memberikan ulasan setelah pesanan Anda diterima.
          </p>
        </div>
      </div>
    @endif
  @else
    <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 2px solid #f0f0f0;">
      <div style="background: #e7f3ff; padding: 1rem; border-radius: 8px; text-align: center;">
        <i class="bx bx-log-in" style="font-size: 2rem; color: #004085; margin-bottom: 0.5rem;"></i>
        <p style="font-size: 0.875rem; color: #004085; margin: 0 0 0.75rem 0;">
          Silakan login untuk memberikan ulasan.
        </p>
        <a href="{{ route('mobile.login', ['redirect' => route('mobile.shop.detail', $product->slug)]) }}" 
           style="display: inline-block; background: #147440; color: white; padding: 0.5rem 1.5rem; border-radius: 8px; text-decoration: none; font-size: 0.875rem; font-weight: 600;">
          Login
        </a>
      </div>
    </div>
  @endif
</div>

<!-- Recommended Products (Frequently Bought Together) -->
@if(isset($recommendedProducts) && $recommendedProducts->count() > 0)
<div style="background: white; padding: 1rem; margin-top: 0.5rem; margin-bottom: 0.5rem;">
  <h5 style="font-size: 1rem; font-weight: 700; margin-bottom: 1rem; color: #333;">
    <i class="bx bx-star"></i> Sering Dibeli Bersama
  </h5>
  <div class="scroll-container" style="display: flex; gap: 0.75rem; overflow-x: auto; -webkit-overflow-scrolling: touch; padding-bottom: 0.5rem;">
    @foreach($recommendedProducts as $recProduct)
      <a href="{{ route('mobile.shop.detail', $recProduct->slug) }}" 
         style="text-decoration: none; color: inherit; min-width: 140px; flex-shrink: 0;">
        <div style="background: #f5f5f5; border-radius: 8px; overflow: hidden;">
          <img src="{{ $recProduct->main_image_path ? Storage::url($recProduct->main_image_path) : asset('sneat/assets/img/placeholder.png') }}" 
               alt="{{ $recProduct->name }}" 
               style="width: 100%; height: 120px; object-fit: cover;"
               onerror="this.src='{{ asset('sneat/assets/img/placeholder.png') }}'">
          <div style="padding: 0.75rem;">
            <div style="font-size: 0.75rem; font-weight: 600; color: #333; margin-bottom: 0.25rem; line-height: 1.3; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
              {{ $recProduct->name }}
            </div>
            <div style="font-size: 0.875rem; font-weight: 700; color: #147440;">
              Rp{{ number_format($recProduct->price, 0, ',', '.') }}
            </div>
            @php
              $reviews = DB::table('product_reviews')->where('product_id', $recProduct->id)->get();
              $avgRating = $reviews->count() > 0 ? round($reviews->avg('rating'), 1) : 0;
            @endphp
            @if($avgRating > 0)
              <div style="display: flex; align-items: center; gap: 0.25rem; margin-top: 0.25rem;">
                @for($i = 1; $i <= 5; $i++)
                  <i class="bx {{ $i <= round($avgRating) ? 'bxs-star' : 'bx-star' }}" 
                     style="color: {{ $i <= round($avgRating) ? '#ffc107' : '#ddd' }}; font-size: 0.7rem;"></i>
                @endfor
                <span style="font-size: 0.7rem; color: #666; margin-left: 0.25rem;">{{ number_format($avgRating, 1) }}</span>
              </div>
            @endif
          </div>
        </div>
      </a>
    @endforeach
  </div>
</div>
@endif

<!-- Related Products -->
@if(isset($related) && $related->count() > 0)
<div style="background: white; padding: 1rem; margin-top: 0.5rem; margin-bottom: 100px;">
  <h5 style="font-size: 1rem; font-weight: 700; margin-bottom: 0.75rem;">Produk Terkait</h5>
  <div class="scroll-container">
    @foreach($related as $relatedProduct)
      <a href="{{ route('mobile.shop.detail', $relatedProduct->slug) }}" style="text-decoration: none; color: inherit; min-width: 140px;">
        <div style="background: #f5f5f5; border-radius: 8px; overflow: hidden;">
          <img src="{{ $relatedProduct->main_image_path ? Storage::url($relatedProduct->main_image_path) : asset('sneat/assets/img/placeholder.png') }}" 
               alt="{{ $relatedProduct->name }}" 
               style="width: 100%; height: 120px; object-fit: cover;"
               onerror="this.src='{{ asset('sneat/assets/img/placeholder.png') }}'">
          <div style="padding: 0.5rem;">
            <div style="font-size: 0.75rem; font-weight: 500; margin-bottom: 0.25rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
              {{ $relatedProduct->name }}
            </div>
            <div style="font-size: 0.875rem; font-weight: 700; color: #147440;">
              Rp{{ number_format($relatedProduct->price, 0, ',', '.') }}
            </div>
          </div>
        </div>
      </a>
    @endforeach
  </div>
</div>
@endif
@endsection

@push('scripts')
<script>
  // Image Zoom Functionality
  document.querySelectorAll('.product-image-zoom').forEach(img => {
    img.addEventListener('click', function() {
      const modal = document.getElementById('imageZoomModal');
      const zoomedImg = document.getElementById('zoomedImage');
      zoomedImg.src = this.dataset.image || this.src;
      modal.style.display = 'flex';
      document.body.style.overflow = 'hidden';
      
      // Initialize pinch zoom
      let scale = 1;
      let lastTouchDistance = 0;
      
      zoomedImg.addEventListener('touchstart', function(e) {
        if (e.touches.length === 2) {
          lastTouchDistance = Math.hypot(
            e.touches[0].clientX - e.touches[1].clientX,
            e.touches[0].clientY - e.touches[1].clientY
          );
        }
      });
      
      zoomedImg.addEventListener('touchmove', function(e) {
        if (e.touches.length === 2) {
          e.preventDefault();
          const distance = Math.hypot(
            e.touches[0].clientX - e.touches[1].clientX,
            e.touches[0].clientY - e.touches[1].clientY
          );
          const delta = distance - lastTouchDistance;
          scale = Math.max(1, Math.min(3, scale + delta * 0.01));
          this.style.transform = `scale(${scale})`;
          lastTouchDistance = distance;
        }
      });
      
      zoomedImg.addEventListener('touchend', function() {
        lastTouchDistance = 0;
      });
    });
  });
  
  function closeImageZoom() {
    const modal = document.getElementById('imageZoomModal');
    const zoomedImg = document.getElementById('zoomedImage');
    modal.style.display = 'none';
    document.body.style.overflow = '';
    zoomedImg.style.transform = 'scale(1)';
  }
  
  // Close modal on outside click
  document.getElementById('imageZoomModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
      closeImageZoom();
    }
  });
  
  // Share Product Function
  function shareProduct() {
    const productName = @json($product->name);
    const productUrl = window.location.href;
    const shareText = `Lihat produk ${productName} di Samsae Store: ${productUrl}`;
    
    if (navigator.share) {
      navigator.share({
        title: productName,
        text: shareText,
        url: productUrl
      }).catch(err => {
        console.log('Error sharing:', err);
        fallbackShare(shareText);
      });
    } else {
      fallbackShare(shareText);
    }
  }
  
  function fallbackShare(text) {
    // Copy to clipboard
    if (navigator.clipboard) {
      navigator.clipboard.writeText(text).then(() => {
        MobileNotification.success('Link produk berhasil disalin!');
      });
    } else {
      // Fallback: show share options
      Swal.fire({
        title: 'Bagikan Produk',
        html: `
          <div style="margin-bottom: 1rem;">
            <a href="https://wa.me/?text=${encodeURIComponent(text)}" 
               target="_blank"
               style="display: block; padding: 0.75rem; background: #25D366; color: white; border-radius: 8px; text-decoration: none; margin-bottom: 0.5rem; text-align: center;">
              <i class="bx bxl-whatsapp"></i> WhatsApp
            </a>
            <button onclick="copyProductLink()" 
                    style="width: 100%; padding: 0.75rem; background: #147440; color: white; border: none; border-radius: 8px; cursor: pointer;">
              <i class="bx bx-copy"></i> Salin Link
            </button>
          </div>
        `,
        showConfirmButton: false,
        showCancelButton: true,
        cancelButtonText: 'Tutup',
        width: '90%',
        customClass: {
          popup: 'mobile-swal-popup'
        }
      });
    }
  }
  
  function copyProductLink() {
    const url = window.location.href;
    if (navigator.clipboard) {
      navigator.clipboard.writeText(url).then(() => {
        MobileNotification.success('Link berhasil disalin!');
        Swal.close();
      });
    }
  }
  
  // Wishlist Functionality
  let isWishlisted = false;
  
  // Check wishlist status on load
  @auth
    fetch('/m/wishlist/check/{{ $product->id }}', {
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
    .then(response => response.json())
    .then(data => {
      isWishlisted = data.is_wishlisted;
      const btn = document.getElementById('wishlistBtn');
      if (btn) {
        const icon = btn.querySelector('i');
        if (icon && isWishlisted) {
          icon.className = 'bx bxs-heart';
          icon.style.color = '#dc3545';
        }
      }
    })
    .catch(() => {}); // Ignore errors
  @endauth
  
  function toggleWishlist() {
    @guest
      window.location.href = '{{ route("mobile.login") }}';
      return;
    @endguest
    
    const btn = document.getElementById('wishlistBtn');
    const icon = btn.querySelector('i');
    
    MobileLoading.show('Memproses...');
    
    fetch('{{ route("mobile.wishlist.toggle") }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: JSON.stringify({ product_id: {{ $product->id }} })
    })
    .then(response => response.json())
    .then(data => {
      MobileLoading.hide();
      if (data.success) {
        isWishlisted = data.is_wishlisted;
        if (isWishlisted) {
          icon.className = 'bx bxs-heart';
          icon.style.color = '#dc3545';
        } else {
          icon.className = 'bx bx-heart';
          icon.style.color = '#666';
        }
        MobileNotification.success(data.message);
        updateWishlistBadge(data.count);
      } else {
        MobileNotification.error(data.message || 'Gagal mengupdate wishlist');
      }
    })
    .catch(error => {
      MobileLoading.hide();
      MobileErrorHandler.handle(error);
    });
  }
  
  function updateWishlistBadge(count) {
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
  
  // Rating Stars Interactive
  document.querySelectorAll('.rating-star').forEach(star => {
    star.addEventListener('click', function() {
      const rating = parseInt(this.dataset.rating);
      const stars = document.querySelectorAll('.rating-star');
      const ratingText = document.getElementById('ratingText');
      
      stars.forEach((s, index) => {
        if (index < rating) {
          s.className = 'bx bxs-star';
          s.style.color = '#ffc107';
        } else {
          s.className = 'bx bx-star';
          s.style.color = '#ddd';
        }
      });
      
      // Update radio button
      document.querySelector(`input[name="rating"][value="${rating}"]`).checked = true;
      
      // Update text
      const texts = ['', 'Sangat Buruk', 'Buruk', 'Cukup', 'Baik', 'Sangat Baik'];
      ratingText.textContent = texts[rating] || 'Pilih rating';
    });
    
    star.addEventListener('mouseenter', function() {
      const rating = parseInt(this.dataset.rating);
      const stars = document.querySelectorAll('.rating-star');
      
      stars.forEach((s, index) => {
        if (index < rating) {
          s.style.color = '#ffc107';
        }
      });
    });
  });
  
  // Initialize rating display
  const selectedRating = document.querySelector('input[name="rating"]:checked');
  if (selectedRating) {
    const rating = parseInt(selectedRating.value);
    const stars = document.querySelectorAll('.rating-star');
    stars.forEach((s, index) => {
      if (index < rating) {
        s.className = 'bx bxs-star';
        s.style.color = '#ffc107';
      }
    });
    const texts = ['', 'Sangat Buruk', 'Buruk', 'Cukup', 'Baik', 'Sangat Baik'];
    document.getElementById('ratingText').textContent = texts[rating] || 'Pilih rating';
  }
  
  // Save to Recently Viewed Products
  (function() {
    const productId = {{ $product->id }};
    const productName = @json($product->name);
    const productSlug = @json($product->slug);
    const productImage = @json($product->main_image_path ? Storage::url($product->main_image_path) : asset('sneat/assets/img/placeholder.png'));
    const productPrice = {{ $product->price }};
    
    // Save to localStorage
    let recentlyViewed = JSON.parse(localStorage.getItem('recentlyViewed') || '[]');
    // Remove if exists
    recentlyViewed = recentlyViewed.filter(item => item.id !== productId);
    // Add to beginning
    recentlyViewed.unshift({
      id: productId,
      name: productName,
      slug: productSlug,
      image: productImage,
      price: productPrice
    });
    // Keep only last 20
    recentlyViewed = recentlyViewed.slice(0, 20);
    localStorage.setItem('recentlyViewed', JSON.stringify(recentlyViewed));
    
    // Save to backend if logged in
    @auth
      fetch('{{ route("api.recently-viewed.save") }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ product_id: productId })
      }).catch(() => {}); // Ignore errors
    @endauth
  })();
  
  // Review Form Submission
  document.getElementById('reviewForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn.innerHTML;
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Mengirim...';
    MobileLoading.show('Mengirim ulasan...');
    
    fetch(this.action, {
      method: 'POST',
      body: formData,
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'Accept': 'application/json'
      }
    })
    .then(response => response.json())
    .then(data => {
      MobileLoading.hide();
      if (data.success) {
        MobileNotification.success(data.message || 'Ulasan berhasil dikirim!');
        setTimeout(() => {
          window.location.reload();
        }, 1500);
      } else {
        MobileNotification.error(data.message || 'Gagal mengirim ulasan');
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalBtnText;
      }
    })
    .catch(error => {
      MobileLoading.hide();
      MobileErrorHandler.handle(error, 'Submit Review');
      submitBtn.disabled = false;
      submitBtn.innerHTML = originalBtnText;
    });
  });
  
  // Quantity controls
  const maxStock = {{ $product->stock_qty ?? 0 }};
  const increaseBtn = document.querySelector('.btn-qty[data-action="increase"]');
  
  // Set initial state of increase button
  if (increaseBtn && maxStock <= 0) {
    increaseBtn.style.opacity = '0.5';
    increaseBtn.style.cursor = 'not-allowed';
    increaseBtn.disabled = true;
  }
  
  document.querySelectorAll('.btn-qty').forEach(btn => {
    btn.addEventListener('click', function() {
      if (this.disabled) return;
      
      const action = this.dataset.action;
      const quantityInput = document.getElementById('quantityInput');
      const quantityDisplay = document.getElementById('quantityDisplay');
      let quantity = parseInt(quantityInput.value);
      
      const maxStock = {{ $product->stock_qty ?? 0 }};
      
      if (action === 'increase') {
        if (quantity < maxStock) {
          quantity++;
        } else {
          // Tampilkan popup dengan SweetAlert untuk informasi stok
          Swal.fire({
            icon: 'warning',
            title: 'Stok Tidak Mencukupi',
            html: `Stok tersedia: <strong>${maxStock}</strong><br>Anda sudah memilih: <strong>${quantity}</strong>`,
            confirmButtonColor: '#147440',
            confirmButtonText: 'Mengerti',
            width: '90%',
            customClass: {
              popup: 'mobile-swal-popup'
            }
          });
          return;
        }
      } else if (action === 'decrease' && quantity > 1) {
        quantity--;
      }
      
      quantityInput.value = quantity;
      quantityDisplay.textContent = quantity;
      
      // Update hidden qty field in form
      document.querySelector('input[name="qty"]').value = quantity;
      
      // Disable increase button if quantity reaches max stock
      const increaseBtn = document.querySelector('.btn-qty[data-action="increase"]');
      if (increaseBtn) {
        if (quantity >= maxStock) {
          increaseBtn.style.opacity = '0.5';
          increaseBtn.style.cursor = 'not-allowed';
        } else {
          increaseBtn.style.opacity = '1';
          increaseBtn.style.cursor = 'pointer';
        }
      }
    });
  });
  
  // Add to cart form
  document.getElementById('addToCartForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn.innerHTML;
    
    // Disable button and show loading
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Menambahkan...';
    MobileLoading.show('Menambahkan ke keranjang...');
    
    fetch(this.action, {
      method: 'POST',
      body: formData,
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'Accept': 'application/json'
      }
    })
    .then(async response => {
      // Always try to parse as JSON first
      let data;
      const text = await response.text();
      
      try {
        if (text) {
          data = JSON.parse(text);
        } else {
          data = { success: false, message: 'Response kosong' };
        }
      } catch (e) {
        // If not JSON, check status
        if (response.ok) {
          data = { success: true, message: 'Produk berhasil ditambahkan ke keranjang!' };
        } else {
          data = { success: false, message: 'Terjadi kesalahan saat menambahkan produk' };
        }
      }
      
      if (!response.ok) {
        throw { response: { status: response.status, data: data } };
      }
      
      return data;
    })
    .then(data => {
      if (data.success) {
        MobileNotification.success(data.message || 'Produk berhasil ditambahkan ke keranjang!');
        
        // Update cart badge if exists
        const cartBadge = document.querySelector('.cart-badge');
        if (cartBadge) {
          const currentCount = parseInt(cartBadge.textContent) || 0;
          const qty = parseInt(formData.get('qty')) || 1;
          cartBadge.textContent = currentCount + qty;
        }
        
        // Redirect to cart page after 1 second
        setTimeout(() => {
          window.location.href = '{{ route('mobile.cart') }}';
        }, 1000);
      } else {
        // Tampilkan error dengan detail stok jika ada
        let errorMsg = data.message || data.error;
        
        if (data.stock_info) {
          const stock = data.stock_info;
          errorMsg = `Stok tidak mencukupi!\n\n` +
                    `Stok tersedia: ${stock.available}\n` +
                    (stock.current_in_cart > 0 ? `Sudah ada di keranjang: ${stock.current_in_cart}\n` : '') +
                    `Yang diminta: ${stock.requested}\n` +
                    (stock.max_can_add > 0 ? `Maksimal yang bisa ditambahkan: ${stock.max_can_add}` : 'Tidak bisa menambahkan lagi');
        }
        
        if (data.errors && typeof data.errors === 'object') {
          const errorMessages = [];
          Object.values(data.errors).forEach(err => {
            if (Array.isArray(err)) {
              errorMessages.push(...err);
            } else {
              errorMessages.push(err);
            }
          });
          errorMsg = errorMessages.join('\n') || errorMsg;
        }
        
        // Tampilkan notifikasi error dengan SweetAlert untuk popup yang lebih jelas
        Swal.fire({
          icon: 'warning',
          title: 'Stok Tidak Mencukupi',
          html: errorMsg.replace(/\n/g, '<br>'),
          confirmButtonColor: '#147440',
          confirmButtonText: 'Mengerti',
          width: '90%',
          customClass: {
            popup: 'mobile-swal-popup'
          }
        });
      }
    })
    .catch(error => {
      let errorMsg = 'Terjadi kesalahan saat menambahkan produk ke keranjang';
      let errorTitle = 'Gagal Menambahkan';
      
      if (error.response && error.response.data) {
        const errorData = error.response.data;
        errorMsg = errorData.message || errorData.error || errorMsg;
        errorTitle = 'Stok Tidak Mencukupi';
        
        // Jika ada stock_info, format pesan dengan detail
        if (errorData.stock_info) {
          const stock = errorData.stock_info;
          errorMsg = `Stok tidak mencukupi!\n\n` +
                    `Stok tersedia: ${stock.available}\n` +
                    (stock.current_in_cart > 0 ? `Sudah ada di keranjang: ${stock.current_in_cart}\n` : '') +
                    `Yang diminta: ${stock.requested}\n` +
                    (stock.max_can_add > 0 ? `Maksimal yang bisa ditambahkan: ${stock.max_can_add}` : 'Tidak bisa menambahkan lagi');
        }
        
        // Jika ada errors object, gabungkan semua error messages
        if (errorData.errors && typeof errorData.errors === 'object') {
          const errorMessages = [];
          Object.values(errorData.errors).forEach(err => {
            if (Array.isArray(err)) {
              errorMessages.push(...err);
            } else {
              errorMessages.push(err);
            }
          });
          errorMsg = errorMessages.join('\n') || errorMsg;
        }
      }
      
      // Tampilkan popup dengan SweetAlert untuk error yang lebih jelas
      Swal.fire({
        icon: 'warning',
        title: errorTitle,
        html: errorMsg.replace(/\n/g, '<br>'),
        confirmButtonColor: '#147440',
        confirmButtonText: 'Mengerti',
        width: '90%',
        customClass: {
          popup: 'mobile-swal-popup'
        }
      });
    })
    .finally(() => {
      MobileLoading.hide();
      submitBtn.disabled = false;
      submitBtn.innerHTML = originalBtnText;
    });
  });
</script>
@endpush
