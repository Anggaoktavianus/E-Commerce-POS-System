<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\HomeCollectionItemRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class HomeCollectionItemController extends Controller
{
    protected function parentOrFail($id)
    {
        $c = DB::table('home_collections')->where('id',$id)->first();
        abort_if(!$c,404);
        return $c;
    }

    public function index($collection)
    {
        $parent = $this->parentOrFail($collection);
        return view('admin.collection_items.index', compact('parent'));
    }

    public function data($collection)
    {
        $parent = $this->parentOrFail($collection);
        $query = DB::table('home_collection_items')
            ->join('products','home_collection_items.product_id','=','products.id')
            ->where('home_collection_items.home_collection_id',$parent->id)
            ->select('home_collection_items.id','products.name as product_name','home_collection_items.sort_order','home_collection_items.created_at');
        return DataTables::of($query)
            ->addIndexColumn() // This adds DT_RowIndex
            ->addColumn('actions', function($row) use ($parent){
                $edit = route('admin.collection_items.edit', $row->id);
                $del = route('admin.collection_items.destroy', $row->id);
                return view('admin.collection_items.partials.actions', compact('edit','del','row','parent'))->render();
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function create($collection)
    {
        $parent = $this->parentOrFail($collection);
        $item = null;
        $products = DB::table('products')->where('is_active', true)->orderBy('name')->get();
        return view('admin.collection_items.form', compact('parent','item','products'));
    }

    public function store(HomeCollectionItemRequest $request, $collection)
    {
        $parent = $this->parentOrFail($collection);
        $data = $request->validated();
        $data['home_collection_id'] = $parent->id;
        $data['sort_order'] = $data['sort_order'] ?? 0;
        DB::table('home_collection_items')->insert(array_merge($data,[ 'created_at'=>now(),'updated_at'=>now() ]));
        $this->flushCollectionsCache($parent->key);
        return redirect()->route('admin.collection_items.index', $parent->id)->with('success','Item added');
    }

    public function edit($item)
    {
        $item = DB::table('home_collection_items')->where('id',$item)->first();
        abort_if(!$item,404);
        $parent = $this->parentOrFail($item->home_collection_id);
        $products = DB::table('products')->where('is_active', true)->orderBy('name')->get();
        return view('admin.collection_items.form', compact('parent','item','products'));
    }

    public function update(HomeCollectionItemRequest $request, $item)
    {
        $row = DB::table('home_collection_items')->where('id',$item)->first();
        abort_if(!$row,404);
        $parent = $this->parentOrFail($row->home_collection_id);
        $data = $request->validated();
        $data['sort_order'] = $data['sort_order'] ?? 0;
        DB::table('home_collection_items')->where('id',$item)->update(array_merge($data,['updated_at'=>now()]));
        $this->flushCollectionsCache($parent->key);
        return redirect()->route('admin.collection_items.index', $parent->id)->with('success','Item updated');
    }

    public function destroy($item)
    {
        $row = DB::table('home_collection_items')->where('id',$item)->first();
        if ($row) {
            $parent = $this->parentOrFail($row->home_collection_id);
            DB::table('home_collection_items')->where('id',$item)->delete();
            $this->flushCollectionsCache($parent->key);
            
            // Return JSON response for AJAX requests
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Collection item deleted successfully'
                ]);
            }
            
            return redirect()->route('admin.collection_items.index', $parent->id)->with('success','Item deleted');
        }
        
        // Return JSON response for AJAX requests
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Item not found'
            ], 404);
        }
        
        return redirect()->back()->with('error','Item not found');
    }

    protected function flushCollectionsCache(?string $key): void
    {
        if ($key === 'bestseller') {
            Cache::forget('home.bestseller');
        }
    }
}
