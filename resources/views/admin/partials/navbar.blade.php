<nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme" id="layout-navbar">
  <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
    <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
      <i class="bx bx-menu bx-sm text-primary"></i>
    </a>
  </div>
  <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
    <div class="navbar-nav align-items-center">
      <div class="nav-item d-flex align-items-center">
        <i class="bx bx-search fs-4 lh-0 text-primary"></i>
        <input type="text" class="form-control border-0 shadow-none ms-2" placeholder="Cari data..." aria-label="Search..." />
      </div>
    </div>
    <ul class="navbar-nav flex-row align-items-center ms-auto">
      <!-- Store Switcher -->
      <li class="nav-item me-3">
        @php
          $__stores = \Illuminate\Support\Facades\DB::table('stores')->where('is_active', true)->orderBy('name')->get();
        @endphp
        <select id="admin-store-switcher" class="form-select form-select-sm">
          <option value="">— Store Saat Ini —</option>
          @foreach($__stores as $s)
            <option value="{{ $s->id }}">{{ $s->name }} ({{ $s->code }})</option>
          @endforeach
        </select>
      </li>
      <!-- Quick Actions -->
      <li class="nav-item me-3">
        <div class="btn-group">
          <button type="button" class="btn btn-outline-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown">
            <i class="bx bx-plus-circle me-1"></i> Cepat
          </button>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="{{ route('admin.products.create') }}">
              <i class="bx bx-package me-2"></i>Produk Baru
            </a></li>
            <li><a class="dropdown-item" href="{{ route('admin.pages.create') }}">
              <i class="bx bx-file me-2"></i>Halaman Baru
            </a></li>
            <li><a class="dropdown-item" href="{{ route('admin.settings.create') }}">
              <i class="bx bx-cog me-2"></i>Pengaturan Baru
            </a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="{{ url('/') }}" target="_blank">
              <i class="bx bx-external-link me-2"></i>Lihat Website
            </a></li>
          </ul>
        </div>
      </li>
      
      <!-- Notifications -->
      <li class="nav-item me-3">
        <a class="nav-link position-relative" href="javascript:void(0);">
          <i class="bx bx-bell fs-4 text-primary"></i>
          <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
            3
            <span class="visually-hidden">Notifikasi baru</span>
          </span>
        </a>
      </li>
      
      <!-- View Site -->
      <li class="nav-item me-3">
        <a href="{{ url('/') }}" class="btn btn-sm btn-outline-primary" target="_blank">
          <i class="bx bx-external-link me-1"></i> Lihat Website
        </a>
      </li>
      
      <!-- User Menu -->
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" data-bs-toggle="dropdown">
          <div class="avatar avatar-online">
            <img src="{{ asset('sneat/assets/img/avatars/1.png') }}" alt class="w-px-40 h-auto rounded-circle" />
          </div>
        </a>
        <ul class="dropdown-menu dropdown-menu-end">
          <li>
            <div class="dropdown-item d-flex align-items-center">
              <div class="me-3">
                <div class="avatar avatar-online">
                  <img src="{{ asset('sneat/assets/img/avatars/1.png') }}" alt class="w-px-40 h-auto rounded-circle" />
                </div>
              </div>
              <div>
                <h6 class="mb-0">{{ auth()->user()->name ?? 'Admin User' }}</h6>
                <small class="text-muted">{{ auth()->user()->email ?? 'admin@example.com' }}</small>
              </div>
            </div>
          </li>
          <li><hr class="dropdown-divider"></li>
          <li><a class="dropdown-item" href="#">
            <i class="bx bx-user me-2"></i> Profil Saya
          </a></li>
          <li><a class="dropdown-item" href="#">
            <i class="bx bx-cog me-2"></i> Pengaturan
          </a></li>
          <li><a class="dropdown-item" href="#">
            <i class="bx bx-help-circle me-2"></i> Bantuan
          </a></li>
          <li><hr class="dropdown-divider"></li>
          <li>
            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button type="submit" class="dropdown-item text-danger">
                <i class="bx bx-power-off me-2"></i> Keluar
              </button>
            </form>
          </li>
        </ul>
      </li>
    </ul>
  </div>
</nav>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
  const sel = document.getElementById('admin-store-switcher');
  if (!sel) return;
  // init from localStorage
  const saved = localStorage.getItem('admin.store_id');
  if (saved && sel.querySelector(`option[value="${saved}"]`)) {
    sel.value = saved;
  }
  sel.addEventListener('change', function(){
    const val = this.value || '';
    localStorage.setItem('admin.store_id', val);
    // propagate to page-level filter if exists
    const pageFilter = document.getElementById('filter-store');
    if (pageFilter) {
      pageFilter.value = val;
      const event = new Event('change');
      pageFilter.dispatchEvent(event);
    }
  });
});
</script>
@endpush
