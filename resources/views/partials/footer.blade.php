    <style>
        /* Modern Footer Styles */
        .modern-footer {
            background: linear-gradient(135deg, #1a2e2f 0%, #2c3a3b 50%, #1a2e2f 100%);
            position: relative;
            overflow: hidden;
        }
        
        .modern-footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 20% 50%, rgba(20, 116, 64, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(226, 175, 24, 0.1) 0%, transparent 50%);
            pointer-events: none;
        }
        
        .modern-footer .container {
            position: relative;
            z-index: 1;
        }
        
        /* Top Section - Newsletter & Social */
        .footer-top-section {
            border-bottom: 1px solid rgba(226, 175, 24, 0.3);
            padding-bottom: 2.5rem;
            margin-bottom: 3rem;
            position: relative;
        }
        
        .footer-top-section::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 2px;
            background: linear-gradient(90deg, transparent, rgba(226, 175, 24, 0.8), transparent);
        }
        
        .footer-brand {
            transition: all 0.3s ease;
        }
        
        .footer-brand:hover {
            transform: translateY(-3px);
        }
        
        .footer-brand h1 {
            background: linear-gradient(135deg, #147440 0%, #20c997 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 800;
            font-size: 2rem;
            letter-spacing: -0.5px;
            transition: all 0.3s ease;
        }
        
        .footer-brand:hover h1 {
            background: linear-gradient(135deg, #20c997 0%, #147440 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .footer-brand p {
            color: #e2af18;
            font-weight: 600;
            font-size: 0.95rem;
            letter-spacing: 0.5px;
        }
        
        /* Modern Newsletter Subscription */
        .newsletter-wrapper {
            position: relative;
            max-width: 100%;
        }
        
        .newsletter-input-group {
            display: flex;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 2px solid rgba(226, 175, 24, 0.3);
            border-radius: 50px;
            padding: 4px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        
        .newsletter-input-group:focus-within {
            border-color: rgba(226, 175, 24, 0.6);
            box-shadow: 0 6px 25px rgba(226, 175, 24, 0.2);
            transform: translateY(-2px);
        }
        
        .newsletter-input {
            flex: 1;
            background: transparent;
            border: none;
            outline: none;
            padding: 12px 24px;
            color: #fff;
            font-size: 0.95rem;
            border-radius: 50px;
        }
        
        .newsletter-input::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }
        
        .newsletter-btn {
            background: linear-gradient(135deg, #e2af18 0%, #f4c430 100%);
            border: none;
            padding: 12px 32px;
            border-radius: 50px;
            color: #1a2e2f;
            font-weight: 700;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.3s ease;
            white-space: nowrap;
            box-shadow: 0 4px 15px rgba(226, 175, 24, 0.3);
            position: relative;
            overflow: hidden;
        }
        
        .newsletter-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.5s ease;
        }
        
        .newsletter-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 25px rgba(226, 175, 24, 0.4);
            background: linear-gradient(135deg, #f4c430 0%, #e2af18 100%);
        }
        
        .newsletter-btn:hover::before {
            left: 100%;
        }
        
        .newsletter-btn:active {
            transform: translateY(0);
        }
        
        /* Social Media Icons */
        .social-icons-wrapper {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
            flex-wrap: wrap;
        }
        
        .social-icon {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 2px solid rgba(226, 175, 24, 0.3);
            color: #e2af18;
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }
        
        .social-icon::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(226, 175, 24, 0.2);
            transform: translate(-50%, -50%);
            transition: width 0.3s ease, height 0.3s ease;
        }
        
        .social-icon:hover {
            transform: translateY(-5px) scale(1.1);
            border-color: #e2af18;
            background: rgba(226, 175, 24, 0.2);
            box-shadow: 0 8px 20px rgba(226, 175, 24, 0.3);
        }
        
        .social-icon:hover::before {
            width: 100%;
            height: 100%;
        }
        
        .social-icon i {
            position: relative;
            z-index: 1;
            font-size: 1.1rem;
            transition: transform 0.3s ease;
        }
        
        .social-icon:hover i {
            transform: scale(1.2) rotate(5deg);
        }
        
        /* Footer Content Sections */
        .footer-item {
            transition: all 0.3s ease;
        }
        
        .footer-item h4 {
            color: #fff;
            font-weight: 700;
            font-size: 1.2rem;
            margin-bottom: 1.5rem;
            position: relative;
            padding-bottom: 0.75rem;
        }
        
        .footer-item h4::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 3px;
            background: linear-gradient(90deg, #147440, #e2af18);
            border-radius: 2px;
        }
        
        .footer-item p {
            color: rgba(255, 255, 255, 0.7);
            line-height: 1.8;
            margin-bottom: 1rem;
            transition: color 0.3s ease;
        }
        
        .footer-item p:hover {
            color: rgba(255, 255, 255, 0.9);
        }
        
        .footer-item a {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-block;
        }
        
        .footer-item a:hover {
            color: #e2af18;
            transform: translateX(5px);
        }
        
        /* Footer Links */
        .btn-link {
            color: rgba(255, 255, 255, 0.7) !important;
            text-decoration: none;
            padding: 8px 0;
            display: block;
            transition: all 0.3s ease;
            position: relative;
            padding-left: 0;
        }
        
        .btn-link::before {
            content: '→';
            position: absolute;
            left: -20px;
            opacity: 0;
            transition: all 0.3s ease;
            color: #e2af18;
        }
        
        .btn-link:hover {
            color: #e2af18 !important;
            transform: translateX(10px);
            padding-left: 20px;
        }
        
        .btn-link:hover::before {
            opacity: 1;
            left: 0;
        }
        
        /* Read More Button */
        .footer-read-more {
            background: transparent;
            border: 2px solid rgba(226, 175, 24, 0.5);
            color: #e2af18;
            padding: 10px 28px;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .footer-read-more::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(226, 175, 24, 0.2), transparent);
            transition: left 0.5s ease;
        }
        
        .footer-read-more:hover {
            border-color: #e2af18;
            background: rgba(226, 175, 24, 0.1);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(226, 175, 24, 0.3);
        }
        
        .footer-read-more:hover::before {
            left: 100%;
        }
        
        /* Contact Section */
        .contact-info {
            color: rgba(255, 255, 255, 0.7);
        }
        
        .contact-info a {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .contact-info a:hover {
            color: #e2af18;
            text-decoration: underline;
        }
        
        .payment-methods {
            margin-top: 1rem;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }
        
        .payment-methods:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(226, 175, 24, 0.3);
            transform: translateY(-2px);
        }
        
        .payment-methods img {
            filter: brightness(0.9);
            transition: filter 0.3s ease;
        }
        
        .payment-methods:hover img {
            filter: brightness(1.1);
        }
        
        /* Copyright Section */
        .modern-copyright {
            background: rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(10px);
            border-top: 1px solid rgba(226, 175, 24, 0.2);
            position: relative;
        }
        
        .modern-copyright::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(226, 175, 24, 0.5), transparent);
        }
        
        .modern-copyright a {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .modern-copyright a:hover {
            color: #e2af18;
        }
        
        /* Back to Top Button */
        .back-to-top {
            position: fixed;
            bottom: 100px; /* Moved up to avoid overlap with floating-cta */
            right: 30px;
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #147440 0%, #20c997 100%);
            border: 3px solid rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1.2rem;
            text-decoration: none;
            opacity: 0;
            visibility: hidden;
            transform: translateY(20px);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 999; /* Lower than floating-cta (1000) */
            box-shadow: 0 4px 15px rgba(20, 116, 64, 0.3);
        }
        
        .back-to-top.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
        
        .back-to-top:hover {
            transform: translateY(-5px) scale(1.1);
            box-shadow: 0 8px 25px rgba(20, 116, 64, 0.5);
            background: linear-gradient(135deg, #20c997 0%, #147440 100%);
        }
        
        .back-to-top:active {
            transform: translateY(-2px) scale(1.05);
        }
        
        .back-to-top i {
            transition: transform 0.3s ease;
        }
        
        .back-to-top:hover i {
            transform: translateY(-3px);
        }
        
        /* Responsive Design */
        @media (max-width: 991.98px) {
            .footer-top-section {
                text-align: center;
            }
            
            .footer-brand {
                margin-bottom: 2rem;
            }
            
            .newsletter-wrapper {
                margin-bottom: 2rem;
            }
            
            .social-icons-wrapper {
                justify-content: center;
            }
            
            .footer-item {
                margin-bottom: 2rem;
            }
            
            .back-to-top {
                bottom: 90px; /* Adjusted for mobile to avoid floating-cta */
                right: 20px;
                width: 45px;
                height: 45px;
            }
        }
        
        @media (max-width: 767.98px) {
            .newsletter-input-group {
                flex-direction: column;
                border-radius: 15px;
            }
            
            .newsletter-input {
                border-radius: 15px 15px 0 0;
                margin-bottom: 4px;
            }
            
            .newsletter-btn {
                border-radius: 0 0 15px 15px;
                width: 100%;
            }
            
            .social-icon {
                width: 44px;
                height: 44px;
            }
        }
    </style>

    <!-- Footer Start -->
    <div class="container-fluid modern-footer text-white-50 footer pt-5 mt-5">
        <div class="container py-5">
            <!-- Top Section: Brand, Newsletter, Social -->
            <div class="footer-top-section">
                <div class="row g-4 align-items-center">
                    <!-- Brand -->
                    <div class="col-lg-3 col-md-12">
                        <a href="{{ url('/') }}" class="footer-brand text-decoration-none">
                            <h1 class="mb-1">{{ $settings['brand_name'] ?? 'Samsae Store' }}</h1>
                            <p class="mb-0">{{ $settings['brand_tagline'] ?? 'Oleh-Oleh' }}</p>
                        </a>
                    </div>
                    
                    <!-- Newsletter Subscription -->
                    <div class="col-lg-6 col-md-12">
                        <div class="newsletter-wrapper">
                            <form class="newsletter-input-group" id="newsletter-form">
                                <input type="email" 
                                       class="newsletter-input" 
                                       placeholder="{{ $siteSettings['subscribe_placeholder'] ?? 'Email Anda' }}" 
                                       required>
                                <button type="submit" class="newsletter-btn">
                                    {{ $siteSettings['subscribe_button_text'] ?? 'Berlangganan Sekarang' }}
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Social Media Icons -->
                    <div class="col-lg-3 col-md-12">
                        <div class="social-icons-wrapper">
                            @php $links = isset($socialLinks) ? $socialLinks : collect(); @endphp
                            @forelse($links as $sl)
                                <a class="social-icon" href="{{ $sl->url }}" target="_blank" rel="noopener" aria-label="{{ $sl->name ?? 'Social Media' }}">
                                    <i class="{{ $sl->icon_class ?? 'fas fa-link' }}"></i>
                                </a>
                            @empty
                                <a class="social-icon" href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                                <a class="social-icon" href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                                <a class="social-icon" href="#" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                                <a class="social-icon" href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Main Content Sections -->
            <div class="row g-5">
                <!-- About Section -->
                <div class="col-lg-3 col-md-6">
                    <div class="footer-item">
                        <h4>{{ $settings['footer_about_title'] ?? 'Mengapa Orang Menyukai Kami!' }}</h4>
                        <p>{{ $settings['footer_about_text'] ?? 'typesetting, remaining essentially unchanged. It was popularised in the 1960s with the like Aldus PageMaker including of Lorem Ipsum.' }}</p>
                        @if(!empty($settings['footer_about_link_url']))
                          <a href="{{ $settings['footer_about_link_url'] }}" class="footer-read-more">
                              {{ $settings['footer_about_link_text'] ?? 'Baca Selengkapnya' }}
                          </a>
                        @endif
                    </div>
                </div>
                
                <!-- Footer Menu Items -->
                @php
                    $footerMenuItems = isset($footerMenus) ? $footerMenus : collect();
                    $showAllFooterMenus = filter_var($siteSettings['footer_menu_show_all'] ?? 'true', FILTER_VALIDATE_BOOLEAN);
                    $footerMenuColumns = (int)($siteSettings['footer_menu_columns'] ?? 2);
                    
                    if (!$showAllFooterMenus && $footerMenuItems->count() > $footerMenuColumns) {
                        $footerMenuItems = $footerMenuItems->take($footerMenuColumns);
                    }
                @endphp
                @foreach($footerMenuItems as $index => $footerMenu)
                    <div class="col-lg-3 col-md-6">
                        <div class="d-flex flex-column text-start footer-item">
                            <h4>{{ $footerMenu->name ?? 'Menu ' . ($index + 1) }}</h4>
                            @if($footerMenu && isset($footerMenu->links) && count($footerMenu->links) > 0)
                                @foreach($footerMenu->links as $link)
                                    @php
                                        $url = !empty($link->page_slug) ? route('pages.show', $link->page_slug) : ($link->url ?? '#');
                                    @endphp
                                    <a class="btn-link" href="{{ $url }}">{{ $link->label }}</a>
                                @endforeach
                            @else
                                <!-- Default links if no menu exists -->
                                @if($index == 0)
                                    <a class="btn-link" href="">Tentang Kami</a>
                                    <a class="btn-link" href="">Hubungi Kami</a>
                                    <a class="btn-link" href="">Kebijakan Privasi</a>
                                    <a class="btn-link" href="">Syarat & Ketentuan</a>
                                    <a class="btn-link" href="">Kebijakan Pengembalian</a>
                                    <a class="btn-link" href="">FAQ & Bantuan</a>
                                @elseif($index == 1)
                                    <a class="btn-link" href="">Akun Saya</a>
                                    <a class="btn-link" href="">Detail Toko</a>
                                    <a class="btn-link" href="">Keranjang Belanja</a>
                                    <a class="btn-link" href="">Daftar Keinginan</a>
                                    <a class="btn-link" href="">Riwayat Pesanan</a>
                                    <a class="btn-link" href="">Pesanan Internasional</a>
                                @else
                                    <a class="btn-link" href="">Layanan {{ $index + 1 }}</a>
                                    <a class="btn-link" href="">Fitur {{ $index + 1 }}</a>
                                    <a class="btn-link" href="">Dukungan {{ $index + 1 }}</a>
                                @endif
                            @endif
                        </div>
                    </div>
                @endforeach
                
                <!-- Contact Section -->
                <div class="col-lg-3 col-md-6">
                    <div class="footer-item">
                        <h4>{{ $siteSettings['contact_section_title'] ?? 'Kontak' }}</h4>
                        <div class="contact-info">
                            <p><i class="fas fa-map-marker-alt me-2" style="color: #e2af18;"></i> {{ $settings['address'] ?? '1429 Netus Rd, NY 48247' }}</p>
                            <p>
                                <i class="fas fa-envelope me-2" style="color: #e2af18;"></i> 
                                <a href="mailto:{{ $settings['email'] ?? 'Example@gmail.com' }}">{{ $settings['email'] ?? 'Example@gmail.com' }}</a>
                            </p>
                            <p>
                                <i class="fas fa-phone me-2" style="color: #e2af18;"></i> 
                                <a href="tel:{{ $settings['phone'] ?? '+0123 4567 8910' }}">{{ $settings['phone'] ?? '+0123 4567 8910' }}</a>
                            </p>
                        </div>
                        <div class="payment-methods">
                            <p class="mb-2" style="font-weight: 600; color: #e2af18;">{{ $siteSettings['contact_payment_title'] ?? 'Pembayaran Diterima' }}</p>
                            @php
                                $paymentImg = $settings['payment_image_path'] ?? 'fruitables/img/payment.png';
                            @endphp
                            <img src="{{ asset($paymentImg) }}" class="img-fluid" alt="payment methods">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Footer End -->

    <!-- Copyright Start -->
    <div class="container-fluid modern-copyright bg-dark py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-12 text-center">
                    <span class="text-light">
                        <a href="{{ url('/') }}">
                            <i class="fas fa-copyright text-light me-2"></i>
                            {{ $siteSettings['copyright_text'] ?? 'Samsae Store' }}
                        </a>, All right reserved.
                    </span>
                </div>
            </div>
        </div>
    </div>
    <!-- Copyright End -->

    <!-- Back to Top -->
    <a href="#" class="back-to-top" id="back-to-top" aria-label="Back to top">
        <i class="fa fa-arrow-up"></i>
    </a>

    <script>
        // Newsletter Form Handler
        document.addEventListener('DOMContentLoaded', function() {
            const newsletterForm = document.getElementById('newsletter-form');
            if (newsletterForm) {
                newsletterForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const input = this.querySelector('.newsletter-input');
                    const email = input.value.trim();
                    
                    if (email) {
                        // Add your newsletter subscription logic here
                        alert('Terima kasih! Email ' + email + ' telah berhasil didaftarkan untuk newsletter.');
                        input.value = '';
                    }
                });
            }
            
            // Back to Top Button
            const backToTop = document.getElementById('back-to-top');
            if (backToTop) {
                // Show/hide button based on scroll position
                window.addEventListener('scroll', function() {
                    if (window.pageYOffset > 300) {
                        backToTop.classList.add('show');
                    } else {
                        backToTop.classList.remove('show');
                    }
                });
                
                // Smooth scroll to top
                backToTop.addEventListener('click', function(e) {
                    e.preventDefault();
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                });
            }
        });
    </script>
