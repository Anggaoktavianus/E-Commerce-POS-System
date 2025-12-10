@extends('admin.layouts.app')

@section('title', $slide ? 'Edit Slide' : 'Create Slide')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">{{ $slide ? 'Edit' : 'Create' }} Slide — {{ $parent->name }}</h4>
    <a href="{{ route('admin.slides.index', $parent->id) }}" class="btn btn-secondary">Back</a>
  </div>

  <div class="card">
    <div class="card-body">
      <form action="{{ $slide ? route('admin.slides.update', $slide->id) : route('admin.slides.store', $parent->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @if($slide)
          @method('PUT')
        @endif

        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Title</label>
            <input type="text" name="title" class="form-control" value="{{ old('title', $slide->title ?? '') }}">
          </div>
          <div class="col-md-6">
            <label class="form-label">Subtitle</label>
            <input type="text" name="subtitle" class="form-control" value="{{ old('subtitle', $slide->subtitle ?? '') }}">
          </div>
        </div>

        <div class="row g-3 mt-1">
          <div class="col-md-4">
            <label class="form-label">Button Text</label>
            <input type="text" name="button_text" class="form-control" value="{{ old('button_text', $slide->button_text ?? '') }}">
          </div>
          <div class="col-md-8">
            <label class="form-label">Button URL</label>
            <input type="url" name="button_url" class="form-control" value="{{ old('button_url', $slide->button_url ?? '') }}">
          </div>
        </div>

        <div class="row g-3 mt-1">
          <div class="col-md-6">
            <label class="form-label">Image</label>
            <input type="file" name="image" class="form-control">
            @if(!empty($slide?->image_path))
              <small class="text-muted d-block mt-1">Current: <img src="{{ asset($slide->image_path) }}" alt="" style="height:60px"> </small>
            @endif
          </div>
          <div class="col-md-3">
            <label class="form-label">Sort Order</label>
            <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $slide->sort_order ?? 0) }}">
          </div>
          <div class="col-md-3 d-flex align-items-end">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" {{ old('is_active', $slide->is_active ?? true) ? 'checked' : '' }}>
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
