@extends('admin.layouts.app')

@section('title', $feature ? 'Edit Feature' : 'Create Feature')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">{{ $feature ? 'Edit' : 'Create' }} Feature</h4>
    <a href="{{ route('admin.features.index') }}" class="btn btn-secondary">Back</a>
  </div>

  <div class="card">
    <div class="card-body">
      <form action="{{ $feature ? route('admin.features.update', $feature->id) : route('admin.features.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @if($feature)
          @method('PUT')
        @endif

        <div class="mb-3">
          <label class="form-label">Title</label>
          <input type="text" name="title" class="form-control" value="{{ old('title', $feature->title ?? '') }}" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Description</label>
          <input type="text" name="description" class="form-control" value="{{ old('description', $feature->description ?? '') }}">
        </div>

        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Icon class (optional)</label>
            <input type="text" name="icon_class" class="form-control" placeholder="e.g. fas fa-truck" value="{{ old('icon_class', $feature->icon_class ?? '') }}">
          </div>
          <div class="col-md-6">
            <label class="form-label">Image (optional)</label>
            <input type="file" name="image" class="form-control">
            @if(!empty($feature?->image_path))
              <small class="text-muted d-block mt-1">Current: <img src="{{ asset($feature->image_path) }}" alt="" style="height:40px"> </small>
            @endif
          </div>
        </div>

        <div class="row g-3 mt-1">
          <div class="col-md-4">
            <label class="form-label">Sort Order</label>
            <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $feature->sort_order ?? 0) }}">
          </div>
          <div class="col-md-4 d-flex align-items-end">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" {{ old('is_active', $feature->is_active ?? true) ? 'checked' : '' }}>
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
