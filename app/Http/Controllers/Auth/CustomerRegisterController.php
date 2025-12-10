<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class CustomerRegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules::defaults()],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'loc_provinsi_id' => ['required','integer','in:31,32,33,34,35','exists:loc_provinsis,id'],
            'loc_kabkota_id' => ['required','integer','exists:loc_kabkotas,id'],
            'loc_kecamatan_id' => ['required','integer','exists:loc_kecamatans,id'],
            'loc_desa_id' => ['required','integer','exists:loc_desas,id'],
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
            'role' => 'customer',
            'is_verified' => true,
            'email_verified_at' => now(),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('home')->with('success', 'Pendaftaran berhasil, selamat datang!');
    }
}
