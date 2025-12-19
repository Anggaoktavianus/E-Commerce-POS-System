@extends('mobile.layouts.app')

@php
use Illuminate\Support\Facades\Storage;
@endphp

@section('title', 'Shop')

@section('content')
<!-- Search Bar with Autocomplete -->
<div style="background: white; padding: 0.75rem 1rem; margin-bottom: 0.5rem;">
  <form action="{{ route('mobile.shop') }}" method="GET" id="searchForm" style="position: relative;">
    <input type="text" 
           name="search" 
           id="searchInput"
           placeholder="Cari produk..." 
           value="{{ request('search') }}"
           autocomplete="off"
           style="width: 100%; padding: 0.75rem 2.5rem 0.75rem 1rem; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 0.875rem; background: white;">
    <i class="bx bx-search" style="position: absolute; right: 1.5rem; top: 50%; transform: translateY(-50%); color: #999; pointer-events: none;"></i>
    
    <!-- Search Suggestions Dropdown -->
    <div id="searchSuggestions" style="display: none; position: absolute; top: 100%; left: 0; right: 0; background: white; border: 1px solid #e0e0e0; border-radius: 8px; margin-top: 0.25rem; max-height: 300px; overflow-y: auto; z-index: 1000; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
      <!-- Search History -->
      <div id="searchHistory" style="padding: 0.5rem;">
        <div style="font-size: 0.75rem; font-weight: 600; color: #666; margin-bottom: 0.5rem; padding: 0 0.5rem;">
          <i class="bx bx-time"></i> Riwayat Pencarian
        </div>
        <div id="searchHistoryList"></div>
      </div>
      
      <!-- Popular Searches -->
      <div id="popularSearches" style="padding: 0.5rem; border-top: 1px solid #f0f0f0;">
        <div style="font-size: 0.75rem; font-weight: 600; color: #666; margin-bottom: 0.5rem; padding: 0 0.5rem;">
          <i class="bx bx-trending-up"></i> Pencarian Populer
        </div>
        <div id="popularSearchesList"></div>
      </div>
    </div>
  </form>
</div>

<!-- Filter & Sort Bar -->
<div style="background: white; padding: 0.75rem 1rem; margin-bottom: 0.5rem; display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap;">
  <select id="sortSelect" 
          onchange="applySort()"
          style="flex: 1; min-width: 120px; padding: 0.625rem; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 0.875rem; background: white;">
    <option value="latest" {{ request('sort') == 'latest' || !request('sort') ? 'selected' : '' }}>Terbaru</option>
    <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Harga: Rendah-Tinggi</option>
    <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Harga: Tinggi-Rendah</option>
    <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>Rating Tertinggi</option>
    <option value="sold" {{ request('sort') == 'sold' ? 'selected' : '' }}>Terlaris</option>
    <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Nama: A-Z</option>
  </select>
  
  <button type="button" 
          onclick="toggleFilter()"
          style="background: #f0f0f0; color: #333; border: 2px solid #e0e0e0; padding: 0.625rem 1rem; border-radius: 8px; font-size: 0.875rem; font-weight: 500; cursor: pointer; white-space: nowrap;">
    <i class="bx bx-filter"></i> Filter
  </button>
</div>

<!-- Filter Panel (Hidden by default) -->
<div id="filterPanel" style="background: white; padding: 1rem; margin-bottom: 0.5rem; border-radius: 12px; display: none;">
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
    <h6 style="font-size: 0.875rem; font-weight: 700; color: #333; margin: 0;">Filter Produk</h6>
    <button type="button" 
            onclick="toggleFilter()"
            style="background: none; border: none; color: #666; font-size: 1.25rem; cursor: pointer;">
      <i class="bx bx-x"></i>
    </button>
  </div>
  
  <div class="form-group" style="margin-bottom: 1rem;">
    <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #333; margin-bottom: 0.5rem;">Kategori</label>
    <select id="categoryFilter" 
            onchange="applyFilters()"
            style="width: 100%; padding: 0.75rem; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 0.875rem; background: white;">
      <option value="">Semua Kategori</option>
      @if(isset($categories))
        @foreach($categories as $category)
          <option value="{{ $category->slug }}" {{ request('category') == $category->slug ? 'selected' : '' }}>
            {{ $category->name }}
          </option>
        @endforeach
      @endif
    </select>
  </div>
  
  <!-- Price Range Filter -->
  @if(isset($priceRange) && $priceRange->min_price && $priceRange->max_price)
  <div class="form-group" style="margin-bottom: 1rem;">
    <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #333; margin-bottom: 0.5rem;">Rentang Harga</label>
    <div style="display: flex; gap: 0.5rem; align-items: center; margin-bottom: 0.5rem;">
      <input type="number" 
             id="minPrice" 
             min="{{ floor($priceRange->min_price) }}" 
             max="{{ ceil($priceRange->max_price) }}"
             value="{{ request('min_price', floor($priceRange->min_price)) }}"
             placeholder="Min"
             style="flex: 1; padding: 0.625rem; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 0.875rem;">
      <span style="color: #666;">-</span>
      <input type="number" 
             id="maxPrice" 
             min="{{ floor($priceRange->min_price) }}" 
             max="{{ ceil($priceRange->max_price) }}"
             value="{{ request('max_price', ceil($priceRange->max_price)) }}"
             placeholder="Max"
             style="flex: 1; padding: 0.625rem; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 0.875rem;">
    </div>
    <div style="font-size: 0.75rem; color: #999;">
      Rp{{ number_format($priceRange->min_price, 0, ',', '.') }} - Rp{{ number_format($priceRange->max_price, 0, ',', '.') }}
    </div>
  </div>
  @endif
  
  <!-- Rating Filter -->
  <div class="form-group" style="margin-bottom: 1rem;">
    <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #333; margin-bottom: 0.5rem;">Rating Minimum</label>
    <select id="ratingFilter" 
            style="width: 100%; padding: 0.75rem; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 0.875rem; background: white;">
      <option value="">Semua Rating</option>
      <option value="4" {{ request('min_rating') == '4' ? 'selected' : '' }}>4+ Bintang</option>
      <option value="3" {{ request('min_rating') == '3' ? 'selected' : '' }}>3+ Bintang</option>
      <option value="2" {{ request('min_rating') == '2' ? 'selected' : '' }}>2+ Bintang</option>
      <option value="1" {{ request('min_rating') == '1' ? 'selected' : '' }}>1+ Bintang</option>
    </select>
  </div>
  
  <div class="form-group" style="margin-bottom: 1rem;">
    <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #333; margin-bottom: 0.5rem;">Stok</label>
    <select id="stockFilter" 
            style="width: 100%; padding: 0.75rem; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 0.875rem; background: white;">
      <option value="">Semua</option>
      <option value="available" {{ request('stock') == 'available' ? 'selected' : '' }}>Tersedia</option>
      <option value="out_of_stock" {{ request('stock') == 'out_of_stock' ? 'selected' : '' }}>Habis</option>
    </select>
  </div>
  
  <div style="display: flex; gap: 0.5rem; margin-top: 1rem;">
    <button type="button" 
            onclick="clearFilters()"
            style="flex: 1; background: #f0f0f0; color: #333; border: none; padding: 0.75rem; border-radius: 8px; font-weight: 600; font-size: 0.875rem; cursor: pointer;">
      <i class="bx bx-refresh"></i> Reset
    </button>
    <button type="button" 
            onclick="applyFilters()"
            style="flex: 1; background: #147440; color: white; border: none; padding: 0.75rem; border-radius: 8px; font-weight: 600; font-size: 0.875rem; cursor: pointer;">
      <i class="bx bx-check"></i> Terapkan
    </button>
  </div>
</div>

<!-- Category Tabs -->
<div class="category-tabs">
  <a href="{{ route('mobile.shop') }}" class="category-tab {{ !request('category') ? 'active' : '' }}">Semua</a>
  @if(isset($categories))
    @foreach($categories as $category)
      <a href="{{ route('mobile.shop', ['category' => $category->slug]) }}" 
         class="category-tab {{ request('category') == $category->slug ? 'active' : '' }}">
        {{ $category->name }}
      </a>
    @endforeach
  @endif
</div>

<!-- Products Grid -->
@if($products->count() > 0)
<div class="product-grid">
  @foreach($products as $product)
    <div class="product-card" style="position: relative;">
      <div style="position: relative;">
        <a href="{{ route('mobile.shop.detail', $product->slug) }}" style="text-decoration: none; color: inherit; display: block;">
          <img src="{{ $product->main_image_path ? Storage::url($product->main_image_path) : asset('sneat/assets/img/placeholder.png') }}" 
               alt="{{ $product->name }}" 
               class="product-image"
               onerror="this.src='{{ asset('sneat/assets/img/placeholder.png') }}'">
        </a>
        @if($product->is_featured)
          <div class="product-badge">NEW</div>
        @endif
        @if($product->is_bestseller)
          <div class="product-badge" style="background: #ff6b35;">HOT</div>
        @endif
        <div style="position: absolute; top: 0.5rem; right: 0.5rem; display: flex; gap: 0.25rem; z-index: 10;">
          <button type="button" 
                  onclick="event.preventDefault(); showQuickView({{ $product->id }}, '{{ $product->slug }}')"
                  style="background: rgba(255,255,255,0.9); border: none; width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <i class="bx bx-search" style="font-size: 0.875rem; color: #147440;"></i>
          </button>
          <button type="button" 
                  onclick="event.preventDefault(); addToComparison({{ $product->id }}, this)"
                  style="background: rgba(255,255,255,0.9); border: none; width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <i class="bx bx-layer" style="font-size: 0.875rem; color: #147440;"></i>
          </button>
          @auth
            <button type="button" 
                    onclick="event.preventDefault(); toggleWishlistQuick({{ $product->id }}, this)"
                    class="wishlist-btn-{{ $product->id }}"
                    style="background: rgba(255,255,255,0.9); border: none; width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
              <i class="bx bx-heart" style="font-size: 0.875rem; color: #666;"></i>
            </button>
          @endauth
        </div>
      </div>
      <a href="{{ route('mobile.shop.detail', $product->slug) }}" style="text-decoration: none; color: inherit;">
        <div class="product-info">
          <div class="product-name">{{ $product->name }}</div>
          <div class="product-price">
            <span class="price-current">Rp{{ number_format($product->price, 0, ',', '.') }}</span>
            @if($product->unit)
              <small style="font-size: 0.7rem; color: #999;">/ {{ $product->unit }}</small>
            @endif
          </div>
        <div class="product-rating" style="margin-top: 0.5rem;">
          @if(isset($product->average_rating) && $product->average_rating > 0)
            <div style="display: flex; align-items: center; gap: 0.25rem; margin-bottom: 0.25rem;">
              @for($i = 1; $i <= 5; $i++)
                <i class="bx {{ $i <= round($product->average_rating) ? 'bxs-star' : 'bx-star' }}" 
                   style="color: {{ $i <= round($product->average_rating) ? '#ffc107' : '#ddd' }}; font-size: 0.75rem;"></i>
              @endfor
              <span style="font-size: 0.7rem; color: #666; margin-left: 0.25rem;">{{ number_format($product->average_rating, 1) }}</span>
              @if(isset($product->total_reviews) && $product->total_reviews > 0)
                <span style="font-size: 0.7rem; color: #999;">({{ $product->total_reviews }})</span>
              @endif
            </div>
          @else
            <div style="display: flex; align-items: center; gap: 0.25rem; margin-bottom: 0.25rem;">
              @for($i = 1; $i <= 5; $i++)
                <i class="bx bx-star" style="color: #ddd; font-size: 0.75rem;"></i>
              @endfor
              <span style="font-size: 0.7rem; color: #999; margin-left: 0.25rem;">Baru</span>
            </div>
          @endif
          @if(isset($product->total_sold) && $product->total_sold > 0)
            <div style="font-size: 0.7rem; color: #666;">
              @if($product->total_sold >= 1000)
                <i class="bx bx-package"></i> {{ number_format($product->total_sold / 1000, 1) }}rb+ terjual
              @else
                <i class="bx bx-package"></i> {{ $product->total_sold }}+ terjual
              @endif
            </div>
          @else
            <div style="font-size: 0.7rem; color: #999;">
              <i class="bx bx-package"></i> Belum terjual
            </div>
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
    </div>
  @endforeach
</div>

<!-- Pagination -->
@if($products->hasPages())
<div style="padding: 1rem; text-align: center; background: white;">
  {{ $products->links() }}
</div>
@endif

@else
<div class="empty-state">
  <i class="bx bx-package"></i>
  <p>Tidak ada produk ditemukan</p>
  <a href="{{ route('mobile.shop') }}" class="btn btn-primary mt-3" style="background: #147440; border: none;">
    Lihat Semua Produk
  </a>
</div>
@endif
@endsection

@push('scripts')
<script>
  function toggleFilter() {
    const panel = document.getElementById('filterPanel');
    panel.style.display = panel.style.display === 'none' ? 'block' : 'none';
  }
  
  function applySort() {
    const sort = document.getElementById('sortSelect').value;
    const url = new URL(window.location.href);
    url.searchParams.set('sort', sort);
    
    MobileLoading.show('Menyortir produk...');
    window.location.href = url.toString();
  }
  
  function applyFilters() {
    const category = document.getElementById('categoryFilter').value;
    const stock = document.getElementById('stockFilter').value;
    const sort = document.getElementById('sortSelect').value;
    const minPrice = document.getElementById('minPrice')?.value;
    const maxPrice = document.getElementById('maxPrice')?.value;
    const minRating = document.getElementById('ratingFilter')?.value;
    
    const url = new URL(window.location.href);
    
    if (category) {
      url.searchParams.set('category', category);
    } else {
      url.searchParams.delete('category');
    }
    
    if (stock) {
      url.searchParams.set('stock', stock);
    } else {
      url.searchParams.delete('stock');
    }
    
    if (sort) {
      url.searchParams.set('sort', sort);
    } else {
      url.searchParams.delete('sort');
    }
    
    if (minPrice) {
      url.searchParams.set('min_price', minPrice);
    } else {
      url.searchParams.delete('min_price');
    }
    
    if (maxPrice) {
      url.searchParams.set('max_price', maxPrice);
    } else {
      url.searchParams.delete('max_price');
    }
    
    if (minRating) {
      url.searchParams.set('min_rating', minRating);
    } else {
      url.searchParams.delete('min_rating');
    }
    
    MobileLoading.show('Menerapkan filter...');
    window.location.href = url.toString();
  }
  
  function clearFilters() {
    MobileLoading.show('Mereset filter...');
    window.location.href = '{{ route('mobile.shop') }}';
  }
  
  // Search History & Autocomplete
  const searchInput = document.getElementById('searchInput');
  const searchSuggestions = document.getElementById('searchSuggestions');
  const searchHistoryList = document.getElementById('searchHistoryList');
  const popularSearchesList = document.getElementById('popularSearchesList');
  
  // Load search history from localStorage
  function loadSearchHistory() {
    const history = JSON.parse(localStorage.getItem('searchHistory') || '[]');
    if (history.length > 0) {
      searchHistoryList.innerHTML = history.slice(0, 5).map(item => `
        <div onclick="selectSearchHistory('${item.replace(/'/g, "\\'")}')" 
             style="padding: 0.5rem; cursor: pointer; border-radius: 6px; margin-bottom: 0.25rem; display: flex; align-items: center; gap: 0.5rem;"
             onmouseover="this.style.background='#f0f0f0'" 
             onmouseout="this.style.background='transparent'">
          <i class="bx bx-time" style="color: #999;"></i>
          <span style="font-size: 0.875rem; color: #333;">${item}</span>
        </div>
      `).join('');
    } else {
      searchHistoryList.innerHTML = '<div style="padding: 0.5rem; font-size: 0.75rem; color: #999;">Belum ada riwayat pencarian</div>';
    }
  }
  
  // Load popular searches
  function loadPopularSearches() {
    const popular = ['Bakpia', 'Kacang Hijau', 'Traveler Pack', 'Regular Pack', 'Gift Box'];
    popularSearchesList.innerHTML = popular.map(item => `
      <div onclick="selectSearchHistory('${item}')" 
           style="padding: 0.5rem; cursor: pointer; border-radius: 6px; margin-bottom: 0.25rem; display: flex; align-items: center; gap: 0.5rem;"
           onmouseover="this.style.background='#f0f0f0'" 
           onmouseout="this.style.background='transparent'">
        <i class="bx bx-trending-up" style="color: #147440;"></i>
        <span style="font-size: 0.875rem; color: #333;">${item}</span>
      </div>
    `).join('');
  }
  
  function selectSearchHistory(query) {
    searchInput.value = query;
    searchSuggestions.style.display = 'none';
    document.getElementById('searchForm').submit();
  }
  
  // Save search to history
  function saveSearchHistory(query) {
    if (!query || query.trim().length < 2) return;
    
    let history = JSON.parse(localStorage.getItem('searchHistory') || '[]');
    // Remove if exists
    history = history.filter(item => item.toLowerCase() !== query.toLowerCase());
    // Add to beginning
    history.unshift(query.trim());
    // Keep only last 10
    history = history.slice(0, 10);
    localStorage.setItem('searchHistory', JSON.stringify(history));
    
    // Save to backend if logged in
    @auth
      fetch('{{ route("api.search-history.save") }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ query: query.trim() })
      }).catch(() => {}); // Ignore errors
    @endauth
  }
  
  // Search input events
  if (searchInput) {
    searchInput.addEventListener('focus', function() {
      loadSearchHistory();
      loadPopularSearches();
      if (this.value.length === 0) {
        searchSuggestions.style.display = 'block';
      }
    });
    
    searchInput.addEventListener('input', function() {
      const query = this.value.trim();
      if (query.length > 0) {
        // Show autocomplete suggestions (can be enhanced with API call)
        searchSuggestions.style.display = 'none'; // Hide for now, can add autocomplete API later
      } else {
        loadSearchHistory();
        loadPopularSearches();
        searchSuggestions.style.display = 'block';
      }
    });
    
    // Hide suggestions when clicking outside
    document.addEventListener('click', function(e) {
      if (!searchInput.contains(e.target) && !searchSuggestions.contains(e.target)) {
        searchSuggestions.style.display = 'none';
      }
    });
    
    // Save search on form submit
    document.getElementById('searchForm').addEventListener('submit', function(e) {
      const query = searchInput.value.trim();
      if (query.length > 0) {
        saveSearchHistory(query);
      }
    });
  }
  
  // Wishlist Quick Toggle
  function toggleWishlistQuick(productId, button) {
    @guest
      window.location.href = '{{ route("mobile.login") }}';
      return;
    @endguest
    
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
        const icon = button.querySelector('i');
        if (data.is_wishlisted) {
          icon.className = 'bx bxs-heart';
          icon.style.color = '#dc3545';
        } else {
          icon.className = 'bx bx-heart';
          icon.style.color = '#666';
        }
        MobileNotification.success(data.message);
        updateWishlistBadge(data.count);
      }
    })
    .catch(error => {
      MobileErrorHandler.handle(error);
    });
  }
  
  // Add to Comparison
  function addToComparison(productId, button) {
    fetch('{{ route("mobile.comparison.add") }}', {
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
        button.style.background = '#147440';
        button.querySelector('i').style.color = 'white';
      } else {
        MobileNotification.error(data.message || 'Gagal menambahkan ke perbandingan');
      }
    })
    .catch(error => {
      MobileErrorHandler.handle(error);
    });
  }
  
  // Quick View Product
  function showQuickView(productId, slug) {
    const modal = document.getElementById('quickViewModal');
    const content = document.getElementById('quickViewContent');
    
    modal.style.display = 'block';
    document.body.style.overflow = 'hidden';
    content.innerHTML = `
      <div style="text-align: center; padding: 2rem;">
        <i class="bx bx-loader-alt bx-spin" style="font-size: 2rem; color: #147440;"></i>
        <p style="margin-top: 1rem; color: #666;">Memuat...</p>
      </div>
    `;
    
    // Fetch product data via API or direct page load
    fetch(`/m/shop/${slug}`, {
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
      }
    })
    .then(response => {
      if (response.headers.get('content-type')?.includes('application/json')) {
        return response.json();
      }
      return response.text().then(html => {
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        const productName = doc.querySelector('h4')?.textContent || 'Product';
        const productPrice = doc.querySelector('[style*="font-size: 1.5rem"]')?.textContent || '';
        const productImage = doc.querySelector('.product-image-zoom')?.getAttribute('data-image') || doc.querySelector('.product-image-zoom')?.src || '';
        return { productName, productPrice, productImage, slug };
      });
    })
    .then(data => {
      const productName = data.productName || data.name || 'Product';
      const productPrice = data.productPrice || (data.price ? `Rp${new Intl.NumberFormat('id-ID').format(data.price)}` : '');
      const productImage = data.productImage || (data.main_image_path ? `/storage/${data.main_image_path}` : '');
      const slug = data.slug || slug;
      
      content.innerHTML = `
        <div style="margin-bottom: 1rem;">
          <img src="${productImage}" 
               alt="${productName}"
               style="width: 100%; height: 200px; object-fit: cover; border-radius: 8px; margin-bottom: 1rem;"
               onerror="this.src='{{ asset('sneat/assets/img/placeholder.png') }}'">
          <h5 style="font-size: 1rem; font-weight: 700; margin-bottom: 0.5rem; color: #333;">${productName}</h5>
          <div style="font-size: 1.25rem; font-weight: 700; color: #147440; margin-bottom: 1rem;">${productPrice}</div>
        </div>
        <div style="display: flex; gap: 0.5rem;">
          <a href="/m/shop/${slug}" 
             onclick="closeQuickView()"
             style="flex: 1; padding: 0.75rem; background: #f0f0f0; color: #333; border-radius: 8px; text-decoration: none; text-align: center; font-weight: 600; font-size: 0.875rem;">
            Lihat Detail
          </a>
          <button onclick="addToCartQuickView(${productId}, '${slug}')" 
                  style="flex: 1; padding: 0.75rem; background: #147440; color: white; border: none; border-radius: 8px; font-weight: 600; font-size: 0.875rem; cursor: pointer;">
            <i class="bx bx-cart"></i> Tambah ke Keranjang
          </button>
        </div>
      `;
    })
    .catch(error => {
      content.innerHTML = `
        <div style="text-align: center; padding: 2rem; color: #dc3545;">
          <i class="bx bx-error-circle" style="font-size: 2rem; margin-bottom: 0.5rem;"></i>
          <p>Gagal memuat data produk</p>
          <button onclick="closeQuickView()" 
                  style="margin-top: 1rem; padding: 0.5rem 1rem; background: #147440; color: white; border: none; border-radius: 8px; cursor: pointer;">
            Tutup
          </button>
        </div>
      `;
      MobileErrorHandler.handle(error);
    });
  }
  
  function closeQuickView() {
    document.getElementById('quickViewModal').style.display = 'none';
    document.body.style.overflow = '';
  }
  
  function addToCartQuickView(productId, slug) {
    MobileLoading.show('Menambahkan ke keranjang...');
    
    fetch('{{ route("cart.add") }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: JSON.stringify({
        id: productId,
        qty: 1
      })
    })
    .then(response => response.json())
    .then(data => {
      MobileLoading.hide();
      if (data.success) {
        MobileNotification.success('Produk ditambahkan ke keranjang');
        closeQuickView();
        updateCartBadge();
        // Redirect to cart after short delay
        setTimeout(() => {
          window.location.href = '{{ route("mobile.cart") }}';
        }, 1000);
      } else {
        MobileNotification.error(data.error || 'Gagal menambahkan ke keranjang');
      }
    })
    .catch(error => {
      MobileLoading.hide();
      MobileErrorHandler.handle(error);
    });
  }
  
  function updateCartBadge() {
    fetch('{{ route("api.cart.count") }}', {
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
    .then(response => response.json())
    .then(data => {
      const badge = document.getElementById('cartBadge');
      if (badge) {
        if (data.count > 0) {
          badge.textContent = data.count > 99 ? '99+' : data.count;
          badge.style.display = 'flex';
        } else {
          badge.style.display = 'none';
        }
      }
    })
    .catch(() => {});
  }
  
  // Close modal when clicking outside
  document.getElementById('quickViewModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
      closeQuickView();
    }
  });
  
  // Close modal with ESC key
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
      closeQuickView();
    }
  });
  
  // Check wishlist status for products
  @auth
    document.querySelectorAll('[class^="wishlist-btn-"]').forEach(btn => {
      const match = btn.className.match(/wishlist-btn-(\d+)/);
      if (match) {
        const productId = match[1];
        fetch(`/m/wishlist/check/${productId}`, {
          headers: {
            'X-Requested-With': 'XMLHttpRequest'
          }
        })
        .then(response => response.json())
        .then(data => {
          if (data.is_wishlisted) {
            const icon = btn.querySelector('i');
            if (icon) {
              icon.className = 'bx bxs-heart';
              icon.style.color = '#dc3545';
            }
          }
        })
        .catch(() => {}); // Ignore errors
      }
    });
  @endauth
  
  // Pull to refresh handler
  document.getElementById('mainContent').addEventListener('pulltorefresh', function(e) {
    MobileLoading.show('Memuat ulang produk...');
    
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
      throw new Error('Gagal memuat produk');
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
