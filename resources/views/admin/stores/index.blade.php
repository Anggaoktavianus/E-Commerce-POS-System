@extends('admin.layouts.app')

@section('title', $title)

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ $title }}</h5>
                    <small class="text-muted">{{ $subtitle }}</small>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="card border-primary">
                                <div class="card-body text-center">
                                    <div class="d-flex align-items-center justify-content-center mb-2">
                                        <div class="avatar avatar-xl bg-primary bg-opacity-10 text-primary rounded-circle">
                                            <i class="bx bx-store fs-3"></i>
                                        </div>
                                    </div>
                                    <h3 class="mb-1">{{ $totalStores }}</h3>
                                    <p class="mb-0">Total Toko</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-success">
                                <div class="card-body text-center">
                                    <div class="d-flex align-items-center justify-content-center mb-2">
                                        <div class="avatar avatar-xl bg-success bg-opacity-10 text-success rounded-circle">
                                            <i class="bx bx-check-circle fs-3"></i>
                                        </div>
                                    </div>
                                    <h3 class="mb-1">{{ $activeStores }}</h3>
                                    <p class="mb-0">Toko Aktif</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-info">
                                <div class="card-body text-center">
                                    <div class="d-flex align-items-center justify-content-center mb-2">
                                        <div class="avatar avatar-xl bg-info bg-opacity-10 text-info rounded-circle">
                                            <i class="bx bx-map-pin fs-3"></i>
                                        </div>
                                    </div>
                                    <h3 class="mb-1">{{ $totalOutlets }}</h3>
                                    <p class="mb-0">Total Outlet</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-warning">
                                <div class="card-body text-center">
                                    <div class="d-flex align-items-center justify-content-center mb-2">
                                        <div class="avatar avatar-xl bg-warning bg-opacity-10 text-warning rounded-circle">
                                            <i class="bx bx-store-alt fs-3"></i>
                                        </div>
                                    </div>
                                    <h3 class="mb-1">{{ $activeOutlets }}</h3>
                                    <p class="mb-0">Outlet Aktif</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <a href="{{ route('admin.stores.stores') }}" class="btn btn-primary btn-lg w-100">
                                <i class="bx bx-store me-2"></i>
                                <span>Manajemen Toko</span>
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="{{ route('admin.outlets.index') }}" class="btn btn-info btn-lg w-100">
                                <i class="bx bx-map-pin me-2"></i>
                                <span>Manajemen Outlet</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
