<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MobileSupportController extends Controller
{
    public function index()
    {
        // Get FAQ from pages or settings
        $faqs = DB::table('pages')
            ->where('slug', 'like', '%faq%')
            ->orWhere('title', 'like', '%FAQ%')
            ->orWhere('title', 'like', '%Bantuan%')
            ->where('is_active', true)
            ->get();
        
        // Get support contact info from settings
        $supportPhone = DB::table('settings')
            ->where('key', 'support_phone')
            ->orWhere('key', 'contact_phone')
            ->value('value');
        
        $supportEmail = DB::table('settings')
            ->where('key', 'support_email')
            ->orWhere('key', 'contact_email')
            ->value('value');
        
        $whatsappNumber = DB::table('settings')
            ->where('key', 'whatsapp_number')
            ->value('value');
        
        return view('mobile.support', compact('faqs', 'supportPhone', 'supportEmail', 'whatsappNumber'));
    }
    
    public function contactWhatsApp(Request $request)
    {
        $phone = $request->input('phone', config('services.whatsapp.default_number', '6282222205204'));
        $message = $request->input('message', 'Halo, saya butuh bantuan');
        
        // Format phone number
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (strlen($phone) > 0 && $phone[0] === '0') {
            $phone = '62' . substr($phone, 1);
        }
        
        $whatsappUrl = "https://wa.me/{$phone}?text=" . urlencode($message);
        
        return redirect($whatsappUrl);
    }
}
