@extends('layouts.app')

@section('content')
    <style>
        .login-card {
            border: 0;
            border-radius: 1rem;
            box-shadow: 0 10px 30px rgba(2, 8, 23, 0.10);
            overflow: hidden;
        }
        .login-card .login-card-header {
            background: #147440 !important;
            border: 0;
            font-weight: 600;
            letter-spacing: .2px;
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem 1.25rem;
            min-height: 72px;
        }
        .login-card .login-card-header img { height: 40px; width: auto; }
        .login-card .login-card-header span { font-size: 1.15rem; font-weight: 700; }
        @media (min-width: 768px) {
            .login-card .login-card-header { padding: 1.1rem 1.5rem; min-height: 78px; }
            .login-card .login-card-header img { height: 44px; }
            .login-card .login-card-header span { font-size: 1.25rem; }
        }
        .login-card .card-body {
            padding: 2rem 1.75rem;
        }
        /* Custom login button */
        .btn-login {
            background-color: #147440;
            border-color: #147440;
            color: #ffffff;
            border-radius: 12px;
            padding-top: .65rem;
            padding-bottom: .65rem;
            box-shadow: 0 8px 16px rgba(20, 116, 64, 0.25);
            transition: transform .12s ease, box-shadow .2s ease, opacity .2s ease;
        }
        .btn-login:hover { opacity: .95; transform: translateY(-1px); box-shadow: 0 12px 22px rgba(20, 116, 64, 0.3); }
        .btn-login:active { transform: translateY(0); box-shadow: 0 6px 14px rgba(20, 116, 64, 0.22); }
        .btn-login:focus { box-shadow: 0 0 0 .2rem rgba(20, 116, 64, 0.25); }

        /* Icon-only button for password toggle */
        .btn-icon { display: inline-flex; align-items: center; gap: .25rem; }
        .btn-icon svg { width: 18px; height: 18px; }
    </style>
    <!-- Page Header -->
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">Login</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Beranda</a></li>
            <li class="breadcrumb-item active text-white">Login</li>
        </ol>
    </div>

    <div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card login-card">
                <div class="card-header bg-primary text-white login-card-header">
                    <img src="{{ asset('storage/uploads/settings/pA4eU0uxj49qQm9buxJyHQVoP8u8u6MyfjDbSNic.png') }}" alt="Logo" style="height:28px; width:auto;" loading="lazy">
                    <span>Login</span>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autofocus>
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required>
                                <button type="button" class="btn btn-outline-secondary btn-icon" id="togglePassword" aria-label="Lihat password" title="Lihat password">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.036 12.322a1.012 1.012 0 010-.644C3.423 7.51 7.36 5 12 5c4.64 0 8.577 2.51 9.964 6.678.07.207.07.437 0 .644C20.577 16.49 16.64 19 12 19c-4.64 0-8.577-2.51-9.964-6.678z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </button>
                            </div>
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3 form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="remember">
                                Ingat saya
                            </label>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-login w-100">Login</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var pw = document.getElementById('password');
            var btn = document.getElementById('togglePassword');
            if (pw && btn) {
                btn.addEventListener('click', function () {
                    var isPassword = pw.getAttribute('type') === 'password';
                    pw.setAttribute('type', isPassword ? 'text' : 'password');
                    btn.setAttribute('aria-label', isPassword ? 'Sembunyikan password' : 'Lihat password');
                    btn.setAttribute('title', isPassword ? 'Sembunyikan password' : 'Lihat password');
                    btn.innerHTML = isPassword
                        ? '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.295 15.591 7.24 18 12 18c1.38 0 2.692-.25 3.887-.707M6.227 6.227A10.45 10.45 0 0112 6c4.76 0 8.705 2.409 10.066 6-.51 1.352-1.36 2.56-2.45 3.54M6.227 6.227L3 3m3.227 3.227l11.546 11.546M21 21l-3.384-3.384"/></svg>'
                        : '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.036 12.322a1.012 1.012 0 010-.644C3.423 7.51 7.36 5 12 5c4.64 0 8.577 2.51 9.964 6.678.07.207.07.437 0 .644C20.577 16.49 16.64 19 12 19c-4.64 0-8.577-2.51-9.964-6.678z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>';
                });
            }
        });
    </script>
@endsection

