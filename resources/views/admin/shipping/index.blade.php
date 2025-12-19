@extends('admin.layouts.app')

@section('title', $title)

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header Section -->
    <div class="card page-header-card mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h4 class="mb-1">
                        <i class="bx bx-truck me-2 text-primary"></i>{{ $title }}
                    </h4>
                    <p class="text-muted mb-0">{{ $subtitle }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card card-modern">
                <div class="card-body text-center p-4">
                    <div class="stat-icon mb-3" style="width: 80px; height: 80px; margin: 0 auto; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <i class="bx bx-truck" style="font-size: 2.5rem;"></i>
                    </div>
                    <h5 class="mb-2">Metode Pengiriman</h5>
                    <p class="text-muted mb-3">Kelola layanan pengiriman yang tersedia</p>
                    <a href="{{ route('admin.shipping.methods') }}" class="btn btn-primary btn-modern">
                        <i class="bx bx-list-ul me-1"></i>Daftar Metode
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-modern">
                <div class="card-body text-center p-4">
                    <div class="stat-icon mb-3" style="width: 80px; height: 80px; margin: 0 auto; background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
                        <i class="bx bx-dollar-circle" style="font-size: 2.5rem;"></i>
                    </div>
                    <h5 class="mb-2">Biaya Pengiriman</h5>
                    <p class="text-muted mb-3">Atur biaya pengiriman per rute</p>
                    <a href="{{ route('admin.shipping.costs') }}" class="btn btn-success btn-modern">
                        <i class="bx bx-money me-1"></i>Daftar Biaya
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Stats -->
    <div class="row g-4">
        <div class="col-6 col-md-3">
            <div class="card stat-card bg-primary text-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <p class="stat-label mb-2">Total Metode</p>
                            <h3 class="stat-value mb-0">{{ \App\Models\ShippingMethod::count() }}</h3>
                        </div>
                        <div class="stat-icon">
                            <i class="bx bx-truck"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card bg-success text-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <p class="stat-label mb-2">Metode Aktif</p>
                            <h3 class="stat-value mb-0">{{ \App\Models\ShippingMethod::where('is_active', true)->count() }}</h3>
                        </div>
                        <div class="stat-icon">
                            <i class="bx bx-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card bg-info text-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <p class="stat-label mb-2">Total Biaya</p>
                            <h3 class="stat-value mb-0">{{ \App\Models\ShippingCost::count() }}</h3>
                        </div>
                        <div class="stat-icon">
                            <i class="bx bx-dollar-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card bg-warning text-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <p class="stat-label mb-2">Biaya Aktif</p>
                            <h3 class="stat-value mb-0">{{ \App\Models\ShippingCost::where('is_active', true)->count() }}</h3>
                        </div>
                        <div class="stat-icon">
                            <i class="bx bx-check"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
