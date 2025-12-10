<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShippingMethod;
use App\Models\ShippingCost;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ShippingController extends Controller
{
    // Shipping Methods Management
    public function index()
    {
        $data = [
            'title' => 'Manajemen Metode Pengiriman',
            'subtitle' => 'Kelola metode pengiriman dan biaya pengiriman',
        ];
        
        return view('admin.shipping.index', $data);
    }

    public function methodsIndex()
    {
        $data = [
            'title' => 'Metode Pengiriman',
            'subtitle' => 'Daftar metode pengiriman yang tersedia',
        ];
        
        return view('admin.shipping.methods', $data);
    }

    public function methodsData()
    {
        $methods = ShippingMethod::withCount('shippingCosts')->get();
        
        return DataTables::of($methods)
            ->addColumn('logo', function($method) {
                if ($method->logo_url) {
                    return '<img src="' . asset($method->logo_url) . '" alt="' . $method->name . '" style="width: 40px; height: 40px; object-fit: cover;">';
                }
                return '<img src="https://via.placeholder.com/40x40/cccccc/000000?text=' . substr($method->name, 0, 1) . '" alt="' . $method->name . '" style="width: 40px; height: 40px; object-fit: cover;">';
            })
            ->addColumn('type_badge', function($method) {
                $colors = [
                    'instant' => 'success',
                    'same_day' => 'info', 
                    'regular' => 'primary',
                    'express' => 'warning',
                    'pickup' => 'secondary'
                ];
                $color = $colors[$method->type] ?? 'secondary';
                return '<span class="badge bg-' . $color . '">' . strtoupper($method->type) . '</span>';
            })
            ->addColumn('service_areas', function($method) {
                if ($method->service_areas) {
                    $areas = $method->service_areas;
                    // Handle both string JSON and array
                    if (is_string($areas)) {
                        $areas = json_decode($areas, true);
                    }
                    if (is_array($areas) && count($areas) > 0) {
                        return '<span class="badge bg-info">' . count($areas) . ' Kota</span>';
                    }
                }
                return '<span class="text-muted">-</span>';
            })
            ->addColumn('status', function($method) {
                if ($method->is_active) {
                    return '<span class="badge bg-success">Aktif</span>';
                }
                return '<span class="badge bg-danger">Tidak Aktif</span>';
            })
            ->addColumn('actions', function($method) {
                $editBtn = '<a href="' . route('admin.shipping.methods.edit', $method->id) . '" class="btn btn-sm btn-outline-primary me-1"><i class="bx bx-edit"></i></a>';
                $deleteBtn = '<button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteMethod(' . $method->id . ')"><i class="bx bx-trash"></i></button>';
                return $editBtn . $deleteBtn;
            })
            ->rawColumns(['logo', 'type_badge', 'service_areas', 'status', 'actions'])
            ->make(true);
    }

    public function methodsCreate()
    {
        $data = [
            'title' => 'Tambah Metode Pengiriman',
            'subtitle' => 'Tambah metode pengiriman baru',
        ];
        
        return view('admin.shipping.methods-create', $data);
    }

    public function methodsStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:100|unique:shipping_methods,code',
            'type' => 'required|in:instant,same_day,regular,express,pickup',
            'logo_url' => 'nullable|url',
            'is_active' => 'boolean',
            'service_areas' => 'nullable|array',
            'service_areas.*' => 'string',
            'max_distance_km' => 'nullable|integer|min:1',
        ]);

        $method = ShippingMethod::create([
            'name' => $request->name,
            'code' => $request->code,
            'type' => $request->type,
            'logo_url' => $request->logo_url,
            'is_active' => $request->is_active ?? true,
            'service_areas' => $request->service_areas ? json_encode($request->service_areas) : null,
            'max_distance_km' => $request->max_distance_km,
        ]);

        return redirect()
            ->route('admin.shipping.methods')
            ->with('success', 'Metode pengiriman berhasil ditambahkan');
    }

    public function methodsEdit($id)
    {
        $method = ShippingMethod::findOrFail($id);
        
        $data = [
            'title' => 'Edit Metode Pengiriman',
            'subtitle' => 'Edit metode pengiriman: ' . $method->name,
            'method' => $method,
        ];
        
        return view('admin.shipping.methods-edit', $data);
    }

    public function methodsUpdate(Request $request, $id)
    {
        $method = ShippingMethod::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:100|unique:shipping_methods,code,' . $id,
            'type' => 'required|in:instant,same_day,regular,express,pickup',
            'logo_url' => 'nullable|url',
            'is_active' => 'boolean',
            'service_areas' => 'nullable|array',
            'service_areas.*' => 'string',
            'max_distance_km' => 'nullable|integer|min:1',
        ]);

        $method->update([
            'name' => $request->name,
            'code' => $request->code,
            'type' => $request->type,
            'logo_url' => $request->logo_url,
            'is_active' => $request->is_active ?? true,
            'service_areas' => $request->service_areas ? json_encode($request->service_areas) : null,
            'max_distance_km' => $request->max_distance_km,
        ]);

        return redirect()
            ->route('admin.shipping.methods')
            ->with('success', 'Metode pengiriman berhasil diperbarui');
    }

    public function methodsDestroy($id)
    {
        $method = ShippingMethod::findOrFail($id);
        
        // Check if method has shipping costs
        if ($method->shippingCosts()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Metode pengiriman tidak dapat dihapus karena masih memiliki biaya pengiriman'
            ], 400);
        }

        $method->delete();

        return response()->json([
            'success' => true,
            'message' => 'Metode pengiriman berhasil dihapus'
        ]);
    }

    // Shipping Costs Management
    public function costsIndex()
    {
        $data = [
            'title' => 'Biaya Pengiriman',
            'subtitle' => 'Kelola biaya pengiriman per kota',
            'methods' => ShippingMethod::where('is_active', true)->get(),
        ];
        
        return view('admin.shipping.costs', $data);
    }

    public function costsData()
    {
        $costs = ShippingCost::with(['shippingMethod'])->get();
        
        return DataTables::of($costs)
            ->addColumn('method_name', function($cost) {
                return $cost->shippingMethod ? $cost->shippingMethod->name : '-';
            })
            ->addColumn('method_logo', function($cost) {
                if ($cost->shippingMethod && $cost->shippingMethod->logo_url) {
                    return '<img src="' . asset($cost->shippingMethod->logo_url) . '" alt="' . $cost->shippingMethod->name . '" style="width: 30px; height: 30px; object-fit: cover;">';
                }
                return '-';
            })
            ->addColumn('route', function($cost) {
                return $cost->origin_city . ' → ' . $cost->destination_city;
            })
            ->addColumn('cost', function($cost) {
                return 'IDR ' . number_format($cost->cost, 0, ',', '.');
            })
            ->addColumn('weight_range', function($cost) {
                return $cost->min_weight . ' - ' . $cost->max_weight . ' kg';
            })
            ->addColumn('estimated_days', function($cost) {
                $days = $cost->estimated_days;
                if (is_numeric($days)) {
                    return $days . ' hari';
                }
                return $days;
            })
            ->addColumn('status', function($cost) {
                if ($cost->is_active) {
                    return '<span class="badge bg-success">Aktif</span>';
                }
                return '<span class="badge bg-danger">Tidak Aktif</span>';
            })
            ->addColumn('actions', function($cost) {
                $editBtn = '<a href="' . route('admin.shipping.costs.edit', $cost->id) . '" class="btn btn-sm btn-outline-primary me-1"><i class="bx bx-edit"></i></a>';
                $deleteBtn = '<button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteCost(' . $cost->id . ')"><i class="bx bx-trash"></i></button>';
                return $editBtn . $deleteBtn;
            })
            ->rawColumns(['method_logo', 'route', 'cost', 'weight_range', 'estimated_days', 'status', 'actions'])
            ->make(true);
    }

    public function costsCreate()
    {
        $data = [
            'title' => 'Tambah Biaya Pengiriman',
            'subtitle' => 'Tambah biaya pengiriman baru',
            'methods' => ShippingMethod::where('is_active', true)->get(),
        ];
        
        return view('admin.shipping.costs-create', $data);
    }

    public function costsStore(Request $request)
    {
        $request->validate([
            'shipping_method_id' => 'required|exists:shipping_methods,id',
            'origin_city' => 'required|string|max:255',
            'destination_city' => 'required|string|max:255',
            'cost' => 'required|integer|min:0',
            'min_weight' => 'required|integer|min:0',
            'max_weight' => 'required|integer|min:1',
            'estimated_days' => 'required|string|max:50',
            'is_active' => 'boolean',
        ]);

        ShippingCost::create([
            'shipping_method_id' => $request->shipping_method_id,
            'origin_city' => $request->origin_city,
            'destination_city' => $request->destination_city,
            'cost' => $request->cost,
            'min_weight' => $request->min_weight,
            'max_weight' => $request->max_weight,
            'estimated_days' => $request->estimated_days,
            'is_active' => $request->is_active ?? true,
        ]);

        return redirect()
            ->route('admin.shipping.costs')
            ->with('success', 'Biaya pengiriman berhasil ditambahkan');
    }

    public function costsEdit($id)
    {
        $cost = ShippingCost::findOrFail($id);
        
        $data = [
            'title' => 'Edit Biaya Pengiriman',
            'subtitle' => 'Edit biaya pengiriman',
            'cost' => $cost,
            'methods' => ShippingMethod::where('is_active', true)->get(),
        ];
        
        return view('admin.shipping.costs-edit', $data);
    }

    public function costsUpdate(Request $request, $id)
    {
        $cost = ShippingCost::findOrFail($id);

        $request->validate([
            'shipping_method_id' => 'required|exists:shipping_methods,id',
            'origin_city' => 'required|string|max:255',
            'destination_city' => 'required|string|max:255',
            'cost' => 'required|integer|min:0',
            'min_weight' => 'required|integer|min:0',
            'max_weight' => 'required|integer|min:1',
            'estimated_days' => 'required|string|max:50',
            'is_active' => 'boolean',
        ]);

        $cost->update([
            'shipping_method_id' => $request->shipping_method_id,
            'origin_city' => $request->origin_city,
            'destination_city' => $request->destination_city,
            'cost' => $request->cost,
            'min_weight' => $request->min_weight,
            'max_weight' => $request->max_weight,
            'estimated_days' => $request->estimated_days,
            'is_active' => $request->is_active ?? true,
        ]);

        return redirect()
            ->route('admin.shipping.costs')
            ->with('success', 'Biaya pengiriman berhasil diperbarui');
    }

    public function costsDestroy($id)
    {
        $cost = ShippingCost::findOrFail($id);
        $cost->delete();

        return response()->json([
            'success' => true,
            'message' => 'Biaya pengiriman berhasil dihapus'
        ]);
    }
}
