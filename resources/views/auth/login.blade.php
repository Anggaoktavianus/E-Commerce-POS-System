@extends('layouts.app')

@section('content')
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-p1CmS4G6hQ4nq1cS2JxJYBq8f+J6yE2Qq0KpG/1o4kR3mPG2k5zj8z2cJzHk2s5b0uS1y8rCw4E3rKq1trrj3Q==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        * { box-sizing: border-box; }
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

        .login-container { position: relative; z-index: 10; width: 100%; max-width: 650px; padding: 0; }
        .login-card { background: rgba(255,255,255,0.95); backdrop-filter: blur(10px); border-radius: 20px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); overflow: hidden; animation: slideUp .6s ease-out; }
        @keyframes slideUp { from{opacity:0; transform:translateY(50px)} to{opacity:1; transform:translateY(0)} }

        .login-header { background: linear-gradient(135deg, #1ea563 0%, #147440 100%); padding: 15px 10px; text-align: center; color: #fff; }
        .login-header .logo { width: 80px; height: 80px; background: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; box-shadow: 0 5px 15px rgba(0,0,0,0.2); }
        .login-header .logo i { font-size: 40px; background: linear-gradient(135deg, #1ea563 0%, #147440 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .login-header h2 { font-size: 28px; font-weight: 700; margin-bottom: 5px; color:#ffff !important; }
        .login-header p { font-size: 14px; opacity: .9; margin: 0; }

        .login-body { padding: 40px 30px; }
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
            color: #a0aec0;
            cursor: pointer;
            padding: 0;
            z-index: 6;
            transition: color .2s ease;
        }
        .input-group .toggle-password:hover { color: #4a5568; }
        .input-group .toggle-password:focus { outline: none; color: #147440; }
        .form-label { font-weight: 600; color: #4a5568; margin-bottom: 8px; font-size: 14px; display: block; }
        .input-icon { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #a0aec0; z-index: 5; }
        .input-group { border-radius: 12px !important; }
        .input-group .form-control { border-radius: 12px !important; }
        .form-control { border: 2px solid #e2e8f0; border-radius: 12px; padding: 15px 15px 15px 45px; font-size: 15px; transition: all .3s ease; background: #f7fafc; }
        .form-control:focus { border-color: #147440; box-shadow: 0 0 0 4px rgba(20,116,64,0.12); outline: none; background: #fff; }
        .form-control.is-invalid { border-color: #ff6b6b; background: #fff5f5; border-radius: 12px; }
        .invalid-feedback { font-size: 13px; margin-top: 8px; display: block; }

        .form-check { display: flex; align-items: center; margin-bottom: 25px; }
        .form-check-input { width: 20px; height: 20px; border: 2px solid #cbd5e0; border-radius: 5px; cursor: pointer; margin-right: 10px; }
        .form-check-input:checked { background-color: #147440; border-color: #147440; }
        .form-check-label { color: #4a5568; font-size: 14px; cursor: pointer; user-select: none; }

        .btn-login { width: 100%; background: linear-gradient(135deg, #1ea563 0%, #147440 100%); border: none; padding: 15px; font-size: 16px; font-weight: 600; border-radius: 12px; color: #fff; cursor: pointer; transition: all .3s ease; box-shadow: 0 4px 15px rgba(20,116,64,.35); position: relative; overflow: hidden; }
        .btn-login::before { content: ''; position: absolute; top: 0; left: -100%; width: 100%; height: 100%; background: rgba(255,255,255,.2); transition: left .5s ease; }
        .btn-login:hover::before { left: 100%; }
        .btn-login:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(20,116,64,.45); }
        .btn-login:active { transform: translateY(0); }

        .login-footer { text-align: center; padding: 20px 30px 30px; border-top: 1px solid #e2e8f0; }
        .login-footer p { color: #718096; font-size: 14px; margin: 0; }
        .login-footer a { color: #147440; text-decoration: none; font-weight: 600; transition: color .3s ease; }
        .login-footer a:hover { color: #0f5c33; text-decoration: underline; }

        @media (max-width: 576px) {
            .login-modern { padding: 1.25rem .75rem; }
            .login-header { padding: 30px 20px; }
            .login-body { padding: 30px 20px; }
            .login-header h2 { font-size: 24px; }
        }

        .btn-login.loading { pointer-events: none; opacity: .7; }
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
                        <h2>Selamat Datang</h2>
                        <p>Silakan masuk ke akun Anda</p>
                    </div>
                    <div class="login-body">
                        @if(session('error'))
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('login') }}" id="loginForm">
                            @csrf

                            <div class="form-group">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope"></i> Email
                                </label>
                                <div class="input-group">
                                    <i class="fas fa-envelope input-icon"></i>
                                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" placeholder="nama@email.com" required autofocus>
                                </div>
                                @error('email')
                                    <span class="invalid-feedback">
                                        <i class="fas fa-exclamation-triangle"></i> {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock"></i> Password
                                </label>
                                <div class="input-group">
                                    <i class="fas fa-lock input-icon"></i>
                                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="Masukkan password" required>
                                    <button type="button" class="toggle-password" aria-label="Lihat password" title="Lihat password">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <span class="invalid-feedback">
                                        <i class="fas fa-exclamation-triangle"></i> {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label" for="remember">Ingat saya</label>
                            </div>

                            <button type="submit" class="btn-login"><i class="fas fa-sign-in-alt"></i> Masuk</button>
                        </form>
                    </div>
                    <div class="login-footer">
                        <p>Belum punya akun? Daftar sebagai <a href="{{ route('customer.register.form') }}"> Customer </a> Atau <a href="{{ route('mitra.register.form') }}"> Mitra </a> Sekarang</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const btn = this.querySelector('.btn-login');
            if (btn) { btn.classList.add('loading'); btn.innerHTML = ''; }
        });
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('input', function() { this.classList.remove('is-invalid'); });
        });
        // Password visibility toggle
        const toggleBtn = document.querySelector('.toggle-password');
        const passwordInput = document.getElementById('password');
        if (toggleBtn && passwordInput) {
            toggleBtn.addEventListener('click', function() {
                const isPassword = passwordInput.type === 'password';
                passwordInput.type = isPassword ? 'text' : 'password';
                toggleBtn.innerHTML = isPassword ? '<i class="fas fa-eye-slash"></i>' : '<i class="fas fa-eye"></i>';
                toggleBtn.setAttribute('aria-label', isPassword ? 'Sembunyikan password' : 'Lihat password');
                toggleBtn.setAttribute('title', isPassword ? 'Sembunyikan password' : 'Lihat password');
            });
        }
    </script>
@endsection
