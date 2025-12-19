@extends('admin.layouts.app')

@section('title', 'Edit Halaman: ' . $page->title)

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header Section -->
    <div class="card page-header-card mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h4 class="mb-1">
                        <i class="bx bx-edit me-2 text-primary"></i>Edit Halaman: {{ $page->title }}
                    </h4>
                    <p class="text-muted mb-0">Perbarui konten dan informasi halaman</p>
                </div>
                <div class="d-flex gap-2 mt-2 mt-md-0">
                    <a href="{{ route('pages.show', $page->slug) }}" target="_blank" class="btn btn-info btn-modern">
                        <i class="bx bx-show me-1"></i>Lihat
                    </a>
                    <a href="{{ route('admin.pages.index') }}" class="btn btn-secondary btn-modern">
                        <i class="bx bx-arrow-back me-1"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card form-card">
                <div class="card-header">
                    <h5 class="card-title mb-0 fw-bold">
                        <i class="bx bx-file me-2"></i>Detail Halaman
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.pages.update', $page->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        @include('admin.pages.partials.form')
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
@endpush

@push('scripts')
@endpush
