<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\Outlet;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class OutletController extends Controller
{
    // Outlet Management
    public function index()
    {
        $currentStore = app()->has('current_store') ? app('current_store') : null;
        $data = [
            'title' => 'Daftar Outlet',
            'subtitle' => 'Kelola data outlet',
            // if current_store exists, limit to it; otherwise show all active stores
            'stores' => $currentStore ? Store::where('id', $currentStore->id)->get() : Store::where('is_active', true)->get(),
        ];
        
        return view('admin.outlets.index', $data);
    }

    public function data()
    {
        $storeId = app()->has('current_store') ? app('current_store')->id : null;
        $outlets = Outlet::with('store')
            ->when($storeId, fn($q) => $q->where('store_id', $storeId))
            ->get();
        
        return DataTables::of($outlets)
            ->addColumn('store_name', function($outlet) {
                $storeShortName = $outlet->store->short_name ? '<small class="text-muted">(' . e($outlet->store->short_name) . ')</small>' : '';
                $outletShortName = $outlet->short_name ? '<small class="text-muted">(' . e($outlet->short_name) . ')</small>' : '';
                return '<div>
                    <strong>' . e($outlet->store->name) . '</strong> ' . $storeShortName . '<br>
                    <small class="text-muted">' . e($outlet->store->code) . '</small><br>
                    <strong>' . e($outlet->name) . '</strong> ' . $outletShortName . '<br>
                    <small class="text-muted">Kode: ' . e($outlet->code) . '</small>
                </div>';
            })
            ->addColumn('type_badge', function($outlet) {
                return $outlet->type_badge;
            })
            ->addColumn('manager_info', function($outlet) {
                if ($outlet->manager_name) {
                    return '<div>
                        <strong>' . $outlet->manager_name . '</strong><br>
                        <small class="text-muted">' . $outlet->formatted_phone . '</small><br>
                        <small class="text-muted">' . $outlet->email . '</small>
                    </div>';
                }
                return '<span class="text-muted">-</span>';
            })
            ->addColumn('location', function($outlet) {
                $location = $outlet->location_ref_text;
                $text = $location
                    ? '<i class="bx bx-map-pin text-muted"></i> ' . $location
                    : '<i class="bx bx-map-pin text-muted"></i> ' . $outlet->city . ', ' . $outlet->province;
                $addrPreview = $outlet->address ? '<br><small class="text-muted">' . substr($outlet->address, 0, 40) . '...</small>' : '';
                return '<div>' . $text . $addrPreview . '</div>';
            })
            ->addColumn('coordinates', function($outlet) {
                if ($outlet->latitude && $outlet->longitude) {
                    return '<a href="' . $outlet->location_url . '" target="_blank" class="btn btn-sm btn-outline-info" title="Lihat di Maps">
                        <i class="bx bx-map"></i> Maps
                    </a>';
                }
                return '<span class="text-muted">-</span>';
            })
            ->addColumn('status', function($outlet) {
                return $outlet->status_badge;
            })
            ->addColumn('actions', function($outlet) {
                $editBtn = '<a href="' . route('admin.outlets.edit', $outlet->id) . '" class="btn btn-sm btn-outline-primary me-1" title="Edit"><i class="bx bx-edit"></i></a>';
                $deleteBtn = '<button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteOutlet(' . $outlet->id . ')" title="Hapus"><i class="bx bx-trash"></i></button>';
                return $editBtn . $deleteBtn;
            })
            ->rawColumns(['store_name', 'type_badge', 'manager_info', 'location', 'coordinates', 'status', 'actions'])
            ->make(true);
    }

    public function create()
    {
        $currentStore = app()->has('current_store') ? app('current_store') : null;
        $data = [
            'title' => 'Tambah Outlet Baru',
            'subtitle' => 'Registrasi outlet baru',
            'stores' => $currentStore ? Store::where('id', $currentStore->id)->get() : Store::where('is_active', true)->get(),
        ];
        
        $response = response()->view('admin.outlets.create', $data);
        // Set Permissions-Policy header for geolocation
        if (!$response->headers->has('Permissions-Policy')) {
            $response->headers->set('Permissions-Policy', 'geolocation=(self)');
        }
        return $response;
    }

    public function store(Request $request)
    {
        $request->validate([
            'store_id' => 'required|exists:stores,id',
            'name' => 'required|string|max:255',
            'short_name' => 'required|string|max:20',
            'code' => 'required|string|max:50|unique:outlets,code',
            'type' => 'required|in:main,branch,pickup_point',
            'manager_name' => 'nullable|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email',
            'address' => 'required|string',
            'province' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'loc_provinsi_id' => 'required|integer|in:31,32,33,34,35|exists:loc_provinsis,id',
            'loc_kabkota_id' => 'required|integer|exists:loc_kabkotas,id',
            'loc_kecamatan_id' => 'required|integer|exists:loc_kecamatans,id',
            'loc_desa_id' => 'required|integer|exists:loc_desas,id',
            'postal_code' => 'required|string|max:10',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'operating_hours' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $outlet = Outlet::create([
            'store_id' => $request->store_id,
            'name' => $request->name,
            'short_name' => $request->short_name,
            'code' => $request->code,
            'type' => $request->type,
            'manager_name' => $request->manager_name,
            'phone' => $request->phone,
            'email' => $request->email,
            'address' => $request->address,
            'province' => $request->province,
            'city' => $request->city,
            'loc_provinsi_id' => $request->loc_provinsi_id,
            'loc_kabkota_id' => $request->loc_kabkota_id,
            'loc_kecamatan_id' => $request->loc_kecamatan_id,
            'loc_desa_id' => $request->loc_desa_id,
            'postal_code' => $request->postal_code,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'operating_hours' => $request->operating_hours,
            'is_active' => $request->is_active ?? true,
        ]);

        return redirect()
            ->route('admin.outlets.index')
            ->with('success', 'Outlet berhasil ditambahkan');
    }

    public function edit($id)
    {
        $storeId = app()->has('current_store') ? app('current_store')->id : null;
        $outlet = Outlet::when($storeId, fn($q) => $q->where('store_id', $storeId))->findOrFail($id);
        
        $currentStore = app()->has('current_store') ? app('current_store') : null;
        $data = [
            'title' => 'Edit Outlet',
            'subtitle' => 'Edit outlet: ' . $outlet->name,
            'outlet' => $outlet,
            'stores' => $currentStore ? Store::where('id', $currentStore->id)->get() : Store::where('is_active', true)->get(),
        ];
        
        $response = response()->view('admin.outlets.edit', $data);
        // Set Permissions-Policy header for geolocation
        if (!$response->headers->has('Permissions-Policy')) {
            $response->headers->set('Permissions-Policy', 'geolocation=(self)');
        }
        return $response;
    }

    public function update(Request $request, $id)
    {
        $outlet = Outlet::findOrFail($id);

        $request->validate([
            'store_id' => 'required|exists:stores,id',
            'name' => 'required|string|max:255',
            'short_name' => 'required|string|max:20',
            'code' => 'required|string|max:50|unique:outlets,code,' . $id,
            'type' => 'required|in:main,branch,pickup_point',
            'manager_name' => 'nullable|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email',
            'address' => 'required|string',
            'province' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'loc_provinsi_id' => 'required|integer|in:31,32,33,34,35|exists:loc_provinsis,id',
            'loc_kabkota_id' => 'required|integer|exists:loc_kabkotas,id',
            'loc_kecamatan_id' => 'required|integer|exists:loc_kecamatans,id',
            'loc_desa_id' => 'required|integer|exists:loc_desas,id',
            'postal_code' => 'required|string|max:10',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'operating_hours' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $outlet->update([
            'store_id' => $request->store_id,
            'name' => $request->name,
            'short_name' => $request->short_name,
            'code' => $request->code,
            'type' => $request->type,
            'manager_name' => $request->manager_name,
            'phone' => $request->phone,
            'email' => $request->email,
            'address' => $request->address,
            'province' => $request->province,
            'city' => $request->city,
            'loc_provinsi_id' => $request->loc_provinsi_id,
            'loc_kabkota_id' => $request->loc_kabkota_id,
            'loc_kecamatan_id' => $request->loc_kecamatan_id,
            'loc_desa_id' => $request->loc_desa_id,
            'postal_code' => $request->postal_code,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'operating_hours' => $request->operating_hours,
            'is_active' => $request->is_active ?? true,
        ]);

        return redirect()
            ->route('admin.outlets.index')
            ->with('success', 'Outlet berhasil diperbarui');
    }

    public function destroy($id)
    {
        $outlet = Outlet::findOrFail($id);
        $outlet->delete();

        return response()->json([
            'success' => true,
            'message' => 'Outlet berhasil dihapus'
        ]);
    }

    // Get outlets by store (for AJAX)
    public function getOutletsByStore($storeId)
    {
        $outlets = Outlet::where('store_id', $storeId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'code', 'type']);

        return response()->json([
            'success' => true,
            'outlets' => $outlets
        ]);
    }
}
