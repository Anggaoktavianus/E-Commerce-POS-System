@extends('layouts.app')

@section('title', 'Belanja Online - ' . config('app.name'))

@section('meta_description', config('app.name') . ' - Belanja online produk berkualitas dengan harga terjangkau. Temukan berbagai produk kebutuhan sehari-hari Anda.')

@section('meta_keywords', 'belanja online, toko online, produk berkualitas, harga terjangkau, ' . config('app.name') . ', shop, belanja')

@section('og_image', asset('storage/defaults/og-shop.jpg'))

@section('content')
    <style>
      html, body {
        overflow-x: hidden;
        max-width: 100%;
      }
      .container-fluid {
        overflow-x: hidden;
        max-width: 100%;
      }
      
      /* Ensure header content doesn't cover dropdowns */
      .modern-page-header {
        z-index: 1 !important;
        position: relative !important;
      }
      
      /* Ensure all content below header has lower z-index */
      .container-fluid:not(.fixed-top) {
        z-index: 1 !important;
        position: relative !important;
      }
      
      /* Modern Page Header */
      .modern-page-header {
        background: linear-gradient(135deg, #147440 0%, #20c997 100%);
        padding: 6rem 0 3rem;
        position: relative;
        overflow: visible !important;
        margin-top: 0;
        margin-bottom: 0;
        z-index: 1 !important;
      }
      
      /* Ensure header pattern doesn't interfere */
      .modern-page-header::before {
        z-index: 0 !important;
      }
      
      /* Ensure proper spacing from fixed navbar */
      /* Navbar fixed-top typically has height around 100-120px on desktop */
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
      .modern-page-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse"><path d="M 40 0 L 0 0 0 40" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="1"/></pattern></defs><rect width="100%" height="100%" fill="url(%23grid)"/></svg>');
        opacity: 0.3;
      }
      .modern-page-header .container {
        position: relative;
        z-index: 1;
      }
      .page-title {
        font-size: 3rem;
        font-weight: 800;
        color: white;
        margin-bottom: 1rem;
        text-shadow: 0 2px 10px rgba(0,0,0,0.2);
        letter-spacing: -0.02em;
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
      
      /* Modern Filter Sidebar */
      .filter-sidebar {
        background: white;
        border-radius: 1.25rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        padding: 1.5rem;
        position: sticky;
        top: 100px;
        margin-bottom: 2rem;
      }
      .filter-section {
        margin-bottom: 2rem;
        padding-bottom: 1.5rem;
        border-bottom: 1px solid #e9ecef;
      }
      .filter-section:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
      }
      .filter-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: #147440;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
      }
      .filter-title i {
        font-size: 1.2rem;
      }
      .category-item {
        padding: 0.75rem 1rem;
        border-radius: 0.75rem;
        margin-bottom: 0.5rem;
        transition: all 0.3s ease;
        border: 2px solid transparent;
      }
      .category-item:hover {
        background: #f8f9fa;
        transform: translateX(5px);
      }
      .category-item.active {
        background: linear-gradient(135deg, rgba(20, 116, 64, 0.1) 0%, rgba(32, 201, 151, 0.1) 100%);
        border-color: #147440;
      }
      .category-item a {
        color: #495057;
        text-decoration: none;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 0.75rem;
      }
      .category-item.active a {
        color: #147440;
        font-weight: 700;
      }
      .category-item i {
        color: #147440;
        font-size: 0.875rem;
      }
      
      /* Modern Search & Filter Bar */
      .search-filter-bar {
        background: white;
        border-radius: 1.5rem;
        box-shadow: 0 8px 30px rgba(0,0,0,0.08);
        padding: 2rem;
        margin-bottom: 2.5rem;
        border: 1px solid rgba(20, 116, 64, 0.1);
      }
      
      .filter-section-wrapper {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
      }
      
      .filter-item {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
      }
      
      .filter-label {
        font-size: 0.875rem;
        font-weight: 700;
        color: #147440;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.25rem;
      }
      
      .filter-label i {
        font-size: 1rem;
      }
      
      /* Unified Input Heights - All inputs must be same height */
      .filter-item .input-group,
      .filter-item .store-switcher-card {
        height: 50px !important;
        min-height: 50px !important;
        max-height: 50px !important;
        box-sizing: border-box;
      }
      
      .modern-search-input {
        height: 50px !important;
        min-height: 50px !important;
        max-height: 50px !important;
        line-height: 50px !important;
        box-sizing: border-box;
        margin: 0 !important;
        padding: 0 1.5rem !important;
        border: 2px solid #e9ecef !important;
        border-radius: 0.875rem;
        font-size: 1rem;
        transition: all 0.3s ease;
        background: #f8f9fa;
        color: #495057;
        font-weight: 500;
        vertical-align: top;
      }
      .modern-search-input:focus {
        border-color: #147440 !important;
        box-shadow: 0 0 0 0.25rem rgba(20, 116, 64, 0.1);
        outline: none;
        background: white;
      }
      .modern-search-input::placeholder {
        color: #adb5bd;
        font-weight: 400;
      }
      
      .input-group {
        display: flex;
        gap: 0;
        border-radius: 0.875rem;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        height: 50px !important;
        align-items: stretch;
        box-sizing: border-box;
      }
      
      .input-group .modern-search-input {
        border-radius: 0.875rem 0 0 0.875rem;
        border-right: none !important;
        flex: 1;
        height: 50px !important;
      }
      
      .search-btn {
        background: linear-gradient(135deg, #147440 0%, #20c997 100%);
        border: none !important;
        border-radius: 0 0.875rem 0.875rem 0;
        padding: 0 2rem !important;
        color: white;
        font-weight: 700;
        transition: all 0.3s ease;
        white-space: nowrap;
        box-shadow: 0 4px 12px rgba(20, 116, 64, 0.2);
        height: 50px !important;
        min-height: 50px !important;
        max-height: 50px !important;
        display: flex;
        align-items: center;
        justify-content: center;
        line-height: 1;
        margin: 0 !important;
        box-sizing: border-box;
      }
      .search-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(20, 116, 64, 0.35);
        background: linear-gradient(135deg, #0f5c33 0%, #147440 100%);
      }
      .search-btn:active {
        transform: translateY(0);
      }
      
      /* Store Switcher Card - Match search input styling exactly */
      .store-switcher-card {
        background: #f8f9fa !important;
        border: 2px solid #e9ecef !important;
        border-radius: 0.875rem !important;
        padding: 0 !important;
        margin: 0 !important;
        transition: all 0.3s ease;
        overflow: hidden;
        height: 50px !important;
        min-height: 50px !important;
        max-height: 50px !important;
        display: flex !important;
        align-items: center !important;
        box-sizing: border-box !important;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05) !important;
      }
      .store-switcher-card:hover {
        border-color: #147440 !important;
        box-shadow: 0 4px 12px rgba(20, 116, 64, 0.15) !important;
        transform: translateY(-2px);
        background: white !important;
      }
      .store-switcher-card:focus-within {
        border-color: #147440 !important;
        box-shadow: 0 0 0 0.25rem rgba(20, 116, 64, 0.1) !important;
        background: white !important;
      }
      .store-switcher-select {
        border: none !important;
        outline: none !important;
        border-radius: 0.875rem !important;
        padding: 0 1.5rem !important;
        font-size: 1rem !important;
        transition: all 0.3s ease;
        background: transparent !important;
        color: #495057 !important;
        cursor: pointer;
        width: 100% !important;
        font-weight: 500 !important;
        height: 50px !important;
        min-height: 50px !important;
        max-height: 50px !important;
        line-height: 50px !important;
        appearance: none !important;
        -webkit-appearance: none !important;
        -moz-appearance: none !important;
        margin: 0 !important;
        vertical-align: top !important;
        box-sizing: border-box !important;
      }
      .store-switcher-select:focus {
        outline: none !important;
        border: none !important;
        background: transparent !important;
      }
      .store-switcher-select:hover {
        color: #147440 !important;
      }
      .store-switcher-card:focus-within .store-switcher-select {
        background: transparent !important;
      }
      .store-switcher-select option {
        background: white;
        color: #495057;
        padding: 0.5rem;
        line-height: 1.5;
      }
      
      /* Ensure perfect alignment */
      .filter-item {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
      }
      
      .filter-item > .input-group,
      .filter-item > .store-switcher-card,
      .filter-item > form > .store-switcher-card {
        margin-top: 0 !important;
        margin-bottom: 0 !important;
      }
      
      /* Ensure row alignment */
      .row.g-4.align-items-end > [class*="col-"] {
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
      }
      
      .row.g-4.align-items-end .filter-item {
        width: 100%;
      }
      
      /* Ensure filter items align properly */
      .filter-item {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
      }
      
      /* Align filter items in same row */
      .row.g-4 > [class*="col-"] {
        display: flex;
        flex-direction: column;
      }
      
      .row.g-4 > [class*="col-"] > .filter-item {
        height: 100%;
        justify-content: flex-start;
      }
      
      /* Product Grid */
      .products-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        flex-wrap: wrap;
        gap: 1rem;
      }
      .products-count {
        color: #6c757d;
        font-size: 0.95rem;
      }
      .products-count strong {
        color: #147440;
        font-weight: 700;
      }
      
      /* Enhanced Product Cards */
      .product-card {
        border: none !important;
        border-radius: 1rem !important;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1) !important;
        overflow: hidden;
        background: #fff;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        position: relative;
        height: 100%;
        display: flex;
        flex-direction: column;
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
        z-index: 2;
      }
      .product-card:hover::before {
        transform: scaleX(1);
      }
      .product-card:hover {
        transform: translateY(-10px) scale(1.02);
        box-shadow: 0 20px 40px rgba(20, 116, 64, 0.2), 0 8px 16px rgba(0, 0, 0, 0.1);
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
        transform: scale(1.15);
      }
      .product-badge {
        background: linear-gradient(135deg, #0f5c33 0%, #147440 100%);
        color: white !important;
        box-shadow: 0 4px 12px rgba(20, 116, 64, 0.3);
        font-weight: 700;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        font-size: 0.7rem;
        padding: 0.4rem 0.9rem;
        border-radius: 50px;
      }
      .promo-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        box-shadow: 0 4px 12px rgba(255, 107, 107, 0.4);
        z-index: 10;
      }
      .product-image-link,
      .product-name-link {
        cursor: pointer;
        text-decoration: none;
        color: inherit;
        transition: all 0.3s ease;
        display: block;
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
        width: 100%;
      }
      .add-to-cart-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(20, 116, 64, 0.4);
        background: linear-gradient(135deg, #0f5c33 0%, #147440 100%);
      }
      .product-price {
        font-size: 1.1rem;
        font-weight: 600;
        background: linear-gradient(135deg, #147440 0%, #20c997 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        letter-spacing: -0.02em;
      }
      .stock-badge {
        border-radius: 50px;
        padding: 0.3rem 0.7rem;
        font-weight: 500;
        font-size: 0.7rem;
        letter-spacing: 0.3px;
        text-transform: uppercase;
      }
      .add-to-cart-form { position: relative; }
      .add-to-cart-btn:disabled { opacity: 0.6; cursor: not-allowed; }
      .btn-loading .spinner-border { width: 1rem; height: 1rem; border-width: 0.15em; }
      
      /* Empty State */
      .empty-state {
        text-align: center;
        padding: 4rem 2rem;
      }
      .empty-state-icon {
        font-size: 5rem;
        color: #dee2e6;
        margin-bottom: 1.5rem;
      }
      .empty-state-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #495057;
        margin-bottom: 0.5rem;
      }
      .empty-state-text {
        color: #6c757d;
        margin-bottom: 2rem;
      }
      
      /* Custom Pagination */
      .pagination {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 0.5rem;
        margin-top: 3rem;
        flex-wrap: wrap;
      }
      .pagination .rounded {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 45px;
        height: 45px;
        padding: 0.5rem 1rem;
        border: 2px solid #e9ecef;
        border-radius: 0.75rem !important;
        background-color: #fff;
        color: #495057;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
      }
      .pagination .rounded:hover {
        background-color: #147440;
        color: #fff;
        border-color: #147440;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(20, 116, 64, 0.2);
      }
      .pagination .active {
        background-color: #147440 !important;
        color: #fff !important;
        border-color: #147440 !important;
      }
      .pagination .disabled {
        opacity: 0.5;
        pointer-events: none;
        background-color: #f8f9fa !important;
        color: #6c757d !important;
        border-color: #dee2e6 !important;
      }
      
      /* Responsive Styles */
      @media (max-width: 991.98px) {
        .page-title {
          font-size: 2.25rem;
        }
        .filter-sidebar {
          position: static;
          margin-bottom: 2rem;
        }
        .category-list {
          max-height: 300px;
          overflow-y: auto;
        }
      }
      @media (max-width: 767.98px) {
        .modern-page-header {
          padding: 3rem 0 2rem;
        }
        .page-title {
          font-size: 1.75rem;
        }
        .search-filter-bar {
          padding: 1.5rem;
          border-radius: 1rem;
        }
        .filter-section-wrapper {
          gap: 1.25rem;
        }
        .filter-item {
          gap: 0.5rem;
        }
        .filter-label {
          font-size: 0.8rem;
        }
        .modern-search-input {
          padding: 0.75rem 1.25rem;
          font-size: 0.95rem;
        }
        .search-btn {
          padding: 0.75rem 1.5rem;
          font-size: 0.9rem;
        }
        .store-switcher-card {
          padding: 0;
        }
        .store-switcher-select {
          padding: 0.75rem 1.25rem;
          font-size: 0.95rem;
        }
        .filter-sidebar {
          padding: 1rem;
        }
        .product-card {
          margin-bottom: 1.5rem;
        }
        .products-header {
          flex-direction: column;
          align-items: flex-start;
        }
      }
      @media (max-width: 575.98px) {
        .category-list {
          max-height: 200px;
        }
        .product-price {
          font-size: 1rem !important;
        }
        .add-to-cart-btn {
          padding: 0.45rem 0.9rem !important;
          font-size: 0.8rem;
        }
        .product-name-link h4 {
          font-size: 0.95rem !important;
          min-height: 2rem !important;
        }
      }
    </style>
    @push('scripts')
    <script>
        console.log('Shop page script loading...');
        
        // Store Switcher - Initialize immediately and on DOM ready
        function initStoreSwitcher() {
            const storeSwitcher = document.getElementById('store-switcher');
            const storeSwitcherForm = document.getElementById('store-switcher-form');
            
            console.log('Initializing store switcher:', {
                storeSwitcher: !!storeSwitcher,
                storeSwitcherForm: !!storeSwitcherForm
            });
            
            if (!storeSwitcher || !storeSwitcherForm) {
                console.error('Store switcher elements not found');
                return false;
            }
            
            // Check if already initialized
            if (storeSwitcher.dataset.initialized === 'true') {
                console.log('Store switcher already initialized');
                return true;
            }
            
            // Mark as initialized
            storeSwitcher.dataset.initialized = 'true';
            
            // Add change event listener
            storeSwitcher.addEventListener('change', function(e) {
                console.log('=== STORE SWITCHER CHANGED ===');
                console.log('Selected value:', this.value);
                
                e.preventDefault();
                e.stopPropagation();
                
                const selectedValue = this.value;
                
                // Build URL with all parameters
                const params = new URLSearchParams();
                
                // Add store_id
                if (selectedValue) {
                    params.append('store_id', selectedValue);
                }
                
                // Add other existing parameters from form hidden inputs
                const hiddenInputs = storeSwitcherForm.querySelectorAll('input[type="hidden"]');
                hiddenInputs.forEach(function(input) {
                    if (input.name && input.value && input.name !== 'store_id') {
                        params.append(input.name, input.value);
                    }
                });
                
                // Also check current URL parameters
                const urlParams = new URLSearchParams(window.location.search);
                urlParams.forEach((value, key) => {
                    if (key !== 'store_id' && !params.has(key) && value && value.trim() !== '') {
                        params.append(key, value);
                    }
                });
                
                // Build final URL
                const baseUrl = storeSwitcherForm.action || window.location.pathname;
                const queryString = params.toString();
                const finalUrl = baseUrl + (queryString ? '?' + queryString : '');
                
                console.log('Base URL:', baseUrl);
                console.log('Query String:', queryString);
                console.log('Final URL:', finalUrl);
                
                // Add loading state
                this.disabled = true;
                this.style.opacity = '0.6';
                this.style.cursor = 'wait';
                
                // Redirect immediately
                console.log('Redirecting to:', finalUrl);
                window.location.href = finalUrl;
            });
            
            console.log('Store switcher listener attached successfully');
            return true;
        }
        
        // Try to initialize immediately
        if (document.readyState === 'complete' || document.readyState === 'interactive') {
            console.log('Document already ready, initializing immediately');
            initStoreSwitcher();
        }
        
        // Also initialize on DOMContentLoaded
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOMContentLoaded fired');
            initStoreSwitcher();
        });
        
        // Fallback: try after a delay
        setTimeout(function() {
            console.log('Timeout fallback - initializing store switcher');
            if (!initStoreSwitcher()) {
                console.warn('Store switcher initialization failed, retrying...');
                setTimeout(initStoreSwitcher, 500);
            }
        }, 100);
        
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
            
            // Store Switcher - Auto submit on change
            function initStoreSwitcher() {
                const storeSwitcher = document.getElementById('store-switcher');
                const storeSwitcherForm = document.getElementById('store-switcher-form');
                
                console.log('Initializing store switcher:', {
                    storeSwitcher: !!storeSwitcher,
                    storeSwitcherForm: !!storeSwitcherForm
                });
                
                if (!storeSwitcher || !storeSwitcherForm) {
                    console.error('Store switcher elements not found');
                    return;
                }
                
                // Remove any existing listeners by using a flag
                if (storeSwitcher.dataset.listenerAttached === 'true') {
                    console.log('Listener already attached, skipping');
                    return;
                }
                
                storeSwitcher.dataset.listenerAttached = 'true';
                
                storeSwitcher.addEventListener('change', function(e) {
                    console.log('Store switcher changed!', this.value);
                    
                    e.preventDefault();
                    e.stopPropagation();
                    
                    const selectedValue = this.value;
                    console.log('Selected value:', selectedValue);
                    
                    // Build URL with all parameters
                    const params = new URLSearchParams();
                    
                    // Add store_id (even if empty for "Semua Store")
                    if (selectedValue) {
                        params.append('store_id', selectedValue);
                    }
                    
                    // Add other existing parameters from form hidden inputs
                    const hiddenInputs = storeSwitcherForm.querySelectorAll('input[type="hidden"]');
                    hiddenInputs.forEach(function(input) {
                        if (input.name && input.value && input.name !== 'store_id') {
                            params.append(input.name, input.value);
                        }
                    });
                    
                    // Also check current URL parameters
                    const urlParams = new URLSearchParams(window.location.search);
                    urlParams.forEach((value, key) => {
                        if (key !== 'store_id' && !params.has(key) && value && value.trim() !== '') {
                            params.append(key, value);
                        }
                    });
                    
                    // Build final URL
                    const baseUrl = storeSwitcherForm.action || window.location.pathname;
                    const queryString = params.toString();
                    const finalUrl = baseUrl + (queryString ? '?' + queryString : '');
                    
                    console.log('Final URL:', finalUrl);
                    console.log('Parameters:', queryString);
                    
                    // Add loading state
                    this.disabled = true;
                    this.style.opacity = '0.6';
                    this.style.cursor = 'wait';
                    
                    // Redirect immediately
                    window.location.href = finalUrl;
                });
                
                console.log('Store switcher listener attached successfully');
            }
            
            // Initialize on DOM ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initStoreSwitcher);
            } else {
                initStoreSwitcher();
            }
            
            // Also try after a short delay in case DOM isn't ready
            setTimeout(initStoreSwitcher, 100);
            
            // Category collapse toggle
            const categoryToggle = document.querySelector('[data-bs-toggle="collapse"][data-bs-target="#categoryCollapse"]');
            if (categoryToggle) {
                categoryToggle.addEventListener('click', function() {
                    const icon = this.querySelector('i');
                    const isExpanded = this.getAttribute('aria-expanded') === 'true';
                    if (icon) {
                        icon.classList.toggle('fa-chevron-down', !isExpanded);
                        icon.classList.toggle('fa-chevron-up', isExpanded);
                    }
                });
            }
            
            // Search functionality
            const searchInput = document.querySelector('.modern-search-input');
            const searchBtn = document.querySelector('.search-btn');
            if (searchInput && searchBtn) {
                const performSearch = () => {
                    const query = searchInput.value.trim();
                    if (query) {
                        const url = new URL(window.location.href);
                        url.searchParams.set('search', query);
                        window.location.href = url.toString();
                    }
                };
                
                searchBtn.addEventListener('click', performSearch);
                searchInput.addEventListener('keypress', (e) => {
                    if (e.key === 'Enter') {
                        performSearch();
                    }
                });
            }
        });
    </script>
    @endpush
    @php
        $isMitra = auth()->check() && method_exists(auth()->user(), 'isMitra') && auth()->user()->isMitra();
        $categorySlug = request('category');
    @endphp
   
    <!-- Single Page Header End -->
    <!-- Modern Page Header -->
    @php
        // Dynamic page title based on filters
        $pageTitle = $siteSettings['shop_page_title'] ?? ($siteSettings['brand_name'] ?? 'Toko Online');
        $activeCategory = null;
        if (request('category')) {
            $activeCategory = $categories->firstWhere('slug', request('category'));
            if ($activeCategory) {
                $pageTitle = $activeCategory->name;
            }
        }
        if (isset($selectedStore) && $selectedStore) {
            $pageTitle = ($activeCategory ? $activeCategory->name . ' - ' : '') . ($selectedStore->short_name ?? $selectedStore->name);
        } elseif (request('search')) {
            $pageTitle = 'Hasil Pencarian: "' . request('search') . '"';
        }
        
        // Build dynamic breadcrumb items
        $breadcrumbItems = [
            ['label' => 'Beranda', 'url' => url('/')]
        ];
        
        if (isset($selectedStore) && $selectedStore) {
            $breadcrumbItems[] = ['label' => 'Toko', 'url' => route('shop')];
            if ($activeCategory) {
                $breadcrumbItems[] = ['label' => $selectedStore->short_name ?? $selectedStore->name, 'url' => route('shop', ['store_id' => request('store_id')])];
                $breadcrumbItems[] = ['label' => $activeCategory->name, 'url' => null];
            } else {
                $breadcrumbItems[] = ['label' => $selectedStore->short_name ?? $selectedStore->name, 'url' => null];
            }
        } elseif ($activeCategory) {
            $breadcrumbItems[] = ['label' => 'Toko', 'url' => route('shop')];
            $breadcrumbItems[] = ['label' => $activeCategory->name, 'url' => null];
        } elseif (request('search')) {
            $breadcrumbItems[] = ['label' => 'Toko', 'url' => route('shop')];
            $breadcrumbItems[] = ['label' => 'Pencarian', 'url' => null];
        } else {
            $breadcrumbItems[] = ['label' => 'Toko', 'url' => null];
        }
    @endphp
    @include('partials.modern-page-header', [
        'pageTitle' => $pageTitle,
        'breadcrumbItems' => $breadcrumbItems
    ])

    <!-- Shop Content -->
    <div class="container-fluid py-5" style="background: linear-gradient(180deg, #f8f9fa 0%, #ffffff 100%);">
        <div class="container py-4">
            <!-- Search & Filter Bar -->
            <div class="search-filter-bar">
                <div class="filter-section-wrapper">
                    <div class="row g-4" style="align-items: flex-end;">
                        <!-- Search Section -->
                        <div class="col-12 col-lg-4">
                            <div class="filter-item">
                                <label class="filter-label">
                                    <i class="fas fa-search"></i>
                                    Cari Produk
                                </label>
                                <div class="input-group" style="height: 50px !important; display: flex !important; align-items: stretch !important;">
                                    <input type="search" 
                                           class="form-control modern-search-input" 
                                           placeholder="Masukkan kata kunci..." 
                                           value="{{ request('search') }}"
                                           id="search-input"
                                           style="height: 50px !important; line-height: 50px !important; padding: 0 1.5rem !important; border: 2px solid #e9ecef !important; border-radius: 0.875rem 0 0 0.875rem !important; border-right: none !important; background: #f8f9fa !important; font-size: 1rem !important; font-weight: 500 !important; margin: 0 !important;">
                                    <button class="btn search-btn" type="button" id="search-btn" style="height: 50px !important; padding: 0 2rem !important; border-radius: 0 0.875rem 0.875rem 0 !important; margin: 0 !important;">
                                        <i class="fas fa-search me-2"></i>Cari
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Store Selection Section -->
                        <div class="col-12 col-lg-8">
                            @if(isset($stores) && $stores->count() > 0)
                            <div class="filter-item">
                                <label class="filter-label">
                                    <i class="fas fa-store"></i>
                                    Pilih Store
                                </label>
                                <form method="GET" action="{{ route('shop') }}" id="store-switcher-form" class="store-switcher-form">
                                    @if(request('category'))
                                        <input type="hidden" name="category" value="{{ request('category') }}">
                                    @endif
                                    @if(request('search'))
                                        <input type="hidden" name="search" value="{{ request('search') }}">
                                    @endif
                                    @if(request('sort'))
                                        <input type="hidden" name="sort" value="{{ request('sort') }}">
                                    @endif
                                    <div class="store-switcher-card" style="height: 50px !important; background: #f8f9fa !important; border: 2px solid #e9ecef !important; border-radius: 0.875rem !important; padding: 0 !important; margin: 0 !important; display: flex !important; align-items: center !important; box-shadow: 0 2px 8px rgba(0,0,0,0.05) !important;">
                                        <select name="store_id" id="store-switcher" class="form-select store-switcher-select" required
                                                onchange="handleStoreChange(this)"
                                                style="height: 50px !important; line-height: 50px !important; padding: 0 1.5rem !important; border: none !important; outline: none !important; background: transparent !important; font-size: 1rem !important; font-weight: 500 !important; color: #495057 !important; width: 100% !important; margin: 0 !important; appearance: none !important; -webkit-appearance: none !important; -moz-appearance: none !important;">
                                            <option value="">Semua Store</option>
                                            @foreach($stores as $store)
                                                <option value="{{ encode_id($store->id) }}" 
                                                        {{ (isset($currentSelectedStoreId) && $currentSelectedStoreId == $store->id) ? 'selected' : '' }}>
                                                    {{ $store->short_name }}
                                                    @if($store->city)
                                                        - {{ $store->city }}
                                                    @endif
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </form>
                                <script>
                                // Inline handler as fallback
                                function handleStoreChange(selectElement) {
                                    console.log('handleStoreChange called with value:', selectElement.value);
                                    const form = document.getElementById('store-switcher-form');
                                    if (!form) {
                                        console.error('Form not found');
                                        return;
                                    }
                                    
                                    const selectedValue = selectElement.value;
                                    const params = new URLSearchParams();
                                    
                                    if (selectedValue) {
                                        params.append('store_id', selectedValue);
                                    }
                                    
                                    // Add hidden inputs
                                    const hiddenInputs = form.querySelectorAll('input[type="hidden"]');
                                    hiddenInputs.forEach(function(input) {
                                        if (input.name && input.value && input.name !== 'store_id') {
                                            params.append(input.name, input.value);
                                        }
                                    });
                                    
                                    // Add current URL params
                                    const urlParams = new URLSearchParams(window.location.search);
                                    urlParams.forEach((value, key) => {
                                        if (key !== 'store_id' && !params.has(key) && value && value.trim() !== '') {
                                            params.append(key, value);
                                        }
                                    });
                                    
                                    const baseUrl = form.action || window.location.pathname;
                                    const queryString = params.toString();
                                    const finalUrl = baseUrl + (queryString ? '?' + queryString : '');
                                    
                                    console.log('Redirecting to:', finalUrl);
                                    selectElement.disabled = true;
                                    selectElement.style.opacity = '0.6';
                                    window.location.href = finalUrl;
                                }
                                </script>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <!-- Filter Sidebar -->
                <div class="col-12 col-lg-3">
                    <div class="filter-sidebar">
                        <!-- Categories Filter -->
                        <div class="filter-section">
                            <h5 class="filter-title">
                                <i class="fas fa-th-large"></i>
                                Kategori
                            </h5>
                            <div class="d-lg-none mb-3">
                                <button class="btn btn-outline-secondary btn-sm w-100" type="button" data-bs-toggle="collapse" data-bs-target="#categoryCollapse">
                                    <i class="fas fa-chevron-down me-2"></i>Lihat Kategori
                                </button>
                            </div>
                            <div class="collapse d-lg-block" id="categoryCollapse">
                                <ul class="list-unstyled mb-0">
                                    <li class="category-item {{ empty($categorySlug) ? 'active' : '' }}">
                                        <a href="{{ route('shop', request()->except('category')) }}">
                                            <i class="fas fa-circle"></i>
                                            <span>Semua Produk</span>
                                        </a>
                                    </li>
                                    @foreach($categories as $cat)
                                        @php
                                            $active = $categorySlug === $cat->slug;
                                        @endphp
                                        <li class="category-item {{ $active ? 'active' : '' }}">
                                            <a href="{{ route('shop', array_merge(request()->except('category'), ['category' => $cat->slug])) }}">
                                                <i class="fas fa-circle"></i>
                                                <span>{{ $cat->name }}</span>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>

                        <!-- Store Badge (if selected) -->
                        @if(isset($selectedStore) && $selectedStore)
                        <div class="filter-section">
                            <h5 class="filter-title">
                                <i class="fas fa-info-circle"></i>
                                Store Aktif
                            </h5>
                            <div class="alert alert-success mb-0" style="border-radius: 0.75rem;">
                                <i class="fas fa-store me-2"></i>
                                <strong>{{ $selectedStore->name }}</strong>
                                @if($selectedStore->city)
                                    <br><small>{{ $selectedStore->city }}</small>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Products Grid -->
                <div class="col-12 col-lg-9">
                    <!-- Products Header -->
                    <div class="products-header">
                        <div>
                            <h2 class="mb-1" style="color: #147440; font-weight: 700;">Produk Kami</h2>
                            <p class="products-count mb-0">
                                Menampilkan <strong>{{ $products->firstItem() ?? 0 }}-{{ $products->lastItem() ?? 0 }}</strong> dari <strong>{{ $products->total() }}</strong> produk
                            </p>
                        </div>
                    </div>

                    

                    <!-- Products Grid -->
                    <div class="row g-4">
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

                                // Harga tampilan mitra vs customer
                                $displayPrice = $price;
                                $originalPrice = null;
                                if ($isMitra && $price > 0) {
                                    $originalPrice = $price;
                                    $displayPrice = $price * 0.9;
                                }
                                
                                $stockQty = $product->stock_qty ?? 0;
                                $isOutOfStock = $stockQty <= 0;
                            @endphp
                            <div class="col-md-6 col-xl-4">
                                <div class="product-card">
                                    <a href="{{ $detailUrl }}" class="product-image-link">
                                        <div class="fruite-img ratio ratio-4x3 rounded-top overflow-hidden">
                                            <img src="{{ asset($image) }}" 
                                                 class="w-100 h-100 rounded-top" 
                                                 style="object-fit: contain;" 
                                                 alt="{{ $product->name }}">
                                        </div>
                                    </a>
                                    <div class="product-badge position-absolute" style="top: 15px; left: 15px; z-index: 10;">
                                        {{ $categoryLabel }}
                                    </div>
                                    @if(($product->is_featured ?? 0) == 1)
                                        <div class="position-absolute" style="top: 15px; right: 15px; z-index: 10;">
                                            <span class="badge bg-warning text-dark stock-badge">
                                                <i class="fas fa-star me-1"></i>Favorit
                                            </span>
                                        </div>
                                    @endif
                                    @if(($product->is_bestseller ?? 0) == 1)
                                        <div class="promo-badge">
                                            <i class="fas fa-fire me-1"></i>Best Seller
                                        </div>
                                    @endif
                                    <div class="p-3 rounded-bottom d-flex flex-column flex-grow-1">
                                        @if($product->store_short_name)
                                        <div class="mb-1">
                                            <span class="badge bg-secondary" style="font-size: 0.7rem; font-weight: 500;">
                                                <i class="fas fa-store me-1"></i>{{ $product->store_short_name }}
                                            </span>
                                        </div>
                                        @endif
                                        <a href="{{ $detailUrl }}" class="product-name-link text-decoration-none">
                                            <h4 class="fw-bold mb-2 text-dark" style="min-height: 2.5rem; line-height: 1.3; font-size: 1rem;">{{ $product->name }}</h4>
                                        </a>
                                        <div class="mb-2">
                                            <p class="product-price mb-0" style="font-size: 1.1rem;">
                                                {{ $siteSettings['currency_symbol'] ?? 'Rp.' }} {{ number_format($displayPrice, 0, ',', '.') }}
                                            </p>
                                            @if($originalPrice)
                                                <div class="mt-1">
                                                    <span class="text-muted text-decoration-line-through" style="font-size: 0.8rem;">
                                                        {{ $siteSettings['currency_symbol'] ?? 'Rp.' }} {{ number_format($originalPrice, 0, ',', '.') }}
                                                    </span>
                                                    <span class="badge bg-success ms-1" style="font-size: 0.7rem;">Mitra</span>
                                                </div>
                                            @endif
                                        </div>
                                        @if($isOutOfStock)
                                            <div class="mb-2">
                                                <span class="badge bg-danger stock-badge" style="font-size: 0.7rem;">
                                                    <i class="fas fa-ban me-1"></i>Stok Habis
                                                </span>
                                            </div>
                                        @elseif($stockQty < 10)
                                            <div class="mb-2">
                                                <span class="badge bg-warning text-dark stock-badge" style="font-size: 0.7rem;">
                                                    <i class="fas fa-exclamation-triangle me-1"></i>Stok Terbatas ({{ $stockQty }} {{ $product->unit }})
                                                </span>
                                            </div>
                                        @else
                                            <div class="mb-2">
                                                <span class="badge bg-success stock-badge" style="font-size: 0.7rem;">
                                                    <i class="fas fa-check-circle me-1"></i>Tersedia ({{ $stockQty }} {{ $product->unit }})
                                                </span>
                                            </div>
                                        @endif
                                        <div class="mt-auto pt-2" style="border-top: 1px solid #e9ecef;">
                                            <form action="{{ route('cart.add') }}" method="POST" class="add-to-cart-form">
                                                @csrf
                                                <input type="hidden" name="id" value="{{ $product->id }}">
                                                <input type="hidden" name="name" value="{{ $product->name }}">
                                                <input type="hidden" name="price" value="{{ $price }}">
                                                <input type="hidden" name="image" value="{{ $product->main_image_path ? ('storage/'.$product->main_image_path) : '' }}">
                                                <input type="hidden" name="qty" value="1" max="{{ $stockQty }}">
                                                @if($isOutOfStock)
                                                    <button class="btn btn-secondary w-100 rounded-pill" type="button" disabled style="opacity: 0.6; font-size: 0.875rem; padding: 0.5rem 1rem;">
                                                        <i class="fa fa-ban me-2"></i>Stok Habis
                                                    </button>
                                                @else
                                                    <button class="btn add-to-cart-btn w-100" type="submit">
                                                        <span class="btn-text">
                                                            <i class="fa fa-shopping-bag me-2"></i>
                                                            <span>{{ $siteSettings['add_to_cart_text'] ?? 'Tambah ke Keranjang' }}</span>
                                                        </span>
                                                        <span class="btn-loading d-none">
                                                            <span class="spinner-border spinner-border-sm me-2"></span>
                                                            <span>Menambahkan...</span>
                                                        </span>
                                                    </button>
                                                @endif
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="empty-state">
                                    <div class="empty-state-icon">
                                        <i class="fas fa-box-open"></i>
                                    </div>
                                    <h3 class="empty-state-title">Tidak ada produk ditemukan</h3>
                                    <p class="empty-state-text">Coba ubah filter atau kata kunci pencarian Anda</p>
                                    <a href="{{ route('shop') }}" class="btn btn-primary">
                                        <i class="fas fa-redo me-2"></i>Reset Filter
                                    </a>
                                </div>
                            </div>
                        @endforelse
                    </div>

                    <!-- Pagination -->
                    @if($products->hasPages())
                        <div class="pagination">
                            @if ($products->onFirstPage())
                                <span class="rounded disabled">&laquo;</span>
                            @else
                                <a href="{{ $products->previousPageUrl() }}" class="rounded">&laquo;</a>
                            @endif

                            @for ($page = 1; $page <= $products->lastPage(); $page++)
                                @if ($page == $products->currentPage())
                                    <span class="active rounded">{{ $page }}</span>
                                @else
                                    <a href="{{ $products->url($page) }}" class="rounded">{{ $page }}</a>
                                @endif
                            @endfor

                            @if ($products->hasMorePages())
                                <a href="{{ $products->nextPageUrl() }}" class="rounded">&raquo;</a>
                            @else
                                <span class="rounded disabled">&raquo;</span>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
