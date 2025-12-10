@extends('admin.layouts.app')

@section('title', $title)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ $title }}</h5>
                    <small class="text-muted">{{ $subtitle }}</small>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.shipping.costs.store') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="shipping_method_id" class="form-label">Metode Pengiriman *</label>
                                    <select class="form-select" id="shipping_method_id" name="shipping_method_id" required>
                                        <option value="">Pilih Metode</option>
                                        @foreach($methods as $method)
                                            <option value="{{ $method->id }}" {{ old('shipping_method_id') == $method->id ? 'selected' : '' }}>
                                                {{ $method->name }} ({{ $method->formatted_type }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('shipping_method_id')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="cost" class="form-label">Biaya Pengiriman (IDR) *</label>
                                    <input type="number" class="form-control" id="cost" name="cost" value="{{ old('cost') }}" required min="0">
                                    @error('cost')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="origin_city" class="form-label">Kota Asal *</label>
                                    <input type="text" class="form-control" id="origin_city" name="origin_city" value="{{ old('origin_city') }}" required placeholder="contoh: Semarang">
                                    @error('origin_city')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="destination_city" class="form-label">Kota Tujuan *</label>
                                    <input type="text" class="form-control" id="destination_city" name="destination_city" value="{{ old('destination_city') }}" required placeholder="contoh: Jakarta">
                                    @error('destination_city')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="min_weight" class="form-label">Berat Minimum (kg) *</label>
                                    <input type="number" class="form-control" id="min_weight" name="min_weight" value="{{ old('min_weight', 0) }}" required min="0" step="0.1">
                                    @error('min_weight')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="max_weight" class="form-label">Berat Maksimum (kg) *</label>
                                    <input type="number" class="form-control" id="max_weight" name="max_weight" value="{{ old('max_weight', 1) }}" required min="0.1" step="0.1">
                                    @error('max_weight')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="estimated_days" class="form-label">Estimasi Waktu *</label>
                                    <input type="text" class="form-control" id="estimated_days" name="estimated_days" value="{{ old('estimated_days') }}" required placeholder="contoh: 2-3 hari">
                                    <small class="text-muted">Contoh: 60 menit, 6 jam, 2-3 hari</small>
                                    @error('estimated_days')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', 1) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Aktif
                                </label>
                            </div>
                        </div>
                        
                        <div class="alert alert-info">
                            <h6><i class="bx bx-info-circle me-2"></i>Informasi:</h6>
                            <ul class="mb-0">
                                <li>Berat minimum dan maksimum menentukan range berat yang berlaku untuk biaya ini</li>
                                <li>Estimasi waktu bisa berupa: "60 menit", "6 jam", "2-3 hari", dll</li>
                                <li>Pastikan tidak ada overlap biaya untuk metode dan rute yang sama</li>
                            </ul>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.shipping.costs') }}" class="btn btn-secondary">
                                <i class="bx bx-arrow-back me-1"></i>Kembali
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="bx bx-save me-1"></i>Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
