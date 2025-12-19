@extends('layouts.app')

@section('title', 'Profil Saya')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder@1.13.0/dist/Control.Geocoder.css" />
<style>
.page-header {
    background: linear-gradient(135deg, #137440 0%, #0f5d33 100%);
    padding: 3rem 0;
    margin-bottom: 2rem;
}

.profile-section {
    background: white;
    border-radius: 12px;
    padding: 2rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    margin-bottom: 2rem;
}

.profile-header {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    padding-bottom: 1.5rem;
    border-bottom: 2px solid #e9ecef;
    margin-bottom: 2rem;
}

.profile-avatar {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background: linear-gradient(135deg, #137440, #0f5d33);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 2.5rem;
    font-weight: bold;
}

.profile-info h4 {
    margin: 0;
    color: #137440;
}

.profile-info p {
    margin: 0.25rem 0;
    color: #6c757d;
}

.info-card {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 1rem;
}

.info-card label {
    font-weight: 600;
    color: #495057;
    font-size: 0.875rem;
    margin-bottom: 0.25rem;
}

.info-card .value {
    color: #212529;
    font-size: 1rem;
}

#map-container {
    height: 400px;
    width: 100%;
    border-radius: 12px;
    border: 2px solid #e9ecef;
    margin-top: 1rem;
}

.required-field::after {
    content: " *";
    color: red;
}

.tab-content {
    padding-top: 1.5rem;
}

.tab-pane {
    display: none;
}

.tab-pane.active,
.tab-pane.show {
    display: block !important;
}
</style>
@endpush

@section('content')
<!-- Single Page Header Start -->
<div class="container-fluid page-header py-5">
    <div class="container">
        <h1 class="text-center text-white display-6">Profil Saya</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-white">Beranda</a></li>
            <li class="breadcrumb-item"><a href="{{ route('customer.dashboard') }}" class="text-white">Dashboard</a></li>
            <li class="breadcrumb-item active text-white">Profil</li>
        </ol>
    </div>
</div>
<!-- Single Page Header End -->

<div class="container py-5">
    <!-- Profile Header -->
    <div class="profile-section">
        <div class="profile-header">
            <div class="profile-avatar">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div class="profile-info">
                <h4>{{ $user->name }}</h4>
                <p><i class="bx bx-envelope me-1"></i>{{ $user->email }}</p>
                @if($user->phone)
                    <p><i class="bx bx-phone me-1"></i>{{ $user->phone }}</p>
                @endif
                <p class="text-muted small">
                    <i class="bx bx-calendar me-1"></i>
                    Bergabung sejak {{ $user->created_at->format('d F Y') }}
                </p>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <ul class="nav nav-tabs mb-4" id="profileTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="info-tab" data-bs-toggle="tab" data-bs-target="#info" type="button" role="tab" aria-controls="info" aria-selected="true">
                <i class="bx bx-info-circle me-1"></i>Informasi Profil
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="password-tab" data-bs-toggle="tab" data-bs-target="#password" type="button" role="tab" aria-controls="password" aria-selected="false">
                <i class="bx bx-lock me-1"></i>Ubah Password
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="address-tab" data-bs-toggle="tab" data-bs-target="#address" type="button" role="tab" aria-controls="address" aria-selected="false">
                <i class="bx bx-map-pin me-1"></i>Alamat Utama
            </button>
        </li>
    </ul>

    <div class="tab-content" id="profileTabsContent">
        <!-- Informasi Profil Tab -->
        <div class="tab-pane fade show active" id="info" role="tabpanel" aria-labelledby="info-tab">
            <div class="profile-section">
                <h5 class="mb-4">
                    <i class="bx bx-edit me-2 text-primary"></i>
                    Edit Informasi Profil
                </h5>
                <form id="profileForm">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label required-field">Nama Lengkap</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required-field">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">No. Telepon</label>
                            <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Alamat</label>
                            <textarea class="form-control" id="address" name="address" rows="2">{{ old('address', $user->address) }}</textarea>
                        </div>
                        
                        <!-- Location Dropdowns -->
                        <div class="col-md-6">
                            <label class="form-label">Provinsi (Ref)</label>
                            <select id="loc_provinsi_id" name="loc_provinsi_id" class="form-select">
                                <option value="">Pilih Provinsi</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Kab/Kota (Ref)</label>
                            <select id="loc_kabkota_id" name="loc_kabkota_id" class="form-select">
                                <option value="">Pilih Kab/Kota</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Kecamatan (Ref)</label>
                            <select id="loc_kecamatan_id" name="loc_kecamatan_id" class="form-select">
                                <option value="">Pilih Kecamatan</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Desa/Kelurahan (Ref)</label>
                            <select id="loc_desa_id" name="loc_desa_id" class="form-select">
                                <option value="">Pilih Desa/Kelurahan</option>
                            </select>
                        </div>
                        
                        <!-- Map Section -->
                        <div class="col-12">
                            <hr>
                            <h6 class="mb-3"><i class="bx bx-map-pin me-2"></i>Pinpoint Lokasi di Peta</h6>
                            
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
                            </div>
                            
                            <div id="map-container"></div>
                            <small class="text-muted d-block mt-2">
                                <i class="bx bx-info-circle me-1"></i>
                                Gunakan GPS, cari alamat, atau klik pada peta untuk menentukan lokasi.
                            </small>
                            
                            <input type="hidden" id="latitude" name="latitude" value="{{ old('latitude', $user->latitude) }}">
                            <input type="hidden" id="longitude" name="longitude" value="{{ old('longitude', $user->longitude) }}">
                        </div>
                        
                        <div class="col-12">
                            <button type="submit" class="btn btn-success">
                                <i class="bx bx-save me-1"></i>Simpan Perubahan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Ubah Password Tab -->
        <div class="tab-pane fade" id="password" role="tabpanel" aria-labelledby="password-tab">
            <div class="profile-section">
                <h5 class="mb-4">
                    <i class="bx bx-lock me-2 text-warning"></i>
                    Ubah Password
                </h5>
                <form id="passwordForm">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label required-field">Password Saat Ini</label>
                            <input type="password" class="form-control" id="current_password" name="current_password" required>
                            <small class="text-muted">Masukkan password Anda saat ini untuk verifikasi</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required-field">Password Baru</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                            <small class="text-muted">Minimal 8 karakter</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required-field">Konfirmasi Password Baru</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-warning">
                                <i class="bx bx-key me-1"></i>Ubah Password
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Alamat Utama Tab -->
        <div class="tab-pane fade" id="address" role="tabpanel" aria-labelledby="address-tab" tabindex="0">
            <div class="profile-section">
                <h5 class="mb-4">
                    <i class="bx bx-map-pin me-2 text-info"></i>
                    Alamat Utama
                </h5>
                
                @if(isset($primaryAddress) && $primaryAddress)
                    <div class="info-card">
                        <label>Nama Penerima</label>
                        <div class="value">{{ $primaryAddress->recipient_name ?? '-' }}</div>
                    </div>
                    <div class="info-card">
                        <label>No. Telepon</label>
                        <div class="value">{{ $primaryAddress->recipient_phone ?? '-' }}</div>
                    </div>
                    <div class="info-card">
                        <label>Alamat Lengkap</label>
                        <div class="value">
                            {{ $primaryAddress->address ?? '-' }}
                            @if(isset($primaryAddress->notes) && $primaryAddress->notes), {{ $primaryAddress->notes }}@endif
                            <br>
                            {{ $primaryAddress->loc_kecamatan_name ?? '' }}{{ isset($primaryAddress->loc_kecamatan_name) && $primaryAddress->loc_kecamatan_name ? ', ' : '' }}
                            {{ $primaryAddress->loc_kabkota_name ?? ($primaryAddress->city ?? '') }}
                            {{ ($primaryAddress->loc_provinsi_name ?? $primaryAddress->province ?? '') ? ', ' : '' }}
                            {{ $primaryAddress->loc_provinsi_name ?? ($primaryAddress->province ?? '') }}
                            @if(isset($primaryAddress->postal_code) && $primaryAddress->postal_code) {{ $primaryAddress->postal_code }}@endif
                        </div>
                    </div>
                    @if(isset($primaryAddress->latitude) && isset($primaryAddress->longitude) && $primaryAddress->latitude && $primaryAddress->longitude)
                        <div class="info-card">
                            <label>Koordinat</label>
                            <div class="value">
                                {{ number_format($primaryAddress->latitude, 6) }}, {{ number_format($primaryAddress->longitude, 6) }}
                            </div>
                        </div>
                    @endif
                    <div class="mt-3">
                        <a href="{{ route('user.addresses.index') }}" class="btn btn-outline-primary">
                            <i class="bx bx-edit me-1"></i>Kelola Alamat
                        </a>
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="bx bx-info-circle me-2"></i>
                        Anda belum memiliki alamat utama. 
                        <a href="{{ route('user.addresses.index') }}" class="alert-link">Tambahkan alamat sekarang</a>
                    </div>
                @endif
                
                <div class="mt-4">
                    <h6>Total Alamat Tersimpan</h6>
                    <p class="text-muted">Anda memiliki <strong>{{ $addressCount ?? 0 }}</strong> alamat tersimpan</p>
                    <a href="{{ route('user.addresses.index') }}" class="btn btn-success">
                        <i class="bx bx-plus me-1"></i>Kelola Semua Alamat
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-control-geocoder@1.13.0/dist/Control.Geocoder.js"></script>

<script>
$(document).ready(function() {
    // Ensure Bootstrap tabs work correctly - use Bootstrap's native tab functionality
    var triggerTabList = [].slice.call(document.querySelectorAll('#profileTabs button[data-bs-toggle="tab"]'));
    triggerTabList.forEach(function (triggerEl) {
        var tabTrigger = new bootstrap.Tab(triggerEl);
        
        triggerEl.addEventListener('click', function (event) {
            event.preventDefault();
            tabTrigger.show();
        });
    });
    
    // Also handle with jQuery as fallback
    $('#profileTabs button[data-bs-toggle="tab"]').on('click', function(e) {
        e.preventDefault();
        var target = $(this).data('bs-target');
        
        // Remove active class from all tabs and panes
        $('#profileTabs button').removeClass('active').attr('aria-selected', 'false');
        $('.tab-pane').removeClass('show active').css('display', 'none');
        
        // Add active class to clicked tab and corresponding pane
        $(this).addClass('active').attr('aria-selected', 'true');
        $(target).addClass('show active').css('display', 'block');
        
        // Ensure content is visible
        setTimeout(function() {
            $(target).css({
                'display': 'block',
                'opacity': '1',
                'visibility': 'visible'
            });
        }, 100);
    });
    
    let map = null;
    let marker = null;
    const defaultLat = -7.0051;
    const defaultLng = 110.4381;
    
    // Current user location values
    const oldProv = {{ $user->loc_provinsi_id ?: 'null' }};
    const oldKab = {{ $user->loc_kabkota_id ?: 'null' }};
    const oldKec = {{ $user->loc_kecamatan_id ?: 'null' }};
    const oldDesa = {{ $user->loc_desa_id ?: 'null' }};
    const oldLat = {{ $user->latitude ?: 'null' }};
    const oldLng = {{ $user->longitude ?: 'null' }};
    
    // Initialize location dropdowns
    function initLocationDropdowns() {
        const provSel = document.getElementById('loc_provinsi_id');
        const kabSel = document.getElementById('loc_kabkota_id');
        const kecSel = document.getElementById('loc_kecamatan_id');
        const desaSel = document.getElementById('loc_desa_id');
        
        fetch('{{ route('api.locations.provinsis') }}')
            .then(r => r.json()).then(json => {
                const $prov = $('#loc_provinsi_id');
                $prov.empty().append('<option value="">Pilih Provinsi</option>');
                (json.data||[]).forEach(it=>{ 
                    $prov.append(new Option(it.name, it.id, false, false)); 
                });
                if ($.fn.select2) { 
                    try { $prov.select2('destroy'); } catch(e){} 
                    $prov.select2({ width: '100%', theme: 'bootstrap-5' }); 
                }
                if (oldProv) {
                    $prov.val(String(oldProv)).trigger('change');
                    setTimeout(function() {
                        loadKab(String(oldProv), true);
                    }, 100);
                }
            });
        
        $('#loc_provinsi_id').on('change select2:select', function(){ 
            loadKab(this.value, false); 
        });
        $('#loc_kabkota_id').on('change select2:select', function(){ 
            loadKec(this.value, false); 
        });
        $('#loc_kecamatan_id').on('change select2:select', function(){ 
            loadDesa(this.value, false); 
        });
        
        function resetSelect(sel, ph){ 
            sel.innerHTML = `<option value="">${ph}</option>`; 
            if ($.fn.select2) {
                try { $(sel).select2('destroy'); } catch(e){} 
                $(sel).select2({ width: '100%', theme: 'bootstrap-5' }); 
            }
        }
        
        function loadKab(pid, restoring){
            const $kab = $('#loc_kabkota_id');
            resetSelect($kab[0], 'Pilih Kab/Kota');
            resetSelect($('#loc_kecamatan_id')[0], 'Pilih Kecamatan');
            resetSelect($('#loc_desa_id')[0], 'Pilih Desa/Kelurahan');
            if(!pid) return;
            fetch(`{{ url('/api/locations/kabkotas') }}/${pid}`).then(r=>r.json()).then(j=>{
                (j.data||[]).forEach(it=>{ 
                    $kab.append(new Option(it.name, it.id, false, false)); 
                });
                if ($.fn.select2) { 
                    try { $kab.select2('destroy'); } catch(e){} 
                    $kab.select2({ width: '100%', theme: 'bootstrap-5' }); 
                }
                if(restoring && oldKab) {
                    setTimeout(function() {
                        $kab.val(String(oldKab)).trigger('change');
                        setTimeout(function() {
                            loadKec(String(oldKab), true);
                        }, 150);
                    }, 150);
                }
            });
        }
        
        function loadKec(kid, restoring){
            const $kec = $('#loc_kecamatan_id');
            resetSelect($kec[0], 'Pilih Kecamatan');
            resetSelect($('#loc_desa_id')[0], 'Pilih Desa/Kelurahan');
            if(!kid) return;
            fetch(`{{ url('/api/locations/kecamatans') }}/${kid}`).then(r=>r.json()).then(j=>{
                (j.data||[]).forEach(it=>{ 
                    $kec.append(new Option(it.name, it.id, false, false)); 
                });
                if ($.fn.select2) { 
                    try { $kec.select2('destroy'); } catch(e){} 
                    $kec.select2({ width: '100%', theme: 'bootstrap-5' }); 
                }
                if(restoring && oldKec) {
                    setTimeout(function() {
                        $kec.val(String(oldKec)).trigger('change');
                        setTimeout(function() {
                            loadDesa(String(oldKec), true);
                        }, 150);
                    }, 150);
                }
            });
        }
        
        function loadDesa(did, restoring){
            const $desa = $('#loc_desa_id');
            resetSelect($desa[0], 'Pilih Desa/Kelurahan');
            if(!did) return;
            fetch(`{{ url('/api/locations/desas') }}/${did}`).then(r=>r.json()).then(j=>{
                (j.data||[]).forEach(it=>{ 
                    $desa.append(new Option(it.name, it.id, false, false)); 
                });
                if ($.fn.select2) { 
                    try { $desa.select2('destroy'); } catch(e){} 
                    $desa.select2({ width: '100%', theme: 'bootstrap-5' }); 
                }
                if(restoring && oldDesa) {
                    setTimeout(function() {
                        $desa.val(String(oldDesa)).trigger('change');
                    }, 150);
                }
            });
        }
    }
    
    // Initialize map
    function initMap() {
        if (map) {
            map.remove();
        }
        
        const initialLat = oldLat || parseFloat($('#latitude').val()) || defaultLat;
        const initialLng = oldLng || parseFloat($('#longitude').val()) || defaultLng;
        
        map = L.map('map-container').setView([initialLat, initialLng], oldLat ? 15 : 13);
        
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
        
        $('#latitude').val(initialLat);
        $('#longitude').val(initialLng);
        
        marker.on('dragend', function() {
            const position = marker.getLatLng();
            $('#latitude').val(position.lat);
            $('#longitude').val(position.lng);
            updateLocationStatus('Lokasi diperbarui');
        });
        
        map.on('click', function(event) {
            const clickedLocation = event.latlng;
            marker.setLatLng(clickedLocation);
            $('#latitude').val(clickedLocation.lat);
            $('#longitude').val(clickedLocation.lng);
            updateLocationStatus('Lokasi dipilih');
        });
        
        // Address search
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
            if (!geocoder) return;
            
            geocoder.geocode(query, function(results) {
                if (results && results.length > 0) {
                    const result = results[0];
                    const lat = result.center.lat;
                    const lng = result.center.lng;
                    
                    marker.setLatLng([lat, lng]);
                    map.setView([lat, lng], 15);
                    $('#latitude').val(lat);
                    $('#longitude').val(lng);
                    updateLocationStatus('Lokasi ditemukan');
                }
            });
        }
    }
    
    function updateLocationStatus(message, type = 'info') {
        const statusEl = document.getElementById('location-status');
        if (statusEl) {
            statusEl.textContent = message;
            statusEl.className = 'ms-2 small ' + (type === 'error' ? 'text-danger' : 'text-success');
        }
    }
    
    // GPS button
    $('#btn-get-location').on('click', function() {
        const btn = $(this);
        btn.prop('disabled', true);
        btn.html('<i class="bx bx-loader-alt bx-spin me-1"></i>Mengambil lokasi...');
        updateLocationStatus('Mengambil lokasi GPS...');
        
        if (!navigator.geolocation) {
            updateLocationStatus('Geolocation tidak didukung oleh browser Anda', 'error');
            btn.prop('disabled', false);
            btn.html('<i class="bx bx-crosshair me-1"></i>Gunakan Lokasi GPS Saya');
            return;
        }
        
        navigator.geolocation.getCurrentPosition(
            function(position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                
                if (marker) {
                    marker.setLatLng([lat, lng]);
                    map.setView([lat, lng], 15);
                }
                $('#latitude').val(lat);
                $('#longitude').val(lng);
                
                updateLocationStatus('Lokasi GPS berhasil diambil');
                btn.prop('disabled', false);
                btn.html('<i class="bx bx-crosshair me-1"></i>Gunakan Lokasi GPS Saya');
            },
            function(error) {
                updateLocationStatus('Gagal mengambil lokasi GPS: ' + error.message, 'error');
                btn.prop('disabled', false);
                btn.html('<i class="bx bx-crosshair me-1"></i>Gunakan Lokasi GPS Saya');
            },
            {
                enableHighAccuracy: false,
                timeout: 15000,
                maximumAge: 60000
            }
        );
    });
    
    // Initialize on page load
    initLocationDropdowns();
    setTimeout(function() {
        if (typeof L !== 'undefined') {
            initMap();
        }
    }, 500);
    
    // Profile form submission
    $('#profileForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = {
            name: $('#name').val(),
            email: $('#email').val(),
            phone: $('#phone').val(),
            address: $('#address').val(),
            loc_provinsi_id: $('#loc_provinsi_id').val(),
            loc_kabkota_id: $('#loc_kabkota_id').val(),
            loc_kecamatan_id: $('#loc_kecamatan_id').val(),
            loc_desa_id: $('#loc_desa_id').val(),
            latitude: $('#latitude').val(),
            longitude: $('#longitude').val()
        };
        
        $.ajax({
            url: '{{ route('customer.profile.update') }}',
            method: 'PUT',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Profil berhasil diperbarui',
                        confirmButtonColor: '#28a745',
                        timer: 2000,
                        timerProgressBar: true
                    }).then(() => {
                        location.reload();
                    });
                }
            },
            error: function(xhr) {
                const errors = xhr.responseJSON?.errors || {};
                let errorMsg = 'Terjadi kesalahan saat memperbarui profil';
                if (Object.keys(errors).length > 0) {
                    errorMsg = Object.values(errors).flat().join('\n');
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: errorMsg,
                    confirmButtonColor: '#dc3545'
                });
            }
        });
    });
    
    // Password form submission
    $('#passwordForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = {
            current_password: $('#current_password').val(),
            password: $('#password').val(),
            password_confirmation: $('#password_confirmation').val()
        };
        
        if (formData.password !== formData.password_confirmation) {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Password baru dan konfirmasi password tidak sesuai',
                confirmButtonColor: '#dc3545'
            });
            return;
        }
        
        $.ajax({
            url: '{{ route('customer.profile.password.update') }}',
            method: 'PUT',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Password berhasil diubah',
                        confirmButtonColor: '#28a745',
                        timer: 2000,
                        timerProgressBar: true
                    }).then(() => {
                        $('#passwordForm')[0].reset();
                    });
                }
            },
            error: function(xhr) {
                const errors = xhr.responseJSON?.errors || {};
                let errorMsg = 'Terjadi kesalahan saat mengubah password';
                if (xhr.responseJSON?.message) {
                    errorMsg = xhr.responseJSON.message;
                } else if (Object.keys(errors).length > 0) {
                    errorMsg = Object.values(errors).flat().join('\n');
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: errorMsg,
                    confirmButtonColor: '#dc3545'
                });
            }
        });
    });
});
</script>
@endpush
