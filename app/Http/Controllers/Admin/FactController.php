<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FactRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class FactController extends Controller
{
    public function index()
    {
        return view('admin.facts.index');
    }

    public function data(Request $request)
    {
        $query = DB::table('facts')->select(['id','label','value','icon_class','is_active','sort_order','created_at']);
        return DataTables::of($query)
            ->addIndexColumn() // This adds DT_RowIndex
            ->editColumn('is_active', fn($row) => $row->is_active ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-secondary">Non-Aktif</span>')
            ->addColumn('actions', function($row){
                $edit = route('admin.facts.edit', $row->id);
                $del = route('admin.facts.destroy', $row->id);
                return view('admin.facts.partials.actions', compact('edit','del','row'))->render();
            })
            ->rawColumns(['is_active','actions'])
            ->make(true);
    }

    public function create()
    {
        $fact = null;
        return view('admin.facts.form', compact('fact'));
    }

    public function store(FactRequest $request)
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = $data['sort_order'] ?? 0;
        DB::table('facts')->insert(array_merge($data,[ 'created_at'=>now(), 'updated_at'=>now() ]));
        Cache::forget('home.facts');
        return redirect()->route('admin.facts.index')->with('success','Fact created');
    }

    public function edit($id)
    {
        $fact = DB::table('facts')->where('id',$id)->first();
        abort_if(!$fact,404);
        return view('admin.facts.form', compact('fact'));
    }

    public function update(FactRequest $request, $id)
    {
        $fact = DB::table('facts')->where('id',$id)->first();
        abort_if(!$fact,404);
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = $data['sort_order'] ?? 0;
        DB::table('facts')->where('id',$id)->update(array_merge($data,[ 'updated_at'=>now() ]));
        Cache::forget('home.facts');
        return redirect()->route('admin.facts.index')->with('success','Fact updated');
    }

    public function destroy($id)
    {
        $fact = DB::table('facts')->where('id',$id)->first();
        if ($fact) {
            DB::table('facts')->where('id',$id)->delete();
            Cache::forget('home.facts');
        }
        
        // Return JSON response for AJAX requests
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Fact deleted successfully'
            ]);
        }
        
        return redirect()->route('admin.facts.index')->with('success','Fact deleted');
    }
}
