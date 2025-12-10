<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SocialLinkRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class SocialLinkController extends Controller
{
    public function index()
    {
        return view('admin.social_links.index');
    }

    public function data(Request $request)
    {
        $query = DB::table('social_links')->select(['id','platform','icon_class','url','sort_order','is_active','created_at']);
        return DataTables::of($query)
        ->addIndexColumn() // This adds DT_RowIndex
            ->editColumn('is_active', fn($row) => $row->is_active ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>')
            ->addColumn('actions', function($row){
                $edit = route('admin.social_links.edit', $row->id);
                $del = route('admin.social_links.destroy', $row->id);
                return view('admin.social_links.partials.actions', compact('edit','del','row'))->render();
            })
            ->rawColumns(['is_active','actions'])
            ->make(true);
    }

    public function create()
    {
        $social_link = null;
        return view('admin.social_links.form', compact('social_link'));
    }

    public function store(SocialLinkRequest $request)
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = $data['sort_order'] ?? 0;
        DB::table('social_links')->insert(array_merge($data,[ 'created_at'=>now(), 'updated_at'=>now() ]));
        Cache::forget('home.social_links');
        return redirect()->route('admin.social_links.index')->with('success','Social link created');
    }

    public function edit($id)
    {
        $social_link = DB::table('social_links')->where('id',$id)->first();
        abort_if(!$social_link,404);
        return view('admin.social_links.form', compact('social_link'));
    }

    public function update(SocialLinkRequest $request, $id)
    {
        $social_link = DB::table('social_links')->where('id',$id)->first();
        abort_if(!$social_link,404);
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = $data['sort_order'] ?? 0;
        DB::table('social_links')->where('id',$id)->update(array_merge($data,[ 'updated_at'=>now() ]));
        Cache::forget('home.social_links');
        return redirect()->route('admin.social_links.index')->with('success','Social link updated');
    }

    public function destroy($id)
    {
        $sl = DB::table('social_links')->where('id',$id)->first();
        if ($sl) {
            DB::table('social_links')->where('id',$id)->delete();
            Cache::forget('home.social_links');
        }
        return redirect()->route('admin.social_links.index')->with('success','Social link deleted');
    }
}
