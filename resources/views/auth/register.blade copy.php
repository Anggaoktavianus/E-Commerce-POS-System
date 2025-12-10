@extends('layouts.app')

@section('content')
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <style>
        * { box-sizing: border-box; }
        
        /* Reset Bootstrap form-select */
        .form-select {
            background-image: none !important;
            padding-right: 6px !important;
        }
        
        .login-modern {
            position: relative;
            min-height: 80vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
            background: linear-gradient(135deg, #1ea563 0%, #147440 100%);
            overflow: hidden;
            border-radius: 12px;
        }
        .login-modern::before,
        .login-modern::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            animation: float 8s ease-in-out infinite;
        }
        .login-modern::before { width: 300px; height: 300px; top: -100px; left: -100px; }
        .login-modern::after { width: 400px; height: 400px; bottom: -150px; right: -150px; animation-delay: 2s; }
        @keyframes float { 0%,100%{transform:translateY(0) rotate(0)} 50%{transform:translateY(-20px) rotate(180deg)} }

        .login-container { position: relative; z-index: 10; width: 100%; max-width: 1200px; padding: 0; }
        .login-card { background: rgba(255,255,255,0.95); backdrop-filter: blur(10px); border-radius: 20px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); overflow: hidden; animation: slideUp .6s ease-out; }
        @keyframes slideUp { from{opacity:0; transform:translateY(50px)} to{opacity:1; transform:translateY(0)} }

        .login-header { background: linear-gradient(135deg, #1ea563 0%, #147440 100%); padding: 15px 10px; text-align: center; color: #fff; }
        .login-header .logo { width: 80px; height: 80px; background: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; box-shadow: 0 5px 15px rgba(0,0,0,0.2); }
        .login-header .logo i { font-size: 40px; background: linear-gradient(135deg, #1ea563 0%, #147440 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .login-header h2 { font-size: 28px; font-weight: 700; margin-bottom: 5px; color:#ffff !important; }
        .login-header p { font-size: 14px; opacity: .9; margin: 0; }

        .login-body { padding: 25px 20px; }
        .alert { border-radius: 12px; border: none; padding: 15px; margin-bottom: 25px; animation: shake .5s ease-in-out; }
        @keyframes shake { 0%,100%{transform:translateX(0)} 25%{transform:translateX(-10px)} 75%{transform:translateX(10px)} }
        .alert-danger { background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%); color: #fff; }

        .form-group { margin-bottom: 25px; position: relative; }
        .input-group .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #666;
            cursor: pointer;
            z-index: 10;
            padding: 5px;
        }
        .input-group .toggle-password:hover { color: #147440; }
        .form-control, .form-select {
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 12px 15px;
            font-size: 14px;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }
        .form-control:focus, .form-select:focus {
            border-color: #147440;
            box-shadow: 0 0 0 3px rgba(20, 116, 64, 0.1);
            background: #fff;
        }
        .form-control.is-invalid, .form-select.is-invalid {
            border-color: #dc3545;
            background: #fff;
        }
        .invalid-feedback {
            color: #dc3545;
            font-size: 12px;
            margin-top: 5px;
            display: block;
        }
        .btn-login {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #1ea563 0%, #147440 100%);
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .btn-login:hover { transform: translateY(-2px); box-shadow: 0 10px 25px rgba(20, 116, 64, 0.3); }
        .btn-login:active { transform: translateY(0); }
        .btn-login.loading { pointer-events: none; opacity: .7; }
        .btn-login.loading::after { content: ''; position: absolute; width: 20px; height: 20px; top: 50%; left: 50%; margin-left: -10px; margin-top: -10px; border: 3px solid rgba(255,255,255,.3); border-radius: 50%; border-top-color: #fff; animation: spin .8s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* Grid layout for register form */
        .register-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        @media (max-width: 991.98px) {
            .register-grid {
                grid-template-columns: 1fr;
            }
        }
        
        /* Form styling for grid layout */
        .register-grid .form-control,
        .register-grid .form-select {
            padding: 4px 8px;
            font-size: 11px;
            height: 28px;
        }
        .register-grid .invalid-feedback {
            font-size: 9px;
            margin-top: 2px;
        }
        .register-grid .auth-section {
            background: #f8f9fa;
            padding: 12px;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
        }
        .register-grid .auth-section h5 {
            color: #2d3748;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 14px;
        }
        .register-grid .auth-section hr {
            border: none;
            border-top: 2px solid #e2e8f0;
            margin-bottom: 10px;
        }
        .register-grid .row {
            margin-bottom: 6px !important;
        }
        .register-grid .col-form-label {
            font-size: 10px;
            margin-bottom: 2px;
        }
        .register-grid textarea {
            height: 40px !important;
            resize: none;
        }
        /* Custom styling for location selects */
        .register-grid select.form-select,
        .register-grid .form-select {
            height: 28px !important;
            padding: 2px 6px !important;
            font-size: 9px !important;
            line-height: 1.1 !important;
            min-height: 28px !important;
            border-radius: 6px !important;
        }
        .register-grid select.form-select option,
        .register-grid .form-select option {
            font-size: 9px !important;
            padding: 1px !important;
        }
        /* Extra compact for location rows */
        .register-grid .row:has(select),
        .register-grid .row:has(.form-select) {
            margin-bottom: 3px !important;
        }
    </style>
    <div class="container-fluid page-header ">
        
    </div>
    <div class="container">
        <div class="login-modern">
            <div class="login-container">
                <div class="login-card">
                    <div class="login-header">
                        <div class="logo">
                            <img src="{{ asset('storage/uploads/settings/pA4eU0uxj49qQm9buxJyHQVoP8u8u6MyfjDbSNic.png') }}" alt="Logo" style="width: 50px; height: 50px; object-fit: cover;">
                        </div>
                        <h2>Daftar Customer</h2>
                        <p>Buat akun baru untuk mulai berbelanja</p>
                    </div>
                    <div class="login-body">
        .btn-login.loading::after { content: ''; position: absolute; width: 20px; height: 20px; top: 50%; left: 50%; margin-left: -10px; margin-top: -10px; border: 3px solid rgba(255,255,255,.3); border-radius: 50%; border-top-color: #fff; animation: spin .8s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }
    </style>
    <div class="container-fluid page-header ">
        
    </div>
    <div class="container">
        <div class="login-modern">
            <div class="login-container">
                <div class="login-card">
                    <div class="login-header">
                        <div class="logo">
                            <img src="{{ asset('storage/uploads/settings/pA4eU0uxj49qQm9buxJyHQVoP8u8u6MyfjDbSNic.png') }}" alt="Logo" style="width: 48px; height: auto; object-fit: contain;">
                        </div>
                        <h2>Daftar Customer</h2>
                        <p>Silakan lengkapi data pribadi Anda</p>
                    </div>
                    <div class="login-body">
                        <form method="POST" action="{{ route('customer.register') }}" class="auth-grid-form">
                            @csrf
                            <div class="mb-3">
                                <label for="name" class="form-label">Nama Lengkap</label>
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autofocus>
                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required>
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required>
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                            <input id="password_confirmation" type="password" class="form-control" name="password_confirmation" required>
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">No. Telepon (opsional)</label>
                            <input id="phone" type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone') }}">
                            @error('phone')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Alamat (opsional)</label>
                            <textarea id="address" class="form-control @error('address') is-invalid @enderror" name="address">{{ old('address') }}</textarea>
                            @error('address')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Kab/Kota *</label>
                                <select id="loc_kabkota_id" name="loc_kabkota_id" class="form-select @error('loc_kabkota_id') is-invalid @enderror" required>
                                    <option value="">Pilih Kab/Kota</option>
                                </select>
                                @error('loc_kabkota_id')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Kecamatan *</label>
                                <select id="loc_kecamatan_id" name="loc_kecamatan_id" class="form-select @error('loc_kecamatan_id') is-invalid @enderror" required>
                                    <option value="">Pilih Kecamatan</option>
                                </select>
                                @error('loc_kecamatan_id')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Desa/Kelurahan *</label>
                                <select id="loc_desa_id" name="loc_desa_id" class="form-select @error('loc_desa_id') is-invalid @enderror" required>
                                    <option value="">Pilih Desa/Kelurahan</option>
                                </select>
                                @error('loc_desa_id')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>

                        <div class="d-grid gap-2 auth-actions">
                            <button type="submit" class="btn-login">
                                <i class="fas fa-user-plus"></i> Daftar Customer
                            </button>
                        </div>
                    </form>
                    </div>
                    <div class="login-footer">
                        <p>Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a></p>
                    </div>
                </div>
    </div>
    </div>
    <script>
        document.getElementById('loginForm')?.addEventListener('submit', function(e) {
            const btn = this.querySelector('.btn-login');
            if (btn) { btn.classList.add('loading'); btn.innerHTML = ''; }
        });
        document.querySelectorAll('.form-control, .form-select').forEach(input => {
            input.addEventListener('input', function() { this.classList.remove('is-invalid'); });
        });
    </script>
@endsection
