<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class MitraMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Anda harus login terlebih dahulu.');
        }

        if (!Auth::user()->isMitra()) {
            return redirect()->route('home')->with('error', 'Hanya mitra yang dapat mengakses halaman ini.');
        }

        return $next($request);
    }
}