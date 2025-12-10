<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CarouselSlideRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class CarouselSlideController extends Controller
{
    protected function parentOrFail($carouselId)
    {
        $carousel = DB::table('carousels')->where('id', $carouselId)->first();
        abort_if(!$carousel, 404);
        return $carousel;
    }

    public function index($carousel)
    {
        $parent = $this->parentOrFail($carousel);
        return view('admin.slides.index', compact('parent'));
    }

    public function data(Request $request, $carousel)
    {
        $this->parentOrFail($carousel);
        $query = DB::table('carousel_slides')->where('carousel_id', $carousel)->select(['id','title','subtitle','button_text','button_url','image_path','sort_order','is_active','created_at']);
        return DataTables::of($query)
            ->addIndexColumn() // This adds DT_RowIndex
            ->editColumn('is_active', fn($row) => $row->is_active ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-secondary">Non-Aktif</span>')
            ->addColumn('image', function($row){
                $src = $row->image_path ? asset($row->image_path) : '';
                return $src ? '<img src="'.$src.'" alt="" style="width:60px;height:40px;object-fit:cover;" />' : '';
            })
            ->addColumn('actions', function($row) use ($carousel){
                $edit = route('admin.slides.edit', $row->id);
                $del = route('admin.slides.destroy', $row->id);
                return view('admin.slides.partials.actions', compact('edit','del','row','carousel'))->render();
            })
            ->rawColumns(['is_active','image','actions'])
            ->make(true);
    }

    public function create($carousel)
    {
        $parent = $this->parentOrFail($carousel);
        $slide = null;
        return view('admin.slides.form', compact('parent','slide'));
    }

    public function store(CarouselSlideRequest $request, $carousel)
    {
        $parent = $this->parentOrFail($carousel);
        $data = $request->validated();
        $data['carousel_id'] = $parent->id;
        // Do not persist temporary 'image' field to DB
        unset($data['image']);
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('uploads/slides', 'public');
            $data['image_path'] = 'storage/'.$path;
        }
        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = $data['sort_order'] ?? 0;
        DB::table('carousel_slides')->insert(array_merge($data,['created_at'=>now(),'updated_at'=>now()]));
        if ($parent->key === 'home_hero') Cache::forget('home.slides.home_hero');
        return redirect()->route('admin.slides.index', $parent->id)->with('success','Slide created');
    }

    public function edit($slideId)
    {
        $slide = DB::table('carousel_slides')->where('id',$slideId)->first();
        abort_if(!$slide,404);
        $parent = $this->parentOrFail($slide->carousel_id);
        return view('admin.slides.form', compact('parent','slide'));
    }

    public function update(CarouselSlideRequest $request, $slideId)
    {
        $slide = DB::table('carousel_slides')->where('id',$slideId)->first();
        abort_if(!$slide,404);
        $parent = $this->parentOrFail($slide->carousel_id);
        $data = $request->validated();
        // Do not persist temporary 'image' field to DB
        unset($data['image']);
        if ($request->hasFile('image')) {
            if ($slide->image_path && str_starts_with($slide->image_path, 'storage/')) {
                $rel = substr($slide->image_path, 8);
                Storage::disk('public')->delete($rel);
            }
            $path = $request->file('image')->store('uploads/slides', 'public');
            $data['image_path'] = 'storage/'.$path;
        }
        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = $data['sort_order'] ?? 0;
        DB::table('carousel_slides')->where('id',$slideId)->update(array_merge($data,['updated_at'=>now()]));
        if ($parent->key === 'home_hero') Cache::forget('home.slides.home_hero');
        return redirect()->route('admin.slides.index', $parent->id)->with('success','Slide updated');
    }

    public function destroy($slideId)
    {
        $slide = DB::table('carousel_slides')->where('id',$slideId)->first();
        if ($slide) {
            $parent = $this->parentOrFail($slide->carousel_id);
            if ($slide->image_path && str_starts_with($slide->image_path, 'storage/')) {
                $rel = substr($slide->image_path, 8);
                Storage::disk('public')->delete($rel);
            }
            DB::table('carousel_slides')->where('id',$slideId)->delete();
            if ($parent->key === 'home_hero') Cache::forget('home.slides.home_hero');
            return redirect()->route('admin.slides.index', $parent->id)->with('success','Slide deleted');
        }
        return redirect()->back()->with('error','Slide not found');
    }
}
