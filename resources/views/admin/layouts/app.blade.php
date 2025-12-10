<!DOCTYPE html>
<html lang="id" dir="ltr" class="light-style" data-theme="theme-default" data-assets-path="{{ asset('sneat/assets/') }}/" data-template="vertical-menu-template-no-customizer">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
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
    });
  </script>
  
  @stack('scripts')
</body>
</html>
