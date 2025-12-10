<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class MitraController extends Controller
{
    public function index()
    {
        return view('admin.mitras.index');
    }

    public function data(Request $request)
    {
        $query = DB::table('users')
            ->where('role', 'mitra')
            ->select(['id','name','email','phone','address','is_verified','created_at']);
        
        return DataTables::of($query)
            ->addIndexColumn() // This adds DT_RowIndex
            ->editColumn('is_verified', fn($row) => $row->is_verified ? '<span class="badge bg-success">Terverifikasi</span>' : '<span class="badge bg-secondary">Menunggu</span>')
            ->addColumn('actions', function($row){
                $verify = route('admin.mitras.toggle_verify', $row->id);
                return view('admin.mitras.partials.actions', compact('verify','row'))->render();
            })
            ->rawColumns(['is_verified','actions'])
            ->make(true);
    }

    public function toggleVerify(User $mitra)
    {
        if (! $mitra->isMitra()) {
            return back()->with('error', 'User ini bukan mitra.');
        }

        $mitra->is_verified = ! $mitra->is_verified;
        $mitra->save();

        return back()->with('success', 'Status verifikasi mitra diperbarui.');
    }
}
