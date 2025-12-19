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
                        <i class="bx bx-store me-2 text-primary"></i>{{ $title }}
                    </h4>
                    <p class="text-muted mb-0">{{ $subtitle }}</p>
                </div>
                <div class="mt-2 mt-md-0">
                    <a href="{{ route('admin.stores.stores') }}" class="btn btn-primary btn-modern">
                        <i class="bx bx-store me-1"></i>Manajemen Toko
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row g-4 mb-4">
        <div class="col-6 col-md-3">
            <div class="card stat-card bg-primary text-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <p class="stat-label mb-2">Total Toko</p>
                            <h3 class="stat-value mb-0 text-white">{{ $totalStores }}</h3>
                        </div>
                        <div class="stat-icon">
                            <i class="bx bx-store"></i>
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
                            <p class="stat-label mb-2">Toko Aktif</p>
                            <h3 class="stat-value mb-0 text-white">{{ $activeStores }}</h3>
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
                            <p class="stat-label mb-2">Total Outlet</p>
                            <h3 class="stat-value mb-0 text-white">{{ $totalOutlets }}</h3>
                        </div>
                        <div class="stat-icon">
                            <i class="bx bx-map-pin"></i>
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
                            <p class="stat-label mb-2">Outlet Aktif</p>
                            <h3 class="stat-value mb-0 text-white">{{ $activeOutlets ?? 0 }}</h3>
                        </div>
                        <div class="stat-icon">
                            <i class="bx bx-check"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-modern">
        <div class="card-header">
            <h5 class="card-title mb-0 fw-bold">
                <i class="bx bx-list-ul me-2"></i>Daftar Toko
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-4">
                <div class="col-md-6">
                    <a href="{{ route('admin.stores.stores') }}" class="btn btn-primary btn-modern w-100">
                        <i class="bx bx-store me-2"></i>
                        <span>Manajemen Toko</span>
                    </a>
                </div>
                <div class="col-md-6">
                    <a href="{{ route('admin.outlets.index') }}" class="btn btn-info btn-modern w-100">
                        <i class="bx bx-map-pin me-2"></i>
                        <span>Manajemen Outlet</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
