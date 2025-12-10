@extends('admin.layouts.app')

@section('title', $setting ? 'Edit Pengaturan' : 'Tambah Pengaturan')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Header -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h4 class="mb-1">
        <i class="bx bx-{{ $setting ? 'edit' : 'plus' }} me-2"></i>
        {{ $setting ? 'Edit' : 'Tambah' }} Pengaturan
      </h4>
      <p class="text-muted mb-0">
        {{ $setting ? 'Ubah nilai pengaturan yang sudah ada' : 'Tambah pengaturan baru untuk website Anda' }}
      </p>
    </div>
    <a href="{{ route('admin.settings.index') }}" class="btn btn-secondary">
      <i class="bx bx-arrow-back me-1"></i> Kembali
    </a>
  </div>

  <!-- Alert Info -->
  @if(!$setting)
  <div class="alert alert-info alert-dismissible" role="alert">
    <i class="bx bx-info-circle me-2"></i>
    <strong>Info:</strong> Sebelum menambah pengaturan baru, pastikan Anda memahami kegunaannya. 
    Lihat panduan di halaman utama untuk informasi lebih lanjut.
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  @endif

  <!-- Form Card -->
  <div class="card">
    <div class="card-header">
      <h5 class="mb-0">
        <i class="bx bx-cog me-2"></i>Detail Pengaturan
      </h5>
    </div>
    <div class="card-body">
      <form action="{{ $setting ? route('admin.settings.update', $setting->id) : route('admin.settings.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @if($setting)
          @method('PUT')
        @endif

        <div class="row g-4">
          <!-- Key Field -->
          <div class="col-md-6">
            <label class="form-label">
              <i class="bx bx-key text-primary me-1"></i>
              Kode Pengaturan
              <span class="text-danger">*</span>
            </label>
            <input type="text" name="key" class="form-control" 
                   value="{{ old('key', $setting->key ?? '') }}" 
                   required {{ $setting ? 'readonly' : '' }}
                   placeholder="contoh: homepage_title, contact_email">
            <div class="form-text">
              <i class="bx bx-info-circle"></i>
              Kode unik untuk pengaturan. Gunakan huruf kecil dan underscore. {{ $setting ? 'Tidak bisa diubah.' : 'Contoh: site_title, contact_phone' }}
            </div>
          </div>

          <!-- Type Field (for new settings) -->
          @if(!$setting)
          <div class="col-md-6">
            <label class="form-label">
              <i class="bx bx-category text-info me-1"></i>
              Tipe Data
              <span class="text-danger">*</span>
            </label>
            <select name="type" class="form-select" required>
              <option value="">Pilih tipe data</option>
              <option value="text" {{ old('type') == 'text' ? 'selected' : '' }}>Teks (Text)</option>
              <option value="textarea" {{ old('type') == 'textarea' ? 'selected' : '' }}>Teks Panjang (Textarea)</option>
              <option value="number" {{ old('type') == 'number' ? 'selected' : '' }}>Angka (Number)</option>
              <option value="boolean" {{ old('type') == 'boolean' ? 'selected' : '' }}>Benar/Salah (Boolean)</option>
              <option value="file" {{ old('type') == 'file' ? 'selected' : '' }}>File/Gambar</option>
            </select>
            <div class="form-text">
              <i class="bx bx-info-circle"></i>
              Pilih tipe data yang sesuai untuk pengaturan ini
            </div>
          </div>
          @endif

          <!-- Description Field -->
          <div class="col-12">
            <label class="form-label">
              <i class="bx bx-file-text text-success me-1"></i>
              Deskripsi
            </label>
            <textarea name="description" rows="2" class="form-control" 
                      placeholder="Jelaskan fungsi pengaturan ini (contoh: Judul utama halaman homepage)">{{ old('description', $setting->description ?? '') }}</textarea>
            <div class="form-text">
              <i class="bx bx-info-circle"></i>
              Jelaskan kegunaan pengaturan ini untuk memudahkan pengelolaan di masa depan
            </div>
          </div>

          <!-- Value Field -->
          <div class="col-12">
            <label class="form-label">
              <i class="bx bx-edit-alt text-warning me-1"></i>
              Nilai Pengaturan
              <span class="text-danger">*</span>
            </label>
            <textarea name="value" rows="4" class="form-control" 
                      placeholder="Masukkan nilai pengaturan">{{ old('value', $setting->value ?? '') }}</textarea>
            <div class="form-text">
              <i class="bx bx-info-circle"></i>
              Nilai yang akan ditampilkan di website. Contoh: "Selamat Datang di Toko Kami"
            </div>
          </div>

          <!-- File Upload for Specific Keys -->
          @php
            $currentKey = old('key', $setting->key ?? '');
            $isFileSetting = in_array($currentKey, ['hero_bg', 'payment_image_path', 'site_logo', 'site_name_logo']);
          @endphp
          @if($isFileSetting)
            <div class="col-md-6">
              <label class="form-label">
                <i class="bx bx-image text-primary me-1"></i>
                @if($currentKey === 'site_logo')
                  Logo Website
                @elseif($currentKey === 'site_name_logo')
                  Logo Nama Website
                @elseif($currentKey === 'payment_image_path')
                  Gambar Metode Pembayaran
                @else
                  Gambar Background Hero
                @endif
              </label>
              <input type="file" name="file" class="form-control" accept="image/*">
              
              <!-- Current File Preview -->
              @if(!empty($setting?->value))
                <div class="mt-2">
                  <small class="text-muted">File saat ini:</small>
                  <div class="border rounded p-2 mt-1 bg-light">
                    @if($currentKey === 'site_logo' || $currentKey === 'site_name_logo')
                      <img src="{{ asset($setting->value) }}" alt="{{ $currentKey }}" 
                           style="height:50px; object-fit: contain;" class="img-fluid">
                    @else
                      <img src="{{ asset($setting->value) }}" alt="{{ $currentKey }}" 
                           style="height:70px" class="img-fluid">
                    @endif
                  </div>
                </div>
              @endif
              
              <!-- File Info -->
              <div class="form-text">
                <i class="bx bx-info-circle"></i>
                @if($currentKey === 'site_logo')
                  Upload logo website (disarankan: 50x50px, format PNG/ JPG)
                @elseif($currentKey === 'site_name_logo')
                  Upload logo nama website (disarankan: 100x50px, format PNG/ JPG)
                @elseif($currentKey === 'payment_image_path')
                  Upload gambar metode pembayaran (disarankan: 300x100px)
                @else
                  Upload gambar background hero (disarankan: 1920x1080px)
                @endif
                <br>File akan menggantikan nilai teks jika diupload.
              </div>
            </div>
          @endif

          <!-- Category Display -->
          @if($setting)
          <div class="col-md-6">
            <label class="form-label">
              <i class="bx bx-tag text-secondary me-1"></i>
              Kategori
            </label>
            <div class="form-control-plaintext">
              {{ $setting->category ?? 'Tidak dikategorikan' }}
            </div>
          </div>
          @endif
        </div>

        <!-- Action Buttons -->
        <div class="mt-4 d-flex gap-2">
          <button type="submit" class="btn btn-primary">
            <i class="bx bx-save me-1"></i> Simpan Pengaturan
          </button>
          <a href="{{ route('admin.settings.index') }}" class="btn btn-secondary">
            <i class="bx bx-x me-1"></i> Batal
          </a>
        </div>
      </form>
    </div>
  </div>

  <!-- Help Guide -->
  <div class="card mt-4">
    <div class="card-header">
      <h5 class="mb-0">
        <i class="bx bx-help-circle me-2"></i>Panduan Pengisian
      </h5>
    </div>
    <div class="card-body">
      <div class="row">
        <div class="col-md-6">
          <h6><i class="bx bx-key text-primary me-2"></i>Kode Pengaturan</h6>
          <ul class="mb-3">
            <li>Gunakan format: <code>category_item_name</code></li>
            <li>Contoh: <code>homepage_title</code>, <code>contact_email</code></li>
            <li>Huruf kecil, gunakan underscore untuk spasi</li>
            <li>Tidak boleh ada spasi atau karakter khusus</li>
          </ul>
          
          <h6><i class="bx bx-edit-alt text-warning me-2"></i>Nilai Pengaturan</h6>
          <ul class="mb-3">
            <li>Tulis nilai yang akan muncul di website</li>
            <li>Bisa berupa teks, angka, atau HTML</li>
            <li>Untuk boolean: gunakan <code>true</code> atau <code>false</code></li>
            <li>Perubahan langsung terlihat di website</li>
          </ul>
        </div>
        <div class="col-md-6">
          <h6><i class="bx bx-file-text text-success me-2"></i>Deskripsi</h6>
          <ul class="mb-3">
            <li>Jelaskan fungsi pengaturan ini</li>
            <li>Contoh: "Judul utama di halaman homepage"</li>
            <li>Membantu tim lain memahami kegunaan</li>
            <li>Penting untuk pengelolaan jangka panjang</li>
          </ul>
          
          <h6><i class="bx bx-error text-danger me-2"></i>Yang Perlu Dihindari</h6>
          <ul class="mb-3">
            <li>Jangan gunakan spasi di kode pengaturan</li>
            <li>Jangan hapus pengaturan penting</li>
            <li>Periksa kembali sebelum menyimpan</li>
            <li>Backup data sebelum perubahan besar</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
