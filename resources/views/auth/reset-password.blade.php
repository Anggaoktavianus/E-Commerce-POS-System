@extends('layouts.app')

@section('content')
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        * { box-sizing: border-box; }
        
        /* Page Background */
        .reset-password-page {
            background: linear-gradient(135deg, #f0f9f4 0%, #d4edda 100%);
            min-height: 100vh;
            padding: 2rem 0;
        }
        
        /* Modern Card Container */
        .reset-password-modern {
            position: relative;
            max-width: 600px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }
        
        .reset-password-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            animation: slideUp 0.6s ease-out;
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Header Section */
        .reset-password-header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            padding: 2rem;
            text-align: center;
            color: white;
            position: relative;
            overflow: hidden;
        }
        
        .reset-password-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: pulse 3s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 0.5; }
            50% { transform: scale(1.1); opacity: 0.8; }
        }
        
        .reset-password-header .logo {
            width: 90px;
            height: 90px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
            position: relative;
            z-index: 1;
        }
        
        .reset-password-header .logo img {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 50%;
        }
        
        .reset-password-header h2 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: white !important;
            position: relative;
            z-index: 1;
        }
        
        .reset-password-header p {
            font-size: 1rem;
            opacity: 0.95;
            margin: 0;
            position: relative;
            z-index: 1;
        }
        
        /* Body Section */
        .reset-password-body {
            padding: 2.5rem;
        }
        
        @media (max-width: 768px) {
            .reset-password-body {
                padding: 1.5rem;
            }
        }
        
        /* Alert Styling */
        .alert {
            border-radius: 12px;
            border: none;
            padding: 1rem 1.25rem;
            margin-bottom: 1.5rem;
            animation: shake 0.5s ease-in-out;
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            75% { transform: translateX(10px); }
        }
        
        .alert-danger {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
            color: white;
        }
        
        .alert-success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
        }
        
        /* Form Controls */
        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            background: white;
        }
        
        .form-control:focus {
            border-color: #28a745;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
            background: white;
        }
        
        .form-control.is-invalid {
            border-color: #dc3545;
            background: #fff5f5;
        }
        
        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.5rem;
            font-size: 0.95rem;
        }
        
        .required-field::after {
            content: " *";
            color: #dc3545;
        }
        
        .invalid-feedback {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
            display: block;
        }
        
        /* Input Group */
        .input-group {
            position: relative;
        }
        
        .input-group .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #6c757d;
            cursor: pointer;
            z-index: 10;
            padding: 5px;
            transition: color 0.3s ease;
        }
        
        .input-group .toggle-password:hover {
            color: #28a745;
        }
        
        /* Form Group */
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        /* Submit Button */
        .btn-submit {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
            margin-top: 1.5rem;
        }
        
        .btn-submit:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(40, 167, 69, 0.4);
        }
        
        .btn-submit:active {
            transform: translateY(-1px);
        }
        
        .btn-submit.loading {
            pointer-events: none;
            opacity: 0.8;
        }
        
        .btn-submit.loading::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            top: 50%;
            left: 50%;
            margin-left: -10px;
            margin-top: -10px;
            border: 3px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 0.8s linear infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        /* Footer */
        .reset-password-footer {
            text-align: center;
            padding: 1.5rem 2rem;
            border-top: 2px solid #e8f5e9;
            background: linear-gradient(135deg, #f8fff9 0%, #ffffff 100%);
        }
        
        .reset-password-footer p {
            color: #6c757d;
            font-size: 0.95rem;
            margin: 0;
        }
        
        .reset-password-footer a {
            color: #28a745;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .reset-password-footer a:hover {
            color: #20c997;
            text-decoration: underline;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .reset-password-header {
                padding: 1.5rem 1rem;
            }
            
            .reset-password-header h2 {
                font-size: 1.5rem;
            }
            
            .reset-password-header .logo {
                width: 70px;
                height: 70px;
            }
            
            .reset-password-header .logo img {
                width: 55px;
                height: 55px;
            }
        }
    </style>
    
    <!-- Page Header with Breadcrumbs -->
    <div class="container-fluid page-header py-3 mb-0" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
        <div class="container py-3">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="h3 text-white mb-2">
                        <i class="bx bx-key me-2"></i>Reset Password
                    </h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a class="text-white text-decoration-none" href="{{ route('home') }}">Beranda</a></li>
                            <li class="breadcrumb-item"><a class="text-white text-decoration-none" href="{{ route('login') }}">Masuk</a></li>
                            <li class="breadcrumb-item text-white active" aria-current="page">Reset Password</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    
    <div class="reset-password-page">
        <div class="reset-password-modern">
            <div class="reset-password-card">
                <div class="reset-password-header">
                    <div class="logo">
                        <img src="{{ asset('storage/uploads/settings/pA4eU0uxj49qQm9buxJyHQVoP8u8u6MyfjDbSNic.png') }}" alt="Logo">
                    </div>
                    <h2>Reset Password</h2>
                    <p>Masukkan password baru untuk akun Anda</p>
                </div>
                <div class="reset-password-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <ul class="mb-0" style="list-style: none; padding-left: 0;">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.update') }}" id="resetPasswordForm">
                        @csrf

                        <input type="hidden" name="token" value="{{ $token }}">
                        <input type="hidden" name="email" value="{{ $email }}">

                        <div class="form-group">
                            <label for="email" class="form-label">
                                <i class="bx bx-envelope me-2"></i>Email
                            </label>
                            <input id="email" type="email" class="form-control" value="{{ $email }}" disabled>
                            <small class="text-muted d-block mt-1">
                                Email yang akan direset password-nya
                            </small>
                        </div>

                        <div class="form-group">
                            <label for="password" class="form-label required-field">
                                <i class="bx bx-lock me-2"></i>Password Baru
                            </label>
                            <div class="input-group">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="Masukkan password baru" required autofocus>
                                <button type="button" class="toggle-password" aria-label="Lihat password" title="Lihat password" data-target="password">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            <small class="text-muted d-block mt-1">
                                <i class="bx bx-info-circle me-1"></i>
                                Minimal 8 karakter
                            </small>
                        </div>

                        <div class="form-group">
                            <label for="password-confirm" class="form-label required-field">
                                <i class="bx bx-lock me-2"></i>Konfirmasi Password Baru
                            </label>
                            <div class="input-group">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" placeholder="Ulangi password baru" required>
                                <button type="button" class="toggle-password" aria-label="Lihat password" title="Lihat password" data-target="password-confirm">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <button type="submit" class="btn-submit">
                            <i class="bx bx-check me-2"></i>Reset Password
                        </button>
                    </form>
                </div>
                <div class="reset-password-footer">
                    <p>Ingat password Anda? <a href="{{ route('login') }}">Kembali ke halaman masuk</a></p>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function(){
            // Toggle password functionality
            document.querySelectorAll('.toggle-password').forEach(button => {
                button.addEventListener('click', function() {
                    const targetId = this.getAttribute('data-target');
                    const input = document.getElementById(targetId);
                    const icon = this.querySelector('i');
                    
                    if (input.type === 'password') {
                        input.type = 'text';
                        icon.classList.remove('fa-eye');
                        icon.classList.add('fa-eye-slash');
                    } else {
                        input.type = 'password';
                        icon.classList.remove('fa-eye-slash');
                        icon.classList.add('fa-eye');
                    }
                });
            });

            // Form loading state
            document.getElementById('resetPasswordForm')?.addEventListener('submit', function(e) {
                const btn = this.querySelector('.btn-submit');
                if (btn) { 
                    btn.classList.add('loading'); 
                    btn.innerHTML = ''; 
                }
            });
            
            // Remove validation feedback on input
            document.querySelectorAll('.form-control').forEach(input => {
                input.addEventListener('input', function() { 
                    this.classList.remove('is-invalid'); 
                });
            });
        });
    </script>
@endsection
