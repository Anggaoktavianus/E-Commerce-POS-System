@extends('admin.layouts.app')

@section('title', 'Edit Artikel')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Header Section -->
  <div class="card page-header-card mb-4">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center flex-wrap">
        <div>
          <h4 class="mb-1">
            <i class="bx bx-edit me-2 text-primary"></i>Edit Artikel
          </h4>
          <p class="text-muted mb-0">Perbarui informasi artikel</p>
        </div>
        <div class="d-flex gap-2 mt-2 mt-md-0">
          <a href="{{ route('admin.artikel.show', $artikel) }}" class="btn btn-info btn-modern">
            <i class="bx bx-eye me-1"></i>Lihat
          </a>
          <a href="{{ route('admin.artikel.index') }}" class="btn btn-secondary btn-modern">
            <i class="bx bx-arrow-back me-1"></i>Kembali
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- Form Section -->
  <form action="{{ route('admin.artikel.update', $artikel) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    
    <div class="row">
      <!-- Main Content -->
      <div class="col-md-8">
        <div class="card form-card mb-4">
          <div class="card-header">
            <h5 class="card-title mb-0 fw-bold text-white">
              <i class="bx bx-edit me-2"></i>Konten Artikel
            </h5>
          </div>
          <div class="card-body">
            <!-- Judul -->
            <div class="mb-3">
              <label for="judul" class="form-label">Judul Artikel <span class="text-danger">*</span></label>
              <input type="text" class="form-control @error('judul') is-invalid @enderror" 
                     id="judul" name="judul" value="{{ old('judul', $artikel->judul) }}" 
                     placeholder="Masukkan judul artikel yang menarik" required>
              @error('judul')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
              <small class="text-muted">Judul akan digunakan untuk slug otomatis dan SEO</small>
            </div>

            <!-- Slug -->
            <div class="mb-3">
              <label for="slug" class="form-label">Slug</label>
              <input type="text" class="form-control @error('slug') is-invalid @enderror" 
                     id="slug" name="slug" value="{{ old('slug', $artikel->slug) }}" 
                     placeholder="slug-otomatis-dari-judul">
              @error('slug')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
              <small class="text-muted">Slug akan digunakan dalam URL. Kosongkan untuk generate otomatis.</small>
            </div>

            <!-- Konten -->
            <div class="mb-3">
              <label for="konten" class="form-label">Konten Artikel <span class="text-danger">*</span></label>
              <textarea class="form-control @error('konten') is-invalid @enderror" 
                        id="konten" name="konten" rows="15" 
                        placeholder="Tulis konten artikel di sini...">{{ old('konten', $artikel->konten) }}</textarea>
              @error('konten')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
              <small class="text-muted">Gunakan editor untuk rich content. Support upload gambar dan file.</small>
            </div>
          </div>
        </div>

        <!-- SEO Settings -->
        <div class="card">
          <div class="card-header">
            <h5 class="mb-0">
              <i class="bx bx-search-alt me-2"></i>SEO Settings
            </h5>
          </div>
          <div class="card-body">
            <!-- Meta Title -->
            <div class="mb-3">
              <label for="meta_title" class="form-label">Meta Title</label>
              <input type="text" class="form-control @error('meta_title') is-invalid @enderror" 
                     id="meta_title" name="meta_title" value="{{ old('meta_title', $artikel->meta_title) }}" 
                     placeholder="Meta title untuk SEO (maks 60 karakter)">
              @error('meta_title')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
              <small class="text-muted">Title yang muncul di search engine. Kosongkan untuk gunakan judul artikel.</small>
            </div>

            <!-- Meta Description -->
            <div class="mb-3">
              <label for="meta_description" class="form-label">Meta Description</label>
              <textarea class="form-control @error('meta_description') is-invalid @enderror" 
                        id="meta_description" name="meta_description" rows="3" 
                        placeholder="Meta description untuk SEO (maks 160 karakter)">{{ old('meta_description', $artikel->meta_description) }}</textarea>
              @error('meta_description')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
              <small class="text-muted">Deskripsi yang muncul di search engine.</small>
            </div>

            <!-- Meta Keywords -->
            <div class="mb-3">
              <label for="meta_keywords" class="form-label">Meta Keywords</label>
              <input type="text" class="form-control @error('meta_keywords') is-invalid @enderror" 
                     id="meta_keywords" name="meta_keywords" value="{{ old('meta_keywords', $artikel->meta_keywords) }}" 
                     placeholder="keyword1, keyword2, keyword3">
              @error('meta_keywords')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
              <small class="text-muted">Pisahkan dengan koma. Maks 10 keywords.</small>
            </div>
          </div>
        </div>
      </div>

      <!-- Sidebar -->
      <div class="col-md-4">
        <!-- Publish Settings -->
        <div class="card mb-4">
          <div class="card-header">
            <h5 class="mb-0">
              <i class="bx bx-cog me-2"></i>Publish Settings
            </h5>
          </div>
          <div class="card-body">
            <!-- Kategori -->
            <div class="mb-3">
              <label for="kategori_artikel_id" class="form-label">Kategori <span class="text-danger">*</span></label>
              <select class="form-select @error('kategori_artikel_id') is-invalid @enderror" 
                      id="kategori_artikel_id" name="kategori_artikel_id" required>
                <option value="">Pilih Kategori</option>
                @foreach($kategori as $kat)
                  <option value="{{ $kat->id }}" {{ old('kategori_artikel_id', $artikel->kategori_artikel_id) == $kat->id ? 'selected' : '' }}>
                    {{ $kat->nama }}
                  </option>
                @endforeach
              </select>
              @error('kategori_artikel_id')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <!-- Status -->
            <div class="mb-3">
              <label class="form-label">Status <span class="text-danger">*</span></label>
              <div class="form-check">
                <input class="form-check-input @error('status') is-invalid @enderror" 
                       type="radio" name="status" id="status_draft" value="draft" 
                       {{ old('status', $artikel->status) == 'draft' ? 'checked' : '' }} required>
                <label class="form-check-label" for="status_draft">
                  <span class="badge bg-secondary">Draft</span> - Belum dipublish
                </label>
              </div>
              <div class="form-check">
                <input class="form-check-input @error('status') is-invalid @enderror" 
                       type="radio" name="status" id="status_published" value="published" 
                       {{ old('status', $artikel->status) == 'published' ? 'checked' : '' }}>
                <label class="form-check-label" for="status_published">
                  <span class="badge bg-success">Published</span> - Tayang sekarang
                </label>
              </div>
              <div class="form-check">
                <input class="form-check-input @error('status') is-invalid @enderror" 
                       type="radio" name="status" id="status_archived" value="archived" 
                       {{ old('status', $artikel->status) == 'archived' ? 'checked' : '' }}>
                <label class="form-check-label" for="status_archived">
                  <span class="badge bg-warning">Archived</span> - Diarsipkan
                </label>
              </div>
              @error('status')
                <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>

            <!-- Published At -->
            <div class="mb-3">
              <label for="published_at" class="form-label">Publish Date</label>
              <input type="datetime-local" class="form-control @error('published_at') is-invalid @enderror" 
                     id="published_at" name="published_at" value="{{ old('published_at', $artikel->published_at ? $artikel->published_at->format('Y-m-d\TH:i') : '') }}">
              @error('published_at')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
              <small class="text-muted">Kosongkan untuk publish sekarang (jika status Published)</small>
            </div>

            <!-- Statistics -->
            <div class="alert alert-info">
              <h6 class="alert-heading">
                <i class="bx bx-bar-chart me-2"></i>Statistik:
              </h6>
              <div class="row small">
                <div class="col-6">
                  <strong>Views:</strong> {{ number_format($artikel->views) }}
                </div>
                <div class="col-6">
                  <strong>Reading Time:</strong> {{ $artikel->reading_time }} min
                </div>
                <div class="col-6">
                  <strong>Dibuat:</strong> {{ $artikel->created_at->format('d M Y') }}
                </div>
                <div class="col-6">
                  <strong>Diperbarui:</strong> {{ $artikel->updated_at->format('d M Y') }}
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Media -->
        <div class="card mb-4">
          <div class="card-header">
            <h5 class="mb-0">
              <i class="bx bx-image me-2"></i>Media
            </h5>
          </div>
          <div class="card-body">
            <!-- Gambar Utama -->
            <div class="mb-3">
              <label for="gambar_utama" class="form-label">Gambar Utama</label>
              <div class="border rounded p-3 text-center bg-light">
                <div id="mainImagePreview" class="mb-3">
                  @if($artikel->gambar_utama)
                    <img src="{{ Storage::url($artikel->gambar_utama) }}" class="img-fluid rounded" style="max-height: 200px;" alt="Current Main Image">
                  @else
                    <i class="bx bx-image bx-3x text-muted"></i>
                  @endif
                </div>
                <input type="file" class="form-control @error('gambar_utama') is-invalid @enderror" 
                       id="gambar_utama" name="gambar_utama" accept="image/*" onchange="previewMainImage(event)">
                @error('gambar_utama')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="text-muted">Format: JPG, PNG, GIF. Maks: 2MB. Kosongkan untuk tetap menggunakan gambar lama.</small>
              </div>
            </div>

            <!-- Gambar Thumbnail -->
            <div class="mb-3">
              <label for="gambar_thumbnail" class="form-label">Gambar Thumbnail</label>
              <div class="border rounded p-3 text-center bg-light">
                <div id="thumbImagePreview" class="mb-3">
                  @if($artikel->gambar_thumbnail)
                    <img src="{{ Storage::url($artikel->gambar_thumbnail) }}" class="img-fluid rounded" style="max-height: 100px;" alt="Current Thumbnail">
                  @else
                    <i class="bx bx-image bx-2x text-muted"></i>
                  @endif
                </div>
                <input type="file" class="form-control @error('gambar_thumbnail') is-invalid @enderror" 
                       id="gambar_thumbnail" name="gambar_thumbnail" accept="image/*" onchange="previewThumbImage(event)">
                @error('gambar_thumbnail')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="text-muted">Format: JPG, PNG, GIF. Maks: 500KB. Ukuran ideal: 300x200px</small>
              </div>
            </div>
          </div>
        </div>

        <!-- Tips -->
        <div class="card">
          <div class="card-header">
            <h5 class="mb-0">
              <i class="bx bx-error me-2"></i>Perhatian
            </h5>
          </div>
          <div class="card-body">
            <ul class="mb-0 small">
              <li>Perubahan judul akan mempengaruhi slug jika slug kosong</li>
              <li>Merubah status ke Published akan otomatis set published_at jika kosong</li>
              <li>Perubahan URL (slug) dapat mempengaruhi SEO dan link yang sudah ada</li>
              <li>Review konten sebelum menyimpan perubahan</li>
            </ul>
          </div>
        </div>
      </div>
    </div>

    <!-- Form Actions -->
    <div class="card mt-4">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <a href="{{ route('admin.artikel.index') }}" class="btn btn-secondary">
            <i class="bx bx-arrow-back me-1"></i> Batal
          </a>
          <div>
            <button type="submit" name="save_draft" value="1" class="btn btn-outline-primary me-2">
              <i class="bx bx-save me-1"></i> Save Draft
            </button>
            <button type="submit" class="btn btn-primary">
              <i class="bx bx-save me-1"></i> Perbarui Artikel
            </button>
          </div>
        </div>
      </div>
    </div>
  </form>
</div>
@endsection

@push('scripts')
<!-- Summernote CSS -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
<script>
    // Auto-generate slug from title
    document.getElementById('judul').addEventListener('input', function() {
        const judul = this.value;
        const slugField = document.getElementById('slug');
        
        if (!slugField.value || slugField.dataset.original === slugField.value) {
            slugField.value = judul.toLowerCase()
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-')
                .trim('-');
            slugField.dataset.original = slugField.value;
        }
    });

    // Auto-generate meta title from title
    document.getElementById('judul').addEventListener('input', function() {
        const judul = this.value;
        const metaTitleField = document.getElementById('meta_title');
        
        if (!metaTitleField.value || metaTitleField.dataset.original === metaTitleField.value) {
            metaTitleField.value = judul.substring(0, 60);
            metaTitleField.dataset.original = metaTitleField.value;
        }
    });

    // Initialize Summernote
    $(document).ready(function() {
        $('#konten').summernote({
            height: 300,
            minHeight: 200,
            maxHeight: 500,
            focus: false,
            placeholder: 'Tulis konten artikel di sini...',
            toolbar: [
                ['style', ['style', 'bold', 'italic', 'underline', 'strikethrough', 'clear']],
                ['font', ['strikethrough', 'superscript', 'subscript']],
                ['fontsize', ['fontsize']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph', 'height']],
                ['insert', ['picture', 'video', 'link', 'table', 'hr']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ],
            callbacks: {
                onImageUpload: function(files) {
                    for (let i = 0; i < files.length; i++) {
                        uploadImage(files[i]);
                    }
                },
                onMediaDelete: function(target) {
                    deleteImage(target[0].src);
                }
            }
        });
    });

    // Upload image function
    function uploadImage(file) {
        const formData = new FormData();
        formData.append('image', file);
        formData.append('_token', '{{ csrf_token() }}');

        $.ajax({
            url: '{{ route("admin.upload.image") }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    const imageUrl = response.url;
                    $('#konten').summernote('insertImage', imageUrl, function($image) {
                        $image.attr('src', imageUrl);
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: response.message || 'Gagal mengupload gambar',
                        confirmButtonColor: '#d33'
                    });
                }
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Terjadi kesalahan saat mengupload gambar',
                    confirmButtonColor: '#d33'
                });
            }
        });
    }

    // Delete image function
    function deleteImage(src) {
        const filename = src.split('/').pop();
        
        $.ajax({
            url: '{{ route("admin.delete.image") }}',
            type: 'POST',
            data: {
                filename: filename,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                console.log('Image deleted successfully');
            },
            error: function(xhr) {
                console.log('Error deleting image');
            }
        });
    }

    // Preview images
    function previewMainImage(event) {
        const file = event.target.files[0];
        const preview = document.getElementById('mainImagePreview');
        
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.innerHTML = '<img src="' + e.target.result + '" class="img-fluid rounded" style="max-height: 200px;" alt="Main Image Preview">';
            }
            reader.readAsDataURL(file);
        }
    }

    function previewThumbImage(event) {
        const file = event.target.files[0];
        const preview = document.getElementById('thumbImagePreview');
        
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.innerHTML = '<img src="' + e.target.result + '" class="img-fluid rounded" style="max-height: 100px;" alt="Thumbnail Preview">';
            }
            reader.readAsDataURL(file);
        }
    }

    // Mark original values
    document.getElementById('slug').addEventListener('input', function() {
        this.dataset.original = this.value;
    });

    document.getElementById('meta_title').addEventListener('input', function() {
        this.dataset.original = this.value;
    });

    // Set original values on load
    document.addEventListener('DOMContentLoaded', function() {
        const slugField = document.getElementById('slug');
        const metaTitleField = document.getElementById('meta_title');
        slugField.dataset.original = slugField.value;
        metaTitleField.dataset.original = metaTitleField.value;
    });

    // Handle save draft button
    document.querySelector('button[name="save_draft"]').addEventListener('click', function(e) {
        e.preventDefault();
        document.getElementById('status_draft').checked = true;
        document.querySelector('form').submit();
    });
</script>
<!-- Summernote JS -->
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
@endpush
