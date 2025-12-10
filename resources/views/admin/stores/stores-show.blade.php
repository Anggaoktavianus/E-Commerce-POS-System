@extends('admin.layouts.app')

@section('title', $title)

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">{{ $title }}</h5>
                        <small class="text-muted">{{ $subtitle }}</small>
                    </div>
                    <div>
                        <a href="{{ route('admin.stores.stores.edit', $store->id) }}" class="btn btn-warning me-2">
                            <i class="bx bx-edit me-1"></i>Edit Toko
                        </a>
                        <a href="{{ route('admin.outlets.create') }}?store_id={{ $store->id }}" class="btn btn-success">
                            <i class="bx bx-plus me-1"></i>Tambah Outlet
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="text-center mb-3">
                                @if($store->logo_url)
                                    <img src="{{ asset($store->logo_url) }}" alt="{{ $store->name }}" class="img-fluid rounded" style="max-width: 200px;">
                                @else
                                    <div class="avatar avatar-xxl bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 200px; height: 200px;">
                                        <span style="font-size: 4rem;">{{ substr($store->name, 0, 1) }}</span>
                                    </div>
                                @endif
                            </div>
                            <div class="text-center">
                                <h4>{{ $store->name }}</h4>
                                <p class="text-muted">Kode: {{ $store->code }}</p>
                                <div>{!! $store->status_badge !!}</div>
                            </div>
                        </div>
                        
                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="text-primary mb-3">Informasi Pemilik</h6>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td width="30%"><strong>Nama Pemilik:</strong></td>
                                            <td>{{ $store->owner_name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Email:</strong></td>
                                            <td>{{ $store->email }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Telepon:</strong></td>
                                            <td>{{ $store->formatted_phone }}</td>
                                        </tr>
                                        @if($store->tax_id)
                                        <tr>
                                            <td><strong>NPWP:</strong></td>
                                            <td>{{ $store->tax_id }}</td>
                                        </tr>
                                        @endif
                                        @if($store->business_license)
                                        <tr>
                                            <td><strong>SIUP:</strong></td>
                                            <td>{{ $store->business_license }}</td>
                                        </tr>
                                        @endif
                                    </table>
                                </div>
                                
                                <div class="col-md-6">
                                    <h6 class="text-primary mb-3">Alamat</h6>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td width="30%"><strong>Alamat:</strong></td>
                                            <td>{{ $store->address }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Kota:</strong></td>
                                            <td>{{ $store->city }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Provinsi:</strong></td>
                                            <td>{{ $store->province }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Kode Pos:</strong></td>
                                            <td>{{ $store->postal_code }}</td>
                                        </tr>
                                        @if($store->location_ref_text)
                                        <tr>
                                            <td><strong>Lokasi (Referensi):</strong></td>
                                            <td>{{ $store->location_ref_text }}</td>
                                        </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Daftar Outlet</h5>
                    <small class="text-muted">Total {{ $store->outlets->count() }} outlet</small>
                </div>
                <div class="card-body">
                    @if($store->outlets->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Nama Outlet</th>
                                        <th>Kode</th>
                                        <th>Tipe</th>
                                        <th>Manajer</th>
                                        <th>Lokasi</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($store->outlets as $outlet)
                                        <tr>
                                            <td>
                                                <strong>{{ $outlet->name }}</strong>
                                                @if($outlet->email)
                                                    <br><small class="text-muted">{{ $outlet->email }}</small>
                                                @endif
                                            </td>
                                            <td>{{ $outlet->code }}</td>
                                            <td>{!! $outlet->type_badge !!}</td>
                                            <td>
                                                @if($outlet->manager_name)
                                                    {{ $outlet->manager_name }}
                                                    <br><small class="text-muted">{{ $outlet->formatted_phone }}</small>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <i class="bx bx-map-pin text-muted"></i> {{ $outlet->city }}, {{ $outlet->province }}
                                                <br><small class="text-muted">{{ substr($outlet->address, 0, 50) }}...</small>
                                                @if($outlet->location_ref_text)
                                                    <br><small class="text-muted">Ref: {{ $outlet->location_ref_text }}</small>
                                                @endif
                                            </td>
                                            <td>{!! $outlet->status_badge !!}</td>
                                            <td>
                                                <a href="{{ route('admin.outlets.edit', $outlet->id) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="bx bx-edit"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bx bx-map-pin fs-1 text-muted"></i>
                            <p class="text-muted mt-2">Belum ada outlet untuk toko ini</p>
                            <a href="{{ route('admin.outlets.create') }}?store_id={{ $store->id }}" class="btn btn-primary">
                                <i class="bx bx-plus me-1"></i>Tambah Outlet Pertama
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
