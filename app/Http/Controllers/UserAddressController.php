<?php

namespace App\Http\Controllers;

use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserAddressController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }
        
        $addresses = UserAddress::where('user_id', $user->id)
            ->where('is_active', true)
            ->orderBy('is_primary', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Check if request is from mobile
        if ($request->is('m/*') || $request->routeIs('mobile.*')) {
            return view('mobile.addresses', compact('addresses'));
        }
        
        return view('customer.addresses.index', compact('addresses'));
    }
    
    public function store(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Anda harus login terlebih dahulu'
            ], 401);
        }
        
        $validated = $request->validate([
            'label' => 'nullable|string|max:50',
            'recipient_name' => 'required|string|max:255',
            'recipient_phone' => 'required|string|max:20',
            'address' => 'required|string',
            'province' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:10',
            'notes' => 'nullable|string',
            'is_primary' => 'boolean',
        ]);
        
        // If setting as primary, unset other primary addresses
        if ($request->is_primary) {
            UserAddress::where('user_id', $user->id)
                ->update(['is_primary' => false]);
        }
        
        // Get location names if IDs provided
        $provinceName = null;
        $cityName = null;
        if ($request->loc_provinsi_id) {
            $provinceName = DB::table('loc_provinsis')->where('id', $request->loc_provinsi_id)->value('name');
        }
        if ($request->loc_kabkota_id) {
            $cityName = DB::table('loc_kabkotas')->where('id', $request->loc_kabkota_id)->value('name');
        }
        
        $address = UserAddress::create([
            'user_id' => $user->id,
            'label' => $validated['label'] ?? null,
            'recipient_name' => $validated['recipient_name'],
            'recipient_phone' => $validated['recipient_phone'],
            'address' => $validated['address'],
            'province' => $validated['province'] ?? $locProvinsiName,
            'city' => $validated['city'] ?? $locKabkotaName,
            'postal_code' => $validated['postal_code'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'loc_provinsi_id' => $request->loc_provinsi_id ?? null,
            'loc_kabkota_id' => $request->loc_kabkota_id ?? null,
            'loc_kecamatan_id' => $request->loc_kecamatan_id ?? null,
            'loc_desa_id' => $request->loc_desa_id ?? null,
            'latitude' => $request->latitude ?? null,
            'longitude' => $request->longitude ?? null,
            'is_primary' => $request->is_primary ?? false,
            'is_active' => true,
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Alamat berhasil disimpan',
            'address' => $address
        ]);
    }
    
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $address = UserAddress::where('user_id', $user->id)->findOrFail($id);
        
        $validated = $request->validate([
            'label' => 'nullable|string|max:50',
            'recipient_name' => 'required|string|max:255',
            'recipient_phone' => 'required|string|max:20',
            'address' => 'required|string',
            'province' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:10',
            'notes' => 'nullable|string',
            'is_primary' => 'boolean',
            'loc_provinsi_id' => 'nullable|exists:loc_provinsis,id',
            'loc_kabkota_id' => 'nullable|exists:loc_kabkotas,id',
            'loc_kecamatan_id' => 'nullable|exists:loc_kecamatans,id',
            'loc_desa_id' => 'nullable|exists:loc_desas,id',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);
        
        // Get location names if IDs provided
        $provinceName = null;
        $cityName = null;
        if ($request->loc_provinsi_id) {
            $provinceName = DB::table('loc_provinsis')->where('id', $request->loc_provinsi_id)->value('name');
        }
        if ($request->loc_kabkota_id) {
            $cityName = DB::table('loc_kabkotas')->where('id', $request->loc_kabkota_id)->value('name');
        }
        
        // If setting as primary, unset other primary addresses
        if ($request->is_primary) {
            UserAddress::where('user_id', $user->id)
                ->where('id', '!=', $id)
                ->update(['is_primary' => false]);
        }
        
        $updateData = array_merge($validated, [
            'province' => $validated['province'] ?? $provinceName,
            'city' => $validated['city'] ?? $cityName,
            'loc_provinsi_id' => $request->loc_provinsi_id ?? $address->loc_provinsi_id,
            'loc_kabkota_id' => $request->loc_kabkota_id ?? $address->loc_kabkota_id,
            'loc_kecamatan_id' => $request->loc_kecamatan_id ?? $address->loc_kecamatan_id,
            'loc_desa_id' => $request->loc_desa_id ?? $address->loc_desa_id,
            'latitude' => $request->latitude ?? $address->latitude,
            'longitude' => $request->longitude ?? $address->longitude,
        ]);
        
        $address->update($updateData);
        
        return response()->json([
            'success' => true,
            'message' => 'Alamat berhasil diperbarui',
            'address' => $address
        ]);
    }
    
    public function destroy($id)
    {
        $user = Auth::user();
        $address = UserAddress::where('user_id', $user->id)->findOrFail($id);
        
        $address->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Alamat berhasil dihapus'
        ]);
    }
    
    public function setPrimary($id)
    {
        $user = Auth::user();
        $address = UserAddress::where('user_id', $user->id)->findOrFail($id);
        
        $address->setAsPrimary();
        
        return response()->json([
            'success' => true,
            'message' => 'Alamat utama berhasil diubah'
        ]);
    }
}
