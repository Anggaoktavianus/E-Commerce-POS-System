@extends('admin.layouts.app')

@section('title', $category ? 'Edit Category' : 'Create Category')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Header Section -->
  <div class="card page-header-card mb-4">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center flex-wrap">
        <div>
          <h4 class="mb-1">
            <i class="bx bx-{{ $category ? 'edit' : 'plus' }} me-2 text-primary"></i>{{ $category ? 'Edit' : 'Tambah' }} Kategori
          </h4>
          <p class="text-muted mb-0">{{ $category ? 'Ubah informasi kategori' : 'Buat kategori baru untuk produk' }}</p>
        </div>
        <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary btn-modern mt-2 mt-md-0">
          <i class="bx bx-arrow-back me-1"></i>Kembali
        </a>
      </div>
    </div>
  </div>

  <div class="card form-card">
    <div class="card-header">
      <h5 class="card-title mb-0 fw-bold text-white">
        <i class="bx bx-info-circle me-2"></i>Informasi Kategori
      </h5>
    </div>
    <div class="card-body">
      <form action="{{ $category ? route('admin.categories.update', $category->id) : route('admin.categories.store') }}" method="POST">
        @csrf
        @if($category)
          @method('PUT')
        @endif

        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Name</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $category->name ?? '') }}" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Slug</label>
            <input type="text" name="slug" class="form-control" value="{{ old('slug', $category->slug ?? '') }}" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Parent</label>
            <select name="parent_id" class="form-select">
              <option value="">— None —</option>
              @foreach($parents as $p)
                <option value="{{ $p->id }}" {{ (string)old('parent_id', $category->parent_id ?? '') === (string)$p->id ? 'selected' : '' }}>{{ $p->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-6 d-flex align-items-end">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" {{ old('is_active', $category->is_active ?? true) ? 'checked' : '' }}>
              <label class="form-check-label" for="is_active"> Active </label>
            </div>
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
