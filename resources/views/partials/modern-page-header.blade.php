@php
    // Get page title
    $pageTitle = $pageTitle ?? ($siteSettings['shop_page_title'] ?? ($siteSettings['brand_name'] ?? 'Halaman'));
    
    // Build breadcrumb items
    $breadcrumbItems = $breadcrumbItems ?? [];
    
    // If empty, create default based on current route
    if (empty($breadcrumbItems)) {
        $breadcrumbItems = [
            ['label' => 'Beranda', 'url' => url('/')]
        ];
        
        // Add page-specific breadcrumb
        $routeName = request()->route()->getName();
        switch($routeName) {
            case 'shop':
                $breadcrumbItems[] = ['label' => 'Toko', 'url' => route('shop')];
                break;
            case 'cart':
                $breadcrumbItems[] = ['label' => 'Toko', 'url' => route('shop')];
                $breadcrumbItems[] = ['label' => 'Keranjang', 'url' => null];
                break;
            case 'checkout':
                $breadcrumbItems[] = ['label' => 'Toko', 'url' => route('shop')];
                $breadcrumbItems[] = ['label' => 'Keranjang', 'url' => route('cart')];
                $breadcrumbItems[] = ['label' => 'Checkout', 'url' => null];
                break;
            case 'contact':
                $breadcrumbItems[] = ['label' => 'Kontak', 'url' => null];
                break;
            case 'pages.show':
                $breadcrumbItems[] = ['label' => 'Halaman', 'url' => null];
                if (isset($page)) {
                    $breadcrumbItems[] = ['label' => $page->title, 'url' => null];
                }
                break;
            case 'shop.detail':
                $breadcrumbItems[] = ['label' => 'Toko', 'url' => route('shop')];
                if (isset($product)) {
                    $breadcrumbItems[] = ['label' => $product->name, 'url' => null];
                }
                break;
            default:
                // Generic fallback
                break;
        }
    }
@endphp

<style>
    .modern-page-header {
        background: linear-gradient(135deg, #147440 0%, #20c997 100%);
        padding: 6rem 0 3rem;
        position: relative;
        overflow: visible !important;
        margin-top: 0;
        margin-bottom: 0;
        z-index: 1 !important;
    }
    
    .modern-page-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse"><path d="M 40 0 L 0 0 0 40" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="1"/></pattern></defs><rect width="100%" height="100%" fill="url(%23grid)"/></svg>');
        opacity: 0.3;
        z-index: 0 !important;
    }
    
    .modern-page-header .container {
        position: relative;
        z-index: 1;
    }
    
    @media (min-width: 992px) {
        .modern-page-header {
            padding-top: 7rem;
        }
    }
    
    @media (max-width: 991.98px) {
        .modern-page-header {
            padding-top: 5.5rem;
        }
    }
    
    @media (max-width: 767.98px) {
        .modern-page-header {
            padding-top: 4.5rem;
        }
    }
    
    .page-title {
        font-size: 3rem;
        font-weight: 800;
        color: white;
        margin-bottom: 1rem;
        text-shadow: 0 2px 10px rgba(0,0,0,0.2);
        letter-spacing: -0.02em;
    }
    
    @media (max-width: 767.98px) {
        .page-title {
            font-size: 2rem;
        }
    }
    
    .modern-breadcrumb {
        background: rgba(255,255,255,0.15);
        backdrop-filter: blur(10px);
        border-radius: 30px;
        padding: 0.3rem 1rem;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        font-size: 0.8rem;
    }
    
    .modern-breadcrumb a {
        color: white;
        text-decoration: none;
        transition: opacity 0.3s;
        font-size: 0.8rem;
    }
    
    .modern-breadcrumb a:hover {
        opacity: 0.8;
    }
    
    .modern-breadcrumb .separator {
        color: rgba(255,255,255,0.7);
        font-size: 0.8rem;
    }
</style>

<div class="container-fluid modern-page-header" style="margin-top: 140px; padding-top: 5rem; padding-bottom: 3rem;">
    <div class="container">
        <h1 class="page-title text-center">{{ $pageTitle }}</h1>
        <div class="text-center mt-4">
            <nav class="modern-breadcrumb">
                @foreach($breadcrumbItems as $index => $item)
                    @if($item['url'] && !$loop->last)
                        <a href="{{ $item['url'] }}">{{ $item['label'] }}</a>
                    @else
                        <span class="text-white">{{ $item['label'] }}</span>
                    @endif
                    @if(!$loop->last)
                        <span class="separator">/</span>
                    @endif
                @endforeach
            </nav>
        </div>
    </div>
</div>
