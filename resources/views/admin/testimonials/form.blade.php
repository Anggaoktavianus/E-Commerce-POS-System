@extends('admin.layouts.app')

@section('title', $testimonial ? 'Edit Testimonial' : 'Create Testimonial')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">{{ $testimonial ? 'Edit' : 'Create' }} Testimonial</h4>
    <a href="{{ route('admin.testimonials.index') }}" class="btn btn-secondary">Back</a>
  </div>

  <div class="card">
    <div class="card-body">
      <form action="{{ $testimonial ? route('admin.testimonials.update', $testimonial->id) : route('admin.testimonials.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @if($testimonial)
          @method('PUT')
        @endif

        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Author Name</label>
            <input type="text" name="author_name" class="form-control" value="{{ old('author_name', $testimonial->author_name ?? '') }}" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Author Title</label>
            <input type="text" name="author_title" class="form-control" value="{{ old('author_title', $testimonial->author_title ?? '') }}">
          </div>
          <div class="col-12">
            <label class="form-label">Content</label>
            <textarea name="content" rows="4" class="form-control" required>{{ old('content', $testimonial->content ?? '') }}</textarea>
          </div>
          <div class="col-md-3">
            <label class="form-label">Rating</label>
            <input type="number" name="rating" class="form-control" min="1" max="5" value="{{ old('rating', $testimonial->rating ?? 5) }}">
          </div>
          <div class="col-md-3">
            <label class="form-label">Sort Order</label>
            <input type="number" name="sort_order" class="form-control" min="0" value="{{ old('sort_order', $testimonial->sort_order ?? 0) }}">
          </div>
          <div class="col-md-6">
            <label class="form-label">Avatar</label>
            <input type="file" name="avatar" class="form-control" accept="image/*">
            @if(!empty($testimonial?->avatar_path))
              <img src="{{ asset($testimonial->avatar_path) }}" class="img-thumbnail mt-2" style="height:80px">
            @endif
          </div>
        </div>

        <div class="row g-3 mt-1">
          <div class="col-md-4 d-flex align-items-end">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" {{ old('is_active', $testimonial->is_active ?? true) ? 'checked' : '' }}>
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
