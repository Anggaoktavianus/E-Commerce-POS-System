@extends('admin.layouts.app')

@section('title', 'Tambah Kategori Artikel')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Header Section -->
  <div class="card page-header-card mb-4">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center flex-wrap">
        <div>
          <h4 class="mb-1">
            <i class="bx bx-plus me-2 text-primary"></i>Tambah Kategori Artikel
          </h4>
          <p class="text-muted mb-0">Buat kategori baru untuk mengelompokkan artikel</p>
        </div>
        <a href="{{ route('admin.kategori_artikel.index') }}" class="btn btn-secondary btn-modern mt-2 mt-md-0">
          <i class="bx bx-arrow-back me-1"></i>Kembali
        </a>
      </div>
    </div>
  </div>

  <!-- Form Section -->
  <div class="card form-card">
    <div class="card-header">
      <h5 class="card-title text-white mb-0 fw-bold">
        <i class="bx bx-edit me-2 text-white"></i>Informasi Kategori
      </h5>
    </div>
    <div class="card-body">
      <form action="{{ route('admin.kategori_artikel.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row">
          <div class="col-md-8">
            <!-- Nama Kategori -->
            <div class="mb-3">
              <label for="nama" class="form-label">Nama Kategori <span class="text-danger">*</span></label>
              <input type="text" class="form-control @error('nama') is-invalid @enderror"
                     id="nama" name="nama" value="{{ old('nama') }}"
                     placeholder="Masukkan nama kategori" required>
              @error('nama')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
              <small class="text-muted">Nama kategori akan digunakan untuk slug otomatis</small>
            </div>

            <!-- Slug -->
            <div class="mb-3">
              <label for="slug" class="form-label">Slug</label>
              <input type="text" class="form-control @error('slug') is-invalid @enderror"
                     id="slug" name="slug" value="{{ old('slug') }}"
                     placeholder="slug-otomatis-dari-nama">
              @error('slug')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
              <small class="text-muted">Slug akan digunakan dalam URL. Kosongkan untuk generate otomatis.</small>
            </div>

            <!-- Deskripsi -->
            <div class="mb-3">
              <label for="deskripsi" class="form-label">Deskripsi</label>
              <textarea class="form-control @error('deskripsi') is-invalid @enderror"
                        id="deskripsi" name="deskripsi" rows="4"
                        placeholder="Deskripsi kategori artikel">{{ old('deskripsi') }}</textarea>
              @error('deskripsi')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
              <small class="text-muted">Jelaskan tentang kategori ini untuk membantu pengguna memahami kontennya</small>
            </div>

            <!-- Status -->
            <div class="mb-3">
              <label class="form-label">Status <span class="text-danger">*</span></label>
              <div class="form-check form-check-inline">
                <input class="form-check-input @error('status') is-invalid @enderror"
                       type="radio" name="status" id="status_aktif" value="aktif"
                       {{ old('status', 'aktif') == 'aktif' ? 'checked' : '' }} required>
                <label class="form-check-label" for="status_aktif">
                  <span class="badge bg-success">Aktif</span>
                </label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input @error('status') is-invalid @enderror"
                       type="radio" name="status" id="status_nonaktif" value="nonaktif"
                       {{ old('status') == 'nonaktif' ? 'checked' : '' }}>
                <label class="form-check-label" for="status_nonaktif">
                  <span class="badge bg-secondary">Non-Aktif</span>
                </label>
              </div>
              @error('status')
                <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
              <small class="text-muted d-block">Kategori aktif akan ditampilkan di website</small>
            </div>
          </div>

          <div class="col-md-4">
            <!-- Gambar Kategori -->
            <div class="mb-3">
              <label for="gambar" class="form-label">Gambar Kategori</label>
              <div class="border rounded p-3 text-center bg-light">
                <div id="imagePreview" class="mb-3">
                  <i class="bx bx-image bx-5x text-muted"></i>
                </div>
                <input type="file" class="form-control @error('gambar') is-invalid @enderror"
                       id="gambar" name="gambar" accept="image/*" onchange="previewImage(event)">
                @error('gambar')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="text-muted">Format: JPG, PNG, GIF. Maks: 2MB</small>
              </div>
            </div>

            <!-- Tips -->
            <div class="alert alert-info">
              <h6 class="alert-heading">
                <i class="bx bx-info-circle me-2"></i>Tips:
              </h6>
              <ul class="mb-0 small">
                <li>Gunakan nama yang deskriptif dan mudah dipahami</li>
                <li>Slug otomatis akan di-generate dari nama</li>
                <li>Gambar opsional untuk visual yang lebih menarik</li>
                <li>Status aktif membuat kategori bisa dipilih saat membuat artikel</li>
              </ul>
            </div>
          </div>
        </div>

        <!-- Form Actions -->
        <div class="row mt-4">
          <div class="col-12">
            <div class="d-flex justify-content-between">
              <a href="{{ route('admin.kategori_artikel.index') }}" class="btn btn-secondary">
                <i class="bx bx-arrow-back me-1"></i> Batal
              </a>
              <div>
                <button type="reset" class="btn btn-outline-secondary me-2">
                  <i class="bx bx-refresh me-1"></i> Reset
                </button>
                <button type="submit" class="btn btn-primary">
                  <i class="bx bx-save me-1"></i> Simpan Kategori
                </button>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
    // Auto-generate slug from name
    document.getElementById('nama').addEventListener('input', function() {
        const nama = this.value;
        const slugField = document.getElementById('slug');

        if (!slugField.value || slugField.dataset.original === slugField.value) {
            slugField.value = nama.toLowerCase()
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-')
                .trim('-');
            slugField.dataset.original = slugField.value;
        }
    });

    // Preview image
    function previewImage(event) {
        const file = event.target.files[0];
        const preview = document.getElementById('imagePreview');

        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.innerHTML = '<img src="' + e.target.result + '" class="img-fluid rounded" style="max-height: 200px;" alt="Preview">';
            }
            reader.readAsDataURL(file);
        }
    }

    // Mark original slug value
    document.getElementById('slug').addEventListener('input', function() {
        this.dataset.original = this.value;
    });
</script>
@endpush
