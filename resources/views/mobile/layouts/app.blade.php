<!DOCTYPE html>
<html lang="id" dir="ltr">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <title>@yield('title', config('app.name'))</title>
  <link rel="icon" type="image/x-icon" href="{{ asset('sneat/assets/img/favicon/favicon.ico') }}" />
  <link rel="stylesheet" href="{{ asset('sneat/assets/vendor/fonts/boxicons.css') }}" />
  <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    
    body {
      font-family: 'Public Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      background-color: #f5f5f5;
      padding-bottom: 70px; /* Space for bottom nav */
      -webkit-font-smoothing: antialiased;
      -moz-osx-font-smoothing: grayscale;
    }
    
    /* Mobile Header */
    .mobile-header {
      background: linear-gradient(135deg, #147440 0%, #1a9c52 100%);
      color: white;
      padding: 0.75rem 1rem;
      position: sticky;
      top: 0;
      z-index: 1000;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .mobile-header .header-top {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 0.75rem;
    }
    
    .mobile-header .logo {
      font-size: 1.25rem;
      font-weight: 700;
      color: white;
      text-decoration: none;
    }
    
    .mobile-header .header-icons {
      display: flex;
      gap: 1rem;
      align-items: center;
    }
    
    .mobile-header .header-icons a {
      color: white;
      font-size: 1.25rem;
      position: relative;
      text-decoration: none;
    }
    
    .mobile-header .header-icons .cart-badge {
      position: absolute;
      top: -8px;
      right: -8px;
      background: #dc3545;
      color: white;
      border-radius: 50%;
      width: 18px;
      height: 18px;
      font-size: 0.7rem;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 600;
    }
    
    /* Search Bar */
    .mobile-search {
      position: relative;
    }
    
    .mobile-search input {
      width: 100%;
      padding: 0.625rem 2.5rem 0.625rem 1rem;
      border: none;
      border-radius: 25px;
      background: rgba(255,255,255,0.95);
      font-size: 0.875rem;
      color: #333;
    }
    
    .mobile-search input:focus {
      outline: none;
      background: white;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .mobile-search .search-icon {
      position: absolute;
      right: 1rem;
      top: 50%;
      transform: translateY(-50%);
      color: #666;
      font-size: 1.125rem;
    }
    
    /* Promotional Carousel */
    .promo-carousel {
      background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
      padding: 1rem;
      margin: 0;
    }
    
    .promo-card {
      background: linear-gradient(135deg, #147440 0%, #1a9c52 100%);
      border-radius: 12px;
      padding: 1rem;
      color: white;
      display: flex;
      align-items: center;
      justify-content: space-between;
      min-width: 280px;
    }
    
    .promo-card .promo-icon {
      width: 48px;
      height: 48px;
      background: rgba(255,255,255,0.2);
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.5rem;
      font-weight: 700;
    }
    
    .promo-card .promo-content {
      flex: 1;
      margin-left: 1rem;
    }
    
    .promo-card .promo-title {
      font-size: 0.875rem;
      font-weight: 600;
      margin-bottom: 0.25rem;
    }
    
    .promo-card .promo-action {
      font-size: 0.75rem;
      opacity: 0.9;
      display: flex;
      align-items: center;
      gap: 0.25rem;
    }
    
    /* Category Icons */
    .category-icons {
      display: flex;
      gap: 1rem;
      overflow-x: auto;
      padding: 1rem;
      background: white;
      -webkit-overflow-scrolling: touch;
      scrollbar-width: none;
    }
    
    .category-icons::-webkit-scrollbar {
      display: none;
    }
    
    .category-icon {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 0.5rem;
      min-width: 70px;
      text-decoration: none;
      color: #333;
    }
    
    .category-icon .icon-circle {
      width: 56px;
      height: 56px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.5rem;
      background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
      color: #147440;
    }
    
    .category-icon .icon-label {
      font-size: 0.75rem;
      text-align: center;
      font-weight: 500;
    }
    
    /* Section Title */
    .section-title {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 1rem;
      background: white;
      margin-top: 0.5rem;
    }
    
    .section-title h5 {
      font-size: 1rem;
      font-weight: 700;
      margin: 0;
      color: #333;
    }
    
    .section-title .countdown {
      font-size: 0.875rem;
      color: #dc3545;
      font-weight: 600;
    }
    
    /* Product Grid */
    .product-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 0.75rem;
      padding: 0.75rem;
      background: #f5f5f5;
    }
    
    .product-card {
      background: white;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 2px 4px rgba(0,0,0,0.05);
      transition: transform 0.2s, box-shadow 0.2s;
      text-decoration: none;
      color: inherit;
      display: block;
    }
    
    .product-card:active {
      transform: scale(0.98);
    }
    
    .product-card .product-image {
      width: 100%;
      height: 140px;
      object-fit: cover;
      background: #f0f0f0;
    }
    
    .product-card .product-badge {
      position: absolute;
      top: 0.5rem;
      right: 0.5rem;
      background: #dc3545;
      color: white;
      padding: 0.25rem 0.5rem;
      border-radius: 6px;
      font-size: 0.7rem;
      font-weight: 600;
    }
    
    .product-card .product-info {
      padding: 0.75rem;
    }
    
    .product-card .product-name {
      font-size: 0.875rem;
      font-weight: 500;
      color: #333;
      margin-bottom: 0.5rem;
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      overflow: hidden;
      line-height: 1.3;
      min-height: 2.6em;
    }
    
    .product-card .product-price {
      display: flex;
      flex-direction: column;
      gap: 0.25rem;
    }
    
    .product-card .price-current {
      font-size: 1rem;
      font-weight: 700;
      color: #147440;
    }
    
    .product-card .price-original {
      font-size: 0.75rem;
      color: #999;
      text-decoration: line-through;
    }
    
    .product-card .product-rating {
      display: flex;
      align-items: center;
      gap: 0.25rem;
      margin-top: 0.5rem;
      font-size: 0.75rem;
      color: #666;
    }
    
    /* Bottom Navigation */
    .bottom-nav {
      position: fixed;
      bottom: 0;
      left: 0;
      right: 0;
      background: white;
      border-top: 1px solid #e0e0e0;
      display: flex;
      justify-content: space-around;
      padding: 0.5rem 0;
      z-index: 1000;
      box-shadow: 0 -2px 8px rgba(0,0,0,0.05);
    }
    
    .bottom-nav-item {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 0.25rem;
      text-decoration: none;
      color: #666;
      font-size: 0.7rem;
      padding: 0.25rem 0.5rem;
      transition: color 0.2s;
      position: relative;
    }
    
    .bottom-nav-item.active {
      color: #147440;
    }
    
    .bottom-nav-item i {
      font-size: 1.25rem;
    }
    
    .bottom-nav-item .nav-badge {
      position: absolute;
      top: 0;
      right: 0;
      background: #dc3545;
      color: white;
      border-radius: 50%;
      width: 16px;
      height: 16px;
      font-size: 0.65rem;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    
    /* Category Tabs */
    .category-tabs {
      display: flex;
      gap: 0.5rem;
      overflow-x: auto;
      padding: 0.75rem 1rem;
      background: white;
      -webkit-overflow-scrolling: touch;
      scrollbar-width: none;
    }
    
    .category-tabs::-webkit-scrollbar {
      display: none;
    }
    
    .category-tab {
      padding: 0.5rem 1rem;
      border-radius: 20px;
      background: #f0f0f0;
      color: #666;
      text-decoration: none;
      font-size: 0.875rem;
      font-weight: 500;
      white-space: nowrap;
      transition: all 0.2s;
    }
    
    .category-tab.active {
      background: #147440;
      color: white;
    }
    
    /* Promo Banner */
    .promo-banner {
      margin: 0.75rem;
      background: linear-gradient(135deg, #147440 0%, #1a9c52 100%);
      border-radius: 12px;
      padding: 1rem;
      color: white;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }
    
    .promo-banner .promo-text {
      flex: 1;
    }
    
    .promo-banner .promo-text strong {
      display: block;
      font-size: 0.875rem;
      margin-bottom: 0.25rem;
    }
    
    .promo-banner .promo-text small {
      font-size: 0.75rem;
      opacity: 0.9;
    }
    
    .promo-banner .promo-btn {
      background: #dc3545;
      color: white;
      border: none;
      padding: 0.5rem 1rem;
      border-radius: 8px;
      font-weight: 600;
      font-size: 0.875rem;
    }
    
    /* Horizontal Scroll Container */
    .scroll-container {
      display: flex;
      gap: 0.75rem;
      overflow-x: auto;
      padding: 1rem;
      background: white;
      -webkit-overflow-scrolling: touch;
      scrollbar-width: none;
    }
    
    .scroll-container::-webkit-scrollbar {
      display: none;
    }
    
    /* Loading State */
    .loading {
      text-align: center;
      padding: 2rem;
      color: #666;
    }
    
    /* Empty State */
    .empty-state {
      text-align: center;
      padding: 3rem 1rem;
      color: #999;
    }
    
    .empty-state i {
      font-size: 3rem;
      margin-bottom: 1rem;
      opacity: 0.3;
    }
    
    /* Toast Notification System */
    .toast-container {
      position: fixed;
      top: 80px;
      left: 50%;
      transform: translateX(-50%);
      z-index: 10000;
      width: 90%;
      max-width: 400px;
      pointer-events: none;
    }
    
    .toast {
      background: white;
      border-radius: 12px;
      padding: 1rem;
      margin-bottom: 0.75rem;
      box-shadow: 0 4px 20px rgba(0,0,0,0.15);
      display: flex;
      align-items: center;
      gap: 0.75rem;
      animation: slideDown 0.3s ease-out;
      pointer-events: auto;
      border-left: 4px solid;
    }
    
    .toast.success {
      border-left-color: #147440;
    }
    
    .toast.error {
      border-left-color: #dc3545;
    }
    
    .toast.warning {
      border-left-color: #ffc107;
    }
    
    .toast.info {
      border-left-color: #0d6efd;
    }
    
    .toast-icon {
      font-size: 1.5rem;
      flex-shrink: 0;
    }
    
    .toast.success .toast-icon {
      color: #147440;
    }
    
    .toast.error .toast-icon {
      color: #dc3545;
    }
    
    .toast.warning .toast-icon {
      color: #ffc107;
    }
    
    .toast.info .toast-icon {
      color: #0d6efd;
    }
    
    .toast-content {
      flex: 1;
    }
    
    .toast-title {
      font-size: 0.875rem;
      font-weight: 600;
      color: #333;
      margin-bottom: 0.25rem;
    }
    
    .toast-message {
      font-size: 0.75rem;
      color: #666;
      line-height: 1.4;
    }
    
    .toast-close {
      background: none;
      border: none;
      color: #999;
      font-size: 1.25rem;
      cursor: pointer;
      padding: 0;
      line-height: 1;
      flex-shrink: 0;
    }
    
    @keyframes slideDown {
      from {
        opacity: 0;
        transform: translateY(-20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
    
    /* Loading Overlay */
    .loading-overlay {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(0,0,0,0.5);
      z-index: 9999;
      display: none;
      align-items: center;
      justify-content: center;
    }
    
    .loading-overlay.active {
      display: flex;
      opacity: 1;
    }
    
    .loading-overlay {
      transition: opacity 0.3s ease-out;
    }
    
    .loading-spinner {
      background: white;
      border-radius: 16px;
      padding: 2rem;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 1rem;
      min-width: 150px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    }
    
    .loading-spinner i {
      font-size: 2.5rem;
      color: #147440;
    }
    
    .loading-text {
      font-size: 0.875rem;
      color: #666;
      font-weight: 500;
    }
    
    /* Skeleton Loader */
    .skeleton {
      background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
      background-size: 200% 100%;
      animation: loading 1.5s ease-in-out infinite;
      border-radius: 8px;
    }
    
    @keyframes loading {
      0% { background-position: 200% 0; }
      100% { background-position: -200% 0; }
    }
    
    .skeleton-text {
      height: 1rem;
      margin-bottom: 0.5rem;
    }
    
    .skeleton-title {
      height: 1.5rem;
      width: 60%;
      margin-bottom: 1rem;
    }
    
    .skeleton-image {
      width: 100%;
      height: 140px;
      border-radius: 12px;
      margin-bottom: 0.75rem;
    }
    
    .skeleton-card {
      background: white;
      border-radius: 12px;
      padding: 0.75rem;
      margin-bottom: 0.75rem;
    }
    
    /* Pull to Refresh */
    .pull-to-refresh {
      position: relative;
      overflow: hidden;
    }
    
    .pull-to-refresh-indicator {
      position: absolute;
      top: -60px;
      left: 50%;
      transform: translateX(-50%);
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 0.5rem;
      color: #147440;
      font-size: 0.875rem;
      transition: top 0.3s ease;
      z-index: 100;
    }
    
    .pull-to-refresh-indicator.active {
      top: 20px;
    }
    
    .pull-to-refresh-indicator i {
      font-size: 1.5rem;
      animation: spin 1s linear infinite;
    }
    
    /* Error State */
    .error-state {
      text-align: center;
      padding: 3rem 1rem;
      background: white;
      border-radius: 12px;
      margin: 1rem;
    }
    
    .error-state i {
      font-size: 3rem;
      color: #dc3545;
      margin-bottom: 1rem;
    }
    
    .error-state h5 {
      font-size: 1rem;
      font-weight: 600;
      color: #333;
      margin-bottom: 0.5rem;
    }
    
    .error-state p {
      font-size: 0.875rem;
      color: #666;
      margin-bottom: 1rem;
    }
    
    .error-state button {
      background: #147440;
      color: white;
      border: none;
      padding: 0.75rem 1.5rem;
      border-radius: 8px;
      font-weight: 600;
      font-size: 0.875rem;
      cursor: pointer;
    }
    
    /* Mobile SweetAlert Custom Styles */
    .mobile-swal-popup {
      font-size: 0.875rem !important;
    }
    
    .mobile-swal-popup .swal2-title {
      font-size: 1rem !important;
      font-weight: 700 !important;
    }
    
    .mobile-swal-popup .swal2-content {
      font-size: 0.875rem !important;
      line-height: 1.6 !important;
      text-align: left !important;
    }
    
    .mobile-swal-popup .swal2-confirm {
      font-size: 0.875rem !important;
      padding: 0.75rem 2rem !important;
    }
  </style>
  
  @stack('styles')
</head>
<body>
  <!-- Mobile Header -->
  <header class="mobile-header">
    <div class="header-top">
      <a href="{{ route('mobile.home') }}" class="logo">
        {{ config('app.name', 'SAMSAE') }}
      </a>
      <div class="header-icons">
        <a href="{{ route('mobile.notifications') }}" class="position-relative" id="notificationIcon">
          <i class="bx bx-bell"></i>
          <span class="cart-badge" id="notificationBadge" style="display: none;">0</span>
        </a>
        <a href="{{ route('mobile.wishlist') }}" class="position-relative">
          <i class="bx bx-heart"></i>
          <span class="cart-badge" id="wishlistBadge" style="display: none;">0</span>
        </a>
        <a href="{{ route('mobile.support') }}" class="position-relative">
          <i class="bx bx-support"></i>
        </a>
        <a href="{{ route('mobile.cart') }}" class="position-relative">
          <i class="bx bx-cart"></i>
          <span class="cart-badge" id="cartBadge">0</span>
        </a>
      </div>
    </div>
    <div class="mobile-search">
      <form action="{{ route('mobile.shop') }}" method="GET">
        <input type="text" name="search" placeholder="Cari produk..." value="{{ request('search') }}">
        <i class="bx bx-search search-icon"></i>
      </form>
    </div>
  </header>

  <!-- Toast Container -->
  <div class="toast-container" id="toastContainer"></div>
  
  <!-- Loading Overlay -->
  <div class="loading-overlay active" id="loadingOverlay">
    <div class="loading-spinner">
      <i class="bx bx-loader-alt bx-spin" style="font-size: 2.5rem; color: #147440;"></i>
      <div class="loading-text" id="loadingText">Memuat...</div>
    </div>
  </div>

  <!-- Main Content -->
  <main id="mainContent" class="pull-to-refresh">
    <div class="pull-to-refresh-indicator" id="pullToRefreshIndicator">
      <i class="bx bx-refresh"></i>
      <span>Melepaskan untuk refresh</span>
    </div>
    @yield('content')
  </main>

  <!-- Bottom Navigation -->
  <nav class="bottom-nav">
    <a href="{{ route('mobile.home') }}" class="bottom-nav-item {{ request()->routeIs('mobile.home') ? 'active' : '' }}">
      <i class="bx bx-home"></i>
      <span>Buat Kamu</span>
    </a>
    <a href="{{ route('mobile.shop') }}" class="bottom-nav-item {{ request()->routeIs('mobile.shop') || request()->routeIs('mobile.shop.detail') ? 'active' : '' }}">
      <i class="bx bx-shopping-bag"></i>
      <span>Produk</span>
    </a>
    <a href="{{ route('mobile.promo') }}" class="bottom-nav-item {{ request()->routeIs('mobile.promo') ? 'active' : '' }}">
      <i class="bx bx-purchase-tag"></i>
      <span>Promo</span>
    </a>
    <a href="{{ route('mobile.transactions') }}" class="bottom-nav-item {{ request()->routeIs('mobile.transactions') ? 'active' : '' }}">
      <i class="bx bx-list-ul"></i>
      <span>Transaksi</span>
    </a>
    <a href="{{ route('mobile.account') }}" class="bottom-nav-item {{ request()->routeIs('mobile.account') ? 'active' : '' }}">
      <i class="bx bx-user"></i>
      <span>Akun</span>
    </a>
  </nav>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    // Global Notification System
    window.MobileNotification = {
      show: function(message, type = 'info', title = null, duration = 4000) {
        const container = document.getElementById('toastContainer');
        if (!container) return;
        
        const icons = {
          success: 'bx-check-circle',
          error: 'bx-x-circle',
          warning: 'bx-error',
          info: 'bx-info-circle'
        };
        
        const titles = {
          success: 'Berhasil',
          error: 'Error',
          warning: 'Peringatan',
          info: 'Info'
        };
        
        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        toast.innerHTML = `
          <i class="bx ${icons[type] || icons.info} toast-icon"></i>
          <div class="toast-content">
            <div class="toast-title">${title || titles[type] || 'Notifikasi'}</div>
            <div class="toast-message">${message}</div>
          </div>
          <button class="toast-close" onclick="this.parentElement.remove()">
            <i class="bx bx-x"></i>
          </button>
        `;
        
        container.appendChild(toast);
        
        // Auto remove
        setTimeout(() => {
          toast.style.animation = 'slideDown 0.3s ease-out reverse';
          setTimeout(() => toast.remove(), 300);
        }, duration);
      },
      
      success: function(message, title = null) {
        this.show(message, 'success', title);
      },
      
      error: function(message, title = null) {
        this.show(message, 'error', title);
      },
      
      warning: function(message, title = null) {
        this.show(message, 'warning', title);
      },
      
      info: function(message, title = null) {
        this.show(message, 'info', title);
      }
    };
    
    // Global Loading System
    window.MobileLoading = {
      show: function(text = 'Memuat...') {
        const overlay = document.getElementById('loadingOverlay');
        const loadingText = document.getElementById('loadingText');
        if (overlay) {
          if (loadingText) loadingText.textContent = text;
          overlay.classList.add('active');
        }
      },
      
      hide: function() {
        const overlay = document.getElementById('loadingOverlay');
        if (overlay) {
          overlay.classList.remove('active');
        }
      }
    };
    
    // Global Error Handler
    window.MobileErrorHandler = {
      handle: function(error, context = '') {
        let message = 'Terjadi kesalahan. Silakan coba lagi.';
        
        if (error.response) {
          // Axios/Fetch error
          const status = error.response.status;
          const data = error.response.data;
          
          if (data && data.message) {
            message = data.message;
          } else if (status === 401) {
            message = 'Sesi Anda telah berakhir. Silakan login kembali.';
          } else if (status === 403) {
            message = 'Anda tidak memiliki izin untuk melakukan aksi ini.';
          } else if (status === 404) {
            message = 'Data tidak ditemukan.';
          } else if (status === 422) {
            if (data && data.errors) {
              const firstError = Object.values(data.errors)[0];
              message = Array.isArray(firstError) ? firstError[0] : firstError;
            } else {
              message = 'Data yang dimasukkan tidak valid.';
            }
          } else if (status === 500) {
            message = 'Terjadi kesalahan pada server. Silakan coba lagi nanti.';
          } else if (status >= 500) {
            message = 'Server sedang mengalami masalah. Silakan coba lagi nanti.';
          }
        } else if (error.message) {
          message = error.message;
        }
        
        MobileNotification.error(message, 'Error');
        return message;
      }
    };
    
    // Show session flash messages
    @if(session('success'))
      MobileNotification.success(@json(session('success')));
    @endif
    
    @if(session('error'))
      MobileNotification.error(@json(session('error')));
    @endif
    
    @if(session('warning'))
      MobileNotification.warning(@json(session('warning')));
    @endif
    
    @if(session('info'))
      MobileNotification.info(@json(session('info')));
    @endif
    
    @if($errors->any())
      @foreach($errors->all() as $error)
        MobileNotification.error(@json($error));
      @endforeach
    @endif
    
    // Update Cart Badge
    function updateCartBadge() {
      fetch('{{ route("api.cart.count") }}', {
        headers: {
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
      .then(response => response.json())
      .then(data => {
        const badge = document.getElementById('cartBadge');
        if (badge) {
          if (data.count > 0) {
            badge.textContent = data.count > 99 ? '99+' : data.count;
            badge.style.display = 'flex';
          } else {
            badge.style.display = 'none';
          }
        }
      })
      .catch(() => {}); // Ignore errors
    }
    
    // Update Notification Badge
    function updateNotificationBadge() {
      @auth
        fetch('{{ route("api.notifications.unread-count") }}', {
          headers: {
            'X-Requested-With': 'XMLHttpRequest'
          }
        })
        .then(response => response.json())
        .then(data => {
          const badge = document.getElementById('notificationBadge');
          if (badge) {
            if (data.count > 0) {
              badge.textContent = data.count > 99 ? '99+' : data.count;
              badge.style.display = 'flex';
            } else {
              badge.style.display = 'none';
            }
          }
        })
        .catch(() => {}); // Ignore errors
      @endauth
    }
    
    // Update Wishlist Badge
    function updateWishlistBadge() {
      @auth
        fetch('{{ route("api.wishlist.count") }}', {
          headers: {
            'X-Requested-With': 'XMLHttpRequest'
          }
        })
        .then(response => response.json())
        .then(data => {
          const badge = document.getElementById('wishlistBadge');
          if (badge) {
            if (data.count > 0) {
              badge.textContent = data.count > 99 ? '99+' : data.count;
              badge.style.display = 'flex';
            } else {
              badge.style.display = 'none';
            }
          }
        })
        .catch(() => {}); // Ignore errors
      @endauth
    }
    
    // Load badges on page load
    updateCartBadge();
    updateNotificationBadge();
    updateWishlistBadge();
    
    // Update badges every 30 seconds
    setInterval(() => {
      updateCartBadge();
      updateNotificationBadge();
      updateWishlistBadge();
    }, 30000);
    
    // Page Loading Handler - Show loading on page load, hide when ready
    (function() {
      // Loading overlay is already shown by default (active class)
      const overlay = document.getElementById('loadingOverlay');
      
      // Hide loading when page is fully loaded
      function hidePageLoading() {
        if (overlay) {
          // Add fade out animation
          overlay.style.transition = 'opacity 0.3s ease-out';
          overlay.style.opacity = '0';
          setTimeout(() => {
            overlay.classList.remove('active');
            overlay.style.opacity = '';
            overlay.style.transition = '';
          }, 300);
        }
      }
      
      // Hide loading when DOM is ready
      if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
          // Wait a bit for content to render
          setTimeout(hidePageLoading, 300);
        });
      } else {
        // DOM already ready
        setTimeout(hidePageLoading, 300);
      }
      
      // Also hide on window load (images, etc.)
      window.addEventListener('load', function() {
        hidePageLoading();
      });
      
      // Fallback: hide after max 2 seconds to prevent stuck loading
      setTimeout(hidePageLoading, 2000);
    })();
    
    // Prevent zoom on double tap
    let lastTouchEnd = 0;
    document.addEventListener('touchend', function (event) {
      const now = (new Date()).getTime();
      if (now - lastTouchEnd <= 300) {
        event.preventDefault();
      }
      lastTouchEnd = now;
    }, false);
    
    // Smooth scroll for horizontal containers
    document.querySelectorAll('.scroll-container, .category-icons, .category-tabs').forEach(container => {
      let isDown = false;
      let startX;
      let scrollLeft;
      
      container.addEventListener('mousedown', (e) => {
        isDown = true;
        container.style.cursor = 'grabbing';
        startX = e.pageX - container.offsetLeft;
        scrollLeft = container.scrollLeft;
      });
      
      container.addEventListener('mouseleave', () => {
        isDown = false;
        container.style.cursor = 'grab';
      });
      
      container.addEventListener('mouseup', () => {
        isDown = false;
        container.style.cursor = 'grab';
      });
      
      container.addEventListener('mousemove', (e) => {
        if (!isDown) return;
        e.preventDefault();
        const x = e.pageX - container.offsetLeft;
        const walk = (x - startX) * 2;
        container.scrollLeft = scrollLeft - walk;
      });
    });
    
    // Pull to Refresh
    (function() {
      const mainContent = document.getElementById('mainContent');
      const indicator = document.getElementById('pullToRefreshIndicator');
      if (!mainContent || !indicator) return;
      
      let startY = 0;
      let currentY = 0;
      let isPulling = false;
      let pullDistance = 0;
      const threshold = 80;
      
      mainContent.addEventListener('touchstart', function(e) {
        if (window.scrollY === 0) {
          startY = e.touches[0].clientY;
          isPulling = true;
        }
      }, { passive: true });
      
      mainContent.addEventListener('touchmove', function(e) {
        if (!isPulling || window.scrollY > 0) {
          isPulling = false;
          return;
        }
        
        currentY = e.touches[0].clientY;
        pullDistance = currentY - startY;
        
        if (pullDistance > 0 && pullDistance < 150) {
          if (pullDistance >= threshold) {
            indicator.classList.add('active');
            indicator.querySelector('span').textContent = 'Lepaskan untuk refresh';
          } else {
            indicator.classList.remove('active');
            indicator.querySelector('span').textContent = 'Tarik ke bawah untuk refresh';
          }
        }
      }, { passive: true });
      
      mainContent.addEventListener('touchend', function() {
        if (isPulling && pullDistance >= threshold) {
          indicator.querySelector('span').textContent = 'Memuat...';
          indicator.querySelector('i').style.animation = 'spin 1s linear infinite';
          
          // Trigger refresh event
          const refreshEvent = new CustomEvent('pulltorefresh', {
            detail: { callback: function() {
              indicator.classList.remove('active');
              indicator.querySelector('span').textContent = 'Tarik ke bawah untuk refresh';
              indicator.querySelector('i').style.animation = '';
            }}
          });
          mainContent.dispatchEvent(refreshEvent);
          
          // Fallback: reload page if no handler
          setTimeout(() => {
            if (indicator.classList.contains('active')) {
              location.reload();
            }
          }, 2000);
        } else {
          indicator.classList.remove('active');
        }
        
        isPulling = false;
        pullDistance = 0;
      }, { passive: true });
    })();
  </script>
  
  @stack('scripts')
</body>
</html>
