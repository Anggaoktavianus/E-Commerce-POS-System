@extends('admin.layouts.app')

@section('title', $menu ? 'Edit Menu' : 'Create Menu')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">{{ $menu ? 'Edit' : 'Create' }} Menu</h4>
    <a href="{{ route('admin.menus.index') }}" class="btn btn-secondary">Back</a>
  </div>

  <div class="card">
    <div class="card-body">
      <form action="{{ $menu ? route('admin.menus.update', $menu->id) : route('admin.menus.store') }}" method="POST">
        @csrf
        @if($menu)
          @method('PUT')
        @endif

        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Name</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $menu->name ?? '') }}" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Location</label>
            @php $loc = old('location', $menu->location ?? 'header'); @endphp
            <select name="location" class="form-select" required>
              <option value="header" {{ $loc==='header' ? 'selected' : '' }}>Header</option>
              <option value="footer_column_1" {{ $loc==='footer_column_1' ? 'selected' : '' }}>Footer Column 1</option>
              <option value="footer_column_2" {{ $loc==='footer_column_2' ? 'selected' : '' }}>Footer Column 2</option>
              <option value="footer_column_3" {{ $loc==='footer_column_3' ? 'selected' : '' }}>Footer Column 3</option>
              <option value="footer_column_4" {{ $loc==='footer_column_4' ? 'selected' : '' }}>Footer Column 4</option>
            </select>
          </div>
        </div>

        <div class="row g-3 mt-1">
          <div class="col-md-4 d-flex align-items-end">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" {{ old('is_active', $menu->is_active ?? true) ? 'checked' : '' }}>
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
