    <!-- Footer Start -->
    <div class="container-fluid bg-dark text-white-50 footer pt-5 mt-5">
        <div class="container py-5">
            <div class="pb-4 mb-4" style="border-bottom: 1px solid rgba(226, 175, 24, 0.5) ;">
                <div class="row g-4">
                    <div class="col-lg-3">
                        <a href="#">
                            <h1 class="text-primary mb-0">{{ $settings['brand_name'] ?? 'Samsae Store' }}</h1>
                            <p class="text-secondary mb-0">{{ $settings['brand_tagline'] ?? 'Oleh-Oleh' }}</p>
                        </a>
                    </div>
                    <div class="col-lg-6">
                        <div class="position-relative mx-auto">
                            <input class="form-control border-0 w-100 py-3 px-4 rounded-pill" type="number" placeholder="{{ $siteSettings['subscribe_placeholder'] ?? 'Your Email' }}">
                            <button type="submit" class="btn btn-primary border-0 border-secondary py-3 px-4 position-absolute rounded-pill text-white" style="top: 0; right: 0;">{{ $siteSettings['subscribe_button_text'] ?? 'Subscribe Now' }}</button>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="d-flex justify-content-end pt-3">
                            @php $links = isset($socialLinks) ? $socialLinks : collect(); @endphp
                            @forelse($links as $sl)
                                <a class="btn btn-outline-secondary me-2 btn-md-square rounded-circle" href="{{ $sl->url }}" target="_blank" rel="noopener">
                                    <i class="{{ $sl->icon_class ?? 'fas fa-link' }}"></i>
                                </a>
                            @empty
                                <a class="btn btn-outline-secondary me-2 btn-md-square rounded-circle" href="#"><i class="fab fa-twitter"></i></a>
                                <a class="btn btn-outline-secondary me-2 btn-md-square rounded-circle" href="#"><i class="fab fa-facebook-f"></i></a>
                                <a class="btn btn-outline-secondary me-2 btn-md-square rounded-circle" href="#"><i class="fab fa-youtube"></i></a>
                                <a class="btn btn-outline-secondary btn-md-square rounded-circle" href="#"><i class="fab fa-linkedin-in"></i></a>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
            <div class="row g-5">
                <div class="col-lg-3 col-md-6">
                    <div class="footer-item">
                        <h4 class="text-light mb-3">{{ $settings['footer_about_title'] ?? 'Why People Like us!' }}</h4>
                        <p class="mb-4">{{ $settings['footer_about_text'] ?? 'typesetting, remaining essentially unchanged. It was popularised in the 1960s with the like Aldus PageMaker including of Lorem Ipsum.' }}</p>
                        @if(!empty($settings['footer_about_link_url']))
                          <a href="{{ $settings['footer_about_link_url'] }}" class="btn border-secondary py-2 px-4 rounded-pill text-primary">{{ $settings['footer_about_link_text'] ?? 'Read More' }}</a>
                        @endif
                    </div>
                </div>
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
                            <h4 class="text-light mb-3">{{ $footerMenu->name ?? 'Menu ' . ($index + 1) }}</h4>
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
                                    <a class="btn-link" href="">About Us</a>
                                    <a class="btn-link" href="">Contact Us</a>
                                    <a class="btn-link" href="">Privacy Policy</a>
                                    <a class="btn-link" href="">Terms & Condition</a>
                                    <a class="btn-link" href="">Return Policy</a>
                                    <a class="btn-link" href="">FAQs & Help</a>
                                @elseif($index == 1)
                                    <a class="btn-link" href="">My Account</a>
                                    <a class="btn-link" href="">Shop details</a>
                                    <a class="btn-link" href="">Shopping Cart</a>
                                    <a class="btn-link" href="">Wishlist</a>
                                    <a class="btn-link" href="">Order History</a>
                                    <a class="btn-link" href="">International Orders</a>
                                @else
                                    <a class="btn-link" href="">Service {{ $index + 1 }}</a>
                                    <a class="btn-link" href="">Feature {{ $index + 1 }}</a>
                                    <a class="btn-link" href="">Support {{ $index + 1 }}</a>
                                @endif
                            @endif
                        </div>
                    </div>
                @endforeach
                <div class="col-lg-3 col-md-6">
                    <div class="footer-item">
                        <h4 class="text-light mb-3">{{ $siteSettings['contact_section_title'] ?? 'Contact' }}</h4>
                        <p>Alamat: {{ $settings['address'] ?? '1429 Netus Rd, NY 48247' }}</p>
                        <p>Email: {{ $settings['email'] ?? 'Example@gmail.com' }}</p>
                        <p>Phone: {{ $settings['phone'] ?? '+0123 4567 8910' }}</p>
                        <p>{{ $siteSettings['contact_payment_title'] ?? 'Payment Accepted' }}</p>
                        @php
                            $paymentImg = $settings['payment_image_path'] ?? 'fruitables/img/payment.png';
                        @endphp
                        <img src="{{ asset($paymentImg) }}" class="img-fluid" alt="payment methods">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Footer End -->

    <!-- Copyright Start -->
    <div class="container-fluid copyright bg-dark py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                    <span class="text-light">
                        <a href="#">
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
    <a href="#" class="btn btn-primary border-3 border-primary rounded-circle back-to-top"><i class="fa fa-arrow-up"></i></a>
