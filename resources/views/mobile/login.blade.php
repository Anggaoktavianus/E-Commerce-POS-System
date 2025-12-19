@extends('mobile.layouts.app')

@section('title', 'Login')

@section('content')
<div style="padding: 2rem 1rem; min-height: calc(100vh - 140px); display: flex; align-items: center; justify-content: center;">
  <div style="width: 100%; max-width: 400px;">
    <!-- Logo/Header -->
    <div style="text-align: center; margin-bottom: 2rem;">
      <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #147440 0%, #1a9c52 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; box-shadow: 0 4px 15px rgba(20, 116, 64, 0.3);">
        <i class="bx bx-user" style="font-size: 2.5rem; color: white;"></i>
      </div>
      <h2 style="font-size: 1.5rem; font-weight: 700; color: #333; margin-bottom: 0.5rem;">Selamat Datang</h2>
      <p style="color: #666; font-size: 0.875rem;">Masuk ke akun Anda</p>
    </div>

    <!-- Login Form -->
    <div style="background: white; border-radius: 16px; padding: 1.5rem; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
      @if($errors->any())
        <div style="background: #ffebee; color: #c62828; padding: 0.75rem; border-radius: 8px; margin-bottom: 1rem; font-size: 0.875rem;">
          <i class="bx bx-error-circle"></i> {{ $errors->first() }}
        </div>
      @endif

      @if(session('success'))
        <div style="background: #e8f5e9; color: #2e7d32; padding: 0.75rem; border-radius: 8px; margin-bottom: 1rem; font-size: 0.875rem;">
          <i class="bx bx-check-circle"></i> {{ session('success') }}
        </div>
      @endif

      <form method="POST" action="{{ route('login') }}">
        @csrf
        <input type="hidden" name="redirect" value="{{ request('redirect', route('mobile.account')) }}">

        <div style="margin-bottom: 1rem;">
          <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #333; margin-bottom: 0.5rem;">
            Email atau Username
          </label>
          <div style="position: relative;">
            <i class="bx bx-envelope" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #999; font-size: 1.125rem;"></i>
            <input type="text" 
                   name="email" 
                   value="{{ old('email') }}"
                   required
                   autofocus
                   placeholder="Masukkan email atau username"
                   style="width: 100%; padding: 0.875rem 1rem 0.875rem 3rem; border: 2px solid #e0e0e0; border-radius: 10px; font-size: 0.875rem; transition: border-color 0.3s;">
          </div>
        </div>

        <div style="margin-bottom: 1.5rem;">
          <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #333; margin-bottom: 0.5rem;">
            Password
          </label>
          <div style="position: relative;">
            <i class="bx bx-lock" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #999; font-size: 1.125rem;"></i>
            <input type="password" 
                   name="password" 
                   required
                   placeholder="Masukkan password"
                   id="passwordInput"
                   style="width: 100%; padding: 0.875rem 1rem 0.875rem 3rem; border: 2px solid #e0e0e0; border-radius: 10px; font-size: 0.875rem; transition: border-color 0.3s;">
            <button type="button" 
                    onclick="togglePassword()"
                    style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); background: none; border: none; color: #999; font-size: 1.125rem; cursor: pointer;">
              <i class="bx bx-hide" id="togglePasswordIcon"></i>
            </button>
          </div>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; font-size: 0.875rem;">
          <label style="display: flex; align-items: center; gap: 0.5rem; color: #666; cursor: pointer;">
            <input type="checkbox" name="remember" style="width: 18px; height: 18px; accent-color: #147440;">
            <span>Ingat saya</span>
          </label>
          <a href="{{ route('password.request') }}" style="color: #147440; text-decoration: none; font-weight: 500;">
            Lupa password?
          </a>
        </div>

        <button type="submit" 
                style="width: 100%; background: linear-gradient(135deg, #147440 0%, #1a9c52 100%); color: white; border: none; padding: 1rem; border-radius: 10px; font-weight: 600; font-size: 1rem; cursor: pointer; box-shadow: 0 4px 15px rgba(20, 116, 64, 0.3); transition: transform 0.2s;">
          <i class="bx bx-log-in"></i> Masuk
        </button>
      </form>

      <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #e0e0e0; text-align: center;">
        <p style="color: #666; font-size: 0.875rem; margin-bottom: 1rem;">
          Belum punya akun?
        </p>
        <a href="{{ route('mobile.register') }}" 
           style="display: block; background: white; color: #147440; border: 2px solid #147440; padding: 0.875rem; border-radius: 10px; text-decoration: none; font-weight: 600; font-size: 0.875rem; text-align: center;">
          <i class="bx bx-user-plus"></i> Daftar Sekarang
        </a>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  function togglePassword() {
    const input = document.getElementById('passwordInput');
    const icon = document.getElementById('togglePasswordIcon');
    
    if (input.type === 'password') {
      input.type = 'text';
      icon.classList.remove('bx-hide');
      icon.classList.add('bx-show');
    } else {
      input.type = 'password';
      icon.classList.remove('bx-show');
      icon.classList.add('bx-hide');
    }
  }

  // Add focus styles
  document.querySelectorAll('input[type="text"], input[type="password"]').forEach(input => {
    input.addEventListener('focus', function() {
      this.style.borderColor = '#147440';
      this.style.outline = 'none';
    });
    
    input.addEventListener('blur', function() {
      this.style.borderColor = '#e0e0e0';
    });
  });
</script>
@endpush
