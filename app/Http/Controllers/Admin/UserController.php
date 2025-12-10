<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public function index()
    {
        return view('admin.users.index');
    }

    public function data(Request $request)
    {
        $query = DB::table('users')->select(['id','name','email','role','is_verified','created_at']);

        if ($role = $request->get('role')) {
            if ($role === 'customer') {
                $query->where(function($q){
                    $q->where('role', 'customer')->orWhereNull('role');
                });
            } else {
                $query->where('role', $role);
            }
        }

        return DataTables::of($query)
            ->addIndexColumn() // This adds DT_RowIndex
            ->editColumn('role', fn($row) => $row->role ?? 'customer')
            ->editColumn('is_verified', fn($row) => $row->is_verified ? '<span class="badge bg-success">Terverifikasi</span>' : '<span class="badge bg-secondary">Menunggu</span>')
            ->addColumn('actions', function($row){
                $edit = route('admin.users.edit', $row->id);
                $del = route('admin.users.destroy', $row->id);
                return view('admin.users.partials.actions', compact('edit','del','row'))->render();
            })
            ->rawColumns(['is_verified','actions'])
            ->make(true);
    }

    public function create()
    {
        $user = null;
        $roles = ['admin' => 'Admin', 'mitra' => 'Mitra', 'customer' => 'Customer'];
        return view('admin.users.form', compact('user','roles'));
    }

    public function store(UserRequest $request)
    {
        $data = $request->validated();
        $data['is_verified'] = $request->boolean('is_verified');
        $data['role'] = $data['role'] ?? 'customer';

        User::create($data);

        return redirect()->route('admin.users.index')->with('success', 'User created');
    }

    public function edit(User $user)
    {
        $roles = ['admin' => 'Admin', 'mitra' => 'Mitra', 'customer' => 'Customer'];
        return view('admin.users.form', compact('user','roles'));
    }

    public function update(UserRequest $request, User $user)
    {
        $data = $request->validated();
        $data['is_verified'] = $request->boolean('is_verified');
        $data['role'] = $data['role'] ?? 'customer';

        // Jika password kosong di form edit, jangan ubah password
        if (empty($data['password'] ?? null)) {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'User updated');
    }

    public function destroy(User $user)
    {
        // Soft delete sudah diaktifkan di model User
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted');
    }

    public function toggleVerify(User $user)
    {
        if (! $user->isMitra()) {
            return back()->with('error', 'User ini bukan mitra.');
        }

        $user->is_verified = ! $user->is_verified;
        $user->save();

        return back()->with('success', 'Status verifikasi mitra diperbarui.');
    }
}
