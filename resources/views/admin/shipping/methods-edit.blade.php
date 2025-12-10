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
                    <form method="POST" action="{{ route('admin.shipping.methods.update', $method->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Nama Metode *</label>
                                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $method->name) }}" required>
                                    @error('name')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="code" class="form-label">Kode Metode *</label>
                                    <input type="text" class="form-control" id="code" name="code" value="{{ old('code', $method->code) }}" required placeholder="contoh: gosend_instant">
                                    @error('code')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="type" class="form-label">Tipe Metode *</label>
                                    <select class="form-select" id="type" name="type" required>
                                        <option value="">Pilih Tipe</option>
                                        <option value="instant" {{ old('type', $method->type) == 'instant' ? 'selected' : '' }}>Instan</option>
                                        <option value="same_day" {{ old('type', $method->type) == 'same_day' ? 'selected' : '' }}>Same Day</option>
                                        <option value="regular" {{ old('type', $method->type) == 'regular' ? 'selected' : '' }}>Reguler</option>
                                        <option value="express" {{ old('type', $method->type) == 'express' ? 'selected' : '' }}>Express</option>
                                        <option value="pickup" {{ old('type', $method->type) == 'pickup' ? 'selected' : '' }}>Ambil Sendiri</option>
                                    </select>
                                    @error('type')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="max_distance_km" class="form-label">Jarak Maksimal (km)</label>
                                    <input type="number" class="form-control" id="max_distance_km" name="max_distance_km" value="{{ old('max_distance_km', $method->max_distance_km) }}" placeholder="Untuk pengiriman instan">
                                    <small class="text-muted">Hanya untuk tipe Instan</small>
                                    @error('max_distance_km')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="logo_url" class="form-label">URL Logo</label>
                            <input type="url" class="form-control" id="logo_url" name="logo_url" value="{{ old('logo_url', $method->logo_url) }}" placeholder="https://example.com/logo.png">
                            <small class="text-muted">URL logo metode pengiriman (opsional)</small>
                            @error('logo_url')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Area Layanan</label>
                            <div id="serviceAreasContainer">
                                @if($method->service_areas && is_array($method->service_areas))
                                    @foreach($method->service_areas as $area)
                                        <div class="service-area-item mb-2">
                                            <div class="input-group">
                                                <input type="text" class="form-control service-area-input" name="service_areas[]" placeholder="Nama kota" value="{{ $area }}">
                                                <button type="button" class="btn btn-outline-danger" onclick="removeServiceArea(this)">
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                                @if(!$method->service_areas || !is_array($method->service_areas) || count($method->service_areas) == 0)
                                    <div class="service-area-item mb-2">
                                        <div class="input-group">
                                            <input type="text" class="form-control service-area-input" name="service_areas[]" placeholder="Nama kota" value="">
                                            <button type="button" class="btn btn-outline-danger" onclick="removeServiceArea(this)">
                                                <i class="bx bx-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm mt-2" onclick="addServiceArea()">
                                <i class="bx bx-plus me-1"></i>Tambah Area
                            </button>
                            <small class="text-muted d-block">Kota-kota yang dilayani oleh metode ini</small>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $method->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Aktif
                                </label>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.admin.shipping.methods') }}" class="btn btn-secondary">
                                <i class="bx bx-arrow-back me-1"></i>Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save me-1"></i>Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function addServiceArea() {
    const container = document.getElementById('serviceAreasContainer');
    const newItem = document.createElement('div');
    newItem.className = 'service-area-item mb-2';
    newItem.innerHTML = `
        <div class="input-group">
            <input type="text" class="form-control service-area-input" name="service_areas[]" placeholder="Nama kota" value="">
            <button type="button" class="btn btn-outline-danger" onclick="removeServiceArea(this)">
                <i class="bx bx-trash"></i>
            </button>
        </div>
    `;
    container.appendChild(newItem);
}

function removeServiceArea(button) {
    const container = document.getElementById('serviceAreasContainer');
    if (container.children.length > 1) {
        button.closest('.service-area-item').remove();
    }
}
</script>
@endpush
