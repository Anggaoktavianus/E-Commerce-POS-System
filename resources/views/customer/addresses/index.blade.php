@extends('layouts.app')

@section('title', 'Alamat Saya')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder@1.13.0/dist/Control.Geocoder.css" />
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<style>
.page-header {
    background: linear-gradient(135deg, #137440 0%, #0f5d33 100%);
    padding: 3rem 0;
    margin-bottom: 2rem;
}

.address-card {
    border: 2px solid #e9ecef;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    transition: all 0.3s ease;
    background: white;
}

.address-card:hover {
    border-color: #137440;
    box-shadow: 0 4px 12px rgba(19, 116, 64, 0.15);
    transform: translateY(-2px);
}

.address-card.primary {
    border-color: #137440;
    background: linear-gradient(135deg, rgba(19, 116, 64, 0.05) 0%, rgba(15, 93, 51, 0.05) 100%);
}

.address-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1rem;
}

.address-actions {
    display: flex;
    gap: 0.5rem;
}

.btn-action-sm {
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
    border-radius: 6px;
}

#map-container {
    height: 400px;
    width: 100%;
    border-radius: 12px;
    border: 2px solid #e9ecef;
    margin-top: 1rem;
}

.form-section {
    background: white;
    border-radius: 12px;
    padding: 2rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    margin-bottom: 2rem;
}

.required-field::after {
    content: " *";
    color: red;
}
</style>
@endpush

@section('content')
<!-- Single Page Header Start -->
<div class="container-fluid page-header py-5">
    <div class="container">
        <h1 class="text-center text-white display-6">Alamat Saya</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-white">Beranda</a></li>
            <li class="breadcrumb-item"><a href="{{ route('customer.dashboard') }}" class="text-white">Dashboard</a></li>
            <li class="breadcrumb-item active text-white">Alamat Saya</li>
        </ol>
    </div>
</div>
<!-- Single Page Header End -->

<div class="container py-5">
    <!-- Add New Address Form -->
    <div class="form-section">
        <h4 class="mb-4">
            <i class="bx bx-plus-circle me-2 text-success"></i>
            Tambah Alamat Baru
        </h4>
        <form id="addressForm">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label required-field">Label Alamat</label>
                    <input type="text" class="form-control" id="label" name="label" placeholder="Rumah, Kantor, dll">
                </div>
                <div class="col-md-6">
                    <label class="form-label required-field">Nama Penerima</label>
                    <input type="text" class="form-control" id="recipient_name" name="recipient_name" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label required-field">No. Telepon</label>
                    <input type="text" class="form-control" id="recipient_phone" name="recipient_phone" required>
                </div>
                <div class="col-md-12">
                    <label class="form-label required-field">Alamat Lengkap</label>
                    <textarea class="form-control" id="address" name="address" rows="2" required></textarea>
                </div>
                
                <!-- Location Dropdowns -->
                <div class="col-md-6">
                    <label class="form-label required-field">Provinsi (Ref)</label>
                    <select id="loc_provinsi_id" name="loc_provinsi_id" class="form-select" required>
                        <option value="">Pilih Provinsi</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label required-field">Kab/Kota (Ref)</label>
                    <select id="loc_kabkota_id" name="loc_kabkota_id" class="form-select" required>
                        <option value="">Pilih Kab/Kota</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label required-field">Kecamatan (Ref)</label>
                    <select id="loc_kecamatan_id" name="loc_kecamatan_id" class="form-select" required>
                        <option value="">Pilih Kecamatan</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label required-field">Desa/Kelurahan (Ref)</label>
                    <select id="loc_desa_id" name="loc_desa_id" class="form-select" required>
                        <option value="">Pilih Desa/Kelurahan</option>
                    </select>
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">Kota (Teks)</label>
                    <input type="text" class="form-control" id="city" name="city">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Kode Pos</label>
                    <input type="text" class="form-control" id="postal_code" name="postal_code">
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
                        Gunakan GPS, cari alamat, atau klik pada peta untuk menentukan lokasi. Pastikan marker berada di lokasi yang tepat.
                    </small>
                    
                    <input type="hidden" id="latitude" name="latitude">
                    <input type="hidden" id="longitude" name="longitude">
                </div>
                
                <div class="col-12">
                    <label class="form-label">Catatan</label>
                    <input type="text" class="form-control" id="notes" name="notes" placeholder="Samping SMA 7, dll">
                </div>
                <div class="col-12">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="is_primary" name="is_primary" value="1">
                        <label class="form-check-label" for="is_primary">
                            Jadikan sebagai alamat utama
                        </label>
                    </div>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-success">
                        <i class="bx bx-plus me-1"></i>Tambah Alamat
                    </button>
                    <button type="button" class="btn btn-secondary" id="btn-reset-form">
                        <i class="bx bx-refresh me-1"></i>Reset Form
                    </button>
                </div>
            </div>
        </form>
    </div>
    
    <!-- Address List -->
    <div class="form-section">
        <h4 class="mb-4">
            <i class="bx bx-map me-2 text-primary"></i>
            Daftar Alamat Tersimpan ({{ $addresses->count() }})
        </h4>
        
        @if($addresses->count() > 0)
            <div id="address-list">
                @foreach($addresses as $address)
                    <div class="address-card {{ $address->is_primary ? 'primary' : '' }}" data-address-id="{{ $address->id }}">
                        <div class="address-header">
                            <div>
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <h5 class="mb-0">{{ $address->recipient_name }}</h5>
                                    @if($address->is_primary)
                                        <span class="badge bg-danger">Alamat Utama</span>
                                    @endif
                                    @if($address->label)
                                        <span class="badge bg-secondary">{{ $address->label }}</span>
                                    @endif
                                </div>
                                <p class="mb-1 text-muted">
                                    <i class="bx bx-phone me-1"></i>{{ $address->recipient_phone }}
                                </p>
                                <p class="mb-0">
                                    {{ $address->address }}
                                    @if($address->notes), {{ $address->notes }}@endif
                                    <br>
                                    {{ $address->loc_kecamatan_name ?? '' }}{{ $address->loc_kecamatan_name ? ', ' : '' }}
                                    {{ $address->loc_kabkota_name ?? $address->city }}
                                    {{ $address->loc_provinsi_name ?? $address->province ? ', ' : '' }}
                                    {{ $address->loc_provinsi_name ?? $address->province }}
                                    @if($address->postal_code) {{ $address->postal_code }}@endif
                                </p>
                                @if($address->latitude && $address->longitude)
                                    <small class="text-muted">
                                        <i class="bx bx-map-pin me-1"></i>
                                        Koordinat: {{ number_format($address->latitude, 6) }}, {{ number_format($address->longitude, 6) }}
                                    </small>
                                @endif
                            </div>
                            <div class="address-actions">
                                @if(!$address->is_primary)
                                    <button type="button" class="btn btn-sm btn-outline-success btn-action-sm set-primary-btn" data-id="{{ $address->id }}">
                                        <i class="bx bx-star me-1"></i>Set Utama
                                    </button>
                                @endif
                                <button type="button" class="btn btn-sm btn-outline-primary btn-action-sm edit-address-btn" data-id="{{ $address->id }}">
                                    <i class="bx bx-edit me-1"></i>Edit
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger btn-action-sm delete-address-btn" data-id="{{ $address->id }}">
                                    <i class="bx bx-trash me-1"></i>Hapus
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-5">
                <i class="bx bx-map-pin" style="font-size: 4rem; color: #d1d5db;"></i>
                <h5 class="mt-3 text-muted">Belum Ada Alamat Tersimpan</h5>
                <p class="text-muted">Tambahkan alamat baru menggunakan form di atas</p>
            </div>
        @endif
    </div>
</div>

<!-- Edit Address Modal -->
<div class="modal fade" id="editAddressModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="bx bx-edit me-2"></i>Edit Alamat
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editAddressForm">
                    @csrf
                    <input type="hidden" id="edit_address_id" name="address_id">
                    <!-- Same form fields as add form, prefixed with edit_ -->
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label required-field">Label Alamat</label>
                            <input type="text" class="form-control" id="edit_label" name="label">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required-field">Nama Penerima</label>
                            <input type="text" class="form-control" id="edit_recipient_name" name="recipient_name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required-field">No. Telepon</label>
                            <input type="text" class="form-control" id="edit_recipient_phone" name="recipient_phone" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label required-field">Alamat Lengkap</label>
                            <textarea class="form-control" id="edit_address" name="address" rows="2" required></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Kota (Teks)</label>
                            <input type="text" class="form-control" id="edit_city" name="city">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Kode Pos</label>
                            <input type="text" class="form-control" id="edit_postal_code" name="postal_code">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Catatan</label>
                            <input type="text" class="form-control" id="edit_notes" name="notes">
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="edit_is_primary" name="is_primary" value="1">
                                <label class="form-check-label" for="edit_is_primary">
                                    Jadikan sebagai alamat utama
                                </label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btn-update-address">
                    <i class="bx bx-save me-1"></i>Simpan Perubahan
                </button>
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
    let map = null;
    let marker = null;
    const defaultLat = -7.0051;
    const defaultLng = 110.4381;
    
    // Initialize location dropdowns
    function initLocationDropdowns() {
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
            });
        
        $('#loc_provinsi_id').on('change select2:select', function(){ 
            loadKab(this.value); 
        });
        $('#loc_kabkota_id').on('change select2:select', function(){ 
            loadKec(this.value); 
        });
        $('#loc_kecamatan_id').on('change select2:select', function(){ 
            loadDesa(this.value); 
        });
        
        function resetSelect(sel, ph){ 
            sel.empty().append(`<option value="">${ph}</option>`); 
            if ($.fn.select2) {
                try { $(sel).select2('destroy'); } catch(e){} 
                $(sel).select2({ width: '100%', theme: 'bootstrap-5' }); 
            }
        }
        
        function loadKab(pid){
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
            });
        }
        
        function loadKec(kid){
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
            });
        }
        
        function loadDesa(did){
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
            });
        }
    }
    
    // Initialize map
    function initMap() {
        if (map) {
            map.remove();
        }
        
        const initialLat = parseFloat($('#latitude').val()) || defaultLat;
        const initialLng = parseFloat($('#longitude').val()) || defaultLng;
        
        map = L.map('map-container').setView([initialLat, initialLng], 15);
        
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
    }, 300);
    
    // Form submission
    $('#addressForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = {
            label: $('#label').val(),
            recipient_name: $('#recipient_name').val(),
            recipient_phone: $('#recipient_phone').val(),
            address: $('#address').val(),
            loc_provinsi_id: $('#loc_provinsi_id').val(),
            loc_kabkota_id: $('#loc_kabkota_id').val(),
            loc_kecamatan_id: $('#loc_kecamatan_id').val(),
            loc_desa_id: $('#loc_desa_id').val(),
            city: $('#city').val(),
            postal_code: $('#postal_code').val(),
            latitude: $('#latitude').val(),
            longitude: $('#longitude').val(),
            notes: $('#notes').val(),
            is_primary: $('#is_primary').is(':checked') ? 1 : 0
        };
        
        $.ajax({
            url: '{{ route('user.addresses.store') }}',
            method: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Alamat berhasil ditambahkan',
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
                let errorMsg = 'Terjadi kesalahan saat menyimpan alamat';
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
    
    // Reset form
    $('#btn-reset-form').on('click', function() {
        $('#addressForm')[0].reset();
        $('#latitude').val('');
        $('#longitude').val('');
        if (marker && map) {
            marker.setLatLng([defaultLat, defaultLng]);
            map.setView([defaultLat, defaultLng], 15);
        }
    });
    
    // Set primary
    $('.set-primary-btn').on('click', function() {
        const id = $(this).data('id');
        const addressCard = $(this).closest('.address-card');
        const addressName = addressCard.find('h5').text() || 'alamat ini';
        
        Swal.fire({
            title: 'Set Alamat Utama?',
            html: `Jadikan <strong>${addressName}</strong> sebagai alamat utama?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="bx bx-star me-1"></i>Ya, Set Utama',
            cancelButtonText: '<i class="bx bx-x me-1"></i>Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `{{ url('/user/addresses') }}/${id}/set-primary`,
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'Alamat utama berhasil diubah',
                                confirmButtonColor: '#28a745',
                                timer: 2000,
                                timerProgressBar: true
                            }).then(() => {
                                location.reload();
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Gagal mengubah alamat utama',
                            confirmButtonColor: '#dc3545'
                        });
                    }
                });
            }
        });
    });
    
    // Delete address
    $('.delete-address-btn').on('click', function() {
        const id = $(this).data('id');
        const addressCard = $(this).closest('.address-card');
        const addressName = addressCard.find('h5').text() || 'alamat ini';
        
        Swal.fire({
            title: 'Hapus Alamat?',
            html: `Apakah Anda yakin ingin menghapus <strong>${addressName}</strong>?<br><small class="text-muted">Tindakan ini tidak dapat dibatalkan.</small>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="bx bx-trash me-1"></i>Ya, Hapus',
            cancelButtonText: '<i class="bx bx-x me-1"></i>Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `{{ url('/user/addresses') }}/${id}`,
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Terhapus!',
                                text: 'Alamat berhasil dihapus',
                                confirmButtonColor: '#28a745',
                                timer: 2000,
                                timerProgressBar: true
                            }).then(() => {
                                location.reload();
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Gagal menghapus alamat',
                            confirmButtonColor: '#dc3545'
                        });
                    }
                });
            }
        });
    });
    
    // Edit address (simplified - just update basic fields)
    $('.edit-address-btn').on('click', function() {
        const id = $(this).data('id');
        const addressCard = $(this).closest('.address-card');
        
        // For now, just show a simple edit modal
        // In production, you might want to load full address data via AJAX
        $('#edit_address_id').val(id);
        $('#editAddressModal').modal('show');
    });
    
    $('#btn-update-address').on('click', function() {
        const id = $('#edit_address_id').val();
        const formData = {
            label: $('#edit_label').val(),
            recipient_name: $('#edit_recipient_name').val(),
            recipient_phone: $('#edit_recipient_phone').val(),
            address: $('#edit_address').val(),
            city: $('#edit_city').val(),
            postal_code: $('#edit_postal_code').val(),
            notes: $('#edit_notes').val(),
            is_primary: $('#edit_is_primary').is(':checked') ? 1 : 0
        };
        
        $.ajax({
            url: `{{ url('/user/addresses') }}/${id}`,
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
                        text: 'Alamat berhasil diperbarui',
                        confirmButtonColor: '#28a745',
                        timer: 2000,
                        timerProgressBar: true
                    }).then(() => {
                        $('#editAddressModal').modal('hide');
                        location.reload();
                    });
                }
            },
            error: function(xhr) {
                const errors = xhr.responseJSON?.errors || {};
                let errorMsg = 'Terjadi kesalahan saat memperbarui alamat';
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
});
</script>
@endpush
