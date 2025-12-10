<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class ApiDocumentationController extends Controller
{
    /**
     * Display API documentation
     */
    public function index()
    {
        $routes = $this->getApiRoutes();
        
        return response()->json([
            'title' => 'Samsae API Documentation',
            'version' => '1.0.0',
            'base_url' => config('app.url'),
            'timestamp' => now()->toISOString(),
            'endpoints' => $routes,
            'authentication' => [
                'type' => 'Bearer Token',
                'description' => 'Use Laravel Sanctum or session-based authentication',
                'header' => 'Authorization: Bearer {token}'
            ],
            'response_formats' => [
                'success' => [
                    'status' => 'success',
                    'data' => 'Response data',
                    'message' => 'Success message (optional)'
                ],
                'error' => [
                    'status' => 'error',
                    'message' => 'Error description',
                    'errors' => 'Validation errors (optional)'
                ]
            ]
        ]);
    }

    /**
     * Get all API routes with documentation
     */
    private function getApiRoutes(): array
    {
        $routes = [];
        
        // Authentication endpoints
        $routes['auth'] = [
            'login' => [
                'method' => 'POST',
                'url' => '/api/login',
                'description' => 'User login',
                'parameters' => [
                    'email' => 'required|string|email',
                    'password' => 'required|string|min:8'
                ],
                'response' => [
                    'user' => 'User object',
                    'token' => 'Authentication token'
                ]
            ],
            'logout' => [
                'method' => 'POST',
                'url' => '/api/logout',
                'description' => 'User logout',
                'auth' => 'required'
            ],
            'register' => [
                'method' => 'POST',
                'url' => '/api/register',
                'description' => 'User registration',
                'parameters' => [
                    'name' => 'required|string|max:255',
                    'email' => 'required|string|email|unique:users',
                    'password' => 'required|string|min:8|confirmed',
                    'phone' => 'required|string|max:20',
                    'address' => 'required|string',
                    'role' => 'required|in:customer,mitra'
                ]
            ]
        ];

        // Products endpoints
        $routes['products'] = [
            'index' => [
                'method' => 'GET',
                'url' => '/api/products',
                'description' => 'Get all products',
                'parameters' => [
                    'page' => 'integer|default:1',
                    'limit' => 'integer|default:20|max:100',
                    'category_id' => 'integer|exists:categories,id',
                    'search' => 'string|max:255',
                    'sort' => 'in:name,price,created_at|default:created_at',
                    'order' => 'in:asc,desc|default:desc'
                ]
            ],
            'show' => [
                'method' => 'GET',
                'url' => '/api/products/{id}',
                'description' => 'Get product details',
                'parameters' => [
                    'id' => 'required|integer|exists:products,id'
                ]
            ]
        ];

        // Orders endpoints
        $routes['orders'] = [
            'index' => [
                'method' => 'GET',
                'url' => '/api/orders',
                'description' => 'Get user orders',
                'auth' => 'required',
                'parameters' => [
                    'status' => 'in:pending,paid,processing,shipped,delivered,cancelled',
                    'page' => 'integer|default:1',
                    'limit' => 'integer|default:20'
                ]
            ],
            'store' => [
                'method' => 'POST',
                'url' => '/api/orders',
                'description' => 'Create new order',
                'auth' => 'required',
                'parameters' => [
                    'items' => 'required|array',
                    'items.*.product_id' => 'required|integer|exists:products,id',
                    'items.*.quantity' => 'required|integer|min:1',
                    'shipping_address' => 'required|array',
                    'shipping_address.first_name' => 'required|string',
                    'shipping_address.last_name' => 'required|string',
                    'shipping_address.phone' => 'required|string',
                    'shipping_address.address' => 'required|string',
                    'payment_method' => 'required|string'
                ]
            ],
            'show' => [
                'method' => 'GET',
                'url' => '/api/orders/{id}',
                'description' => 'Get order details',
                'auth' => 'required',
                'parameters' => [
                    'id' => 'required|integer|exists:orders,id'
                ]
            ]
        ];

        // Locations endpoints
        $routes['locations'] = [
            'provinsis' => [
                'method' => 'GET',
                'url' => '/api/locations/provinsis',
                'description' => 'Get all provinces'
            ],
            'kabkotas' => [
                'method' => 'GET',
                'url' => '/api/locations/kabkotas/{provinsi_id}',
                'description' => 'Get regencies/cities by province',
                'parameters' => [
                    'provinsi_id' => 'required|integer|exists:loc_provinsis,id'
                ]
            ],
            'kecamatans' => [
                'method' => 'GET',
                'url' => '/api/locations/kecamatans/{kabkota_id}',
                'description' => 'Get districts by regency/city',
                'parameters' => [
                    'kabkota_id' => 'required|integer|exists:loc_kabkotas,id'
                ]
            ],
            'desas' => [
                'method' => 'GET',
                'url' => '/api/locations/desas/{kecamatan_id}',
                'description' => 'Get villages by district',
                'parameters' => [
                    'kecamatan_id' => 'required|integer|exists:loc_kecamatans,id'
                ]
            ]
        ];

        // Payment endpoints
        $routes['payment'] = [
            'create_transaction' => [
                'method' => 'POST',
                'url' => '/api/payment/create',
                'description' => 'Create payment transaction',
                'auth' => 'required',
                'parameters' => [
                    'order_id' => 'required|integer|exists:orders,id',
                    'payment_method' => 'required|string'
                ],
                'response' => [
                    'snap_token' => 'Midtrans Snap token',
                    'redirect_url' => 'Payment redirect URL'
                ]
            ],
            'check_status' => [
                'method' => 'GET',
                'url' => '/api/payment/status/{order_id}',
                'description' => 'Check payment status',
                'auth' => 'required',
                'parameters' => [
                    'order_id' => 'required|integer|exists:orders,id'
                ]
            ]
        ];

        // Admin endpoints
        $routes['admin'] = [
            'dashboard' => [
                'method' => 'GET',
                'url' => '/api/admin/dashboard',
                'description' => 'Get admin dashboard statistics',
                'auth' => 'required|admin'
            ],
            'users' => [
                'method' => 'GET',
                'url' => '/api/admin/users',
                'description' => 'Get all users',
                'auth' => 'required|admin',
                'parameters' => [
                    'role' => 'in:admin,mitra,customer',
                    'page' => 'integer|default:1',
                    'limit' => 'integer|default:20'
                ]
            ]
        ];

        return $routes;
    }
}
