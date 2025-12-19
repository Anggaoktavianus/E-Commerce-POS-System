@extends('admin.layouts.app')

@section('title', 'POS Settings')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Header Section -->
  <div class="card page-header-card mb-4">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h4 class="mb-1">
            <i class="bx bx-cog me-2 text-primary"></i>POS Settings
          </h4>
          <p class="text-muted mb-0">Kelola pengaturan POS per outlet</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Outlets List -->
  <div class="card">
    <div class="card-header">
      <h5 class="mb-0">Pilih Outlet</h5>
    </div>
    <div class="card-body">
      @if($outlets->count() > 0)
        <div class="row g-3">
          @foreach($outlets as $outlet)
            <div class="col-md-6 col-lg-4">
              <div class="card border">
                <div class="card-body">
                  <h6 class="card-title">{{ $outlet->name }}</h6>
                  <p class="text-muted small mb-2">
                    <i class="bx bx-map-pin me-1"></i>{{ $outlet->address ?? 'No address' }}
                  </p>
                  <a href="{{ route('admin.pos.settings.show', $outlet->id) }}" class="btn btn-sm btn-primary w-100">
                    <i class="bx bx-cog me-1"></i>Kelola Settings
                  </a>
                </div>
              </div>
            </div>
          @endforeach
        </div>
      @else
        <div class="text-center py-5">
          <i class="bx bx-store fs-1 text-muted mb-3"></i>
          <p class="text-muted">Tidak ada outlet yang aktif</p>
        </div>
      @endif
    </div>
  </div>
</div>
@endsection
