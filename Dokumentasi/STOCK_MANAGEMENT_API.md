# API Documentation - Stock Management System

## Base URL
```
/admin (untuk admin endpoints)
/cart (untuk frontend endpoints)
```

---

## Admin Endpoints

### 1. Get Stock Movements List

**Endpoint:** `GET /admin/stock-movements`

**Description:** Menampilkan halaman daftar riwayat perubahan stok.

**Query Parameters:**
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| product_id | integer | No | Filter by product ID |
| type | string | No | Filter by type (in, out, adjustment, restore) |

**Response:** HTML page

---

### 2. Get Stock Movements Data (DataTables)

**Endpoint:** `GET /admin/stock-movements/data`

**Description:** Mengembalikan data JSON untuk DataTables.

**Query Parameters:**
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| product_id | integer | No | Filter by product ID |
| type | string | No | Filter by type |
| draw | integer | Yes | DataTables draw counter |
| start | integer | Yes | DataTables start position |
| length | integer | Yes | DataTables page length |

**Response:**
```json
{
  "draw": 1,
  "recordsTotal": 100,
  "recordsFiltered": 50,
  "data": [
    {
      "DT_RowIndex": 1,
      "created_at": "11/12/2025 14:30",
      "product": {
        "name": "Produk A"
      },
      "type": "<span class='badge bg-danger'>Stock Keluar</span>",
      "quantity": "<span class='text-danger'>-5</span>",
      "old_stock": "100",
      "new_stock": "<strong>95</strong>",
      "reference_number": "<a href='...'>ORD-12345</a>",
      "user": {
        "name": "Admin"
      },
      "notes_display": "<small class='text-muted'>Stock keluar untuk order #ORD-12345</small>"
    }
  ]
}
```

---

### 3. Get Stock History per Product

**Endpoint:** `GET /admin/products/{product}/stock-history`

**Description:** Menampilkan riwayat stok untuk produk tertentu.

**URL Parameters:**
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| product | string | Yes | Encoded product ID |

**Response:** HTML page

---

### 4. Export Stock History

**Endpoint:** `GET /admin/stock-movements/export`

**Description:** Export riwayat perubahan stok ke CSV.

**Query Parameters:**
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| product_id | integer | No | Filter by product ID |
| type | string | No | Filter by type |
| start_date | date | No | Filter by start date (Y-m-d) |
| end_date | date | No | Filter by end date (Y-m-d) |

**Response:** CSV file download

**CSV Format:**
```csv
Tanggal,Waktu,Produk,SKU,Tipe,Jumlah,Stok Lama,Stok Baru,Referensi,User,Catatan
2025-12-11,14:30:00,Produk A,SKU-001,Stock Keluar,-5,100,95,ORD-12345,Admin,Stock keluar untuk order #ORD-12345
```

---

### 5. Export Stock Summary

**Endpoint:** `GET /admin/stock-movements/export-summary`

**Description:** Export ringkasan stok semua produk ke CSV.

**Response:** CSV file download

**CSV Format:**
```csv
Produk,SKU,Kategori,Store,Stok Tersedia,Unit,Harga,Status Stok
Produk A,SKU-001,Kategori A,Store 1,95,pcs,50000,Tersedia
Produk B,SKU-002,Kategori B,Store 1,5,pcs,75000,Terbatas
```

---

### 6. Adjust Stock

**Endpoint:** `POST /admin/products/{product}/adjust-stock`

**Description:** Sesuaikan stok produk secara manual.

**URL Parameters:**
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| product | string | Yes | Encoded product ID |

**Request Body:**
```json
{
  "adjustment_type": "set|increase|decrease",
  "quantity": 10,
  "notes": "Catatan penyesuaian (optional)"
}
```

**Request Headers:**
```
Content-Type: application/json
X-CSRF-TOKEN: {csrf_token}
```

**Response (Success):**
```json
{
  "success": true,
  "message": "Stok berhasil disesuaikan",
  "old_stock": 50,
  "new_stock": 60
}
```

**Response (Error):**
```json
{
  "success": false,
  "message": "Validation error",
  "errors": {
    "adjustment_type": ["The adjustment type field is required."],
    "quantity": ["The quantity must be at least 1."]
  }
}
```

**Validation Rules:**
- `adjustment_type`: required, in:set,increase,decrease
- `quantity`: required, integer, min:1
- `notes`: optional, string, max:500

---

## Frontend Endpoints

### 7. Check Stock Availability

**Endpoint:** `GET /cart/check-stock`

**Description:** Cek ketersediaan stok untuk produk (AJAX).

**Query Parameters:**
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| product_id | integer | Yes | Product ID |
| quantity | integer | Yes | Quantity yang diminta (min: 1) |

**Response (Success):**
```json
{
  "available": true,
  "stock": 100,
  "requested": 5,
  "current_cart_qty": 2,
  "max_can_add": 98,
  "message": "Stok tersedia",
  "is_out_of_stock": false,
  "is_low_stock": false
}
```

**Response (Out of Stock):**
```json
{
  "available": false,
  "stock": 0,
  "requested": 5,
  "current_cart_qty": 0,
  "max_can_add": 0,
  "message": "Stok habis",
  "is_out_of_stock": true,
  "is_low_stock": false
}
```

**Response (Insufficient Stock):**
```json
{
  "available": false,
  "stock": 3,
  "requested": 5,
  "current_cart_qty": 0,
  "max_can_add": 3,
  "message": "Stok tidak mencukupi. Stok tersedia: 3, maksimal yang bisa ditambahkan: 3",
  "is_out_of_stock": false,
  "is_low_stock": true
}
```

**Response (Product Not Found):**
```json
{
  "available": false,
  "message": "Produk tidak ditemukan",
  "stock": 0
}
```
Status Code: 404

---

## Error Codes

| Status Code | Description |
|-------------|-------------|
| 200 | Success |
| 400 | Bad Request (validation error) |
| 404 | Not Found (product/route not found) |
| 422 | Unprocessable Entity (validation failed) |
| 500 | Internal Server Error |

---

## Authentication

### Admin Endpoints
- Requires admin authentication
- CSRF token required for POST requests
- Session-based authentication

### Frontend Endpoints
- Public access (no authentication required)
- CSRF token required for POST requests

---

## Rate Limiting

Currently no rate limiting applied. Consider implementing for production.

---

## Example Usage

### JavaScript (AJAX)

```javascript
// Check stock availability
fetch('/cart/check-stock?product_id=1&quantity=5')
  .then(response => response.json())
  .then(data => {
    if (data.available) {
      console.log('Stock available:', data.stock);
    } else {
      console.log('Stock unavailable:', data.message);
    }
  });

// Adjust stock
fetch('/admin/products/encoded_id/adjust-stock', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
  },
  body: JSON.stringify({
    adjustment_type: 'increase',
    quantity: 10,
    notes: 'Stock masuk dari supplier'
  })
})
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      console.log('Stock adjusted:', data.new_stock);
    }
  });
```

### cURL

```bash
# Check stock
curl "http://localhost:8000/cart/check-stock?product_id=1&quantity=5"

# Adjust stock
curl -X POST "http://localhost:8000/admin/products/encoded_id/adjust-stock" \
  -H "Content-Type: application/json" \
  -H "X-CSRF-TOKEN: {token}" \
  -d '{
    "adjustment_type": "increase",
    "quantity": 10,
    "notes": "Stock masuk dari supplier"
  }'
```

---

**Last Updated**: 2025-12-11
