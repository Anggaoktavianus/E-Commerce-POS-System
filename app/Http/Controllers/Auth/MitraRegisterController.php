<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class MitraRegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register-mitra');
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
            'role' => 'mitra',
            'company_name' => $request->company_name,
            'company_address' => $request->company_address,
            'company_phone' => $request->company_phone,
            'npwp' => $request->npwp,
            'is_verified' => false,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('mitra.dashboard')
            ->with('success', 'Pendaftaran berhasil! Akun Anda sedang menunggu verifikasi admin.');
    }
}