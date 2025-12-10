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
                    <form method="POST" action="{{ route('admin.stores.stores.store') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Nama Toko *</label>
                                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="code" class="form-label">Kode Toko *</label>
                                    <input type="text" class="form-control" id="code" name="code" value="{{ old('code') }}" required placeholder="Contoh: ABC001">
                                    @error('code')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="owner_name" class="form-label">Nama Pemilik *</label>
                                    <input type="text" class="form-control" id="owner_name" name="owner_name" value="{{ old('owner_name') }}" required>
                                    @error('owner_name')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email *</label>
                                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
                                    @error('email')
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
                                
                                <div class="mb-3">
                                    <label for="tax_id" class="form-label">NPWP</label>
                                    <input type="text" class="form-control" id="tax_id" name="tax_id" value="{{ old('tax_id') }}" placeholder="Contoh: 12.345.678.9-012.345">
                                    @error('tax_id')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="business_license" class="form-label">SIUP</label>
                                    <input type="text" class="form-control" id="business_license" name="business_license" value="{{ old('business_license') }}" placeholder="Nomor SIUP">
                                    @error('business_license')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="logo_url" class="form-label">URL Logo</label>
                                    <input type="url" class="form-control" id="logo_url" name="logo_url" value="{{ old('logo_url') }}" placeholder="https://example.com/logo.png">
                                    @error('logo_url')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
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
                            <a href="{{ route('admin.stores.stores') }}" class="btn btn-secondary">
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
      const $prov = $('#loc_provinsi_id');
      (json.data||[]).forEach(it=>{ $prov.append(new Option(it.name, it.id, false, false)); });
      if (window.jQuery && $.fn.select2) { try { $prov.select2('destroy'); } catch(e){} $prov.select2({ width: '100%' }); }
      if (oldProv) { $prov.val(oldProv).trigger('change'); loadKab(oldProv, true); }
    });
  $('#loc_provinsi_id').on('change select2:select', function(){ loadKab(this.value,false); });
  $('#loc_kabkota_id').on('change select2:select', function(){ loadKec(this.value,false); });
  $('#loc_kecamatan_id').on('change select2:select', function(){ loadDesa(this.value,false); });
  function resetSelect(sel, ph){ sel.innerHTML = `<option value="">${ph}</option>`; }
  function loadKab(pid, restoring){
    resetSelect(kabSel,'Pilih Kab/Kota'); resetSelect(kecSel,'Pilih Kecamatan'); resetSelect(desaSel,'Pilih Desa/Kelurahan');
    if(!pid) return;
    fetch(`{{ url('/api/locations/kabkotas') }}/${pid}`).then(r=>r.json()).then(j=>{
      const $kab = $('#loc_kabkota_id');
      (j.data||[]).forEach(it=>{ $kab.append(new Option(it.name, it.id, false, false)); });
      if (window.jQuery && $.fn.select2) { try { $kab.select2('destroy'); } catch(e){} $kab.select2({ width: '100%' }); }
      if(restoring && oldKab){ $kab.val(oldKab); loadKec(oldKab,true); }
    });
  }
  function loadKec(kid, restoring){
    resetSelect(kecSel,'Pilih Kecamatan'); resetSelect(desaSel,'Pilih Desa/Kelurahan');
    if(!kid) return;
    fetch(`{{ url('/api/locations/kecamatans') }}/${kid}`).then(r=>r.json()).then(j=>{
      const $kec = $('#loc_kecamatan_id');
      (j.data||[]).forEach(it=>{ $kec.append(new Option(it.name, it.id, false, false)); });
      if(restoring && oldKec){ $kec.val(oldKec).trigger('change'); loadDesa(oldKec,true);} 
    });
  }
  function loadDesa(did, restoring){
    resetSelect(desaSel,'Pilih Desa/Kelurahan');
    if(!did) return;
    fetch(`{{ url('/api/locations/desas') }}/${did}`).then(r=>r.json()).then(j=>{
      const $desa = $('#loc_desa_id');
      (j.data||[]).forEach(it=>{ $desa.append(new Option(it.name, it.id, false, false)); });
      if (window.jQuery && $.fn.select2) { try { $desa.select2('destroy'); } catch(e){} $desa.select2({ width: '100%' }); }
      if(restoring && oldDesa){ $desa.val(oldDesa).trigger('change'); }
    });
  }
  // Initialize Select2
  $('#loc_provinsi_id, #loc_kabkota_id, #loc_kecamatan_id, #loc_desa_id').select2({ width: '100%' });
});
</script>
@endpush
