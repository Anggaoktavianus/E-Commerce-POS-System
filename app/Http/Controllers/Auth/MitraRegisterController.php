<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class MitraRegisterController extends Controller
{
    public function showRegistrationForm()
    {
        $response = response()->view('auth.register-mitra');
        // Set Permissions-Policy header (Feature-Policy is deprecated, handled by middleware)
        // Only set if not already set by middleware
        if (!$response->headers->has('Permissions-Policy')) {
            $response->headers->set('Permissions-Policy', 'geolocation=(self)');
        }
        return $response;
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string'],
            'loc_provinsi_id' => ['required','integer','in:31,32,33,34,35','exists:loc_provinsis,id'],
            'loc_kabkota_id' => ['required','integer','exists:loc_kabkotas,id'],
            'loc_kecamatan_id' => ['required','integer','exists:loc_kecamatans,id'],
            'loc_desa_id' => ['required','integer','exists:loc_desas,id'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'company_name' => ['required', 'string', 'max:255'],
            'company_address' => ['required', 'string'],
            'company_phone' => ['required', 'string', 'max:20'],
            'npwp' => ['required', 'string', 'max:25'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'address' => $request->address,
            'loc_provinsi_id' => $request->loc_provinsi_id,
            'loc_kabkota_id' => $request->loc_kabkota_id,
            'loc_kecamatan_id' => $request->loc_kecamatan_id,
            'loc_desa_id' => $request->loc_desa_id,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'role' => 'mitra',
            'company_name' => $request->company_name,
            'company_address' => $request->company_address,
            'company_phone' => $request->company_phone,
            'npwp' => $request->npwp,
            'is_verified' => false,
        ]);

        // Get location names for address
        $locProvinsiName = \DB::table('loc_provinsis')->where('id', $request->loc_provinsi_id)->value('name');
        $locKabkotaName = \DB::table('loc_kabkotas')->where('id', $request->loc_kabkota_id)->value('name');
        $locKecamatanName = \DB::table('loc_kecamatans')->where('id', $request->loc_kecamatan_id)->value('name');
        $locDesaName = \DB::table('loc_desas')->where('id', $request->loc_desa_id)->value('name');

        // Automatically create primary address in user_addresses table
        UserAddress::create([
            'user_id' => $user->id,
            'label' => 'Alamat Utama',
            'recipient_name' => $request->name,
            'recipient_phone' => $request->phone,
            'address' => $request->address,
            'province' => $locProvinsiName,
            'city' => $locKabkotaName,
            'postal_code' => null, // Can be added later if needed
            'country' => 'Indonesia',
            'loc_provinsi_id' => $request->loc_provinsi_id,
            'loc_kabkota_id' => $request->loc_kabkota_id,
            'loc_kecamatan_id' => $request->loc_kecamatan_id,
            'loc_desa_id' => $request->loc_desa_id,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'notes' => null,
            'is_primary' => true,
            'is_active' => true,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('mitra.dashboard')
            ->with('success', 'Pendaftaran berhasil! Akun Anda sedang menunggu verifikasi admin.');
    }
}