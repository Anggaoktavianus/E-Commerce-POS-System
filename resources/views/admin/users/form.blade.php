@extends('admin.layouts.app')

@section('title', ($user ? 'Edit' : 'Create') . ' User')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Header Section -->
  <div class="card page-header-card mb-4">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center flex-wrap">
        <div>
          <h4 class="mb-1">
            <i class="bx bx-{{ $user ? 'edit' : 'plus' }} me-2 text-primary"></i>{{ $user ? 'Edit' : 'Tambah' }} Pengguna
          </h4>
          <p class="text-muted mb-0">{{ $user ? 'Ubah informasi pengguna' : 'Buat pengguna baru untuk sistem' }}</p>
        </div>
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-modern mt-2 mt-md-0">
          <i class="bx bx-arrow-back me-1"></i>Kembali
        </a>
      </div>
    </div>
  </div>

  <div class="card form-card">
    <div class="card-header">
      <h5 class="card-title mb-0 fw-bold">
        <i class="bx bx-info-circle me-2"></i>Informasi Pengguna
      </h5>
    </div>
    <div class="card-body">
      <form action="{{ $user ? route('admin.users.update', $user) : route('admin.users.store') }}" method="POST">
        @csrf
        @if($user)
          @method('PUT')
        @endif

        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Name</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $user->name ?? '') }}" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email', $user->email ?? '') }}" required>
          </div>
          <div class="col-md-4">
            <label class="form-label">Role</label>
            @php $currentRole = old('role', $user->role ?? 'customer'); @endphp
            <select name="role" id="role" class="form-select">
              @foreach($roles as $roleValue => $roleLabel)
                <option value="{{ $roleValue }}" {{ $currentRole === $roleValue ? 'selected' : '' }}>{{ $roleLabel }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label">Password {{ $user ? '(leave blank to keep)' : '' }}</label>
            <input type="password" name="password" class="form-control" autocomplete="new-password">
          </div>
          <div class="col-md-4 d-flex align-items-end">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="is_verified" value="1" id="is_verified" {{ old('is_verified', $user->is_verified ?? false) ? 'checked' : '' }}>
              <label class="form-check-label" for="is_verified"> Verified </label>
            </div>
          </div>
          <div class="col-md-6">
            <label class="form-label">Phone</label>
            <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone ?? '') }}">
          </div>
          <div class="col-md-6">
            <label class="form-label">Address</label>
            <textarea name="address" class="form-control" rows="2">{{ old('address', $user->address ?? '') }}</textarea>
          </div>

          <div class="col-md-6">
            <label class="form-label">Provinsi *</label>
            <select id="loc_provinsi_id" name="loc_provinsi_id" class="form-select" required>
              <option value="">Pilih Provinsi</option>
            </select>
            @error('loc_provinsi_id')<div class="text-danger small">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label class="form-label">Kab/Kota *</label>
            <select id="loc_kabkota_id" name="loc_kabkota_id" class="form-select" required>
              <option value="">Pilih Kab/Kota</option>
            </select>
            @error('loc_kabkota_id')<div class="text-danger small">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label class="form-label">Kecamatan *</label>
            <select id="loc_kecamatan_id" name="loc_kecamatan_id" class="form-select" required>
              <option value="">Pilih Kecamatan</option>
            </select>
            @error('loc_kecamatan_id')<div class="text-danger small">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label class="form-label">Desa/Kelurahan *</label>
            <select id="loc_desa_id" name="loc_desa_id" class="form-select" required>
              <option value="">Pilih Desa/Kelurahan</option>
            </select>
            @error('loc_desa_id')<div class="text-danger small">{{ $message }}</div>@enderror
          </div>
        </div>

        <div class="mt-4">
          <button class="btn btn-primary" type="submit">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
  const roleSel = document.getElementById('role');
  const provSel = document.getElementById('loc_provinsi_id');
  const kabSel = document.getElementById('loc_kabkota_id');
  const kecSel = document.getElementById('loc_kecamatan_id');
  const desaSel = document.getElementById('loc_desa_id');
  const oldProv = '{{ old('loc_provinsi_id', $user->loc_provinsi_id ?? '') }}';
  const oldKab = '{{ old('loc_kabkota_id', $user->loc_kabkota_id ?? '') }}';
  const oldKec = '{{ old('loc_kecamatan_id', $user->loc_kecamatan_id ?? '') }}';
  const oldDesa = '{{ old('loc_desa_id', $user->loc_desa_id ?? '') }}';

  function toggleRequiredByRole(){
    const isAdmin = roleSel.value === 'admin';
    [provSel, kabSel, kecSel, desaSel].forEach(sel => {
      if (isAdmin) sel.removeAttribute('required'); else sel.setAttribute('required','required');
    });
  }
  roleSel.addEventListener('change', toggleRequiredByRole);
  toggleRequiredByRole();

  fetch('{{ route('api.locations.provinsis') }}')
    .then(r => r.json())
    .then(json => {
      const $prov = $('#loc_provinsi_id');
      (json.data || []).forEach(it => { $prov.append(new Option(it.name, it.id, false, false)); });
      if (window.jQuery && $.fn.select2) { try { $prov.select2('destroy'); } catch(e){} $prov.select2({ width: '100%' }); }
      if (oldProv) { $prov.val(oldProv).trigger('change'); loadKab(oldProv, true); }
    });
  $('#loc_provinsi_id').on('change select2:select', function(){ loadKab(this.value, false); });
  $('#loc_kabkota_id').on('change select2:select', function(){ loadKec(this.value, false); });
  $('#loc_kecamatan_id').on('change select2:select', function(){ loadDesa(this.value, false); });
  function resetSelect(sel, ph){ sel.innerHTML = `<option value="">${ph}</option>`; }
  function loadKab(pid, restoring){
    resetSelect(kabSel,'Pilih Kab/Kota'); resetSelect(kecSel,'Pilih Kecamatan'); resetSelect(desaSel,'Pilih Desa/Kelurahan');
    if(!pid) return; fetch(`{{ url('/api/locations/kabkotas') }}/${pid}`).then(r=>r.json()).then(j=>{
      const $kab = $('#loc_kabkota_id');
      (j.data||[]).forEach(it=>{ $kab.append(new Option(it.name, it.id, false, false)); });
      if (window.jQuery && $.fn.select2) { try { $kab.select2('destroy'); } catch(e){} $kab.select2({ width: '100%' }); }
      if(restoring && oldKab){ $kab.val(oldKab).trigger('change'); loadKec(oldKab,true);} 
    }); }
  function loadKec(kid, restoring){ resetSelect(kecSel,'Pilih Kecamatan'); resetSelect(desaSel,'Pilih Desa/Kelurahan'); if(!kid) return; fetch(`{{ url('/api/locations/kecamatans') }}/${kid}`).then(r=>r.json()).then(j=>{ const $kec = $('#loc_kecamatan_id'); (j.data||[]).forEach(it=>{ $kec.append(new Option(it.name, it.id, false, false)); }); if (window.jQuery && $.fn.select2) { try { $kec.select2('destroy'); } catch(e){} $kec.select2({ width: '100%' }); } if(restoring && oldKec){ $kec.val(oldKec).trigger('change'); loadDesa(oldKec,true);} }); }
  function loadDesa(did, restoring){ resetSelect(desaSel,'Pilih Desa/Kelurahan'); if(!did) return; fetch(`{{ url('/api/locations/desas') }}/${did}`).then(r=>r.json()).then(j=>{ const $desa = $('#loc_desa_id'); (j.data||[]).forEach(it=>{ $desa.append(new Option(it.name, it.id, false, false)); }); if (window.jQuery && $.fn.select2) { try { $desa.select2('destroy'); } catch(e){} $desa.select2({ width: '100%' }); } if(restoring && oldDesa){ $desa.val(oldDesa).trigger('change'); } }); }
});
</script>
@endpush
