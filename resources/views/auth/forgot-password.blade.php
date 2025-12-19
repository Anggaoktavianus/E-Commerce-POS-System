@extends('layouts.app')

@section('content')
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        * { box-sizing: border-box; }
        
        /* Page Background */
        .forgot-password-page {
            background: linear-gradient(135deg, #f0f9f4 0%, #d4edda 100%);
            min-height: 100vh;
            padding: 2rem 0;
        }
        
        /* Modern Card Container */
        .forgot-password-modern {
            position: relative;
            max-width: 600px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }
        
        .forgot-password-card {
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
        .forgot-password-header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            padding: 2rem;
            text-align: center;
            color: white;
            position: relative;
            overflow: hidden;
        }
        
        .forgot-password-header::before {
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
        
        .forgot-password-header .logo {
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
        
        .forgot-password-header .logo img {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 50%;
        }
        
        .forgot-password-header h2 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: white !important;
            position: relative;
            z-index: 1;
        }
        
        .forgot-password-header p {
            font-size: 1rem;
            opacity: 0.95;
            margin: 0;
            position: relative;
            z-index: 1;
        }
        
        /* Body Section */
        .forgot-password-body {
            padding: 2.5rem;
        }
        
        @media (max-width: 768px) {
            .forgot-password-body {
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
        
        .alert-info {
            background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
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
        .forgot-password-footer {
            text-align: center;
            padding: 1.5rem 2rem;
            border-top: 2px solid #e8f5e9;
            background: linear-gradient(135deg, #f8fff9 0%, #ffffff 100%);
        }
        
        .forgot-password-footer p {
            color: #6c757d;
            font-size: 0.95rem;
            margin: 0;
        }
        
        .forgot-password-footer a {
            color: #28a745;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .forgot-password-footer a:hover {
            color: #20c997;
            text-decoration: underline;
        }
        
        /* Method Selection */
        .method-tabs {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
            border-bottom: 2px solid #e8f5e9;
        }
        
        .method-tab {
            flex: 1;
            padding: 0.75rem 1rem;
            text-align: center;
            background: #f8f9fa;
            border: none;
            border-bottom: 3px solid transparent;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 600;
            color: #6c757d;
            border-radius: 10px 10px 0 0;
        }
        
        .method-tab:hover {
            background: #e9ecef;
            color: #495057;
        }
        
        .method-tab.active {
            background: white;
            color: #28a745;
            border-bottom-color: #28a745;
        }
        
        .method-content {
            display: none;
        }
        
        .method-content.active {
            display: block;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .forgot-password-header {
                padding: 1.5rem 1rem;
            }
            
            .forgot-password-header h2 {
                font-size: 1.5rem;
            }
            
            .forgot-password-header .logo {
                width: 70px;
                height: 70px;
            }
            
            .forgot-password-header .logo img {
                width: 55px;
                height: 55px;
            }
            
            .method-tabs {
                flex-direction: column;
                gap: 0.5rem;
            }
            
            .method-tab {
                border-radius: 10px;
                border-bottom: 3px solid transparent;
            }
            
            .method-tab.active {
                border-bottom-color: #28a745;
            }
        }
    </style>
    
    <!-- Page Header with Breadcrumbs -->
    <div class="container-fluid page-header py-3 mb-0" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
        <div class="container py-3">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="h3 text-white mb-2">
                        <i class="bx bx-key me-2"></i>Lupa Password
                    </h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a class="text-white text-decoration-none" href="{{ route('home') }}">Beranda</a></li>
                            <li class="breadcrumb-item"><a class="text-white text-decoration-none" href="{{ route('login') }}">Masuk</a></li>
                            <li class="breadcrumb-item text-white active" aria-current="page">Lupa Password</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    
    <div class="forgot-password-page">
        <div class="forgot-password-modern">
            <div class="forgot-password-card">
                <div class="forgot-password-header">
                    <div class="logo">
                        <img src="{{ asset('storage/uploads/settings/pA4eU0uxj49qQm9buxJyHQVoP8u8u6MyfjDbSNic.png') }}" alt="Logo">
                    </div>
                    <h2>Lupa Password</h2>
                    <p>Pilih metode untuk menerima link reset password</p>
                </div>
                <div class="forgot-password-body">
                    @if(session('status'))
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>{{ session('status') }}
                        </div>
                    @endif

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

                    <!-- Method Selection Tabs -->
                    <div class="method-tabs">
                        <button type="button" class="method-tab active" data-method="whatsapp">
                            <i class="bx bxl-whatsapp me-2"></i>WhatsApp
                        </button>
                        <button type="button" class="method-tab" data-method="email">
                            <i class="bx bx-envelope me-2"></i>Email
                        </button>
                        
                    </div>

                    <!-- Email Method -->
                    <div class="method-content " id="email-method">
                        <form method="POST" action="{{ route('password.email') }}" id="forgotPasswordForm">
                            @csrf

                            <div class="form-group">
                                <label for="email" class="form-label required-field">
                                    <i class="bx bx-envelope me-2"></i>Alamat Email
                                </label>
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" placeholder="nama@email.com" required autofocus>
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                <small class="text-muted d-block mt-2">
                                    <i class="bx bx-info-circle me-1"></i>
                                    Kami akan mengirimkan link reset password ke email Anda
                                </small>
                            </div>

                            <button type="submit" class="btn-submit">
                                <i class="bx bx-paper-plane me-2"></i>Kirim via Email
                            </button>
                        </form>
                    </div>

                    <!-- WhatsApp Method -->
                    <div class="method-content active" id="whatsapp-method">
                        <form method="POST" action="{{ route('password.whatsapp') }}" id="forgotPasswordWhatsAppForm">
                            @csrf

                            <div class="form-group">
                                <label for="phone" class="form-label required-field">
                                    <i class="bx bxl-whatsapp me-2"></i>Nomor Telepon/WhatsApp
                                </label>
                                <input id="phone" type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone') }}" placeholder="08123456789" required>
                                @error('phone')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                <small class="text-muted d-block mt-2">
                                    <i class="bx bx-info-circle me-1"></i>
                                    Masukkan nomor telepon/WhatsApp yang terdaftar di akun Anda. Link reset password akan dikirim via WhatsApp.
                                </small>
                            </div>

                            <button type="submit" class="btn-submit">
                                <i class="bx bxl-whatsapp me-2"></i>Kirim via WhatsApp
                            </button>
                        </form>
                    </div>
                </div>
                <div class="forgot-password-footer">
                    <p>Ingat password Anda? <a href="{{ route('login') }}">Kembali ke halaman masuk</a></p>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function(){
            // Method tab switching
            const methodTabs = document.querySelectorAll('.method-tab');
            const methodContents = document.querySelectorAll('.method-content');
            
            methodTabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    const method = this.getAttribute('data-method');
                    
                    // Remove active class from all tabs and contents
                    methodTabs.forEach(t => t.classList.remove('active'));
                    methodContents.forEach(c => c.classList.remove('active'));
                    
                    // Add active class to clicked tab and corresponding content
                    this.classList.add('active');
                    document.getElementById(method + '-method').classList.add('active');
                });
            });
            
            // Form loading state for email form
            document.getElementById('forgotPasswordForm')?.addEventListener('submit', function(e) {
                const btn = this.querySelector('.btn-submit');
                if (btn) { 
                    btn.classList.add('loading'); 
                    btn.innerHTML = ''; 
                }
            });
            
            // Form loading state for WhatsApp form
            document.getElementById('forgotPasswordWhatsAppForm')?.addEventListener('submit', function(e) {
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
            
            // Phone number formatting
            const phoneInput = document.getElementById('phone');
            if (phoneInput) {
                phoneInput.addEventListener('input', function(e) {
                    // Remove non-numeric characters except +
                    let value = this.value.replace(/[^0-9+]/g, '');
                    this.value = value;
                });
            }
        });
    </script>
@endsection
