@extends('admin.layouts.app')

@section('title', 'Receipt Templates')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Header Section -->
  <div class="card page-header-card mb-4">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h4 class="mb-1">
            <i class="bx bx-receipt me-2 text-primary"></i>Receipt Templates
          </h4>
          <p class="text-muted mb-0">Kelola template struk untuk POS</p>
        </div>
        <div>
          <select id="outletSelect" class="form-select me-2" onchange="changeOutlet(this.value)" style="display: inline-block; width: auto;">
            <option value="">Global Templates</option>
            @foreach($outlets as $outlet)
              <option value="{{ $outlet->id }}" {{ $outletId == $outlet->id ? 'selected' : '' }}>
                {{ $outlet->name }}
              </option>
            @endforeach
          </select>
          <a href="{{ route('admin.pos.receipt-templates.create', ['outlet_id' => $outletId]) }}" class="btn btn-primary">
            <i class="bx bx-plus me-1"></i>Tambah Template
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- Templates List -->
  <div class="card">
    <div class="card-header">
      <h5 class="mb-0">Daftar Template</h5>
    </div>
    <div class="card-body">
      @if($templates->count() > 0)
        <div class="table-responsive">
          <table class="table">
            <thead>
              <tr>
                <th>Nama</th>
                <th>Outlet</th>
                <th>Default</th>
                <th>Status</th>
                <th>Updated</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              @foreach($templates as $template)
                <tr>
                  <td>
                    <strong>{{ $template->name }}</strong>
                  </td>
                  <td>
                    {{ $template->outlet ? $template->outlet->name : 'Global' }}
                  </td>
                  <td>
                    @if($template->is_default)
                      <span class="badge bg-success">Default</span>
                    @else
                      <span class="badge bg-secondary">-</span>
                    @endif
                  </td>
                  <td>
                    @if($template->is_active)
                      <span class="badge bg-success">Active</span>
                    @else
                      <span class="badge bg-danger">Inactive</span>
                    @endif
                  </td>
                  <td>
                    {{ $template->updated_at->format('d/m/Y H:i') }}
                  </td>
                  <td>
                    <div class="btn-group">
                      <a href="{{ route('admin.pos.receipt-templates.preview', $template->id) }}" 
                         class="btn btn-sm btn-info" target="_blank" title="Preview">
                        <i class="bx bx-show"></i>
                      </a>
                      <a href="{{ route('admin.pos.receipt-templates.edit', $template->id) }}" 
                         class="btn btn-sm btn-primary" title="Edit">
                        <i class="bx bx-edit"></i>
                      </a>
                      <button class="btn btn-sm btn-danger" 
                              onclick="confirmDelete({{ $template->id }})" 
                              title="Delete">
                        <i class="bx bx-trash"></i>
                      </button>
                    </div>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @else
        <div class="text-center py-5">
          <i class="bx bx-receipt fs-1 text-muted mb-3"></i>
          <p class="text-muted">Belum ada template</p>
          <a href="{{ route('admin.pos.receipt-templates.create', ['outlet_id' => $outletId]) }}" class="btn btn-primary">
            <i class="bx bx-plus me-1"></i>Tambah Template Pertama
          </a>
        </div>
      @endif
    </div>
  </div>
</div>

<script>
function changeOutlet(outletId) {
  const url = new URL(window.location.href);
  if (outletId) {
    url.searchParams.set('outlet_id', outletId);
  } else {
    url.searchParams.delete('outlet_id');
  }
  window.location.href = url.toString();
}

function confirmDelete(id) {
  Swal.fire({
    title: 'Hapus Template?',
    text: 'Apakah Anda yakin ingin menghapus template ini?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#6c757d',
    confirmButtonText: 'Ya, Hapus',
    cancelButtonText: 'Batal'
  }).then((result) => {
    if (result.isConfirmed) {
      deleteTemplate(id);
    }
  });
}

function deleteTemplate(id) {
  fetch(`{{ route('admin.pos.receipt-templates.destroy', ':id') }}`.replace(':id', id), {
    method: 'DELETE',
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      'Accept': 'application/json'
    }
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      Swal.fire({
        icon: 'success',
        title: 'Berhasil',
        text: 'Template berhasil dihapus',
        timer: 2000
      }).then(() => {
        location.reload();
      });
    } else {
      Swal.fire({
        icon: 'error',
        title: 'Gagal',
        text: data.message || 'Gagal menghapus template'
      });
    }
  })
  .catch(error => {
    console.error('Error:', error);
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: 'Terjadi kesalahan'
    });
  });
}
</script>
@endsection
