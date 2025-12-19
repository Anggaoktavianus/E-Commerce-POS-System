# DIAGRAM DATABASE POS & KASIR

## Entity Relationship Diagram (ERD)

```
┌─────────────────┐
│     stores      │
├─────────────────┤
│ id              │◄────┐
│ name            │     │
│ code            │     │
│ ...             │     │
└─────────────────┘     │
                        │
┌─────────────────┐     │
│    outlets      │     │
├─────────────────┤     │
│ id              │     │
│ store_id        │─────┘
│ name            │
│ code            │
│ ...             │
└─────────────────┘
        │
        │ 1:N
        │
        ▼
┌─────────────────┐
│   pos_shifts    │
├─────────────────┤
│ id              │
│ outlet_id       │─────┐
│ user_id         │     │
│ shift_date      │     │
│ opening_balance │     │
│ closing_balance │     │
│ status          │     │
│ ...             │     │
└─────────────────┘     │
        │               │
        │ 1:N           │
        │               │
        ▼               │
┌─────────────────┐     │
│pos_transactions │     │
├─────────────────┤     │
│ id              │     │
│ transaction_num │     │
│ outlet_id      │─────┘
│ shift_id       │─────┐
│ user_id        │     │
│ customer_id    │     │
│ total_amount   │     │
│ payment_method │     │
│ ...            │     │
└─────────────────┘     │
        │               │
        │ 1:N           │
        │               │
        ▼               │
┌─────────────────┐     │
│pos_transaction_ │     │
│    items        │     │
├─────────────────┤     │
│ id              │     │
│ transaction_id  │─────┘
│ product_id      │─────┐
│ quantity        │     │
│ unit_price      │     │
│ total_amount    │     │
│ stock_before    │     │
│ stock_after     │     │
│ ...             │     │
└─────────────────┘     │
                        │
┌─────────────────┐     │
│    products     │     │
├─────────────────┤     │
│ id              │◄────┘
│ name            │
│ sku             │
│ price           │
│ ...             │
└─────────────────┘
        │
        │ N:M
        │
        ▼
┌─────────────────┐
│outlet_product_ │
│  inventories   │
├─────────────────┤
│ outlet_id      │
│ product_id     │
│ stock          │
│ price_override │
│ ...            │
└─────────────────┘

┌─────────────────┐
│ pos_payments    │
├─────────────────┤
│ id              │
│ transaction_id  │─────┐
│ payment_method  │     │
│ amount          │     │
│ ...             │     │
└─────────────────┘     │
                        │
                        │
┌─────────────────┐     │
│pos_cash_movements│    │
├─────────────────┤     │
│ id              │     │
│ shift_id        │─────┘
│ outlet_id       │
│ type            │
│ amount          │
│ ...             │
└─────────────────┘

┌─────────────────┐
│     users       │
├─────────────────┤
│ id              │
│ name            │
│ role            │
│ ...             │
└─────────────────┘
        │
        │ 1:N (kasir)
        │
        ▼
┌─────────────────┐
│   pos_shifts    │
└─────────────────┘
```

## Relasi Detail

### 1. **Outlet → POS Shifts**
- Satu outlet bisa punya banyak shifts
- Setiap shift hanya untuk satu outlet

### 2. **Shift → Transactions**
- Satu shift bisa punya banyak transactions
- Setiap transaction hanya dalam satu shift
- Transaction tidak bisa dibuat jika shift belum dibuka

### 3. **Transaction → Transaction Items**
- Satu transaction punya banyak items
- Setiap item hanya untuk satu transaction
- Cascade delete: jika transaction dihapus, items juga terhapus

### 4. **Transaction → Payments**
- Satu transaction bisa punya banyak payments (split payment)
- Setiap payment hanya untuk satu transaction
- Cascade delete

### 5. **Product → Transaction Items**
- Satu product bisa ada di banyak transaction items
- Setiap item hanya untuk satu product

### 6. **User (Kasir) → Shifts**
- Satu user bisa buka banyak shifts
- Setiap shift hanya dibuka oleh satu user

### 7. **User (Customer) → Transactions**
- Satu customer bisa punya banyak transactions (optional)
- Setiap transaction bisa punya satu customer (optional)

## Indexes yang Disarankan

```sql
-- pos_shifts
INDEX idx_outlet_date (outlet_id, shift_date)
INDEX idx_user (user_id)
INDEX idx_status (status)

-- pos_transactions
INDEX idx_outlet_date (outlet_id, created_at)
INDEX idx_shift (shift_id)
INDEX idx_transaction_number (transaction_number)
INDEX idx_status (status)
INDEX idx_customer (customer_id)

-- pos_transaction_items
INDEX idx_transaction (transaction_id)
INDEX idx_product (product_id)

-- pos_payments
INDEX idx_transaction (transaction_id)

-- pos_cash_movements
INDEX idx_shift (shift_id)
INDEX idx_type (type)
```

## Constraints

### Foreign Keys
- Semua foreign keys menggunakan `ON DELETE RESTRICT` untuk data integrity
- Kecuali transaction_items dan payments menggunakan `ON DELETE CASCADE`

### Unique Constraints
- `pos_transactions.transaction_number` - UNIQUE
- `pos_settings(outlet_id, setting_key)` - UNIQUE

### Check Constraints
- `pos_shifts.opening_balance >= 0`
- `pos_transactions.total_amount > 0`
- `pos_transaction_items.quantity > 0`
- `pos_payments.amount > 0`

## Data Flow

### Transaction Flow
```
1. User opens shift → pos_shifts (status='open')
2. User creates transaction → pos_transactions
3. User adds items → pos_transaction_items
4. User processes payment → pos_payments (if split)
5. System updates inventory → outlet_product_inventories
6. System creates stock movement → stock_movements
7. User closes shift → pos_shifts (status='closed')
```

### Cash Flow
```
Opening Balance (pos_shifts.opening_balance)
    +
Cash Sales (sum of cash payments in transactions)
    +
Deposits (pos_cash_movements type='deposit')
    -
Withdrawals (pos_cash_movements type='withdrawal')
    -
Transfers (pos_cash_movements type='transfer')
    =
Expected Cash (pos_shifts.expected_cash)

Actual Cash Count (pos_shifts.actual_cash)
    -
Expected Cash
    =
Variance (pos_shifts.variance)
```
