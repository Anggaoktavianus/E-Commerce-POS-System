@extends('admin.layouts.app')

@section('title', $social_link ? 'Edit Social Link' : 'Create Social Link')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">{{ $social_link ? 'Edit' : 'Create' }} Social Link</h4>
    <a href="{{ route('admin.social_links.index') }}" class="btn btn-secondary">Back</a>
  </div>

  <div class="card">
    <div class="card-body">
      <form action="{{ $social_link ? route('admin.social_links.update', $social_link->id) : route('admin.social_links.store') }}" method="POST">
        @csrf
        @if($social_link)
          @method('PUT')
        @endif

        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Label</label>
            <input type="text" name="label" class="form-control" value="{{ old('label', $social_link->label ?? '') }}" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Icon Class</label>
            <input type="text" name="icon_class" class="form-control" value="{{ old('icon_class', $social_link->icon_class ?? '') }}" placeholder="e.g. fab fa-facebook">
          </div>
          <div class="col-md-8">
            <label class="form-label">URL</label>
            <input type="url" name="url" class="form-control" value="{{ old('url', $social_link->url ?? '') }}" required>
          </div>
          <div class="col-md-4">
            <label class="form-label">Sort Order</label>
            <input type="number" name="sort_order" class="form-control" min="0" value="{{ old('sort_order', $social_link->sort_order ?? 0) }}">
          </div>
        </div>

        <div class="row g-3 mt-1">
          <div class="col-md-4 d-flex align-items-end">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" {{ old('is_active', $social_link->is_active ?? true) ? 'checked' : '' }}>
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
