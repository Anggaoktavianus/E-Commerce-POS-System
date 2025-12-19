<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        
        // Security Headers
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        
        // Permissions Policy - Allow geolocation for registration pages and admin stores/outlets pages
        $path = $request->path();
        
        $isRegistrationPage = $request->routeIs('customer.register.form') || 
                              $request->routeIs('mitra.register.form') ||
                              $request->routeIs('customer.register') || 
                              $request->routeIs('mitra.register') ||
                              str_contains($path, 'register');
        
        // Check path first (more reliable), then route names
        $isAdminLocationPage = str_contains($path, 'admin/stores/stores/create') ||
                               str_contains($path, 'admin/stores/stores/edit') ||
                               str_contains($path, 'admin/outlets/create') ||
                               str_contains($path, 'admin/outlets/edit') ||
                               $request->routeIs('admin.stores.stores.create') ||
                               $request->routeIs('admin.stores.stores.edit') ||
                               $request->routeIs('admin.outlets.create') ||
                               $request->routeIs('admin.outlets.edit');
        
        if ($isRegistrationPage || $isAdminLocationPage) {
            // Allow geolocation for registration pages and admin location pages (only use Permissions-Policy, Feature-Policy is deprecated)
            $response->headers->set('Permissions-Policy', 'geolocation=(self), microphone=(), camera=(), payment=()');
        } else {
            // Block geolocation for other pages (security)
            // Only block if not already set by controller (controllers may set it for specific pages)
            if (!$response->headers->has('Permissions-Policy')) {
                $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=(), payment=()');
            }
        }
        
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        
        return $response;
    }
}
