@extends('layouts.app')

@section('title', 'Belanja Online - ' . config('app.name'))

@section('meta_description', config('app.name') . ' - Belanja online produk berkualitas dengan harga terjangkau. Temukan berbagai produk kebutuhan sehari-hari Anda.')

@section('meta_keywords', 'belanja online, toko online, produk berkualitas, harga terjangkau, ' . config('app.name') . ', shop, belanja')

@section('og_image', asset('storage/defaults/og-shop.jpg'))

@section('content')
    <style>
      .product-card{border-color:#147440 !important;border-width:1px !important;transition:border-color .2s ease,box-shadow .2s ease,transform .2s ease;border-radius:.75rem}
      .product-card:hover{box-shadow:0 8px 20px rgba(20,116,64,.15),0 2px 6px rgba(0,0,0,.04);transform:translateY(-2px)}
      
      /* Custom Pagination Styling */
      .pagination {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 5px;
        margin-top: 2rem;
        flex-wrap: wrap;
      }
      
      .pagination .rounded {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 40px;
        height: 40px;
        padding: 8px 12px;
        border: 1px solid #137440;
        border-radius: 8px !important;
        background-color: #fff;
        color: #137440;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.3s ease;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
      }
      
      .pagination .rounded:hover {
        background-color: #137440;
        color: #fff;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(19,116,64,0.2);
      }
      
      .pagination .active {
        background-color: #137440 !important;
        color: #fff !important;
        border-color: #137440 !important;
      }
      
      .pagination .disabled {
        opacity: 0.5;
        pointer-events: none;
        background-color: #f8f9fa !important;
        color: #6c757d !important;
        border-color: #dee2e6 !important;
      }
      
      .pagination .disabled:hover {
        transform: none;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
      }
    </style>
    @php
        $isMitra = auth()->check() && method_exists(auth()->user(), 'isMitra') && auth()->user()->isMitra();
    @endphp

    <!-- Single Page Header start -->
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">Shop</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="#">Pages</a></li>
            <li class="breadcrumb-item active text-white">Shop</li>
        </ol>
    </div>
    <!-- Single Page Header End -->

    <!-- Fruits Shop Start-->
    <div class="container-fluid fruite py-5">
        <div class="container py-5">
            <h1 class="mb-4">Fresh fruits shop</h1>
            <div class="row g-4">
                <div class="col-lg-12">
                    <div class="row g-4">
                        <div class="col-xl-3">
                            <div class="input-group w-100 mx-auto d-flex">
                                <input type="search" class="form-control p-3" placeholder="keywords" aria-describedby="search-icon-1">
                                <span id="search-icon-1" class="input-group-text p-3"><i class="fa fa-search"></i></span>
                            </div>
                        </div>
                        <div class="col-6"></div>
                        <div class="col-xl-3">
                            <div class="bg-light ps-3 py-3 rounded d-flex justify-content-between mb-4">
                                <label for="fruits">Default Sorting:</label>
                                <select id="fruits" name="fruitlist" class="border-0 form-select-sm bg-light me-3" form="fruitform">
                                    <option value="volvo">Nothing</option>
                                    <option value="saab">Popularity</option>
                                    <option value="opel">Organic</option>
                                    <option value="audi">Fantastic</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row g-4">
                        <div class="col-lg-3">
                            <div class="row g-4">
                                <div class="col-lg-12">
                                    <div class="mb-3">
                                        <h4>Categories</h4>
                                        <ul class="list-unstyled fruite-categorie">
                                            <li>
                                                <div class="d-flex justify-content-between fruite-name">
                                                    <a href="{{ route('shop') }}" class="{{ empty($categorySlug) ? 'fw-bold' : '' }}">
                                                        <i class="fas fa-apple-alt me-2"></i>All Products
                                                    </a>
                                                </div>
                                            </li>
                                            @foreach($categories as $cat)
                                                @php
                                                    $active = isset($categorySlug) && $categorySlug === $cat->slug;
                                                @endphp
                                                <li>
                                                    <div class="d-flex justify-content-between fruite-name">
                                                        <a href="{{ route('shop', ['category' => $cat->slug]) }}" class="{{ $active ? 'fw-bold text-primary' : '' }}">
                                                            <i class="fas fa-apple-alt me-2"></i>{{ $cat->name }}
                                                        </a>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="mb-3">
                                        <h4 class="mb-2">Price</h4>
                                        <input type="range" class="form-range w-100" id="rangeInput" name="rangeInput" min="0" max="500" value="0" oninput="amount.value=rangeInput.value">
                                        <output id="amount" name="amount" min-velue="0" max-value="500" for="rangeInput">0</output>
                                    </div>
                                </div>
                               
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <div class="row g-4 justify-content-center">
                                @forelse($products as $product)
                                    @php
                                        $image = $product->main_image_path
                                            ? 'storage/'.$product->main_image_path
                                            : 'fruitables/img/fruite-item-1.jpg';
                                        $categoryLabel = 'Product';
                                        $price = $product->price ?? 0;
                                        $detailUrl = $product->slug
                                            ? route('shop.detail', $product->slug)
                                            : '#';
                                        $detailUrl = $product->slug
                                            ? route('shop.detail', $product->slug)
                                            : '#';

                                        // Harga tampilan mitra vs customer (logika cart tetap pakai $price)
                                        $displayPrice = $price;
                                        $originalPrice = null;
                                        if ($isMitra && $price > 0) {
                                            $originalPrice = $price;
                                            $displayPrice = $price * 0.9; // contoh diskon 10% untuk mitra
                                        }
                                    @endphp
                                    <div class="col-md-6 col-lg-6 col-xl-4">
                                        <div class="rounded position-relative fruite-item product-card bg-white border shadow-sm d-flex flex-column h-100">
                                            <div class="fruite-img ratio ratio-4x3">
                                                <a href="{{ $detailUrl }}">
                                                    <img src="{{ asset($image) }}" class="w-100 h-100 rounded-top" style="object-fit: contain;" alt="{{ $product->name }}">
                                                </a>
                                            </div>
                                            <div class="text-white bg-secondary px-3 py-1 rounded position-absolute" style="top: 10px; left: 10px;">{{ $categoryLabel }}</div>
                                            <div class="p-4 rounded-bottom d-flex flex-column flex-grow-1">
                                                <h4><a href="{{ $detailUrl }}" class="text-dark text-decoration-none">{{ $product->name }}</a></h4>
                                                <p class="mb-2">{{ $product->short_description ?? 'Fresh product available for you.' }}</p>
                                                <div class="d-flex justify-content-between flex-lg-wrap mt-auto">
                                                    <p class="text-dark fs-5 fw-bold mb-0">
                                                        Rp. {{ number_format($displayPrice, 2) }} {{ $product->unit ? '/ '.$product->unit : '' }}
                                                        @if($originalPrice)
                                                            <span class="text-muted text-decoration-line-through ms-1">
                                                                Rp. {{ number_format($originalPrice, 2) }}
                                                            </span>
                                                            <span class="badge bg-success ms-1">Mitra</span>
                                                        @endif
                                                    </p>
                                                    <form method="POST" action="{{ route('cart.add') }}">
                                                        @csrf
                                                        <input type="hidden" name="id" value="{{ $product->id }}">
                                                        <input type="hidden" name="name" value="{{ $product->name }}">
                                                        <input type="hidden" name="price" value="{{ $price }}">
                                                        <input type="hidden" name="image" value="{{ $product->main_image_path ? ('storage/'.$product->main_image_path) : '' }}">
                                                        <input type="hidden" name="qty" value="1">
                                                        <button type="submit" class="btn border border-secondary rounded-pill px-3 text-primary"><i class="fa fa-shopping-bag me-2 text-primary"></i> Add to cart</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12 text-center py-4">No products found.</div>
                                @endforelse

                                <div class="col-12">
                                    @if($products->hasPages())
                                        <div class="pagination d-flex justify-content-center mt-5">
                                            {{-- Previous Page Link --}}
                                            @if ($products->onFirstPage())
                                                <span class="rounded px-3 py-1 me-1 disabled">&laquo;</span>
                                            @else
                                                <a href="{{ $products->previousPageUrl() }}" class="rounded px-3 py-1 me-1">&laquo;</a>
                                            @endif

                                            {{-- Page numbers --}}
                                            @for ($page = 1; $page <= $products->lastPage(); $page++)
                                                @if ($page == $products->currentPage())
                                                    <span class="active rounded px-3 py-1 me-1">{{ $page }}</span>
                                                @else
                                                    <a href="{{ $products->url($page) }}" class="rounded px-3 py-1 me-1">{{ $page }}</a>
                                                @endif
                                            @endfor

                                            {{-- Next Page Link --}}
                                            @if ($products->hasMorePages())
                                                <a href="{{ $products->nextPageUrl() }}" class="rounded px-3 py-1 ms-1">&raquo;</a>
                                            @else
                                                <span class="rounded px-3 py-1 ms-1 disabled">&raquo;</span>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Fruits Shop End-->
@endsection
