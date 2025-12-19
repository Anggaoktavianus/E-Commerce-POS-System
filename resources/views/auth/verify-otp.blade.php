@extends('layouts.app')

@section('content')
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

    <style>
        * { box-sizing: border-box; }
        
        /* Page Background */
        .verify-otp-page {
            background: linear-gradient(135deg, #f0f9f4 0%, #d4edda 100%);
            min-height: 100vh;
            padding: 2rem 0;
        }
        
        /* Modern Card Container */
        .verify-otp-modern {
            position: relative;
            max-width: 500px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }
        
        .verify-otp-card {
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
        .verify-otp-header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            padding: 2rem;
            text-align: center;
            color: white;
            position: relative;
            overflow: hidden;
        }
        
        .verify-otp-header::before {
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
        
        .verify-otp-header .icon {
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
        
        .verify-otp-header .icon i {
            font-size: 2.5rem;
            color: #28a745;
        }
        
        .verify-otp-header h2 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: white !important;
            position: relative;
            z-index: 1;
        }
        
        .verify-otp-header p {
            font-size: 1rem;
            opacity: 0.95;
            margin: 0;
            position: relative;
            z-index: 1;
        }
        
        /* Body Section */
        .verify-otp-body {
            padding: 2.5rem;
        }
        
        @media (max-width: 768px) {
            .verify-otp-body {
                padding: 1.5rem;
            }
        }
        
        /* Alert Styling */
        .alert {
            border-radius: 12px;
            border: none;
            padding: 1rem 1.25rem;
            margin-bottom: 1.5rem;
        }
        
        .alert-success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
        }
        
        .alert-danger {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
            color: white;
        }
        
        /* OTP Input Container */
        .otp-input-container {
            display: flex;
            justify-content: center;
            gap: 0.75rem;
            margin: 2rem 0;
        }
        
        .otp-input {
            width: 55px;
            height: 65px;
            text-align: center;
            font-size: 1.75rem;
            font-weight: 700;
            border: 2px solid #e9ecef;
            border-radius: 12px;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }
        
        .otp-input:focus {
            border-color: #28a745;
            background: white;
            box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.1);
            outline: none;
        }
        
        .otp-input.filled {
            border-color: #28a745;
            background: white;
        }
        
        /* Form Controls */
        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #28a745;
            box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.1);
        }
        
        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .required-field::after {
            content: ' *';
            color: #dc3545;
        }
        
        /* Button */
        .btn-submit {
            width: 100%;
            padding: 0.875rem;
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 1.5rem;
        }
        
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(40, 167, 69, 0.3);
        }
        
        .btn-submit:active {
            transform: translateY(0);
        }
        
        .btn-submit:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }
        
        /* Footer */
        .verify-otp-footer {
            text-align: center;
            padding: 1.5rem 2.5rem;
            background: #f8f9fa;
            border-top: 1px solid #e9ecef;
        }
        
        .verify-otp-footer a {
            color: #28a745;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        
        .verify-otp-footer a:hover {
            color: #20c997;
        }
        
        /* Resend OTP */
        .resend-otp {
            text-align: center;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e9ecef;
        }
        
        .resend-otp a {
            color: #28a745;
            text-decoration: none;
            font-weight: 500;
        }
        
        .resend-otp a:hover {
            text-decoration: underline;
        }
        
        .resend-otp .countdown {
            color: #6c757d;
            font-size: 0.9rem;
        }
    </style>

    <div class="verify-otp-page">
        <div class="verify-otp-modern">
            <div class="verify-otp-card">
                <!-- Header -->
                <div class="verify-otp-header">
                    <div class="icon">
                        <i class="bx bx-shield-quarter"></i>
                    </div>
                    <h2>Verifikasi OTP</h2>
                    <p>Masukkan kode OTP yang telah dikirim</p>
                </div>

                <!-- Body -->
                <div class="verify-otp-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            <i class="bx bx-check-circle me-2"></i>{{ session('status') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <i class="bx bx-error-circle me-2"></i>
                            <ul class="mb-0" style="list-style: none; padding-left: 0;">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.otp.verify.post') }}" id="otpVerifyForm">
                        @csrf
                        
                        <input type="hidden" name="email" value="{{ $email ?? old('email') }}">

                        <div class="mb-4">
                            <label class="form-label">
                                <i class="bx bx-key me-2"></i>Kode OTP
                            </label>
                            <div class="otp-input-container">
                                <input type="text" class="otp-input" name="otp[]" maxlength="1" pattern="[0-9]" inputmode="numeric" autocomplete="off" required>
                                <input type="text" class="otp-input" name="otp[]" maxlength="1" pattern="[0-9]" inputmode="numeric" autocomplete="off" required>
                                <input type="text" class="otp-input" name="otp[]" maxlength="1" pattern="[0-9]" inputmode="numeric" autocomplete="off" required>
                                <input type="text" class="otp-input" name="otp[]" maxlength="1" pattern="[0-9]" inputmode="numeric" autocomplete="off" required>
                                <input type="text" class="otp-input" name="otp[]" maxlength="1" pattern="[0-9]" inputmode="numeric" autocomplete="off" required>
                                <input type="text" class="otp-input" name="otp[]" maxlength="1" pattern="[0-9]" inputmode="numeric" autocomplete="off" required>
                            </div>
                            <input type="hidden" name="otp" id="otp-combined">
                            <small class="text-muted d-block mt-2 text-center">
                                <i class="bx bx-info-circle me-1"></i>Kode OTP berlaku selama 10 menit
                            </small>
                        </div>

                        <button type="submit" class="btn-submit" id="submitBtn">
                            <i class="bx bx-check me-2"></i>Verifikasi OTP
                        </button>
                    </form>

                    <div class="resend-otp">
                        <p class="mb-2">Tidak menerima kode?</p>
                        <a href="{{ route('password.request') }}">Kirim ulang OTP</a>
                    </div>
                </div>

                <!-- Footer -->
                <div class="verify-otp-footer">
                    <a href="{{ route('login') }}">
                        <i class="bx bx-arrow-back me-1"></i>Kembali ke Login
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const otpInputs = document.querySelectorAll('.otp-input');
            const form = document.getElementById('otpVerifyForm');
            const otpCombined = document.getElementById('otp-combined');

            // Auto-focus and move to next input
            otpInputs.forEach((input, index) => {
                input.addEventListener('input', function(e) {
                    // Only allow numbers
                    this.value = this.value.replace(/[^0-9]/g, '');
                    
                    if (this.value.length === 1) {
                        this.classList.add('filled');
                        // Move to next input
                        if (index < otpInputs.length - 1) {
                            otpInputs[index + 1].focus();
                        }
                    } else {
                        this.classList.remove('filled');
                    }
                });

                input.addEventListener('keydown', function(e) {
                    // Handle backspace
                    if (e.key === 'Backspace' && this.value === '' && index > 0) {
                        otpInputs[index - 1].focus();
                        otpInputs[index - 1].value = '';
                        otpInputs[index - 1].classList.remove('filled');
                    }
                });

                input.addEventListener('paste', function(e) {
                    e.preventDefault();
                    const pastedData = e.clipboardData.getData('text').replace(/[^0-9]/g, '').slice(0, 6);
                    if (pastedData.length === 6) {
                        pastedData.split('').forEach((char, i) => {
                            if (otpInputs[i]) {
                                otpInputs[i].value = char;
                                otpInputs[i].classList.add('filled');
                            }
                        });
                        otpInputs[5].focus();
                    }
                });
            });

            // Form submission
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Combine OTP inputs
                const otpValue = Array.from(otpInputs).map(input => input.value).join('');
                
                if (otpValue.length !== 6) {
                    alert('Silakan masukkan 6 digit kode OTP');
                    return;
                }

                // Set combined OTP value
                otpCombined.value = otpValue;

                // Disable submit button
                const submitBtn = document.getElementById('submitBtn');
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memverifikasi...';

                // Submit form
                form.submit();
            });

            // Focus first input on load
            otpInputs[0].focus();
        });
    </script>
@endsection
