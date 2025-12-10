<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
use Illuminate\Session\TokenMismatchException;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        '/midtrans/notification',
        '/webhook/*',
        '/api/webhook/*',
    ];

    /**
     * Handle failed CSRF token validation.
     */
    protected function tokensMatch($request)
    {
        $token = $request->input('_token') ?: $request->header('X-CSRF-TOKEN');
        
        if (!$token || strlen($token) !== 40) {
            return false;
        }

        return parent::tokensMatch($request);
    }

    /**
     * Add additional CSRF token validation
     */
    protected function addCookieToResponse($request, $response)
    {
        $response = parent::addCookieToResponse($request, $response);
        
        // Set secure and HttpOnly flags for CSRF cookie
        if (config('app.env') === 'production') {
            $response->headers->setCookie(
                $response->headers->getCookie('XSRF-TOKEN')
                    ->withSecure(true)
                    ->withHttpOnly(false)
                    ->withSameSite('Lax')
            );
        }
        
        return $response;
    }
}
