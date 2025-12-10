<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TestimonialRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class TestimonialController extends Controller
{
    public function index()
    {
        return view('admin.testimonials.index');
    }

    public function data(Request $request)
    {
        $query = DB::table('testimonials')->select(['id','author_name','author_title','rating','is_active','sort_order','created_at']);
        return DataTables::of($query)
            ->addIndexColumn() // This adds DT_RowIndex
            ->editColumn('is_active', fn($row) => $row->is_active ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-secondary">Non-Aktif</span>')
            ->addColumn('actions', function($row){
                $edit = route('admin.testimonials.edit', $row->id);
                $del = route('admin.testimonials.destroy', $row->id);
                return view('admin.testimonials.partials.actions', compact('edit','del','row'))->render();
            })
            ->rawColumns(['is_active','actions'])
            ->make(true);
    }

    public function create()
    {
        $testimonial = null;
        return view('admin.testimonials.form', compact('testimonial'));
    }

    public function store(TestimonialRequest $request)
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = $data['sort_order'] ?? 0;
        if ($request->hasFile('avatar')) {
            $data['avatar_path'] = $request->file('avatar')->store('uploads/testimonials', 'public');
        }
        DB::table('testimonials')->insert(array_merge($data,[
            'created_at'=>now(),'updated_at'=>now(),
        ]));
        Cache::forget('home.testimonials');
        return redirect()->route('admin.testimonials.index')->with('success','Testimonial created');
    }

    public function edit($id)
    {
        $testimonial = DB::table('testimonials')->where('id',$id)->first();
        abort_if(!$testimonial,404);
        return view('admin.testimonials.form', compact('testimonial'));
    }

    public function update(TestimonialRequest $request, $id)
    {
        $testimonial = DB::table('testimonials')->where('id',$id)->first();
        abort_if(!$testimonial,404);
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = $data['sort_order'] ?? 0;
        if ($request->hasFile('avatar')) {
            $data['avatar_path'] = $request->file('avatar')->store('uploads/testimonials', 'public');
        }
        DB::table('testimonials')->where('id',$id)->update(array_merge($data,[
            'updated_at'=>now(),
        ]));
        Cache::forget('home.testimonials');
        return redirect()->route('admin.testimonials.index')->with('success','Testimonial updated');
    }

    public function destroy($id)
    {
        $t = DB::table('testimonials')->where('id',$id)->first();
        if ($t) {
            DB::table('testimonials')->where('id',$id)->delete();
            Cache::forget('home.testimonials');
        }
        return redirect()->route('admin.testimonials.index')->with('success','Testimonial deleted');
    }
}
