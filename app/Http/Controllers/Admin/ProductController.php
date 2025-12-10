<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductRequest;
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
            ->select('products.id','products.name','products.slug','products.price','products.unit','products.is_active','products.is_featured','products.is_bestseller','products.created_at', DB::raw('GROUP_CONCAT(categories.name SEPARATOR ", ") as categories'))
            ->where('products.store_id', $storeId)
            ->groupBy('products.id','products.name','products.slug','products.price','products.unit','products.is_active','products.is_featured','products.is_bestseller','products.created_at');

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
            ->editColumn('is_active', fn($row) => $row->is_active ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-secondary">Non-Aktif</span>')
            ->editColumn('is_featured', fn($row) => $row->is_featured ? '<span class="badge bg-info">Unggulan</span>' : '')
            ->editColumn('is_bestseller', fn($row) => $row->is_bestseller ? '<span class="badge bg-warning text-dark">Terlaris</span>' : '')
            ->addColumn('actions', function($row){
                $encodedId = encode_id($row->id);
                $edit = route('admin.products.edit', $encodedId);
                $del = route('admin.products.destroy', $encodedId);
                return view('admin.products.partials.actions', compact('edit','del','row'))->render();
            })
            ->rawColumns(['is_active','is_featured','is_bestseller','actions'])
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

        // gallery data dari form (reorder & delete existing)
        $gallery = $request->input('gallery', []);
        // file gallery baru
        $extraImages = $request->file('images', []);

        DB::table('products')->where('id',$decodedId)->update(array_merge($data,[ 'updated_at'=>now() ]));
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

        $this->syncCategories($decodedId, $category_ids);
        $this->flushProductsCache();
        return redirect()->route('admin.products.index')->with('success','Product updated');
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
        return redirect()->route('admin.products.index')->with('success','Product deleted');
    }

    protected function syncCategories(int $productId, array $categoryIds): void
    {
        DB::table('product_categories')->where('product_id',$productId)->delete();
        $rows = [];
        foreach ($categoryIds as $cid) {
            $rows[] = ['product_id'=>$productId,'category_id'=> (int)$cid];
        }
        if ($rows) DB::table('product_categories')->insert($rows);
    }

    protected function flushProductsCache(): void
    {
        Cache::forget('home.products.fruits');
        Cache::forget('home.products.vegetables');
        Cache::forget('home.bestseller');
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
}
