@extends('mobile.layouts.app')

@php
use Illuminate\Support\Facades\Auth;
@endphp

@section('title', 'Alamat Saya')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
  #map {
    width: 100%;
    height: 200px;
    border-radius: 12px;
    margin-top: 1rem;
  }
  
  .address-card {
    background: white;
    padding: 1rem;
    margin-bottom: 0.5rem;
    border-radius: 12px;
    border: 2px solid #e0e0e0;
  }
  
  .address-card.primary {
    border-color: #147440;
    background: linear-gradient(135deg, rgba(20, 116, 64, 0.05) 0%, rgba(26, 156, 82, 0.05) 100%);
  }
  
  .form-section {
    background: white;
    padding: 1rem;
    margin-bottom: 0.5rem;
    border-radius: 12px;
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
  
  .form-control {
    width: 100%;
    padding: 0.875rem;
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    font-size: 0.875rem;
  }
  
  .form-select {
    width: 100%;
    padding: 0.875rem;
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    font-size: 0.875rem;
    background-color: white;
  }
</style>
@endpush

@section('content')
@auth
<!-- Header -->
<div style="background: white; padding: 1rem; margin-bottom: 0.5rem; display: flex; justify-content: space-between; align-items: center;">
  <h5 style="font-size: 1rem; font-weight: 700; color: #333; margin: 0;">
    <i class="bx bx-map"></i> Alamat Saya
  </h5>
  <button type="button" 
          onclick="showAddForm()"
          style="background: #147440; color: white; border: none; padding: 0.5rem 1rem; border-radius: 8px; font-size: 0.875rem; font-weight: 600; cursor: pointer;">
    <i class="bx bx-plus"></i> Tambah
  </button>
</div>

<!-- Add/Edit Form (Hidden by default) -->
<div id="addressFormSection" class="form-section" style="display: none;">
  <h5 style="font-size: 1rem; font-weight: 700; margin-bottom: 1rem; color: #333;" id="formTitle">
    <i class="bx bx-plus-circle"></i> Tambah Alamat Baru
  </h5>
  
  <form id="addressForm">
    @csrf
    <input type="hidden" name="address_id" id="address_id">
    <input type="hidden" name="_method" id="form_method" value="POST">

    <div class="form-group">
      <label class="form-label">Label (opsional)</label>
      <input type="text" 
             name="label" 
             id="label"
             class="form-control"
             placeholder="Contoh: Rumah, Kantor">
    </div>

    <div class="form-group">
      <label class="form-label">Nama Penerima <span style="color: #dc3545;">*</span></label>
      <input type="text" 
             name="recipient_name" 
             id="recipient_name"
             class="form-control"
             required>
    </div>

    <div class="form-group">
      <label class="form-label">No. Telepon <span style="color: #dc3545;">*</span></label>
      <input type="tel" 
             name="recipient_phone" 
             id="recipient_phone"
             class="form-control"
             required>
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

    <div class="form-group">
      <label class="form-label">Alamat Lengkap <span style="color: #dc3545;">*</span></label>
      <textarea name="address" 
                id="address"
                rows="3"
                class="form-control"
                required></textarea>
    </div>

    <div class="form-group">
      <label class="form-label">Kode Pos</label>
      <input type="text" 
             name="postal_code" 
             id="postal_code"
             class="form-control">
    </div>

    <div class="form-group">
      <label class="form-label">Catatan</label>
      <textarea name="notes" 
                id="notes"
                rows="2"
                class="form-control"
                placeholder="Contoh: Dekat masjid, Rumah warna merah"></textarea>
    </div>

    <div class="form-group">
      <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
        <input type="checkbox" 
               name="is_primary" 
               id="is_primary"
               style="width: 18px; height: 18px; accent-color: #147440;">
        <span style="font-size: 0.875rem;">Jadikan alamat utama</span>
      </label>
    </div>

    <!-- GPS Location -->
    <div class="form-group">
      <label class="form-label">Lokasi GPS</label>
      <button type="button" 
              id="btnGetLocation" 
              style="background: #147440; color: white; border: none; padding: 0.75rem 1rem; border-radius: 10px; font-size: 0.875rem; font-weight: 600; width: 100%; cursor: pointer;">
        <i class="bx bx-crosshair"></i> Gunakan Lokasi GPS Saya
      </button>
      <div id="map"></div>
      <input type="hidden" name="latitude" id="latitude" value="">
      <input type="hidden" name="longitude" id="longitude" value="">
    </div>

    <div style="display: flex; gap: 0.5rem; margin-top: 1rem;">
      <button type="submit" 
              style="flex: 1; background: #147440; color: white; border: none; padding: 0.875rem; border-radius: 10px; font-weight: 600; font-size: 0.875rem; cursor: pointer;">
        <i class="bx bx-save"></i> Simpan
      </button>
      <button type="button" 
              onclick="hideAddForm()"
              style="flex: 1; background: #f0f0f0; color: #333; border: none; padding: 0.875rem; border-radius: 10px; font-weight: 600; font-size: 0.875rem; cursor: pointer;">
        <i class="bx bx-x"></i> Batal
      </button>
    </div>
  </form>
</div>

<!-- Addresses List -->
@if(isset($addresses) && $addresses->count() > 0)
  @foreach($addresses as $address)
    <div class="address-card {{ $address->is_primary ? 'primary' : '' }}" id="address-{{ $address->id }}">
      <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.75rem;">
        <div style="flex: 1;">
          @if($address->is_primary)
            <span style="display: inline-block; background: #147440; color: white; padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.75rem; font-weight: 600; margin-bottom: 0.5rem;">
              Alamat Utama
            </span>
          @endif
          @if($address->label)
            <div style="font-size: 0.875rem; font-weight: 600; color: #333; margin-bottom: 0.25rem;">
              {{ $address->label }}
            </div>
          @endif
          <div style="font-size: 0.875rem; font-weight: 500; color: #333; margin-bottom: 0.5rem;">
            {{ $address->recipient_name }}
          </div>
          <div style="font-size: 0.875rem; color: #666; line-height: 1.6; margin-bottom: 0.5rem;">
            {{ $address->address }}<br>
            {{ $address->loc_kabkota_name ?? $address->city ?? '' }}<br>
            {{ $address->loc_provinsi_name ?? $address->province ?? '' }}
            @if($address->postal_code)
              <br>{{ $address->postal_code }}
            @endif
          </div>
          <div style="font-size: 0.75rem; color: #999;">
            <i class="bx bx-phone"></i> {{ $address->recipient_phone }}
          </div>
        </div>
        <div style="display: flex; flex-direction: column; gap: 0.5rem; margin-left: 1rem;">
          @if(!$address->is_primary)
            <button type="button" 
                    onclick="setPrimary({{ $address->id }})"
                    style="background: #147440; color: white; border: none; padding: 0.5rem; border-radius: 6px; font-size: 0.75rem; cursor: pointer; white-space: nowrap;">
              <i class="bx bx-check"></i> Utama
            </button>
          @endif
          <button type="button" 
                  onclick="editAddress({{ $address->id }})"
                  style="background: #ffc107; color: white; border: none; padding: 0.5rem; border-radius: 6px; font-size: 0.75rem; cursor: pointer;">
            <i class="bx bx-edit"></i>
          </button>
          <button type="button" 
                  onclick="deleteAddress({{ $address->id }})"
                  style="background: #dc3545; color: white; border: none; padding: 0.5rem; border-radius: 6px; font-size: 0.75rem; cursor: pointer;">
            <i class="bx bx-trash"></i>
          </button>
        </div>
      </div>
    </div>
  @endforeach
@else
  <div class="empty-state">
    <i class="bx bx-map"></i>
    <p>Belum ada alamat tersimpan</p>
    <button type="button" 
            onclick="showAddForm()"
            style="background: #147440; color: white; border: none; padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 600; margin-top: 1rem; cursor: pointer;">
      <i class="bx bx-plus"></i> Tambah Alamat
    </button>
  </div>
@endif

@else
<div class="empty-state">
  <i class="bx bx-user"></i>
  <p>Silakan login untuk mengakses alamat</p>
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
  const addresses = @json(isset($addresses) ? $addresses : []);
  
  function showAddForm() {
    document.getElementById('addressFormSection').style.display = 'block';
    document.getElementById('formTitle').innerHTML = '<i class="bx bx-plus-circle"></i> Tambah Alamat Baru';
    document.getElementById('addressForm').reset();
    document.getElementById('address_id').value = '';
    document.getElementById('form_method').value = 'POST';
    document.getElementById('addressFormSection').scrollIntoView({ behavior: 'smooth' });
  }
  
  function hideAddForm() {
    document.getElementById('addressFormSection').style.display = 'none';
    document.getElementById('addressForm').reset();
  }
  
  function editAddress(id) {
    const address = addresses.find(a => a.id === id);
    if (!address) return;
    
    document.getElementById('addressFormSection').style.display = 'block';
    document.getElementById('formTitle').innerHTML = '<i class="bx bx-edit"></i> Edit Alamat';
    document.getElementById('address_id').value = address.id;
    document.getElementById('form_method').value = 'PUT';
    document.getElementById('label').value = address.label || '';
    document.getElementById('recipient_name').value = address.recipient_name;
    document.getElementById('recipient_phone').value = address.recipient_phone;
    document.getElementById('address').value = address.address;
    document.getElementById('postal_code').value = address.postal_code || '';
    document.getElementById('notes').value = address.notes || '';
    document.getElementById('is_primary').checked = address.is_primary;
    document.getElementById('latitude').value = address.latitude || '';
    document.getElementById('longitude').value = address.longitude || '';
    
    // Load location dropdowns
    if (address.loc_provinsi_id) {
      loadProvinsiAndSet(address.loc_provinsi_id, address.loc_kabkota_id, address.loc_kecamatan_id, address.loc_desa_id);
    }
    
    // Update map if coordinates exist
    if (address.latitude && address.longitude) {
      if (map && marker) {
        marker.setLatLng([address.latitude, address.longitude]);
        map.setView([address.latitude, address.longitude], 15);
      }
    }
    
    document.getElementById('addressFormSection').scrollIntoView({ behavior: 'smooth' });
  }
  
  function deleteAddress(id) {
    Swal.fire({
      title: 'Hapus Alamat?',
      text: 'Apakah Anda yakin ingin menghapus alamat ini?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#dc3545',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Ya, Hapus',
      cancelButtonText: 'Batal'
    }).then((result) => {
      if (result.isConfirmed) {
        MobileLoading.show('Menghapus alamat...');
        
        fetch(`{{ url('/user/addresses') }}/${id}`, {
          method: 'DELETE',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest'
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
            MobileNotification.success('Alamat berhasil dihapus');
            setTimeout(() => {
              location.reload();
            }, 1000);
          } else {
            MobileNotification.error(data.message || 'Gagal menghapus alamat');
          }
        })
        .catch(error => {
          MobileErrorHandler.handle(error, 'Delete Address');
        })
        .finally(() => {
          MobileLoading.hide();
        });
      }
    });
  }
  
  function setPrimary(id) {
    MobileLoading.show('Mengubah alamat utama...');
    
    fetch(`{{ url('/user/addresses') }}/${id}/set-primary`, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'X-Requested-With': 'XMLHttpRequest'
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
        MobileNotification.success('Alamat utama berhasil diubah');
        setTimeout(() => {
          location.reload();
        }, 1000);
      } else {
        MobileNotification.error(data.message || 'Gagal mengubah alamat utama');
      }
    })
    .catch(error => {
      MobileErrorHandler.handle(error, 'Set Primary Address');
    })
    .finally(() => {
      MobileLoading.hide();
    });
  }
  
  // Location dropdowns
  const provSel = document.getElementById('loc_provinsi_id');
  const kabSel = document.getElementById('loc_kabkota_id');
  const kecSel = document.getElementById('loc_kecamatan_id');
  const desaSel = document.getElementById('loc_desa_id');
  
  function loadProvinsiAndSet(provId, kabId, kecId, desaId) {
    if (!provId) {
      // Just load provinsi list
      fetch('{{ route('api.locations.provinsis') }}')
        .then(r => r.json())
        .then(json => {
          provSel.innerHTML = '<option value="">Pilih Provinsi</option>';
          (json.data || []).forEach(it => {
            const option = document.createElement('option');
            option.value = it.id;
            option.textContent = it.name;
            provSel.appendChild(option);
          });
        });
      return;
    }
    
    fetch('{{ route('api.locations.provinsis') }}')
      .then(r => r.json())
      .then(json => {
        provSel.innerHTML = '<option value="">Pilih Provinsi</option>';
        (json.data || []).forEach(it => {
          const option = document.createElement('option');
          option.value = it.id;
          option.textContent = it.name;
          if (it.id == provId) {
            option.selected = true;
          }
          provSel.appendChild(option);
        });
        provSel.disabled = false;
        if (provId) {
          loadKabAndSet(provId, kabId, kecId, desaId);
        }
      });
  }
  
  function loadKabAndSet(provId, kabId, kecId, desaId) {
    resetSelect(kabSel, 'Pilih Kab/Kota');
    resetSelect(kecSel, 'Pilih Kecamatan');
    resetSelect(desaSel, 'Pilih Desa/Kelurahan');
    if (!provId) return;
    
    fetch(`{{ url('/api/locations/kabkotas') }}/${provId}`)
      .then(r => r.json())
      .then(j => {
        (j.data || []).forEach(it => {
          const option = document.createElement('option');
          option.value = it.id;
          option.textContent = it.name;
          if (kabId && it.id == kabId) {
            option.selected = true;
          }
          kabSel.appendChild(option);
        });
        kabSel.disabled = false;
        if (kabId) {
          loadKecAndSet(kabId, kecId, desaId);
        }
      });
  }
  
  function loadKecAndSet(kabId, kecId, desaId) {
    resetSelect(kecSel, 'Pilih Kecamatan');
    resetSelect(desaSel, 'Pilih Desa/Kelurahan');
    if (!kabId) return;
    
    fetch(`{{ url('/api/locations/kecamatans') }}/${kabId}`)
      .then(r => r.json())
      .then(j => {
        (j.data || []).forEach(it => {
          const option = document.createElement('option');
          option.value = it.id;
          option.textContent = it.name;
          if (kecId && it.id == kecId) {
            option.selected = true;
          }
          kecSel.appendChild(option);
        });
        kecSel.disabled = false;
        if (kecId) {
          loadDesaAndSet(kecId, desaId);
        }
      });
  }
  
  function loadDesaAndSet(kecId, desaId) {
    resetSelect(desaSel, 'Pilih Desa/Kelurahan');
    if (!kecId) return;
    
    fetch(`{{ url('/api/locations/desas') }}/${kecId}`)
      .then(r => r.json())
      .then(j => {
        (j.data || []).forEach(it => {
          const option = document.createElement('option');
          option.value = it.id;
          option.textContent = it.name;
          if (desaId && it.id == desaId) {
            option.selected = true;
          }
          desaSel.appendChild(option);
        });
        desaSel.disabled = false;
      });
  }
  
  // Load Provinsi on page load
  fetch('{{ route('api.locations.provinsis') }}')
    .then(r => r.json())
    .then(json => {
      (json.data || []).forEach(it => {
        const option = document.createElement('option');
        option.value = it.id;
        option.textContent = it.name;
        provSel.appendChild(option);
      });
    });
  
  provSel.addEventListener('change', function() {
    loadKabAndSet(this.value, null, null, null);
  });
  
  kabSel.addEventListener('change', function() {
    loadKecAndSet(this.value, null, null);
  });
  
  kecSel.addEventListener('change', function() {
    loadDesaAndSet(this.value, null);
  });
  
  function resetSelect(sel, ph) {
    sel.innerHTML = `<option value="">${ph}</option>`;
    sel.disabled = true;
  }
  
  // Leaflet Map
  let map, marker;
  let defaultLat = -7.0051;
  let defaultLng = 110.4381;
  
  function initMap() {
    map = L.map('map').setView([defaultLat, defaultLng], 15);
    
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
    
    marker = L.marker([defaultLat, defaultLng], {
      draggable: true,
      icon: customIcon
    }).addTo(map);
    
    marker.on('dragend', function() {
      const position = marker.getLatLng();
      document.getElementById('latitude').value = position.lat;
      document.getElementById('longitude').value = position.lng;
    });
    
    map.on('click', function(event) {
      const clickedLocation = event.latlng;
      marker.setLatLng(clickedLocation);
      document.getElementById('latitude').value = clickedLocation.lat;
      document.getElementById('longitude').value = clickedLocation.lng;
    });
  }
  
  // Get GPS Location
  document.getElementById('btnGetLocation').addEventListener('click', function() {
    const btn = this;
    btn.disabled = true;
    btn.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Mendeteksi lokasi...';
    
    if (!navigator.geolocation) {
      Swal.fire({
        icon: 'error',
        title: 'GPS Tidak Tersedia',
        text: 'Browser tidak mendukung GPS',
        confirmButtonColor: '#147440',
        confirmButtonText: 'Mengerti',
        width: '90%',
        customClass: {
          popup: 'mobile-swal-popup'
        }
      });
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
        
        btn.disabled = false;
        btn.innerHTML = '<i class="bx bx-crosshair"></i> Gunakan Lokasi GPS Saya';
      },
      function(error) {
        Swal.fire({
          icon: 'error',
          title: 'Gagal Mendapatkan Lokasi',
          text: 'Gagal mendapatkan lokasi GPS. Pastikan GPS sudah diaktifkan.',
          confirmButtonColor: '#147440',
          confirmButtonText: 'Mengerti',
          width: '90%',
          customClass: {
            popup: 'mobile-swal-popup'
          }
        });
        btn.disabled = false;
        btn.innerHTML = '<i class="bx bx-crosshair"></i> Gunakan Lokasi GPS Saya';
      }
    );
  });
  
  // Form submission
  document.getElementById('addressForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const addressId = document.getElementById('address_id').value;
    const method = document.getElementById('form_method').value;
    
    // Add _method for PUT
    if (method === 'PUT') {
      formData.append('_method', 'PUT');
    }
    
    const url = addressId 
      ? `{{ url('/user/addresses') }}/${addressId}`
      : '{{ route('user.addresses.store') }}';
    
    MobileLoading.show(addressId ? 'Memperbarui alamat...' : 'Menyimpan alamat...');
    
    fetch(url, {
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
        MobileNotification.success(data.message || 'Alamat berhasil disimpan');
        setTimeout(() => {
          location.reload();
        }, 1000);
      } else {
        MobileNotification.error(data.message || 'Gagal menyimpan alamat');
      }
    })
    .catch(error => {
      MobileErrorHandler.handle(error, 'Save Address');
    })
    .finally(() => {
      MobileLoading.hide();
    });
  });
  
  // Initialize map
  window.addEventListener('load', function() {
    setTimeout(initMap, 500);
  });
</script>
@endpush
