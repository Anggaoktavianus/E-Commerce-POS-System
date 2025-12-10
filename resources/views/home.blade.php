@extends('layouts.app')

@section('title', $settings['site_name'] ?? $settings['brand_name'] ?? config('app.name'))

@section('meta_description', $settings['meta_description'] ?? ($settings['site_description'] ?? ($settings['site_name'] ?? $settings['brand_name'] ?? config('app.name')) . ' - Toko online terpercaya untuk kebutuhan sehari-hari. Produk segar, berkualitas dengan harga terjangkau.'))

@section('meta_keywords', $settings['meta_keywords'] ?? 'toko online, belanja online, produk segar, sayuran, buah-buahan, kebutuhan sehari-hari, ' . ($settings['site_name'] ?? $settings['brand_name'] ?? config('app.name')))

@section('og_image', $settings['site_logo'] ?? asset('storage/defaults/og-home.jpg'))

@section('content')
    <style>
      .product-card{border-color:#147440 !important;border-width:1px !important;transition:border-color .25s ease,box-shadow .25s ease,transform .25s ease;border-radius:1.1rem}
      .product-card:hover{box-shadow:0 10px 24px rgba(20,116,64,.18),0 3px 8px rgba(0,0,0,.05);transform:translateY(-3px)}
      .product-card:active{box-shadow:0 14px 30px rgba(20,116,64,.25),0 6px 12px rgba(0,0,0,.08);transform:translateY(-1px)}
      /* Main Artikel Cards */
      .main-artikel-card{transition:transform .25s ease,box-shadow .25s ease}
      .main-artikel-card:hover{transform:translateY(-8px);box-shadow:0 15px 35px rgba(0,0,0,.15)}
      .main-artikel-title{transition:color .25s ease}
      .main-artikel-title:hover{color:#0d6efd !important}
      .main-artikel-image{transition:transform .25s ease}
      .main-artikel-card:hover .main-artikel-image{transform:scale(1.02)}
      /* Side Artikel Cards */
      .side-artikel-card{transition:transform .25s ease,box-shadow .25s ease}
      .side-artikel-card:hover{transform:translateY(-3px);box-shadow:0 8px 20px rgba(0,0,0,.1)}
      .side-artikel-title{transition:color .25s ease}
      .side-artikel-title:hover{color:#0d6efd !important}
      .side-artikel-image{transition:transform .25s ease}
      .side-artikel-card:hover .side-artikel-image{transform:scale(1.05)}
      /* Smooth carousel movement */
      .banners-middle-carousel .owl-stage{transition-timing-function:cubic-bezier(.25,.8,.25,1) !important}
      .banners-middle-carousel .item .service-item{transition:box-shadow .25s ease, transform .25s ease}
      .banners-middle-carousel .item .service-item:hover{transform:translateY(-2px);box-shadow:0 8px 20px rgba(0,0,0,.08)}
    </style>
    <!-- Hero Start -->
    @php
      $heroBg = $settings['hero_bg'] ?? ($slides->first()->image_path ?? 'fruitables/img/hero-img.jpg');
      $heroBgUrl = asset($heroBg);
    @endphp
    <div class="container-fluid py-5 mb-5 hero-header" style="background-image: linear-gradient(rgba(248,223,173,0.1), rgba(248,223,173,0.1)), url('{{ $heroBgUrl }}'); background-position:center center; background-repeat:no-repeat; background-size:cover;">
        <div class="container py-5">
            <div class="row g-5 align-items-center">
                <div class="col-md-12 col-lg-7">
                    <h4 class="mb-3 text-secondary">{{ $settings['brand_tagline'] ?? '' }}</h4>
                    <h1 class="mb-5 display-3 text-primary">{{ $settings['brand_name'] ?? 'Welcome' }}</h1>
                    <div class="position-relative mx-auto">
                        <input class="form-control border-2 border-secondary w-75 py-3 px-4 rounded-pill" type="text" placeholder="{{ $siteSettings['search_placeholder'] ?? 'Search for products...' }}">
                        <button type="submit" class="btn btn-primary border-2 border-secondary py-3 px-4 position-absolute rounded-pill text-white h-100" style="top: 0; right: 25%;">{{ $siteSettings['search_button_text'] ?? 'Search' }}</button>
                    </div>
                </div>
                <div class="col-md-12 col-lg-5">
                    <div id="carouselId" class="carousel slide position-relative" data-bs-ride="carousel">
                        <div class="carousel-inner" role="listbox">
                            @foreach($slides as $slide)
                                <div class="carousel-item {{ $loop->first ? 'active' : '' }} rounded">
                                    <img src="{{ asset($slide->image_path) }}" class="img-fluid w-100 h-100 rounded" alt="slide">
                                    @if($slide->button_text)
                                        <a href="{{ $slide->button_url ?? '#' }}" class="btn px-4 py-2 text-white rounded">{{ $slide->button_text }}</a>
                                    @endif
                                </div>
                            @endforeach 
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#carouselId" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#carouselId" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Hero End -->
    
    <!-- Featurs Section Start -->
    <div class="container-fluid featurs py-5">
        <div class="container py-5">
            <div class="row g-4">
                @foreach($features as $feature)
                    <div class="col-md-6 col-lg-3">
                        <div class="featurs-item text-center rounded bg-light p-4">
                            <div class="featurs-icon btn-square rounded-circle bg-secondary mb-5 mx-auto">
                                @if($feature->icon_class)
                                    <i class="{{ $feature->icon_class }} fa-3x text-white"></i>
                                @elseif($feature->image_path)
                                    <img src="{{ asset($feature->image_path) }}" class="img-fluid" alt="icon">
                                @endif
                            </div>
                            <div class="featurs-content text-center">
                                <h5>{{ $feature->title }}</h5>
                                <p class="mb-0">{{ $feature->description }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    <!-- Featurs Section End -->

    <!-- Fruits Shop Start-->
    <div class="container-fluid fruite py-5">
        <div class="container py-5">
            <div class="tab-class text-center">
                <div class="row g-4">
                    <div class="col-lg-4 text-start">
                        <h1>{{ $siteSettings['homepage_products_title'] ?? 'Our Products' }}</h1>
                        @if(!empty($siteSettings['homepage_products_subtitle']))
                            <p class="text-muted">{{ $siteSettings['homepage_products_subtitle'] }}</p>
                        @endif
                    </div>
                    <div class="col-lg-8 text-end">
                        <ul class="nav nav-pills d-inline-flex text-center mb-5">
                            <li class="nav-item">
                                <a class="d-flex m-2 py-2 bg-light rounded-pill active" data-bs-toggle="pill" href="#tab-all">
                                    <span class="text-dark" style="width: 130px;">All Products</span>
                                </a>
                            </li>
                            @foreach($categoriesTabs as $cat)
                                <li class="nav-item">
                                    <a class="d-flex m-2 py-2 bg-light rounded-pill" data-bs-toggle="pill" href="#tab-cat-{{ $cat->id }}">
                                        <span class="text-dark" style="width: 130px;">{{ $cat->name }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="tab-content">
                    <!-- All Products Tab -->
                    <div id="tab-all" class="tab-pane fade show p-0 active">
                        <div class="row g-4">
                            <div class="col-lg-12">
                                <div class="row g-4">
                                    @forelse($allProducts as $product)
                                        <div class="col-md-6 col-lg-4 col-xl-3">
                                            <div class="rounded position-relative fruite-item product-card bg-white border border-success border-2 shadow-sm d-flex flex-column h-100">
                                                <div class="fruite-img ratio ratio-4x3 rounded-top overflow-hidden">
                                                    <img src="{{ asset($product->main_image_path ? ('storage/' . $product->main_image_path) : 'fruitables/img/fruite-item-1.jpg') }}" class="w-100 h-100 rounded-top" style="object-fit: contain;" alt="">
                                                </div>
                                                <div class="text-white bg-secondary px-3 py-1 rounded position-absolute" style="top: 10px; left: 10px;">{{ $siteSettings['product_badge_text'] ?? 'Product' }}</div>
                                                <div class="p-4 rounded-bottom d-flex flex-column flex-grow-1 text-start">
                                                    <h4>{{ $product->name }}</h4>
                                                    <p class="mb-2 text-muted">{{ $product->short_description ?? ($siteSettings['product_default_description'] ?? 'Fresh product from Samsae.') }}</p>
                                                    <div class="d-flex justify-content-between flex-lg-wrap mt-auto">
                                                        <p class="text-dark fs-5 fw-bold mb-0">{{ $siteSettings['currency_symbol'] ?? 'Rp.' }} {{ number_format($product->price, 0, ',', '.') }} {{ $product->unit ? '/ '.$product->unit : '' }}</p>
                                                        <form action="{{ route('cart.add') }}" method="POST">
                                                            @csrf
                                                            <input type="hidden" name="id" value="{{ $product->id }}">
                                                            <input type="hidden" name="name" value="{{ $product->name }}">
                                                            <input type="hidden" name="price" value="{{ $product->price }}">
                                                            <input type="hidden" name="image" value="{{ $product->main_image_path ? ('storage/' . $product->main_image_path) : '' }}">
                                                            <input type="hidden" name="qty" value="1">
                                                            <button class="btn border border-secondary rounded-pill px-3 text-primary" type="submit"><i class="fa fa-shopping-bag me-2 text-primary"></i> {{ $siteSettings['add_to_cart_text'] ?? 'Add to cart' }}</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="col-12 text-center py-4">{{ $siteSettings['no_products_found_text'] ?? 'No products found.' }}</div>
                                    @endforelse
                                </div>
                                @if(method_exists($allProducts, 'hasPages') && $allProducts->hasPages())
                                    <div class="col-12">
                                        <div class="pagination d-flex justify-content-center mt-4">
                                            @if ($allProducts->onFirstPage())
                                                <span class="rounded px-3 py-1 me-1 disabled">&laquo;</span>
                                            @else
                                                <a href="{{ $allProducts->previousPageUrl() }}" class="rounded px-3 py-1 me-1">&laquo;</a>
                                            @endif

                                            @for ($page = 1; $page <= $allProducts->lastPage(); $page++)
                                                @if ($page == $allProducts->currentPage())
                                                    <span class="active rounded px-3 py-1 me-1">{{ $page }}</span>
                                                @else
                                                    <a href="{{ $allProducts->url($page) }}" class="rounded px-3 py-1 me-1">{{ $page }}</a>
                                                @endif
                                            @endfor

                                            @if ($allProducts->hasMorePages())
                                                <a href="{{ $allProducts->nextPageUrl() }}" class="rounded px-3 py-1 ms-1">&raquo;</a>
                                            @else
                                                <span class="rounded px-3 py-1 ms-1 disabled">&raquo;</span>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Category Tabs -->
                    @foreach($categoriesTabs as $cat)
                        <div id="tab-cat-{{ $cat->id }}" class="tab-pane fade p-0">
                            <div class="row g-4">
                                <div class="col-lg-12">
                                    <div class="row g-4">
                                        @forelse($cat->products as $product)
                                            <div class="col-md-6 col-lg-4 col-xl-3">
                                                <div class="rounded position-relative fruite-item product-card bg-white border border-success border-2 shadow-sm d-flex flex-column h-100">
                                                    <div class="fruite-img ratio ratio-4x3 rounded-top overflow-hidden">
                                                        <img src="{{ asset($product->main_image_path ? ('storage/' . $product->main_image_path) : 'fruitables/img/fruite-item-1.jpg') }}" class="w-100 h-100 rounded-top" style="object-fit: contain;" alt="">
                                                    </div>
                                                    <div class="text-white bg-secondary px-3 py-1 rounded position-absolute" style="top: 10px; left: 10px;">{{ $siteSettings['product_badge_text'] ?? 'Product' }}</div>
                                                    <div class="p-4 rounded-bottom d-flex flex-column flex-grow-1 text-start">
                                                        <h4>{{ $product->name }}</h4>
                                                        <p class="mb-2 text-muted">{{ $product->short_description ?? ($siteSettings['product_default_description'] ?? 'Fresh product from Samsae.') }}</p>
                                                        <div class="d-flex justify-content-between flex-lg-wrap mt-auto">
                                                            <p class="text-dark fs-5 fw-bold mb-0">{{ $siteSettings['currency_symbol'] ?? 'Rp.' }} {{ number_format($product->price, 0, ',', '.') }} {{ $product->unit ? '/ '.$product->unit : '' }}</p>
                                                            <form action="{{ route('cart.add') }}" method="POST">
                                                                @csrf
                                                                <input type="hidden" name="id" value="{{ $product->id }}">
                                                                <input type="hidden" name="name" value="{{ $product->name }}">
                                                                <input type="hidden" name="price" value="{{ $product->price }}">
                                                                <input type="hidden" name="image" value="{{ $product->main_image_path ? ('storage/' . $product->main_image_path) : '' }}">
                                                                <input type="hidden" name="qty" value="1">
                                                                <button class="btn border border-secondary rounded-pill px-3 text-primary" type="submit"><i class="fa fa-shopping-bag me-2 text-primary"></i> {{ $siteSettings['add_to_cart_text'] ?? 'Add to cart' }}</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="col-12 text-center py-4">{{ $siteSettings['no_category_products_text'] ?? 'No products in' }} {{ $cat->name }}.</div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>      
        </div>
    </div>
    <!-- Fruits Shop End-->

    <!-- Artikel Section Start -->
    @php
        $latestArtikel = \App\Models\Artikel::with(['kategoriArtikel', 'user'])
            ->where('status', 'published')
            ->orderBy('created_at', 'desc')
            ->limit(4)
            ->get();
        $mainArtikel = $latestArtikel->first();
        $otherArtikel = $latestArtikel->slice(1);
    @endphp
    
    @if($latestArtikel->count() > 0)
    <div class="container-fluid bg-light py-5">
        <div class="container py-5">
            <div class="text-center mx-auto" style="max-width: 700px;">
                <h1 class="text-primary mb-4">Artikel & Berita</h1>
                <p class="mb-0">Temukan informasi menarik, tips berguna, dan berita terbaru dari kami</p>
            </div>
            
            <div class="row g-4 mt-4">
                <!-- Main Article (Left Side) -->
                @if($mainArtikel)
                    <div class="col-lg-6">
                        <div class="card h-100 shadow-lg main-artikel-card">
                            @if($mainArtikel->gambar_utama)
                                <img src="{{ Storage::url($mainArtikel->gambar_utama) }}" 
                                     class="card-img-top main-artikel-image" 
                                     alt="{{ $mainArtikel->judul }}"
                                     style="height: 350px; object-fit: cover;">
                            @elseif($mainArtikel->gambar_thumbnail)
                                <img src="{{ Storage::url($mainArtikel->gambar_thumbnail) }}" 
                                     class="card-img-top main-artikel-image" 
                                     alt="{{ $mainArtikel->judul }}"
                                     style="height: 350px; object-fit: cover;">
                            @else
                                <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                                     style="height: 350px;">
                                    <i class="bx bx-image bx-5x text-muted"></i>
                                </div>
                            @endif
                            
                            <div class="card-body">
                                <!-- Category Badge -->
                                <div class="mb-3">
                                    <span class="badge bg-primary fs-6 px-3 py-2">{{ $mainArtikel->kategoriArtikel->nama }}</span>
                                    <span class="badge bg-success fs-6 px-3 py-2 ms-2">Terbaru</span>
                                </div>
                                
                                <!-- Title -->
                                <h2 class="card-title fw-bold mb-3">
                                    <a href="{{ route('artikel.show', $mainArtikel->slug) }}" 
                                       class="text-decoration-none text-dark main-artikel-title">
                                        {{ $mainArtikel->judul }}
                                    </a>
                                </h2>
                                
                                <!-- Excerpt -->
                                <p class="card-text text-muted mb-4">
                                    {{ Str::limit(strip_tags($mainArtikel->konten), 200) }}
                                </p>
                                
                                <!-- Meta Info -->
                                <div class="d-flex justify-content-between align-items-center text-muted mb-4">
                                    <div>
                                        <span class="me-3"><i class="bx bx-calendar me-1"></i> {{ $mainArtikel->created_at->format('d M Y') }}</span>
                                        <span class="me-3"><i class="bx bx-time me-1"></i> {{ $mainArtikel->reading_time }} menit</span>
                                        <span><i class="bx bx-user me-1"></i> {{ $mainArtikel->user ? $mainArtikel->user->name : 'Admin' }}</span>
                                    </div>
                                    <span><i class="bx bx-show me-1"></i> {{ number_format($mainArtikel->views) }}</span>
                                </div>
                                
                                <!-- Read More Button -->
                                <div>
                                    <a href="{{ route('artikel.show', $mainArtikel->slug) }}" 
                                       class="btn btn-primary btn-lg px-4 text-white">
                                        <i class="bx bx-book-open me-2"></i>Baca Selengkapnya
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                
                <!-- Other Articles (Right Side) -->
                <div class="col-lg-6">
                    <div class="row g-4 h-100">
                        @foreach($otherArtikel as $artikel)
                            <div class="col-12">
                                <div class="card h-100 shadow-sm side-artikel-card">
                                    <div class="row g-0 h-100">
                                        <div class="col-md-4">
                                            @if($artikel->gambar_thumbnail)
                                                <img src="{{ Storage::url($artikel->gambar_thumbnail) }}" 
                                                     class="img-fluid rounded-start h-100 side-artikel-image" 
                                                     alt="{{ $artikel->judul }}"
                                                     style="object-fit: cover;">
                                            @elseif($artikel->gambar_utama)
                                                <img src="{{ Storage::url($artikel->gambar_utama) }}" 
                                                     class="img-fluid rounded-start h-100 side-artikel-image" 
                                                     alt="{{ $artikel->judul }}"
                                                     style="object-fit: cover;">
                                            @else
                                                <div class="bg-light d-flex align-items-center justify-content-center h-100 rounded-start">
                                                    <i class="bx bx-image bx-3x text-muted"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col-md-8">
                                            <div class="card-body h-100 d-flex flex-column">
                                                <!-- Category Badge -->
                                                <div class="mb-2">
                                                    <span class="badge bg-primary">{{ $artikel->kategoriArtikel->nama }}</span>
                                                </div>
                                                
                                                <!-- Title -->
                                                <h5 class="card-title fw-bold mb-2">
                                                    <a href="{{ route('artikel.show', $artikel->slug) }}" 
                                                       class="text-decoration-none text-dark side-artikel-title">
                                                        {{ Str::limit($artikel->judul, 50) }}
                                                    </a>
                                                </h5>
                                                
                                                <!-- Excerpt -->
                                                <p class="card-text text-muted small flex-grow-1 mb-2">
                                                    {{ Str::limit(strip_tags($artikel->konten), 80) }}
                                                </p>
                                                
                                                <!-- Meta Info -->
                                                <div class="d-flex justify-content-between align-items-center text-muted small">
                                                    <div>
                                                        <span class="me-2"><i class="bx bx-calendar me-1"></i> {{ $artikel->created_at->format('d M') }}</span>
                                                        <span><i class="bx bx-time me-1"></i> {{ $artikel->reading_time }}m</span>
                                                    </div>
                                                    <span><i class="bx bx-show me-1"></i> {{ number_format($artikel->views) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            
            <!-- View All Articles Button -->
            <div class="text-center mt-5">
                <a href="{{ route('artikel.index') }}" class="btn btn-outline-primary btn-lg">
                    <i class="bx bx-file me-2"></i>Lihat Semua Artikel
                </a>
            </div>
        </div>
    </div>
    @endif
    <!-- Artikel Section End -->

<!-- Middle Banners Carousel Start -->
<div class="container-fluid service py-5">
    <div class="container py-5">
        <div class="owl-carousel banners-middle-carousel">
            @foreach($bannersMiddle as $banner)
                <div class="item px-2">
                    <a href="{{ $banner->button_url ?? '#' }}">
                        <div class="service-item bg-secondary rounded border border-secondary" style="height: 300px; overflow: hidden;">
                            <img src="{{ asset($banner->image_path) }}" class="img-fluid w-100 mt-2" style="object-fit: contain; height: 180px;" alt="">
                            <div class="px-4 rounded-bottom">
                                <div class="service-content bg-primary text-center p-4 rounded mt-4">
                                    <h5 class="text-white">{{ $banner->title }}</h5>
                                    @if($banner->subtitle) 
                                        <a href="{{ $banner->button_url ?? '#' }}" class="btn btn-outline-secondary btn-sm w-100 mt-3">
                                            <i class="bx bx-map me-2"></i> {{ $banner->subtitle }}
                                        </a>
                                    @endif
                                </div>
                            </div> 
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
        
        <style>
        .banners-middle-carousel .owl-nav button {
            width: 50px !important;
            height: 50px !important;
            background: white !important;
            border: none !important;
            border-radius: 50% !important;
            font-size: 24px !important;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3) !important;
        }
        .banners-middle-carousel .owl-nav button i {
            font-size: 24px !important;
            color: #333 !important;
        }
        .banners-middle-carousel .owl-nav button:hover {
            background: #f8f9fa !important;
            transform: scale(1.1) !important;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.4) !important;
        }
        .banners-middle-carousel .owl-nav button:hover i {
            color: #007bff !important;
        }
        </style>
    </div>
</div>
<!-- Middle Banners Carousel End -->

@push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function(){
      if (window.jQuery && jQuery.fn.owlCarousel) {
        const itemCount = {{ $bannersMiddle->count() ?? 0 }};
        const shouldLoop = itemCount > 3;
        jQuery('.banners-middle-carousel').owlCarousel({
          items: 3,
          margin: 16,
          loop: shouldLoop,
          autoplay: shouldLoop,
          autoplayTimeout: 3600,
          autoplayHoverPause: true,
          smartSpeed: 600,
          dotsSpeed: 600,
          dragEndSpeed: 500,
          dots: true,
          nav: true,
          navText: [
            '<i class="bx bx-chevron-left"></i>',
            '<i class="bx bx-chevron-right"></i>'
          ],
          responsive: {
            0: { items: 1 },
            576: { items: 2 },
            992: { items: 3 }
          }
        });
      }
    });
    </script>
    @endpush
    @push('styles')
    <style>
    /* Bootstrap carousel custom styles */
    .banners-middle-carousel .owl-nav {
  position: absolute !important;
  top: 50% !important;
  transform: translateY(-50%) !important;
  width: 100% !important;
  display: flex !important;
  justify-content: space-between !important;
  pointer-events: none !important;
  padding: 0 30px !important;
}

.banners-middle-carousel .owl-nav button {
  width: 50px !important;
  height: 50px !important;
  background: rgba(255, 255, 255, 0.95) !important;
  border: 3px solid #007bff !important;
  border-radius: 50% !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  cursor: pointer !important;
  pointer-events: all !important;
  transition: all 0.4s ease !important;
  box-shadow: 0 4px 15px rgba(0, 123, 255, 0.4) !important;
  margin: 0 !important;
}

.banners-middle-carousel .owl-nav button:hover {
  background: #007bff !important;
  border-color: #0056b3 !important;
  transform: scale(1.15) !important;
  box-shadow: 0 6px 20px rgba(0, 123, 255, 0.6) !important;
}

.banners-middle-carousel .owl-nav button i {
  font-size: 24px !important;
  color: #007bff !important;
  transition: color 0.4s ease !important;
  font-weight: bold !important;
  margin: 0 !important;
}

.banners-middle-carousel .owl-nav button:hover i {
  color: white !important;
}

.banners-middle-carousel .owl-nav .owl-prev {
  position: absolute !important;
  left: -25px !important;
  top: 50% !important;
  transform: translateY(-50%) !important;
}

.banners-middle-carousel .owl-nav .owl-next {
  position: absolute !important;
  right: -25px !important;
  top: 50% !important;
  transform: translateY(-50%) !important;
}

.banners-middle-carousel .owl-nav button:disabled {
  opacity: 0.4 !important;
  cursor: not-allowed !important;
  transform: scale(1) !important;
}

.banners-middle-carousel .owl-nav button:disabled:hover {
  background: rgba(255, 255, 255, 0.95) !important;
  border-color: #007bff !important;
  box-shadow: 0 4px 15px rgba(0, 123, 255, 0.4) !important;
}

.banners-middle-carousel .owl-nav button:disabled i {
  color: #007bff !important;
}
    </style>
    @endpush
    <!-- Artikel Section End -->

    
 
    <!-- Vesitable Shop Start-->
    <div class="container-fluid vesitable py-5">
        <div class="container py-5">
            <h1 class="mb-0">{{ $siteSettings['homepage_vegetables_title'] ?? 'Fresh Organic Vegetables' }}</h1>
            @if(!empty($siteSettings['homepage_vegetables_subtitle']))
                <p class="text-muted mb-4">{{ $siteSettings['homepage_vegetables_subtitle'] }}</p>
            @endif
            <div class="owl-carousel vegetable-carousel justify-content-center">
                @foreach($vegetableProducts as $product)
                    <div class="border border-primary rounded position-relative vesitable-item">
                        <div class="vesitable-img">
                            <img src="{{ asset($product->main_image_path ? ('storage/' . $product->main_image_path) : 'fruitables/img/vegetable-item-1.jpg') }}" class="img-fluid w-100 rounded-top" alt="">
                        </div>
                        <div class="text-white bg-secondary px-3 py-1 rounded position-absolute" style="top: 10px; left: 10px;">{{ $siteSettings['product_badge_text'] ?? 'Product' }}</div>
                        <div class="p-4 rounded-bottom">
                            <h4>{{ $product->name }}</h4>
                            <div class="d-flex justify-content-between flex-lg-wrap">
                                <p class="text-dark fs-5 fw-bold mb-0">{{ $siteSettings['currency_symbol'] ?? 'Rp.' }} {{ number_format($product->price, 0, ',', '.') }} {{ $product->unit ? '/ '.$product->unit : '' }}</p>
                                <form action="{{ route('cart.add') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $product->id }}">
                                    <input type="hidden" name="name" value="{{ $product->name }}">
                                    <input type="hidden" name="price" value="{{ $product->price }}">
                                    <input type="hidden" name="image" value="{{ $product->main_image_path ? ('storage/' . $product->main_image_path) : '' }}">
                                    <input type="hidden" name="qty" value="1">
                                    <button class="btn border border-secondary rounded-pill px-3 text-primary" type="submit"><i class="fa fa-shopping-bag me-2 text-primary"></i> {{ $siteSettings['add_to_cart_text'] ?? 'Add to cart' }}</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    <!-- Vesitable Shop End -->

    <!-- Banner Section Start-->
    @php $topCount = $bannersTop->count() ?? 0; @endphp
    <div class="container-fluid banner bg-secondary my-5">
        <div class="container py-5">
            @if($topCount > 1)
                <div class="owl-carousel banners-top-carousel">
                    @foreach($bannersTop as $banner)
                        <div class="item">
                            <div class="row g-4 align-items-center">
                                <div class="col-lg-6">
                                    <div class="py-4">
                                        <h1 class="display-3 text-white">{{ $banner->title ?? 'Welcome' }}</h1>
                                        @if(!empty($banner?->subtitle))
                                            <p class="fw-normal display-3 text-dark mb-4">{{ $banner->subtitle }}</p>
                                        @endif
                                        @if(!empty($banner?->button_text))
                                            <a href="{{ $banner->button_url ?? '#' }}" class="banner-btn btn border-2 border-white rounded-pill text-dark py-3 px-5">{{ $banner->button_text }}</a>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="position-relative">
                                        <img src="{{ asset($banner->image_path ?? 'fruitables/img/fruite-item-1.jpg') }}" class="img-fluid w-100 rounded" alt="">
                                        @if($banner->show_circle && $banner->circle_number)
                                        <div class="d-flex align-items-center justify-content-center bg-white rounded-circle position-absolute" style="width: 140px; height: 140px; top: 0; left: 0;">
                                            <h1 style="font-size: 100px;">{{ $banner->circle_number }}</h1>
                                            <div class="d-flex flex-column">
                                                <span class="h2 mb-0">{{ $banner->circle_value ?? '' }}</span>
                                                <span class="h4 text-muted mb-0">{{ $banner->circle_unit ?? '' }}</span>
                                            </div>
                                        </div>
                                    @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                @php $banner = $bannersTop->first(); @endphp
                <div class="row g-4 align-items-center">
                    <div class="col-lg-6">
                        <div class="py-4">
                            <h1 class="display-3 text-white">{{ $banner->title ?? 'Welcome' }}</h1>
                            @if(!empty($banner?->subtitle))
                                <p class="fw-normal display-3 text-dark mb-4">{{ $banner->subtitle }}</p>
                            @endif
                            @if(!empty($banner?->button_text))
                                <a href="{{ $banner->button_url ?? '#' }}" class="banner-btn btn border-2 border-white rounded-pill text-dark py-3 px-5">{{ $banner->button_text }}</a>
                            @endif
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="position-relative">
                            <img src="{{ asset($banner->image_path ?? 'fruitables/img/fruite-item-1.jpg') }}" class="img-fluid w-100 rounded" alt="">
                            @if($banner->show_circle && $banner->circle_number)
                                        <div class="d-flex align-items-center justify-content-center bg-white rounded-circle position-absolute" style="width: 140px; height: 140px; top: 0; left: 0;">
                                            <h1 style="font-size: 60px;">{{ $banner->circle_number }}</h1>
                                            <div class="d-flex flex-column">
                                                <span class="h2 mb-0">{{ $banner->circle_value ?? '' }}</span>
                                                <span class="h5 text-muted mb-0">{{ $banner->circle_unit ?? '' }}</span>
                                            </div>
                                        </div>
                                    @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function(){
      if (window.jQuery && jQuery.fn.owlCarousel) {
        const tCount = {{ $topCount }};
        if (tCount > 1) {
          jQuery('.banners-top-carousel').owlCarousel({
            items: 1,
            margin: 0,
            loop: true,
            autoplay: true,
            autoplayTimeout: 4500,
            autoplayHoverPause: true,
            smartSpeed: 650,
            dotsSpeed: 650,
            dragEndSpeed: 550,
            dots: true,
            nav: false
          });
        }
      }
    });
    </script>
    @endpush
    <!-- Banner Section End -->

    <!-- Bestsaler Product Start -->
    <div class="container-fluid py-5">
        <div class="container py-5">
            <div class="text-center mx-auto mb-5" style="max-width: 700px;">
                <h1 class="display-4">{{ $siteSettings['homepage_bestseller_title'] ?? 'Bestseller Products' }}</h1>
                @if(!empty($siteSettings['homepage_bestseller_subtitle']))
                    <p>{{ $siteSettings['homepage_bestseller_subtitle'] }}</p>
                @else
                    <p>Latin words, combined with a handful of model sentence structures, to generate Lorem Ipsum which looks reasonable.</p>
                @endif
            </div>
            <div class="row g-4">
                @foreach($bestsellerItems as $product)
                    <div class="col-lg-6 col-xl-4">
                        <div class="p-4 rounded bg-light">
                            <div class="row align-items-center">
                                <div class="col-6">
                                    <img src="{{ asset($product->main_image_path ? ('storage/' . $product->main_image_path) : 'fruitables/img/fruite-item-1.jpg') }}" class="img-fluid rounded-circle w-100" alt="">
                                </div>
                                <div class="col-6">
                                    <a href="#" class="h5">{{ $product->name }}</a>
                                    <div class="d-flex my-3">
                                        <i class="fas fa-star text-primary"></i>
                                        <i class="fas fa-star text-primary"></i>
                                        <i class="fas fa-star text-primary"></i>
                                        <i class="fas fa-star"></i>
                                    </div>
                                    <h4 class="mb-3">{{ $siteSettings['currency_symbol'] ?? 'Rp.' }} {{ number_format($product->price, 0, ',', '.') }}</h4>
                                    <form action="{{ route('cart.add') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $product->id }}">
                                        <input type="hidden" name="name" value="{{ $product->name }}">
                                        <input type="hidden" name="price" value="{{ $product->price }}">
                                        <input type="hidden" name="image" value="{{ $product->main_image_path ? ('storage/' . $product->main_image_path) : '' }}">
                                        <input type="hidden" name="qty" value="1">
                                        <button class="btn border border-secondary rounded-pill px-3 text-primary" type="submit"><i class="fa fa-shopping-bag me-2 text-primary"></i> {{ $siteSettings['add_to_cart_text'] ?? 'Add to cart' }}</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    <!-- Bestsaler Product End -->

    <!-- Fact Start -->
    <!-- <div class="container-fluid py-5">
        <div class="container">
            <div class="bg-light p-5 rounded">
                <div class="row g-4 justify-content-center">
                    @foreach($facts as $fact)
                        <div class="col-md-6 col-lg-6 col-xl-3">
                            <div class="counter bg-white rounded p-5">
                                @if($fact->icon_class)
                                    <i class="{{ $fact->icon_class }} text-secondary"></i>
                                @endif
                                <h4>{{ $fact->label }}</h4>
                                <h1>{{ number_format($fact->value) }}</h1>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div> -->
    <!-- Fact End -->

    <!-- Testimonial Start -->
    <div class="container-fluid testimonial py-5">
        <div class="container py-5">
            <div class="testimonial-header text-center">
                <h4 class="text-primary">{{ $siteSettings['homepage_testimonial_title'] ?? 'Our Testimonial' }}</h4>
                <h1 class="display-5 mb-5 text-dark">{{ $siteSettings['homepage_testimonial_subtitle'] ?? 'Our Client Saying!' }}</h1>
            </div>
            <div class="owl-carousel testimonial-carousel">
                @foreach($testimonials as $t)
                    <div class="testimonial-item img-border-radius bg-light rounded p-4">
                        <div class="position-relative">
                            <i class="fa fa-quote-right fa-2x text-secondary position-absolute" style="bottom: 30px; right: 0;"></i>
                            <div class="mb-4 pb-4 border-bottom border-secondary">
                                <p class="mb-0">{{ $t->content }}</p>
                            </div>
                            <div class="d-flex align-items-center flex-nowrap">
                                <div class="bg-secondary rounded">
                                    <img src="{{ $t->avatar_path ? asset($t->avatar_path) : asset('fruitables/img/testimonial-1.jpg') }}" class="img-fluid rounded" style="width: 100px; height: 100px;" alt="">
                                </div>
                                <div class="ms-4 d-block">
                                    <h4 class="text-dark">{{ $t->author_name }}</h4>
                                    <p class="m-0 pb-3">{{ $t->author_title ?? 'Customer' }}</p>
                                    <div class="d-flex pe-5">
                                        @for($i=1;$i<=5;$i++)
                                            <i class="fas fa-star {{ $i <= ($t->rating ?? 5) ? 'text-primary' : '' }}"></i>
                                        @endfor
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    <!-- Testimonial End -->

    

    <!-- Back to Top -->
    <a href="#" class="btn btn-primary border-3 border-primary rounded-circle back-to-top"><i class="fa fa-arrow-up"></i></a>

@endsection
