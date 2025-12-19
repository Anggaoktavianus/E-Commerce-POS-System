@extends('layouts.app')

@section('title', ($product->meta_title ?? $product->name) . ' - ' . config('app.name'))

@section('meta_description', $product->meta_description ?? Str::limit(strip_tags($product->description ?? $product->name), 160))

@section('meta_keywords', $product->meta_keywords ?? str_replace(' ', ', ', $product->name) . ', ' . config('app.name') . ', produk, belanja, harga, detail')

@section('og_image', ($product->main_image_path ?? null) ? asset('storage/' . $product->main_image_path) : asset('storage/defaults/og-product.jpg'))

@section('content')
    

    @include('partials.modern-page-header', [
        'pageTitle' => $product->name ?? ($siteSettings['product_detail_title'] ?? 'Detail Produk'),
        'breadcrumbItems' => [
            ['label' => 'Beranda', 'url' => url('/')],
            ['label' => 'Toko', 'url' => route('shop')],
            ['label' => $product->name ?? 'Detail Produk', 'url' => null]
        ]
    ])

    <!-- Single Product Start -->
    <div class="container-fluid py-5 mt-5" style="background: linear-gradient(180deg, #f8f9fa 0%, #ffffff 100%);">
        <div class="container py-5">
            <div class="row g-4 mb-5">
                <div class="col-lg-8 col-xl-9">
                    <div class="row g-4">
                        <div class="col-lg-6">
                            <div class="border rounded overflow-hidden">
                                @php
                                    $price = $product->price ?? 0;
                                    $gallery = $images ?? collect();
                                @endphp

                                @if($gallery->count() > 1)
                                    <div id="productGallery" class="carousel slide" data-bs-ride="carousel">
                                        <div class="carousel-inner">
                                            @foreach($gallery as $idx => $img)
                                                @php
                                                    $src = $img->image_path
                                                        ? 'storage/'.$img->image_path
                                                        : 'fruitables/img/vegetable-item-1.jpg';
                                                @endphp
                                                <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                                                    <img src="{{ asset($src) }}" class="d-block w-100 rounded" alt="{{ $product->name }}">
                                                </div>
                                            @endforeach
                                        </div>
                                        <button class="carousel-control-prev" type="button" data-bs-target="#productGallery" data-bs-slide="prev">
                                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                            <span class="visually-hidden">Sebelumnya</span>
                                        </button>
                                        <button class="carousel-control-next" type="button" data-bs-target="#productGallery" data-bs-slide="next">
                                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                            <span class="visually-hidden">Selanjutnya</span>
                                        </button>
                                    </div>
                                    <div class="mt-2 d-flex justify-content-center gap-2">
                                        @foreach($gallery as $idx => $img)
                                            @php
                                                $src = $img->image_path
                                                    ? 'storage/'.$img->image_path
                                                    : 'fruitables/img/vegetable-item-1.jpg';
                                            @endphp
                                            <img src="{{ asset($src) }}" class="img-thumbnail" style="width:60px;height:60px;cursor:pointer;"
                                                 data-bs-target="#productGallery" data-bs-slide-to="{{ $idx }}">
                                        @endforeach
                                    </div>
                                @else
                                    @php
                                        $image = $product->main_image_path
                                            ? 'storage/'.$product->main_image_path
                                            : 'fruitables/img/vegetable-item-1.jpg';
                                    @endphp
                                    <img src="{{ asset($image) }}" class="img-fluid rounded" alt="{{ $product->name }}">
                                @endif
                            </div>
                        </div>
                        <div class="col-lg-6">
                            @if($product->store_short_name)
                            <div class="mb-2">
                                <span class="badge bg-secondary" style="font-size: 0.875rem; font-weight: 500;">
                                    <i class="fas fa-store me-1"></i>{{ $product->store_short_name }}
                                </span>
                            </div>
                            @endif
                            <h4 class="fw-bold mb-3">{{ $product->name }}</h4>
                            <h5 class="fw-bold mb-3">{{ $siteSettings['currency_symbol'] ?? 'Rp.' }} {{ number_format($price, 0, ',', '.') }} {{ $product->unit ? '/ '.$product->unit : '' }}</h5>
                            @php
                                $detailStockQty = $product->stock_qty ?? 0;
                                $detailIsOutOfStock = $detailStockQty <= 0;
                            @endphp
                            @if($detailIsOutOfStock)
                                <div class="mb-3">
                                    <span class="badge bg-danger" style="font-size: 0.875rem; padding: 0.5rem 1rem;">
                                        <i class="fas fa-ban me-1"></i>Stok Habis
                                    </span>
                                </div>
                            @elseif($detailStockQty < 10)
                                <div class="mb-3">
                                    <span class="badge bg-warning text-dark" style="font-size: 0.875rem; padding: 0.5rem 1rem;">
                                        <i class="fas fa-exclamation-triangle me-1"></i>Stok Terbatas ({{ $detailStockQty }} {{ $product->unit ?? 'pcs' }})
                                    </span>
                                </div>
                            @else
                                <div class="mb-3">
                                    <span class="badge bg-success" style="font-size: 0.875rem; padding: 0.5rem 1rem;">
                                        <i class="fas fa-check-circle me-1"></i>Tersedia ({{ $detailStockQty }} {{ $product->unit ?? 'pcs' }})
                                    </span>
                                </div>
                            @endif
                            <p class="mb-3 text-muted">{{ $siteSettings['product_category_label'] ?? 'Kategori' }}: {{ $product->category->name ?? 'Produk' }}</p>
                            <div class="d-flex mb-4">
                                @for($i=1; $i<=5; $i++)
                                    <i class="fa fa-star {{ $i <= ($product->rating ?? 5) ? 'text-primary' : 'text-secondary' }}"></i>
                                @endfor
                            </div>
                            <p class="mb-4">{{ $product->short_description ?? ($siteSettings['product_default_description'] ?? 'Fresh product from Samsae.') }}</p>
                            <p class="mb-4">{!! nl2br(e($product->description ?? '')) !!}</p>
                            @php
                                $stockQty = $detailStockQty;
                                $isOutOfStock = $detailIsOutOfStock;
                            @endphp
                            <form method="POST" action="{{ route('cart.add') }}" id="add-to-cart-form" class="mb-4 d-flex align-items-center gap-3">
                                @csrf
                                <input type="hidden" name="id" value="{{ $product->id }}">
                                <input type="hidden" name="name" value="{{ $product->name }}">
                                <input type="hidden" name="price" value="{{ $price }}">
                                <input type="hidden" name="image" value="{{ $product->main_image_path ? ('storage/'.$product->main_image_path) : '' }}">
                                <input type="number" name="qty" id="product-qty" value="1" min="1" max="{{ $stockQty }}" class="form-control form-control-sm text-center" style="width: 100px;" placeholder="{{ $siteSettings['quantity_placeholder'] ?? 'Jumlah' }}" {{ $isOutOfStock ? 'disabled' : '' }}>
                                <div id="stock-status" class="small text-muted"></div>
                                @if($isOutOfStock)
                                    <button type="button" class="btn border border-secondary rounded-pill px-4 py-2 text-muted" disabled>
                                        <i class="fa fa-ban me-2"></i> Stok Habis
                                    </button>
                                @else
                                    <button type="submit" id="add-to-cart-btn" class="btn border border-secondary rounded-pill px-4 py-2 text-primary">
                                        <i class="fa fa-shopping-bag me-2 text-primary"></i> {{ $siteSettings['add_to_cart_text'] ?? 'Tambah ke Keranjang' }}
                                    </button>
                                @endif
                            </form>
                            <script>
                            (function() {
                                var productId = {{ $product->id }};
                                var qtyInput = document.getElementById('product-qty');
                                var stockStatus = document.getElementById('stock-status');
                                var addBtn = document.getElementById('add-to-cart-btn');
                                var form = document.getElementById('add-to-cart-form');
                                
                                function checkStock() {
                                    if (!qtyInput || !stockStatus) return;
                                    
                                    var qty = parseInt(qtyInput.value) || 1;
                                    
                                    fetch('{{ route("cart.check_stock") }}?product_id=' + productId + '&quantity=' + qty)
                                        .then(response => response.json())
                                        .then(data => {
                                            if (data.available) {
                                                stockStatus.innerHTML = '<span class="text-success"><i class="fa fa-check-circle"></i> Stok tersedia (' + data.stock + ')</span>';
                                                if (addBtn) addBtn.disabled = false;
                                            } else {
                                                stockStatus.innerHTML = '<span class="text-danger"><i class="fa fa-exclamation-circle"></i> ' + data.message + '</span>';
                                                if (addBtn) addBtn.disabled = true;
                                            }
                                            
                                            if (data.is_low_stock && data.available) {
                                                stockStatus.innerHTML = '<span class="text-warning"><i class="fa fa-exclamation-triangle"></i> Stok terbatas (' + data.stock + ' tersisa)</span>';
                                            }
                                        })
                                        .catch(error => {
                                            console.error('Error checking stock:', error);
                                        });
                                }
                                
                                if (qtyInput) {
                                    qtyInput.addEventListener('change', checkStock);
                                    qtyInput.addEventListener('keyup', function() {
                                        setTimeout(checkStock, 500);
                                    });
                                }
                                
                                if (form) {
                                    form.addEventListener('submit', function(e) {
                                        var qty = parseInt(qtyInput.value) || 1;
                                        fetch('{{ route("cart.check_stock") }}?product_id=' + productId + '&quantity=' + qty)
                                            .then(response => response.json())
                                            .then(data => {
                                                if (!data.available) {
                                                    e.preventDefault();
                                                    alert(data.message);
                                                    return false;
                                                }
                                            })
                                            .catch(error => {
                                                console.error('Error checking stock:', error);
                                            });
                                    });
                                }
                                
                                // Initial check
                                setTimeout(checkStock, 1000);
                            })();
                            </script>
                        </div>
                        <div class="col-lg-12">
                            <nav>
                                <div class="nav nav-tabs mb-3">
                                    <button class="nav-link active border-white border-bottom-0" type="button" role="tab"
                                        id="nav-about-tab" data-bs-toggle="tab" data-bs-target="#nav-about"
                                        aria-controls="nav-about" aria-selected="true">{{ $siteSettings['product_description_tab'] ?? 'Deskripsi' }}</button>
                                    <button class="nav-link border-white border-bottom-0" type="button" role="tab"
                                        id="nav-mission-tab" data-bs-toggle="tab" data-bs-target="#nav-mission"
                                        aria-controls="nav-mission" aria-selected="false">{{ $siteSettings['product_reviews_tab'] ?? 'Ulasan' }}</button>
                                </div>
                            </nav>
                            <div class="tab-content mb-5">
                                <div class="tab-pane active" id="nav-about" role="tabpanel" aria-labelledby="nav-about-tab">
                                    <p>{{ $product->short_description ?? 'Fresh product from Samsae, langsung dari mitra terbaik kami.' }}</p>
                                    @if(!empty($product->description))
                                        <p>{!! nl2br(e($product->description)) !!}</p>
                                    @endif
                                    <div class="px-2 mt-3">
                                        <div class="row g-4">
                                            <div class="col-6">
                                                <div class="row bg-light align-items-center text-center justify-content-center py-2">
                                                    <div class="col-6">
                                                        <p class="mb-0">{{ $siteSettings['price_label'] ?? 'Harga' }}</p>
                                                    </div>
                                                    <div class="col-6">
                                                        <p class="mb-0">{{ $siteSettings['currency_symbol'] ?? 'Rp.' }} {{ number_format($price, 2) }} {{ $product->unit ? '/ '.$product->unit : '' }}</p>
                                                    </div>
                                                </div>
                                                <div class="row bg-light align-items-center text-center justify-content-center py-2">
                                                    <div class="col-6">
                                                        <p class="mb-0">Stock</p>
                                                    </div>
                                                    <div class="col-6">
                                                        <p class="mb-0">{{ $product->stock_qty ?? 0 }} {{ $product->unit ?? 'pcs' }}</p>
                                                    </div>
                                                </div>
                                                <div class="row text-center align-items-center justify-content-center py-2">
                                                    <div class="col-6">
                                                        <p class="mb-0">{{ $siteSettings['unit_label'] ?? 'Unit' }}</p>
                                                    </div>
                                                    <div class="col-6">
                                                        <p class="mb-0">{{ $product->unit ?? 'kg' }}</p>
                                                    </div>
                                                </div>
                                                <div class="row bg-light text-center align-items-center justify-content-center py-2">
                                                    <div class="col-6">
                                                        <p class="mb-0">{{ $siteSettings['stock_label'] ?? 'Stock' }}</p>
                                                    </div>
                                                    <div class="col-6">
                                                        <p class="mb-0">{{ $product->stock ?? 'Available' }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="row text-center align-items-center justify-content-center py-2">
                                                    <div class="col-6">
                                                        <p class="mb-0">{{ $siteSettings['size_label'] ?? 'Size' }}</p>
                                                    </div>
                                                    <div class="col-6">
                                                        <p class="mb-0">{{ $product->size ?? 'Medium' }}</p>
                                                    </div>
                                                </div>
                                                <div class="row bg-light text-center align-items-center justify-content-center py-2">
                                                    <div class="col-6">
                                                        <p class="mb-0">{{ $siteSettings['color_label'] ?? 'Color' }}</p>
                                                    </div>
                                                    <div class="col-6">
                                                        <p class="mb-0">{{ $product->color ?? 'Natural' }}</p>
                                                    </div>
                                                </div>
                                                <div class="row text-center align-items-center justify-content-center py-2">
                                                    <div class="col-6">
                                                        <p class="mb-0">{{ $siteSettings['weight_label'] ?? 'Weight' }}</p>
                                                    </div>
                                                    <div class="col-6">
                                                        <p class="mb-0">{{ $product->weight ?? '1kg' }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="nav-mission" role="tabpanel" aria-labelledby="nav-mission-tab">
                                    @forelse($reviews as $rev)
                                        <div class="d-flex mb-3">
                                            <img src="{{ asset('fruitables/img/avatar.jpg') }}" class="img-fluid rounded-circle p-3" style="width: 100px; height: 100px;" alt="">
                                            <div class="">
                                                <p class="mb-2" style="font-size: 14px;">{{ \Illuminate\Support\Carbon::parse($rev->created_at)->format('d M Y') }}</p>
                                                <div class="d-flex justify-content-between">
                                                    <h5>{{ $rev->name }}</h5>
                                                    <div class="d-flex mb-3">
                                                        @for($i=1;$i<=5;$i++)
                                                            <i class="fa fa-star {{ $i <= ($rev->rating ?? 0) ? 'text-secondary' : '' }}"></i>
                                                        @endfor
                                                    </div>
                                                </div>
                                                <p class="text-dark mb-0">{{ $rev->content }}</p>
                                            </div>
                                        </div>
                                    @empty
                                        <p class="mb-0">Belum ada ulasan untuk produk ini.</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                        <form action="{{ route('shop.reviews.store', $product->slug) }}" method="POST">
                            @csrf
                            <h4 class="mb-5 fw-bold">Leave a Reply</h4>
                            <div class="row g-4">
                                <div class="col-lg-6">
                                    <div class="border-bottom rounded">
                                        <input type="text" name="name" class="form-control border-0 me-4" placeholder="Your Name *" value="{{ old('name', auth()->user()->name ?? '') }}" required>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="border-bottom rounded">
                                        <input type="email" name="email" class="form-control border-0" placeholder="Your Email *" value="{{ old('email', auth()->user()->email ?? '') }}" required>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="border-bottom rounded my-4">
                                        <textarea name="content" class="form-control border-0" cols="30" rows="5" placeholder="Your Review *" spellcheck="false" required>{{ old('content') }}</textarea>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="d-flex justify-content-between py-3 mb-5 align-items-center">
                                        <div class="d-flex align-items-center">
                                            <p class="mb-0 me-3">Please rate:</p>
                                            <div class="d-flex align-items-center" style="font-size: 16px;">
                                                @for($i=1;$i<=5;$i++)
                                                    <label class="me-1">
                                                        <input type="radio" name="rating" value="{{ $i }}" class="d-none" {{ (int)old('rating',5) === $i ? 'checked' : '' }}>
                                                        <i class="fa fa-star {{ (int)old('rating',5) >= $i ? 'text-secondary' : 'text-muted' }}"></i>
                                                    </label>
                                                @endfor
                                            </div>
                                        </div>
                                        <button type="submit" class="btn border border-secondary text-primary rounded-pill px-4 py-3">Post Comment</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-lg-4 col-xl-3">
                    <div class="row g-4 fruite">
                        <div class="col-lg-12">
                            <div class="input-group w-100 mx-auto d-flex mb-4">
                                <input type="search" class="form-control p-3" placeholder="keywords" aria-describedby="search-icon-1">
                                <span id="search-icon-1" class="input-group-text p-3"><i class="fa fa-search"></i></span>
                            </div>
                            <div class="mb-4">
                                <h4>Categories</h4>
                                <ul class="list-unstyled fruite-categorie">
                                    <li>
                                        <div class="d-flex justify-content-between fruite-name">
                                            <a href="{{ route('shop') }}">
                                                <i class="fas fa-apple-alt me-2"></i>All Products
                                            </a>
                                        </div>
                                    </li>
                                    @foreach($categories as $cat)
                                        <li>
                                            <div class="d-flex justify-content-between fruite-name">
                                                <a href="{{ route('shop', ['category' => $cat->slug]) }}">
                                                    <i class="fas fa-apple-alt me-2"></i>{{ $cat->name }}
                                                </a>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <h4 class="mb-4">Featured products</h4>
                            @foreach($sidebarFeatured as $feat)
                                
                                @php
                                    $featImage = $feat->main_image_path
                                        ? 'storage/'.$feat->main_image_path
                                        : 'fruitables/img/fruite-item-1.jpg';
                                    $featUrl = $feat->slug ? route('shop.detail', $feat->slug) : '#';
                                @endphp
                                <div class="d-flex align-items-center justify-content-start mb-3">
                                    <div class="rounded me-3" style="width: 100px; height: 100px;">
                                        <a href="{{ $featUrl }}">
                                            <img src="{{ asset($featImage) }}" class="img-fluid rounded" alt="{{ $feat->name }}">
                                        </a>
                                    </div>
                                    <div>
                                        @if($feat->store_short_name)
                                        <div class="mb-1">
                                            <span class="badge bg-secondary" style="font-size: 0.65rem; font-weight: 500;">
                                                <i class="fas fa-store me-1"></i>{{ $feat->store_short_name }}
                                            </span>
                                        </div>
                                        @endif
                                        <h6 class="mb-2"><a href="{{ $featUrl }}" class="text-dark text-decoration-none">{{ $feat->name }}</a></h6>
                                        <div class="d-flex mb-2">
                                            <i class="fa fa-star text-secondary"></i>
                                            <i class="fa fa-star text-secondary"></i>
                                            <i class="fa fa-star text-secondary"></i>
                                            <i class="fa fa-star text-secondary"></i>
                                            <i class="fa fa-star"></i>
                                        </div>
                                        <div class="d-flex mb-0">
                                            <h5 class="fw-bold me-2">Rp. {{ number_format($feat->price ?? 0, 2) }}</h5>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                    </div>
                </div>
            </div>
            <h1 class="fw-bold mb-0">Related products</h1>
            <div class="vesitable">
                <div class="owl-carousel vegetable-carousel justify-content-center">
                    @foreach($related as $rel)
                        @php
                            $relImage = $rel->main_image_path
                                ? 'storage/'.$rel->main_image_path
                                : 'fruitables/img/fruite-item-1.jpg';
                            $relUrl = $rel->slug ? route('shop.detail', $rel->slug) : '#';
                        @endphp
                        <div class="rounded position-relative fruite-item product-card bg-white border border-success border-2 shadow-sm d-flex flex-column h-100">
                            <a href="{{ $relUrl }}" class="product-image-link">
                                <div class="fruite-img ratio ratio-4x3 rounded-top overflow-hidden">
                                    <img src="{{ asset($relImage) }}" class="w-100 h-100 rounded-top" style="object-fit: contain;" alt="{{ $rel->name }}">
                                </div>
                            </a>
                            <div class="product-badge position-absolute" style="top: 15px; left: 15px; z-index: 10;">
                                {{ $siteSettings['product_badge_text'] ?? 'Produk' }}
                            </div>
                            @php
                                $relStockQty = $rel->stock_qty ?? 0;
                                $relIsOutOfStock = $relStockQty <= 0;
                            @endphp
                            @if(($rel->is_bestseller ?? 0) == 1)
                                <div class="promo-badge">
                                    <i class="fas fa-fire me-1"></i>Best Seller
                                </div>
                            @endif
                            <div class="p-4 rounded-bottom d-flex flex-column flex-grow-1 text-start">
                                @if($rel->store_short_name)
                                <div class="mb-1">
                                    <span class="badge bg-secondary" style="font-size: 0.75rem; font-weight: 500;">
                                        <i class="fas fa-store me-1"></i>{{ $rel->store_short_name }}
                                    </span>
                                </div>
                                @endif
                                <a href="{{ $relUrl }}" class="product-name-link text-decoration-none">
                                    <h4 class="fw-bold mb-2 text-dark" style="min-height: 3rem; line-height: 1.4;">{{ $rel->name }}</h4>
                                </a>
                                <div class="mb-2">
                                    <p class="product-price mb-0" style="font-size: 1.1rem;">
                                        {{ $siteSettings['currency_symbol'] ?? 'Rp.' }} {{ number_format($rel->price ?? 0, 0, ',', '.') }}
                                    </p>
                                </div>
                                @if($relIsOutOfStock)
                                    <div class="mb-3">
                                        <span class="badge bg-danger stock-badge">
                                            <i class="fas fa-ban me-1"></i>Stok Habis
                                        </span>
                                    </div>
                                @elseif($relStockQty < 10)
                                    <div class="mb-3">
                                        <span class="badge bg-warning text-dark stock-badge">
                                            <i class="fas fa-exclamation-triangle me-1"></i>Stok Terbatas ({{ $relStockQty }})
                                        </span>
                                    </div>
                                @else
                                    <div class="mb-3">
                                        <span class="badge bg-success stock-badge">
                                            <i class="fas fa-check-circle me-1"></i>Tersedia ({{ $relStockQty }})
                                        </span>
                                    </div>
                                @endif
                                <div class="d-flex justify-content-between align-items-center flex-lg-wrap mt-auto pt-3" style="border-top: 1px solid #e9ecef;">
                                    <div>
                                        <p class="product-price mb-0">{{ $siteSettings['currency_symbol'] ?? 'Rp.' }} {{ number_format($rel->price ?? 0, 0, ',', '.') }}</p>
                                        @if($rel->unit)
                                            <small style="color: #6c757d !important;">/ {{ $rel->unit }}</small>
                                        @endif
                                    </div>
                                    <form action="{{ route('cart.add') }}" method="POST" class="add-to-cart-form">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $rel->id }}">
                                        <input type="hidden" name="name" value="{{ $rel->name }}">
                                        <input type="hidden" name="price" value="{{ $rel->price ?? 0 }}">
                                        <input type="hidden" name="image" value="{{ $rel->main_image_path ? ('storage/'.$rel->main_image_path) : '' }}">
                                        <input type="hidden" name="qty" value="1" max="{{ $relStockQty }}">
                                        @if($relIsOutOfStock)
                                            <button class="btn btn-secondary rounded-pill px-4" type="button" disabled style="opacity: 0.6;">
                                                <i class="fa fa-ban me-2"></i>Stok Habis
                                            </button>
                                        @else
                                            <button class="btn add-to-cart-btn" type="submit">
                                                <span class="btn-text">
                                                    <i class="fa fa-shopping-bag me-2"></i>
                                                    <span class="d-none d-md-inline">{{ $siteSettings['add_to_cart_text'] ?? 'Tambah' }}</span>
                                                    <span class="d-md-none">+</span>
                                                </span>
                                                <span class="btn-loading d-none">
                                                    <span class="spinner-border spinner-border-sm me-2"></span>
                                                    <span class="d-none d-md-inline">Menambahkan...</span>
                                                </span>
                                            </button>
                                        @endif
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <!-- Single Product End -->

@push('styles')
<style>
    /* Enhanced Product Cards - Same as Home */
    .product-card {
        border: none !important;
        border-radius: 1.25rem !important;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1) !important;
        overflow: hidden;
        background: #fff;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        position: relative;
    }
    .product-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #147440, #20c997);
        transform: scaleX(0);
        transform-origin: left;
        transition: transform 0.4s ease;
    }
    .product-card:hover::before {
        transform: scaleX(1);
    }
    .product-card:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 20px 40px rgba(20, 116, 64, 0.15), 0 8px 16px rgba(0, 0, 0, 0.1);
        border-color: #147440 !important;
    }
    .product-card .fruite-img {
        position: relative;
        overflow: hidden;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    }
    .product-card .fruite-img img {
        transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .product-card:hover .fruite-img img {
        transform: scale(1.1);
    }
    .product-badge {
        background: linear-gradient(135deg, #0f5c33 0%, #147440 100%);
        color: white !important;
        box-shadow: 0 4px 12px rgba(20, 116, 64, 0.3);
        font-weight: 700;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        font-size: 0.75rem;
        padding: 0.5rem 1rem;
        border-radius: 50px;
        animation: pulse 2s ease-in-out infinite;
    }
    @keyframes pulse {
        0%, 100% {
            box-shadow: 0 4px 12px rgba(20, 116, 64, 0.3);
        }
        50% {
            box-shadow: 0 4px 20px rgba(20, 116, 64, 0.5);
        }
    }
    .promo-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        box-shadow: 0 4px 12px rgba(255, 107, 107, 0.4);
        z-index: 10;
        animation: pulse 2s ease-in-out infinite;
    }
    .product-image-link,
    .product-name-link {
        cursor: pointer;
        text-decoration: none;
        color: inherit;
        transition: all 0.3s ease;
        display: block;
    }
    .product-image-link:hover {
        opacity: 0.9;
    }
    .product-image-link:hover img {
        transform: scale(1.05);
    }
    .product-name-link:hover {
        text-decoration: none;
    }
    .product-name-link h4 {
        transition: color 0.3s ease;
    }
    .product-name-link:hover h4 {
        color: #147440 !important;
    }
    .add-to-cart-btn {
        background: linear-gradient(135deg, #147440 0%, #20c997 100%);
        border: none !important;
        color: white !important;
        font-weight: 600;
        padding: 0.75rem 1.5rem;
        border-radius: 50px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(20, 116, 64, 0.3);
        position: relative;
        overflow: hidden;
    }
    .add-to-cart-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(20, 116, 64, 0.4);
        background: linear-gradient(135deg, #0f5c33 0%, #147440 100%);
    }
    .product-price {
        font-size: 1.5rem;
        font-weight: 700;
        background: linear-gradient(135deg, #147440 0%, #20c997 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        letter-spacing: -0.02em;
    }
    .stock-badge {
        border-radius: 50px;
        padding: 0.4rem 0.9rem;
        font-weight: 600;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        text-transform: uppercase;
    }
    .add-to-cart-form { position: relative; }
    .add-to-cart-btn:disabled { opacity: 0.6; cursor: not-allowed; }
    .btn-loading .spinner-border { width: 1rem; height: 1rem; border-width: 0.15em; }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add to cart with loading state
    document.querySelectorAll('.add-to-cart-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            const btn = this.querySelector('.add-to-cart-btn');
            const btnText = btn ? btn.querySelector('.btn-text') : null;
            const btnLoading = btn ? btn.querySelector('.btn-loading') : null;
            
            if (btn && !btn.disabled) {
                btn.disabled = true;
                if (btnText) btnText.classList.add('d-none');
                if (btnLoading) btnLoading.classList.remove('d-none');
            }
        });
    });
});
</script>
@endpush

@endsection
