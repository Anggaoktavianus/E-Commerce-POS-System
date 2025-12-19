@extends('mobile.layouts.app')

@section('title', 'Daftar')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
  #map {
    width: 100%;
    height: 250px;
    border-radius: 12px;
    margin-top: 1rem;
  }
  
  .form-section {
    background: white;
    padding: 1rem;
    margin-bottom: 0.5rem;
    border-radius: 12px;
  }
  
  .form-section-title {
    font-size: 1rem;
    font-weight: 700;
    color: #147440;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }
  
  .form-group {
    margin-bottom: 1rem;
  }
  
  .form-label {
    display: block;
    font-size: 0.875rem;
    font-weight: 500;
    color: #333;
    margin-bottom: 0.5rem;
  }
  
  .form-label .required {
    color: #dc3545;
  }
  
  .form-control {
    width: 100%;
    padding: 0.875rem;
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    font-size: 0.875rem;
    transition: border-color 0.3s;
  }
  
  .form-control:focus {
    outline: none;
    border-color: #147440;
  }
  
  .form-select {
    width: 100%;
    padding: 0.875rem;
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    font-size: 0.875rem;
    background-color: white;
  }
  
  .btn-location {
    background: #147440;
    color: white;
    border: none;
    padding: 0.75rem 1rem;
    border-radius: 10px;
    font-size: 0.875rem;
    font-weight: 600;
    width: 100%;
    margin-top: 0.5rem;
    cursor: pointer;
  }
  
  .location-status {
    margin-top: 0.5rem;
    padding: 0.5rem;
    border-radius: 8px;
    font-size: 0.75rem;
    text-align: center;
  }
  
  .location-status.success {
    background: #e8f5e9;
    color: #2e7d32;
  }
  
  .location-status.error {
    background: #ffebee;
    color: #c62828;
  }
</style>
@endpush

@section('content')
<div style="padding: 1rem 0;">
  <!-- Header -->
  <div style="text-align: center; margin-bottom: 1.5rem; padding: 0 1rem;">
    <div style="width: 70px; height: 70px; background: linear-gradient(135deg, #147440 0%, #1a9c52 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; box-shadow: 0 4px 15px rgba(20, 116, 64, 0.3);">
      <i class="bx bx-user-plus" style="font-size: 2rem; color: white;"></i>
    </div>
    <h2 style="font-size: 1.25rem; font-weight: 700; color: #333; margin-bottom: 0.5rem;">Daftar Akun</h2>
    <p style="color: #666; font-size: 0.875rem;">Buat akun baru untuk mulai berbelanja</p>
  </div>

  <form method="POST" action="{{ route('customer.register') }}" id="registerForm">
    @csrf

    @if($errors->any())
      <div style="background: #ffebee; color: #c62828; padding: 0.75rem; border-radius: 8px; margin: 0 1rem 1rem; font-size: 0.875rem;">
        <i class="bx bx-error-circle"></i> {{ $errors->first() }}
      </div>
    @endif

    <!-- Informasi Pribadi -->
    <div class="form-section">
      <div class="form-section-title">
        <i class="bx bx-user"></i>
        <span>Informasi Pribadi</span>
      </div>

      <div class="form-group">
        <label class="form-label">
          Nama Lengkap <span class="required">*</span>
        </label>
        <input type="text" 
               name="name" 
               value="{{ old('name') }}"
               class="form-control"
               required
               placeholder="Masukkan nama lengkap">
      </div>

      <div class="form-group">
        <label class="form-label">
          Email <span class="required">*</span>
        </label>
        <input type="email" 
               name="email" 
               value="{{ old('email') }}"
               class="form-control"
               required
               placeholder="nama@email.com">
      </div>

      <div class="form-group">
        <label class="form-label">
          No. Telepon
        </label>
        <input type="tel" 
               name="phone" 
               value="{{ old('phone') }}"
               class="form-control"
               placeholder="08xxxxxxxxxx">
      </div>

      <div class="form-group">
        <label class="form-label">
          Password <span class="required">*</span>
        </label>
        <input type="password" 
               name="password" 
               class="form-control"
               required
               placeholder="Minimal 8 karakter"
               id="password">
      </div>

      <div class="form-group">
        <label class="form-label">
          Konfirmasi Password <span class="required">*</span>
        </label>
        <input type="password" 
               name="password_confirmation" 
               class="form-control"
               required
               placeholder="Ulangi password">
      </div>
    </div>

    <!-- Alamat -->
    <div class="form-section">
      <div class="form-section-title">
        <i class="bx bx-map"></i>
        <span>Alamat</span>
      </div>

      <div class="form-group">
        <label class="form-label">
          Provinsi <span class="required">*</span>
        </label>
        <select name="loc_provinsi_id" 
                id="loc_provinsi_id" 
                class="form-select"
                required>
          <option value="">Pilih Provinsi</option>
        </select>
      </div>

      <div class="form-group">
        <label class="form-label">
          Kabupaten/Kota <span class="required">*</span>
        </label>
        <select name="loc_kabkota_id" 
                id="loc_kabkota_id" 
                class="form-select"
                required
                disabled>
          <option value="">Pilih Provinsi terlebih dahulu</option>
        </select>
      </div>

      <div class="form-group">
        <label class="form-label">
          Kecamatan <span class="required">*</span>
        </label>
        <select name="loc_kecamatan_id" 
                id="loc_kecamatan_id" 
                class="form-select"
                required
                disabled>
          <option value="">Pilih Kabupaten/Kota terlebih dahulu</option>
        </select>
      </div>

      <div class="form-group">
        <label class="form-label">
          Desa/Kelurahan <span class="required">*</span>
        </label>
        <select name="loc_desa_id" 
                id="loc_desa_id" 
                class="form-select"
                required
                disabled>
          <option value="">Pilih Kecamatan terlebih dahulu</option>
        </select>
      </div>

      <div class="form-group">
        <label class="form-label">
          Alamat Lengkap <span class="required">*</span>
        </label>
        <textarea name="address" 
                  rows="3"
                  class="form-control"
                  required
                  placeholder="Masukkan alamat lengkap">{{ old('address') }}</textarea>
      </div>
    </div>

    <!-- Lokasi GPS -->
    <div class="form-section">
      <div class="form-section-title">
        <i class="bx bx-crosshair"></i>
        <span>Lokasi GPS</span>
      </div>

      <button type="button" 
              id="btnGetLocation" 
              class="btn-location">
        <i class="bx bx-crosshair"></i> Gunakan Lokasi GPS Saya
      </button>

      <div id="locationStatus" class="location-status" style="display: none;"></div>

      <div id="map"></div>

      <input type="hidden" 
             name="latitude" 
             id="latitude" 
             value="{{ old('latitude', '-7.0051') }}"
             required>
      <input type="hidden" 
             name="longitude" 
             id="longitude" 
             value="{{ old('longitude', '110.4381') }}"
             required>
    </div>

    <!-- Submit Button -->
    <div style="padding: 1rem; background: white; margin-top: 0.5rem; border-radius: 12px;">
      <button type="submit" 
              style="width: 100%; background: linear-gradient(135deg, #147440 0%, #1a9c52 100%); color: white; border: none; padding: 1rem; border-radius: 12px; font-weight: 700; font-size: 1rem; cursor: pointer; box-shadow: 0 4px 15px rgba(20, 116, 64, 0.3);">
        <i class="bx bx-user-plus"></i> Daftar Sekarang
      </button>
    </div>

    <!-- Login Link -->
    <div style="text-align: center; padding: 1.5rem 1rem;">
      <p style="color: #666; font-size: 0.875rem; margin-bottom: 1rem;">
        Sudah punya akun?
      </p>
      <a href="{{ route('mobile.login') }}" 
         style="display: inline-block; background: white; color: #147440; border: 2px solid #147440; padding: 0.875rem 1.5rem; border-radius: 10px; text-decoration: none; font-weight: 600; font-size: 0.875rem;">
        <i class="bx bx-log-in"></i> Masuk
      </a>
    </div>
  </form>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
  // Location dropdowns
  const provSel = document.getElementById('loc_provinsi_id');
  const kabSel = document.getElementById('loc_kabkota_id');
  const kecSel = document.getElementById('loc_kecamatan_id');
  const desaSel = document.getElementById('loc_desa_id');

  const oldProv = '{{ old('loc_provinsi_id') }}';
  const oldKab = '{{ old('loc_kabkota_id') }}';
  const oldKec = '{{ old('loc_kecamatan_id') }}';
  const oldDesa = '{{ old('loc_desa_id') }}';

  // Load Provinsi
  fetch('{{ route('api.locations.provinsis') }}')
    .then(r => r.json())
    .then(json => {
      (json.data || []).forEach(it => {
        const option = document.createElement('option');
        option.value = it.id;
        option.textContent = it.name;
        provSel.appendChild(option);
      });
      if (oldProv) {
        provSel.value = oldProv;
        loadKab(oldProv, true);
      }
    });

  provSel.addEventListener('change', function() {
    loadKab(this.value, false);
  });

  kabSel.addEventListener('change', function() {
    loadKec(this.value, false);
  });

  kecSel.addEventListener('change', function() {
    loadDesa(this.value, false);
  });

  function resetSelect(sel, ph) {
    sel.innerHTML = `<option value="">${ph}</option>`;
    sel.disabled = true;
  }

  function loadKab(pid, restoring) {
    resetSelect(kabSel, 'Pilih Kab/Kota');
    resetSelect(kecSel, 'Pilih Kecamatan');
    resetSelect(desaSel, 'Pilih Desa/Kelurahan');
    if (!pid) return;
    
    fetch(`{{ url('/api/locations/kabkotas') }}/${pid}`)
      .then(r => r.json())
      .then(j => {
        (j.data || []).forEach(it => {
          const option = document.createElement('option');
          option.value = it.id;
          option.textContent = it.name;
          kabSel.appendChild(option);
        });
        kabSel.disabled = false;
        if (restoring && oldKab) {
          kabSel.value = oldKab;
          loadKec(oldKab, true);
        }
      });
  }

  function loadKec(kid, restoring) {
    resetSelect(kecSel, 'Pilih Kecamatan');
    resetSelect(desaSel, 'Pilih Desa/Kelurahan');
    if (!kid) return;
    
    fetch(`{{ url('/api/locations/kecamatans') }}/${kid}`)
      .then(r => r.json())
      .then(j => {
        (j.data || []).forEach(it => {
          const option = document.createElement('option');
          option.value = it.id;
          option.textContent = it.name;
          kecSel.appendChild(option);
        });
        kecSel.disabled = false;
        if (restoring && oldKec) {
          kecSel.value = oldKec;
          loadDesa(oldKec, true);
        }
      });
  }

  function loadDesa(did, restoring) {
    resetSelect(desaSel, 'Pilih Desa/Kelurahan');
    if (!did) return;
    
    fetch(`{{ url('/api/locations/desas') }}/${did}`)
      .then(r => r.json())
      .then(j => {
        (j.data || []).forEach(it => {
          const option = document.createElement('option');
          option.value = it.id;
          option.textContent = it.name;
          desaSel.appendChild(option);
        });
        desaSel.disabled = false;
        if (restoring && oldDesa) {
          desaSel.value = oldDesa;
        }
      });
  }

  // Leaflet Map
  let map, marker;
  let defaultLat = -7.0051;
  let defaultLng = 110.4381;

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
      updateLocationStatus('Lokasi diperbarui', 'success');
    });

    map.on('click', function(event) {
      const clickedLocation = event.latlng;
      marker.setLatLng(clickedLocation);
      document.getElementById('latitude').value = clickedLocation.lat;
      document.getElementById('longitude').value = clickedLocation.lng;
      updateLocationStatus('Lokasi diperbarui', 'success');
    });
  }

  function updateLocationStatus(message, type) {
    const statusEl = document.getElementById('locationStatus');
    statusEl.textContent = message;
    statusEl.className = 'location-status ' + (type || 'success');
    statusEl.style.display = 'block';
    setTimeout(() => {
      statusEl.style.display = 'none';
    }, 3000);
  }

  // Get GPS Location
  document.getElementById('btnGetLocation').addEventListener('click', function() {
    const btn = this;
    btn.disabled = true;
    btn.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Mendeteksi lokasi...';

    if (!navigator.geolocation) {
      updateLocationStatus('Browser tidak mendukung GPS', 'error');
      btn.disabled = false;
      btn.innerHTML = '<i class="bx bx-crosshair"></i> Gunakan Lokasi GPS Saya';
      return;
    }

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

        updateLocationStatus('Lokasi GPS berhasil dideteksi!', 'success');
        btn.disabled = false;
        btn.innerHTML = '<i class="bx bx-crosshair"></i> Gunakan Lokasi GPS Saya';
      },
      function(error) {
        let errorMsg = 'Gagal mendapatkan lokasi GPS.';
        if (error.code === 1) {
          errorMsg = 'Akses lokasi ditolak. Silakan aktifkan GPS di pengaturan.';
        } else if (error.code === 2) {
          errorMsg = 'Lokasi tidak ditemukan.';
        } else if (error.code === 3) {
          errorMsg = 'Waktu habis saat mendapatkan lokasi.';
        }
        updateLocationStatus(errorMsg, 'error');
        btn.disabled = false;
        btn.innerHTML = '<i class="bx bx-crosshair"></i> Gunakan Lokasi GPS Saya';
      },
      {
        enableHighAccuracy: true,
        timeout: 10000,
        maximumAge: 0
      }
    );
  });

  // Initialize map when page loads
  window.addEventListener('load', function() {
    setTimeout(initMap, 500);
  });
</script>
@endpush
