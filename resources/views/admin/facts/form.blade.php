@extends('admin.layouts.app')

@section('title', $fact ? 'Edit Fact' : 'Create Fact')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">{{ $fact ? 'Edit' : 'Create' }} Fact</h4>
    <a href="{{ route('admin.facts.index') }}" class="btn btn-secondary">Back</a>
  </div>

  <div class="card">
    <div class="card-body">
      <form action="{{ $fact ? route('admin.facts.update', $fact->id) : route('admin.facts.store') }}" method="POST">
        @csrf
        @if($fact)
          @method('PUT')
        @endif

        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Label</label>
            <input type="text" name="label" class="form-control" value="{{ old('label', $fact->label ?? '') }}" required>
          </div>
          <div class="col-md-3">
            <label class="form-label">Value</label>
            <input type="number" name="value" class="form-control" min="0" value="{{ old('value', $fact->value ?? 0) }}" required>
          </div>
          <div class="col-md-3">
            <label class="form-label">Sort Order</label>
            <input type="number" name="sort_order" class="form-control" min="0" value="{{ old('sort_order', $fact->sort_order ?? 0) }}">
          </div>
          <div class="col-md-6">
            <label class="form-label">Icon Class</label>
            <input type="text" name="icon_class" class="form-control" value="{{ old('icon_class', $fact->icon_class ?? '') }}" placeholder="e.g. fas fa-check">
          </div>
        </div>

        <div class="row g-3 mt-1">
          <div class="col-md-4 d-flex align-items-end">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" {{ old('is_active', $fact->is_active ?? true) ? 'checked' : '' }}>
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
