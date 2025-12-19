<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\Outlet;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class StoreController extends Controller
{
    // Store Management
    public function index()
    {
        $data = [
            'title' => 'Manajemen Toko',
            'subtitle' => 'Kelola toko dan outlet',
            'totalStores' => Store::count(),
            'activeStores' => Store::where('is_active', true)->count(),
            'totalOutlets' => Outlet::count(),
            'activeOutlets' => Outlet::where('is_active', true)->count(),
        ];
        
        return view('admin.stores.index', $data);
    }

    public function storesIndex()
    {
        $data = [
            'title' => 'Daftar Toko',
            'subtitle' => 'Kelola data toko',
        ];
        
        return view('admin.stores.stores', $data);
    }

    public function storesData()
    {
        $stores = Store::withCount(['outlets', 'outlets as active_outlets_count' => function($query) {
            $query->where('is_active', true);
        }])->get();
        
        return DataTables::of($stores)
            ->addColumn('logo', function($store) {
                if ($store->logo_url) {
                    return '<img src="' . asset($store->logo_url) . '" alt="' . $store->name . '" style="width: 40px; height: 40px; object-fit: cover; border-radius: 8px;">';
                }
                return '<img src="https://via.placeholder.com/40x40/6c757d/ffffff?text=' . substr($store->name, 0, 1) . '" alt="' . $store->name . '" style="width: 40px; height: 40px; object-fit: cover; border-radius: 8px;">';
            })
            ->addColumn('store_info', function($store) {
                $shortName = $store->short_name ? '<small class="text-muted">(' . e($store->short_name) . ')</small>' : '';
                return '<div>
                    <strong>' . e($store->name) . '</strong> ' . $shortName . '<br>
                    <small class="text-muted">Kode: ' . e($store->code) . '</small><br>
                    <small class="text-muted">' . ($store->domain ? e($store->domain) : '-') . '</small>
                </div>';
            })
            ->addColumn('owner_info', function($store) {
                return '<div>
                    <strong>' . $store->owner_name . '</strong><br>
                    <small class="text-muted">' . $store->email . '</small><br>
                    <small class="text-muted">' . $store->formatted_phone . '</small>
                </div>';
            })
            ->addColumn('address', function($store) {
                $location = $store->location_ref_text;
                $text = $location
                    ? '<i class="bx bx-map-pin text-muted"></i> ' . $location
                    : '<i class="bx bx-map-pin text-muted"></i> ' . $store->city . ', ' . $store->province;
                $addrPreview = $store->address ? '<br><small class="text-muted">' . substr($store->address, 0, 50) . '...</small>' : '';
                return '<div>' . $text . $addrPreview . '</div>';
            })
            ->addColumn('outlets_info', function($store) {
                return '<div>
                    <span class="badge bg-primary">' . $store->outlets_count . ' Total</span><br>
                    <span class="badge bg-success">' . $store->active_outlets_count . ' Aktif</span>
                </div>';
            })
            ->addColumn('status', function($store) {
                return $store->status_badge;
            })
            ->addColumn('actions', function($store) {
                $viewBtn = '<a href="' . route('admin.stores.show', $store->id) . '" class="btn btn-sm btn-outline-info me-1" title="Lihat Detail"><i class="bx bx-show"></i></a>';
                $editBtn = '<a href="' . route('admin.stores.stores.edit', $store->id) . '" class="btn btn-sm btn-outline-primary me-1" title="Edit"><i class="bx bx-edit"></i></a>';
                $deleteBtn = '<button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteStore(' . $store->id . ')" title="Hapus"><i class="bx bx-trash"></i></button>';
                return $viewBtn . $editBtn . $deleteBtn;
            })
            ->rawColumns(['logo', 'store_info', 'owner_info', 'address', 'outlets_info', 'status', 'actions'])
            ->make(true);
    }

    public function storesCreate()
    {
        $data = [
            'title' => 'Tambah Toko Baru',
            'subtitle' => 'Registrasi toko baru',
        ];
        
        $response = response()->view('admin.stores.stores-create', $data);
        // Set Permissions-Policy header for geolocation
        if (!$response->headers->has('Permissions-Policy')) {
            $response->headers->set('Permissions-Policy', 'geolocation=(self)');
        }
        return $response;
    }

    public function storesStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'short_name' => 'required|string|max:20',
            'code' => 'required|string|max:50|unique:stores,code',
            'owner_name' => 'required|string|max:255',
            'email' => 'required|email|unique:stores,email',
            'phone' => 'required|string|max:20',
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
            'tax_id' => 'nullable|string|max:50',
            'business_license' => 'nullable|string|max:100',
            'logo_url' => 'nullable|url',
            'is_active' => 'boolean',
        ]);

        $store = Store::create([
            'name' => $request->name,
            'short_name' => $request->short_name,
            'code' => $request->code,
            'owner_name' => $request->owner_name,
            'email' => $request->email,
            'phone' => $request->phone,
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
            'tax_id' => $request->tax_id,
            'business_license' => $request->business_license,
            'logo_url' => $request->logo_url,
            'is_active' => $request->is_active ?? true,
        ]);

        return redirect()
            ->route('admin.stores.stores')
            ->with('success', 'Toko berhasil ditambahkan');
    }

    public function storesShow($id)
    {
        $store = Store::with(['outlets' => function($query) {
            $query->orderBy('type', 'asc')->orderBy('name', 'asc');
        }])->findOrFail($id);
        
        $data = [
            'title' => 'Detail Toko',
            'subtitle' => $store->name,
            'store' => $store,
        ];
        
        return view('admin.stores.stores-show', $data);
    }

    public function storesEdit($id)
    {
        $store = Store::findOrFail($id);
        
        $data = [
            'title' => 'Edit Toko',
            'subtitle' => 'Edit toko: ' . $store->name,
            'store' => $store,
        ];
        
        $response = response()->view('admin.stores.stores-edit', $data);
        // Set Permissions-Policy header for geolocation
        if (!$response->headers->has('Permissions-Policy')) {
            $response->headers->set('Permissions-Policy', 'geolocation=(self)');
        }
        return $response;
    }

    public function storesUpdate(Request $request, $id)
    {
        $store = Store::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'short_name' => 'required|string|max:20',
            'code' => 'required|string|max:50|unique:stores,code,' . $id,
            'owner_name' => 'required|string|max:255',
            'email' => 'required|email|unique:stores,email,' . $id,
            'phone' => 'required|string|max:20',
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
            'tax_id' => 'nullable|string|max:50',
            'business_license' => 'nullable|string|max:100',
            'logo_url' => 'nullable|url',
            'is_active' => 'boolean',
        ]);

        $store->update([
            'name' => $request->name,
            'short_name' => $request->short_name,
            'code' => $request->code,
            'owner_name' => $request->owner_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'province' => $request->province,
            'city' => $request->city,
            'loc_provinsi_id' => $request->loc_provinsi_id,
            'loc_kabkota_id' => $request->loc_kabkota_id,
            'loc_kecamatan_id' => $request->loc_kecamatan_id,
            'loc_desa_id' => $request->loc_desa_id,
            'postal_code' => $request->postal_code,
            'latitude' => $request->latitude ?: null,
            'longitude' => $request->longitude ?: null,
            'tax_id' => $request->tax_id,
            'business_license' => $request->business_license,
            'logo_url' => $request->logo_url,
            'is_active' => $request->has('is_active') ? (bool)$request->is_active : true,
        ]);

        return redirect()
            ->route('admin.stores.stores')
            ->with('success', 'Toko berhasil diperbarui');
    }

    public function storesDestroy($id)
    {
        $store = Store::findOrFail($id);
        
        // Check if store has outlets
        if ($store->outlets()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Toko tidak dapat dihapus karena masih memiliki outlet. Hapus outlet terlebih dahulu.'
            ]);
        }
        
        $store->delete();

        return response()->json([
            'success' => true,
            'message' => 'Toko berhasil dihapus'
        ]);
    }
}
