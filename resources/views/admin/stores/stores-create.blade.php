@extends('admin.layouts.app')

@section('title', $title)

@push('styles')
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder@1.13.0/dist/Control.Geocoder.css" />
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<style>
    * { box-sizing: border-box; }
    
    /* Page Background */
    .admin-form-page {
        background: linear-gradient(135deg, #f0f9f4 0%, #d4edda 100%);
        min-height: 100vh;
        padding: 2rem 0;
    }
    
    /* Modern Card Container */
    .admin-form-modern {
        position: relative;
        max-width: 1400px;
        margin: 0 auto;
        padding: 2rem 1rem;
    }
    
    .admin-form-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        overflow: hidden;
        animation: slideUp 0.6s ease-out;
    }
    
    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    /* Header Section */
    .admin-form-header {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        padding: 2rem;
        text-align: center;
        color: white;
        position: relative;
        overflow: hidden;
    }
    
    .admin-form-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        animation: pulse 3s ease-in-out infinite;
    }
    
    @keyframes pulse {
        0%, 100% { transform: scale(1); opacity: 0.5; }
        50% { transform: scale(1.1); opacity: 0.8; }
    }
    
    .admin-form-header h3 {
        font-size: 1.75rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        color: white !important;
        position: relative;
        z-index: 1;
    }
    
    .admin-form-header p {
        font-size: 1rem;
        opacity: 0.95;
        margin: 0;
        position: relative;
        z-index: 1;
    }
    
    /* Body Section */
    .admin-form-body {
        padding: 2.5rem;
    }
    
    @media (max-width: 768px) {
        .admin-form-body {
            padding: 1.5rem;
        }
    }
    
    /* Form Sections */
    .form-section {
        background: linear-gradient(135deg, #f8fff9 0%, #ffffff 100%);
        padding: 1.75rem;
        border-radius: 15px;
        border: 2px solid #e8f5e9;
        margin-bottom: 1.5rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
    }
    
    .form-section:hover {
        box-shadow: 0 4px 15px rgba(40, 167, 69, 0.1);
        border-color: #28a745;
    }
    
    .form-section h5 {
        color: #28a745;
        font-weight: 700;
        margin-bottom: 1rem;
        font-size: 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .form-section h5::before {
        content: '';
        width: 4px;
        height: 24px;
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        border-radius: 2px;
    }
    
    .form-section hr {
        border: none;
        border-top: 2px solid #e8f5e9;
        margin: 1rem 0 1.5rem;
    }
    
    /* Form Controls */
    .form-control, .form-select {
        border: 2px solid #e9ecef;
        border-radius: 10px;
        padding: 0.75rem 1rem;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        background: white;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #28a745;
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
        background: white;
    }
    
    .form-control.is-invalid, .form-select.is-invalid {
        border-color: #dc3545;
        background: #fff5f5;
    }
    
    .form-label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 0.5rem;
        font-size: 0.95rem;
    }
    
    .required-field::after {
        content: " *";
        color: #dc3545;
    }
    
    .invalid-feedback, .text-danger {
        color: #dc3545;
        font-size: 0.875rem;
        margin-top: 0.25rem;
        display: block;
    }
    
    /* Input Group */
    .input-group-text {
        background: linear-gradient(135deg, #f8fff9 0%, #e8f5e9 100%);
        border: 2px solid #e9ecef;
        color: #28a745;
    }
    
    /* Map Container */
    #map {
        border-radius: 12px;
        border: 2px solid #e9ecef;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
    }
    
    #map:hover {
        box-shadow: 0 4px 15px rgba(40, 167, 69, 0.15);
    }
    
    /* Buttons */
    .btn-success, .btn-sm.btn-success {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        border: none;
        color: white;
        border-radius: 10px;
        padding: 0.5rem 1.25rem;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(40, 167, 69, 0.3);
    }
    
    .btn-success:hover, .btn-sm.btn-success:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(40, 167, 69, 0.4);
        color: white;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        border: none;
        color: white;
        border-radius: 10px;
        padding: 0.75rem 2rem;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
    }
    
    .btn-primary:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(40, 167, 69, 0.4);
        color: white;
    }
    
    .btn-secondary {
        background: #6c757d;
        border: none;
        color: white;
        border-radius: 10px;
        padding: 0.75rem 2rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-secondary:hover {
        background: #5a6268;
        transform: translateY(-2px);
        color: white;
    }
    
    /* Form Check */
    .form-check-input:checked {
        background-color: #28a745;
        border-color: #28a745;
    }
    
    .form-check-input:focus {
        border-color: #28a745;
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
    }
    
    /* Select2 Customization */
    .select2-container--default .select2-selection--single {
        border: 2px solid #e9ecef;
        border-radius: 10px;
        height: auto;
        padding: 0.5rem;
    }
    
    .select2-container--default.select2-container--focus .select2-selection--single {
        border-color: #28a745;
    }
    
    /* Location Status */
    #location-status {
        font-size: 0.875rem;
        font-weight: 500;
    }
    
    /* Action Buttons */
    .action-buttons {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 2rem;
        padding-top: 1.5rem;
        border-top: 2px solid #e8f5e9;
    }
    
    @media (max-width: 768px) {
        .action-buttons {
            flex-direction: column;
            gap: 1rem;
        }
        
        .action-buttons .btn {
            width: 100%;
        }
    }
    
    /* Textarea */
    textarea.form-control {
        min-height: 100px;
        resize: vertical;
    }
</style>
@endpush

@section('content')
<div class="admin-form-page">
    <div class="admin-form-modern">
        <div class="admin-form-card">
            <div class="admin-form-header">
                <h3><i class="bx bx-store me-2"></i>{{ $title }}</h3>
                <p>{{ $subtitle }}</p>
                </div>
            <div class="admin-form-body">
                    <form method="POST" action="{{ route('admin.stores.stores.store') }}">
                        @csrf
                    
                    <!-- Informasi Toko -->
                    <div class="form-section">
                        <h5><i class="bx bx-store me-2"></i>Informasi Toko</h5>
                        <hr>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label required-field">Nama Toko</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="Masukkan nama toko" required>
                                    @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                            <div class="col-md-6">
                                <label for="short_name" class="form-label required-field">Nama Singkat</label>
                                <input type="text" class="form-control @error('short_name') is-invalid @enderror" id="short_name" name="short_name" value="{{ old('short_name') }}" placeholder="Contoh: Samsae Jogja" maxlength="20" required>
                                    <small class="form-text text-muted">Nama singkat untuk tampilan yang lebih ringkas (maksimal 20 karakter)</small>
                                    @error('short_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                            <div class="col-md-6">
                                <label for="code" class="form-label required-field">Kode Toko</label>
                                <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code') }}" placeholder="Contoh: ABC001" required>
                                    @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                            <div class="col-md-6">
                                <label for="owner_name" class="form-label required-field">Nama Pemilik</label>
                                <input type="text" class="form-control @error('owner_name') is-invalid @enderror" id="owner_name" name="owner_name" value="{{ old('owner_name') }}" placeholder="Masukkan nama pemilik" required>
                                    @error('owner_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                            <div class="col-md-6">
                                <label for="email" class="form-label required-field">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="email@example.com" required>
                                    @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                            <div class="col-md-6">
                                <label for="phone" class="form-label required-field">Telepon</label>
                                <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}" placeholder="08123456789" required>
                                    @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="logo_url" class="form-label">URL Logo</label>
                                <input type="url" class="form-control @error('logo_url') is-invalid @enderror" id="logo_url" name="logo_url" value="{{ old('logo_url') }}" placeholder="https://example.com/logo.png">
                                @error('logo_url')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <!-- Alamat & Lokasi -->
                    <div class="form-section">
                        <h5><i class="bx bx-map me-2"></i>Alamat & Lokasi</h5>
                        <hr>
                        
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="address" class="form-label required-field">Alamat Lengkap</label>
                                <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3" placeholder="Jl. Contoh No. 123, RT/RW 001/002" required>{{ old('address') }}</textarea>
                                    @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                    <div class="col-md-6">
                                <label class="form-label required-field">Provinsi (Ref)</label>
                                <select id="loc_provinsi_id" name="loc_provinsi_id" class="form-select @error('loc_provinsi_id') is-invalid @enderror" required>
                                                <option value="">Pilih Provinsi</option>
                                            </select>
                                @error('loc_provinsi_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                        </div>
                            
                                    <div class="col-md-6">
                                <label class="form-label required-field">Kab/Kota (Ref)</label>
                                <select id="loc_kabkota_id" name="loc_kabkota_id" class="form-select @error('loc_kabkota_id') is-invalid @enderror" required>
                                                <option value="">Pilih Kab/Kota</option>
                                            </select>
                                @error('loc_kabkota_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                        </div>
                            
                                    <div class="col-md-6">
                                <label class="form-label required-field">Kecamatan (Ref)</label>
                                <select id="loc_kecamatan_id" name="loc_kecamatan_id" class="form-select @error('loc_kecamatan_id') is-invalid @enderror" required>
                                                <option value="">Pilih Kecamatan</option>
                                            </select>
                                @error('loc_kecamatan_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                        </div>
                            
                                    <div class="col-md-6">
                                <label class="form-label required-field">Desa/Kelurahan (Ref)</label>
                                <select id="loc_desa_id" name="loc_desa_id" class="form-select @error('loc_desa_id') is-invalid @enderror" required>
                                                <option value="">Pilih Desa/Kelurahan</option>
                                            </select>
                                @error('loc_desa_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                </div>

                                    <div class="col-md-4">
                                <label for="province" class="form-label required-field">Provinsi (Teks)</label>
                                <input type="text" class="form-control @error('province') is-invalid @enderror" id="province" name="province" value="{{ old('province') }}" required>
                                @error('province')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                        </div>
                            
                                    <div class="col-md-4">
                                <label for="city" class="form-label required-field">Kota (Teks)</label>
                                <input type="text" class="form-control @error('city') is-invalid @enderror" id="city" name="city" value="{{ old('city') }}" required>
                                @error('city')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                        </div>
                            
                                    <div class="col-md-4">
                                <label for="postal_code" class="form-label required-field">Kode Pos</label>
                                <input type="text" class="form-control @error('postal_code') is-invalid @enderror" id="postal_code" name="postal_code" value="{{ old('postal_code') }}" required>
                                @error('postal_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                        </div>
                                    </div>
                    </div>
                    
                    <!-- Pinpoint Lokasi di Peta -->
                    <div class="form-section">
                        <h5><i class="bx bx-map-pin me-2"></i>Pinpoint Lokasi di Peta</h5>
                        <hr>
                        
                        <div class="mb-3">
                            <button type="button" id="btn-get-location" class="btn btn-sm btn-success">
                                <i class="bx bx-crosshair me-1"></i>Gunakan Lokasi GPS Saya
                            </button>
                            <span id="location-status" class="ms-2 small text-muted"></span>
                                </div>
                                
                                <div class="mb-3">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bx bx-search"></i>
                                </span>
                                <input type="text" 
                                       id="address-search" 
                                       class="form-control" 
                                       placeholder="Cari alamat (contoh: Jl. Sudirman No. 123, Semarang)" 
                                       autocomplete="off">
                                <button type="button" id="btn-clear-search" class="btn btn-outline-secondary" style="display:none;">
                                    <i class="bx bx-x"></i>
                                </button>
                            </div>
                            <small class="text-muted d-block mt-1">
                                <i class="bx bx-info-circle me-1"></i>
                                Ketik alamat lengkap untuk mencari lokasi di peta
                            </small>
                        </div>
                        
                        <div id="map" style="height: 400px; width: 100%;"></div>
                        <small class="text-muted d-block mt-2">
                            <i class="bx bx-info-circle me-1"></i>
                            Gunakan GPS, cari alamat, atau klik pada peta untuk menentukan lokasi toko. Pastikan marker berada di lokasi yang tepat.
                        </small>
                        <input type="hidden" id="latitude" name="latitude" value="{{ old('latitude') }}">
                        <input type="hidden" id="longitude" name="longitude" value="{{ old('longitude') }}">
                        @error('latitude')
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                        @error('longitude')
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                                    @enderror
                                </div>
                                
                    <!-- Informasi Tambahan -->
                    <div class="form-section">
                        <h5><i class="bx bx-info-circle me-2"></i>Informasi Tambahan</h5>
                        <hr>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="tax_id" class="form-label">NPWP</label>
                                <input type="text" class="form-control @error('tax_id') is-invalid @enderror" id="tax_id" name="tax_id" value="{{ old('tax_id') }}" placeholder="Contoh: 12.345.678.9-012.345">
                                @error('tax_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                            <div class="col-md-6">
                                <label for="business_license" class="form-label">SIUP</label>
                                <input type="text" class="form-control @error('business_license') is-invalid @enderror" id="business_license" name="business_license" value="{{ old('business_license') }}" placeholder="Nomor SIUP">
                                @error('business_license')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                            <div class="col-12">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', 1) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                        <strong>Aktif</strong> - Toko akan ditampilkan di sistem
                                        </label>
                                </div>
                                </div>
                            </div>
                        </div>
                        
                    <div class="action-buttons">
                            <a href="{{ route('admin.stores.stores') }}" class="btn btn-secondary">
                                <i class="bx bx-arrow-back me-1"></i>Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                            <i class="bx bx-save me-1"></i>Simpan Toko
                            </button>
                        </div>
                    </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-control-geocoder@1.13.0/dist/Control.Geocoder.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

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
      if (window.jQuery && $.fn.select2) { try { $kec.select2('destroy'); } catch(e){} $kec.select2({ width: '100%' }); }
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
  $('#loc_provinsi_id, #loc_kabkota_id, #loc_kecamatan_id, #loc_desa_id').select2({ width: '100%' });

  // Leaflet Maps Picker
  let map, marker;
  let defaultLat = -7.0051;
  let defaultLng = 110.4381;

  function updateLocationStatus(message, type = 'info') {
    const statusEl = document.getElementById('location-status');
    if (statusEl) {
      statusEl.textContent = message;
      statusEl.className = 'ms-2 small ' + (type === 'success' ? 'text-success' : type === 'error' ? 'text-danger' : type === 'warning' ? 'text-warning' : 'text-muted');
    }
  }

  function initMap() {
    const initialLat = parseFloat(document.getElementById('latitude').value) || defaultLat;
    const initialLng = parseFloat(document.getElementById('longitude').value) || defaultLng;

    map = L.map('map').setView([initialLat, initialLng], 15);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '© OpenStreetMap contributors',
      maxZoom: 19
    }).addTo(map);

    const customIcon = L.icon({
      iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png',
      shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png',
      iconSize: [25, 41],
      iconAnchor: [12, 41],
      popupAnchor: [1, -34],
      shadowSize: [41, 41]
    });

    marker = L.marker([initialLat, initialLng], {
      draggable: true,
      icon: customIcon
    }).addTo(map);

    marker.on('dragend', function() {
      const position = marker.getLatLng();
      document.getElementById('latitude').value = position.lat;
      document.getElementById('longitude').value = position.lng;
      updateLocationStatus('Lokasi diperbarui');
    });

    map.on('click', function(event) {
      const clickedLocation = event.latlng;
      marker.setLatLng(clickedLocation);
      document.getElementById('latitude').value = clickedLocation.lat;
      document.getElementById('longitude').value = clickedLocation.lng;
      updateLocationStatus('Lokasi dipilih');
    });

    let geocoder = null;
    if (typeof L.Control.Geocoder !== 'undefined') {
      geocoder = L.Control.Geocoder.nominatim();
    }

    const addressSearchInput = document.getElementById('address-search');
    const clearSearchBtn = document.getElementById('btn-clear-search');
    let searchTimeout;

    if (addressSearchInput) {
      addressSearchInput.addEventListener('input', function() {
        const query = this.value.trim();
        if (query.length < 3) {
          clearSearchBtn.style.display = 'none';
          return;
        }
        clearSearchBtn.style.display = 'inline-block';
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
          if (geocoder && query.length >= 3) {
            performGeocodeSearch(query);
          }
        }, 500);
      });

      addressSearchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
          e.preventDefault();
          const query = this.value.trim();
          if (query.length >= 3) {
            performGeocodeSearch(query);
          }
        }
      });

      clearSearchBtn.addEventListener('click', function() {
        addressSearchInput.value = '';
        this.style.display = 'none';
      });
    }

    function performGeocodeSearch(query) {
      if (!geocoder) {
        updateLocationStatus('Fitur pencarian alamat belum siap', 'warning');
        return;
      }

      updateLocationStatus('Mencari alamat...', 'info');
      
      geocoder.geocode(query, function(results) {
        if (!results || results.length === 0) {
          updateLocationStatus('Alamat tidak ditemukan', 'warning');
          return;
        }

        const result = results[0];
        const location = result.center;
        const address = result.name;

        marker.setLatLng(location);
        map.setView(location, 15);

        document.getElementById('latitude').value = location.lat;
        document.getElementById('longitude').value = location.lng;

        const addressField = document.getElementById('address');
        if (addressField && !addressField.value) {
          addressField.value = address;
        }

        updateLocationStatus('Alamat ditemukan: ' + address, 'success');
      }, {
        bounds: map.getBounds(),
        limit: 5
      });
    }

    if (document.getElementById('latitude').value && document.getElementById('longitude').value) {
      updateLocationStatus('Lokasi tersimpan');
    }
  }

  function getCurrentLocation() {
    const btn = document.getElementById('btn-get-location');
    if (!navigator.geolocation) {
      updateLocationStatus('GPS tidak didukung', 'error');
      return;
    }

    btn.disabled = true;
    btn.innerHTML = '<i class="bx bx-loader-alt bx-spin me-1"></i>Mendeteksi...';
    updateLocationStatus('Meminta izin GPS...', 'info');

    try {
      navigator.geolocation.getCurrentPosition(
        function(position) {
          const lat = position.coords.latitude;
          const lng = position.coords.longitude;
          
          if (marker && map) {
            marker.setLatLng([lat, lng]);
            map.setView([lat, lng], 15);
          }
          
          document.getElementById('latitude').value = lat;
          document.getElementById('longitude').value = lng;
          
          updateLocationStatus('Lokasi GPS berhasil didapatkan', 'success');
          btn.disabled = false;
          btn.innerHTML = '<i class="bx bx-crosshair me-1"></i>Gunakan Lokasi GPS Saya';
        },
        function(error) {
          let errorMsg = 'Gagal mendapatkan lokasi GPS';
          if (error.code === 1) {
            errorMsg = 'Izin akses lokasi ditolak. Silakan izinkan akses lokasi di pengaturan browser.';
          } else if (error.code === 2) {
            errorMsg = 'Lokasi tidak tersedia. Silakan coba lagi atau masukkan koordinat secara manual.';
          } else if (error.code === 3) {
            errorMsg = 'Waktu permintaan habis. Silakan coba lagi.';
          }
          updateLocationStatus(errorMsg, 'error');
          btn.disabled = false;
          btn.innerHTML = '<i class="bx bx-crosshair me-1"></i>Gunakan Lokasi GPS Saya';
        },
        {
          enableHighAccuracy: false,
          timeout: 15000,
          maximumAge: 60000
        }
      );
    } catch (error) {
      updateLocationStatus('Error: ' + error.message, 'error');
      btn.disabled = false;
      btn.innerHTML = '<i class="bx bx-crosshair me-1"></i>Gunakan Lokasi GPS Saya';
    }
  }

  document.getElementById('btn-get-location').addEventListener('click', getCurrentLocation);

  if (typeof L !== 'undefined') {
    initMap();
  } else {
    const checkLeaflet = setInterval(function() {
      if (typeof L !== 'undefined') {
        clearInterval(checkLeaflet);
        initMap();
      }
    }, 100);
  }
});
</script>
@endpush
