<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Permissions Policy for Geolocation (set via HTTP header, meta tag as fallback) -->
    <meta http-equiv="Permissions-Policy" content="geolocation=(self)">
    
    <!-- SEO Meta Tags -->
    <title>@yield('title', config('app.name', 'Samsae Store'))</title>
    
    <!-- Meta Description -->
    @hasSection('meta_description')
        <meta name="description" content="@yield('meta_description')">
    @else
        <meta name="description" content="{{ config('app.name') }} - Toko online terpercaya untuk kebutuhan sehari-hari Anda. Produk berkualitas dengan harga terjangkau.">
    @endif
    
    <!-- Meta Keywords -->
    @hasSection('meta_keywords')
        <meta name="keywords" content="@yield('meta_keywords')">
    @else
        <meta name="keywords" content="toko online, belanja online, produk berkualitas, harga terjangkau, {{ config('app.name') }}">
    @endif
    
    <!-- Open Graph Meta Tags for Social Media -->
    <meta property="og:title" content="@yield('title', config('app.name', 'Samsae Store'))">
    @hasSection('meta_description')
        <meta property="og:description" content="@yield('meta_description')">
    @else
        <meta property="og:description" content="{{ config('app.name') }} - Toko online terpercaya untuk kebutuhan sehari-hari Anda.">
    @endif
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    @hasSection('og_image')
        <meta property="og:image" content="@yield('og_image')">
    @else
        <meta property="og:image" content="{{ asset('storage/uploads/settings/pA4eU0uxj49qQm9buxJyHQVoP8u8u6MyfjDbSNic.png') }}">
    @endif
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:site_name" content="{{ config('app.name', 'Samsae Store') }}">
    
    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('title', config('app.name', 'Samsae Store'))">
    @hasSection('meta_description')
        <meta name="twitter:description" content="@yield('meta_description')">
    @else
        <meta name="twitter:description" content="{{ config('app.name') }} - Toko online terpercaya untuk kebutuhan sehari-hari Anda.">
    @endif
    @hasSection('og_image')
        <meta name="twitter:image" content="@yield('og_image')"> 
    @else
        <meta name="twitter:image" content="{{ asset('storage/uploads/settings/pA4eU0uxj49qQm9buxJyHQVoP8u8u6MyfjDbSNic.png') }}">
    @endif
    
    <!-- Canonical URL -->
    <link rel="canonical" href="{{ url()->current() }}">
    
    <!-- Additional Meta Tags -->
    <meta name="robots" content="index, follow">
    <meta name="googlebot" content="index, follow">
    <meta name="author" content="{{ config('app.name', 'Samsae Store') }}">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" href="{{ asset('storage/uploads/settings/pA4eU0uxj49qQm9buxJyHQVoP8u8u6MyfjDbSNic.png') }}">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

    <link href="{{ asset('fruitables/lib/lightbox/css/lightbox.min.css') }}" rel="stylesheet">
    <link href="{{ asset('fruitables/lib/owlcarousel/assets/owl.carousel.min.css') }}" rel="stylesheet">

    <link href="{{ asset('fruitables/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('fruitables/css/style.css') }}" rel="stylesheet">
    <!-- Select2 global CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    
    <!-- Enhanced Navbar CSS -->
    <style>
        .fixed-top {
            transition: all 0.3s ease;
            background: transparent !important;
            border: 0;
            z-index: 1030;
        }
        
        .fixed-top.shadow {
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: saturate(180%) blur(12px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
        
        /* Ensure navbar stays white when scrolled */
        .fixed-top.shadow .navbar {
            background: transparent !important;
        }
        
        /* Smooth transition for all navbar elements */
        .fixed-top * {
            transition: color 0.3s ease, background-color 0.3s ease;
        }
        
        /* Prevent horizontal overflow */
        html, body {
          overflow-x: hidden;
          max-width: 100%;
          width: 100%;
        }
        * {
          box-sizing: border-box;
        }
        .container-fluid {
          overflow-x: hidden;
          max-width: 100%;
        }
        .row {
          margin-left: 0;
          margin-right: 0;
        }
        .row > [class*="col-"] {
          padding-left: 0.75rem;
          padding-right: 0.75rem;
        }
        /* Fix body padding for fixed navbar */
        body {
            padding-top: 0 !important;
        }
        
        /* Ensure navbar and dropdowns are above all content */
        .fixed-top {
            z-index: 9999 !important;
        }
        .navbar {
            z-index: 9999 !important;
            position: relative !important;
        }
        .navbar-collapse {
            z-index: 9999 !important;
        }
        .dropdown {
            z-index: 10000 !important;
            position: relative !important;
        }
        .dropdown-menu {
            z-index: 10000 !important;
        }
        /* Ensure all dropdowns are visible */
        .navbar .dropdown-menu {
            z-index: 10000 !important;
            position: absolute !important;
            margin-top: 0 !important;
        }
        /* Fix for nested dropdowns in navigation */
        .navbar-nav .dropdown-menu {
            z-index: 10000 !important;
            position: absolute !important;
        }
        /* Ensure dropdown items are clickable */
        .dropdown-item {
            position: relative !important;
            z-index: 10001 !important;
        }
        /* Prevent content from covering dropdowns */
        .modern-page-header,
        .page-header,
        .container-fluid:not(.fixed-top) {
            z-index: 1 !important;
            position: relative !important;
            overflow: visible !important;
        }
        /* Ensure no overflow hidden on navbar */
        .navbar,
        .navbar-collapse,
        .navbar-nav,
        .navbar-nav .nav-item {
            overflow: visible !important;
        }
        /* Ensure dropdown can extend beyond container */
        .container-fluid.fixed-top {
            overflow: visible !important;
        }
        .container-fluid.fixed-top .container {
            overflow: visible !important;
        }
        /* Force dropdown visibility when shown */
        .dropdown.show > .dropdown-menu {
            z-index: 10000 !important;
        }
        
        /* Remove our custom padding adjustments since fruitables handles it */
        .page-header,
        .hero-header {
            /* Let fruitables CSS handle this */
        }

        /* Select2 match Bootstrap form-control */
        .select2-container { width: 100% !important; }
        .select2-container .select2-selection--single {
            height: calc(2.375rem + 2px) !important;
            border: 1px solid #ced4da !important;
            border-radius: 0.375rem !important;
            display: flex !important;
            align-items: center !important;
            padding: 0 0.75rem !important;
            background-color: #fff !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #6c757d !important;
            padding-left: 0 !important;
            line-height: 1.6 !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__placeholder {
            color: #6c757d !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 100% !important;
            right: 0.5rem !important;
        }
        .select2-container--default.select2-container--focus .select2-selection--single,
        .select2-container--default .select2-selection--single:focus {
            border-color: #86b7fe !important;
            box-shadow: 0 0 0 .25rem rgba(13,110,253,.25) !important;
            outline: 0 !important;
        }
        .select2-dropdown { border-color: #ced4da !important; }
        .select2-results__option { padding: 0.375rem 0.75rem !important; }
    </style>
    
    @stack('styles')
</head>
<body>
    @include('partials.header')

    @yield('content')

    @include('partials.footer')

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Select2 global JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('fruitables/lib/easing/easing.min.js') }}"></script>
    <script src="{{ asset('fruitables/lib/waypoints/waypoints.min.js') }}"></script>
    <script src="{{ asset('fruitables/lib/owlcarousel/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('fruitables/lib/lightbox/js/lightbox.min.js') }}"></script>
    <script src="{{ asset('fruitables/js/main.js') }}"></script>
    
    <!-- Enhanced Navbar Scroll Behavior -->
    <script>
        $(document).ready(function() {
            // Enhanced fixed navbar on scroll
            $(window).scroll(function() {
                var scrollTop = $(this).scrollTop();
                var navbar = $('.fixed-top');
                
                if (scrollTop > 50) {
                    navbar.addClass('shadow').css({
                        'background': 'rgba(255, 255, 255, 0.95)',
                        'backdrop-filter': 'saturate(180%) blur(12px)',
                        'box-shadow': '0 4px 20px rgba(0, 0, 0, 0.1)'
                    });
                } else {
                    navbar.removeClass('shadow').css({
                        'background': 'transparent',
                        'backdrop-filter': 'none',
                        'box-shadow': 'none'
                    });
                }
            });
            
            // Initial check
            $(window).scroll();
        });
    </script>
    
    <script>
      // Fallback: pastikan spinner hilang walau main.js gagal
      function hideSpinner() {
        var sp = document.getElementById('spinner');
        if (!sp) return;
        sp.classList.remove('show');
        sp.style.display = 'none';
      }
      document.addEventListener('DOMContentLoaded', function(){
        setTimeout(hideSpinner, 50);
      });
      window.addEventListener('load', function(){
        setTimeout(hideSpinner, 50);
      });
      // Cadangan tambahan 1 detik
      setTimeout(hideSpinner, 1000);
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
      document.addEventListener('DOMContentLoaded', function(){
        if (window.jQuery && $.fn.select2) {
          // Auto init all selects, allow opt-out via .no-select2
          $('select').not('.no-select2').each(function(){
            const $el = $(this);
            if (!$el.data('select2')) {
              $el.select2({ width: '100%' });
            }
          });
        }
        
        // Fix dropdown z-index and visibility
        function fixDropdownZIndex() {
          // Set high z-index for all dropdowns
          document.querySelectorAll('.dropdown-menu').forEach(function(menu) {
            menu.style.zIndex = '10000';
            menu.style.position = 'absolute';
          });
          
          // Ensure dropdown containers have proper z-index
          document.querySelectorAll('.dropdown').forEach(function(dropdown) {
            dropdown.style.zIndex = '10000';
            dropdown.style.position = 'relative';
            dropdown.style.overflow = 'visible';
          });
          
          // Ensure navbar has high z-index
          const navbar = document.querySelector('.navbar');
          if (navbar) {
            navbar.style.zIndex = '9999';
            navbar.style.position = 'relative';
            navbar.style.overflow = 'visible';
          }
          
          // Ensure fixed-top container has high z-index
          const fixedTop = document.querySelector('.fixed-top');
          if (fixedTop) {
            fixedTop.style.zIndex = '9999';
            fixedTop.style.overflow = 'visible';
          }
        }
        
        // Run on load
        fixDropdownZIndex();
        
        // Run when dropdown is shown
        document.querySelectorAll('[data-bs-toggle="dropdown"]').forEach(function(toggle) {
          toggle.addEventListener('shown.bs.dropdown', function() {
            fixDropdownZIndex();
          });
        });
        
        // Also fix on any dropdown show event
        document.addEventListener('show.bs.dropdown', function() {
          fixDropdownZIndex();
        });
      });
    </script>
    @if(session('success') || session('info') || session('error'))
    <script>
      (function(){
        const msg = @json(session('success') ?? session('info') ?? session('error'));
        const icon = @json(session('success') ? 'success' : (session('info') ? 'info' : 'error'));
        Swal.fire({
          icon: icon,
          title: msg,
          timer: 1800,
          showConfirmButton: false
        });
      })();
    </script>
    @endif
    @stack('scripts')
</body>
</html>

