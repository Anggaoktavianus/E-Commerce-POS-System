@extends('layouts.app')

@section('title', 'Lacak Pesanan')

@section('content')
    @include('partials.modern-page-header', [
        'pageTitle' => 'Lacak Pesanan',
        'breadcrumbItems' => [
            ['label' => 'Beranda', 'url' => url('/')],
            ['label' => 'Lacak Pesanan', 'url' => null]
        ]
    ])
    
<div class="container-fluid py-5" style="background: linear-gradient(180deg, #f8f9fa 0%, #ffffff 100%);">
  <div class="container py-5">
    <div class="row">
      <div class="col-12">
      
      <div class="card mb-4">
        <div class="card-body">
          <form id="trackingForm" class="row g-3">
            <div class="col-md-8">
              <input type="text" 
                     class="form-control" 
                     id="orderNumber" 
                     placeholder="Masukkan nomor pesanan (contoh: ORD-1234567-ABCD)"
                     required>
            </div>
            <div class="col-md-4">
              <button type="submit" class="btn btn-primary w-100">
                <i class="bx bx-search"></i> Lacak
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Tracking Result -->
      <div id="trackingResult" style="display: none;">
        <!-- Order Info -->
        <div class="card mb-4">
          <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bx bx-package"></i> Informasi Pesanan</h5>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-6">
                <p><strong>Nomor Pesanan:</strong> <span id="orderNumberDisplay"></span></p>
                <p><strong>Status:</strong> <span id="orderStatus" class="badge"></span></p>
              </div>
              <div class="col-md-6">
                <p><strong>Metode Pengiriman:</strong> <span id="shippingMethod"></span></p>
                <p><strong>Alamat Tujuan:</strong> <span id="destinationAddress"></span></p>
              </div>
            </div>
          </div>
        </div>

        <!-- Map Container -->
        <div class="card mb-4">
          <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bx bx-map"></i> Peta Tracking</h5>
            <div>
              <span id="trackingStatus" class="badge bg-light text-dark"></span>
              <span id="etaDisplay" class="badge bg-warning text-dark ms-2"></span>
            </div>
          </div>
          <div class="card-body p-0">
            <div id="map" style="height: 500px; width: 100%;"></div>
          </div>
        </div>

        <!-- Tracking Timeline -->
        <div class="card">
          <div class="card-header">
            <h5 class="mb-0"><i class="bx bx-time"></i> Timeline Pengiriman</h5>
          </div>
          <div class="card-body">
            <div class="timeline" id="trackingTimeline">
              <!-- Timeline items will be inserted here -->
            </div>
          </div>
        </div>
      </div>

      <!-- Error Message -->
      <div id="errorMessage" class="alert alert-danger" style="display: none;"></div>
    </div>
  </div>
  </div>
</div>

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script>
let map;
let driverMarker;
let destinationMarker;
let routeLine;
let trackingInterval;

document.getElementById('trackingForm').addEventListener('submit', function(e) {
  e.preventDefault();
  const orderNumber = document.getElementById('orderNumber').value.trim();
  if (orderNumber) {
    loadTracking(orderNumber);
  }
});

function loadTracking(orderNumber) {
  fetch(`/api/tracking/${orderNumber}`)
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        displayTracking(data);
        startAutoRefresh(orderNumber);
      } else {
        showError(data.message || 'Pesanan tidak ditemukan atau tracking belum tersedia');
      }
    })
    .catch(error => {
      console.error('Error:', error);
      showError('Terjadi kesalahan saat memuat data tracking');
    });
}

function displayTracking(data) {
  document.getElementById('trackingResult').style.display = 'block';
  document.getElementById('errorMessage').style.display = 'none';
  
  // Update order info
  document.getElementById('orderNumberDisplay').textContent = data.order.order_number;
  document.getElementById('orderStatus').textContent = data.order.status.toUpperCase();
  document.getElementById('orderStatus').className = 'badge bg-' + getStatusColor(data.order.status);
  document.getElementById('shippingMethod').textContent = data.tracking?.driver ? 'Pengiriman Instan' : 'Standard';
  document.getElementById('destinationAddress').textContent = data.order.shipping_address?.address || '-';
  
  // Update tracking status
  document.getElementById('trackingStatus').textContent = data.tracking.status_label || 'Menunggu';
  document.getElementById('etaDisplay').textContent = data.tracking.formatted_eta || 'Menghitung...';
  
  // Initialize map
  initMap(data);
  
  // Update timeline
  updateTimeline(data.tracking);
}

function initMap(data) {
  // Destroy existing map if any
  if (map) {
    map.remove();
  }
  
  const tracking = data.tracking;
  const destination = data.order.destination;
  
  // Default center (Semarang)
  let center = [-6.2088, 106.8456];
  
  // If we have driver location, use it as center
  if (tracking?.current_location) {
    center = [tracking.current_location.lat, tracking.current_location.lng];
  } else if (destination) {
    center = [destination.lat, destination.lng];
  }
  
  // Initialize map
  map = L.map('map').setView(center, 13);
  
  // Add tile layer
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
  }).addTo(map);
  
  // Add destination marker
  if (destination) {
    destinationMarker = L.marker([destination.lat, destination.lng], {
      icon: L.icon({
        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png',
        iconSize: [25, 41],
        iconAnchor: [12, 41],
        popupAnchor: [1, -34]
      })
    }).addTo(map);
    destinationMarker.bindPopup('<b>Tujuan Pengiriman</b><br>' + (destination.address || ''));
  }
  
  // Add driver marker if available
  if (tracking?.current_location) {
    driverMarker = L.marker([tracking.current_location.lat, tracking.current_location.lng], {
      icon: L.icon({
        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-green.png',
        iconSize: [25, 41],
        iconAnchor: [12, 41],
        popupAnchor: [1, -34]
      })
    }).addTo(map);
    
    const driverInfo = tracking.driver ? 
      `<b>Kurir: ${tracking.driver.name}</b><br>${tracking.current_location.address || ''}` :
      'Lokasi Kurir';
    driverMarker.bindPopup(driverInfo);
    
    // Draw route if we have both points
    if (destination) {
      drawRoute(
        [tracking.current_location.lat, tracking.current_location.lng],
        [destination.lat, destination.lng]
      );
    }
  }
  
  // Fit bounds to show both markers
  if (driverMarker && destinationMarker) {
    const group = new L.featureGroup([driverMarker, destinationMarker]);
    map.fitBounds(group.getBounds().pad(0.1));
  }
}

function drawRoute(start, end) {
  // Remove existing route
  if (routeLine) {
    map.removeLayer(routeLine);
  }
  
  // Draw simple straight line (can be upgraded to use routing API)
  routeLine = L.polyline([start, end], {
    color: '#147440',
    weight: 4,
    opacity: 0.7,
    dashArray: '10, 10'
  }).addTo(map);
}

function updateTimeline(tracking) {
  const timeline = document.getElementById('trackingTimeline');
  timeline.innerHTML = '';
  
  const steps = [
    { key: 'picked_at', label: 'Pesanan Diambil', icon: 'bx-package', status: tracking.status === 'picked' || tracking.status === 'on_the_way' || tracking.status === 'arrived' || tracking.status === 'delivered' },
    { key: 'on_the_way_at', label: 'Dalam Perjalanan', icon: 'bx-truck', status: tracking.status === 'on_the_way' || tracking.status === 'arrived' || tracking.status === 'delivered' },
    { key: 'arrived_at', label: 'Sudah Sampai', icon: 'bx-check-circle', status: tracking.status === 'arrived' || tracking.status === 'delivered' },
  ];
  
  steps.forEach((step, index) => {
    const item = document.createElement('div');
    item.className = 'timeline-item ' + (step.status ? 'completed' : 'pending');
    item.innerHTML = `
      <div class="timeline-marker">
        <i class="bx ${step.icon}"></i>
      </div>
      <div class="timeline-content">
        <h6>${step.label}</h6>
        <p>${tracking[step.key] ? new Date(tracking[step.key]).toLocaleString('id-ID') : 'Menunggu...'}</p>
      </div>
    `;
    timeline.appendChild(item);
  });
}

function startAutoRefresh(orderNumber) {
  // Clear existing interval
  if (trackingInterval) {
    clearInterval(trackingInterval);
  }
  
  // Refresh every 5 seconds
  trackingInterval = setInterval(() => {
    loadTracking(orderNumber);
  }, 5000);
}

function showError(message) {
  document.getElementById('errorMessage').textContent = message;
  document.getElementById('errorMessage').style.display = 'block';
  document.getElementById('trackingResult').style.display = 'none';
}

function getStatusColor(status) {
  const colors = {
    'pending': 'warning',
    'paid': 'success',
    'processing': 'info',
    'shipped': 'primary',
    'delivered': 'success',
    'completed': 'success'
  };
  return colors[status] || 'secondary';
}
</script>

<style>
.timeline {
  position: relative;
  padding-left: 30px;
}

.timeline-item {
  position: relative;
  padding-bottom: 30px;
  padding-left: 40px;
}

.timeline-item::before {
  content: '';
  position: absolute;
  left: 15px;
  top: 30px;
  bottom: -10px;
  width: 2px;
  background: #e0e0e0;
}

.timeline-item:last-child::before {
  display: none;
}

.timeline-item.completed::before {
  background: #147440;
}

.timeline-marker {
  position: absolute;
  left: 0;
  top: 0;
  width: 30px;
  height: 30px;
  border-radius: 50%;
  background: #e0e0e0;
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1;
}

.timeline-item.completed .timeline-marker {
  background: #147440;
  color: white;
}

.timeline-item.pending .timeline-marker {
  background: #e0e0e0;
  color: #999;
}
</style>
@endpush
@endsection
