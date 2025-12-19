@extends('admin.layouts.app')

@section('title', 'POS Settings - ' . $outlet->name)

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Header Section -->
  <div class="card page-header-card mb-4">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h4 class="mb-1">
            <i class="bx bx-cog me-2 text-primary"></i>POS Settings
          </h4>
          <p class="text-muted mb-0">Outlet: {{ $outlet->name }}</p>
        </div>
        <div>
          <a href="{{ route('admin.pos.settings.index') }}" class="btn btn-secondary">
            <i class="bx bx-arrow-back me-1"></i>Kembali
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- Settings Form -->
  <form id="settingsForm">
    <div class="row g-4">
      <!-- Tax Settings -->
      <div class="col-md-6">
        <div class="card">
          <div class="card-header">
            <h5 class="mb-0"><i class="bx bx-receipt me-2"></i>Tax Settings</h5>
          </div>
          <div class="card-body">
            <div class="mb-3">
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="tax_enabled" 
                       name="tax_enabled" {{ $defaultSettings['tax_enabled'] ? 'checked' : '' }}>
                <label class="form-check-label" for="tax_enabled">Enable Tax</label>
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label">Tax Rate (%)</label>
              <input type="number" class="form-control" id="tax_rate" name="tax_rate" 
                     value="{{ $defaultSettings['tax_rate'] }}" step="0.01" min="0" max="100">
            </div>
          </div>
        </div>
      </div>

      <!-- Discount Settings -->
      <div class="col-md-6">
        <div class="card">
          <div class="card-header">
            <h5 class="mb-0"><i class="bx bx-discount me-2"></i>Discount Settings</h5>
          </div>
          <div class="card-body">
            <div class="mb-3">
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="discount_enabled" 
                       name="discount_enabled" {{ $defaultSettings['discount_enabled'] ? 'checked' : '' }}>
                <label class="form-check-label" for="discount_enabled">Enable Discount</label>
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label">Max Discount Percentage (%)</label>
              <input type="number" class="form-control" id="max_discount_percentage" name="max_discount_percentage" 
                     value="{{ $defaultSettings['max_discount_percentage'] }}" step="0.01" min="0" max="100">
            </div>
          </div>
        </div>
      </div>

      <!-- Payment Methods -->
      <div class="col-md-6">
        <div class="card">
          <div class="card-header">
            <h5 class="mb-0"><i class="bx bx-credit-card me-2"></i>Payment Methods</h5>
          </div>
          <div class="card-body">
            @php
              $paymentMethods = $defaultSettings['payment_methods'] ?? ['cash', 'card', 'ewallet', 'qris'];
            @endphp
            <div class="mb-2">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="payment_methods[]" value="cash" 
                       id="pm_cash" {{ in_array('cash', $paymentMethods) ? 'checked' : '' }}>
                <label class="form-check-label" for="pm_cash">Cash</label>
              </div>
            </div>
            <div class="mb-2">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="payment_methods[]" value="card" 
                       id="pm_card" {{ in_array('card', $paymentMethods) ? 'checked' : '' }}>
                <label class="form-check-label" for="pm_card">Card</label>
              </div>
            </div>
            <div class="mb-2">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="payment_methods[]" value="ewallet" 
                       id="pm_ewallet" {{ in_array('ewallet', $paymentMethods) ? 'checked' : '' }}>
                <label class="form-check-label" for="pm_ewallet">E-Wallet</label>
              </div>
            </div>
            <div class="mb-2">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="payment_methods[]" value="qris" 
                       id="pm_qris" {{ in_array('qris', $paymentMethods) ? 'checked' : '' }}>
                <label class="form-check-label" for="pm_qris">QRIS</label>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Loyalty Points -->
      <div class="col-md-6">
        <div class="card">
          <div class="card-header">
            <h5 class="mb-0"><i class="bx bx-gift me-2"></i>Loyalty Points</h5>
          </div>
          <div class="card-body">
            <div class="mb-3">
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="loyalty_points_enabled" 
                       name="loyalty_points_enabled" {{ $defaultSettings['loyalty_points_enabled'] ? 'checked' : '' }}>
                <label class="form-check-label" for="loyalty_points_enabled">Enable Loyalty Points</label>
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label">Points Rate (%)</label>
              <input type="number" class="form-control" id="loyalty_points_rate" name="loyalty_points_rate" 
                     value="{{ $defaultSettings['loyalty_points_rate'] }}" step="0.01" min="0" max="100">
              <small class="text-muted">Percentage of transaction amount to award as points</small>
            </div>
          </div>
        </div>
      </div>

      <!-- Member Discount -->
      <div class="col-md-6">
        <div class="card">
          <div class="card-header">
            <h5 class="mb-0"><i class="bx bx-id-card me-2"></i>Member Discount</h5>
          </div>
          <div class="card-body">
            <div class="mb-3">
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="member_discount_enabled" 
                       name="member_discount_enabled" {{ ($defaultSettings['member_discount_enabled'] ?? true) ? 'checked' : '' }}>
                <label class="form-check-label" for="member_discount_enabled">Enable Member Discount</label>
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label">Member Discount Rate (%)</label>
              <input type="number" class="form-control" id="member_discount_rate" name="member_discount_rate" 
                     value="{{ $defaultSettings['member_discount_rate'] ?? 5 }}" step="0.01" min="0" max="100">
              <small class="text-muted">Discount percentage for verified members</small>
            </div>
          </div>
        </div>
      </div>

      <!-- Receipt Settings -->
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <h5 class="mb-0"><i class="bx bx-printer me-2"></i>Receipt Settings</h5>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="auto_print_receipt" 
                           name="auto_print_receipt" {{ $defaultSettings['auto_print_receipt'] ? 'checked' : '' }}>
                    <label class="form-check-label" for="auto_print_receipt">Auto Print Receipt</label>
                  </div>
                </div>
                <div class="mb-3">
                  <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="receipt_show_logo" 
                           name="receipt_show_logo" {{ $defaultSettings['receipt_show_logo'] ? 'checked' : '' }}>
                    <label class="form-check-label" for="receipt_show_logo">Show Logo on Receipt</label>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <label class="form-label">Receipt Footer Text</label>
                  <textarea class="form-control" id="receipt_footer_text" name="receipt_footer_text" rows="3">{{ $defaultSettings['receipt_footer_text'] }}</textarea>
                  <small class="text-muted">Text to display at bottom of receipt</small>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Submit Button -->
    <div class="mt-4">
      <button type="submit" class="btn btn-primary">
        <i class="bx bx-save me-1"></i>Simpan Settings
      </button>
    </div>
  </form>
</div>

<script>
document.getElementById('settingsForm').addEventListener('submit', function(e) {
  e.preventDefault();
  
  const formData = new FormData(this);
  const data = {};
  
  // Convert FormData to object
  for (let [key, value] of formData.entries()) {
    if (key === 'payment_methods[]') {
      if (!data.payment_methods) data.payment_methods = [];
      data.payment_methods.push(value);
    } else {
      data[key] = value;
    }
  }
  
  // Handle checkboxes
  data.tax_enabled = document.getElementById('tax_enabled').checked;
  data.discount_enabled = document.getElementById('discount_enabled').checked;
  data.loyalty_points_enabled = document.getElementById('loyalty_points_enabled').checked;
  data.member_discount_enabled = document.getElementById('member_discount_enabled').checked;
  data.auto_print_receipt = document.getElementById('auto_print_receipt').checked;
  data.receipt_show_logo = document.getElementById('receipt_show_logo').checked;
  
  Swal.fire({
    title: 'Menyimpan...',
    text: 'Mohon tunggu',
    allowOutsideClick: false,
    didOpen: () => {
      Swal.showLoading();
    }
  });

  fetch('{{ route("admin.pos.settings.update", $outlet->id) }}', {
    method: 'PUT',
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      'Accept': 'application/json',
      'Content-Type': 'application/json'
    },
    body: JSON.stringify(data)
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      Swal.fire({
        icon: 'success',
        title: 'Berhasil',
        text: 'Settings berhasil disimpan',
        timer: 2000
      });
    } else {
      Swal.fire({
        icon: 'error',
        title: 'Gagal',
        text: data.message || 'Gagal menyimpan settings'
      });
    }
  })
  .catch(error => {
    console.error('Error:', error);
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: 'Terjadi kesalahan saat menyimpan settings'
    });
  });
});
</script>
@endsection
