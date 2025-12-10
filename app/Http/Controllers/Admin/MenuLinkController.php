<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MenuLinkRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class MenuLinkController extends Controller
{
    protected function parentOrFail($menuId)
    {
        $menu = DB::table('navigation_menus')->where('id',$menuId)->first();
        abort_if(!$menu,404);
        return $menu;
    }

    public function index($menu)
    {
        $parent = $this->parentOrFail($menu);
        return view('admin.links.index', compact('parent'));
    }

    public function data(Request $request, $menu)
    {
        $this->parentOrFail($menu);
        $query = DB::table('navigation_links')
            ->leftJoin('navigation_links as parent_links', 'navigation_links.parent_id', '=', 'parent_links.id')
            ->where('navigation_links.navigation_menu_id', $menu)
            ->orderByRaw('COALESCE(parent_links.sort_order, navigation_links.sort_order) ASC') // Order by parent's sort_order, or own sort_order if no parent
            ->orderByRaw('CASE WHEN navigation_links.parent_id IS NULL THEN 0 ELSE 1 END ASC') // Parents first, then children
            ->orderBy('navigation_links.sort_order', 'asc') // Order children by their sort_order
            ->orderBy('navigation_links.created_at', 'asc')
            ->select([
                'navigation_links.id',
                'navigation_links.label', 
                'navigation_links.url',
                'navigation_links.route_name',
                'navigation_links.target',
                'navigation_links.parent_id',
                'navigation_links.sort_order',
                'navigation_links.is_active',
                'navigation_links.created_at',
                'parent_links.label as parent_label'
            ]);
            
        return DataTables::of($query)
            ->addIndexColumn() // This adds DT_RowIndex
            ->addColumn('drag_order', function($row) {
                if ($row->parent_id) {
                    // Get parent's sort order to create decimal numbering
                    $parentSort = DB::table('navigation_links')
                        ->where('id', $row->parent_id)
                        ->value('sort_order');
                    return '<span style="margin-left: 20px;"><i class="fas fa-arrows-alt drag-handle me-1"></i><span class="drag-handle">' . $parentSort . '.' . $row->sort_order . '</span></span>';
                } else {
                    return '<i class="fas fa-arrows-alt drag-handle me-1"></i><span class="drag-handle">' . $row->sort_order . '</span>';
                }
            })
            ->editColumn('label', function($row) {
                if ($row->parent_id) {
                    return '<span style="margin-left: 20px;"><i class="fas fa-angle-right text-muted me-1"></i>' . $row->label . '</span>';
                } else {
                    return $row->label;
                }
            })
            ->editColumn('is_active', fn($row) => $row->is_active ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-secondary">Non-Aktif</span>')
            ->addColumn('parent_name', function($row) {
                return $row->parent_label ?: '-';
            })
            ->addColumn('actions', function($row) use ($menu){
                $edit = route('admin.links.edit', $row->id);
                $del = route('admin.links.destroy', $row->id);
                return view('admin.links.partials.actions', compact('edit','del','row','menu'))->render();
            })
            ->rawColumns(['drag_order', 'label', 'is_active','actions'])
            ->make(true);
    }

    public function create($menu)
    {
        $parent = $this->parentOrFail($menu);
        $link = null;
        $parents = DB::table('navigation_links')
            ->where('navigation_menu_id',$parent->id)
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->get();

        $pages = DB::table('pages')
            ->where('is_published', true)
            ->orderBy('title')
            ->get();

        // Get the next sort order
        $nextSortOrder = DB::table('navigation_links')
            ->where('navigation_menu_id', $parent->id)
            ->max('sort_order') + 1;

        return view('admin.links.form', compact('parent','link','parents','pages','nextSortOrder'));
    }

    public function store(MenuLinkRequest $request, $menu)
    {
        $parent = $this->parentOrFail($menu);
        $data = $request->validated();
        $data['navigation_menu_id'] = $parent->id;
        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = $data['sort_order'] ?? 0;
        DB::table('navigation_links')->insert(array_merge($data,['created_at'=>now(),'updated_at'=>now()]));
        $this->flushMenusCache($parent->location);
        return redirect()->route('admin.links.index', $parent->id)->with('success','Link created');
    }

    public function edit($linkId)
    {
        $link = DB::table('navigation_links')->where('id',$linkId)->first();
        abort_if(!$link,404);
        $parent = $this->parentOrFail($link->navigation_menu_id);
        $parents = DB::table('navigation_links')
            ->where('navigation_menu_id',$parent->id)
            ->whereNull('parent_id')
            ->where('id','!=',$link->id)
            ->orderBy('sort_order')
            ->get();

        $pages = DB::table('pages')
            ->where('is_published', true)
            ->orderBy('title')
            ->get();

        return view('admin.links.form', compact('parent','link','parents','pages'));
    }

    public function update(MenuLinkRequest $request, $linkId)
    {
        $link = DB::table('navigation_links')->where('id',$linkId)->first();
        abort_if(!$link,404);
        $parent = $this->parentOrFail($link->navigation_menu_id);
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = $data['sort_order'] ?? 0;
        DB::table('navigation_links')->where('id',$linkId)->update(array_merge($data,['updated_at'=>now()]));
        $this->flushMenusCache($parent->location);
        return redirect()->route('admin.links.index', $parent->id)->with('success','Link updated');
    }

    public function updateOrder(Request $request)
    {
        $request->validate([
            'orders' => 'required|array',
            'orders.*.id' => 'required|integer',
            'orders.*.sort_order' => 'required|integer|min:0'
        ]);

        foreach ($request->orders as $order) {
            DB::table('navigation_links')
                ->where('id', $order['id'])
                ->update(['sort_order' => $order['sort_order']]);
        }

        // Clear cache
        Cache::forget('navigation_menus');
        
        return response()->json([
            'success' => true,
            'message' => 'Urutan link berhasil diperbarui'
        ]);
    }

    public function destroy($linkId)
    {
        $link = DB::table('navigation_links')->where('id',$linkId)->first();
        if ($link) {
            $parent = $this->parentOrFail($link->navigation_menu_id);
            DB::table('navigation_links')->where('id',$linkId)->delete();
            $this->flushMenusCache($parent->location);
            return redirect()->route('admin.links.index', $parent->id)->with('success','Link deleted');
        }
        return redirect()->back()->with('error','Link not found');
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
