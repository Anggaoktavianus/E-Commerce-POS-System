@extends('admin.layouts.app')

@section('title', 'Edit Halaman: ' . $page->title)

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold py-3 mb-0">Edit Halaman: {{ $page->title }}</h4>
        <div>
            <a href="{{ route('pages.show', $page->slug) }}" target="_blank" class="btn btn-outline-primary me-2">
                <i class='bx bx-show me-1'></i> Lihat
            </a>
            <a href="{{ route('admin.pages.index') }}" class="btn btn-outline-secondary">
                <i class='bx bx-arrow-back me-1'></i> Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
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
