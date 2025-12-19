@extends('layouts.app')

@section('content')
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder@1.13.0/dist/Control.Geocoder.css" />

    <style>
        * { box-sizing: border-box; }
        
        /* Page Background */
        .register-page {
            background: linear-gradient(135deg, #f0f9f4 0%, #d4edda 100%);
            min-height: 100vh;
            padding: 2rem 0;
        }
        
        /* Reset Bootstrap form-select */
        .form-select {
            background-image: none !important;
            padding-right: 6px !important;
        }
        
        /* Modern Card Container */
        .register-modern {
            position: relative;
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }
        
        .register-card {
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
        .register-header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            padding: 2rem;
            text-align: center;
            color: white;
            position: relative;
            overflow: hidden;
        }
        
        .register-header::before {
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
        
        .register-header .logo {
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
        
        .register-header .logo img {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 50%;
        }
        
        .register-header h2 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: white !important;
            position: relative;
            z-index: 1;
        }
        
        .register-header p {
            font-size: 1rem;
            opacity: 0.95;
            margin: 0;
            position: relative;
            z-index: 1;
        }
        
        /* Body Section */
        .register-body {
            padding: 2.5rem;
        }
        
        @media (max-width: 768px) {
            .register-body {
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
        
        /* Form Sections */
        .auth-section {
            background: linear-gradient(135deg, #f8fff9 0%, #ffffff 100%);
            padding: 1.75rem;
            border-radius: 15px;
            border: 2px solid #e8f5e9;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
        }
        
        .auth-section:hover {
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.1);
            border-color: #28a745;
        }
        
        .auth-section h5 {
            color: #28a745;
            font-weight: 700;
            margin-bottom: 1rem;
            font-size: 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .auth-section h5::before {
            content: '';
            width: 4px;
            height: 24px;
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border-radius: 2px;
        }
        
        .auth-section hr {
            border: none;
            border-top: 2px solid #e8f5e9;
            margin: 1rem 0 1.5rem;
        }
        
        /* Form Controls */
        .form-control, .form-select {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            background: white;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #28a745;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
            background: white;
        }
        
        .form-control.is-invalid, .form-select.is-invalid {
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
        
        /* Address Search Box */
        #address-search {
            border-radius: 10px 0 0 10px;
        }
        
        #btn-clear-search {
            border-radius: 0 10px 10px 0;
        }
        
        .input-group-text {
            background: linear-gradient(135deg, #f8fff9 0%, #e8f5e9 100%);
            border: 2px solid #e9ecef;
            border-right: none;
            color: #28a745;
        }
        
        /* Map Container */
        #map {
            border-radius: 12px;
            border: 2px solid #e9ecef;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        }
        
        #map:hover {
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.15);
        }
        
        /* Buttons */
        .btn-success, .btn-sm.btn-success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
            color: white;
            border-radius: 10px;
            padding: 0.5rem 1.25rem;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(40, 167, 69, 0.3);
        }
        
        .btn-success:hover, .btn-sm.btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.4);
            color: white;
        }
        
        .btn-outline-primary, .btn-outline-secondary {
            border-radius: 10px;
            border-width: 2px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-outline-primary:hover {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border-color: #28a745;
            color: white;
            transform: translateY(-2px);
        }
        
        /* Submit Button */
        .btn-register {
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
        
        .btn-register:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(40, 167, 69, 0.4);
        }
        
        .btn-register:active {
            transform: translateY(-1px);
        }
        
        .btn-register.loading {
            pointer-events: none;
            opacity: 0.8;
        }
        
        .btn-register.loading::after {
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
        .register-footer {
            text-align: center;
            padding: 1.5rem 2rem;
            border-top: 2px solid #e8f5e9;
            background: linear-gradient(135deg, #f8fff9 0%, #ffffff 100%);
        }
        
        .register-footer p {
            color: #6c757d;
            font-size: 0.95rem;
            margin: 0;
        }
        
        .register-footer a {
            color: #28a745;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .register-footer a:hover {
            color: #20c997;
            text-decoration: underline;
        }
        
        /* Location Status */
        #location-status {
            font-size: 0.875rem;
            font-weight: 500;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .register-header {
                padding: 1.5rem 1rem;
            }
            
            .register-header h2 {
                font-size: 1.5rem;
            }
            
            .register-header .logo {
                width: 70px;
                height: 70px;
            }
            
            .register-header .logo img {
                width: 55px;
                height: 55px;
            }
            
            .auth-section {
                padding: 1.25rem;
            }
            
            .auth-section h5 {
                font-size: 1.1rem;
            }
        }
        
        /* Textarea */
        textarea.form-control {
            min-height: 100px;
            resize: vertical;
        }
        
        /* Select2 Customization */
        .select2-container--default .select2-selection--single {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            height: auto;
            padding: 0.5rem;
        }
        
        .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: #28a745;
        }
    </style>
    
    <!-- Page Header with Breadcrumbs -->
    <div class="container-fluid page-header py-3 mb-0" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
        <div class="container py-3">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="h3 text-white mb-2">
                        <i class="bx bx-store me-2"></i>Daftar Akun Mitra
                    </h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a class="text-white text-decoration-none" href="{{ route('home') }}">Beranda</a></li>
                            <li class="breadcrumb-item text-white active" aria-current="page">Daftar Mitra</li>
                        </ol>
                    </nav>
    </div>
            </div>
        </div>
    </div>
    
    <div class="register-page">
        <div class="register-modern">
            <div class="register-card">
                <div class="register-header">
                        <div class="logo">
                        <img src="{{ asset('storage/uploads/settings/pA4eU0uxj49qQm9buxJyHQVoP8u8u6MyfjDbSNic.png') }}" alt="Logo">
                        </div>
                        <h2>Daftar Mitra</h2>
                        <p>Silakan lengkapi data perusahaan Anda</p>
                    </div>
                <div class="register-body">
                    <form method="POST" action="{{ route('mitra.register') }}">
                        @csrf
                        <!-- Informasi Pribadi -->
                        <div class="mb-4 auth-section">
                            <h5>Informasi Pribadi</h5>
                            <hr>
                            <div class="row mb-3">
                                <label for="name" class="col-12 col-form-label">{{ __('Nama Lengkap') }}</label>
                                <div class="col-12">
                                    <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus placeholder="Masukkan nama anda">
                                    @error('name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="email" class="col-12 col-form-label">{{ __('Alamat Email') }}</label>
                                <div class="col-12">
                                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="Masukkan email">
                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">  
                                <div class="col-md-6">
                                    <label for="password" class="col-12 col-form-label">{{ __('Password') }}</label>
                                    <div class="col-12">
                                        <div class="input-group">
                                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="Masukkan password" required autocomplete="new-password">
                                            <button type="button" class="toggle-password" aria-label="Lihat password" title="Lihat password" data-target="password">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                        @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                   <label for="password-confirm" class="col-12 col-form-label">{{ __('Konfirmasi Password') }}</label>
                                    <div class="col-12">
                                        <div class="input-group">
                                            <input id="password-confirm" type="password" class="form-control" name="password_confirmation" placeholder="Konfirmasi password" required autocomplete="new-password">
                                            <button type="button" class="toggle-password" aria-label="Lihat password" title="Lihat password" data-target="password-confirm">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="phone" class="col-12 col-form-label">{{ __('Nomor Telepon') }}</label>
                                    <div class="col-12">
                                        <input id="phone" type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone') }}" placeholder="08123456789" required>
                                        @error('phone')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="address" class="col-12 col-form-label">{{ __('Alamat Lengkap') }} <span class="text-danger">*</span></label>
                                    <div class="col-12">
                                        <textarea id="address" class="form-control @error('address') is-invalid @enderror" name="address" placeholder="Jl. Contoh No. 123, RT/RW 001/002" required>{{ old('address') }}</textarea>
                                        <small class="text-muted">Masukkan alamat lengkap Anda</small>
                                        @error('address')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Maps Picker -->
                            <div class="row mb-3">
                                <div class="col-12">
                                    <label class="col-12 col-form-label">
                                        <i class="bx bx-map me-1"></i>Pinpoint Lokasi di Peta <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-12">
                                        <div class="mb-2">
                                            <button type="button" id="btn-get-location" class="btn btn-sm btn-success">
                                                <i class="bx bx-crosshair me-1"></i>Gunakan Lokasi GPS Saya
                                            </button>
                                            <span id="location-status" class="ms-2 small text-muted"></span>
                                        </div>
                                        <!-- Address Search Box -->
                                        <div class="mb-2">
                                            <div class="input-group">
                                                <span class="input-group-text bg-light">
                                                    <i class="bx bx-search"></i>
                                                </span>
                                                <input type="text" 
                                                       id="address-search" 
                                                       class="form-control" 
                                                       placeholder="Cari alamat (contoh: Jl. Sudirman No. 123, Semarang)" 
                                                       autocomplete="off">
                                                <button type="button" id="btn-clear-search" class="btn btn-outline-secondary" style="display:none;">
                                                    <i class="bx bx-x"></i>
                                                </button>
                                            </div>
                                            <small class="text-muted d-block mt-1">
                                                <i class="bx bx-info-circle me-1"></i>
                                                Ketik alamat lengkap untuk mencari lokasi di peta
                                            </small>
                                        </div>
                                        <div id="map" style="height: 400px; width: 100%; border-radius: 8px; border: 2px solid #dee2e6;"></div>
                                        <small class="text-muted d-block mt-2">
                                            <i class="bx bx-info-circle me-1"></i>
                                            Gunakan GPS, cari alamat, atau klik pada peta untuk menentukan lokasi alamat Anda. Pastikan marker berada di lokasi yang tepat.
                                        </small>
                                        <input type="hidden" id="latitude" name="latitude" value="{{ old('latitude') }}" required>
                                        <input type="hidden" id="longitude" name="longitude" value="{{ old('longitude') }}" required>
                                        @error('latitude')
                                            <span class="invalid-feedback d-block" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                        @error('longitude')
                                            <span class="invalid-feedback d-block" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="col-12 col-form-label">Provinsi *</label>
                                    <div class="col-12">
                                        <select id="loc_provinsi_id" name="loc_provinsi_id" class="form-select @error('loc_provinsi_id') is-invalid @enderror" required>
                                            <option value="">Pilih Provinsi</option>
                                        </select>
                                        @error('loc_provinsi_id')
                                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="col-12 col-form-label">Kab/Kota *</label>
                                    <div class="col-12">
                                        <select id="loc_kabkota_id" name="loc_kabkota_id" class="form-select @error('loc_kabkota_id') is-invalid @enderror" required>
                                            <option value="">Pilih Kab/Kota</option>
                                        </select>
                                        @error('loc_kabkota_id')
                                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="col-12 col-form-label">Kecamatan *</label>
                                    <div class="col-12">
                                        <select id="loc_kecamatan_id" name="loc_kecamatan_id" class="form-select @error('loc_kecamatan_id') is-invalid @enderror" required>
                                            <option value="">Pilih Kecamatan</option>
                                        </select>
                                        @error('loc_kecamatan_id')
                                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="col-12 col-form-label">Desa/Kelurahan *</label>
                                    <div class="col-12">
                                        <select id="loc_desa_id" name="loc_desa_id" class="form-select @error('loc_desa_id') is-invalid @enderror" required>
                                            <option value="">Pilih Desa/Kelurahan</option>
                                        </select>
                                        @error('loc_desa_id')
                                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Informasi Perusahaan -->
                        <div class="auth-section">
                            <h5><i class="bx bx-building me-2"></i>Informasi Perusahaan</h5>
                            <hr>
                            
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="company_name" class="form-label required-field">{{ __('Nama Perusahaan') }}</label>
                                    <input id="company_name" type="text" class="form-control @error('company_name') is-invalid @enderror" name="company_name" value="{{ old('company_name') }}" placeholder="PT. Contoh Perusahaan" required>
                                    @error('company_name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                            </div>

                                <div class="col-md-6">
                                    <label for="company_phone" class="form-label required-field">{{ __('Telepon Perusahaan') }}</label>
                                    <input id="company_phone" type="text" class="form-control @error('company_phone') is-invalid @enderror" name="company_phone" value="{{ old('company_phone') }}" placeholder="02112345678" required>
                                    @error('company_phone')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                            </div>

                                <div class="col-12">
                                    <label for="company_address" class="form-label required-field">{{ __('Alamat Perusahaan') }}</label>
                                    <textarea id="company_address" class="form-control @error('company_address') is-invalid @enderror" name="company_address" rows="3" placeholder="Jl. Kantor No. 456, RT/RW 003/004" required>{{ old('company_address') }}</textarea>
                                    @error('company_address')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                            </div>

                                <div class="col-md-6">
                                    <label for="npwp" class="form-label required-field">{{ __('NPWP') }}</label>
                                    <input id="npwp" type="text" class="form-control @error('npwp') is-invalid @enderror" name="npwp" value="{{ old('npwp') }}" placeholder="12.345.678.9-012.345" required>
                                    @error('npwp')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                            </div>
                        </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-register">
                                <i class="bx bx-store me-2"></i>{{ __('Daftar Sebagai Mitra') }}
                                </button>
                        </div>
                    </form>
                    </div>
                <div class="register-footer">
                        <p>Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
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
            document.querySelector('form')?.addEventListener('submit', function(e) {
                const btn = this.querySelector('.btn-register');
                if (btn) { 
                    btn.classList.add('loading'); 
                    btn.innerHTML = '<i class="bx bx-loader-alt bx-spin me-2"></i>Mendaftar...'; 
                }
            });
            
            // Remove validation feedback on input
            document.querySelectorAll('.form-control, .form-select').forEach(input => {
                input.addEventListener('input', function() { 
                    this.classList.remove('is-invalid'); 
                });
            });

            // Location loading functionality with jQuery and Select2
            const provSel = document.getElementById('loc_provinsi_id');
            const kabSel = document.getElementById('loc_kabkota_id');
            const kecSel = document.getElementById('loc_kecamatan_id');
            const desaSel = document.getElementById('loc_desa_id');
            const oldProv = '{{ old('loc_provinsi_id') }}';
            const oldKab = '{{ old('loc_kabkota_id') }}';
            const oldKec = '{{ old('loc_kecamatan_id') }}';
            const oldDesa = '{{ old('loc_desa_id') }}';

            fetch('{{ route('api.locations.provinsis') }}')
                .then(r => r.json()).then(json => {
                    const $prov = $('#loc_provinsi_id');
                    (json.data||[]).forEach(it=>{ $prov.append(new Option(it.name, it.id, false, false)); });
                    if (window.jQuery && $.fn.select2) { try { $prov.select2('destroy'); } catch(e){} $prov.select2({ width: '100%' }); }
                    if (oldProv) { $prov.val(oldProv).trigger('change'); loadKab(oldProv, true); }
                });
            $('#loc_provinsi_id').on('change select2:select', function(){ loadKab(this.value,false); });
            $('#loc_kabkota_id').on('change select2:select', function(){ loadKec(this.value,false); });
            $('#loc_kecamatan_id').on('change select2:select', function(){ loadDesa(this.value,false); });
            function resetSelect(sel, ph){ sel.innerHTML = `<option value="">${ph}</option>`; }
            function loadKab(pid, restoring){
                resetSelect(kabSel,'Pilih Kab/Kota'); resetSelect(kecSel,'Pilih Kecamatan'); resetSelect(desaSel,'Pilih Desa/Kelurahan');
                if(!pid) return;
                fetch(`{{ url('/api/locations/kabkotas') }}/${pid}`).then(r=>r.json()).then(j=>{
                    const $kab = $('#loc_kabkota_id');
                    (j.data||[]).forEach(it=>{ $kab.append(new Option(it.name, it.id, false, false)); });
                    if (window.jQuery && $.fn.select2) { try { $kab.select2('destroy'); } catch(e){} $kab.select2({ width: '100%' }); }
                    if(restoring && oldKab){ $kab.val(oldKab); loadKec(oldKab,true); }
                });
            }
            function loadKec(kid, restoring){
                resetSelect(kecSel,'Pilih Kecamatan'); resetSelect(desaSel,'Pilih Desa/Kelurahan');
                if(!kid) return;
                fetch(`{{ url('/api/locations/kecamatans') }}/${kid}`).then(r=>r.json()).then(j=>{
                    const $kec = $('#loc_kecamatan_id');
                    (j.data||[]).forEach(it=>{ $kec.append(new Option(it.name, it.id, false, false)); });
                    if(restoring && oldKec){ $kec.val(oldKec).trigger('change'); loadDesa(oldKec,true);} 
                });
            }
            function loadDesa(did, restoring){
                resetSelect(desaSel,'Pilih Desa/Kelurahan');
                if(!did) return;
                fetch(`{{ url('/api/locations/desas') }}/${did}`).then(r=>r.json()).then(j=>{
                    const $desa = $('#loc_desa_id');
                    (j.data||[]).forEach(it=>{ $desa.append(new Option(it.name, it.id, false, false)); });
                    if (window.jQuery && $.fn.select2) { try { $desa.select2('destroy'); } catch(e){} $desa.select2({ width: '100%' }); }
                    if(restoring && oldDesa){ $desa.val(oldDesa).trigger('change'); }
                });
            }
            // Initialize Select2
            $('#loc_provinsi_id, #loc_kabkota_id, #loc_kecamatan_id, #loc_desa_id').select2({ width: '100%' });

            // Leaflet Maps Picker (Free alternative to Google Maps)
            let map, marker;
            let defaultLat = -7.0051; // Semarang default
            let defaultLng = 110.4381;

            function initMap() {
                // Get initial coordinates from old input or use default
                const initialLat = parseFloat(document.getElementById('latitude').value) || defaultLat;
                const initialLng = parseFloat(document.getElementById('longitude').value) || defaultLng;

                // Initialize map
                map = L.map('map').setView([initialLat, initialLng], 15);

                // Add OpenStreetMap tiles
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors',
                    maxZoom: 19
                }).addTo(map);

                // Create custom icon
                const customIcon = L.icon({
                    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png',
                    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png',
                    iconSize: [25, 41],
                    iconAnchor: [12, 41],
                    popupAnchor: [1, -34],
                    shadowSize: [41, 41]
                });

                // Create marker
                marker = L.marker([initialLat, initialLng], {
                    draggable: true,
                    icon: customIcon
                }).addTo(map);

                // Update coordinates when marker is dragged
                marker.on('dragend', function() {
                    const position = marker.getLatLng();
                    document.getElementById('latitude').value = position.lat;
                    document.getElementById('longitude').value = position.lng;
                    updateLocationStatus('Lokasi diperbarui');
                });

                // Update marker position when map is clicked
                map.on('click', function(event) {
                    const clickedLocation = event.latlng;
                    marker.setLatLng(clickedLocation);
                    document.getElementById('latitude').value = clickedLocation.lat;
                    document.getElementById('longitude').value = clickedLocation.lng;
                    updateLocationStatus('Lokasi dipilih');
                });

                // Initialize geocoder for address search
                let geocoderControl = null;
                let geocoder = null;
                
                if (typeof L.Control.Geocoder !== 'undefined') {
                    geocoder = L.Control.Geocoder.nominatim();
                    
                    // Initialize geocoder (we'll use it programmatically via custom search box)
                    // Don't add control to map - we have custom search box above
                }

                // Address search functionality
                const addressSearchInput = document.getElementById('address-search');
                const clearSearchBtn = document.getElementById('btn-clear-search');
                let searchTimeout;

                if (addressSearchInput) {
                    // Search on input
                    addressSearchInput.addEventListener('input', function() {
                        const query = this.value.trim();
                        
                        if (query.length < 3) {
                            clearSearchBtn.style.display = 'none';
                            return;
                        }
                        
                        clearSearchBtn.style.display = 'inline-block';
                        
                        // Debounce search
                        clearTimeout(searchTimeout);
                        searchTimeout = setTimeout(function() {
                            if (geocoder && query.length >= 3) {
                                performGeocodeSearch(query);
                            }
                        }, 500);
                    });

                    // Search on Enter key
                    addressSearchInput.addEventListener('keypress', function(e) {
                        if (e.key === 'Enter') {
                            e.preventDefault();
                            const query = this.value.trim();
                            if (query.length >= 3) {
                                performGeocodeSearch(query);
                            }
                        }
                    });

                    // Clear search
                    clearSearchBtn.addEventListener('click', function() {
                        addressSearchInput.value = '';
                        this.style.display = 'none';
                    });
                }

                // Perform geocode search
                function performGeocodeSearch(query) {
                    if (!geocoder) {
                        updateLocationStatus('Fitur pencarian alamat belum siap. Silakan tunggu sebentar.', 'warning');
                        return;
                    }

                    updateLocationStatus('Mencari alamat...', 'info');
                    
                    geocoder.geocode(query, function(results) {
                        if (!results || results.length === 0) {
                            updateLocationStatus('Alamat tidak ditemukan. Coba dengan kata kunci yang lebih spesifik.', 'warning');
                            return;
                        }

                        // Use first result
                        const result = results[0];
                        const location = result.center;
                        const address = result.name;

                        // Update marker and map
                        marker.setLatLng(location);
                        map.setView(location, 15);

                        // Update form fields
                        document.getElementById('latitude').value = location.lat;
                        document.getElementById('longitude').value = location.lng;

                        // Update address field if empty
                        const addressField = document.getElementById('address');
                        if (addressField && !addressField.value) {
                            addressField.value = address;
                        }

                        updateLocationStatus('Alamat ditemukan: ' + address, 'success');
                    }, {
                        bounds: map.getBounds(), // Limit search to current map bounds
                        limit: 5 // Limit results
                    });
                }

                // Set initial coordinates if available
                if (document.getElementById('latitude').value && document.getElementById('longitude').value) {
                    updateLocationStatus('Lokasi tersimpan');
                } else {
                    // If no coordinates, prepare for auto-request
                    console.log('No coordinates found, will auto-request GPS');
                }
            }

            // Get user's GPS location (manual button click - always allowed)
            function getCurrentLocation() {
                const btn = document.getElementById('btn-get-location');
                const statusEl = document.getElementById('location-status');
                
                if (!navigator.geolocation) {
                    updateLocationStatus('GPS tidak didukung oleh browser Anda', 'error');
                    return;
                }

                btn.disabled = true;
                btn.innerHTML = '<i class="bx bx-loader-alt bx-spin me-1"></i>Mendeteksi lokasi...';
                updateLocationStatus('Meminta izin akses GPS...', 'info');

                try {
                    navigator.geolocation.getCurrentPosition(
                        function(position) {
                            const lat = position.coords.latitude;
                            const lng = position.coords.longitude;
                            
                            // Update marker and map
                            if (marker && map) {
                                marker.setLatLng([lat, lng]);
                                map.setView([lat, lng], 15);
                            }
                            
                            // Update form fields
                            document.getElementById('latitude').value = lat;
                            document.getElementById('longitude').value = lng;
                            
                            updateLocationStatus('Lokasi GPS berhasil dideteksi!', 'success');
                            btn.disabled = false;
                            btn.innerHTML = '<i class="bx bx-crosshair me-1"></i>Gunakan Lokasi GPS Saya';
                        },
                        function(error) {
                            let errorMsg = 'Gagal mendapatkan lokasi GPS.';
                            switch(error.code) {
                                case error.PERMISSION_DENIED:
                                    errorMsg = 'Izin akses lokasi ditolak. Silakan izinkan akses lokasi di pengaturan browser.';
                                    break;
                                case error.POSITION_UNAVAILABLE:
                                    errorMsg = 'Lokasi GPS tidak tersedia. Pastikan GPS aktif dan Anda berada di area terbuka. Atau klik pada peta untuk set lokasi manual.';
                                    break;
                                case error.TIMEOUT:
                                    errorMsg = 'Waktu permintaan lokasi habis. Silakan coba lagi atau klik pada peta untuk set lokasi manual.';
                                    break;
                            }
                            if (error.message && error.message.includes('permissions policy')) {
                                errorMsg = 'Browser memblokir akses GPS. Silakan cek pengaturan browser atau gunakan HTTPS.';
                            }
                            updateLocationStatus(errorMsg, 'error');
                            btn.disabled = false;
                            btn.innerHTML = '<i class="bx bx-crosshair me-1"></i>Gunakan Lokasi GPS Saya';
                        },
                        {
                            enableHighAccuracy: false, // Try with less accuracy first (works better indoors)
                            timeout: 15000, // Increase timeout
                            maximumAge: 60000 // Accept cached position up to 1 minute old
                        }
                    );
                } catch (e) {
                    console.error('Error in getCurrentLocation:', e);
                    updateLocationStatus('Error saat meminta akses GPS: ' + e.message, 'error');
                    btn.disabled = false;
                    btn.innerHTML = '<i class="bx bx-crosshair me-1"></i>Gunakan Lokasi GPS Saya';
                }
            }

            // Update location status message
            function updateLocationStatus(message, type = 'info') {
                const statusEl = document.getElementById('location-status');
                const colors = {
                    'success': 'text-success',
                    'error': 'text-danger',
                    'info': 'text-info'
                };
                statusEl.className = 'ms-2 small ' + (colors[type] || 'text-muted');
                statusEl.textContent = message;
                
                // Auto-hide after 5 seconds for success/info
                if (type !== 'error') {
                    setTimeout(() => {
                        if (statusEl.textContent === message) {
                            statusEl.textContent = '';
                        }
                    }, 5000);
                }
            }

            // Auto-request GPS permission function
            function autoRequestGPS() {
                // Wait a bit more to ensure map is fully ready
                setTimeout(function() {
                    if (!navigator.geolocation) {
                        console.log('GPS not supported');
                        return; // GPS not supported
                    }

                    // Check if coordinates are already set (from old input)
                    const latInput = document.getElementById('latitude');
                    const lngInput = document.getElementById('longitude');
                    
                    if (latInput && lngInput && latInput.value && lngInput.value) {
                        console.log('Coordinates already set, skipping auto-request');
                        return; // Already has coordinates
                    }

                    // Check if map and marker are ready
                    if (typeof map === 'undefined' || !map || typeof marker === 'undefined' || !marker) {
                        console.log('Map not ready yet, retrying...');
                        setTimeout(autoRequestGPS, 500);
                        return;
                    }

                    console.log('Auto-requesting GPS permission...');
                    // Show info message
                    updateLocationStatus('Meminta izin akses GPS...', 'info');

                    // Check permissions policy first
                    if (navigator.permissions && navigator.permissions.query) {
                        navigator.permissions.query({name: 'geolocation'}).then(function(result) {
                            console.log('Geolocation permission status:', result.state);
                            if (result.state === 'denied') {
                                updateLocationStatus('Izin GPS ditolak. Silakan klik tombol GPS untuk mengaktifkan.', 'error');
                                return;
                            }
                            // If granted or prompt, proceed with getCurrentPosition
                            // Add small delay to ensure permissions policy is processed
                            setTimeout(requestGPSPosition, 100);
                        }).catch(function(err) {
                            console.log('Permission query error:', err);
                            // Fallback to direct request with delay
                            setTimeout(requestGPSPosition, 100);
                        });
                    } else {
                        // Fallback for browsers that don't support permissions API
                        // Add delay to ensure permissions policy is processed
                        setTimeout(requestGPSPosition, 100);
                    }

                    function requestGPSPosition() {
                        try {
                            navigator.geolocation.getCurrentPosition(
                                function(position) {
                                    console.log('GPS position received:', position.coords);
                                    const lat = position.coords.latitude;
                                    const lng = position.coords.longitude;
                                    
                                    // Update marker and map
                                    if (marker && map) {
                                        marker.setLatLng([lat, lng]);
                                        map.setView([lat, lng], 15);
                                    }
                                    
                                    // Update form fields
                                    if (latInput) latInput.value = lat;
                                    if (lngInput) lngInput.value = lng;
                                    
                                    updateLocationStatus('Lokasi GPS otomatis terdeteksi!', 'success');
                                },
                                function(error) {
                                    console.log('GPS error:', error);
                                    let errorMsg = '';
                                    
                                    if (error.code === error.PERMISSION_DENIED) {
                                        errorMsg = 'Izin GPS diperlukan. Silakan klik tombol GPS di atas.';
                                    } else if (error.code === error.POSITION_UNAVAILABLE) {
                                        // GPS tidak tersedia - mungkin di dalam ruangan atau GPS tidak aktif
                                        errorMsg = 'Lokasi GPS tidak tersedia. Pastikan GPS aktif dan Anda berada di area terbuka. Atau klik pada peta untuk set lokasi manual.';
                                    } else if (error.code === error.TIMEOUT) {
                                        errorMsg = 'Waktu permintaan GPS habis. Silakan coba lagi atau klik pada peta untuk set lokasi manual.';
                                    } else if (error.message && error.message.includes('permissions policy')) {
                                        errorMsg = 'Browser memblokir akses GPS. Silakan klik tombol GPS untuk mengaktifkan.';
                                    } else {
                                        // Unknown error - don't show message, user can click manually
                                        errorMsg = '';
                                    }
                                    
                                    if (errorMsg) {
                                        updateLocationStatus(errorMsg, error.code === error.PERMISSION_DENIED ? 'info' : 'warning');
                                    } else {
                                        updateLocationStatus('', 'info'); // Clear status
                                    }
                                },
                                {
                                    enableHighAccuracy: false, // Try with less accuracy first (works better indoors)
                                    timeout: 15000, // Increase timeout
                                    maximumAge: 60000 // Accept cached position up to 1 minute old
                                }
                            );
                        } catch (e) {
                            console.error('Error requesting GPS:', e);
                            updateLocationStatus('Error saat meminta akses GPS. Silakan klik tombol GPS atau set lokasi manual di peta.', 'error');
                        }
                    }
                }, 1000); // Wait 1 second after map init
            }

            // Flag to track if GPS has been requested
            let gpsRequested = false;

            // Load Leaflet library
            if (typeof L === 'undefined') {
                const leafletScript = document.createElement('script');
                leafletScript.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
                leafletScript.onload = function() {
                    console.log('Leaflet loaded');
                    // Load geocoder plugin
                    const geocoderScript = document.createElement('script');
                    geocoderScript.src = 'https://unpkg.com/leaflet-control-geocoder@1.13.0/dist/Control.Geocoder.js';
                    geocoderScript.onload = function() {
                        console.log('Geocoder loaded, initializing map...');
                        initMap();
                        // Request GPS on first user interaction (scroll, mouse move, click, touch)
                        setupGPSOnInteraction();
                    };
                    document.head.appendChild(geocoderScript);
                };
                document.head.appendChild(leafletScript);
            } else {
                console.log('Leaflet already loaded');
                initMap();
                // Request GPS on first user interaction
                setupGPSOnInteraction();
            }

            // Setup GPS request on first user interaction
            function setupGPSOnInteraction() {
                const events = ['scroll', 'mousemove', 'click', 'touchstart', 'keydown'];
                const requestGPSOnce = function() {
                    if (!gpsRequested) {
                        gpsRequested = true;
                        console.log('User interaction detected, requesting GPS...');
                        autoRequestGPS();
                        // Remove all event listeners after first trigger
                        events.forEach(event => {
                            document.removeEventListener(event, requestGPSOnce);
                            window.removeEventListener(event, requestGPSOnce);
                        });
                    }
                };

                // Add event listeners for user interaction
                events.forEach(event => {
                    document.addEventListener(event, requestGPSOnce, { once: true, passive: true });
                    window.addEventListener(event, requestGPSOnce, { once: true, passive: true });
                });

                // Don't try immediately - wait for user interaction
                // This avoids Permissions Policy violation
            }

            // GPS button event
            document.getElementById('btn-get-location').addEventListener('click', getCurrentLocation);

            // Form validation - ensure coordinates are set
            document.querySelector('form').addEventListener('submit', function(e) {
                const lat = document.getElementById('latitude').value;
                const lng = document.getElementById('longitude').value;
                
                if (!lat || !lng) {
                    e.preventDefault();
                    alert('Silakan pinpoint lokasi Anda di peta terlebih dahulu!');
                    document.getElementById('map').scrollIntoView({ behavior: 'smooth', block: 'center' });
                    return false;
                }
            });
        });
    </script>
@endsection
