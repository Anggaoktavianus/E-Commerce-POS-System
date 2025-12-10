@extends('layouts.app')

@section('title', 'Kontak Kami - ' . config('app.name'))

@section('meta_description', 'Hubungi ' . config('app.name') . ' untuk informasi lebih lanjut. Kami siap membantu Anda dengan layanan pelanggan terbaik.')

@section('meta_keywords', 'kontak, hubungi kami, customer service, layanan pelanggan, ' . config('app.name') . ', alamat, telepon, email')

@section('og_image', asset('storage/defaults/og-contact.jpg'))

@section('content')
    

    <!-- Single Page Header start -->
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">{{ $siteSettings['contact_page_title'] ?? 'Contact' }}</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ $siteSettings['breadcrumb_home_text'] ?? 'Home' }}</a></li>
            <li class="breadcrumb-item"><a href="#">{{ $siteSettings['breadcrumb_pages_text'] ?? 'Pages' }}</a></li>
            <li class="breadcrumb-item active text-white">{{ $siteSettings['breadcrumb_contact_text'] ?? 'Contact' }}</li>
        </ol>
    </div>
    <!-- Single Page Header End -->

    <!-- Contact Start -->
    <div class="container-fluid contact py-5">
        <div class="container py-5">
            <div class="p-5 bg-light rounded">
                <div class="row g-4">
                    <div class="col-12">
                        <div class="text-center mx-auto" style="max-width: 700px;">
                            <h1 class="text-primary">{{ $siteSettings['contact_form_title'] ?? 'Get in touch' }}</h1>
                            <p class="mb-4">{{ $siteSettings['contact_form_description'] ?? 'The contact form is currently inactive. Get a functional and working contact form with Ajax & PHP in a few minutes. Just copy and paste the files, add a little code and you\'re done.' }} <a href="{{ $siteSettings['contact_form_link_url'] ?? 'https://htmlcodex.com/contact-form' }}">{{ $siteSettings['contact_form_link_text'] ?? 'Download Now' }}</a>.</p>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="h-100 rounded">
                            <iframe class="rounded w-100" style="height: 400px;" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d387191.33750346623!2d-73.97968099999999!3d40.6974881!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c24fa5d33f083b%3A0xc80b8f06e177fe62!2sNew%20York%2C%20NY%2C%20USA!5e0!3m2!1sen!2sbd!4v1694259649153!5m2!1sen!2sbd" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <form action="" class="">
                            <input type="text" class="w-100 form-control border-0 py-3 mb-4" placeholder="{{ $siteSettings['contact_name_placeholder'] ?? 'Your Name' }}">
                            <input type="email" class="w-100 form-control border-0 py-3 mb-4" placeholder="{{ $siteSettings['contact_email_placeholder'] ?? 'Enter Your Email' }}">
                            <textarea class="w-100 form-control border-0 mb-4" rows="5" cols="10" placeholder="{{ $siteSettings['contact_message_placeholder'] ?? 'Your Message' }}"></textarea>
                            <button class="w-100 btn form-control border-secondary py-3 bg-white text-primary " type="submit">{{ $siteSettings['contact_submit_button'] ?? 'Submit' }}</button>
                        </form>
                    </div>
                    <div class="col-lg-5">
                        <div class="d-flex p-4 rounded mb-4 bg-white">
                            <i class="fas fa-map-marker-alt fa-2x text-primary me-4"></i>
                            <div>
                                <h4>{{ $siteSettings['contact_address_title'] ?? 'Address' }}</h4>
                                <p class="mb-2">{{ $siteSettings['address'] ?? '123 Street New York.USA' }}</p>
                            </div>
                        </div>
                        <div class="d-flex p-4 rounded mb-4 bg-white">
                            <i class="fas fa-envelope fa-2x text-primary me-4"></i>
                            <div>
                                <h4>{{ $siteSettings['contact_email_title'] ?? 'Mail Us' }}</h4>
                                <p class="mb-2">{{ $siteSettings['email'] ?? 'info@example.com' }}</p>
                            </div>
                        </div>
                        <div class="d-flex p-4 rounded bg-white">
                            <i class="fa fa-phone-alt fa-2x text-primary me-4"></i>
                            <div>
                                <h4>{{ $siteSettings['contact_phone_title'] ?? 'Telephone' }}</h4>
                                <p class="mb-2">{{ $siteSettings['phone'] ?? '(+012) 3456 7890' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Contact End -->
@endsection
