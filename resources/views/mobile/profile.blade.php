@extends('mobile.layouts.app')

@php
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
@endphp

@section('title', 'Profil Saya')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
  #map {
    width: 100%;
    height: 200px;
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
@auth
<!-- Profile Header -->
<div style="background: linear-gradient(135deg, #147440 0%, #1a9c52 100%); padding: 1.5rem; color: white; margin-bottom: 0.5rem;">
  <div style="display: flex; align-items: center; gap: 1rem;">
    <div style="width: 70px; height: 70px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 2rem; font-weight: 700;">
      {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
    </div>
    <div style="flex: 1;">
      <h4 style="font-size: 1.125rem; font-weight: 700; margin-bottom: 0.25rem;">{{ Auth::user()->name }}</h4>
      <p style="font-size: 0.875rem; opacity: 0.9; margin: 0;">{{ Auth::user()->email }}</p>
    </div>
  </div>
</div>

<!-- Tabs -->
<div style="background: white; padding: 0.5rem; margin-bottom: 0.5rem; display: flex; gap: 0.5rem; border-radius: 12px;">
  <button type="button" 
          onclick="showTab('profile')" 
          id="tab-profile"
          style="flex: 1; padding: 0.75rem; border: none; background: #147440; color: white; border-radius: 8px; font-weight: 600; font-size: 0.875rem; cursor: pointer;">
    Profil
  </button>
  <button type="button" 
          onclick="showTab('password')" 
          id="tab-password"
          style="flex: 1; padding: 0.75rem; border: none; background: #f0f0f0; color: #666; border-radius: 8px; font-weight: 600; font-size: 0.875rem; cursor: pointer;">
    Password
  </button>
</div>

<!-- Profile Tab -->
<div id="tab-content-profile" class="tab-content">
  <form id="profileForm">
    @csrf
    @method('PUT')
    
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
               id="name"
               value="{{ Auth::user()->name }}"
               class="form-control"
               required>
      </div>

      <div class="form-group">
        <label class="form-label">
          Email <span class="required">*</span>
        </label>
        <input type="email" 
               name="email" 
               id="email"
               value="{{ Auth::user()->email }}"
               class="form-control"
               required>
      </div>

      <div class="form-group">
        <label class="form-label">
          No. Telepon
        </label>
        <input type="tel" 
               name="phone" 
               id="phone"
               value="{{ Auth::user()->phone }}"
               class="form-control">
      </div>

      <div class="form-group">
        <label class="form-label">
          Alamat
        </label>
        <textarea name="address" 
                  id="address"
                  rows="3"
                  class="form-control">{{ Auth::user()->address }}</textarea>
      </div>
    </div>

    <!-- Lokasi -->
    <div class="form-section">
      <div class="form-section-title">
        <i class="bx bx-map"></i>
        <span>Lokasi</span>
      </div>

      <div class="form-group">
        <label class="form-label">Provinsi</label>
        <select name="loc_provinsi_id" 
                id="loc_provinsi_id" 
                class="form-select">
          <option value="">Pilih Provinsi</option>
        </select>
      </div>

      <div class="form-group">
        <label class="form-label">Kabupaten/Kota</label>
        <select name="loc_kabkota_id" 
                id="loc_kabkota_id" 
                class="form-select"
                disabled>
          <option value="">Pilih Provinsi terlebih dahulu</option>
        </select>
      </div>

      <div class="form-group">
        <label class="form-label">Kecamatan</label>
        <select name="loc_kecamatan_id" 
                id="loc_kecamatan_id" 
                class="form-select"
                disabled>
          <option value="">Pilih Kabupaten/Kota terlebih dahulu</option>
        </select>
      </div>

      <div class="form-group">
        <label class="form-label">Desa/Kelurahan</label>
        <select name="loc_desa_id" 
                id="loc_desa_id" 
                class="form-select"
                disabled>
          <option value="">Pilih Kecamatan terlebih dahulu</option>
        </select>
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
             value="{{ Auth::user()->latitude ?: '-7.0051' }}">
      <input type="hidden" 
             name="longitude" 
             id="longitude" 
             value="{{ Auth::user()->longitude ?: '110.4381' }}">
    </div>

    <!-- Submit Button -->
    <div style="padding: 1rem; background: white; margin-top: 0.5rem; border-radius: 12px;">
      <button type="submit" 
              style="width: 100%; background: linear-gradient(135deg, #147440 0%, #1a9c52 100%); color: white; border: none; padding: 1rem; border-radius: 12px; font-weight: 700; font-size: 1rem; cursor: pointer; box-shadow: 0 4px 15px rgba(20, 116, 64, 0.3);">
        <i class="bx bx-save"></i> Simpan Perubahan
      </button>
    </div>
  </form>
</div>

<!-- Password Tab -->
<div id="tab-content-password" class="tab-content" style="display: none;">
  <form id="passwordForm">
    @csrf
    @method('PUT')
    
    <div class="form-section">
      <div class="form-section-title">
        <i class="bx bx-lock"></i>
        <span>Ubah Password</span>
      </div>

      <div class="form-group">
        <label class="form-label">
          Password Lama <span class="required">*</span>
        </label>
        <input type="password" 
               name="current_password" 
               id="current_password"
               class="form-control"
               required
               placeholder="Masukkan password lama">
      </div>

      <div class="form-group">
        <label class="form-label">
          Password Baru <span class="required">*</span>
        </label>
        <input type="password" 
               name="password" 
               id="password"
               class="form-control"
               required
               placeholder="Minimal 8 karakter">
      </div>

      <div class="form-group">
        <label class="form-label">
          Konfirmasi Password Baru <span class="required">*</span>
        </label>
        <input type="password" 
               name="password_confirmation" 
               id="password_confirmation"
               class="form-control"
               required
               placeholder="Ulangi password baru">
      </div>

      <div style="padding: 1rem; background: white; margin-top: 0.5rem; border-radius: 12px;">
        <button type="submit" 
                style="width: 100%; background: linear-gradient(135deg, #147440 0%, #1a9c52 100%); color: white; border: none; padding: 1rem; border-radius: 12px; font-weight: 700; font-size: 1rem; cursor: pointer; box-shadow: 0 4px 15px rgba(20, 116, 64, 0.3);">
          <i class="bx bx-lock"></i> Ubah Password
        </button>
      </div>
    </div>
  </form>
</div>

@else
<div class="empty-state">
  <i class="bx bx-user"></i>
  <p>Silakan login untuk mengakses profil</p>
  <a href="{{ route('mobile.login') }}" 
     style="display: inline-block; background: #147440; color: white; padding: 0.75rem 1.5rem; border-radius: 8px; text-decoration: none; font-weight: 600; margin-top: 1rem;">
    <i class="bx bx-log-in"></i> Login
  </a>
</div>
@endauth
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  // Tab switching
  function showTab(tab) {
    document.querySelectorAll('.tab-content').forEach(content => {
      content.style.display = 'none';
    });
    document.querySelectorAll('[id^="tab-"]').forEach(btn => {
      btn.style.background = '#f0f0f0';
      btn.style.color = '#666';
    });
    
    document.getElementById('tab-content-' + tab).style.display = 'block';
    document.getElementById('tab-' + tab).style.background = '#147440';
    document.getElementById('tab-' + tab).style.color = 'white';
  }

  // Location dropdowns
  const provSel = document.getElementById('loc_provinsi_id');
  const kabSel = document.getElementById('loc_kabkota_id');
  const kecSel = document.getElementById('loc_kecamatan_id');
  const desaSel = document.getElementById('loc_desa_id');

  const userProv = {{ Auth::user()->loc_provinsi_id ?: 'null' }};
  const userKab = {{ Auth::user()->loc_kabkota_id ?: 'null' }};
  const userKec = {{ Auth::user()->loc_kecamatan_id ?: 'null' }};
  const userDesa = {{ Auth::user()->loc_desa_id ?: 'null' }};

  // Load Provinsi
  fetch('{{ route('api.locations.provinsis') }}')
    .then(r => r.json())
    .then(json => {
      (json.data || []).forEach(it => {
        const option = document.createElement('option');
        option.value = it.id;
        option.textContent = it.name;
        if (userProv && it.id == userProv) {
          option.selected = true;
        }
        provSel.appendChild(option);
      });
      if (userProv) {
        loadKab(userProv, true);
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
          if (restoring && userKab && it.id == userKab) {
            option.selected = true;
          }
          kabSel.appendChild(option);
        });
        kabSel.disabled = false;
        if (restoring && userKab) {
          loadKec(userKab, true);
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
          if (restoring && userKec && it.id == userKec) {
            option.selected = true;
          }
          kecSel.appendChild(option);
        });
        kecSel.disabled = false;
        if (restoring && userKec) {
          loadDesa(userKec, true);
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
          if (restoring && userDesa && it.id == userDesa) {
            option.selected = true;
          }
          desaSel.appendChild(option);
        });
        desaSel.disabled = false;
      });
  }

  // Leaflet Map
  let map, marker;
  let defaultLat = -7.0051;
  let defaultLng = 110.4381;
  let userLat = {{ Auth::user()->latitude ?: 'null' }};
  let userLng = {{ Auth::user()->longitude ?: 'null' }};

  function initMap() {
    const initialLat = userLat || defaultLat;
    const initialLng = userLng || defaultLng;

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

  // Profile form submission
  document.getElementById('profileForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    // Add _method for PUT
    formData.append('_method', 'PUT');
    
    MobileLoading.show('Menyimpan perubahan...');
    
    fetch('{{ route('customer.profile.update') }}', {
      method: 'POST',
      body: formData,
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
      }
    })
    .then(response => {
      if (!response.ok) {
        return response.json().then(data => {
          throw { response: { status: response.status, data: data } };
        });
      }
      return response.json();
    })
    .then(data => {
      if (data.success) {
        MobileNotification.success('Profil berhasil diperbarui');
        setTimeout(() => {
          location.reload();
        }, 1000);
      } else {
        MobileNotification.error(data.message || 'Gagal memperbarui profil');
      }
    })
    .catch(error => {
      MobileErrorHandler.handle(error, 'Update Profile');
    })
    .finally(() => {
      MobileLoading.hide();
    });
  });

  // Password form submission
  document.getElementById('passwordForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    // Add _method for PUT
    formData.append('_method', 'PUT');
    
    MobileLoading.show('Mengubah password...');
    
    fetch('{{ route('customer.profile.password.update') }}', {
      method: 'POST',
      body: formData,
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
      }
    })
    .then(response => {
      if (!response.ok) {
        return response.json().then(data => {
          throw { response: { status: response.status, data: data } };
        });
      }
      return response.json();
    })
    .then(data => {
      if (data.success) {
        MobileNotification.success('Password berhasil diubah');
        document.getElementById('passwordForm').reset();
      } else {
        MobileNotification.error(data.message || 'Gagal mengubah password');
      }
    })
    .catch(error => {
      MobileErrorHandler.handle(error, 'Update Password');
    })
    .finally(() => {
      MobileLoading.hide();
    });
  });

  // Initialize map when page loads
  window.addEventListener('load', function() {
    setTimeout(initMap, 500);
  });
</script>
@endpush
