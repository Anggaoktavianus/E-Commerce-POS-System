@extends('admin.layouts.app')

@section('title', 'Transaksi Baru - POS')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Header Section -->
  <div class="card page-header-card mb-4">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h4 class="mb-1">
            <i class="bx bx-plus-circle me-2 text-primary"></i>Transaksi Baru
          </h4>
          <p class="text-muted mb-0">Outlet: {{ $outlet->name }} | Shift: {{ $shift->id }}</p>
        </div>
        <div>
          <a href="{{ route('admin.pos.dashboard', ['outlet_id' => $outletId]) }}" class="btn btn-secondary">
            <i class="bx bx-arrow-back me-1"></i>Kembali
          </a>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-4">
    <!-- Product Search & Cart -->
    <div class="col-md-8">
      <!-- Product Search -->
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="mb-0"><i class="bx bx-search me-2"></i>Cari Produk</h5>
        </div>
        <div class="card-body">
          <div class="input-group mb-3">
            <input type="text" id="productSearch" class="form-control" placeholder="Cari produk atau scan barcode..." autofocus>
            <button class="btn btn-primary" onclick="searchProduct()">
              <i class="bx bx-search"></i> Cari
            </button>
            <button class="btn btn-success" onclick="toggleBarcodeScanner()" id="barcodeScannerBtn" title="Barcode Scanner">
              <i class="bx bx-camera"></i> Scan
            </button>
          </div>
          <!-- Barcode Scanner -->
          <div id="barcodeScannerContainer" style="display: none;" class="mb-3">
            <div class="card border-success">
              <div class="card-header bg-success text-white">
                <div class="d-flex justify-content-between align-items-center">
                  <span><i class="bx bx-camera me-1"></i>Barcode Scanner</span>
                  <button class="btn btn-sm btn-light" onclick="toggleBarcodeScanner()">
                    <i class="bx bx-x"></i>
                  </button>
                </div>
              </div>
              <div class="card-body">
                <div id="barcodeScanner" style="width: 100%; height: 300px; position: relative; background: #000;">
                  <video id="barcodeVideo" style="width: 100%; height: 100%; object-fit: cover;"></video>
                  <canvas id="barcodeCanvas" style="display: none;"></canvas>
                </div>
                <div class="mt-2 text-center">
                  <small class="text-muted">Arahkan kamera ke barcode produk</small>
                </div>
                <div id="barcodeResult" class="alert alert-info mt-2" style="display: none;"></div>
              </div>
            </div>
          </div>
          <div id="productResults" class="row g-2"></div>
        </div>
      </div>

      <!-- Cart -->
      <div class="card">
        <div class="card-header">
          <h5 class="mb-0"><i class="bx bx-cart me-2"></i>Keranjang</h5>
        </div>
        <div class="card-body">
          <div id="cartItems" class="table-responsive">
            <table class="table">
              <thead>
                <tr>
                  <th>Produk</th>
                  <th>Qty</th>
                  <th>Harga</th>
                  <th>Diskon</th>
                  <th>Total</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody id="cartTableBody">
                <tr>
                  <td colspan="6" class="text-center text-muted py-4">Keranjang kosong</td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="mt-3">
            <div class="d-flex justify-content-between mb-2">
              <span>Subtotal:</span>
              <strong id="subtotal">Rp 0</strong>
            </div>
            <div class="d-flex justify-content-between mb-2">
              <span>Diskon Item:</span>
              <strong id="itemDiscount" class="text-danger">Rp 0</strong>
            </div>
            <div class="d-flex justify-content-between mb-2">
              <span>Diskon Transaksi:</span>
              <strong id="transactionDiscount" class="text-danger">Rp 0</strong>
            </div>
            <div class="d-flex justify-content-between mb-2">
              <span>Total Diskon:</span>
              <strong id="totalDiscount" class="text-danger">Rp 0</strong>
            </div>
            <hr>
            <div class="d-flex justify-content-between">
              <span><strong>Total:</strong></span>
              <strong class="text-primary fs-4" id="total">Rp 0</strong>
            </div>
          </div>
          <!-- Discount & Coupon Section -->
          <div class="mt-3 border-top pt-3">
            <div class="mb-2">
              <label class="form-label small">Diskon Transaksi (Rp)</label>
              <input type="number" id="transactionDiscountInput" class="form-control form-control-sm" 
                     step="0.01" min="0" placeholder="0" onchange="updateTransactionDiscount()">
            </div>
            <div class="mb-2">
              <label class="form-label small">Kode Kupon/Voucher</label>
              <div class="input-group input-group-sm">
                <input type="text" id="couponCode" class="form-control" placeholder="Masukkan kode kupon">
                <button class="btn btn-outline-primary" type="button" onclick="applyCoupon()">
                  <i class="bx bx-check"></i>
                </button>
              </div>
              <small id="couponMessage" class="text-muted"></small>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Customer & Payment -->
    <div class="col-md-4">
      <!-- Customer Selection -->
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="mb-0"><i class="bx bx-user me-2"></i>Customer</h5>
        </div>
        <div class="card-body">
          <div class="input-group mb-2">
            <input type="text" id="customerSearch" class="form-control" placeholder="Cari customer...">
            <button class="btn btn-outline-primary" onclick="searchCustomer()">
              <i class="bx bx-search"></i>
            </button>
          </div>
          <div id="customerResults"></div>
          <div id="selectedCustomer" class="mt-2" style="display: none;">
            <div class="alert alert-info">
              <strong id="customerName"></strong>
              <button class="btn btn-sm btn-link float-end" onclick="clearCustomer()">Hapus</button>
            </div>
          </div>
          <button class="btn btn-sm btn-outline-primary w-100" onclick="showAddCustomerModal()">
            <i class="bx bx-plus me-1"></i>Tambah Customer Baru
          </button>
          <!-- Loyalty Points Section -->
          <div id="loyaltyPointsSection" class="mt-3 border-top pt-3" style="display: none;">
            <div class="mb-2">
              <label class="form-label small">Loyalty Points</label>
              <div class="d-flex justify-content-between mb-2">
                <span class="small text-muted">Balance:</span>
                <strong id="loyaltyBalance" class="text-primary">0 points</strong>
              </div>
              <div class="input-group input-group-sm">
                <input type="number" id="loyaltyPointsInput" class="form-control" 
                       placeholder="Points to redeem" min="0" step="1">
                <button class="btn btn-outline-success" type="button" onclick="applyLoyaltyPoints()">
                  <i class="bx bx-check"></i> Apply
                </button>
              </div>
              <small id="loyaltyMessage" class="text-muted"></small>
              <div id="loyaltyDiscount" class="mt-2" style="display: none;">
                <small class="text-success">
                  <i class="bx bx-check-circle"></i> Discount: <strong id="loyaltyDiscountAmount">Rp 0</strong>
                </small>
              </div>
            </div>
          </div>
          <!-- Member Discount Section -->
          <div id="memberDiscountSection" class="mt-3 border-top pt-3" style="display: none;">
            <div class="mb-2">
              <label class="form-label small">Member Discount</label>
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="memberDiscountToggle" 
                       onchange="toggleMemberDiscount()">
                <label class="form-check-label" for="memberDiscountToggle">
                  Apply Member Discount
                </label>
              </div>
              <small id="memberDiscountInfo" class="text-muted"></small>
              <div id="memberDiscountDisplay" class="mt-2" style="display: none;">
                <small class="text-success">
                  <i class="bx bx-check-circle"></i> Member Discount: <strong id="memberDiscountAmount">Rp 0</strong>
                </small>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Payment -->
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="mb-0"><i class="bx bx-credit-card me-2"></i>Pembayaran</h5>
        </div>
        <div class="card-body">
          <div class="mb-3">
            <label class="form-label">Metode Pembayaran</label>
            <select id="paymentMethod" class="form-select" onchange="updatePaymentMethod()">
              <option value="cash">Cash (Tunai)</option>
              <option value="card">Card (Kartu)</option>
              <option value="ewallet">E-Wallet</option>
              <option value="qris">QRIS</option>
              <option value="split">Split Payment</option>
            </select>
          </div>
          <div id="cashPayment" class="mb-3">
            <label class="form-label">Cash Received</label>
            <input type="number" id="cashReceived" class="form-control" step="0.01" min="0" onchange="calculateChange()">
            <small class="text-muted">Kembalian: <span id="changeAmount" class="text-success">Rp 0</span></small>
          </div>
          <div id="otherPayment" style="display: none;">
            <label class="form-label">Reference Number</label>
            <input type="text" id="paymentReference" class="form-control" placeholder="Nomor referensi pembayaran">
          </div>
          <!-- Split Payment UI -->
          <div id="splitPayment" style="display: none;">
            <label class="form-label">Split Payment</label>
            <div id="splitPaymentsList" class="mb-2"></div>
            <button type="button" class="btn btn-sm btn-outline-primary mb-2" onclick="addSplitPayment()">
              <i class="bx bx-plus"></i> Tambah Pembayaran
            </button>
            <div class="alert alert-info small mb-0">
              Total: <strong id="splitTotal">Rp 0</strong> / <strong id="splitRequired">Rp 0</strong>
            </div>
          </div>
        </div>
      </div>

      <!-- Actions -->
      <div class="card">
        <div class="card-body">
          <button class="btn btn-primary w-100 mb-2" onclick="confirmProcessTransaction()" id="processBtn" disabled>
            <i class="bx bx-check me-1"></i>Proses Transaksi <small>(F3)</small>
          </button>
          <button class="btn btn-secondary w-100" onclick="confirmClearCart()">
            <i class="bx bx-x me-1"></i>Bersihkan <small>(ESC)</small>
          </button>
          <div class="mt-2 text-center">
            <small class="text-muted">
              <kbd>F1</kbd> Transaksi Baru | 
              <kbd>F2</kbd> Cari Produk | 
              <kbd>F3</kbd> Proses | 
              <kbd>F4</kbd> Print
            </small>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Add Customer Modal -->
<div class="modal fade" id="addCustomerModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Tambah Customer Baru</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="addCustomerForm">
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Nama <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control">
          </div>
          <div class="mb-3">
            <label class="form-label">Telepon</label>
            <input type="text" name="phone" class="form-control">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Tambah</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
let cart = [];
let selectedCustomer = null;
let transactionDiscount = 0;
let couponDiscount = 0;
let appliedCoupon = null;
let loyaltyPointsDiscount = 0;
let appliedLoyaltyPoints = 0;
let customerLoyaltyBalance = 0;
let memberDiscount = 0;
let memberDiscountRate = 5; // Default 5%, will be loaded from settings
let applyMemberDiscount = false;
let splitPayments = [];
const outletId = {{ $outletId }};
const shiftId = {{ $shift->id }};

// Product Search
function searchProduct() {
  const query = document.getElementById('productSearch').value;
  if (!query) return;

  const resultsContainer = document.getElementById('productResults');
  const searchBtn = document.querySelector('#productSearch').nextElementSibling;
  
  // Show loading
  showSkeletonLoading(resultsContainer, 4);
  setButtonLoading(searchBtn, true);

  fetch(`{{ route('admin.pos.products.search') }}?query=${query}&outlet_id=${outletId}`, {
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      'Accept': 'application/json'
    }
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      displayProducts(data.data);
    } else {
      resultsContainer.innerHTML = '<div class="alert alert-warning">Produk tidak ditemukan</div>';
    }
  })
  .catch(error => {
    console.error('Error:', error);
    resultsContainer.innerHTML = '<div class="alert alert-danger">Terjadi kesalahan saat mencari produk</div>';
  })
  .finally(() => {
    setButtonLoading(searchBtn, false);
  });
}

function displayProducts(products) {
  const container = document.getElementById('productResults');
  container.innerHTML = '';
  
  products.forEach(product => {
    const card = document.createElement('div');
    card.className = 'col-md-6';
    card.innerHTML = `
      <div class="card ${product.has_stock ? '' : 'border-danger'}">
        <div class="card-body">
          <h6>${product.name}</h6>
          <p class="text-muted small mb-2">SKU: ${product.sku || '-'}</p>
          <p class="mb-2"><strong>Rp ${formatNumber(product.price)}</strong></p>
          <p class="mb-2">
            Stok: <span class="${product.has_stock ? 'text-success' : 'text-danger'}">${product.stock_at_outlet}</span>
          </p>
          ${product.has_stock ? `
            <button class="btn btn-sm btn-primary w-100" onclick="addToCart(${product.id}, '${product.name}', ${product.price}, ${product.stock_at_outlet})">
              <i class="bx bx-plus"></i> Tambah
            </button>
          ` : '<span class="badge bg-danger">Stok Habis</span>'}
        </div>
      </div>
    `;
    container.appendChild(card);
  });
}

// Cart Management
function addToCart(productId, productName, price, stock) {
  const existingItem = cart.find(item => item.product_id === productId);
  
  if (existingItem) {
    if (existingItem.quantity >= stock) {
      Swal.fire({
        icon: 'warning',
        title: 'Stok Tidak Mencukupi',
        text: `Stok tersedia: ${stock}`,
        timer: 2000
      });
      return;
    }
    existingItem.quantity += 1;
    existingItem.total_amount = existingItem.unit_price * existingItem.quantity - (existingItem.discount_amount || 0);
  } else {
    cart.push({
      product_id: productId,
      product_name: productName,
      quantity: 1,
      unit_price: price,
      discount_amount: 0,
      total_amount: price
    });
  }
  
  updateCart();
}

function removeFromCart(index) {
  cart.splice(index, 1);
  updateCart();
}

function updateQuantity(index, quantity) {
  if (quantity <= 0) {
    removeFromCart(index);
    return;
  }
  cart[index].quantity = quantity;
  cart[index].total_amount = (cart[index].unit_price * quantity) - (cart[index].discount_amount || 0);
  updateCart();
}

function updateItemDiscount(index, discount) {
  const discountAmount = parseFloat(discount) || 0;
  const item = cart[index];
  const maxDiscount = item.unit_price * item.quantity;
  
  if (discountAmount > maxDiscount) {
    Swal.fire({
      icon: 'warning',
      title: 'Diskon Terlalu Besar',
      text: `Diskon maksimal: Rp ${formatNumber(maxDiscount)}`,
      timer: 2000
    });
    return;
  }
  
  item.discount_amount = discountAmount;
  item.total_amount = (item.unit_price * item.quantity) - discountAmount;
  updateCart();
}

function updateCart() {
  const tbody = document.getElementById('cartTableBody');
  tbody.innerHTML = '';
  
  if (cart.length === 0) {
    tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4">Keranjang kosong</td></tr>';
    document.getElementById('processBtn').disabled = true;
  } else {
    cart.forEach((item, index) => {
      const row = document.createElement('tr');
      row.innerHTML = `
        <td>${item.product_name}</td>
        <td>
          <input type="number" class="form-control form-control-sm" value="${item.quantity}" 
                 min="1" onchange="updateQuantity(${index}, parseInt(this.value))" style="width: 80px;">
        </td>
        <td>Rp ${formatNumber(item.unit_price)}</td>
        <td>
          <input type="number" class="form-control form-control-sm" 
                 value="${item.discount_amount || 0}" 
                 step="0.01" min="0" 
                 placeholder="0"
                 onchange="updateItemDiscount(${index}, this.value)"
                 style="width: 100px;">
        </td>
        <td><strong>Rp ${formatNumber(item.total_amount)}</strong></td>
        <td>
          <button class="btn btn-sm btn-danger" onclick="confirmRemoveFromCart(${index})">
            <i class="bx bx-trash"></i>
          </button>
        </td>
      `;
      tbody.appendChild(row);
    });
    document.getElementById('processBtn').disabled = false;
  }
  
  calculateTotals();
}

function calculateTotals() {
  const itemSubtotal = cart.reduce((sum, item) => sum + (item.unit_price * item.quantity), 0);
  const itemDiscount = cart.reduce((sum, item) => sum + (item.discount_amount || 0), 0);
  const subtotal = itemSubtotal - itemDiscount;
  
  // Calculate member discount if enabled
  if (applyMemberDiscount) {
    calculateMemberDiscount();
  }
  
  const totalDiscount = itemDiscount + transactionDiscount + couponDiscount + loyaltyPointsDiscount + memberDiscount;
  const total = subtotal - transactionDiscount - couponDiscount - loyaltyPointsDiscount - memberDiscount;
  
  document.getElementById('subtotal').textContent = 'Rp ' + formatNumber(itemSubtotal);
  document.getElementById('itemDiscount').textContent = 'Rp ' + formatNumber(itemDiscount);
  document.getElementById('transactionDiscount').textContent = 'Rp ' + formatNumber(transactionDiscount + couponDiscount);
  document.getElementById('totalDiscount').textContent = 'Rp ' + formatNumber(totalDiscount);
  document.getElementById('total').textContent = 'Rp ' + formatNumber(total);
  
  // Update split payment required amount
  document.getElementById('splitRequired').textContent = 'Rp ' + formatNumber(total);
  updateSplitTotal();
  
  calculateChange();
}

function calculateChange() {
  const total = parseFloat(document.getElementById('total').textContent.replace(/[^\d]/g, ''));
  const cashReceived = parseFloat(document.getElementById('cashReceived').value) || 0;
  const change = cashReceived - total;
  
  document.getElementById('changeAmount').textContent = 'Rp ' + formatNumber(Math.max(0, change));
}

// Customer Management
function searchCustomer() {
  const query = document.getElementById('customerSearch').value;
  if (!query) return;

  const resultsContainer = document.getElementById('customerResults');
  const searchBtn = document.querySelector('#customerSearch').nextElementSibling;
  
  // Show loading
  resultsContainer.innerHTML = '<div class="text-center py-3"><div class="loading-inline"></div> Mencari...</div>';
  setButtonLoading(searchBtn, true);

  fetch(`{{ route('admin.pos.customers.search') }}?query=${query}`, {
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      'Accept': 'application/json'
    }
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      displayCustomers(data.data);
    } else {
      resultsContainer.innerHTML = '<div class="alert alert-warning">Customer tidak ditemukan</div>';
    }
  })
  .catch(error => {
    console.error('Error:', error);
    resultsContainer.innerHTML = '<div class="alert alert-danger">Terjadi kesalahan saat mencari customer</div>';
  })
  .finally(() => {
    setButtonLoading(searchBtn, false);
  });
}

function displayCustomers(customers) {
  const container = document.getElementById('customerResults');
  container.innerHTML = '';
  
  customers.forEach(customer => {
    const div = document.createElement('div');
    div.className = 'mb-2 p-2 border rounded cursor-pointer';
    div.onclick = () => selectCustomer(customer);
    div.innerHTML = `
      <strong>${customer.name}</strong><br>
      <small class="text-muted">${customer.email || customer.phone || ''}</small>
    `;
    container.appendChild(div);
  });
}

function selectCustomer(customer) {
  selectedCustomer = customer;
  document.getElementById('customerName').textContent = customer.name;
  document.getElementById('selectedCustomer').style.display = 'block';
  document.getElementById('customerResults').innerHTML = '';
  document.getElementById('customerSearch').value = '';
  
  // Load loyalty points balance
  if (customer.id) {
    loadLoyaltyBalance(customer.id);
  }
  
  // Check if customer is verified member
  if (customer.is_verified) {
    document.getElementById('memberDiscountSection').style.display = 'block';
    loadMemberDiscountRate();
  } else {
    document.getElementById('memberDiscountSection').style.display = 'none';
    memberDiscount = 0;
    applyMemberDiscount = false;
    document.getElementById('memberDiscountToggle').checked = false;
    calculateTotals();
  }
}

function loadMemberDiscountRate() {
  // Load from settings (default 5%)
  // For now, use default
  memberDiscountRate = 5;
  document.getElementById('memberDiscountInfo').textContent = `Member discount rate: ${memberDiscountRate}%`;
}

function toggleMemberDiscount() {
  applyMemberDiscount = document.getElementById('memberDiscountToggle').checked;
  
  if (applyMemberDiscount && selectedCustomer && selectedCustomer.is_verified) {
    calculateMemberDiscount();
  } else {
    memberDiscount = 0;
    document.getElementById('memberDiscountDisplay').style.display = 'none';
    calculateTotals();
  }
}

function calculateMemberDiscount() {
  if (!applyMemberDiscount || !selectedCustomer || !selectedCustomer.is_verified) {
    memberDiscount = 0;
    return;
  }
  
  const itemSubtotal = cart.reduce((sum, item) => sum + (item.unit_price * item.quantity), 0);
  const itemDiscount = cart.reduce((sum, item) => sum + (item.discount_amount || 0), 0);
  const subtotal = itemSubtotal - itemDiscount;
  
  memberDiscount = subtotal * (memberDiscountRate / 100);
  
  document.getElementById('memberDiscountAmount').textContent = 'Rp ' + formatNumber(memberDiscount);
  document.getElementById('memberDiscountDisplay').style.display = 'block';
  
  calculateTotals();
}

function loadLoyaltyBalance(customerId) {
  const balanceElement = document.getElementById('loyaltyBalance');
  balanceElement.innerHTML = '<span class="loading-inline"></span> Loading...';
  
  fetch(`{{ route('admin.pos.customers.search') }}?loyalty_check=true&customer_id=${customerId}`, {
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      'Accept': 'application/json'
    }
  })
  .then(response => response.json())
  .then(data => {
    if (data.success && data.balance !== undefined) {
      customerLoyaltyBalance = data.balance || 0;
      balanceElement.textContent = customerLoyaltyBalance + ' points';
      document.getElementById('loyaltyPointsSection').style.display = 'block';
    } else {
      customerLoyaltyBalance = 0;
      balanceElement.textContent = '0 points';
      document.getElementById('loyaltyPointsSection').style.display = 'block';
    }
  })
  .catch(error => {
    console.error('Error loading loyalty balance:', error);
    customerLoyaltyBalance = 0;
    balanceElement.textContent = '0 points';
    document.getElementById('loyaltyPointsSection').style.display = 'block';
  });
}

function applyLoyaltyPoints() {
  if (!selectedCustomer || !selectedCustomer.id) {
    Swal.fire({
      icon: 'warning',
      title: 'Pilih Customer',
      text: 'Silakan pilih customer terlebih dahulu',
      timer: 2000
    });
    return;
  }

  const points = parseInt(document.getElementById('loyaltyPointsInput').value) || 0;
  
  if (points <= 0) {
    Swal.fire({
      icon: 'warning',
      title: 'Points Tidak Valid',
      text: 'Masukkan jumlah points yang valid',
      timer: 2000
    });
    return;
  }

  if (points > customerLoyaltyBalance) {
    Swal.fire({
      icon: 'error',
      title: 'Points Tidak Mencukupi',
      text: `Balance: ${customerLoyaltyBalance} points`,
      timer: 3000
    });
    return;
  }

  // Calculate discount (1 point = Rp1)
  const discount = points;
  loyaltyPointsDiscount = discount;
  appliedLoyaltyPoints = points;

  document.getElementById('loyaltyMessage').textContent = `Menggunakan ${points} points`;
  document.getElementById('loyaltyMessage').className = 'text-success';
  document.getElementById('loyaltyDiscountAmount').textContent = 'Rp ' + formatNumber(discount);
  document.getElementById('loyaltyDiscount').style.display = 'block';
  
  calculateTotals();
}

function clearLoyaltyPoints() {
  loyaltyPointsDiscount = 0;
  appliedLoyaltyPoints = 0;
  document.getElementById('loyaltyPointsInput').value = '';
  document.getElementById('loyaltyMessage').textContent = '';
  document.getElementById('loyaltyDiscount').style.display = 'none';
  calculateTotals();
}

function clearCustomer() {
  selectedCustomer = null;
  document.getElementById('selectedCustomer').style.display = 'none';
  document.getElementById('loyaltyPointsSection').style.display = 'none';
  document.getElementById('memberDiscountSection').style.display = 'none';
  clearLoyaltyPoints();
  memberDiscount = 0;
  applyMemberDiscount = false;
  document.getElementById('memberDiscountToggle').checked = false;
  calculateTotals();
}

function showAddCustomerModal() {
  new bootstrap.Modal(document.getElementById('addCustomerModal')).show();
}

document.getElementById('addCustomerForm').addEventListener('submit', function(e) {
  e.preventDefault();
  const formData = new FormData(this);
  
  fetch('{{ route("admin.pos.customers.store") }}', {
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
      selectCustomer(data.data);
      bootstrap.Modal.getInstance(document.getElementById('addCustomerModal')).hide();
      this.reset();
    } else {
      alert(data.message || 'Gagal menambah customer');
    }
  });
});

// Payment Method
function updatePaymentMethod() {
  const method = document.getElementById('paymentMethod').value;
  if (method === 'cash') {
    document.getElementById('cashPayment').style.display = 'block';
    document.getElementById('otherPayment').style.display = 'none';
    document.getElementById('splitPayment').style.display = 'none';
  } else if (method === 'split') {
    document.getElementById('cashPayment').style.display = 'none';
    document.getElementById('otherPayment').style.display = 'none';
    document.getElementById('splitPayment').style.display = 'block';
    if (splitPayments.length === 0) {
      addSplitPayment();
    }
  } else {
    document.getElementById('cashPayment').style.display = 'none';
    document.getElementById('otherPayment').style.display = 'block';
    document.getElementById('splitPayment').style.display = 'none';
  }
}

// Split Payment Management
function addSplitPayment() {
  const paymentId = Date.now();
  splitPayments.push({
    id: paymentId,
    method: 'cash',
    amount: 0,
    reference: ''
  });
  updateSplitPaymentsUI();
}

function removeSplitPayment(id) {
  splitPayments = splitPayments.filter(p => p.id !== id);
  if (splitPayments.length === 0 && document.getElementById('paymentMethod').value === 'split') {
    document.getElementById('paymentMethod').value = 'cash';
    updatePaymentMethod();
  } else {
    updateSplitPaymentsUI();
  }
}

function updateSplitPayment(id, field, value) {
  const payment = splitPayments.find(p => p.id === id);
  if (payment) {
    payment[field] = value;
    updateSplitPaymentsUI();
  }
}

function updateSplitPaymentsUI() {
  const container = document.getElementById('splitPaymentsList');
  container.innerHTML = '';
  
  splitPayments.forEach((payment, index) => {
    const div = document.createElement('div');
    div.className = 'border rounded p-2 mb-2';
    div.innerHTML = `
      <div class="d-flex justify-content-between align-items-center mb-2">
        <strong>Pembayaran ${index + 1}</strong>
        <button class="btn btn-sm btn-danger" onclick="removeSplitPayment(${payment.id})">
          <i class="bx bx-trash"></i>
        </button>
      </div>
      <div class="row g-2">
        <div class="col-6">
          <select class="form-select form-select-sm" onchange="updateSplitPayment(${payment.id}, 'method', this.value)">
            <option value="cash" ${payment.method === 'cash' ? 'selected' : ''}>Cash</option>
            <option value="card" ${payment.method === 'card' ? 'selected' : ''}>Card</option>
            <option value="ewallet" ${payment.method === 'ewallet' ? 'selected' : ''}>E-Wallet</option>
            <option value="qris" ${payment.method === 'qris' ? 'selected' : ''}>QRIS</option>
          </select>
        </div>
        <div class="col-6">
          <input type="number" class="form-control form-control-sm" 
                 placeholder="Jumlah" step="0.01" min="0"
                 value="${payment.amount}"
                 onchange="updateSplitPayment(${payment.id}, 'amount', parseFloat(this.value) || 0)">
        </div>
        ${payment.method !== 'cash' ? `
        <div class="col-12">
          <input type="text" class="form-control form-control-sm" 
                 placeholder="Reference Number"
                 value="${payment.reference}"
                 onchange="updateSplitPayment(${payment.id}, 'reference', this.value)">
        </div>
        ` : ''}
      </div>
    `;
    container.appendChild(div);
  });
  
  updateSplitTotal();
}

function updateSplitTotal() {
  const total = splitPayments.reduce((sum, p) => sum + (parseFloat(p.amount) || 0), 0);
  const required = parseFloat(document.getElementById('total').textContent.replace(/[^\d]/g, '')) || 0;
  
  document.getElementById('splitTotal').textContent = 'Rp ' + formatNumber(total);
  document.getElementById('splitTotal').className = total >= required ? 'text-success' : 'text-danger';
}

// Transaction Discount
function updateTransactionDiscount() {
  const discount = parseFloat(document.getElementById('transactionDiscountInput').value) || 0;
  const subtotal = cart.reduce((sum, item) => sum + (item.unit_price * item.quantity), 0) - 
                   cart.reduce((sum, item) => sum + (item.discount_amount || 0), 0);
  
  if (discount > subtotal) {
    Swal.fire({
      icon: 'warning',
      title: 'Diskon Terlalu Besar',
      text: `Diskon maksimal: Rp ${formatNumber(subtotal)}`,
      timer: 2000
    });
    document.getElementById('transactionDiscountInput').value = subtotal;
    transactionDiscount = subtotal;
  } else {
    transactionDiscount = discount;
  }
  
  calculateTotals();
}

// Coupon Management
function applyCoupon() {
  const code = document.getElementById('couponCode').value.trim();
  if (!code) {
    Swal.fire({
      icon: 'warning',
      title: 'Kode Kupon Kosong',
      text: 'Silakan masukkan kode kupon',
      timer: 2000
    });
    return;
  }
  
  const subtotal = cart.reduce((sum, item) => sum + (item.unit_price * item.quantity), 0) - 
                   cart.reduce((sum, item) => sum + (item.discount_amount || 0), 0) - 
                   transactionDiscount;
  
  fetch(`{{ route('admin.pos.products.search') }}?coupon_check=true&code=${code}&subtotal=${subtotal}&customer_id=${selectedCustomer ? selectedCustomer.id : ''}`, {
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      'Accept': 'application/json'
    }
  })
  .then(response => response.json())
  .then(data => {
    if (data.success && data.coupon) {
      appliedCoupon = data.coupon;
      couponDiscount = data.discount || 0;
      document.getElementById('couponMessage').textContent = `Kupon diterapkan: Diskon Rp ${formatNumber(couponDiscount)}`;
      document.getElementById('couponMessage').className = 'text-success';
      calculateTotals();
    } else {
      Swal.fire({
        icon: 'error',
        title: 'Kupon Tidak Valid',
        text: data.message || 'Kode kupon tidak ditemukan atau tidak dapat digunakan',
        timer: 3000
      });
      document.getElementById('couponCode').value = '';
      document.getElementById('couponMessage').textContent = '';
    }
  })
  .catch(error => {
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: 'Terjadi kesalahan saat memvalidasi kupon'
    });
  });
}

// Confirmation Dialogs
function confirmProcessTransaction() {
  if (cart.length === 0) {
    Swal.fire({
      icon: 'warning',
      title: 'Keranjang Kosong',
      text: 'Silakan tambahkan produk terlebih dahulu'
    });
    return;
  }

  const paymentMethod = document.getElementById('paymentMethod').value;
  
  // Validate split payment
  if (paymentMethod === 'split') {
    const total = parseFloat(document.getElementById('total').textContent.replace(/[^\d]/g, '')) || 0;
    const splitTotal = splitPayments.reduce((sum, p) => sum + (parseFloat(p.amount) || 0), 0);
    
    if (Math.abs(splitTotal - total) > 0.01) {
      Swal.fire({
        icon: 'error',
        title: 'Total Split Payment Tidak Sesuai',
        text: `Total pembayaran: Rp ${formatNumber(splitTotal)}, Total transaksi: Rp ${formatNumber(total)}`
      });
      return;
    }
  }

  Swal.fire({
    title: 'Konfirmasi Transaksi',
    html: `
      <p>Total: <strong>Rp ${document.getElementById('total').textContent.replace('Rp ', '')}</strong></p>
      <p>Metode: <strong>${paymentMethod.toUpperCase()}</strong></p>
      <p>Apakah Anda yakin ingin memproses transaksi ini?</p>
    `,
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#147440',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Ya, Proses',
    cancelButtonText: 'Batal'
  }).then((result) => {
    if (result.isConfirmed) {
      processTransaction();
    }
  });
}

function confirmClearCart() {
  Swal.fire({
    title: 'Bersihkan Keranjang?',
    text: 'Semua item di keranjang akan dihapus',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#6c757d',
    confirmButtonText: 'Ya, Bersihkan',
    cancelButtonText: 'Batal'
  }).then((result) => {
    if (result.isConfirmed) {
      clearCart();
    }
  });
}

function confirmRemoveFromCart(index) {
  Swal.fire({
    title: 'Hapus Item?',
    text: cart[index].product_name,
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#6c757d',
    confirmButtonText: 'Ya, Hapus',
    cancelButtonText: 'Batal',
    buttonsStyling: false,
    customClass: {
      confirmButton: 'btn btn-sm btn-danger',
      cancelButton: 'btn btn-sm btn-secondary'
    }
  }).then((result) => {
    if (result.isConfirmed) {
      removeFromCart(index);
    }
  });
}

// Process Transaction
function processTransaction() {
  const paymentMethod = document.getElementById('paymentMethod').value;
  const cashReceived = document.getElementById('cashReceived').value;
  const paymentReference = document.getElementById('paymentReference').value;
  
  if (paymentMethod === 'cash' && (!cashReceived || parseFloat(cashReceived) < calculateTotal())) {
    Swal.fire({
      icon: 'error',
      title: 'Cash Tidak Mencukupi',
      text: 'Jumlah cash yang diterima tidak mencukupi'
    });
    return;
  }

  const itemSubtotal = cart.reduce((sum, item) => sum + (item.unit_price * item.quantity), 0);
  const itemDiscount = cart.reduce((sum, item) => sum + (item.discount_amount || 0), 0);
  const subtotal = itemSubtotal - itemDiscount;
  
  // Calculate member discount if enabled
  if (applyMemberDiscount) {
    calculateMemberDiscount();
  }
  
  const total = subtotal - transactionDiscount - couponDiscount - loyaltyPointsDiscount - memberDiscount;

  const transactionData = {
    outlet_id: outletId,
    shift_id: shiftId,
    customer_id: selectedCustomer ? selectedCustomer.id : null,
    items: cart.map(item => ({
      product_id: item.product_id,
      quantity: item.quantity,
      unit_price: item.unit_price,
      discount_amount: item.discount_amount || 0,
      tax_amount: 0,
      total_amount: item.total_amount
    })),
    subtotal: subtotal,
    discount_amount: transactionDiscount + couponDiscount + loyaltyPointsDiscount + memberDiscount,
    tax_amount: 0,
    total_amount: total,
    payment_method: paymentMethod,
    cash_received: paymentMethod === 'cash' ? parseFloat(cashReceived) : null,
    change_amount: paymentMethod === 'cash' ? (parseFloat(cashReceived) - total) : null,
    payment_details: paymentMethod !== 'cash' && paymentMethod !== 'split' ? { reference_number: paymentReference } : null,
    coupon_code: appliedCoupon ? appliedCoupon.code : null,
    loyalty_points: appliedLoyaltyPoints || null,
    apply_member_discount: applyMemberDiscount || false
  };

  // Add split payments if split payment
  if (paymentMethod === 'split') {
    transactionData.payments = splitPayments.map(p => ({
      method: p.method,
      amount: parseFloat(p.amount) || 0,
      reference_number: p.reference || null,
      details: p.method !== 'cash' ? { reference_number: p.reference } : null
    }));
  }

  // Show loading
  const processBtn = document.getElementById('processBtn');
  setButtonLoading(processBtn, true);
  showGlobalLoading('Memproses transaksi...');

  fetch('{{ route("admin.pos.transactions.store") }}', {
    method: 'POST',
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      'Accept': 'application/json',
      'Content-Type': 'application/json'
    },
    body: JSON.stringify(transactionData)
  })
  .then(response => response.json())
  .then(data => {
    hideGlobalLoading();
    setButtonLoading(processBtn, false);
    
    if (data.success) {
      // Clear draft after successful transaction
      clearDraft();
      
      Swal.fire({
        icon: 'success',
        title: 'Transaksi Berhasil!',
        text: `No. Transaksi: ${data.data.transaction_number}`,
        confirmButtonText: 'OK'
      }).then(() => {
        window.location.href = '{{ route("admin.pos.transactions.index") }}';
      });
    } else {
      Swal.fire({
        icon: 'error',
        title: 'Gagal Memproses Transaksi',
        text: data.message || 'Terjadi kesalahan'
      });
    }
  })
  .catch(error => {
    console.error('Error:', error);
    hideGlobalLoading();
    setButtonLoading(processBtn, false);
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: 'Terjadi kesalahan saat memproses transaksi'
    });
  });
}

function calculateTotal() {
  return cart.reduce((sum, item) => sum + item.total_amount, 0);
}

function clearCart() {
  cart = [];
  transactionDiscount = 0;
  couponDiscount = 0;
  appliedCoupon = null;
  loyaltyPointsDiscount = 0;
  appliedLoyaltyPoints = 0;
  memberDiscount = 0;
  applyMemberDiscount = false;
  document.getElementById('transactionDiscountInput').value = '';
  document.getElementById('couponCode').value = '';
  document.getElementById('couponMessage').textContent = '';
  document.getElementById('memberDiscountToggle').checked = false;
  clearLoyaltyPoints();
  clearDraft(); // Clear draft when cart is cleared
  updateCart();
}

function formatNumber(num) {
  return new Intl.NumberFormat('id-ID').format(num);
}

// Keyboard Shortcuts
document.addEventListener('keydown', function(e) {
  // F1 - New Transaction (reload page)
  if (e.key === 'F1') {
    e.preventDefault();
    if (confirm('Buat transaksi baru? (Data yang belum disimpan akan hilang)')) {
      window.location.reload();
    }
  }
  
  // F2 - Focus product search
  if (e.key === 'F2') {
    e.preventDefault();
    document.getElementById('productSearch').focus();
  }
  
  // F3 - Process transaction
  if (e.key === 'F3' && !document.getElementById('processBtn').disabled) {
    e.preventDefault();
    confirmProcessTransaction();
  }
  
  // F4 - Print (if transaction completed, not available in create)
  // ESC - Clear/Cancel
  if (e.key === 'Escape') {
    if (cart.length > 0) {
      confirmClearCart();
    }
  }
  
  // Enter in product search
  if (e.target.id === 'productSearch' && e.key === 'Enter') {
    e.preventDefault();
    searchProduct();
  }
  
  // Enter in customer search
  if (e.target.id === 'customerSearch' && e.key === 'Enter') {
    e.preventDefault();
    searchCustomer();
  }
  
  // Enter in coupon code
  if (e.target.id === 'couponCode' && e.key === 'Enter') {
    e.preventDefault();
    applyCoupon();
  }
});

// Initialize payment method display
updatePaymentMethod();

// Barcode Scanner
let barcodeScannerActive = false;
let barcodeStream = null;
let barcodeScannerInterval = null;

function toggleBarcodeScanner() {
  const container = document.getElementById('barcodeScannerContainer');
  const btn = document.getElementById('barcodeScannerBtn');
  
  if (barcodeScannerActive) {
    stopBarcodeScanner();
    container.style.display = 'none';
    btn.classList.remove('btn-danger');
    btn.classList.add('btn-success');
    btn.innerHTML = '<i class="bx bx-camera"></i> Scan';
  } else {
    startBarcodeScanner();
    container.style.display = 'block';
    btn.classList.remove('btn-success');
    btn.classList.add('btn-danger');
    btn.innerHTML = '<i class="bx bx-x"></i> Stop';
  }
  
  barcodeScannerActive = !barcodeScannerActive;
}

async function startBarcodeScanner() {
  const video = document.getElementById('barcodeVideo');
  const canvas = document.getElementById('barcodeCanvas');
  const resultDiv = document.getElementById('barcodeResult');
  
  try {
    // Request camera access
    const stream = await navigator.mediaDevices.getUserMedia({
      video: {
        facingMode: 'environment' // Use back camera on mobile
      }
    });
    
    barcodeStream = stream;
    video.srcObject = stream;
    video.play();
    
    // Start scanning
    barcodeScannerInterval = setInterval(() => {
      scanBarcode(video, canvas, resultDiv);
    }, 500); // Scan every 500ms
    
  } catch (error) {
    console.error('Error accessing camera:', error);
    Swal.fire({
      icon: 'error',
      title: 'Camera Access Denied',
      text: 'Silakan izinkan akses kamera untuk menggunakan barcode scanner'
    });
    toggleBarcodeScanner();
  }
}

function stopBarcodeScanner() {
  if (barcodeStream) {
    barcodeStream.getTracks().forEach(track => track.stop());
    barcodeStream = null;
  }
  
  if (barcodeScannerInterval) {
    clearInterval(barcodeScannerInterval);
    barcodeScannerInterval = null;
  }
  
  const video = document.getElementById('barcodeVideo');
  video.srcObject = null;
  
  document.getElementById('barcodeResult').style.display = 'none';
}

function scanBarcode(video, canvas, resultDiv) {
  const context = canvas.getContext('2d');
  canvas.width = video.videoWidth;
  canvas.height = video.videoHeight;
  context.drawImage(video, 0, 0, canvas.width, canvas.height);
  
  const imageData = context.getImageData(0, 0, canvas.width, canvas.height);
  
  // Simple barcode detection using ZXing library (we'll use manual input fallback)
  // For now, we'll use a simpler approach: manual barcode input with camera overlay
  
  // Alternative: Use QuaggaJS or ZXing library
  // For simplicity, we'll add a manual barcode input that works with camera
}

// Alternative: Simple barcode input with camera overlay
function handleBarcodeInput(barcode) {
  if (!barcode || barcode.length < 3) return;
  
  // Search product by barcode
  document.getElementById('productSearch').value = barcode;
  searchProduct();
  
  // Show result
  const resultDiv = document.getElementById('barcodeResult');
  resultDiv.innerHTML = `<i class="bx bx-check-circle"></i> Barcode ditemukan: ${barcode}`;
  resultDiv.className = 'alert alert-success mt-2';
  resultDiv.style.display = 'block';
  
  setTimeout(() => {
    resultDiv.style.display = 'none';
  }, 3000);
}

// Listen for barcode scanner input (keyboard wedge scanner)
document.getElementById('productSearch').addEventListener('keypress', function(e) {
  // If Enter is pressed quickly after typing, it might be a barcode scanner
  if (e.key === 'Enter') {
    const value = this.value.trim();
    if (value.length >= 3) {
      handleBarcodeInput(value);
    }
  }
});

// Enhanced: Listen for rapid input (barcode scanners usually input very fast)
let barcodeInputTimer;
let lastInputTime = 0;
document.getElementById('productSearch').addEventListener('input', function(e) {
  const now = Date.now();
  const timeDiff = now - lastInputTime;
  lastInputTime = now;
  
  // If input is very fast (less than 50ms between characters), it's likely a barcode scanner
  if (timeDiff < 50 && timeDiff > 0) {
    clearTimeout(barcodeInputTimer);
    barcodeInputTimer = setTimeout(() => {
      const value = this.value.trim();
      if (value.length >= 3) {
        handleBarcodeInput(value);
      }
    }, 100);
  }
});

// Auto-save Draft Transactions
const DRAFT_STORAGE_KEY = `pos_draft_${outletId}_${shiftId}`;

function saveDraft() {
  const draft = {
    cart: cart,
    selectedCustomer: selectedCustomer,
    transactionDiscount: transactionDiscount,
    couponDiscount: couponDiscount,
    appliedCoupon: appliedCoupon,
    loyaltyPointsDiscount: loyaltyPointsDiscount,
    appliedLoyaltyPoints: appliedLoyaltyPoints,
    customerLoyaltyBalance: customerLoyaltyBalance,
    splitPayments: splitPayments,
    paymentMethod: document.getElementById('paymentMethod').value,
    cashReceived: document.getElementById('cashReceived').value,
    paymentReference: document.getElementById('paymentReference').value,
    couponCode: document.getElementById('couponCode').value,
    transactionDiscountInput: document.getElementById('transactionDiscountInput').value,
    loyaltyPointsInput: document.getElementById('loyaltyPointsInput').value,
    timestamp: new Date().toISOString()
  };
  
  try {
    localStorage.setItem(DRAFT_STORAGE_KEY, JSON.stringify(draft));
    console.log('Draft saved');
  } catch (e) {
    console.error('Failed to save draft:', e);
  }
}

function loadDraft() {
  try {
    const draftData = localStorage.getItem(DRAFT_STORAGE_KEY);
    if (!draftData) return false;
    
    const draft = JSON.parse(draftData);
    
    // Check if draft is not too old (max 24 hours)
    const draftTime = new Date(draft.timestamp);
    const now = new Date();
    const hoursDiff = (now - draftTime) / (1000 * 60 * 60);
    
    if (hoursDiff > 24) {
      clearDraft();
      return false;
    }
    
    // Restore draft
    Swal.fire({
      title: 'Draft Ditemukan',
      html: `
        <p>Ada draft transaksi yang belum selesai.</p>
        <p>Dibuat: ${new Date(draft.timestamp).toLocaleString('id-ID')}</p>
        <p>Apakah Anda ingin melanjutkan draft ini?</p>
      `,
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: '#147440',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Ya, Lanjutkan',
      cancelButtonText: 'Tidak, Mulai Baru'
    }).then((result) => {
      if (result.isConfirmed) {
        restoreDraft(draft);
      } else {
        clearDraft();
      }
    });
    
    return true;
  } catch (e) {
    console.error('Failed to load draft:', e);
    clearDraft();
    return false;
  }
}

function restoreDraft(draft) {
  cart = draft.cart || [];
  selectedCustomer = draft.selectedCustomer || null;
  transactionDiscount = draft.transactionDiscount || 0;
  couponDiscount = draft.couponDiscount || 0;
  appliedCoupon = draft.appliedCoupon || null;
  loyaltyPointsDiscount = draft.loyaltyPointsDiscount || 0;
  appliedLoyaltyPoints = draft.appliedLoyaltyPoints || 0;
  customerLoyaltyBalance = draft.customerLoyaltyBalance || 0;
  splitPayments = draft.splitPayments || [];
  
  // Restore UI
  if (selectedCustomer) {
    document.getElementById('customerName').textContent = selectedCustomer.name;
    document.getElementById('selectedCustomer').style.display = 'block';
    if (customerLoyaltyBalance > 0) {
      document.getElementById('loyaltyBalance').textContent = customerLoyaltyBalance + ' points';
      document.getElementById('loyaltyPointsSection').style.display = 'block';
    }
  }
  
  document.getElementById('paymentMethod').value = draft.paymentMethod || 'cash';
  document.getElementById('cashReceived').value = draft.cashReceived || '';
  document.getElementById('paymentReference').value = draft.paymentReference || '';
  document.getElementById('couponCode').value = draft.couponCode || '';
  document.getElementById('transactionDiscountInput').value = draft.transactionDiscountInput || '';
  document.getElementById('loyaltyPointsInput').value = draft.loyaltyPointsInput || '';
  
  updatePaymentMethod();
  updateCart();
  calculateTotals();
  
  if (appliedCoupon) {
    document.getElementById('couponMessage').textContent = `Kupon diterapkan: Diskon Rp ${formatNumber(couponDiscount)}`;
    document.getElementById('couponMessage').className = 'text-success';
  }
  
  if (appliedLoyaltyPoints > 0) {
    document.getElementById('loyaltyMessage').textContent = `Menggunakan ${appliedLoyaltyPoints} points`;
    document.getElementById('loyaltyMessage').className = 'text-success';
    document.getElementById('loyaltyDiscountAmount').textContent = 'Rp ' + formatNumber(loyaltyPointsDiscount);
    document.getElementById('loyaltyDiscount').style.display = 'block';
  }
  
  // Restore split payments UI
  if (draft.paymentMethod === 'split' && splitPayments.length > 0) {
    updateSplitPaymentsUI();
  }
  
  Swal.fire({
    icon: 'success',
    title: 'Draft Dipulihkan',
    text: 'Draft transaksi berhasil dipulihkan',
    timer: 2000
  });
}

function clearDraft() {
  try {
    localStorage.removeItem(DRAFT_STORAGE_KEY);
    console.log('Draft cleared');
  } catch (e) {
    console.error('Failed to clear draft:', e);
  }
}

// Auto-save draft setiap 10 detik
setInterval(() => {
  if (cart.length > 0) {
    saveDraft();
  }
}, 10000);

// Save draft on cart changes
const originalUpdateCart = updateCart;
updateCart = function() {
  originalUpdateCart();
  if (cart.length > 0) {
    saveDraft();
  }
};

// Load draft on page load
document.addEventListener('DOMContentLoaded', function() {
  setTimeout(() => {
    loadDraft();
  }, 500);
});

// Clear draft after successful transaction
const originalProcessTransaction = processTransaction;
processTransaction = function() {
  const result = originalProcessTransaction.apply(this, arguments);
  // Clear draft will be called after success in the promise
  return result;
};
</script>
@endsection
