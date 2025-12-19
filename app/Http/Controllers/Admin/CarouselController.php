<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CarouselRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class CarouselController extends Controller
{
    public function index()
    {
        return view('admin.carousels.index');
    }

    public function data(Request $request)
    {
        $query = DB::table('carousels')->select(['id','name','key','is_active','created_at']);
        return DataTables::of($query)
            ->addIndexColumn() // This adds DT_RowIndex
            ->editColumn('is_active', fn($row) => $row->is_active ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-secondary">Non-Aktif</span>')
            ->addColumn('slides', function($row){
                $url = route('admin.slides.index', $row->id);
                return '<a href="'.$url.'" class="btn btn-sm btn-outline-info">Slides</a>';
            })
            ->addColumn('actions', function($row){
                $edit = route('admin.carousels.edit', $row->id);
                $del = route('admin.carousels.destroy', $row->id);
                return view('admin.carousels.partials.actions', compact('edit','del','row'))->render();
            })
            ->rawColumns(['is_active','slides','actions'])
            ->make(true);
    }

    public function create()
    {
        $carousel = null;
        return view('admin.carousels.form', compact('carousel'));
    }

    public function store(CarouselRequest $request)
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');
        DB::table('carousels')->insert(array_merge($data,[
            'created_at'=>now(),'updated_at'=>now()
        ]));
        if (($data['key'] ?? null) === 'home_hero') {
            Cache::forget('home.slides.home_hero');
        }
        return redirect()->route('admin.carousels.index')->with('success','Carousel created');
    }

    public function edit($id)
    {
        $carousel = DB::table('carousels')->where('id',$id)->first();
        abort_if(!$carousel,404);
        return view('admin.carousels.form', compact('carousel'));
    }

    public function update(CarouselRequest $request, $id)
    {
        $carousel = DB::table('carousels')->where('id',$id)->first();
        abort_if(!$carousel,404);
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');
        DB::table('carousels')->where('id',$id)->update(array_merge($data,[
            'updated_at'=>now()
        ]));
        if (($data['key'] ?? $carousel->key) === 'home_hero') {
            Cache::forget('home.slides.home_hero');
        }
        return redirect()->route('admin.carousels.index')->with('success','Carousel updated');
    }

    public function destroy($id)
    {
        $carousel = DB::table('carousels')->where('id',$id)->first();
        if ($carousel) {
            DB::table('carousels')->where('id',$id)->delete();
            if ($carousel->key === 'home_hero') {
                Cache::forget('home.slides.home_hero');
            }
        }
        
        // Return JSON response for AJAX requests
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Carousel deleted successfully'
            ]);
        }
        
        return redirect()->route('admin.carousels.index')->with('success','Carousel deleted');
    }
}
