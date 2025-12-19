<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CategoryRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class CategoryController extends Controller
{
    public function index()
    {
        return view('admin.categories.index');
    }

    public function data(Request $request)
    {
        $query = DB::table('categories')->select(['id','name','slug','parent_id','is_active','created_at']);
        return DataTables::of($query)
            ->addIndexColumn() // This adds DT_RowIndex
            ->editColumn('is_active', fn($row) => $row->is_active ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-secondary">Non-Aktif</span>')
            ->addColumn('actions', function($row){
                $edit = route('admin.categories.edit', $row->id);
                $del = route('admin.categories.destroy', $row->id);
                return view('admin.categories.partials.actions', compact('edit','del','row'))->render();
            })
            ->rawColumns(['is_active','actions'])
            ->make(true);
    }

    public function create()
    {
        $category = null;
        $parents = DB::table('categories')->whereNull('parent_id')->orderBy('name')->get();
        return view('admin.categories.form', compact('category','parents'));
    }

    public function store(CategoryRequest $request)
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');
        DB::table('categories')->insert(array_merge($data,[ 'created_at'=>now(),'updated_at'=>now() ]));
        $this->flushProductsCache();
        return redirect()->route('admin.categories.index')->with('success','Category created');
    }

    public function edit($id)
    {
        $category = DB::table('categories')->where('id',$id)->first();
        abort_if(!$category,404);
        $parents = DB::table('categories')->whereNull('parent_id')->where('id','!=',$id)->orderBy('name')->get();
        return view('admin.categories.form', compact('category','parents'));
    }

    public function update(CategoryRequest $request, $id)
    {
        $cat = DB::table('categories')->where('id',$id)->first();
        abort_if(!$cat,404);
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');
        DB::table('categories')->where('id',$id)->update(array_merge($data,[ 'updated_at'=>now() ]));
        $this->flushProductsCache();
        return redirect()->route('admin.categories.index')->with('success','Category updated');
    }

    public function destroy($id)
    {
        $cat = DB::table('categories')->where('id',$id)->first();
        if ($cat) {
            DB::table('product_categories')->where('category_id',$id)->delete();
            DB::table('categories')->where('id',$id)->delete();
            $this->flushProductsCache();
        }
        
        // Return JSON response for AJAX requests
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Category deleted successfully'
            ]);
        }
        
        return redirect()->route('admin.categories.index')->with('success','Category deleted');
    }

    protected function flushProductsCache(): void
    {
        Cache::forget('home.products.fruits');
        Cache::forget('home.products.vegetables');
    }
}
