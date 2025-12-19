<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BannerRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class BannerController extends Controller
{
    public function index()
    {
        return view('admin.banners.index');
    }

    public function data(Request $request)
    {
        $query = DB::table('banners')->select(['id','title','subtitle','button_text','button_url','image_path','position','sort_order','is_active','created_at']);
        return DataTables::of($query)
            ->addIndexColumn() // This adds DT_RowIndex
            ->editColumn('is_active', fn($row) => $row->is_active ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-secondary">Non-Aktif</span>')
            ->addColumn('image', function($row){
                $src = $row->image_path ? asset($row->image_path) : '';
                return $src ? '<img src="'.$src.'" alt="" style="width:60px;height:40px;object-fit:cover;" />' : '';
            })
            ->addColumn('actions', function($row){
                $edit = route('admin.banners.edit', $row->id);
                $del = route('admin.banners.destroy', $row->id);
                return view('admin.banners.partials.actions', compact('edit','del','row'))->render();
            })
            ->rawColumns(['is_active','image','actions'])
            ->make(true);
    }

    public function create()
    {
        $banner = null;
        $nextSortOrder = DB::table('banners')->max('sort_order') + 1;
        return view('admin.banners.form', compact('banner', 'nextSortOrder'));
    }

    public function store(BannerRequest $request)
    {
        $data = $request->validated();
        // ensure no stray 'image' field gets persisted
        unset($data['image']);
        if ($request->hasFile('image_path')) {
            $path = $request->file('image_path')->store('uploads/banners', 'public');
            $data['image_path'] = 'storage/'.$path;
        }
        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['show_circle'] = $request->boolean('show_circle');

        DB::table('banners')->insert(array_merge($data,[
            'created_at' => now(), 'updated_at' => now(),
        ]));

        $this->flushCacheForPosition(data_get($data,'position'));
        return redirect()->route('admin.banners.index')->with('success', 'Banner created');
    }

    public function edit($id)
    {
        $banner = DB::table('banners')->where('id', $id)->first();
        abort_if(!$banner, 404);
        return view('admin.banners.form', compact('banner'));
    }

    public function update(BannerRequest $request, $id)
    {
        $banner = DB::table('banners')->where('id', $id)->first();
        abort_if(!$banner, 404);

        $data = $request->validated();
        // ensure no stray 'image' field gets persisted
        unset($data['image']);
        if ($request->hasFile('image_path')) {
            if ($banner->image_path && str_starts_with($banner->image_path, 'storage/')) {
                $rel = substr($banner->image_path, 8);
                Storage::disk('public')->delete($rel);
            }
            $path = $request->file('image_path')->store('uploads/banners', 'public');
            $data['image_path'] = 'storage/'.$path;
        }
        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['show_circle'] = $request->boolean('show_circle');

        DB::table('banners')->where('id', $id)->update(array_merge($data,[
            'updated_at' => now(),
        ]));

        $this->flushCacheForPosition(data_get($data,'position', $banner->position));
        return redirect()->route('admin.banners.index')->with('success', 'Banner updated');
    }

    public function destroy($id)
    {
        $banner = DB::table('banners')->where('id', $id)->first();
        if ($banner) {
            if ($banner->image_path && str_starts_with($banner->image_path, 'storage/')) {
                $rel = substr($banner->image_path, 8);
                Storage::disk('public')->delete($rel);
            }
            DB::table('banners')->where('id', $id)->delete();
            $this->flushCacheForPosition($banner->position);
        }
        
        // Return JSON response for AJAX requests
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Banner deleted successfully'
            ]);
        }
        
        return redirect()->route('admin.banners.index')->with('success', 'Banner deleted');
    }

    protected function flushCacheForPosition(?string $position): void
    {
        if ($position === 'home_top') Cache::forget('home.banners.top');
        if ($position === 'home_middle') Cache::forget('home.banners.middle');
        if ($position === 'home_bottom') Cache::forget('home.banners.bottom');
    }
}
