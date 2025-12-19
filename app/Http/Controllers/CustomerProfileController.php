<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;

class CustomerProfileController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }
        
        // Check if request is from mobile
        if ($request->is('m/*') || $request->routeIs('mobile.*')) {
            return view('mobile.profile', compact('user'));
        }
        
        // Get user's primary address
        $primaryAddress = UserAddress::where('user_id', $user->id)
            ->where('is_primary', true)
            ->where('is_active', true)
            ->first();
        
        // Get all addresses count
        $addressCount = UserAddress::where('user_id', $user->id)
            ->where('is_active', true)
            ->count();
        
        // Get location names for user
        $provinceName = null;
        $cityName = null;
        $districtName = null;
        $villageName = null;
        
        if ($user->loc_provinsi_id) {
            $provinceName = DB::table('loc_provinsis')->where('id', $user->loc_provinsi_id)->value('name');
        }
        if ($user->loc_kabkota_id) {
            $cityName = DB::table('loc_kabkotas')->where('id', $user->loc_kabkota_id)->value('name');
        }
        if ($user->loc_kecamatan_id) {
            $districtName = DB::table('loc_kecamatans')->where('id', $user->loc_kecamatan_id)->value('name');
        }
        if ($user->loc_desa_id) {
            $villageName = DB::table('loc_desas')->where('id', $user->loc_desa_id)->value('name');
        }
        
        return view('customer.profile.index', compact(
            'user',
            'primaryAddress',
            'addressCount',
            'provinceName',
            'cityName',
            'districtName',
            'villageName'
        ));
    }
    
    public function update(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Anda harus login terlebih dahulu'
            ], 401);
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
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
        
        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'loc_provinsi_id' => $validated['loc_provinsi_id'] ?? null,
            'loc_kabkota_id' => $validated['loc_kabkota_id'] ?? null,
            'loc_kecamatan_id' => $validated['loc_kecamatan_id'] ?? null,
            'loc_desa_id' => $validated['loc_desa_id'] ?? null,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diperbarui',
            'user' => $user->fresh()
        ]);
    }
    
    public function updatePassword(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Anda harus login terlebih dahulu'
            ], 401);
        }
        
        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);
        
        // Check current password
        if (!Hash::check($validated['current_password'], $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Password saat ini tidak sesuai',
                'errors' => ['current_password' => ['Password saat ini tidak sesuai']]
            ], 422);
        }
        
        // Update password
        $user->update([
            'password' => Hash::make($validated['password'])
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Password berhasil diubah'
        ]);
    }
}
