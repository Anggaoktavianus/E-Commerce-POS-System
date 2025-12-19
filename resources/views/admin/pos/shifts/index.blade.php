@extends('admin.layouts.app')

@section('title', 'Kelola Shift POS')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Header Section -->
  <div class="card page-header-card mb-4">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h4 class="mb-1">
            <i class="bx bx-time me-2 text-primary"></i>Kelola Shift POS
          </h4>
          <p class="text-muted mb-0">Buka dan tutup shift kasir</p>
        </div>
        <div>
          <select id="outletSelect" class="form-select" onchange="changeOutlet(this.value)">
            <option value="">Pilih Outlet</option>
            @foreach($outlets as $outlet)
              <option value="{{ $outlet->id }}" {{ $outletId == $outlet->id ? 'selected' : '' }}>
                {{ $outlet->name }}
              </option>
            @endforeach
          </select>
        </div>
      </div>
    </div>
  </div>

  @if($outletId)
    <!-- Open Shift Button -->
    @php
      $openShift = \App\Models\PosShift::where('outlet_id', $outletId)
        ->where('status', 'open')
        ->where('shift_date', today())
        ->first();
    @endphp

    @if(!$openShift)
      <div class="card mb-4">
        <div class="card-body text-center">
          <i class="bx bx-time fs-1 text-warning mb-3"></i>
          <h5>Tidak ada shift yang terbuka</h5>
          <p class="text-muted">Buka shift baru untuk mulai transaksi</p>
          <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#openShiftModal">
            <i class="bx bx-plus me-1"></i>Buka Shift Baru
          </button>
        </div>
      </div>
    @else
      <div class="alert alert-success">
        <i class="bx bx-check-circle me-2"></i>
        <strong>Shift Aktif:</strong> 
        Kasir: {{ $openShift->user->name }} | 
        Opening: Rp {{ number_format($openShift->opening_balance, 0, ',', '.') }} | 
        Sales: Rp {{ number_format($openShift->total_sales, 0, ',', '.') }}
        <button class="btn btn-sm btn-danger float-end" onclick="closeShiftModal({{ $openShift->id }})">
          <i class="bx bx-x me-1"></i>Tutup Shift
        </button>
      </div>
    @endif

    <!-- Shifts List -->
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0"><i class="bx bx-list-ul me-2"></i>Riwayat Shift</h5>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover" id="shiftsTable">
            <thead>
              <tr>
                <th>Tanggal</th>
                <th>Shift</th>
                <th>Kasir</th>
                <th>Opening</th>
                <th>Sales</th>
                <th>Transaksi</th>
                <th>Status</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              @foreach($shifts as $shift)
                <tr>
                  <td>{{ $shift->shift_date->format('d/m/Y') }}</td>
                  <td>
                    @if($shift->shift_number == 1) Pagi
                    @elseif($shift->shift_number == 2) Siang
                    @else Malam
                    @endif
                  </td>
                  <td>{{ $shift->user->name }}</td>
                  <td>Rp {{ number_format($shift->opening_balance, 0, ',', '.') }}</td>
                  <td>Rp {{ number_format($shift->total_sales, 0, ',', '.') }}</td>
                  <td>{{ $shift->total_transactions }}</td>
                  <td>
                    @if($shift->status == 'open')
                      <span class="badge bg-success">Terbuka</span>
                    @elseif($shift->status == 'closed')
                      <span class="badge bg-secondary">Tertutup</span>
                    @else
                      <span class="badge bg-warning">Pending</span>
                    @endif
                  </td>
                  <td>
                    <div class="btn-group">
                      <a href="{{ route('admin.pos.shifts.report', $shift->id) }}" class="btn btn-sm btn-info" title="Report">
                        <i class="bx bx-show"></i>
                      </a>
                      <a href="{{ route('admin.pos.cash-movements.index', $shift->id) }}" class="btn btn-sm btn-warning" title="Cash Movement">
                        <i class="bx bx-money"></i>
                      </a>
                    </div>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        <div class="mt-3">
          {{ $shifts->links() }}
        </div>
      </div>
    </div>
  @else
    <div class="alert alert-info">
      <i class="bx bx-info-circle me-2"></i>Silakan pilih outlet terlebih dahulu
    </div>
  @endif
</div>

<!-- Open Shift Modal -->
<div class="modal fade" id="openShiftModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Buka Shift Baru</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="openShiftForm">
        <div class="modal-body">
          <input type="hidden" name="outlet_id" value="{{ $outletId }}">
          <div class="mb-3">
            <label class="form-label">Shift <span class="text-danger">*</span></label>
            <select name="shift_number" class="form-select" required>
              <option value="1">Pagi (1)</option>
              <option value="2">Siang (2)</option>
              <option value="3">Malam (3)</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Opening Balance <span class="text-danger">*</span></label>
            <input type="number" name="opening_balance" class="form-control" step="0.01" min="0" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Catatan</label>
            <textarea name="notes" class="form-control" rows="3"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Buka Shift</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Close Shift Modal -->
<div class="modal fade" id="closeShiftModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Tutup Shift</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="closeShiftForm">
        <div class="modal-body">
          <input type="hidden" name="shift_id" id="closeShiftId">
          <div class="mb-3">
            <label class="form-label">Actual Cash Count <span class="text-danger">*</span></label>
            <input type="number" name="actual_cash" class="form-control" step="0.01" min="0" required>
            <small class="text-muted">Masukkan jumlah uang tunai yang sebenarnya di kasir</small>
          </div>
          <div class="mb-3">
            <label class="form-label">Catatan</label>
            <textarea name="notes" class="form-control" rows="3"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-danger">Tutup Shift</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
function changeOutlet(outletId) {
  if (outletId) {
    window.location.href = '{{ route("admin.pos.shifts.index") }}?outlet_id=' + outletId;
  }
}

// Open Shift Form
document.getElementById('openShiftForm').addEventListener('submit', function(e) {
  e.preventDefault();
  const formData = new FormData(this);
  
  fetch('{{ route("admin.pos.shifts.open") }}', {
    method: 'POST',
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      'Accept': 'application/json'
    },
    body: formData
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      location.reload();
    } else {
      alert(data.message || 'Gagal membuka shift');
    }
  })
  .catch(error => {
    console.error('Error:', error);
    alert('Terjadi kesalahan');
  });
});

// Close Shift
function closeShiftModal(shiftId) {
  document.getElementById('closeShiftId').value = shiftId;
  new bootstrap.Modal(document.getElementById('closeShiftModal')).show();
}

document.getElementById('closeShiftForm').addEventListener('submit', function(e) {
  e.preventDefault();
  const formData = new FormData(this);
  const shiftId = formData.get('shift_id');
  
  fetch(`{{ url('admin/pos/shifts') }}/${shiftId}/close`, {
    method: 'POST',
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      'Accept': 'application/json'
    },
    body: formData
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      location.reload();
    } else {
      alert(data.message || 'Gagal menutup shift');
    }
  })
  .catch(error => {
    console.error('Error:', error);
    alert('Terjadi kesalahan');
  });
});
</script>
@endsection
