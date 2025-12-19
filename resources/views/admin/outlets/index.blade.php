@extends('admin.layouts.app')

@section('title', $title)

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header Section -->
    <div class="card page-header-card mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h4 class="mb-1">
                        <i class="bx bx-store me-2 text-primary"></i>{{ $title }}
                    </h4>
                    <p class="text-muted mb-0">{{ $subtitle }}</p>
                </div>
                <div class="d-flex gap-2 mt-2 mt-md-0">
                    <select id="storeFilter" class="form-select" style="width: 200px;">
                        <option value="">Semua Toko</option>
                        @foreach($stores as $store)
                            <option value="{{ $store->id }}">{{ $store->name }}</option>
                        @endforeach
                    </select>
                    <a href="{{ route('admin.outlets.create') }}" class="btn btn-primary btn-modern">
                        <i class="bx bx-plus me-1"></i>Tambah Outlet
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-modern">
        <div class="card-header">
            <h5 class="card-title mb-0 fw-bold">
                <i class="bx bx-list-ul me-2"></i>Daftar Outlet
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-modern" id="outletsTable">
                    <thead>
                        <tr>
                            <th class="text-white">Toko</th>
                            <th class="text-white">Nama Outlet</th>
                            <th class="text-white">Tipe</th>
                            <th class="text-white">Manajer</th>
                            <th class="text-white">Lokasi</th>
                            <th class="text-white">Maps</th>
                            <th class="text-white">Status</th>
                            <th class="text-white">Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
$(document).ready(function() {
    var table = $('#outletsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("admin.outlets.data") }}',
        columns: [
            { data: 'store_name', name: 'store.name' },
            { data: 'name', name: 'name' },
            { data: 'type_badge', name: 'type', orderable: false, searchable: false },
            { data: 'manager_info', name: 'manager_name', orderable: false, searchable: false },
            { data: 'location', name: 'city', orderable: false, searchable: false },
            { data: 'coordinates', name: 'latitude', orderable: false, searchable: false },
            { data: 'status', name: 'is_active', orderable: false, searchable: false },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
        }
    });
    
    // Filter by store
    $('#storeFilter').on('change', function() {
        var storeId = $(this).val();
        table.column(0).search(storeId).draw();
    });
});

function deleteOutlet(id) {
    if (confirm('Apakah Anda yakin ingin menghapus outlet ini?')) {
        $.ajax({
            url: `/admin/outlets/${id}`,
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    $('#outletsTable').DataTable().ajax.reload();
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message
                    });
                }
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan saat menghapus outlet'
                });
            }
        });
    }
}
</script>
@endpush
