@extends('admin.layouts.app')

@section('title', $title)

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
@endpush

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">{{ $title }}</h5>
                        <small class="text-muted">{{ $subtitle }}</small>
                    </div>
                    <div>
                        <select id="storeFilter" class="form-select d-inline-block" style="width: 200px;">
                            <option value="">Semua Toko</option>
                            @foreach($stores as $store)
                                <option value="{{ $store->id }}">{{ $store->name }}</option>
                            @endforeach
                        </select>
                        <a href="{{ route('admin.outlets.create') }}" class="btn btn-success ms-2">
                            <i class="bx bx-plus me-1"></i>Tambah Outlet
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped" id="outletsTable">
                            <thead>
                                <tr>
                                    <th>Toko</th>
                                    <th>Nama Outlet</th>
                                    <th>Tipe</th>
                                    <th>Manajer</th>
                                    <th>Lokasi</th>
                                    <th>Maps</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
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
