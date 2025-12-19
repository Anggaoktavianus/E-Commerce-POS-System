@extends('admin.layouts.app')

@section('title', 'Cash Movement - Shift #' . $shift->id)

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Header Section -->
  <div class="card page-header-card mb-4">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h4 class="mb-1">
            <i class="bx bx-money me-2 text-primary"></i>Cash Movement
          </h4>
          <p class="text-muted mb-0">
            Shift #{{ $shift->id }} | Outlet: {{ $shift->outlet->name }} | 
            Status: <span class="badge bg-{{ $shift->status === 'open' ? 'success' : 'secondary' }}">{{ ucfirst($shift->status) }}</span>
          </p>
        </div>
        <div>
          <a href="{{ route('admin.pos.shifts.index') }}" class="btn btn-secondary">
            <i class="bx bx-arrow-back me-1"></i>Kembali
          </a>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-4">
    <!-- Cash Summary -->
    <div class="col-md-4">
      <div class="card">
        <div class="card-header">
          <h5 class="mb-0">Cash Summary</h5>
        </div>
        <div class="card-body">
          <div class="mb-3">
            <label class="text-muted small">Opening Balance</label>
            <h4 class="mb-0">Rp {{ number_format($shift->opening_balance, 0, ',', '.') }}</h4>
          </div>
          <div class="mb-3">
            <label class="text-muted small">Cash Sales</label>
            <h5 class="mb-0 text-success">
              + Rp {{ number_format($shift->transactions()->where('payment_method', 'cash')->where('status', 'completed')->sum('total_amount'), 0, ',', '.') }}
            </h5>
          </div>
          <div class="mb-3">
            <label class="text-muted small">Deposits</label>
            <h5 class="mb-0 text-success">
              + Rp {{ number_format($cashMovements->where('type', 'deposit')->sum('amount'), 0, ',', '.') }}
            </h5>
          </div>
          <div class="mb-3">
            <label class="text-muted small">Withdrawals</label>
            <h5 class="mb-0 text-danger">
              - Rp {{ number_format($cashMovements->where('type', 'withdrawal')->sum('amount'), 0, ',', '.') }}
            </h5>
          </div>
          <div class="mb-3">
            <label class="text-muted small">Transfers</label>
            <h5 class="mb-0 text-warning">
              - Rp {{ number_format($cashMovements->where('type', 'transfer')->sum('amount'), 0, ',', '.') }}
            </h5>
          </div>
          <hr>
          <div>
            <label class="text-muted small">Expected Cash</label>
            <h4 class="mb-0 text-primary">
              Rp {{ number_format($shift->calculateExpectedCash(), 0, ',', '.') }}
            </h4>
          </div>
        </div>
      </div>

      @if($shift->isOpen())
        <!-- Add Cash Movement Button -->
        <div class="card mt-3">
          <div class="card-body">
            <button class="btn btn-primary w-100 mb-2" onclick="showAddModal('deposit')">
              <i class="bx bx-plus-circle me-1"></i>Deposit Cash
            </button>
            @if(auth()->user()->canManageCash())
              <button class="btn btn-warning w-100 mb-2" onclick="showAddModal('withdrawal')">
                <i class="bx bx-minus-circle me-1"></i>Withdraw Cash
              </button>
              <button class="btn btn-info w-100" onclick="showAddModal('transfer')">
                <i class="bx bx-transfer me-1"></i>Transfer Cash
              </button>
            @endif
          </div>
        </div>
      @endif
    </div>

    <!-- Cash Movements List -->
    <div class="col-md-8">
      <div class="card">
        <div class="card-header">
          <h5 class="mb-0">Cash Movements History</h5>
        </div>
        <div class="card-body">
          @if($cashMovements->count() > 0)
            <div class="table-responsive">
              <table class="table">
                <thead>
                  <tr>
                    <th>Tanggal</th>
                    <th>Type</th>
                    <th>Amount</th>
                    <th>Reason</th>
                    <th>User</th>
                    <th>Reference</th>
                    @if($shift->isOpen())
                      <th>Action</th>
                    @endif
                  </tr>
                </thead>
                <tbody>
                  @foreach($cashMovements as $movement)
                    <tr>
                      <td>{{ $movement->created_at->format('d/m/Y H:i') }}</td>
                      <td>
                        <span class="badge bg-{{ $movement->type === 'deposit' ? 'success' : ($movement->type === 'withdrawal' ? 'danger' : 'warning') }}">
                          {{ ucfirst($movement->type) }}
                        </span>
                      </td>
                      <td>
                        <strong class="text-{{ $movement->type === 'deposit' ? 'success' : ($movement->type === 'withdrawal' ? 'danger' : 'warning') }}">
                          {{ $movement->type === 'deposit' ? '+' : '-' }} Rp {{ number_format($movement->amount, 0, ',', '.') }}
                        </strong>
                      </td>
                      <td>{{ $movement->reason }}</td>
                      <td>{{ $movement->user->name }}</td>
                      <td>{{ $movement->reference_number ?? '-' }}</td>
                      @if($shift->isOpen() && (auth()->user()->canManageCash() || $movement->type === 'deposit'))
                        <td>
                          <button class="btn btn-sm btn-danger" onclick="confirmDelete({{ $movement->id }})">
                            <i class="bx bx-trash"></i>
                          </button>
                        </td>
                      @endif
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @else
            <div class="text-center py-5">
              <i class="bx bx-money fs-1 text-muted mb-3"></i>
              <p class="text-muted">Belum ada cash movement</p>
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Add Cash Movement Modal -->
<div class="modal fade" id="addCashMovementModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalTitle">Add Cash Movement</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="cashMovementForm">
        <div class="modal-body">
          <input type="hidden" id="movementType" name="type">
          <div class="mb-3">
            <label class="form-label">Amount <span class="text-danger">*</span></label>
            <input type="number" class="form-control" id="amount" name="amount" 
                   step="0.01" min="0.01" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Reason <span class="text-danger">*</span></label>
            <textarea class="form-control" id="reason" name="reason" rows="3" required></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Reference Number</label>
            <input type="text" class="form-control" id="reference_number" name="reference_number">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
function showAddModal(type) {
  const modal = new bootstrap.Modal(document.getElementById('addCashMovementModal'));
  const title = document.getElementById('modalTitle');
  const typeInput = document.getElementById('movementType');
  
  typeInput.value = type;
  title.textContent = type === 'deposit' ? 'Deposit Cash' : (type === 'withdrawal' ? 'Withdraw Cash' : 'Transfer Cash');
  
  document.getElementById('cashMovementForm').reset();
  modal.show();
}

document.getElementById('cashMovementForm').addEventListener('submit', function(e) {
  e.preventDefault();
  
  const formData = new FormData(this);
  
  Swal.fire({
    title: 'Memproses...',
    text: 'Mohon tunggu',
    allowOutsideClick: false,
    didOpen: () => {
      Swal.showLoading();
    }
  });

  fetch('{{ route("admin.pos.cash-movements.store", $shift->id) }}', {
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
      Swal.fire({
        icon: 'success',
        title: 'Berhasil',
        text: 'Cash movement berhasil dibuat',
        timer: 2000
      }).then(() => {
        location.reload();
      });
    } else {
      Swal.fire({
        icon: 'error',
        title: 'Gagal',
        text: data.message || 'Gagal membuat cash movement'
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
});

function confirmDelete(id) {
  Swal.fire({
    title: 'Hapus Cash Movement?',
    text: 'Apakah Anda yakin ingin menghapus cash movement ini?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#6c757d',
    confirmButtonText: 'Ya, Hapus',
    cancelButtonText: 'Batal'
  }).then((result) => {
    if (result.isConfirmed) {
      deleteCashMovement(id);
    }
  });
}

function deleteCashMovement(id) {
  Swal.fire({
    title: 'Menghapus...',
    text: 'Mohon tunggu',
    allowOutsideClick: false,
    didOpen: () => {
      Swal.showLoading();
    }
  });

  fetch('{{ route("admin.pos.cash-movements.destroy", [$shift->id, ":id"]) }}'.replace(':id', id), {
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
        text: 'Cash movement berhasil dihapus',
        timer: 2000
      }).then(() => {
        location.reload();
      });
    } else {
      Swal.fire({
        icon: 'error',
        title: 'Gagal',
        text: data.message || 'Gagal menghapus cash movement'
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
