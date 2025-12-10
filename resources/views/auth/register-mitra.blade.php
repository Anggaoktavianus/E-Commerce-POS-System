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

        /* Grid layout for register-mitra form */
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
            padding: 10px;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
        }
        .register-grid .auth-section h5 {
            color: #2d3748;
            font-weight: 600;
            margin-bottom: 6px;
            font-size: 13px;
        }
        .register-grid .auth-section hr {
            border: none;
            border-top: 2px solid #e2e8f0;
            margin-bottom: 8px;
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
        /* Force compact for all selects */
        .register-grid .col-md-6 select,
        .register-grid .col-md-6 .form-select {
            height: 28px !important;
            padding: 2px 6px !important;
            font-size: 9px !important;
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
                            <img src="{{ asset('storage/uploads/settings/pA4eU0uxj49qQm9buxJyHQVoP8u8u6MyfjDbSNic.png') }}" alt="Logo" style="width: 48px; height: auto; object-fit: contain;">
                        </div>
                        <h2>Daftar Mitra</h2>
                        <p>Silakan lengkapi data perusahaan Anda</p>
                    </div>
                    <div class="login-body">
                    <form method="POST" action="{{ route('mitra.register') }}">
                        @csrf
                        <div class="register-grid">
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
                                    <label for="address" class="col-12 col-form-label">{{ __('Alamat Lengkap') }}</label>
                                    <div class="col-12">
                                        <textarea id="address" class="form-control @error('address') is-invalid @enderror" name="address" placeholder="Jl. Contoh No. 123, RT/RW 001/002" required>{{ old('address') }}</textarea>
                                        @error('address')
                                            <span class="invalid-feedback" role="alert">
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
                        <div class="mb-4 auth-section">
                            <h5>Informasi Perusahaan</h5>
                            <hr>
                            <div class="row mb-3">
                                <label for="company_name" class="col-12 col-form-label">{{ __('Nama Perusahaan') }}</label>
                                <div class="col-12">
                                    <input id="company_name" type="text" class="form-control @error('company_name') is-invalid @enderror" name="company_name" value="{{ old('company_name') }}" placeholder="PT. Contoh Perusahaan" required>
                                    @error('company_name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="company_address" class="col-12 col-form-label">{{ __('Alamat Perusahaan') }}</label>
                                <div class="col-12">
                                    <textarea id="company_address" class="form-control @error('company_address') is-invalid @enderror" name="company_address" placeholder="Jl. Kantor No. 456, RT/RW 003/004" required>{{ old('company_address') }}</textarea>
                                    @error('company_address')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="company_phone" class="col-12 col-form-label">{{ __('Telepon Perusahaan') }}</label>
                                <div class="col-12">
                                    <input id="company_phone" type="text" class="form-control @error('company_phone') is-invalid @enderror" name="company_phone" value="{{ old('company_phone') }}" placeholder="02112345678" required>
                                    @error('company_phone')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="npwp" class="col-12 col-form-label">{{ __('NPWP') }}</label>
                                <div class="col-12">
                                    <input id="npwp" type="text" class="form-control @error('npwp') is-invalid @enderror" name="npwp" value="{{ old('npwp') }}" placeholder="12.345.678.9-012.345" required>
                                    @error('npwp')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        </div>

                        <div class="row mb-0 auth-actions">
                            <div class="col-12">
                                <button type="submit" class="btn-login">
                                    <i class="fas fa-user-plus"></i> Daftar Sebagai Mitra
                                </button>
                            </div>
                        </div>
                    </form>
                    </div>
                    <div class="login-footer">
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
                const btn = this.querySelector('.btn-login');
                if (btn) { 
                    btn.classList.add('loading'); 
                    btn.innerHTML = ''; 
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
        });
    </script>
@endsection
