@extends('admin.layouts.app')

@section('title', 'Detail Artikel')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Header Section -->
  <div class="card mb-4">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h4 class="mb-1">
            <i class="bx bx-file me-2"></i>Detail Artikel
          </h4>
          <p class="text-muted mb-0">Informasi lengkap artikel</p>
        </div>
        <div>
          <a href="{{ route('admin.artikel.edit', $artikel) }}" class="btn btn-warning me-2">
            <i class="bx bx-edit me-1"></i> Edit
          </a>
          <a href="{{ route('admin.artikel.index') }}" class="btn btn-secondary">
            <i class="bx bx-arrow-back me-1"></i> Kembali
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- Article Content -->
  <div class="row">
    <div class="col-md-8">
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="mb-0">
            <i class="bx bx-text me-2"></i>Konten Artikel
          </h5>
        </div>
        <div class="card-body">
          <!-- Title and Status -->
          <div class="d-flex justify-content-between align-items-start mb-3">
            <h2 class="h3 mb-0">{{ $artikel->judul }}</h2>
            <span class="badge {{ $artikel->status === 'published' ? 'bg-success' : ($artikel->status === 'draft' ? 'bg-secondary' : 'bg-warning') }}">
              {{ ucfirst($artikel->status) }}
            </span>
          </div>

          <!-- Meta Info -->
          <div class="row mb-3 text-muted small">
            <div class="col-md-6">
              <i class="bx bx-user me-1"></i> Author: {{ $artikel->user ? $artikel->user->name : 'Anonymous' }}
            </div>
            <div class="col-md-6">
              <i class="bx bx-category me-1"></i> Kategori: {{ $artikel->kategoriArtikel->nama }}
            </div>
            <div class="col-md-6">
              <i class="bx bx-calendar me-1"></i> Dibuat: {{ $artikel->created_at->format('d M Y H:i') }}
            </div>
            <div class="col-md-6">
              <i class="bx bx-refresh me-1"></i> Diperbarui: {{ $artikel->updated_at->format('d M Y H:i') }}
            </div>
            @if($artikel->published_at)
            <div class="col-md-6">
              <i class="bx bx-time me-1"></i> Dipublish: {{ $artikel->published_at->format('d M Y H:i') }}
            </div>
            @endif
            <div class="col-md-6">
              <i class="bx bx-show me-1"></i> Views: {{ number_format($artikel->views) }}
            </div>
          </div>

          <!-- Featured Image -->
          @if($artikel->gambar_utama)
          <div class="mb-4">
            <img src="{{ Storage::url($artikel->gambar_utama) }}" 
                 class="img-fluid rounded" 
                 alt="{{ $artikel->judul }}"
                 style="max-height: 400px; width: 100%; object-fit: cover;">
          </div>
          @endif

          <!-- Article Content -->
          <div class="article-content">
            {!! $artikel->konten !!}
          </div>

          <!-- Reading Info -->
          <div class="alert alert-info mt-4">
            <div class="d-flex justify-content-between">
              <div>
                <i class="bx bx-time-five me-2"></i>
                <strong>Reading Time:</strong> {{ $artikel->reading_time }} menit
              </div>
              <div>
                <i class="bx bx-text me-2"></i>
                <strong>Word Count:</strong> {{ str_word_count(strip_tags($artikel->konten)) }} kata
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- SEO Information -->
      <div class="card">
        <div class="card-header">
          <h5 class="mb-0">
            <i class="bx bx-search-alt me-2"></i>SEO Information
          </h5>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-12 mb-3">
              <strong>URL Slug:</strong>
              <code>{{ $artikel->slug }}</code>
            </div>
            @if($artikel->meta_title)
            <div class="col-md-12 mb-3">
              <strong>Meta Title:</strong><br>
              <span class="text-muted">{{ $artikel->meta_title }}</span>
            </div>
            @endif
            @if($artikel->meta_description)
            <div class="col-md-12 mb-3">
              <strong>Meta Description:</strong><br>
              <span class="text-muted">{{ $artikel->meta_description }}</span>
            </div>
            @endif
            @if($artikel->meta_keywords)
            <div class="col-md-12">
              <strong>Meta Keywords:</strong><br>
              <span class="text-muted">{{ $artikel->meta_keywords }}</span>
            </div>
            @endif
          </div>
        </div>
      </div>
    </div>

    <!-- Sidebar -->
    <div class="col-md-4">
      <!-- Quick Actions -->
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="mb-0">
            <i class="bx bx-bolt me-2"></i>Quick Actions
          </h5>
        </div>
        <div class="card-body">
          <div class="d-grid gap-2">
            <a href="{{ route('admin.artikel.edit', $artikel) }}" class="btn btn-warning">
              <i class="bx bx-edit me-2"></i> Edit Artikel
            </a>
            
            @if($artikel->status === 'draft')
            <form action="{{ route('admin.artikel.update', $artikel) }}" method="POST" class="d-inline">
              @csrf
              @method('PUT')
              <input type="hidden" name="status" value="published">
              <button type="submit" class="btn btn-success w-100">
                <i class="bx bx-publish me-2"></i> Publish Sekarang
              </button>
            </form>
            @endif

            @if($artikel->status === 'published')
            <form action="{{ route('admin.artikel.update', $artikel) }}" method="POST" class="d-inline">
              @csrf
              @method('PUT')
              <input type="hidden" name="status" value="draft">
              <button type="submit" class="btn btn-secondary w-100">
                <i class="bx bx-edit me-2"></i> Unpublish
              </button>
            </form>
            @endif

            <button type="button" class="btn btn-outline-danger w-100" onclick="deleteArticle({{ $artikel->id }})">
              <i class="bx bx-trash me-2"></i> Hapus Artikel
            </button>
          </div>
        </div>
      </div>

      <!-- Statistics -->
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="mb-0">
            <i class="bx bx-bar-chart me-2"></i>Statistik
          </h5>
        </div>
        <div class="card-body">
          <div class="row text-center">
            <div class="col-6 mb-3">
              <h4 class="text-primary">{{ number_format($artikel->views) }}</h4>
              <small class="text-muted">Total Views</small>
            </div>
            <div class="col-6 mb-3">
              <h4 class="text-info">{{ $artikel->reading_time }}</h4>
              <small class="text-muted">Menit Baca</small>
            </div>
            <div class="col-6">
              <h4 class="text-success">{{ str_word_count(strip_tags($artikel->konten)) }}</h4>
              <small class="text-muted">Jumlah Kata</small>
            </div>
            <div class="col-6">
              <h4 class="text-warning">{{ strlen(strip_tags($artikel->konten)) }}</h4>
              <small class="text-muted">Karakter</small>
            </div>
          </div>
        </div>
      </div>

      <!-- Category Info -->
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="mb-0">
            <i class="bx bx-category me-2"></i>Informasi Kategori
          </h5>
        </div>
        <div class="card-body">
          <div class="d-flex align-items-center mb-3">
            @if($artikel->kategoriArtikel->gambar)
              <img src="{{ Storage::url($artikel->kategoriArtikel->gambar) }}" 
                   class="rounded me-3" 
                   alt="{{ $artikel->kategoriArtikel->nama }}"
                   style="width: 50px; height: 50px; object-fit: cover;">
            @else
              <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" 
                   style="width: 50px; height: 50px;">
                <i class="bx bx-image text-muted"></i>
              </div>
            @endif
            <div>
              <h6 class="mb-0">{{ $artikel->kategoriArtikel->nama }}</h6>
              <small class="text-muted">{{ $artikel->kategoriArtikel->artikel_count }} artikel</small>
            </div>
          </div>
          @if($artikel->kategoriArtikel->deskripsi)
          <p class="text-muted small mb-0">{{ $artikel->kategoriArtikel->deskripsi }}</p>
          @endif
        </div>
      </div>

      <!-- Article URL -->
      <div class="card">
        <div class="card-header">
          <h5 class="mb-0">
            <i class="bx bx-link me-2"></i>Article URL
          </h5>
        </div>
        <div class="card-body">
          <div class="input-group">
            <input type="text" class="form-control" value="{{ url('/artikel/' . $artikel->slug) }}" readonly>
            <button class="btn btn-outline-secondary" type="button" onclick="copyUrl()">
              <i class="bx bx-copy"></i> Copy
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  // Copy URL to clipboard
  function copyUrl() {
    const urlInput = document.querySelector('input[readonly]');
    urlInput.select();
    document.execCommand('copy');
    
    // Show success message
    const button = event.target.closest('button');
    const originalHTML = button.innerHTML;
    button.innerHTML = '<i class="bx bx-check"></i> Copied!';
    button.classList.add('btn-success');
    button.classList.remove('btn-outline-secondary');
    
    setTimeout(() => {
      button.innerHTML = originalHTML;
      button.classList.remove('btn-success');
      button.classList.add('btn-outline-secondary');
    }, 2000);
  }

  // Delete article
  function deleteArticle(id) {
    Swal.fire({
      title: 'Hapus Artikel?',
      text: "Artikel ini akan dihapus secara permanen!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Ya, hapus!',
      cancelButtonText: 'Batal'
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: '{{ route("admin.artikel.destroy", ":id") }}'.replace(':id', id),
          type: 'DELETE',
          data: {
            _token: '{{ csrf_token() }}'
          },
          success: function(response) {
            if (response.success) {
              Swal.fire(
                'Terhapus!',
                response.message,
                'success'
              ).then(() => {
                window.location.href = '{{ route("admin.artikel.index") }}';
              });
            } else {
              Swal.fire(
                'Error!',
                response.message,
                'error'
              );
            }
          },
          error: function(xhr) {
            Swal.fire(
              'Error!',
              'Terjadi kesalahan saat menghapus artikel.',
              'error'
            );
          }
        });
      }
    });
  }
</script>
@endpush
