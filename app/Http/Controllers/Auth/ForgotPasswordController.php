<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Services\WhatsAppService;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    protected $whatsappService;

    public function __construct(WhatsAppService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Find user by email
        $user = DB::table('users')->where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Email tidak ditemukan dalam sistem.']);
        }

        // Generate 6-digit OTP
        $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Delete existing tokens for this email
        DB::table('password_reset_tokens')->where('email', $user->email)->delete();
        
        // Insert new OTP (store as plain text for easy verification)
        DB::table('password_reset_tokens')->insert([
            'email' => $user->email,
            'token' => bcrypt($otp), // Store hashed OTP
            'created_at' => now(),
        ]);

        // Send OTP via email
        try {
            $emailContent = "Halo!\n\n";
            $emailContent .= "Anda telah meminta reset password untuk akun Samsae Store.\n\n";
            $emailContent .= "Kode OTP Anda adalah:\n";
            $emailContent .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
            $emailContent .= "  {$otp}\n";
            $emailContent .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
            $emailContent .= "Kode ini berlaku selama 10 menit.\n\n";
            $emailContent .= "JANGAN bagikan kode ini kepada siapapun.\n\n";
            $emailContent .= "Jika Anda tidak meminta reset password, abaikan pesan ini.\n\n";
            $emailContent .= "Terima kasih,\n";
            $emailContent .= "Tim Samsae Store";

            \Illuminate\Support\Facades\Mail::raw($emailContent, function($message) use ($user) {
                $message->to($user->email)
                        ->subject('Kode OTP Reset Password - Samsae Store');
            });

            // Store email in session for OTP verification page
            session(['otp_email' => $user->email]);
            
            return redirect()->route('password.otp.verify')->with('status', 'Kode OTP telah dikirim ke email Anda. Silakan cek inbox atau folder spam.');
        } catch (\Exception $e) {
            \Log::error('Email OTP Error: ' . $e->getMessage());
            return back()->withErrors(['email' => 'Gagal mengirim email. Silakan coba lagi atau gunakan metode WhatsApp.']);
        }
    }

    public function sendResetLinkWhatsApp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => ['required', 'string'],
        ], [
            'phone.required' => 'Nomor telepon wajib diisi.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Clean phone number
        $phone = preg_replace('/[^0-9+]/', '', $request->phone);
        $phone = ltrim($phone, '+');
        if (strlen($phone) > 0 && $phone[0] !== '0' && strlen($phone) >= 10) {
            $phone = '0' . $phone;
        }

        // Find user by phone
        $user = DB::table('users')->where('phone', $phone)->first();

        if (!$user) {
            return back()->withErrors(['phone' => 'Nomor telepon tidak ditemukan dalam sistem.']);
        }

        // Generate 6-digit OTP
        $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Delete existing tokens for this email
        DB::table('password_reset_tokens')->where('email', $user->email)->delete();
        
        // Insert new OTP (store as plain text for easy verification)
        DB::table('password_reset_tokens')->insert([
            'email' => $user->email,
            'token' => bcrypt($otp), // Store hashed OTP
            'created_at' => now(),
        ]);

        // Send OTP via WhatsApp
        $result = $this->whatsappService->sendPasswordResetOTP($phone, $otp);

        if ($result['success']) {
            // Store email in session for OTP verification page
            session(['otp_email' => $user->email]);
            
            return redirect()->route('password.otp.verify')->with('status', 'Kode OTP telah dikirim ke WhatsApp Anda. Silakan cek pesan WhatsApp.');
        }

        return back()->withErrors(['phone' => $result['message'] ?? 'Gagal mengirim pesan WhatsApp. Silakan coba lagi.']);
    }

    public function showOtpVerifyForm()
    {
        if (!session('otp_email')) {
            return redirect()->route('password.request')->withErrors(['email' => 'Sesi telah berakhir. Silakan request OTP lagi.']);
        }

        return view('auth.verify-otp', [
            'email' => session('otp_email')
        ]);
    }

    public function verifyOtp(Request $request)
    {
        // Handle OTP from array input (6 separate inputs)
        $otpArray = $request->input('otp', []);
        $otpCombined = $request->input('otp');
        
        // If OTP comes as array, combine it
        if (is_array($otpArray) && count($otpArray) === 6) {
            $otpValue = implode('', $otpArray);
        } else {
            $otpValue = $otpCombined;
        }

        $request->merge(['otp' => $otpValue]);

        $request->validate([
            'otp' => ['required', 'string', 'size:6', 'regex:/^[0-9]{6}$/'],
            'email' => ['required', 'email'],
        ], [
            'otp.required' => 'Kode OTP wajib diisi.',
            'otp.size' => 'Kode OTP harus 6 digit.',
            'otp.regex' => 'Kode OTP harus berupa 6 digit angka.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
        ]);

        // Find password reset record
        $passwordReset = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$passwordReset) {
            return back()->withErrors(['otp' => 'Kode OTP tidak ditemukan atau sudah kadaluarsa.']);
        }

        // Check if OTP is expired (10 minutes)
        $otpAge = now()->diffInMinutes(\Carbon\Carbon::parse($passwordReset->created_at));
        if ($otpAge > 10) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            session()->forget('otp_email');
            return back()->withErrors(['otp' => 'Kode OTP sudah kadaluarsa. Silakan request ulang.']);
        }

        // Verify OTP
        $otpValid = \Illuminate\Support\Facades\Hash::check($request->otp, $passwordReset->token);

        if (!$otpValid) {
            return back()->withErrors(['otp' => 'Kode OTP tidak valid. Silakan coba lagi.']);
        }

        // OTP verified, generate a token for password reset
        $resetToken = Str::random(64);
        DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->update([
                'token' => bcrypt($resetToken),
                'created_at' => now(),
            ]);

        // Clear OTP session
        session()->forget('otp_email');

        return redirect()->route('password.reset', ['token' => $resetToken, 'email' => $request->email])
            ->with('success', 'OTP berhasil diverifikasi. Silakan masukkan password baru Anda.');
    }
}
