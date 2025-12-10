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
                    <form method="POST" action="{{ route('admin.outlets.store') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="store_id" class="form-label">Toko *</label>
                                    <select class="form-select" id="store_id" name="store_id" required>
                                        <option value="">Pilih Toko</option>
                                        @foreach($stores as $store)
                                            <option value="{{ $store->id }}" {{ old('store_id', request('store_id')) == $store->id ? 'selected' : '' }}>
                                                {{ $store->name }} ({{ $store->code }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('store_id')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="name" class="form-label">Nama Outlet *</label>
                                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="code" class="form-label">Kode Outlet *</label>
                                    <input type="text" class="form-control" id="code" name="code" value="{{ old('code') }}" required placeholder="Contoh: ABC-SMG01">
                                    @error('code')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="type" class="form-label">Tipe Outlet *</label>
                                    <select class="form-select" id="type" name="type" required>
                                        <option value="">Pilih Tipe</option>
                                        <option value="main" {{ old('type') == 'main' ? 'selected' : '' }}>Utama</option>
                                        <option value="branch" {{ old('type') == 'branch' ? 'selected' : '' }}>Cabang</option>
                                        <option value="pickup_point" {{ old('type') == 'pickup_point' ? 'selected' : '' }}>Pickup Point</option>
                                    </select>
                                    @error('type')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="manager_name" class="form-label">Nama Manajer</label>
                                    <input type="text" class="form-control" id="manager_name" name="manager_name" value="{{ old('manager_name') }}">
                                    @error('manager_name')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Telepon *</label>
                                    <input type="tel" class="form-control" id="phone" name="phone" value="{{ old('phone') }}" required>
                                    @error('phone')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}">
                                    @error('email')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="address" class="form-label">Alamat *</label>
                                    <textarea class="form-control" id="address" name="address" rows="3" required>{{ old('address') }}</textarea>
                                    @error('address')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Provinsi (Ref) *</label>
                                            <select id="loc_provinsi_id" name="loc_provinsi_id" class="form-select" required>
                                                <option value="">Pilih Provinsi</option>
                                            </select>
                                            @error('loc_provinsi_id')<div class="text-danger small">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Kab/Kota (Ref) *</label>
                                            <select id="loc_kabkota_id" name="loc_kabkota_id" class="form-select" required>
                                                <option value="">Pilih Kab/Kota</option>
                                            </select>
                                            @error('loc_kabkota_id')<div class="text-danger small">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Kecamatan (Ref) *</label>
                                            <select id="loc_kecamatan_id" name="loc_kecamatan_id" class="form-select" required>
                                                <option value="">Pilih Kecamatan</option>
                                            </select>
                                            @error('loc_kecamatan_id')<div class="text-danger small">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Desa/Kelurahan (Ref) *</label>
                                            <select id="loc_desa_id" name="loc_desa_id" class="form-select" required>
                                                <option value="">Pilih Desa/Kelurahan</option>
                                            </select>
                                            @error('loc_desa_id')<div class="text-danger small">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="province" class="form-label">Provinsi (Teks) *</label>
                                            <input type="text" class="form-control" id="province" name="province" value="{{ old('province') }}" required>
                                            @error('province')<div class="text-danger small">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="city" class="form-label">Kota (Teks) *</label>
                                            <input type="text" class="form-control" id="city" name="city" value="{{ old('city') }}" required>
                                            @error('city')<div class="text-danger small">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="postal_code" class="form-label">Kode Pos *</label>
                                            <input type="text" class="form-control" id="postal_code" name="postal_code" value="{{ old('postal_code') }}" required>
                                            @error('postal_code')<div class="text-danger small">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="latitude" class="form-label">Latitude</label>
                                            <input type="number" step="any" class="form-control" id="latitude" name="latitude" value="{{ old('latitude') }}" placeholder="-7.7956">
                                            @error('latitude')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="longitude" class="form-label">Longitude</label>
                                            <input type="number" step="any" class="form-control" id="longitude" name="longitude" value="{{ old('longitude') }}" placeholder="110.3695">
                                            @error('longitude')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Jam Operasional</label>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-2">
                                                <label class="form-label small">Senin - Jumat</label>
                                                <div class="input-group">
                                                    <input type="time" class="form-control" name="operating_hours[weekdays][open]" value="{{ old('operating_hours.weekdays.open', '08:00') }}">
                                                    <span class="input-group-text">s/d</span>
                                                    <input type="time" class="form-control" name="operating_hours[weekdays][close]" value="{{ old('operating_hours.weekdays.close', '17:00') }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-2">
                                                <label class="form-label small">Sabtu - Minggu</label>
                                                <div class="input-group">
                                                    <input type="time" class="form-control" name="operating_hours[weekend][open]" value="{{ old('operating_hours.weekend.open', '09:00') }}">
                                                    <span class="input-group-text">s/d</span>
                                                    <input type="time" class="form-control" name="operating_hours[weekend][close]" value="{{ old('operating_hours.weekend.close', '15:00') }}">
                                                </div>
                                            </div>
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
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.outlets.index') }}" class="btn btn-secondary">
                                <i class="bx bx-arrow-back me-1"></i>Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
  const provSel = document.getElementById('loc_provinsi_id');
  const kabSel = document.getElementById('loc_kabkota_id');
  const kecSel = document.getElementById('loc_kecamatan_id');
  const desaSel = document.getElementById('loc_desa_id');
  const oldProv = '{{ old('loc_provinsi_id') }}';
  const oldKab = '{{ old('loc_kabkota_id') }}';
  const oldKec = '{{ old('loc_kecamatan_id') }}';
  const oldDesa = '{{ old('loc_desa_id') }}';

  fetch('{{ route('api.locations.provinsis') }}')
    .then(r => r.json()).then(json => {
      (json.data||[]).forEach(it=>{ const o=document.createElement('option'); o.value=it.id; o.textContent=it.name; provSel.appendChild(o); });
      if (oldProv) { provSel.value=oldProv; loadKab(oldProv, true); }
    });
  provSel.addEventListener('change', function(){ loadKab(this.value,false); });
  kabSel.addEventListener('change', function(){ loadKec(this.value,false); });
  kecSel.addEventListener('change', function(){ loadDesa(this.value,false); });
  function resetSelect(sel, ph){ sel.innerHTML = `<option value="">${ph}</option>`; }
  function loadKab(pid, restoring){ resetSelect(kabSel,'Pilih Kab/Kota'); resetSelect(kecSel,'Pilih Kecamatan'); resetSelect(desaSel,'Pilih Desa/Kelurahan'); if(!pid) return; fetch(`{{ url('/api/locations/kabkotas') }}/${pid}`).then(r=>r.json()).then(j=>{ (j.data||[]).forEach(it=>{ const o=document.createElement('option'); o.value=it.id; o.textContent=it.name; kabSel.appendChild(o); }); if (restoring && oldKab) { kabSel.value=oldKab; loadKec(oldKab,true); } }); }
  function loadKec(kid, restoring){ resetSelect(kecSel,'Pilih Kecamatan'); resetSelect(desaSel,'Pilih Desa/Kelurahan'); if(!kid) return; fetch(`{{ url('/api/locations/kecamatans') }}/${kid}`).then(r=>r.json()).then(j=>{ (j.data||[]).forEach(it=>{ const o=document.createElement('option'); o.value=it.id; o.textContent=it.name; kecSel.appendChild(o); }); if(restoring && oldKec){ kecSel.value=oldKec; loadDesa(oldKec,true);} }); }
  function loadDesa(did, restoring){ resetSelect(desaSel,'Pilih Desa/Kelurahan'); if(!did) return; fetch(`{{ url('/api/locations/desas') }}/${did}`).then(r=>r.json()).then(j=>{ (j.data||[]).forEach(it=>{ const o=document.createElement('option'); o.value=it.id; o.textContent=it.name; desaSel.appendChild(o); }); if(restoring && oldDesa){ desaSel.value=oldDesa; } }); }
});
</script>
@endpush
