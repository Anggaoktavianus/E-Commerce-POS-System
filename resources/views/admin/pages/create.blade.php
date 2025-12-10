@extends('admin.layouts.app')

@section('title', 'Tambah Halaman Baru')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Header -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h4 class="mb-1">
        <i class="bx bx-plus me-2"></i>Tambah Halaman Baru
      </h4>
      <p class="text-muted mb-0">Buat halaman statis baru untuk website Anda</p>
    </div>
    <a href="{{ route('admin.pages.index') }}" class="btn btn-secondary">
      <i class="bx bx-arrow-back me-1"></i> Kembali
    </a>
  </div>

  <!-- Alert Info -->
  <div class="alert alert-info alert-dismissible" role="alert">
    <i class="bx bx-info-circle me-2"></i>
    <strong>Info:</strong> Gunakan editor rich text untuk membuat konten yang menarik. 
    Upload gambar dan file lampiran untuk melengkapi halaman Anda.
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>

  <!-- Form -->
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h5 class="mb-0">
            <i class="bx bx-file me-2"></i>Detail Halaman
          </h5>
        </div>
        <div class="card-body">
          <form action="{{ route('admin.pages.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @include('admin.pages.partials.form')
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Help Guide -->
  <div class="card mt-4">
    <div class="card-header">
      <h5 class="mb-0">
        <i class="bx bx-help-circle me-2"></i>Panduan Pembuatan Halaman
      </h5>
    </div>
    <div class="card-body">
      <div class="row">
        <div class="col-md-6">
          <h6><i class="bx bx-edit text-primary me-2"></i>Konten Utama</h6>
          <ul class="mb-3">
            <li><strong>Judul:</strong> Judul halaman yang akan muncul di browser dan menu</li>
            <li><strong>Konten:</strong> Gunakan editor untuk format teks, gambar, link</li>
            <li><strong>Video:</strong> Embed video YouTube/Vimeo dengan URL</li>
            <li><strong>Gambar Unggulan:</strong> Gambar utama untuk halaman</li>
          </ul>
          
          <h6><i class="bx bx-cog text-success me-2"></i>Pengaturan SEO</h6>
          <ul class="mb-3">
            <li><strong>Meta Title:</strong> Judul untuk search engine (maks 60 karakter)</li>
            <li><strong>Meta Description:</strong> Deskripsi untuk search engine (maks 160 karakter)</li>
            <li><strong>Slug:</strong> URL-friendly version dari judul (auto-generate)</li>
          </ul>
        </div>
        <div class="col-md-6">
          <h6><i class="bx bx-image text-info me-2"></i>Media & Lampiran</h6>
          <ul class="mb-3">
            <li><strong>Gambar Unggulan:</strong> Format JPG, PNG, WebP (maks 2MB)</li>
            <li><strong>Lampiran:</strong> Upload PDF, DOC, ZIP (maks 5MB per file)</li>
            <li><strong>Video:</strong> Paste URL YouTube atau Vimeo</li>
            <li><strong>Galeri:</strong> Bisa tambahkan beberapa gambar di konten</li>
          </ul>
          
          <h6><i class="bx bx-check text-warning me-2"></i>Status Publikasi</h6>
          <ul class="mb-3">
            <li><strong>Published:</strong> Halaman langsung muncul di website</li>
            <li><strong>Draft:</strong> Simpan sebagai draf, belum muncul di website</li>
            <li><strong>Preview:</strong> Lihat halaman sebelum publish</li>
            <li><strong>Schedule:</strong> Jadwalkan publikasi (fitur future)</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('styles')
<style>
  .note-editor.note-frame .note-editing-area {
    min-height: 300px;
  }
</style>
@endpush

@push('scripts')
@endpush
