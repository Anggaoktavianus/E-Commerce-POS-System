# CONTOH IMPLEMENTASI POS & KASIR

## 1. Migration Files

### Migration: create_pos_shifts_table.php

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pos_shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('outlet_id')->constrained('outlets')->onDelete('restrict');
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
            $table->date('shift_date');
            $table->tinyInteger('shift_number')->comment('1=pagi, 2=siang, 3=malam');
            $table->decimal('opening_balance', 15, 2)->default(0);
            $table->decimal('closing_balance', 15, 2)->nullable();
            $table->decimal('expected_cash', 15, 2)->nullable();
            $table->decimal('actual_cash', 15, 2)->nullable();
            $table->decimal('variance', 15, 2)->nullable();
            $table->decimal('total_sales', 15, 2)->default(0);
            $table->integer('total_transactions')->default(0);
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->enum('status', ['open', 'closed', 'pending'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['outlet_id', 'shift_date']);
            $table->index('user_id');
            $table->index('status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('pos_shifts');
    }
};
```

### Migration: create_pos_transactions_table.php

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pos_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_number', 50)->unique();
            $table->foreignId('outlet_id')->constrained('outlets')->onDelete('restrict');
            $table->foreignId('shift_id')->constrained('pos_shifts')->onDelete('restrict');
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('customer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2);
            $table->enum('payment_method', ['cash', 'card', 'ewallet', 'qris', 'split']);
            $table->json('payment_details')->nullable();
            $table->decimal('cash_received', 15, 2)->nullable();
            $table->decimal('change_amount', 15, 2)->nullable();
            $table->enum('status', ['completed', 'cancelled', 'refunded'])->default('completed');
            $table->timestamp('cancelled_at')->nullable();
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('cancel_reason')->nullable();
            $table->boolean('receipt_printed')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['outlet_id', 'created_at']);
            $table->index('shift_id');
            $table->index('transaction_number');
            $table->index('status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('pos_transactions');
    }
};
```

## 2. Model Examples

### Model: PosShift.php

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PosShift extends Model
{
    use HasFactory;

    protected $fillable = [
        'outlet_id',
        'user_id',
        'shift_date',
        'shift_number',
        'opening_balance',
        'closing_balance',
        'expected_cash',
        'actual_cash',
        'variance',
        'total_sales',
        'total_transactions',
        'opened_at',
        'closed_at',
        'status',
        'notes',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'closing_balance' => 'decimal:2',
        'expected_cash' => 'decimal:2',
        'actual_cash' => 'decimal:2',
        'variance' => 'decimal:2',
        'total_sales' => 'decimal:2',
        'shift_date' => 'date',
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    // Relationships
    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(PosTransaction::class);
    }

    public function cashMovements()
    {
        return $this->hasMany(PosCashMovement::class);
    }

    // Scopes
    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    public function scopeForOutlet($query, $outletId)
    {
        return $query->where('outlet_id', $outletId);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Methods
    public function isOpen()
    {
        return $this->status === 'open';
    }

    public function canClose()
    {
        return $this->isOpen() && $this->transactions()->count() > 0;
    }

    public function calculateExpectedCash()
    {
        $cashSales = $this->transactions()
            ->where('payment_method', 'cash')
            ->where('status', 'completed')
            ->sum('total_amount');

        $deposits = $this->cashMovements()
            ->where('type', 'deposit')
            ->sum('amount');

        $withdrawals = $this->cashMovements()
            ->where('type', 'withdrawal')
            ->sum('amount');

        $transfers = $this->cashMovements()
            ->where('type', 'transfer')
            ->sum('amount');

        return $this->opening_balance + $cashSales + $deposits - $withdrawals - $transfers;
    }
}
```

### Model: PosTransaction.php

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PosTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_number',
        'outlet_id',
        'shift_id',
        'user_id',
        'customer_id',
        'subtotal',
        'discount_amount',
        'tax_amount',
        'total_amount',
        'payment_method',
        'payment_details',
        'cash_received',
        'change_amount',
        'status',
        'cancelled_at',
        'cancelled_by',
        'cancel_reason',
        'receipt_printed',
        'notes',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'cash_received' => 'decimal:2',
        'change_amount' => 'decimal:2',
        'payment_details' => 'array',
        'cancelled_at' => 'datetime',
        'receipt_printed' => 'boolean',
    ];

    // Relationships
    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    public function shift()
    {
        return $this->belongsTo(PosShift::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function items()
    {
        return $this->hasMany(PosTransactionItem::class);
    }

    public function payments()
    {
        return $this->hasMany(PosPayment::class);
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    // Methods
    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function canCancel()
    {
        // Bisa di-cancel jika:
        // 1. Status masih completed
        // 2. Shift masih open
        // 3. Belum lebih dari 24 jam
        return $this->isCompleted() 
            && $this->shift->isOpen()
            && $this->created_at->diffInHours(now()) < 24;
    }

    public function generateTransactionNumber()
    {
        $outlet = $this->outlet;
        $date = now()->format('Ymd');
        $count = self::where('outlet_id', $outlet->id)
            ->whereDate('created_at', today())
            ->count() + 1;

        return "POS-{$outlet->code}-{$date}-" . str_pad($count, 4, '0', STR_PAD_LEFT);
    }
}
```

## 3. Service Examples

### Service: PosService.php

```php
<?php

namespace App\Services;

use App\Models\PosTransaction;
use App\Models\PosTransactionItem;
use App\Models\PosShift;
use App\Models\OutletProductInventory;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class PosService
{
    public function createTransaction(array $data)
    {
        return DB::transaction(function () use ($data) {
            // Validate shift is open
            $shift = PosShift::findOrFail($data['shift_id']);
            if (!$shift->isOpen()) {
                throw new \Exception('Shift is not open');
            }

            // Create transaction
            $transaction = PosTransaction::create([
                'transaction_number' => $this->generateTransactionNumber($data['outlet_id']),
                'outlet_id' => $data['outlet_id'],
                'shift_id' => $data['shift_id'],
                'user_id' => $data['user_id'],
                'customer_id' => $data['customer_id'] ?? null,
                'subtotal' => $data['subtotal'],
                'discount_amount' => $data['discount_amount'] ?? 0,
                'tax_amount' => $data['tax_amount'] ?? 0,
                'total_amount' => $data['total_amount'],
                'payment_method' => $data['payment_method'],
                'payment_details' => $data['payment_details'] ?? null,
                'cash_received' => $data['cash_received'] ?? null,
                'change_amount' => $data['change_amount'] ?? null,
                'status' => 'completed',
            ]);

            // Create transaction items and update inventory
            foreach ($data['items'] as $item) {
                $this->createTransactionItem($transaction, $item);
            }

            // Update shift totals
            $this->updateShiftTotals($shift);

            return $transaction->load('items', 'customer');
        });
    }

    protected function createTransactionItem(PosTransaction $transaction, array $item)
    {
        $product = \App\Models\Product::findOrFail($item['product_id']);
        $outletId = $transaction->outlet_id;

        // Get current stock
        $inventory = OutletProductInventory::where('outlet_id', $outletId)
            ->where('product_id', $item['product_id'])
            ->firstOrFail();

        $stockBefore = $inventory->stock;

        // Validate stock
        if ($inventory->stock < $item['quantity']) {
            throw new \Exception("Insufficient stock for product: {$product->name}");
        }

        // Update inventory
        $inventory->stock -= $item['quantity'];
        $inventory->save();

        $stockAfter = $inventory->stock;

        // Create transaction item
        $transactionItem = PosTransactionItem::create([
            'transaction_id' => $transaction->id,
            'product_id' => $item['product_id'],
            'product_name' => $product->name,
            'product_sku' => $product->sku,
            'quantity' => $item['quantity'],
            'unit_price' => $item['unit_price'],
            'discount_amount' => $item['discount_amount'] ?? 0,
            'tax_amount' => $item['tax_amount'] ?? 0,
            'total_amount' => $item['total_amount'],
            'stock_before' => $stockBefore,
            'stock_after' => $stockAfter,
        ]);

        // Create stock movement
        StockMovement::create([
            'product_id' => $item['product_id'],
            'outlet_id' => $outletId,
            'type' => 'pos_sale',
            'quantity' => -$item['quantity'],
            'reference_type' => 'pos_transaction',
            'reference_id' => $transaction->id,
            'notes' => "POS Sale - Transaction #{$transaction->transaction_number}",
        ]);

        return $transactionItem;
    }

    protected function updateShiftTotals(PosShift $shift)
    {
        $shift->total_sales = $shift->transactions()
            ->where('status', 'completed')
            ->sum('total_amount');
        
        $shift->total_transactions = $shift->transactions()
            ->where('status', 'completed')
            ->count();
        
        $shift->save();
    }

    public function cancelTransaction($transactionId, $userId, $reason)
    {
        return DB::transaction(function () use ($transactionId, $userId, $reason) {
            $transaction = PosTransaction::findOrFail($transactionId);

            if (!$transaction->canCancel()) {
                throw new \Exception('Transaction cannot be cancelled');
            }

            // Restore inventory for each item
            foreach ($transaction->items as $item) {
                $inventory = OutletProductInventory::where('outlet_id', $transaction->outlet_id)
                    ->where('product_id', $item->product_id)
                    ->first();

                if ($inventory) {
                    $inventory->stock += $item->quantity;
                    $inventory->save();
                }

                // Create stock movement (return)
                StockMovement::create([
                    'product_id' => $item->product_id,
                    'outlet_id' => $transaction->outlet_id,
                    'type' => 'pos_return',
                    'quantity' => $item->quantity,
                    'reference_type' => 'pos_transaction',
                    'reference_id' => $transaction->id,
                    'notes' => "POS Return - Transaction #{$transaction->transaction_number}",
                ]);
            }

            // Update transaction
            $transaction->status = 'cancelled';
            $transaction->cancelled_at = now();
            $transaction->cancelled_by = $userId;
            $transaction->cancel_reason = $reason;
            $transaction->save();

            // Update shift totals
            $this->updateShiftTotals($transaction->shift);

            return $transaction;
        });
    }

    protected function generateTransactionNumber($outletId)
    {
        $outlet = \App\Models\Outlet::findOrFail($outletId);
        $date = now()->format('Ymd');
        $count = PosTransaction::where('outlet_id', $outletId)
            ->whereDate('created_at', today())
            ->count() + 1;

        return "POS-{$outlet->code}-{$date}-" . str_pad($count, 4, '0', STR_PAD_LEFT);
    }
}
```

## 4. Controller Examples

### Controller: PosTransactionController.php

```php
<?php

namespace App\Http\Controllers\Admin\Pos;

use App\Http\Controllers\Controller;
use App\Services\PosService;
use App\Http\Requests\Pos\StoreTransactionRequest;
use Illuminate\Http\Request;

class PosTransactionController extends Controller
{
    protected $posService;

    public function __construct(PosService $posService)
    {
        $this->posService = $posService;
    }

    public function index(Request $request)
    {
        $query = \App\Models\PosTransaction::with(['outlet', 'user', 'customer', 'items']);

        // Filters
        if ($request->filled('outlet_id')) {
            $query->where('outlet_id', $request->outlet_id);
        }

        if ($request->filled('shift_id')) {
            $query->where('shift_id', $request->shift_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $transactions = $query->latest()->paginate(20);

        return view('admin.pos.transactions.index', compact('transactions'));
    }

    public function store(StoreTransactionRequest $request)
    {
        try {
            $data = $request->validated();
            $data['user_id'] = auth()->id();

            $transaction = $this->posService->createTransaction($data);

            return response()->json([
                'success' => true,
                'message' => 'Transaction created successfully',
                'data' => $transaction
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function show($id)
    {
        $transaction = \App\Models\PosTransaction::with([
            'outlet',
            'shift',
            'user',
            'customer',
            'items.product',
            'payments'
        ])->findOrFail($id);

        return view('admin.pos.transactions.show', compact('transaction'));
    }

    public function cancel(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        try {
            $transaction = $this->posService->cancelTransaction(
                $id,
                auth()->id(),
                $request->reason
            );

            return response()->json([
                'success' => true,
                'message' => 'Transaction cancelled successfully',
                'data' => $transaction
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
```

## 5. Request Validation Examples

### Request: StoreTransactionRequest.php

```php
<?php

namespace App\Http\Requests\Pos;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check() && auth()->user()->can('pos.transaction');
    }

    public function rules()
    {
        return [
            'outlet_id' => 'required|exists:outlets,id',
            'shift_id' => 'required|exists:pos_shifts,id',
            'customer_id' => 'nullable|exists:users,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount_amount' => 'nullable|numeric|min:0',
            'items.*.tax_amount' => 'nullable|numeric|min:0',
            'items.*.total_amount' => 'required|numeric|min:0',
            'subtotal' => 'required|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,card,ewallet,qris,split',
            'payment_details' => 'nullable|array',
            'cash_received' => 'required_if:payment_method,cash|numeric|min:0',
            'change_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ];
    }

    public function messages()
    {
        return [
            'items.required' => 'Transaction must have at least one item',
            'items.min' => 'Transaction must have at least one item',
            'cash_received.required_if' => 'Cash received is required for cash payment',
        ];
    }
}
```

## 6. Route Examples

```php
// routes/web.php

Route::middleware(['auth', 'admin'])->prefix('admin/pos')->name('admin.pos.')->group(function() {
    // Dashboard
    Route::get('/', [PosDashboardController::class, 'index'])->name('dashboard');
    
    // Shifts
    Route::get('shifts', [PosShiftController::class, 'index'])->name('shifts.index');
    Route::get('shifts/current', [PosShiftController::class, 'current'])->name('shifts.current');
    Route::post('shifts/open', [PosShiftController::class, 'open'])->name('shifts.open');
    Route::post('shifts/{id}/close', [PosShiftController::class, 'close'])->name('shifts.close');
    Route::get('shifts/{id}/report', [PosShiftController::class, 'report'])->name('shifts.report');
    
    // Transactions
    Route::get('transactions', [PosTransactionController::class, 'index'])->name('transactions.index');
    Route::post('transactions', [PosTransactionController::class, 'store'])->name('transactions.store');
    Route::get('transactions/{id}', [PosTransactionController::class, 'show'])->name('transactions.show');
    Route::post('transactions/{id}/cancel', [PosTransactionController::class, 'cancel'])->name('transactions.cancel');
    Route::get('transactions/{id}/receipt', [PosReceiptController::class, 'show'])->name('transactions.receipt');
    
    // Products
    Route::get('products/search', [PosProductController::class, 'search'])->name('products.search');
    Route::get('products/barcode/{code}', [PosProductController::class, 'byBarcode'])->name('products.barcode');
    
    // Reports
    Route::get('reports/daily', [PosReportController::class, 'daily'])->name('reports.daily');
    Route::get('reports/shift', [PosReportController::class, 'shift'])->name('reports.shift');
});
```

---

**Catatan:** Contoh-contoh di atas adalah implementasi dasar. Untuk production, perlu ditambahkan:
- Error handling yang lebih robust
- Logging
- Caching untuk performa
- Unit tests
- API documentation
