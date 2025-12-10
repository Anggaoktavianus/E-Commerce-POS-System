<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ShippingController;
use App\Http\Controllers\StoreFrontendController;
use App\Http\Controllers\StoreAdminController;
use App\Http\Controllers\Admin\FeatureController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\CarouselController;
use App\Http\Controllers\Admin\CarouselSlideController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\MenuLinkController;
use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\Admin\FactController;
use App\Http\Controllers\Admin\SocialLinkController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\HomeCollectionController;
use App\Http\Controllers\Admin\HomeCollectionItemController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\ImageUploadController;
use App\Http\Controllers\Auth\MitraRegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\CustomerRegisterController;
use App\Http\Controllers\Admin\MitraController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\MitraDashboardController;
use App\Http\Controllers\CustomerDashboardController;
use App\Http\Controllers\Admin\KategoriArtikelController;
use App\Http\Controllers\Admin\ArtikelController;

Route::get('/', [HomeController::class, 'index'])->name('home');

// Auth: Login & Logout
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Customer Register
Route::get('/register', [CustomerRegisterController::class, 'showRegistrationForm'])->name('customer.register.form');
Route::post('/register', [CustomerRegisterController::class, 'register'])->name('customer.register');

Route::get('/shop', [ShopController::class, 'index'])->name('shop');
Route::get('/shop/{slug}', [ShopController::class, 'show'])->name('shop.detail');
Route::post('/shop/{slug}/reviews', [ShopController::class, 'storeReview'])->name('shop.reviews.store');
// Admin (protected by auth & admin middleware)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function() {
    Route::view('/', 'admin.dashboard')->name('dashboard');
    // Users management (termasuk mitra)
    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::get('users/data', [UserController::class, 'data'])->name('users.data');
    Route::get('users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('users', [UserController::class, 'store'])->name('users.store');
    Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::post('users/{user}/toggle-verify', [UserController::class, 'toggleVerify'])->name('users.toggle_verify');
    
    // Mitras management
    Route::get('mitras', [MitraController::class, 'index'])->name('mitras.index');
    Route::post('mitras/{mitra}/toggle-verify', [MitraController::class, 'toggleVerify'])->name('mitras.toggle_verify');
    
    // Features
    Route::get('features', [FeatureController::class, 'index'])->name('features.index');
    Route::get('features/data', [FeatureController::class, 'data'])->name('features.data');
    Route::get('features/create', [FeatureController::class, 'create'])->name('features.create');
    Route::post('features', [FeatureController::class, 'store'])->name('features.store');
    Route::get('features/{feature}/edit', [FeatureController::class, 'edit'])->name('features.edit');
    Route::put('features/{feature}', [FeatureController::class, 'update'])->name('features.update');
    Route::delete('features/{feature}', [FeatureController::class, 'destroy'])->name('features.destroy');

    // Banners
    Route::get('banners', [BannerController::class, 'index'])->name('banners.index');
    Route::get('banners/data', [BannerController::class, 'data'])->name('banners.data');
    Route::get('banners/create', [BannerController::class, 'create'])->name('banners.create');
    Route::post('banners', [BannerController::class, 'store'])->name('banners.store');
    Route::get('banners/{banner}/edit', [BannerController::class, 'edit'])->name('banners.edit');
    Route::put('banners/{banner}', [BannerController::class, 'update'])->name('banners.update');
    Route::delete('banners/{banner}', [BannerController::class, 'destroy'])->name('banners.destroy');

    // Carousels
    Route::get('carousels', [CarouselController::class, 'index'])->name('carousels.index');
    Route::get('carousels/data', [CarouselController::class, 'data'])->name('carousels.data');
    Route::get('carousels/create', [CarouselController::class, 'create'])->name('carousels.create');
    Route::post('carousels', [CarouselController::class, 'store'])->name('carousels.store');
    Route::get('carousels/{carousel}/edit', [CarouselController::class, 'edit'])->name('carousels.edit');
    Route::put('carousels/{carousel}', [CarouselController::class, 'update'])->name('carousels.update');
    Route::delete('carousels/{carousel}', [CarouselController::class, 'destroy'])->name('carousels.destroy');

    // Slides
    Route::get('carousels/{carousel}/slides', [CarouselSlideController::class, 'index'])->name('slides.index');
    Route::get('carousels/{carousel}/slides/data', [CarouselSlideController::class, 'data'])->name('slides.data');
    Route::get('carousels/{carousel}/slides/create', [CarouselSlideController::class, 'create'])->name('slides.create');
    Route::post('carousels/{carousel}/slides', [CarouselSlideController::class, 'store'])->name('slides.store');
    Route::get('slides/{slide}/edit', [CarouselSlideController::class, 'edit'])->name('slides.edit');
    Route::put('slides/{slide}', [CarouselSlideController::class, 'update'])->name('slides.update');
    Route::delete('slides/{slide}', [CarouselSlideController::class, 'destroy'])->name('slides.destroy');

    // Menus
    Route::get('menus', [MenuController::class, 'index'])->name('menus.index');
    Route::get('menus/data', [MenuController::class, 'data'])->name('menus.data');
    Route::get('menus/create', [MenuController::class, 'create'])->name('menus.create');
    Route::post('menus', [MenuController::class, 'store'])->name('menus.store');
    Route::get('menus/{menu}/edit', [MenuController::class, 'edit'])->name('menus.edit');
    Route::put('menus/{menu}', [MenuController::class, 'update'])->name('menus.update');
    Route::delete('menus/{menu}', [MenuController::class, 'destroy'])->name('menus.destroy');

    // Menu Links
    Route::get('menus/{menu}/links', [MenuLinkController::class, 'index'])->name('links.index');
    Route::get('menus/{menu}/links/data', [MenuLinkController::class, 'data'])->name('links.data');
    Route::get('menus/{menu}/links/create', [MenuLinkController::class, 'create'])->name('links.create');
    Route::post('menus/{menu}/links', [MenuLinkController::class, 'store'])->name('links.store');
    Route::get('links/{link}/edit', [MenuLinkController::class, 'edit'])->name('links.edit');
    Route::put('links/{link}', [MenuLinkController::class, 'update'])->name('links.update');
    Route::post('links/order', [MenuLinkController::class, 'updateOrder'])->name('links.order');
    Route::delete('links/{link}', [MenuLinkController::class, 'destroy'])->name('links.destroy');

    // Orders
    Route::get('orders', [\App\Http\Controllers\Admin\OrderController::class, 'index'])->name('orders.index');
    Route::get('orders/data', [\App\Http\Controllers\Admin\OrderController::class, 'data'])->name('orders.data');
    Route::get('orders/{order}', [\App\Http\Controllers\Admin\OrderController::class, 'show'])->name('orders.show');
    Route::post('orders/{order}/refresh-status', [\App\Http\Controllers\Admin\OrderController::class, 'refreshStatus'])->name('orders.refresh-status');

    // Testimonials
    Route::get('testimonials', [TestimonialController::class, 'index'])->name('testimonials.index');
    Route::get('testimonials/data', [TestimonialController::class, 'data'])->name('testimonials.data');
    Route::get('testimonials/create', [TestimonialController::class, 'create'])->name('testimonials.create');
    Route::post('testimonials', [TestimonialController::class, 'store'])->name('testimonials.store');
    Route::get('testimonials/{testimonial}/edit', [TestimonialController::class, 'edit'])->name('testimonials.edit');
    Route::put('testimonials/{testimonial}', [TestimonialController::class, 'update'])->name('testimonials.update');
    Route::delete('testimonials/{testimonial}', [TestimonialController::class, 'destroy'])->name('testimonials.destroy');

    // Facts
    Route::get('facts', [FactController::class, 'index'])->name('facts.index');
    Route::get('facts/data', [FactController::class, 'data'])->name('facts.data');
    Route::get('facts/create', [FactController::class, 'create'])->name('facts.create');
    Route::post('facts', [FactController::class, 'store'])->name('facts.store');
    Route::get('facts/{fact}/edit', [FactController::class, 'edit'])->name('facts.edit');
    Route::put('facts/{fact}', [FactController::class, 'update'])->name('facts.update');
    Route::delete('facts/{fact}', [FactController::class, 'destroy'])->name('facts.destroy');

    // Social Links
    Route::get('social-links', [SocialLinkController::class, 'index'])->name('social_links.index');
    Route::get('social-links/data', [SocialLinkController::class, 'data'])->name('social_links.data');
    Route::get('social-links/create', [SocialLinkController::class, 'create'])->name('social_links.create');
    Route::post('social-links', [SocialLinkController::class, 'store'])->name('social_links.store');
    Route::get('social-links/{social_link}/edit', [SocialLinkController::class, 'edit'])->name('social_links.edit');
    Route::put('social-links/{social_link}', [SocialLinkController::class, 'update'])->name('social_links.update');
    Route::delete('social-links/{social_link}', [SocialLinkController::class, 'destroy'])->name('social_links.destroy');

    // Settings
    Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
    Route::get('settings/logo', [SettingController::class, 'logo'])->name('settings.logo');
    Route::post('settings/deleteLogo', [SettingController::class, 'deleteLogo'])->name('settings.deleteLogo');
    Route::get('settings/data', [SettingController::class, 'data'])->name('settings.data');
    Route::get('settings/create', [SettingController::class, 'create'])->name('settings.create');
    Route::post('settings', [SettingController::class, 'store'])->name('settings.store');
    Route::get('settings/{setting}/edit', [SettingController::class, 'edit'])->name('settings.edit');
    Route::put('settings/{setting}', [SettingController::class, 'update'])->name('settings.update');
    Route::delete('settings/{setting}', [SettingController::class, 'destroy'])->name('settings.destroy');

    // Categories
    Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('categories/data', [CategoryController::class, 'data'])->name('categories.data');
    Route::get('categories/create', [CategoryController::class, 'create'])->name('categories.create');
    Route::post('categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::put('categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    // Products
    Route::get('products', [ProductController::class, 'index'])->name('products.index');
    Route::get('products/data', [ProductController::class, 'data'])->name('products.data');
    Route::get('products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('products', [ProductController::class, 'store'])->name('products.store');
    Route::get('products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

    // Shipping Management
    Route::get('shipping', [App\Http\Controllers\Admin\ShippingController::class, 'index'])->name('shipping.index');
    
    // Shipping Methods
    Route::get('shipping/methods', [App\Http\Controllers\Admin\ShippingController::class, 'methodsIndex'])->name('shipping.methods');
    Route::get('shipping/methods/data', [App\Http\Controllers\Admin\ShippingController::class, 'methodsData'])->name('shipping.methods.data');
    Route::get('shipping/methods/create', [App\Http\Controllers\Admin\ShippingController::class, 'methodsCreate'])->name('shipping.methods.create');
    Route::post('shipping/methods', [App\Http\Controllers\Admin\ShippingController::class, 'methodsStore'])->name('shipping.methods.store');
    Route::get('shipping/methods/{method}/edit', [App\Http\Controllers\Admin\ShippingController::class, 'methodsEdit'])->name('shipping.methods.edit');
    Route::put('shipping/methods/{method}', [App\Http\Controllers\Admin\ShippingController::class, 'methodsUpdate'])->name('shipping.methods.update');
    Route::delete('shipping/methods/{method}', [App\Http\Controllers\Admin\ShippingController::class, 'methodsDestroy'])->name('shipping.methods.destroy');
    
    // Shipping Costs
    Route::get('shipping/costs', [App\Http\Controllers\Admin\ShippingController::class, 'costsIndex'])->name('shipping.costs');
    Route::get('shipping/costs/data', [App\Http\Controllers\Admin\ShippingController::class, 'costsData'])->name('shipping.costs.data');
    Route::get('shipping/costs/create', [App\Http\Controllers\Admin\ShippingController::class, 'costsCreate'])->name('shipping.costs.create');
    Route::post('shipping/costs', [App\Http\Controllers\Admin\ShippingController::class, 'costsStore'])->name('shipping.costs.store');
    Route::get('shipping/costs/{cost}/edit', [App\Http\Controllers\Admin\ShippingController::class, 'costsEdit'])->name('shipping.costs.edit');
    Route::put('shipping/costs/{cost}', [App\Http\Controllers\Admin\ShippingController::class, 'costsUpdate'])->name('shipping.costs.update');
    Route::delete('shipping/costs/{cost}', [App\Http\Controllers\Admin\ShippingController::class, 'costsDestroy'])->name('shipping.costs.destroy');

    // Store Management
    Route::get('stores', [App\Http\Controllers\Admin\StoreController::class, 'index'])->name('stores.index');
    
    // Stores CRUD
    Route::get('stores/stores', [App\Http\Controllers\Admin\StoreController::class, 'storesIndex'])->name('stores.stores');
    Route::get('stores/stores/data', [App\Http\Controllers\Admin\StoreController::class, 'storesData'])->name('stores.stores.data');
    Route::get('stores/stores/create', [App\Http\Controllers\Admin\StoreController::class, 'storesCreate'])->name('stores.stores.create');
    Route::post('stores/stores', [App\Http\Controllers\Admin\StoreController::class, 'storesStore'])->name('stores.stores.store');
    Route::get('stores/stores/{store}/show', [App\Http\Controllers\Admin\StoreController::class, 'storesShow'])->name('stores.show');
    Route::get('stores/stores/{store}/edit', [App\Http\Controllers\Admin\StoreController::class, 'storesEdit'])->name('stores.stores.edit');
    Route::put('stores/stores/{store}', [App\Http\Controllers\Admin\StoreController::class, 'storesUpdate'])->name('stores.stores.update');
    Route::delete('stores/stores/{store}', [App\Http\Controllers\Admin\StoreController::class, 'storesDestroy'])->name('stores.stores.destroy');
    
    // Outlets CRUD
    Route::get('outlets', [App\Http\Controllers\Admin\OutletController::class, 'index'])->name('outlets.index');
    Route::get('outlets/data', [App\Http\Controllers\Admin\OutletController::class, 'data'])->name('outlets.data');
    Route::get('outlets/create', [App\Http\Controllers\Admin\OutletController::class, 'create'])->name('outlets.create');
    Route::post('outlets', [App\Http\Controllers\Admin\OutletController::class, 'store'])->name('outlets.store');
    Route::get('outlets/{outlet}/edit', [App\Http\Controllers\Admin\OutletController::class, 'edit'])->name('outlets.edit');
    Route::put('outlets/{outlet}', [App\Http\Controllers\Admin\OutletController::class, 'update'])->name('outlets.update');
    Route::delete('outlets/{outlet}', [App\Http\Controllers\Admin\OutletController::class, 'destroy'])->name('outlets.destroy');
    Route::get('outlets/by-store/{storeId}', [App\Http\Controllers\Admin\OutletController::class, 'getOutletsByStore'])->name('outlets.by-store');

    // Home Collections
    Route::get('collections', [HomeCollectionController::class, 'index'])->name('collections.index');
    Route::get('collections/data', [HomeCollectionController::class, 'data'])->name('collections.data');
    Route::get('collections/create', [HomeCollectionController::class, 'create'])->name('collections.create');
    Route::post('collections', [HomeCollectionController::class, 'store'])->name('collections.store');
    Route::get('collections/{collection}/edit', [HomeCollectionController::class, 'edit'])->name('collections.edit');
    Route::put('collections/{collection}', [HomeCollectionController::class, 'update'])->name('collections.update');
    Route::delete('collections/{collection}', [HomeCollectionController::class, 'destroy'])->name('collections.destroy');

    // Collection Items
    Route::get('collections/{collection}/items', [HomeCollectionItemController::class, 'index'])->name('collection_items.index');
    Route::get('collections/{collection}/items/data', [HomeCollectionItemController::class, 'data'])->name('collection_items.data');
    Route::get('collections/{collection}/items/create', [HomeCollectionItemController::class, 'create'])->name('collection_items.create');
    Route::post('collections/{collection}/items', [HomeCollectionItemController::class, 'store'])->name('collection_items.store');
    Route::get('collection-items/{item}/edit', [HomeCollectionItemController::class, 'edit'])->name('collection_items.edit');
    Route::put('collection-items/{item}', [HomeCollectionItemController::class, 'update'])->name('collection_items.update');
    Route::delete('collection-items/{item}', [HomeCollectionItemController::class, 'destroy'])->name('collection_items.destroy');
    
    // Pages
    Route::resource('pages', PageController::class)->except(['show']);
    Route::get('pages/data', [PageController::class, 'getData'])->name('pages.data');
    
    // Image Upload for Editor
    Route::post('upload-image', [ImageUploadController::class, 'upload'])->name('upload.image');
    Route::post('delete-image', [ImageUploadController::class, 'delete'])->name('delete.image');
    
    // Kategori Artikel
    Route::get('kategori-artikel', [KategoriArtikelController::class, 'index'])->name('kategori_artikel.index');
    Route::get('kategori-artikel/data', [KategoriArtikelController::class, 'data'])->name('kategori_artikel.data');
    Route::get('kategori-artikel/create', [KategoriArtikelController::class, 'create'])->name('kategori_artikel.create');
    Route::post('kategori-artikel', [KategoriArtikelController::class, 'store'])->name('kategori_artikel.store');
    Route::get('kategori-artikel/{kategoriArtikel}/edit', [KategoriArtikelController::class, 'edit'])->name('kategori_artikel.edit');
    Route::put('kategori-artikel/{kategoriArtikel}', [KategoriArtikelController::class, 'update'])->name('kategori_artikel.update');
    Route::delete('kategori-artikel/{kategoriArtikel}', [KategoriArtikelController::class, 'destroy'])->name('kategori_artikel.destroy');
    
    // Artikel
    Route::get('artikel', [ArtikelController::class, 'index'])->name('artikel.index');
    Route::get('artikel/data', [ArtikelController::class, 'data'])->name('artikel.data');
    Route::get('artikel/create', [ArtikelController::class, 'create'])->name('artikel.create');
    Route::post('artikel', [ArtikelController::class, 'store'])->name('artikel.store');
    Route::get('artikel/{artikel}/edit', [ArtikelController::class, 'edit'])->name('artikel.edit');
    Route::put('artikel/{artikel}', [ArtikelController::class, 'update'])->name('artikel.update');
    Route::delete('artikel/{artikel}', [ArtikelController::class, 'destroy'])->name('artikel.destroy');
    Route::get('artikel/{artikel}', [ArtikelController::class, 'show'])->name('artikel.show');
});

// Location API routes (restricted provinces handled in controller)
Route::prefix('api/locations')->name('api.locations.')->group(function(){
    Route::get('/provinsis', [\App\Http\Controllers\Api\LocationController::class, 'provinsis'])->name('provinsis');
    Route::get('/kabkotas/{provinsiId}', [\App\Http\Controllers\Api\LocationController::class, 'kabkotas'])->name('kabkotas');
    Route::get('/kecamatans/{kabkotaId}', [\App\Http\Controllers\Api\LocationController::class, 'kecamatans'])->name('kecamatans');
    Route::get('/desas/{kecamatanId}', [\App\Http\Controllers\Api\LocationController::class, 'desas'])->name('desas');
    Route::get('/schema', [\App\Http\Controllers\Api\LocationController::class, 'schema'])->name('schema');
});

// Public API to fetch outlets by store (used by checkout UI)
Route::get('/api/outlets/by-store/{storeId}', [App\Http\Controllers\Admin\OutletController::class, 'getOutletsByStore'])->name('api.outlets.by-store');

// Public page route (must be outside admin group)
Route::get('pages/{slug}', [PageController::class, 'show'])->name('pages.show');

// Public Artikel routes
Route::get('/artikel', [\App\Http\Controllers\ArtikelController::class, 'publicIndex'])->name('artikel.index');
Route::get('/artikel/{slug}', [\App\Http\Controllers\ArtikelController::class, 'publicShow'])->name('artikel.show');
Route::post('/artikel/{id}/increment-views', [\App\Http\Controllers\ArtikelController::class, 'incrementViews'])->name('artikel.increment-views');
// Cart routes
Route::get('/cart', [CartController::class, 'index'])->name('cart');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
Route::post('/cart/coupon', [CartController::class, 'applyCoupon'])->name('cart.coupon');

// Checkout and Payment Routes
Route::middleware('auth')->group(function () {
    Route::get('/checkout', [\App\Http\Controllers\CheckoutController::class, 'index'])->name('checkout');
    Route::post('/checkout/process', [\App\Http\Controllers\CheckoutController::class, 'process'])->name('checkout.process');
});

// Payment finish (no auth required - for Midtrans redirect)
Route::get('/payment/finish', [\App\Http\Controllers\CheckoutController::class, 'finish'])->name('payment.finish');

// Midtrans notification (no auth required)
Route::post('/midtrans/notification', [\App\Http\Controllers\CheckoutController::class, 'notification'])->name('midtrans.notification');

Route::view('/testimonial', 'pages.testimonial')->name('testimonial');
Route::view('/contact', 'pages.contact')->name('contact');

Route::fallback(function () {
    return response()->view('pages.notfound', [], 404);
});

// Halaman pendaftaran & dashboard mitra
Route::get('/register/mitra', [MitraRegisterController::class, 'showRegistrationForm'])->name('mitra.register.form');
Route::post('/register/mitra', [MitraRegisterController::class, 'register'])->name('mitra.register');

Route::middleware(['auth', 'mitra'])->prefix('mitra')->name('mitra.')->group(function () {
    Route::get('/dashboard', [MitraDashboardController::class, 'index'])->name('dashboard');
    Route::get('/orders/datatables', [MitraDashboardController::class, 'datatables'])->name('orders.datatables');
});

// Customer Dashboard
Route::middleware(['auth'])->prefix('customer')->name('customer.')->group(function () {
    Route::get('/dashboard', [CustomerDashboardController::class, 'index'])->name('dashboard');
    Route::get('/orders/datatables', [CustomerDashboardController::class, 'datatables'])->name('orders.datatables');
});

// Test OrderController
Route::get('/test-order', [OrderController::class, 'test']);

// Simple Test Routes for Debugging
Route::get('/test-subdomain', function() {
    return response()->json([
        'message' => 'Simple test works!',
        'host' => request()->getHost(),
        'stores_count' => \App\Models\Store::count(),
        'stores' => \App\Models\Store::pluck('name', 'code'),
    ]);
});

Route::get('/test-subdomain/{store}', function($store) {
    $storeModel = \App\Models\Store::where('code', $store)->first();
    
    return response()->json([
        'subdomain' => $store,
        'host' => request()->getHost(),
        'store_found' => $storeModel ? true : false,
        'store' => $storeModel,
        'message' => $storeModel ? 'Store found!' : 'Store not found'
    ]);
});

// Multi-Tenant Store Frontend Routes (disabled)
/*
Route::prefix('store/{store}')->middleware('store.context')->group(function() {
    // Frontend Routes
    Route::get('/', [StoreFrontendController::class, 'home'])->name('store.home');
    Route::get('/products', [StoreFrontendController::class, 'products'])->name('store.products');
    Route::get('/product/{slug}', [StoreFrontendController::class, 'productDetail'])->name('store.product.detail');
    Route::get('/about', [StoreFrontendController::class, 'about'])->name('store.about');
    Route::get('/contact', [StoreFrontendController::class, 'contact'])->name('store.contact');
    Route::get('/cart', [StoreFrontendController::class, 'cart'])->name('store.cart');
    Route::get('/checkout', [StoreFrontendController::class, 'checkout'])->name('store.checkout');
    
    // Store Admin Routes
    Route::prefix('admin')->name('store.admin.')->group(function() {
        Route::get('/dashboard', [StoreAdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/products', [StoreAdminController::class, 'products'])->name('products');
        Route::get('/categories', [StoreAdminController::class, 'categories'])->name('categories');
        Route::get('/orders', [StoreAdminController::class, 'orders'])->name('orders');
        Route::get('/outlets', [StoreAdminController::class, 'outlets'])->name('outlets');
        Route::get('/settings', [StoreAdminController::class, 'settings'])->name('settings');
        Route::post('/settings', [StoreAdminController::class, 'updateSettings'])->name('settings.update');
        Route::get('/analytics', [StoreAdminController::class, 'analytics'])->name('analytics');
    });
});
*/

// Subdomain Test Routes (Requires hosts file)
Route::domain('{store}.samsae.test')->group(function() {
    Route::get('/', function($store) {
        $storeModel = \App\Models\Store::where('code', $store)->first();
        
        return response()->json([
            'subdomain' => $store,
            'host' => request()->getHost(),
            'url' => request()->fullUrl(),
            'store_found' => $storeModel ? true : false,
            'store' => $storeModel,
            'message' => 'Real subdomain routing works!'
        ]);
    });
});

// Shipping API routes
Route::prefix('api/shipping')->name('api.shipping.')->group(function () {
    Route::get('/methods', [ShippingController::class, 'getAvailableMethods'])->name('methods');
    Route::post('/calculate', [ShippingController::class, 'calculateCost'])->name('calculate');
    Route::post('/check-availability', [ShippingController::class, 'checkAvailability'])->name('check');
    Route::get('/method/{methodId}', [ShippingController::class, 'getMethodDetails'])->name('method');
    Route::get('/cities', [ShippingController::class, 'getSupportedCities'])->name('cities');
    Route::get('/instant-cities', [ShippingController::class, 'getInstantDeliveryCities'])->name('instant-cities');
    Route::post('/validate-fresh', [ShippingController::class, 'validateFreshProductShipping'])->name('validate-fresh');
});

// Order Actions (accessible by both mitra and customer)
Route::middleware(['auth'])->prefix('orders')->name('orders.')->group(function () {
    Route::get('/test', [OrderController::class, 'test'])->name('test');
    Route::get('/{orderNumber}', [OrderController::class, 'show'])->name('show');
    Route::get('/{orderNumber}/track', [OrderController::class, 'track'])->name('track');
    Route::get('/{orderNumber}/invoice', [OrderController::class, 'invoice'])->name('invoice');
    Route::post('/{orderNumber}/cancel', [OrderController::class, 'cancel'])->name('cancel');
    Route::post('/{orderNumber}/reorder', [OrderController::class, 'reorder'])->name('reorder');
});
