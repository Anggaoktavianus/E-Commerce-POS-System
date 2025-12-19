<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductRequest;
use App\Helpers\CacheHelper;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class ProductController extends Controller
{
    public function index()
    {
        $categories = DB::table('categories')->orderBy('name')->get();
        $stores = DB::table('stores')->where('is_active', true)->orderBy('name')->get();
        return view('admin.products.index', compact('categories','stores'));
    }

    public function data(Request $request)
    {
        $requestedStoreId = $request->get('store_id');
        $storeId = $requestedStoreId ?: (app()->has('current_store') ? app('current_store')->id : 1);
        $categoryId = $request->get('category_id');
        $featured = $request->get('featured'); // '1'|'0'|null

        $query = DB::table('products')
            ->leftJoin('product_categories', 'products.id', '=', 'product_categories.product_id')
            ->leftJoin('categories', 'product_categories.category_id', '=', 'categories.id')
            ->leftJoin('stores', 'products.store_id', '=', 'stores.id')
            ->select(
                'products.id',
                'products.name',
                'products.slug',
                'products.price',
                'products.unit',
                'products.stock_qty',
                'products.store_id',
                'products.is_active',
                'products.is_featured',
                'products.is_bestseller',
                'products.created_at',
                'stores.name as store_name',
                'stores.code as store_code',
                DB::raw('GROUP_CONCAT(categories.name SEPARATOR ", ") as categories')
            )
            ->where('products.store_id', $storeId)
            ->groupBy('products.id','products.name','products.slug','products.price','products.unit','products.stock_qty','products.store_id','products.is_active','products.is_featured','products.is_bestseller','products.created_at','stores.name','stores.code');

        if (!empty($categoryId)) {
            $query->where('product_categories.category_id', (int)$categoryId);
        }
        if ($featured === '1') {
            $query->where('products.is_featured', true);
        } elseif ($featured === '0') {
            $query->where(function($q){ $q->whereNull('products.is_featured')->orWhere('products.is_featured', false); });
        }

        return DataTables::of($query) 
            ->addIndexColumn() // This adds DT_RowIndex
            ->editColumn('stock_qty', function($row) {
                $stockQty = $row->stock_qty ?? 0;
                $unit = $row->unit ?? 'pcs';
                $badgeClass = $stockQty <= 0 ? 'bg-danger' : ($stockQty <= 10 ? 'bg-warning text-dark' : 'bg-success');
                return '<span class="badge ' . $badgeClass . '">' . number_format($stockQty, 0, ',', '.') . ' ' . $unit . '</span>';
            })
            ->editColumn('store_name', function($row) {
                $storeName = $row->store_name ?? 'N/A';
                $storeCode = $row->store_code ?? '';
                
                $display = $storeName;
                if ($storeCode) {
                    $display .= ' <small class="text-muted">(' . $storeCode . ')</small>';
                }
                return $display;
            })
            ->editColumn('is_active', fn($row) => $row->is_active ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-secondary">Non-Aktif</span>')
            ->editColumn('is_featured', fn($row) => $row->is_featured ? '<span class="badge bg-info">Unggulan</span>' : '')
            ->editColumn('is_bestseller', fn($row) => $row->is_bestseller ? '<span class="badge bg-warning text-dark">Terlaris</span>' : '')
            ->addColumn('actions', function($row){
                $encodedId = encode_id($row->id);
                $edit = route('admin.products.edit', $encodedId);
                $del = route('admin.products.destroy', $encodedId);
                return view('admin.products.partials.actions', compact('edit','del','row'))->render();
            })
            ->rawColumns(['stock_qty','store_name','is_active','is_featured','is_bestseller','actions'])
            ->make(true);
    }

    public function create()
    {
        $product = null;
        $categories = DB::table('categories')->where('is_active', true)->orderBy('name')->get();
        $stores = DB::table('stores')->where('is_active', true)->orderBy('name')->get();
        return view('admin.products.form', compact('product','categories','stores'));
    }

    public function store(ProductRequest $request)
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_bestseller'] = $request->boolean('is_bestseller');
        if ($request->hasFile('main_image')) {
            $data['main_image_path'] = $this->storeResizedImage($request->file('main_image'));
        }
        // additional gallery images
        $extraImages = $request->file('images', []);
        // do not persist raw uploaded file fields
        unset($data['main_image']);
        unset($data['images']);
        $category_ids = $data['category_ids'] ?? [];
        unset($data['category_ids']);
        $gallery = $request->input('gallery', []);

        $id = DB::table('products')->insertGetId(array_merge($data,[ 'created_at'=>now(),'updated_at'=>now() ]));
        // persist image into product_images table as well
        if (!empty($data['main_image_path'])) {
            DB::table('product_images')->insert([
                'product_id' => $id,
                'image_path' => $data['main_image_path'],
                'sort_order' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        // save additional images (sort_order mulai 1)
        if ($extraImages) {
            $maxOrder = (int) DB::table('product_images')->where('product_id', $id)->max('sort_order');
            $order = $maxOrder + 1;
            foreach ($extraImages as $img) {
                $path = $this->storeResizedImage($img);
                DB::table('product_images')->insert([
                    'product_id' => $id,
                    'image_path' => $path,
                    'sort_order' => $order++,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
        $this->syncCategories($id, $category_ids);
        $this->flushProductsCache();
        return redirect()->route('admin.products.index')->with('success','Product created');
    }

    public function edit($id)
    {
        $storeId = app()->has('current_store') ? app('current_store')->id : 1;
        $decodedId = decode_id((string) $id);
        abort_if(!$decodedId, 404);
        $product = DB::table('products')->where('id',$decodedId)->where('store_id',$storeId)->first();
        abort_if(!$product,404);
        $categories = DB::table('categories')->where('is_active', true)->orderBy('name')->get();
        $selected = DB::table('product_categories')->where('product_id',$decodedId)->pluck('category_id')->toArray();
        $stores = DB::table('stores')->where('is_active', true)->orderBy('name')->get();
        return view('admin.products.form', compact('product','categories','selected','stores'));
    }

    public function update(ProductRequest $request, $id)
    {
        $storeId = app()->has('current_store') ? app('current_store')->id : 1;
        $decodedId = decode_id((string) $id);
        abort_if(!$decodedId, 404);
        $p = DB::table('products')->where('id',$decodedId)->where('store_id',$storeId)->first();
        abort_if(!$p,404);
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_bestseller'] = $request->boolean('is_bestseller');
        if ($request->hasFile('main_image')) {
            // delete old product_images entry that matches previous main image
            if (!empty($p->main_image_path)) {
                DB::table('product_images')
                    ->where('product_id', $decodedId)
                    ->where('image_path', $p->main_image_path)
                    ->delete();
            }
            // delete old main image file if exists
            if (!empty($p->main_image_path) && Storage::disk('public')->exists($p->main_image_path)) {
                Storage::disk('public')->delete($p->main_image_path);
            }
            $data['main_image_path'] = $request->file('main_image')->store('uploads/products', 'public');
        }
        // do not persist raw uploaded file field
        unset($data['main_image']);
        unset($data['images']);
        $category_ids = $data['category_ids'] ?? [];
        unset($data['category_ids']);

        // IMPORTANT: Don't allow stock_qty to be updated from edit form
        // Stock should only be updated through stock adjustment feature to maintain accurate stock_movements
        // Remove stock_qty from data array to prevent any direct update
        // Even if someone bypasses the readonly attribute via browser dev tools, stock_qty will be ignored here
        unset($data['stock_qty']);

        // gallery data dari form (reorder & delete existing)
        $gallery = $request->input('gallery', []);
        // file gallery baru
        $extraImages = $request->file('images', []);

        // Use transaction to ensure data consistency
        DB::beginTransaction();
        try {
            // Update product (stock_qty is excluded to maintain stock_movements accuracy)
            DB::table('products')->where('id',$decodedId)->update(array_merge($data,[ 'updated_at'=>now() ]));
            
            // Verify product still exists after update
            $updatedProduct = DB::table('products')->where('id', $decodedId)->first();
            if (!$updatedProduct) {
                throw new \Exception("Product dengan ID {$decodedId} tidak ditemukan setelah update");
            }
            
            // add new image row when a new main_image is uploaded
            if (!empty($data['main_image_path'])) {
                DB::table('product_images')->insert([
                    'product_id' => $decodedId,
                    'image_path' => $data['main_image_path'],
                    'sort_order' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // proses reorder & delete gallery yang sudah ada
            foreach ($gallery as $imgId => $g) {
                $imgId = (int) $imgId;
                $delete = !empty($g['delete']);
                $sort = isset($g['sort_order']) ? (int) $g['sort_order'] : null;

                if ($delete) {
                    $imgRow = DB::table('product_images')->where('id', $imgId)->first();
                    if ($imgRow) {
                        // hindari orphan main_image_path
                        if ($imgRow->image_path === $p->main_image_path) {
                            DB::table('products')->where('id',$decodedId)->update(['main_image_path' => null]);
                        }
                        if ($imgRow->image_path && Storage::disk('public')->exists($imgRow->image_path)) {
                            Storage::disk('public')->delete($imgRow->image_path);
                        }
                        DB::table('product_images')->where('id', $imgId)->delete();
                    }
                } elseif ($sort !== null) {
                    DB::table('product_images')->where('id', $imgId)->update(['sort_order' => $sort]);
                }
            }

            // simpan gambar gallery tambahan (bila ada)
            if ($extraImages) {
                $maxOrder = (int) DB::table('product_images')->where('product_id', $decodedId)->max('sort_order');
                $order = $maxOrder + 1;
                foreach ($extraImages as $img) {
                    $path = $this->storeResizedImage($img);
                    DB::table('product_images')->insert([
                        'product_id' => $decodedId,
                        'image_path' => $path,
                        'sort_order' => $order++,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            // Sync categories with validation
            $this->syncCategories($decodedId, $category_ids);
            
            DB::commit();
            $this->flushProductsCache();
            return redirect()->route('admin.products.index')->with('success','Product updated');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Product update failed', [
                'product_id' => $decodedId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->withInput()->with('error', 'Gagal mengupdate produk: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $decodedId = decode_id((string) $id);
        abort_if(!$decodedId, 404);
        $p = DB::table('products')->where('id',$decodedId)->first();
        if ($p) {
            DB::table('product_categories')->where('product_id',$decodedId)->delete();
            DB::table('products')->where('id',$decodedId)->delete();
            $this->flushProductsCache();
        }
        
        // Return JSON response for AJAX requests
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Product deleted successfully'
            ]);
        }
        
        // Return redirect for regular requests
        return redirect()->route('admin.products.index')->with('success','Product deleted');
    }

    protected function syncCategories(int $productId, array $categoryIds): void
    {
        // Verify product exists before syncing categories
        $product = DB::table('products')->where('id', $productId)->first();
        if (!$product) {
            \Log::error('syncCategories: Product not found', ['product_id' => $productId]);
            throw new \Exception("Product dengan ID {$productId} tidak ditemukan");
        }
        
        // Verify categories exist
        $validCategoryIds = [];
        foreach ($categoryIds as $cid) {
            $cid = (int)$cid;
            if ($cid > 0) {
                $category = DB::table('categories')->where('id', $cid)->first();
                if ($category) {
                    $validCategoryIds[] = $cid;
                } else {
                    \Log::warning('syncCategories: Category not found', ['category_id' => $cid]);
                }
            }
        }
        
        // Delete existing categories
        DB::table('product_categories')->where('product_id', $productId)->delete();
        
        // Insert valid categories
        if (!empty($validCategoryIds)) {
            $rows = [];
            foreach ($validCategoryIds as $cid) {
                $rows[] = ['product_id' => $productId, 'category_id' => $cid];
            }
            DB::table('product_categories')->insert($rows);
        }
    }

    protected function flushProductsCache(): void
    {
        // Use helper to flush all product-related caches comprehensively
        CacheHelper::flushProductCaches();
    }

    protected function storeResizedImage($file): string
    {
        // Jika Intervention Image tersedia, gunakan untuk resize; jika tidak, fallback ke store biasa
        if (class_exists(\Intervention\Image\Facades\Image::class)) {
            $image = \Intervention\Image\Facades\Image::make($file)
                ->orientate()
                ->resize(800, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });

            $path = 'uploads/products/'.$file->hashName();
            Storage::disk('public')->put($path, (string) $image->encode());
            return $path;
        }

        return $file->store('uploads/products', 'public');
    }

    /**
     * Adjust stock manually
     */
    public function adjustStock(Request $request, $id)
    {
        $request->validate([
            'adjustment_type' => 'required|in:set,increase,decrease',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:500'
        ]);

        $storeId = app()->has('current_store') ? app('current_store')->id : 1;
        $decodedId = decode_id((string) $id);
        abort_if(!$decodedId, 404);

        $product = Product::where('id', $decodedId)->where('store_id', $storeId)->first();
        abort_if(!$product, 404);

        // IMPORTANT: Get old_stock BEFORE updating the product
        $oldStock = $product->stock_qty ?? 0;
        $quantity = (int) $request->quantity;
        $adjustmentType = $request->adjustment_type;

        // Calculate new stock based on adjustment type
        $newStock = match($adjustmentType) {
            'set' => $quantity,  // Set stock to specific value
            'increase' => $oldStock + $quantity,  // Add quantity to current stock
            'decrease' => max(0, $oldStock - $quantity),  // Subtract quantity from current stock
        };

        // Update stock in database
        $product->stock_qty = $newStock;
        $product->save();

        // Calculate adjustment quantity for logging (can be positive or negative)
        $adjustmentQuantity = match($adjustmentType) {
            'set' => $newStock - $oldStock,  // Difference between new and old
            'increase' => $quantity,  // Positive quantity
            'decrease' => -$quantity,  // Negative quantity
        };

        // Log stock movement with old_stock and new_stock
        // Note: We pass oldStock and newStock explicitly to ensure correct values
        \App\Services\StockMovementService::logAdjustmentWithValues(
            $product,
            $adjustmentQuantity,
            $oldStock,
            $newStock,
            $request->notes ?? "Penyesuaian manual stok: " . match($adjustmentType) {
                'set' => "Set stok menjadi {$newStock}",
                'increase' => "Tambah {$quantity} unit",
                'decrease' => "Kurangi {$quantity} unit",
            },
            auth()->id()
        );

        $this->flushProductsCache();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Stok berhasil disesuaikan',
                'old_stock' => $oldStock,
                'new_stock' => $newStock
            ]);
        }

        return redirect()->back()->with('success', 'Stok berhasil disesuaikan');
    }

    /**
     * Get product stock info (for AJAX requests)
     */
    public function getStockInfo($id)
    {
        $storeId = app()->has('current_store') ? app('current_store')->id : 1;
        $decodedId = decode_id((string) $id);
        abort_if(!$decodedId, 404);

        $product = Product::where('id', $decodedId)->where('store_id', $storeId)->first();
        abort_if(!$product, 404);

        return response()->json([
            'stock_qty' => $product->stock_qty ?? 0,
            'unit' => $product->unit ?? 'pcs'
        ]);
    }

    /**
     * Transfer product from one store to another
     */
    public function transferProduct(Request $request, $id)
    {
        $request->validate([
            'target_store_id' => 'required|exists:stores,id',
            'transfer_type' => 'required|in:move,copy',
            'notes' => 'nullable|string|max:500'
        ]);

        $storeId = app()->has('current_store') ? app('current_store')->id : 1;
        $decodedId = decode_id((string) $id);
        abort_if(!$decodedId, 404);

        $product = Product::where('id', $decodedId)->where('store_id', $storeId)->first();
        abort_if(!$product, 404);

        $targetStoreId = (int) $request->target_store_id;
        
        // Prevent transferring to the same store
        if ($product->store_id == $targetStoreId) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat memindahkan produk ke toko yang sama'
            ], 422);
        }

        // Check if target store exists and is active
        $targetStore = DB::table('stores')->where('id', $targetStoreId)->where('is_active', true)->first();
        if (!$targetStore) {
            return response()->json([
                'success' => false,
                'message' => 'Toko tujuan tidak ditemukan atau tidak aktif'
            ], 404);
        }

        DB::beginTransaction();
        try {
            if ($request->transfer_type === 'move') {
                // Move: Update store_id of existing product
                // Check for SKU conflict in target store
                if ($product->sku) {
                    $existingProduct = DB::table('products')
                        ->where('store_id', $targetStoreId)
                        ->where('sku', $product->sku)
                        ->where('id', '!=', $decodedId)
                        ->first();
                    
                    if ($existingProduct) {
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => "SKU '{$product->sku}' sudah digunakan di toko tujuan. Silakan ubah SKU produk terlebih dahulu."
                        ], 422);
                    }
                }

                // Update store_id
                DB::table('products')
                    ->where('id', $decodedId)
                    ->update([
                        'store_id' => $targetStoreId,
                        'updated_at' => now()
                    ]);

                // Log stock movement for transfer
                if (class_exists(\App\Services\StockMovementService::class)) {
                    \App\Services\StockMovementService::logAdjustmentWithValues(
                        Product::find($decodedId),
                        0, // No quantity change
                        $product->stock_qty ?? 0,
                        $product->stock_qty ?? 0,
                        "Produk dipindahkan dari toko ID {$product->store_id} ke toko ID {$targetStoreId}. " . ($request->notes ?? ''),
                        auth()->id()
                    );
                }

                $message = "Produk berhasil dipindahkan ke toko '{$targetStore->name}'";
            } else {
                // Copy: Create new product in target store
                // Check if product already exists in target store (by SKU or name)
                $existingProduct = null;
                
                if ($product->sku) {
                    // Check by SKU first
                    $existingProduct = DB::table('products')
                        ->where('store_id', $targetStoreId)
                        ->where('sku', $product->sku)
                        ->first();
                }
                
                // If not found by SKU, check by name (for products without SKU)
                if (!$existingProduct) {
                    $existingProduct = DB::table('products')
                        ->where('store_id', $targetStoreId)
                        ->where('name', $product->name)
                        ->first();
                }
                
                // If product already exists in target store, return error
                if ($existingProduct) {
                    DB::rollBack();
                    $reason = $product->sku 
                        ? "SKU '{$product->sku}'" 
                        : "nama '{$product->name}'";
                    return response()->json([
                        'success' => false,
                        'message' => "Produk dengan {$reason} sudah ada di toko tujuan. Produk tidak dapat disalin lagi ke toko yang sama."
                    ], 422);
                }

                // Get product data
                $productData = (array) DB::table('products')->where('id', $decodedId)->first();
                
                // Remove id and timestamps, set new store_id
                unset($productData['id'], $productData['created_at'], $productData['updated_at']);
                $productData['store_id'] = $targetStoreId;
                $productData['created_at'] = now();
                $productData['updated_at'] = now();
                
                // Generate unique slug from product name
                $baseSlug = \Illuminate\Support\Str::slug($product->name);
                $slug = $baseSlug;
                $counter = 1;
                
                // Check if slug exists and generate unique one
                while (DB::table('products')->where('slug', $slug)->exists()) {
                    $slug = $baseSlug . '-' . $counter;
                    $counter++;
                }
                
                $productData['slug'] = $slug;

                // Insert new product
                $newProductId = DB::table('products')->insertGetId($productData);

                // Copy categories
                $categories = DB::table('product_categories')
                    ->where('product_id', $decodedId)
                    ->get();
                
                if ($categories->isNotEmpty()) {
                    $categoryRows = [];
                    foreach ($categories as $cat) {
                        $categoryRows[] = [
                            'product_id' => $newProductId,
                            'category_id' => $cat->category_id
                        ];
                    }
                    DB::table('product_categories')->insert($categoryRows);
                }

                // Copy product images
                $images = DB::table('product_images')
                    ->where('product_id', $decodedId)
                    ->get();
                
                if ($images->isNotEmpty()) {
                    $imageRows = [];
                    foreach ($images as $img) {
                        $imageRows[] = [
                            'product_id' => $newProductId,
                            'image_path' => $img->image_path,
                            'sort_order' => $img->sort_order,
                            'created_at' => now(),
                            'updated_at' => now()
                        ];
                    }
                    DB::table('product_images')->insert($imageRows);
                }

                $message = "Produk berhasil disalin ke toko '{$targetStore->name}' (ID Produk Baru: {$newProductId})";
            }

            $this->flushProductsCache();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $message
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Product transfer error', [
                'product_id' => $decodedId,
                'target_store_id' => $targetStoreId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memindahkan produk: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Transfer multiple products from one store to another (Batch)
     */
    public function batchTransfer(Request $request)
    {
        $request->validate([
            'target_store_id' => 'required|exists:stores,id',
            'transfer_type' => 'required|in:move,copy',
            'transfer_all' => 'nullable|boolean',
            'product_ids' => 'required_without:transfer_all|array',
            'product_ids.*' => 'required_without:transfer_all|string',
            'notes' => 'nullable|string|max:500'
        ]);

        $storeId = app()->has('current_store') ? app('current_store')->id : 1;
        $targetStoreId = (int) $request->target_store_id;
        $transferType = $request->transfer_type;
        $transferAll = $request->boolean('transfer_all', false);
        
        // Check if target store exists and is active
        $targetStore = DB::table('stores')->where('id', $targetStoreId)->where('is_active', true)->first();
        if (!$targetStore) {
            return response()->json([
                'success' => false,
                'message' => 'Toko tujuan tidak ditemukan atau tidak aktif'
            ], 404);
        }

        // If transfer_all, get all products from current store
        if ($transferAll) {
            $products = DB::table('products')
                ->where('store_id', $storeId)
                ->get();
            
            if ($products->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada produk di toko saat ini untuk ditransfer'
                ], 404);
            }
        } else {
            // Validate product_ids is provided
            if (!$request->has('product_ids') || empty($request->product_ids)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada produk yang dipilih untuk ditransfer'
                ], 422);
            }

            // Decode product IDs
            $productIds = [];
            foreach ($request->product_ids as $encodedId) {
                $decodedId = decode_id($encodedId);
                if ($decodedId) {
                    $productIds[] = $decodedId;
                }
            }

            if (empty($productIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada produk yang valid untuk ditransfer'
                ], 422);
            }

            // Get products from current store
            $products = DB::table('products')
                ->whereIn('id', $productIds)
                ->where('store_id', $storeId)
                ->get();
        }

        if ($products->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada produk yang ditemukan di toko saat ini'
            ], 404);
        }

        $totalProducts = $products->count();

        DB::beginTransaction();
        try {
            $successCount = 0;
            $failedCount = 0;
            $skippedCount = 0;
            $failedProducts = [];
            $skippedProducts = [];

            foreach ($products as $product) {
                try {
                    // Check if product already exists in target store (by SKU or name)
                    $existingProduct = null;
                    
                    if ($product->sku) {
                        // Check by SKU first
                        $existingProduct = DB::table('products')
                            ->where('store_id', $targetStoreId)
                            ->where('sku', $product->sku)
                            ->where('id', '!=', $product->id)
                            ->first();
                    }
                    
                    // If not found by SKU, check by name (for products without SKU or when copying)
                    if (!$existingProduct && $transferType === 'copy') {
                        $existingProduct = DB::table('products')
                            ->where('store_id', $targetStoreId)
                            ->where('name', $product->name)
                            ->first();
                    }
                    
                    // If product already exists in target store, skip it
                    if ($existingProduct) {
                        $skippedCount++;
                        $skippedProducts[] = [
                            'name' => $product->name,
                            'sku' => $product->sku ?? 'N/A',
                            'reason' => "Produk dengan " . ($product->sku ? "SKU '{$product->sku}'" : "nama '{$product->name}'") . " sudah ada di toko tujuan"
                        ];
                        continue;
                    }

                    if ($transferType === 'move') {
                        // Move: Update store_id
                        DB::table('products')
                            ->where('id', $product->id)
                            ->update([
                                'store_id' => $targetStoreId,
                                'updated_at' => now()
                            ]);

                        // Log stock movement
                        if (class_exists(\App\Services\StockMovementService::class)) {
                            $productModel = Product::find($product->id);
                            if ($productModel) {
                                \App\Services\StockMovementService::logAdjustmentWithValues(
                                    $productModel,
                                    0,
                                    $product->stock_qty ?? 0,
                                    $product->stock_qty ?? 0,
                                    "Batch transfer: Produk dipindahkan dari toko ID {$product->store_id} ke toko ID {$targetStoreId}. " . ($request->notes ?? ''),
                                    auth()->id()
                                );
                            }
                        }
                    } else {
                        // Copy: Create new product
                        // Check if product already exists in target store (by SKU or name)
                        $existingProduct = null;
                        
                        if ($product->sku) {
                            // Check by SKU first
                            $existingProduct = DB::table('products')
                                ->where('store_id', $targetStoreId)
                                ->where('sku', $product->sku)
                                ->first();
                        }
                        
                        // If not found by SKU, check by name (for products without SKU)
                        if (!$existingProduct) {
                            $existingProduct = DB::table('products')
                                ->where('store_id', $targetStoreId)
                                ->where('name', $product->name)
                                ->first();
                        }
                        
                        // If product already exists in target store, skip it
                        if ($existingProduct) {
                            $skippedCount++;
                            $skippedProducts[] = [
                                'name' => $product->name,
                                'sku' => $product->sku ?? 'N/A',
                                'reason' => "Produk dengan " . ($product->sku ? "SKU '{$product->sku}'" : "nama '{$product->name}'") . " sudah ada di toko tujuan"
                            ];
                            continue;
                        }
                        
                        $productData = (array) $product;
                        unset($productData['id'], $productData['created_at'], $productData['updated_at']);
                        $productData['store_id'] = $targetStoreId;
                        $productData['created_at'] = now();
                        $productData['updated_at'] = now();
                        
                        // Generate unique slug from product name
                        $baseSlug = \Illuminate\Support\Str::slug($product->name);
                        $slug = $baseSlug;
                        $counter = 1;
                        
                        // Check if slug exists and generate unique one
                        while (DB::table('products')->where('slug', $slug)->exists()) {
                            $slug = $baseSlug . '-' . $counter;
                            $counter++;
                        }
                        
                        $productData['slug'] = $slug;

                        $newProductId = DB::table('products')->insertGetId($productData);

                        // Copy categories
                        $categories = DB::table('product_categories')
                            ->where('product_id', $product->id)
                            ->get();
                        
                        if ($categories->isNotEmpty()) {
                            $categoryRows = [];
                            foreach ($categories as $cat) {
                                $categoryRows[] = [
                                    'product_id' => $newProductId,
                                    'category_id' => $cat->category_id
                                ];
                            }
                            DB::table('product_categories')->insert($categoryRows);
                        }

                        // Copy product images
                        $images = DB::table('product_images')
                            ->where('product_id', $product->id)
                            ->get();
                        
                        if ($images->isNotEmpty()) {
                            $imageRows = [];
                            foreach ($images as $img) {
                                $imageRows[] = [
                                    'product_id' => $newProductId,
                                    'image_path' => $img->image_path,
                                    'sort_order' => $img->sort_order,
                                    'created_at' => now(),
                                    'updated_at' => now()
                                ];
                            }
                            DB::table('product_images')->insert($imageRows);
                        }
                    }

                    $successCount++;
                } catch (\Exception $e) {
                    $failedCount++;
                    $failedProducts[] = [
                        'name' => $product->name ?? 'Unknown',
                        'error' => $e->getMessage()
                    ];
                    \Log::error('Batch transfer product error', [
                        'product_id' => $product->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            $this->flushProductsCache();
            DB::commit();

            // Build result message
            $message = "Transfer batch selesai: {$successCount} dari {$totalProducts} produk berhasil";
            if ($skippedCount > 0) {
                $message .= ", {$skippedCount} dilewati (SKU duplikat)";
            }
            if ($failedCount > 0) {
                $message .= ", {$failedCount} gagal";
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'details' => [
                    'success' => $successCount,
                    'skipped' => $skippedCount,
                    'failed' => $failedCount,
                    'skipped_products' => $skippedProducts,
                    'failed_products' => $failedProducts
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Batch transfer error', [
                'product_ids' => $productIds,
                'target_store_id' => $targetStoreId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat transfer batch: ' . $e->getMessage()
            ], 500);
        }
    }
}
