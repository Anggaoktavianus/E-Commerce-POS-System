@extends('admin.layouts.app')

@section('title', $carousel ? 'Edit Carousel' : 'Create Carousel')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">{{ $carousel ? 'Edit' : 'Create' }} Carousel</h4>
    <a href="{{ route('admin.carousels.index') }}" class="btn btn-secondary">Back</a>
  </div>

  <div class="card">
    <div class="card-body">
      <form action="{{ $carousel ? route('admin.carousels.update', $carousel->id) : route('admin.carousels.store') }}" method="POST">
        @csrf
        @if($carousel)
          @method('PUT')
        @endif

        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Name</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $carousel->name ?? '') }}" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Key (optional)</label>
            <input type="text" name="key" class="form-control" placeholder="e.g. home_hero" value="{{ old('key', $carousel->key ?? '') }}">
          </div>
        </div>

        <div class="row g-3 mt-1">
          <div class="col-md-4 d-flex align-items-end">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" {{ old('is_active', $carousel->is_active ?? true) ? 'checked' : '' }}>
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
