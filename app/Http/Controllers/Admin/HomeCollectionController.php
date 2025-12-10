<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\HomeCollectionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class HomeCollectionController extends Controller
{
    public function index()
    {
        return view('admin.collections.index');
    }

    public function data(Request $request)
    {
        $query = DB::table('home_collections')->select(['id','name','key','is_active','created_at']);
        return DataTables::of($query)
            ->addIndexColumn() // This adds DT_RowIndex
            ->editColumn('is_active', fn($row) => $row->is_active ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-secondary">Non-Aktif</span>')
            ->addColumn('items', function($row){
                $url = route('admin.collection_items.index', $row->id);
                return '<a href="'.$url.'" class="btn btn-sm btn-outline-info">Items</a>';
            })
            ->addColumn('actions', function($row){
                $edit = route('admin.collections.edit', $row->id);
                $del = route('admin.collections.destroy', $row->id);
                return view('admin.collections.partials.actions', compact('edit','del','row'))->render();
            })
            ->rawColumns(['is_active','items','actions'])
            ->make(true);
    }

    public function create()
    {
        $collection = null;
        return view('admin.collections.form', compact('collection'));
    }

    public function store(HomeCollectionRequest $request)
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');
        DB::table('home_collections')->insert(array_merge($data,[ 'created_at'=>now(), 'updated_at'=>now() ]));
        $this->flushCollectionsCache($data['key'] ?? null);
        return redirect()->route('admin.collections.index')->with('success','Collection created');
    }

    public function edit($id)
    {
        $collection = DB::table('home_collections')->where('id',$id)->first();
        abort_if(!$collection,404);
        return view('admin.collections.form', compact('collection'));
    }

    public function update(HomeCollectionRequest $request, $id)
    {
        $c = DB::table('home_collections')->where('id',$id)->first();
        abort_if(!$c,404);
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');
        DB::table('home_collections')->where('id',$id)->update(array_merge($data,[ 'updated_at'=>now() ]));
        $this->flushCollectionsCache($data['key'] ?? $c->key);
        return redirect()->route('admin.collections.index')->with('success','Collection updated');
    }

    public function destroy($id)
    {
        $c = DB::table('home_collections')->where('id',$id)->first();
        if ($c) {
            DB::table('home_collection_items')->where('home_collection_id',$c->id)->delete();
            DB::table('home_collections')->where('id',$id)->delete();
            $this->flushCollectionsCache($c->key);
        }
        return redirect()->route('admin.collections.index')->with('success','Collection deleted');
    }

    protected function flushCollectionsCache(?string $key): void
    {
        if ($key === 'bestseller') {
            Cache::forget('home.bestseller');
        }
    }
}
