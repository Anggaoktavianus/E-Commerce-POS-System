@php
    // Default breadcrumb items
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
