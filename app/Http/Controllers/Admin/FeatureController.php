<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FeatureRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class FeatureController extends Controller
{
    public function index()
    {
        return view('admin.features.index');
    }

    public function data(Request $request)
    {
        $query = DB::table('features')->select(['id','title','description','icon_class','image_path','sort_order','is_active','created_at']);
        return DataTables::of($query)
            ->addIndexColumn() // This adds DT_RowIndex
            ->editColumn('is_active', fn($row) => $row->is_active ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-secondary">Non-Aktif</span>')
            ->addColumn('image', function($row){
                $src = $row->image_path ? asset($row->image_path) : '';
                return $src ? '<img src="'.$src.'" alt="" style="width:40px;height:40px;object-fit:cover;" />' : '';
            })
            ->addColumn('actions', function($row){
                $edit = route('admin.features.edit', $row->id);
                $del = route('admin.features.destroy', $row->id);
                return view('admin.features.partials.actions', compact('edit','del','row'))->render();
            })
            ->rawColumns(['is_active','image','actions'])
            ->make(true);
    }

    public function create()
    {
        $feature = null;
        return view('admin.features.form', compact('feature'));
    }

    public function store(FeatureRequest $request)
    {
        $data = $request->validated();
        $path = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('uploads/features', 'public');
            $data['image_path'] = 'storage/'.$path;
        }
        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = $data['sort_order'] ?? 0;

        DB::table('features')->insert(array_merge(
            $data,
            ['created_at' => now(), 'updated_at' => now()]
        ));

        Cache::forget('home.features');
        return redirect()->route('admin.features.index')->with('success', 'Feature created');
    }

    public function edit($id)
    {
        $feature = DB::table('features')->where('id', $id)->first();
        abort_if(!$feature, 404);
        return view('admin.features.form', compact('feature'));
    }

    public function update(FeatureRequest $request, $id)
    {
        $feature = DB::table('features')->where('id', $id)->first();
        abort_if(!$feature, 404);

        $data = $request->validated();
        if ($request->hasFile('image')) {
            // delete old
            if ($feature->image_path && str_starts_with($feature->image_path, 'storage/')) {
                $rel = substr($feature->image_path, 8); // remove 'storage/'
                Storage::disk('public')->delete($rel);
            }
            $path = $request->file('image')->store('uploads/features', 'public');
            $data['image_path'] = 'storage/'.$path;
        }
        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = $data['sort_order'] ?? 0;

        DB::table('features')->where('id', $id)->update(array_merge(
            $data,
            ['updated_at' => now()]
        ));

        Cache::forget('home.features');
        return redirect()->route('admin.features.index')->with('success', 'Feature updated');
    }

    public function destroy($id)
    {
        $feature = DB::table('features')->where('id', $id)->first();
        if ($feature) {
            if ($feature->image_path && str_starts_with($feature->image_path, 'storage/')) {
                $rel = substr($feature->image_path, 8);
                Storage::disk('public')->delete($rel);
            }
            DB::table('features')->where('id', $id)->delete();
            Cache::forget('home.features');
        }
        
        // Return JSON response for AJAX requests
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Feature deleted successfully'
            ]);
        }
        
        return redirect()->route('admin.features.index')->with('success', 'Feature deleted');
    }
}
