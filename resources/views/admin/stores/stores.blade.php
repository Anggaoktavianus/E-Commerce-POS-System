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
                    <a href="{{ route('admin.stores.stores.create') }}" class="btn btn-primary">
                        <i class="bx bx-plus me-1"></i>Tambah Toko
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped" id="storesTable">
                            <thead>
                                <tr>
                                    <th>Logo</th>
                                    <th>Informasi Toko</th>
                                    <th>Pemilik</th>
                                    <th>Alamat</th>
                                    <th>Outlet</th>
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
    $('#storesTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("admin.stores.stores.data") }}',
        columns: [
            { data: 'logo', name: 'logo', orderable: false, searchable: false },
            { data: 'store_info', name: 'name' },
            { data: 'owner_info', name: 'owner_name' },
            { data: 'address', name: 'city', orderable: false, searchable: false },
            { data: 'outlets_info', name: 'outlets_count', orderable: true, searchable: false },
            { data: 'status', name: 'is_active', orderable: false, searchable: false },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
        }
    });
});

function deleteStore(id) {
    if (confirm('Apakah Anda yakin ingin menghapus toko ini?')) {
        $.ajax({
            url: `/admin/stores/stores/${id}`,
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    $('#storesTable').DataTable().ajax.reload();
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
                    text: 'Terjadi kesalahan saat menghapus toko'
                });
            }
        });
    }
}
</script>
@endpush
