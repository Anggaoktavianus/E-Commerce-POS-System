<!DOCTYPE html>
<html lang="id" dir="ltr" class="light-style" data-theme="theme-default" data-assets-path="{{ asset('sneat/assets/') }}/" data-template="vertical-menu-template-no-customizer">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <!-- Permissions Policy for Geolocation (set via HTTP header, meta tag as fallback) -->
  <meta http-equiv="Permissions-Policy" content="geolocation=(self)">
  <title>@yield('title', 'Admin Panel - ' . config('app.name'))</title>
  <link rel="icon" type="image/x-icon" href="{{ asset('sneat/assets/img/favicon/favicon.ico') }}" />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('sneat/assets/vendor/fonts/boxicons.css') }}" />
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css"/>
  <link rel="stylesheet" href="{{ asset('sneat/assets/vendor/css/core.css') }}" />
  <link rel="stylesheet" href="{{ asset('sneat/assets/vendor/css/theme-default.css') }}" />
  <link rel="stylesheet" href="{{ asset('sneat/assets/css/demo.css') }}" />
  <link rel="stylesheet" href="{{ asset('sneat/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
  <link rel="stylesheet" href="https://cdn.datatables.net/2.0.7/css/dataTables.bootstrap5.min.css" />
  <!-- Select2 global CSS -->
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

  <!-- Enhanced Admin Styles -->
  <style>
    /* Loading States */
    .loading-overlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.5);
      display: none;
      justify-content: center;
      align-items: center;
      z-index: 9999;
    }
    
    .loading-overlay.active {
      display: flex;
    }
    
    .loading-spinner {
      width: 50px;
      height: 50px;
      border: 5px solid #f3f3f3;
      border-top: 5px solid #147440;
      border-radius: 50%;
      animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
    
    .skeleton {
      background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
      background-size: 200% 100%;
      animation: loading 1.5s ease-in-out infinite;
      border-radius: 4px;
    }
    
    @keyframes loading {
      0% { background-position: 200% 0; }
      100% { background-position: -200% 0; }
    }
    
    .skeleton-text {
      height: 1em;
      margin: 0.5em 0;
    }
    
    .skeleton-title {
      height: 1.5em;
      width: 60%;
      margin-bottom: 1em;
    }
    
    .skeleton-button {
      height: 2.5em;
      width: 120px;
    }
    
    .skeleton-table-row {
      height: 3em;
      margin: 0.5em 0;
    }
    
    .btn-loading {
      position: relative;
      pointer-events: none;
      opacity: 0.7;
    }
    
    .btn-loading::after {
      content: "";
      position: absolute;
      width: 16px;
      height: 16px;
      top: 50%;
      left: 50%;
      margin-left: -8px;
      margin-top: -8px;
      border: 2px solid #ffffff;
      border-radius: 50%;
      border-top-color: transparent;
      animation: spin 0.8s linear infinite;
    }
    
    .loading-inline {
      display: inline-block;
      width: 16px;
      height: 16px;
      border: 2px solid #147440;
      border-top-color: transparent;
      border-radius: 50%;
      animation: spin 0.8s linear infinite;
      margin-left: 8px;
    }
  </style>
  <style>
    /* Enhanced Admin Dashboard Styles */
    .app-brand-text {
      font-weight: 700 !important;
      color: #147440 !important;
    }

    .menu-item.active .menu-link {
      background: linear-gradient(118deg, #147440, rgba(4, 105, 24, 0.7)) !important;
      color: #fff !important;
      border-radius: 0.5rem !important;
    }

    .menu-item.active .menu-link i {
      color: #fff !important;
    }

    /* Non-active menu items - Clear differentiation */
    .menu-item:not(.active) .menu-link {
      color: #5e6670 !important;
      background: transparent !important;
      transition: all 0.3s ease !important;
    }

    .menu-item:not(.active) .menu-link:hover {
      color: #147440 !important;
      background: rgba(105, 108, 255, 0.08) !important;
    }

    .menu-item:not(.active) .menu-link i {
      color: #5e6670 !important;
      transition: color 0.3s ease !important;
    }

    .menu-item:not(.active) .menu-link:hover i {
      color: #147440 !important;
    }

    /* Sub-menu items - Enhanced differentiation */
    .menu-sub .menu-item:not(.active) .menu-link {
      color: #697a8d !important;
      background: transparent !important;
      border-radius: 0.375rem !important;
      margin: 0.125rem 0 !important;
    }

    .menu-sub .menu-item:not(.active) .menu-link:hover {
      color: #147440 !important;
      background: rgba(105, 108, 255, 0.06) !important;
    }

    .menu-sub .menu-item:not(.active) .menu-link i {
      color: #697a8d !important;
    }

    .menu-sub .menu-item:not(.active) .menu-link:hover i {
      color: #147440 !important;
    }

    /* Active sub-menu items */
    .menu-sub .menu-item.active .menu-link {
      background: linear-gradient(118deg, #147440, rgba(105, 108, 255, 0.7)) !important;
      color: #fff !important;
    }

    .menu-sub .menu-item.active .menu-link i {
      color: #fff !important;
    }

    /* Remove dots/bullets from submenu and sub-submenu - FORCE REMOVAL */
    .menu-sub,
    .menu-sub ul,
    .menu-sub .menu-sub,
    .menu-sub .menu-sub ul {
      list-style: none !important;
      list-style-type: none !important;
      padding-left: 0 !important;
      margin-left: 0 !important;
    }

    .menu-sub li,
    .menu-sub .menu-item,
    .menu-sub .menu-sub li,
    .menu-sub .menu-sub .menu-item,
    .menu-sub ul li,
    .menu-sub .menu-sub ul li {
      list-style: none !important;
      list-style-type: none !important;
    }

    .menu-sub li::before,
    .menu-sub .menu-item::before,
    .menu-sub .menu-sub li::before,
    .menu-sub .menu-sub .menu-item::before,
    .menu-sub ul li::before,
    .menu-sub .menu-sub ul li::before {
      content: none !important;
      display: none !important;
    }

    .menu-sub li::after,
    .menu-sub .menu-item::after,
    .menu-sub .menu-sub li::after,
    .menu-sub .menu-sub .menu-item::after {
      content: none !important;
      display: none !important;
    }

    .menu-sub li::marker,
    .menu-sub .menu-item::marker,
    .menu-sub .menu-sub li::marker,
    .menu-sub .menu-sub .menu-item::marker {
      display: none !important;
      content: none !important;
    }

    /* Additional fix for any remaining bullets */
    .menu-inner .menu-sub,
    .menu-inner .menu-sub ul {
      list-style: none !important;
      list-style-type: none !important;
    }

    .menu-inner .menu-sub li,
    .menu-inner .menu-sub .menu-item {
      list-style: none !important;
      list-style-type: none !important;
    }

    .menu-inner .menu-sub li::marker,
    .menu-inner .menu-sub .menu-item::marker {
      display: none !important;
      content: '' !important;
    }

    /* Enhanced Card Styles */
    .card {
      border: 1px solid #e7e7e7 !important;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04) !important;
      transition: all 0.3s ease !important;
    }

    .card:hover {
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08) !important;
      transform: translateY(-2px) !important;
    }

    /* Enhanced Button Styles */
    .btn-primary {
      background: linear-gradient(118deg, #147440, rgba(105, 108, 255, 0.7)) !important;
      border: none !important;
      transition: all 0.3s ease !important;
    }

    .btn-primary:hover {
      transform: translateY(-1px) !important;
      box-shadow: 0 4px 8px rgba(105, 108, 255, 0.3) !important;
    }

    /* Enhanced Navbar */
    .bg-navbar-theme {
      background: linear-gradient(118deg, #f8f8f8, #ffffff) !important;
      border-bottom: 1px solid #e7e7e7 !important;
    }

    /* Enhanced Form Controls */
    .form-control:focus {
      border-color: #147440 !important;
      box-shadow: 0 0 0 0.2rem rgba(105, 108, 255, 0.25) !important;
    }

    /* Enhanced DataTable */
    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
      background: linear-gradient(118deg, #147440, rgba(105, 108, 255, 0.7)) !important;
      border: none !important;
    }

    /* Enhanced Stats Cards */
    .stats-card {
      background: linear-gradient(118deg, #fff, #f8f9fa) !important;
      border-left: 4px solid #147440 !important;
    }

    /* Enhanced User Avatar */
    .avatar img {
      border: 2px solid #147440 !important;
    }

    /* Enhanced Search Box */
    .navbar-nav .form-control {
      background: rgba(105, 108, 255, 0.1) !important;
      border: 1px solid rgba(105, 108, 255, 0.2) !important;
    }

    .navbar-nav .form-control:focus {
      background: #fff !important;
      border-color: #147440 !important;
    }

    /* Store Switcher */
    .store-switcher {
      margin-left: 10px;
    }

    /* Select2 match Bootstrap form-control */
    .select2-container { width: 100% !important; }
    .select2-container .select2-selection--single {
      height: calc(2.375rem + 2px) !important; /* .form-control height */
      border: 1px solid #ced4da !important;
      border-radius: 0.375rem !important;
      display: flex !important;
      align-items: center !important;
      padding: 0 0.75rem !important;
      background-color: #fff !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
      color: #566a7f !important;
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
      box-shadow: 0 0 0 .25rem rgba(105,108,255,.25) !important;
      outline: 0 !important;
    }
    .select2-dropdown { border-color: #ced4da !important; }
    .select2-results__option { padding: 0.375rem 0.75rem !important; }

    /* ============================================
       MODERN ADMIN UI - GLOBAL STYLES
       ============================================ */

    /* Modern Stat Cards */
    .stat-card {
      border: none;
      border-radius: 16px;
      transition: all 0.3s ease;
      overflow: hidden;
      position: relative;
      box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }

    .stat-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 8px 24px rgba(0,0,0,0.12);
    }

    .stat-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: linear-gradient(90deg, rgba(255,255,255,0.3), rgba(255,255,255,0.1));
    }

    .stat-card .stat-icon {
      width: 64px;
      height: 64px;
      border-radius: 16px;
      display: flex;
      align-items: center;
      justify-content: center;
      background: rgba(255,255,255,0.2);
      backdrop-filter: blur(10px);
      font-size: 28px;
    }

    .stat-card .stat-value {
      font-size: 2.5rem;
      font-weight: 700;
      line-height: 1.2;
      margin: 0.5rem 0;
    }

    .stat-card .stat-label {
      font-size: 0.875rem;
      opacity: 0.9;
      font-weight: 500;
    }

    .stat-card .stat-change {
      font-size: 0.75rem;
      margin-top: 0.5rem;
      opacity: 0.8;
    }

    /* Gradient Backgrounds for Stat Cards */
    .stat-card.bg-primary {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .stat-card.bg-success {
      background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    }

    .stat-card.bg-info {
      background: linear-gradient(135deg, #3494E6 0%, #EC6EAD 100%);
    }

    .stat-card.bg-warning {
      background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }

    .stat-card.bg-danger {
      background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
    }

    /* Modern Header Card */
    .page-header-card {
      border: none;
      border-radius: 16px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.08);
      background: #fff;
    }

    .page-header-card .card-body {
      padding: 1.5rem;
    }

    .page-header-card h4 {
      font-weight: 700;
      margin-bottom: 0.5rem;
    }

    .page-header-card .text-muted {
      font-size: 0.9rem;
    }

    /* Modern Alert */
    .alert-modern {
      border: none;
      border-radius: 16px;
      border-left: 4px solid;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    /* Modern Table */
    .table-modern {
      border-radius: 12px;
      overflow: hidden;
    }

    .table-modern thead {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
    }

    .table-modern tbody tr {
      transition: all 0.2s ease;
    }

    .table-modern tbody tr:hover {
      background: #f8f9fa;
      transform: scale(1.01);
    }

    /* Modern Card */
    .card-modern {
      border: none;
      border-radius: 16px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.08);
      transition: all 0.3s ease;
    }

    .card-modern:hover {
      box-shadow: 0 4px 16px rgba(0,0,0,0.12);
    }

    .card-modern .card-header {
      background: #fff;
      border-bottom: 1px solid #e7e7e7;
      border-radius: 16px 16px 0 0;
      padding: 1.25rem 1.5rem;
    }

    .card-modern .card-body {
      padding: 1.5rem;
    }

    /* Welcome Card */
    .welcome-card {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      border-radius: 20px;
      border: none;
      color: white;
      overflow: hidden;
      position: relative;
    }

    .welcome-card::before {
      content: '';
      position: absolute;
      top: -50%;
      right: -50%;
      width: 200%;
      height: 200%;
      background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
      animation: pulse 4s ease-in-out infinite;
    }

    @keyframes pulse {
      0%, 100% { transform: scale(1); opacity: 0.5; }
      50% { transform: scale(1.1); opacity: 0.8; }
    }

    .welcome-card .card-body {
      position: relative;
      z-index: 1;
    }

    /* Order Item */
    .order-item {
      padding: 1rem;
      border-radius: 12px;
      background: #f8f9fa;
      margin-bottom: 0.75rem;
      transition: all 0.2s ease;
      border-left: 4px solid transparent;
    }

    .order-item:hover {
      background: #e9ecef;
      border-left-color: #667eea;
      transform: translateX(4px);
    }

    .order-item:last-child {
      margin-bottom: 0;
    }

    /* Payment Card */
    .payment-card {
      background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
      border-radius: 20px;
      border: none;
      color: white;
    }

    /* Empty State */
    .empty-state {
      padding: 3rem 1rem;
      text-align: center;
    }

    .empty-state i {
      font-size: 4rem;
      opacity: 0.3;
      margin-bottom: 1rem;
    }

    /* Responsive */
    @media (max-width: 768px) {
      .stat-card .stat-value {
        font-size: 2rem;
      }

      .stat-card .stat-icon {
        width: 48px;
        height: 48px;
        font-size: 24px;
      }

      .page-header-card .card-body {
        padding: 1rem;
      }
    }

    /* Enhanced Form Card */
    .form-card {
      border: none;
      border-radius: 16px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }

    .form-card .card-header {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      border-radius: 16px 16px 0 0;
      padding: 1.25rem 1.5rem;
    }

    /* Enhanced Button */
    .btn-modern {
      border-radius: 12px;
      padding: 0.625rem 1.25rem;
      font-weight: 500;
      transition: all 0.3s ease;
    }

    .btn-modern:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    /* Enhanced Badge */
    .badge-modern {
      padding: 0.5rem 0.75rem;
      border-radius: 8px;
      font-weight: 500;
    }

    /* Search Card */
    .search-card {
      border: none;
      border-radius: 16px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.08);
      background: #f8f9fa;
    }

    .search-card .card-body {
      padding: 1.25rem;
    }
  </style>

  @stack('styles')
</head>
<body class="layout-menu-fixed">
  <div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
      @include('admin.partials.sidebar')
      <div class="layout-page">
        @include('admin.partials.navbar')
        <div class="content-wrapper">
          <!-- Global Loading Overlay -->
  <div id="globalLoadingOverlay" class="loading-overlay">
    <div class="text-center">
      <div class="loading-spinner mb-3"></div>
      <p class="text-white">Memproses...</p>
    </div>
  </div>

  @yield('content')
          @include('admin.partials.footer')
        </div>
      </div>
    </div>
  </div>

  <script src="{{ asset('sneat/assets/vendor/js/helpers.js') }}"></script>
  <script src="{{ asset('sneat/assets/js/config.js') }}"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="{{ asset('sneat/assets/vendor/js/bootstrap.js') }}"></script>
  <script src="{{ asset('sneat/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
  <script src="{{ asset('sneat/assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
  <script src="{{ asset('sneat/assets/vendor/js/menu.js') }}"></script>
  <script src="{{ asset('sneat/assets/js/main.js') }}"></script>
  <script src="https://cdn.datatables.net/2.0.7/js/dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/2.0.7/js/dataTables.bootstrap5.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <!-- Select2 global JS -->
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function(){
      if (window.jQuery && $.fn.select2) {
        // Auto init for any select marked
        $('[data-select2], .js-select2').each(function(){
          const $el = $(this);
          if (!$el.data('select2')) {
            $el.select2({ width: '100%' });
          }
        });
        // Common location selects if present
        ['#loc_provinsi_id','#loc_kabkota_id','#loc_kecamatan_id','#loc_desa_id'].forEach(function(sel){
          const $el = $(sel);
          if ($el.length && !$el.data('select2')) {
            $el.select2({ width: '100%' });
          }
        });
      }
    });
  </script>

  <!-- Enhanced Admin JavaScript -->
  <script>
    document.addEventListener('DOMContentLoaded', function(){
      // Enhanced SweetAlert notifications
      // Toast notification helper
      // Loading States Helper Functions
      function showGlobalLoading(message = 'Memproses...') {
        const overlay = document.getElementById('globalLoadingOverlay');
        if (overlay) {
          const textElement = overlay.querySelector('p');
          if (textElement) textElement.textContent = message;
          overlay.classList.add('active');
        }
      }

      function hideGlobalLoading() {
        const overlay = document.getElementById('globalLoadingOverlay');
        if (overlay) {
          overlay.classList.remove('active');
        }
      }

      function setButtonLoading(button, loading = true) {
        if (!button) return;
        
        if (loading) {
          button.classList.add('btn-loading');
          button.disabled = true;
          button.setAttribute('data-original-text', button.innerHTML);
          button.innerHTML = '<span class="loading-inline"></span> Memproses...';
        } else {
          button.classList.remove('btn-loading');
          button.disabled = false;
          const originalText = button.getAttribute('data-original-text');
          if (originalText) {
            button.innerHTML = originalText;
            button.removeAttribute('data-original-text');
          }
        }
      }

      function showSkeletonLoading(container, count = 3) {
        if (!container) return;
        
        const skeletonHTML = Array(count).fill(0).map(() => `
          <div class="skeleton skeleton-table-row"></div>
        `).join('');
        
        container.innerHTML = skeletonHTML;
        container.classList.add('skeleton-container');
      }

      function hideSkeletonLoading(container) {
        if (!container) return;
        container.classList.remove('skeleton-container');
      }

      function showToast(type, message, title = null) {
        const Toast = Swal.mixin({
          toast: true,
          position: 'top-end',
          showConfirmButton: false,
          timer: 3000,
          timerProgressBar: true,
          didOpen: (toast) => {
            toast.onmouseenter = Swal.stopTimer;
            toast.onmouseleave = Swal.resumeTimer;
          }
        });

        const config = {
          icon: type,
          title: title || message,
          text: title ? message : null
        };

        Toast.fire(config);
      }

      // Auto show session messages
      @if(session('success'))
        showToast('success', '{{ session('success') }}');
      @endif

      @if(session('error'))
        showToast('error', '{{ session('error') }}');
      @endif

      @if(session('warning'))
        showToast('warning', '{{ session('warning') }}');
      @endif

      @if(session('info'))
        showToast('info', '{{ session('info') }}');
      @endif

      // Global error handler for AJAX requests
      $(document).ajaxError(function(event, xhr, settings, thrownError) {
        let message = 'Terjadi kesalahan. Silakan coba lagi.';
        
        if (xhr.responseJSON && xhr.responseJSON.message) {
          message = xhr.responseJSON.message;
        } else if (xhr.status === 422) {
          const errors = xhr.responseJSON?.errors;
          if (errors) {
            const firstError = Object.values(errors)[0];
            message = Array.isArray(firstError) ? firstError[0] : firstError;
          } else {
            message = 'Validasi gagal. Periksa kembali data yang diinput.';
          }
        } else if (xhr.status === 403) {
          message = 'Anda tidak memiliki izin untuk melakukan aksi ini.';
        } else if (xhr.status === 404) {
          message = 'Data tidak ditemukan.';
        } else if (xhr.status === 500) {
          message = 'Terjadi kesalahan server. Silakan hubungi administrator.';
        }

        showToast('error', message);
      });

      // Global success handler for AJAX requests
      $(document).ajaxSuccess(function(event, xhr, settings) {
        if (xhr.responseJSON && xhr.responseJSON.success && xhr.responseJSON.message) {
          showToast('success', xhr.responseJSON.message);
        }
      });
      @if(session('success'))
        Swal.fire({
          icon: 'success',
          title: 'Berhasil!',
          text: @json(session('success')),
          confirmButtonText: 'OK',
          confirmButtonColor: '#147440',
          timer: 3000,
          timerProgressBar: true
        });
      @endif
      @if(session('error'))
        Swal.fire({
          icon: 'error',
          title: 'Error!',
          text: @json(session('error')),
          confirmButtonText: 'OK',
          confirmButtonColor: '#dc3545'
        });
      @endif
      @if(session('info'))
        Swal.fire({
          icon: 'info',
          title: 'Info',
          text: @json(session('info')),
          confirmButtonText: 'OK',
          confirmButtonColor: '#147440'
        });
      @endif

      // Enhanced DataTable initialization
      if ($.fn.DataTable) {
        $.extend(true, $.fn.dataTable.defaults, {
          language: {
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_ hingga _END_ dari _TOTAL_ data",
            infoEmpty: "Menampilkan 0 hingga 0 dari 0 data",
            infoFiltered: "(difilter dari _MAX_ total data)",
            zeroRecords: "Tidak ada data yang ditemukan",
            emptyTable: "Tidak ada data tersedia",
            paginate: {
              first: "Pertama",
              last: "Terakhir",
              next: "Selanjutnya",
              previous: "Sebelumnya"
            }
          },
          pageLength: 10,
          responsive: true,
          dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip'
        });
      }

      // Enhanced form validation
      const forms = document.querySelectorAll('form');
      forms.forEach(form => {
        form.addEventListener('submit', function(e) {
          const submitBtn = form.querySelector('button[type="submit"]');
          if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="bx bx-loader bx-spin"></i> Memproses...';

            // Re-enable after 5 seconds (fallback)
            setTimeout(() => {
              submitBtn.disabled = false;
              submitBtn.innerHTML = submitBtn.getAttribute('data-original-text') || 'Simpan';
            }, 5000);
          }
        });
      });

      // Enhanced confirmations
      document.querySelectorAll('[data-confirm]').forEach(element => {
        element.addEventListener('click', function(e) {
          e.preventDefault();
          const message = this.getAttribute('data-confirm') || 'Apakah Anda yakin?';
          const form = this.closest('form');

          Swal.fire({
            title: 'Konfirmasi',
            text: message,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
          }).then((result) => {
            if (result.isConfirmed && form) {
              form.submit();
            }
          });
        });
      });

      // Enhanced tooltips
      if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
          return new bootstrap.Tooltip(tooltipTriggerEl);
        });
      }

      // Enhanced sidebar toggle
      const menuToggle = document.querySelector('.layout-menu-toggle');
      if (menuToggle) {
        menuToggle.addEventListener('click', function() {
          document.body.classList.toggle('layout-menu-collapsed');
        });
      }

      // Enhanced search functionality
      const searchInput = document.querySelector('.navbar-nav .form-control');
      if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
          if (e.key === 'Enter') {
            const searchTerm = this.value.trim();
            if (searchTerm) {
              // Implement global search functionality
              console.log('Searching for:', searchTerm);
            }
          }
        });
      }

      // Enhanced notification badge
      const notificationBell = document.querySelector('.nav-link .badge');
      if (notificationBell) {
        // Simulate notification updates
        setInterval(() => {
          const count = Math.floor(Math.random() * 10);
          notificationBell.textContent = count;
          notificationBell.style.display = count > 0 ? 'inline-block' : 'none';
        }, 30000);
      }
        // Find all action column cells - multiple selectors
        const selectors = [
          'table.dataTable tbody td:last-child',
          '#artikel-table tbody td:last-child',
          'table#artikel-table tbody td:last-child',
          'table.table tbody td:last-child',
          'table.table-striped tbody td:last-child'
        ];

        let actionCells = [];
        selectors.forEach(function(selector) {
          const cells = document.querySelectorAll(selector);
          cells.forEach(function(cell) {
            if (!actionCells.includes(cell)) {
              actionCells.push(cell);
            }
          });
        });

        actionCells.forEach(function(cell) {
          // FORCE: Remove all padding/margin
          cell.style.setProperty('padding-top', '0.2rem', 'important');
          cell.style.setProperty('padding-bottom', '0.2rem', 'important');
          cell.style.setProperty('padding-left', '0.5rem', 'important');
          cell.style.setProperty('padding-right', '0.5rem', 'important');
          cell.style.setProperty('margin', '0', 'important');
          cell.style.setProperty('line-height', '1', 'important');
          cell.style.setProperty('height', 'auto', 'important');
          cell.style.setProperty('min-height', '26px', 'important');
          cell.style.setProperty('max-height', '30px', 'important');
          cell.style.setProperty('vertical-align', 'middle', 'important');
          cell.style.setProperty('overflow', 'hidden', 'important');

          // Fix action buttons container
          const actionButtons = cell.querySelector('.action-buttons');
          if (actionButtons) {
            actionButtons.style.setProperty('height', '24px', 'important');
            actionButtons.style.setProperty('min-height', '24px', 'important');
            actionButtons.style.setProperty('max-height', '24px', 'important');
            actionButtons.style.setProperty('margin', '0', 'important');
            actionButtons.style.setProperty('padding', '0', 'important');
            actionButtons.style.setProperty('line-height', '1', 'important');
            actionButtons.style.setProperty('display', 'flex', 'important');
            actionButtons.style.setProperty('align-items', 'center', 'important');
          }

          // Remove any <br> tags completely
          const brTags = cell.querySelectorAll('br');
          brTags.forEach(function(br) {
            br.remove();
          });

          // Remove any whitespace text nodes
          Array.from(cell.childNodes).forEach(function(node) {
            if (node.nodeType === Node.TEXT_NODE && node.textContent.trim() === '') {
              node.remove();
            }
          });

          // Remove any empty divs or spans
          const emptyElements = cell.querySelectorAll('div:empty, span:empty');
          emptyElements.forEach(function(el) {
            if (el.textContent.trim() === '' && el.children.length === 0) {
              el.remove();
            }
          });
        });
      }

      // Run fix multiple times to catch all scenarios
      function runFix() {
        fixActionButtonsSpacing();
        setTimeout(fixActionButtonsSpacing, 50);
        setTimeout(fixActionButtonsSpacing, 100);
        setTimeout(fixActionButtonsSpacing, 200);
      }

      // Run fix after DataTables initialization
      if (typeof $.fn.DataTable !== 'undefined') {
        // Fix after initial load
        setTimeout(runFix, 100);

    });
  </script>

  @stack('scripts')
</body>
</html>
