<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MenuRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class MenuController extends Controller
{
    public function index()
    {
        return view('admin.menus.index');
    }

    public function data(Request $request)
    {
        $query = DB::table('navigation_menus')->select(['id','name','location','is_active','created_at']);
        return DataTables::of($query)
            ->addIndexColumn() // This adds DT_RowIndex
            ->editColumn('is_active', fn($row) => $row->is_active ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-secondary">Non-Aktif</span>')
            ->addColumn('links', function($row){
                $url = route('admin.links.index', $row->id);
                return '<a href="'.$url.'" class="btn btn-sm btn-outline-info">Links</a>';
            })
            ->addColumn('actions', function($row){
                $edit = route('admin.menus.edit', $row->id);
                $del = route('admin.menus.destroy', $row->id);
                return view('admin.menus.partials.actions', compact('edit','del','row'))->render();
            })
            ->rawColumns(['is_active','links','actions'])
            ->make(true);
    }

    public function create()
    {
        $menu = null;
        return view('admin.menus.form', compact('menu'));
    }

    public function store(MenuRequest $request)
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');
        DB::table('navigation_menus')->insert(array_merge($data,[
            'created_at'=>now(),'updated_at'=>now(),
        ]));
        $this->flushMenusCache($data['location'] ?? null);
        return redirect()->route('admin.menus.index')->with('success','Menu created');
    }

    public function edit($id)
    {
        $menu = DB::table('navigation_menus')->where('id',$id)->first();
        abort_if(!$menu,404);
        return view('admin.menus.form', compact('menu'));
    }

    public function update(MenuRequest $request, $id)
    {
        $menu = DB::table('navigation_menus')->where('id',$id)->first();
        abort_if(!$menu,404);
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');
        DB::table('navigation_menus')->where('id',$id)->update(array_merge($data,[
            'updated_at'=>now(),
        ]));
        $this->flushMenusCache($data['location'] ?? $menu->location);
        return redirect()->route('admin.menus.index')->with('success','Menu updated');
    }

    public function destroy($id)
    {
        $menu = DB::table('navigation_menus')->where('id',$id)->first();
        if ($menu) {
            DB::table('navigation_links')->where('navigation_menu_id',$menu->id)->delete();
            DB::table('navigation_menus')->where('id',$id)->delete();
            $this->flushMenusCache($menu->location);
        }
        
        // Return JSON response for AJAX requests
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Menu deleted successfully'
            ]);
        }
        
        return redirect()->route('admin.menus.index')->with('success','Menu deleted');
    }

    protected function flushMenusCache(?string $location): void
    {
        if ($location === 'header') {
            Cache::forget('home.header_menu');
            Cache::forget('home.header_links');
        }
        if ($location && str_starts_with($location, 'footer_column_')) {
            Cache::forget('home.footer_menus');
        }
    }
}
