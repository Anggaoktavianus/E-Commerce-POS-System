@extends('layouts.app')

@section('title', ($product->meta_title ?? $product->name) . ' - ' . config('app.name'))

@section('meta_description', $product->meta_description ?? Str::limit(strip_tags($product->description ?? $product->name), 160))

@section('meta_keywords', $product->meta_keywords ?? str_replace(' ', ', ', $product->name) . ', ' . config('app.name') . ', produk, belanja, harga, detail')

@section('og_image', $product->image ? asset('storage/' . $product->image) : asset('storage/defaults/og-product.jpg'))

@section('content')
    

    <!-- Single Page Header start -->
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">{{ $siteSettings['product_detail_title'] ?? 'Shop Detail' }}</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ $siteSettings['breadcrumb_home_text'] ?? 'Home' }}</a></li>
            <li class="breadcrumb-item"><a href="#">{{ $siteSettings['breadcrumb_pages_text'] ?? 'Pages' }}</a></li>
            <li class="breadcrumb-item active text-white">{{ $siteSettings['breadcrumb_shop_detail_text'] ?? 'Shop Detail' }}</li>
        </ol>
    </div>
    <!-- Single Page Header End -->

    <!-- Single Product Start -->
    <div class="container-fluid py-5 mt-5">
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
                                            <span class="visually-hidden">Previous</span>
                                        </button>
                                        <button class="carousel-control-next" type="button" data-bs-target="#productGallery" data-bs-slide="next">
                                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                            <span class="visually-hidden">Next</span>
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
                            <h4 class="fw-bold mb-3">{{ $product->name }}</h4>
                            <p class="mb-3">{{ $siteSettings['product_category_label'] ?? 'Category' }}: {{ $product->category->name ?? 'Product' }}</p>
                            <h5 class="fw-bold mb-3">{{ $siteSettings['currency_symbol'] ?? 'Rp.' }} {{ number_format($price, 0, ',', '.') }} {{ $product->unit ? '/ '.$product->unit : '' }}</h5>
                            <div class="d-flex mb-4">
                                @for($i=1; $i<=5; $i++)
                                    <i class="fa fa-star {{ $i <= ($product->rating ?? 5) ? 'text-primary' : 'text-secondary' }}"></i>
                                @endfor
                            </div>
                            <p class="mb-4">{{ $product->short_description ?? ($siteSettings['product_default_description'] ?? 'Fresh product from Samsae.') }}</p>
                            <p class="mb-4">{!! nl2br(e($product->description ?? '')) !!}</p>
                            <form method="POST" action="{{ route('cart.add') }}" class="mb-4 d-flex align-items-center gap-3">
                                @csrf
                                <input type="hidden" name="id" value="{{ $product->id }}">
                                <input type="hidden" name="name" value="{{ $product->name }}">
                                <input type="hidden" name="price" value="{{ $price }}">
                                <input type="hidden" name="image" value="{{ $product->main_image_path ? ('storage/'.$product->main_image_path) : '' }}">
                                <input type="number" name="qty" value="1" min="1" class="form-control form-control-sm text-center" style="width: 100px;" placeholder="{{ $siteSettings['quantity_placeholder'] ?? 'Qty' }}">
                                <button type="submit" class="btn border border-secondary rounded-pill px-4 py-2 text-primary"><i class="fa fa-shopping-bag me-2 text-primary"></i> {{ $siteSettings['add_to_cart_text'] ?? 'Add to cart' }}</button>
                            </form>
                        </div>
                        <div class="col-lg-12">
                            <nav>
                                <div class="nav nav-tabs mb-3">
                                    <button class="nav-link active border-white border-bottom-0" type="button" role="tab"
                                        id="nav-about-tab" data-bs-toggle="tab" data-bs-target="#nav-about"
                                        aria-controls="nav-about" aria-selected="true">{{ $siteSettings['product_description_tab'] ?? 'Description' }}</button>
                                    <button class="nav-link border-white border-bottom-0" type="button" role="tab"
                                        id="nav-mission-tab" data-bs-toggle="tab" data-bs-target="#nav-mission"
                                        aria-controls="nav-mission" aria-selected="false">{{ $siteSettings['product_reviews_tab'] ?? 'Reviews' }}</button>
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
                        <div class="border border-primary rounded position-relative vesitable-item">
                            <div class="vesitable-img">
                                <a href="{{ $relUrl }}">
                                    <img src="{{ asset($relImage) }}" class="img-fluid w-100 rounded-top" alt="{{ $rel->name }}">
                                </a>
                            </div>
                            <div class="text-white bg-primary px-3 py-1 rounded position-absolute" style="top: 10px; right: 10px;">Product</div>
                            <div class="p-4 pb-0 rounded-bottom">
                                <h4>{{ $rel->name }}</h4>
                                <p>{{ $rel->short_description ?? 'Fresh product from Samsae.' }}</p>
                                <div class="d-flex justify-content-between flex-lg-wrap">
                                    <p class="text-dark fs-5 fw-bold">Rp. {{ number_format($rel->price ?? 0, 2) }}</p>
                                    <form method="POST" action="{{ route('cart.add') }}">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $rel->id }}">
                                        <input type="hidden" name="name" value="{{ $rel->name }}">
                                        <input type="hidden" name="price" value="{{ $rel->price ?? 0 }}">
                                        <input type="hidden" name="image" value="{{ $rel->main_image_path ? ('storage/'.$rel->main_image_path) : '' }}">
                                        <input type="hidden" name="qty" value="1">
                                        <button type="submit" class="btn border border-secondary rounded-pill px-3 text-primary"><i class="fa fa-shopping-bag me-2 text-primary"></i> Add to cart</button>
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

@endsection
