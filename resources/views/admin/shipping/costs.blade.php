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
                    <a href="{{ route('admin.shipping.costs.create') }}" class="btn btn-success">
                        <i class="bx bx-plus me-1"></i>Tambah Biaya
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped" id="costsTable">
                            <thead>
                                <tr>
                                    <th>Metode</th>
                                    <th>Rute</th>
                                    <th>Berat (kg)</th>
                                    <th>Biaya</th>
                                    <th>Estimasi</th>
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
    $('#costsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("admin.shipping.costs.data") }}',
        columns: [
            { data: 'method_logo', name: 'method_logo', orderable: false, searchable: false },
            { data: 'route', name: 'route' },
            { data: 'weight_range', name: 'weight_range', orderable: false, searchable: false },
            { data: 'cost', name: 'cost', orderable: true, searchable: false },
            { data: 'estimated_days', name: 'estimated_days' },
            { data: 'status', name: 'is_active', orderable: false, searchable: false },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
        }
    });
});

function deleteCost(id) {
    if (confirm('Apakah Anda yakin ingin menghapus biaya pengiriman ini?')) {
        $.ajax({
            url: `/admin/shipping/costs/${id}`,
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    $('#costsTable').DataTable().ajax.reload();
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
                let message = 'Terjadi kesalahan saat menghapus biaya pengiriman';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: message
                });
            }
        });
    }
}
</script>
@endpush
