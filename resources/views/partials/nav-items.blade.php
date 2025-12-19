@php $dropdown = $dropdown ?? false; @endphp
@foreach($items as $link)
    @php
        // Resolve URL with route existence guard
        if (!empty($link->page_slug)) {
            // allow linking by slug even if page_id not stored
            $url = route('pages.show', $link->page_slug);
        } elseif (!empty($link->route_name) && \Illuminate\Support\Facades\Route::has($link->route_name)) {
            $url = route($link->route_name);
        } else {
            $url = $link->url ?? '#';
        }

        $hasChildren = isset($link->children) && $link->children->isNotEmpty();

        // Determine active state for this item
        $path = trim(parse_url($url, PHP_URL_PATH) ?? '', '/');
        $isActive = request()->fullUrlIs($url)
            || ($path !== '' && (request()->is($path) || request()->is($path.'/*')))
            || (!empty($link->route_name) && request()->routeIs($link->route_name.'*'));

        // If dropdown, mark active when any child is active
        $childActive = false;
        if ($hasChildren) {
            foreach ($link->children as $child) {
                if (!empty($child->page_slug)) {
                    $cUrl = route('pages.show', $child->page_slug);
                } elseif (!empty($child->route_name) && \Illuminate\Support\Facades\Route::has($child->route_name)) {
                    $cUrl = route($child->route_name);
                } else {
                    $cUrl = $child->url ?? '#';
                }
                $cPath = trim(parse_url($cUrl, PHP_URL_PATH) ?? '', '/');
                if (request()->fullUrlIs($cUrl)
                    || ($cPath !== '' && (request()->is($cPath) || request()->is($cPath.'/*')))
                    || (!empty($child->route_name) && request()->routeIs($child->route_name.'*'))
                ) { $childActive = true; break; }
            }
        }
    @endphp

    @if($hasChildren)
        @if(!$dropdown)
            <div class="nav-item dropdown d-flex align-items-center" style="position: relative; z-index: 10000 !important; overflow: visible !important;">
                <a href="{{ $url }}" class="nav-link pe-1 {{ ($isActive || $childActive) ? 'active' : '' }}">{{ $link->label }}</a>
                <a href="#" class="nav-link dropdown-toggle p-0 ms-0" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Toggle dropdown"></a>
                <div class="dropdown-menu rounded-0 m-0" style="z-index: 10000 !important; overflow: visible !important;">
                    @include('partials.nav-items', ['items' => $link->children, 'dropdown' => true])
                </div>
            </div>
        @else
            <h6 class="dropdown-header">{{ $link->label }}</h6>
            @include('partials.nav-items', ['items' => $link->children, 'dropdown' => true])
        @endif
    @else
        @if($dropdown)
            <a href="{{ $url }}" class="dropdown-item">{{ $link->label }}</a>
        @else
            <a href="{{ $url }}" class="nav-item nav-link {{ $isActive ? 'active' : '' }}">{{ $link->label }}</a>
        @endif
    @endif
@endforeach
