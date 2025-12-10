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
                        <div class="col-md-6">
                            <div class="card border-primary">
                                <div class="card-body text-center">
                                    <i class="bx bx-truck display-4 text-primary"></i>
                                    <h6 class="mt-3">Metode Pengiriman</h6>
                                    <p class="text-muted small">Kelola layanan pengiriman yang tersedia</p>
                                    <a href="{{ route('admin.shipping.methods') }}" class="btn btn-primary btn-sm">
                                        <i class="bx bx-list-ul me-1"></i>Daftar Metode
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-success">
                                <div class="card-body text-center">
                                    <i class="bx bx-dollar-circle display-4 text-success"></i>
                                    <h6 class="mt-3">Biaya Pengiriman</h6>
                                    <p class="text-muted small">Atur biaya pengiriman per rute</p>
                                    <a href="{{ route('admin.shipping.costs') }}" class="btn btn-success btn-sm">
                                        <i class="bx bx-money me-1"></i>Daftar Biaya
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="row">
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h3 class="text-primary mb-0">{{ \App\Models\ShippingMethod::count() }}</h3>
                                    <small class="text-muted">Total Metode</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h3 class="text-success mb-0">{{ \App\Models\ShippingMethod::where('is_active', true)->count() }}</h3>
                                    <small class="text-muted">Metode Aktif</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h3 class="text-info mb-0">{{ \App\Models\ShippingCost::count() }}</h3>
                                    <small class="text-muted">Total Biaya</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h3 class="text-warning mb-0">{{ \App\Models\ShippingCost::where('is_active', true)->count() }}</h3>
                                    <small class="text-muted">Biaya Aktif</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
