@extends('layouts.app')

@section('title', $settings['site_name'] ?? $settings['brand_name'] ?? config('app.name'))

@section('meta_description', $settings['meta_description'] ?? ($settings['site_description'] ?? ($settings['site_name'] ?? $settings['brand_name'] ?? config('app.name')) . ' - Toko online terpercaya untuk kebutuhan sehari-hari. Produk segar, berkualitas dengan harga terjangkau.'))

@section('meta_keywords', $settings['meta_keywords'] ?? 'toko online, belanja online, produk segar, sayuran, buah-buahan, kebutuhan sehari-hari, ' . ($settings['site_name'] ?? $settings['brand_name'] ?? config('app.name')))

@section('og_image', $settings['site_logo'] ?? asset('storage/defaults/og-home.jpg'))

@section('content')
    <style>
      html, body {
        overflow-x: hidden;
        max-width: 100%;
      }
      html { scroll-behavior: smooth; }
      
      /* Modern Hero Section */
      .hero-header {
        position: relative;
        overflow: hidden;
      }
      .hero-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, rgba(20, 116, 64, 0.1) 0%, rgba(13, 110, 253, 0.05) 100%);
        z-index: 1;
      }
      .hero-header > .container {
        position: relative;
        z-index: 2;
      }
      .hero-title {
        animation: fadeInUp 0.8s ease-out;
        font-weight: 700;
        letter-spacing: -0.02em;
      }
      .hero-subtitle {
        animation: fadeInUp 0.8s ease-out 0.2s both;
        color: #6c757d;
      }
      .hero-search {
        animation: fadeInUp 0.8s ease-out 0.4s both;
      }
      
      @keyframes fadeInUp {
        from {
          opacity: 0;
          transform: translateY(30px);
        }
        to {
          opacity: 1;
          transform: translateY(0);
        }
      }
      
      /* Enhanced Product Cards */
      .product-card {
        border: none !important;
        border-radius: 1.25rem !important;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1) !important;
        overflow: hidden;
        background: #fff;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        position: relative;
      }
      .product-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #147440, #20c997);
        transform: scaleX(0);
        transform-origin: left;
        transition: transform 0.4s ease;
      }
      .product-card:hover::before {
        transform: scaleX(1);
      }
      .product-card:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 20px 40px rgba(20, 116, 64, 0.15), 0 8px 16px rgba(0, 0, 0, 0.1);
        border-color: #147440 !important;
      }
      .product-card:active {
        transform: translateY(-4px) scale(1.01);
      }
      
      /* Product Image Enhancement */
      .product-card .fruite-img {
        position: relative;
        overflow: hidden;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
      }
      .product-card .fruite-img img {
        transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
      }
      .product-card:hover .fruite-img img {
        transform: scale(1.1);
      }
      
      /* Product Badge Enhancement */
      .product-badge {
        background: linear-gradient(135deg, #0f5c33 0%, #147440 100%);
        color: white !important;
        box-shadow: 0 4px 12px rgba(20, 116, 64, 0.3);
        font-weight: 700;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        font-size: 0.75rem;
        padding: 0.5rem 1rem;
        border-radius: 50px;
        animation: pulse 2s ease-in-out infinite;
      }
      
      @keyframes pulse {
        0%, 100% {
          box-shadow: 0 4px 12px rgba(20, 116, 64, 0.3);
        }
        50% {
          box-shadow: 0 4px 20px rgba(20, 116, 64, 0.5);
        }
      }
      
      /* Enhanced Add to Cart Button */
      .add-to-cart-btn {
        background: linear-gradient(135deg, #147440 0%, #20c997 100%);
        border: none !important;
        color: white !important;
        font-weight: 600;
        padding: 0.75rem 1.5rem;
        border-radius: 50px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(20, 116, 64, 0.3);
        position: relative;
        overflow: hidden;
      }
      .add-to-cart-btn::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.3);
        transform: translate(-50%, -50%);
        transition: width 0.6s, height 0.6s;
      }
      .add-to-cart-btn:hover::before {
        width: 300px;
        height: 300px;
      }
      .add-to-cart-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(20, 116, 64, 0.4);
        background: linear-gradient(135deg, #0f5c33 0%, #147440 100%);
      }
      .add-to-cart-btn:active {
        transform: translateY(0);
      }
      .add-to-cart-btn .fa-shopping-bag {
        transition: transform 0.3s ease;
      }
      .add-to-cart-btn:hover .fa-shopping-bag {
        transform: scale(1.2) rotate(-10deg);
      }
      
      /* Price Display Enhancement */
      .product-price {
        font-size: 1.5rem;
        font-weight: 700;
        background: linear-gradient(135deg, #147440 0%, #20c997 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        letter-spacing: -0.02em;
      }
      
      /* Stock Badge Enhancement */
      .stock-badge {
        border-radius: 50px;
        padding: 0.4rem 0.9rem;
        font-weight: 600;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        text-transform: uppercase;
      }
      
      /* Features Section Enhancement */
      .featurs-item {
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        border: 2px solid transparent;
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
      }
      .featurs-item:hover {
        transform: translateY(-8px);
        border-color: #147440;
        box-shadow: 0 12px 30px rgba(20, 116, 64, 0.15);
        background: #fff;
      }
      .featurs-icon {
        transition: all 0.4s ease;
        background: linear-gradient(135deg, #147440 0%, #20c997 100%) !important;
        box-shadow: 0 8px 20px rgba(20, 116, 64, 0.3);
      }
      .featurs-item:hover .featurs-icon {
        transform: scale(1.1) rotate(5deg);
        box-shadow: 0 12px 30px rgba(20, 116, 64, 0.4);
      }
      
      /* Category Tabs Enhancement */
      .category-tabs-wrapper {
        display: flex;
        justify-content: flex-end;
        align-items: center;
      }
      .category-tabs-container {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        justify-content: flex-end;
        align-items: center;
      }
      .category-tab {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.625rem 1.25rem;
        background: #f8f9fa;
        border: 2px solid transparent;
        border-radius: 50px;
        text-decoration: none;
        transition: all 0.3s ease;
        font-weight: 600;
        font-size: 0.875rem;
        min-height: 42px;
        white-space: nowrap;
        cursor: pointer;
      }
      .category-tab-text {
        display: block;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        max-width: 150px;
        color: #495057;
      }
      .category-tab:hover {
        background: linear-gradient(135deg, #147440 0%, #20c997 100%) !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(20, 116, 64, 0.3);
        border-color: #147440;
      }
      .category-tab:hover .category-tab-text {
        color: white !important;
      }
      .category-tab.active {
        background: linear-gradient(135deg, #147440 0%, #20c997 100%) !important;
        border-color: #147440;
        box-shadow: 0 4px 12px rgba(20, 116, 64, 0.3);
      }
      .category-tab.active .category-tab-text {
        color: white !important;
      }
      
      /* Responsive Category Tabs */
      @media (max-width: 991.98px) {
        .category-tabs-wrapper {
          justify-content: center;
        }
        .category-tabs-container {
          justify-content: center;
        }
        .category-tab {
          font-size: 0.8125rem;
          padding: 0.5rem 1rem;
          min-height: 38px;
        }
        .category-tab-text {
          max-width: 120px;
        }
      }
      @media (max-width: 575.98px) {
        .category-tabs-container {
          gap: 0.5rem;
        }
        .category-tab {
          font-size: 0.75rem;
          padding: 0.5rem 0.875rem;
          min-height: 36px;
        }
        .category-tab-text {
          max-width: 100px;
        }
      }
      
      /* Section Titles Enhancement */
      .section-title {
        position: relative;
        display: inline-block;
        font-weight: 700;
        letter-spacing: -0.02em;
      }
      .section-title::after {
        content: '';
        position: absolute;
        bottom: -10px;
        left: 0;
        width: 60px;
        height: 4px;
        background: linear-gradient(90deg, #147440, #20c997);
        border-radius: 2px;
      }
      
      /* Loading States */
      .add-to-cart-form { position: relative; }
      .add-to-cart-btn:disabled { opacity: 0.6; cursor: not-allowed; }
      .btn-loading .spinner-border { width: 1rem; height: 1rem; border-width: 0.15em; }
      
      /* Main Artikel Cards */
      .main-artikel-card{transition:transform .25s ease,box-shadow .25s ease}
      .main-artikel-card:hover{transform:translateY(-8px);box-shadow:0 15px 35px rgba(0,0,0,.15)}
      .main-artikel-title{transition:color .25s ease}
      .main-artikel-title:hover{color:#147440 !important}
      .main-artikel-image{transition:transform .25s ease}
      .main-artikel-card:hover .main-artikel-image{transform:scale(1.02)}
      .main-artikel-card .product-image-link {
        display: block;
        overflow: hidden;
      }
      
      /* Side Artikel Cards */
      .side-artikel-card{transition:transform .25s ease,box-shadow .25s ease}
      .side-artikel-card:hover{transform:translateY(-3px);box-shadow:0 8px 20px rgba(0,0,0,.1)}
      .side-artikel-title{transition:color .25s ease}
      .side-artikel-title:hover{color:#147440 !important}
      .side-artikel-image{transition:transform .25s ease}
      .side-artikel-card:hover .side-artikel-image{transform:scale(1.05)}
      .side-artikel-card .product-image-link {
        display: block;
        height: 100%;
      }
      
      /* Store Selection Section - Enhanced Modern Design */
      .store-selection-wrapper {
        background: transparent;
        position: relative;
        overflow-x: hidden;
        width: 100%;
        max-width: 100%;
      }
      .store-selection-section {
        padding: 1rem 0;
        width: 100%;
        max-width: 100%;
      }
      .store-selection-form {
        width: 100%;
        max-width: 100%;
        overflow-x: hidden;
      }
      .store-selection-form .row {
        margin-left: 0;
        margin-right: 0;
        width: 100%;
      }
      .store-selection-form .row > [class*="col-"] {
        padding-left: 0.75rem;
        padding-right: 0.75rem;
      }
      
      /* Header Section */
      .store-selection-header {
        animation: fadeInDown 0.6s ease-out;
      }
      .store-selection-icon-wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
      }
      .store-selection-icon {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, #147440 0%, #20c997 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 8px 20px rgba(20, 116, 64, 0.3);
        animation: pulse 2s ease-in-out infinite;
      }
      .store-selection-icon i {
        font-size: 2rem;
        color: white;
      }
      .store-selection-title {
        font-size: 2.5rem;
        font-weight: 700;
        background: linear-gradient(135deg, #147440 0%, #20c997 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        letter-spacing: -0.02em;
        margin-bottom: 1rem;
      }
      .store-selection-subtitle {
        font-size: 1.1rem;
        color: #6c757d;
        max-width: 600px;
        margin: 0 auto;
      }
      
      /* Store Cards - Modern Card Design */
      .store-card-wrapper {
        cursor: pointer;
        display: block;
        margin: 0;
        height: 100%;
      }
      .store-card {
        position: relative;
        height: 100%;
        min-height: 280px;
        border-radius: 1.25rem;
        overflow: hidden;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        background: white;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        border: 2px solid transparent;
      }
      .store-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #147440, #20c997);
        transform: scaleX(0);
        transform-origin: left;
        transition: transform 0.4s ease;
        z-index: 2;
      }
      .store-card:hover::before,
      .store-card.active::before {
        transform: scaleX(1);
      }
      .store-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 30px rgba(20, 116, 64, 0.2);
        border-color: #147440;
      }
      .store-card.active {
        border-color: #147440;
        box-shadow: 0 12px 30px rgba(20, 116, 64, 0.25);
        transform: translateY(-4px);
      }
      .store-card-inner {
        position: relative;
        height: 100%;
        padding: 2rem 1.5rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        z-index: 1;
      }
      .store-check-badge {
        position: absolute;
        top: 1rem;
        right: 1rem;
        width: 32px;
        height: 32px;
        background: linear-gradient(135deg, #147440 0%, #20c997 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        opacity: 0;
        transform: scale(0);
        transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        z-index: 3;
        box-shadow: 0 4px 12px rgba(20, 116, 64, 0.4);
      }
      .store-check-badge i {
        font-size: 0.875rem;
      }
      .store-card.active .store-check-badge,
      .store-radio:checked + .store-card .store-check-badge {
        opacity: 1;
        transform: scale(1);
      }
      .store-logo-container {
        width: 100px;
        height: 100px;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, rgba(20, 116, 64, 0.05) 0%, rgba(32, 201, 151, 0.05) 100%);
        border-radius: 50%;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
      }
      .store-card:hover .store-logo-container {
        transform: scale(1.1);
        background: linear-gradient(135deg, rgba(20, 116, 64, 0.1) 0%, rgba(32, 201, 151, 0.1) 100%);
      }
      .store-card.active .store-logo-container {
        background: linear-gradient(135deg, rgba(20, 116, 64, 0.15) 0%, rgba(32, 201, 151, 0.15) 100%);
        box-shadow: 0 8px 20px rgba(20, 116, 64, 0.2);
      }
      .store-logo-img {
        max-width: 70px;
        max-height: 70px;
        object-fit: contain;
        transition: transform 0.3s ease;
      }
      .store-card:hover .store-logo-img {
        transform: scale(1.1);
      }
      .store-logo-placeholder {
        font-size: 3rem;
        color: #147440;
        transition: transform 0.3s ease;
      }
      .store-card:hover .store-logo-placeholder {
        transform: scale(1.1) rotate(5deg);
      }
      .store-info {
        width: 100%;
      }
      .store-name {
        color: #147440;
        font-weight: 700;
        font-size: 1.15rem;
        margin-bottom: 0.75rem;
        line-height: 1.4;
        transition: color 0.3s ease;
        word-wrap: break-word;
      }
      .store-card:hover .store-name {
        color: #0f5c33;
      }
      .store-location {
        color: #6c757d;
        font-size: 0.9rem;
        margin: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
      }
      .store-location i {
        font-size: 0.875rem;
      }
      .store-card-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, rgba(20, 116, 64, 0) 0%, rgba(32, 201, 151, 0) 100%);
        opacity: 0;
        transition: opacity 0.3s ease;
        pointer-events: none;
      }
      .store-card.active .store-card-overlay {
        opacity: 1;
        background: linear-gradient(135deg, rgba(20, 116, 64, 0.03) 0%, rgba(32, 201, 151, 0.03) 100%);
      }
      
      /* Responsive Design */
      @media (max-width: 991.98px) {
        .store-selection-title {
          font-size: 2rem;
        }
        .store-selection-subtitle {
          font-size: 1rem;
        }
        .store-selection-icon {
          width: 70px;
          height: 70px;
        }
        .store-selection-icon i {
          font-size: 1.75rem;
        }
        .store-card {
          min-height: 260px;
        }
        .store-logo-container {
          width: 90px;
          height: 90px;
        }
        .store-logo-img {
          max-width: 60px;
          max-height: 60px;
        }
        .store-logo-placeholder {
          font-size: 2.5rem;
        }
      }
      @media (max-width: 767.98px) {
        .store-selection-section {
          padding: 1.5rem 0;
        }
        .store-selection-header {
          margin-bottom: 2rem !important;
        }
        .store-selection-title {
          font-size: 1.75rem;
        }
        .store-selection-icon {
          width: 60px;
          height: 60px;
        }
        .store-selection-icon i {
          font-size: 1.5rem;
        }
        .store-card {
          min-height: 240px;
        }
        .store-card-inner {
          padding: 1.5rem 1rem;
        }
        .store-logo-container {
          width: 80px;
          height: 80px;
          margin-bottom: 1rem;
        }
        .store-logo-img {
          max-width: 50px;
          max-height: 50px;
        }
        .store-logo-placeholder {
          font-size: 2rem;
        }
        .store-name {
          font-size: 1rem;
        }
        .store-location {
          font-size: 0.85rem;
        }
      }
      @media (max-width: 575.98px) {
        .store-selection-title {
          font-size: 1.5rem;
        }
        .store-selection-subtitle {
          font-size: 0.9rem;
        }
        .store-card {
          min-height: 220px;
        }
        .store-card-inner {
          padding: 1.25rem 0.75rem;
        }
      }
      
      @keyframes fadeInDown {
        from {
          opacity: 0;
          transform: translateY(-20px);
        }
        to {
          opacity: 1;
          transform: translateY(0);
        }
      }
      
      /* Smooth carousel movement */
      .banners-middle-carousel .owl-stage{transition-timing-function:cubic-bezier(.25,.8,.25,1) !important}
      .banners-middle-carousel .item .service-item{transition:box-shadow .25s ease, transform .25s ease}
      .banners-middle-carousel .item .service-item:hover{transform:translateY(-2px);box-shadow:0 8px 20px rgba(0,0,0,.08)}
      
      /* Trust Indicators */
      .trust-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        background: rgba(20, 116, 64, 0.1);
        border-radius: 50px;
        font-size: 0.875rem;
        font-weight: 600;
        color: #147440;
        margin: 0.25rem;
        transition: all 0.3s ease;
      }
      .trust-badge:hover {
        background: rgba(20, 116, 64, 0.15);
        transform: translateY(-2px);
      }
      .trust-badge i {
        color: #20c997;
      }
      
      /* Promo Badge */
      .promo-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        box-shadow: 0 4px 12px rgba(255, 107, 107, 0.4);
        z-index: 10;
        animation: pulse 2s ease-in-out infinite;
      }
      
      /* Social Proof Section */
      .social-proof {
        background: linear-gradient(135deg, rgba(20, 116, 64, 0.05) 0%, rgba(32, 201, 151, 0.05) 100%);
        border-radius: 1rem;
        padding: 2rem;
        margin: 2rem 0;
      }
      .social-proof-item {
        text-align: center;
        padding: 1rem;
      }
      .social-proof-number {
        font-size: 2.5rem;
        font-weight: 700;
        background: linear-gradient(135deg, #147440 0%, #20c997 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
      }
      .social-proof-label {
        color: #6c757d;
        font-size: 0.875rem;
        margin-top: 0.5rem;
      }
      
      /* Urgency Indicator */
      .urgency-badge {
        background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
        color: white;
        padding: 0.4rem 0.8rem;
        border-radius: 50px;
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        animation: shake 0.5s ease-in-out infinite;
      }
      @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-3px); }
        75% { transform: translateX(3px); }
      }
      
      /* Clickable Product Image and Name */
      .product-image-link,
      .product-name-link {
        cursor: pointer;
        text-decoration: none;
        color: inherit;
        transition: all 0.3s ease;
        display: block;
      }
      .product-image-link:hover {
        opacity: 0.9;
      }
      .product-image-link:hover img {
        transform: scale(1.05);
      }
      .product-name-link:hover {
        text-decoration: none;
      }
      .product-name-link h4,
      .product-name-link h5 {
        transition: color 0.3s ease;
      }
      .product-name-link:hover h4,
      .product-name-link:hover h5 {
        color: #147440 !important;
      }
      
      /* Better text contrast */
      .text-muted {
        color: #495057 !important;
      }
      .hero-subtitle.text-secondary {
        color: #495057 !important;
      }
      
      /* Section Spacing */
      .section-spacing {
        padding: 5rem 0;
      }
      
      /* Floating Action Button */
      .floating-cta {
        position: fixed;
        bottom: 30px;
        right: 30px;
        background: linear-gradient(135deg, #147440 0%, #20c997 100%);
        color: white;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 8px 24px rgba(20, 116, 64, 0.4);
        z-index: 1001; /* Higher than back-to-top (999) */
        transition: all 0.3s ease;
        text-decoration: none;
        font-size: 1.5rem;
      }
      .floating-cta:hover {
        transform: scale(1.1);
        box-shadow: 0 12px 32px rgba(20, 116, 64, 0.5);
        color: white;
      }
      
      /* Scroll Animation */
      @keyframes slideInUp {
        from {
          opacity: 0;
          transform: translateY(50px);
        }
        to {
          opacity: 1;
          transform: translateY(0);
        }
      }
      .animate-on-scroll {
        opacity: 0;
        animation: slideInUp 0.8s ease-out forwards;
      }
      
      /* Better Typography */
      .display-2 {
        font-size: 3.5rem;
        font-weight: 700;
        line-height: 1.2;
        letter-spacing: -0.02em;
      }
      
      /* Enhanced Search Bar */
      .hero-search .input-group {
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
      }
      .hero-search .input-group:focus-within {
        box-shadow: 0 15px 50px rgba(20, 116, 64, 0.2);
        transform: translateY(-2px);
      }
      
      /* Product Grid Enhancement */
      .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 2rem;
      }
      
      /* Testimonial Enhancement */
      .testimonial-card {
        background: white;
        border-radius: 1.25rem;
        padding: 2rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        transition: all 0.4s ease;
        height: 100%;
      }
      .testimonial-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
      }
      
      /* Stats Counter Animation */
      .counter-number {
        font-size: 3rem;
        font-weight: 700;
        background: linear-gradient(135deg, #147440 0%, #20c997 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
      }
    </style>
    <!-- Hero Start -->
    @php
      $heroBg = $settings['hero_bg'] ?? ($slides->first()->image_path ?? 'fruitables/img/hero-img.jpg');
      $heroBgUrl = asset($heroBg);
    @endphp
    <div class="container-fluid py-5 mb-5 hero-header" style="background-image: linear-gradient(135deg, rgba(20, 116, 64, 0.05) 0%, rgba(13, 110, 253, 0.03) 100%), url('{{ $heroBgUrl }}'); background-position:center center; background-repeat:no-repeat; background-size:cover;">
        <div class="container py-5">
            <div class="row g-5 align-items-center">
                <div class="col-md-12 col-lg-7">
                    <div class="hero-subtitle mb-3">
                        <span class="trust-badge">
                            <i class="fas fa-check-circle"></i>
                            <span>100% Produk Asli & Berkualitas</span>
                        </span>
                        <span class="trust-badge">
                            <i class="fas fa-shipping-fast"></i>
                            <span>Gratis Ongkir</span>
                        </span>
                    </div>
                    <h4 class="hero-subtitle mb-3 fw-normal" style="color: #495057 !important;">{{ $settings['brand_tagline'] ?? 'Toko Online Terpercaya' }}</h4>
                    <h1 class="hero-title mb-4 display-2 fw-bold text-primary">{{ $settings['brand_name'] ?? 'Selamat Datang' }}</h1>
                    <p class="hero-subtitle mb-4 fs-5" style="color: #495057 !important;">Temukan produk berkualitas dengan harga terbaik. Belanja mudah, cepat, dan aman hanya di sini!</p>
                    <form action="{{ route('shop') }}" method="GET" class="hero-search position-relative mx-auto mb-4">
                        <div class="input-group input-group-lg shadow-lg" style="border-radius: 50px; overflow: hidden;">
                            <input class="form-control border-0 py-3 px-4" 
                                   type="text" 
                                   name="search"
                                   placeholder="{{ $siteSettings['search_placeholder'] ?? 'Cari produk favorit Anda...' }}"
                                   value="{{ request('search') }}"
                                   style="font-size: 1.1rem;"
                                   aria-label="Cari produk">
                            <button type="submit" class="btn btn-primary border-0 px-5 text-white fw-bold" style="background: linear-gradient(135deg, #147440 0%, #20c997 100%);" aria-label="Tombol cari">
                                <i class="fa fa-search me-2"></i>{{ $siteSettings['search_button_text'] ?? 'Cari' }}
                            </button>
                        </div>
                    </form>
                    <div class="hero-subtitle d-flex flex-wrap gap-3">
                        <a href="{{ route('shop') }}" class="btn btn-lg btn-outline-primary rounded-pill px-4 fw-bold" style="border-width: 2px; color: #147440; border-color: #147440;">
                            <i class="fas fa-shopping-bag me-2"></i>Belanja Sekarang
                        </a>
                        <a href="#produk" class="btn btn-lg btn-primary rounded-pill px-4 fw-bold text-white" style="background: linear-gradient(135deg, #147440 0%, #20c997 100%); border: none;">
                            <i class="fas fa-fire me-2"></i>Lihat Promo
                        </a>
                    </div>
                </div>
                <div class="col-md-12 col-lg-5">
                    <div id="carouselId" class="carousel slide position-relative" data-bs-ride="carousel">
                        <div class="carousel-inner" role="listbox">
                            @foreach($slides as $slide)
                                <div class="carousel-item {{ $loop->first ? 'active' : '' }} rounded">
                                    <img src="{{ asset($slide->image_path) }}" class="img-fluid w-100 h-100 rounded" alt="slide">
                                    @if($slide->button_text)
                                        <a href="{{ $slide->button_url ?? '#' }}" class="btn px-4 py-2 text-white rounded">{{ $slide->button_text }}</a>
                                    @endif
                                </div>
                            @endforeach 
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#carouselId" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Sebelumnya</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#carouselId" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Selanjutnya</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Hero End -->
    
    <!-- Featurs Section Start -->
    <div class="container-fluid featurs py-5" style="background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);">
        <div class="container py-5">
            <div class="text-center mb-5">
                <h2 class="section-title d-inline-block mb-3">Mengapa Pilih Kami?</h2>
                <p class="fs-5" style="color: #495057 !important;">Keunggulan yang membuat kami berbeda</p>
            </div>
            <div class="row g-4">
                @foreach($features as $feature)
                    <div class="col-md-6 col-lg-3">
                        <div class="featurs-item text-center rounded p-5 h-100">
                            <div class="featurs-icon btn-square rounded-circle bg-secondary mb-4 mx-auto d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                @if($feature->icon_class)
                                    <i class="{{ $feature->icon_class }} fa-2x text-white"></i>
                                @elseif($feature->image_path)
                                    <img src="{{ asset($feature->image_path) }}" class="img-fluid" alt="icon" style="max-width: 50px;">
                                @endif
                            </div>
                            <div class="featurs-content text-center">
                                <h5 class="fw-bold mb-3">{{ $feature->title }}</h5>
                                <p class="mb-0" style="color: #495057 !important;">{{ $feature->description }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    <!-- Featurs Section End -->

    <!-- Store Selection Section -->
    @if(isset($stores) && $stores->count() > 0)
    <div class="container-fluid store-selection-wrapper" style="padding-top: 3rem; padding-bottom: 3rem;">
        <div class="container">
            <div class="store-selection-section">
                <!-- Header Section -->
                <div class="store-selection-header text-center mb-5">
                    <div class="store-selection-icon-wrapper mb-3">
                        <div class="store-selection-icon">
                            <i class="fas fa-store"></i>
                        </div>
                    </div>
                    <h2 class="store-selection-title mb-3">Pilih Store</h2>
                    <p class="store-selection-subtitle">Pilih store untuk melihat produk yang tersedia</p>
                </div>

                <!-- Store Cards Grid -->
                <form method="GET" action="{{ route('shop') }}" id="store-selection-form" class="store-selection-form">
                    <div class="row g-4 justify-content-center mx-0">
                                        @foreach($stores as $store)
                                            <div class="col-12 col-sm-6 col-md-6 col-lg-4 col-xl-3">
                                                <label class="store-card-wrapper">
                                                    <input type="radio" name="store_id" value="{{ encode_id($store->id) }}" 
                                                           class="store-radio d-none" 
                                                           {{ ($selectedStoreId == $store->id) ? 'checked' : '' }}>
                                    <div class="store-card {{ ($selectedStoreId == $store->id) ? 'active' : '' }}">
                                        <div class="store-card-inner">
                                            <div class="store-check-badge">
                                                <i class="fas fa-check"></i>
                                            </div>
                                            <div class="store-logo-container">
                                                @if($store->logo_url)
                                                    <img src="{{ asset($store->logo_url) }}" alt="{{ $store->name }}" class="store-logo-img">
                                                @else
                                                    <div class="store-logo-placeholder">
                                                        <i class="fas fa-store"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="store-info">
                                                <h5 class="store-name">{{ $store->short_name }}</h5>
                                                @if($store->city)
                                                    <p class="store-location">
                                                        <i class="fas fa-map-marker-alt"></i>
                                                        <span>{{ $store->city }}</span>
                                                    </p>
                                                @endif
                                            </div>
                                            <div class="store-card-overlay"></div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <!-- Social Proof Section -->
    @if($testimonials->count() > 0)
    <div class="container-fluid py-4">
        <div class="container">
            <div class="social-proof">
                <div class="row g-4">
                    <div class="col-6 col-md-3">
                        <div class="social-proof-item">
                            @php
                                $totalProducts = \Illuminate\Support\Facades\DB::table('products')->where('is_active', true)->count();
                            @endphp
                            <div class="social-proof-number">{{ $totalProducts }}+</div>
                            <div class="social-proof-label">Produk Tersedia</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="social-proof-item">
                            <div class="social-proof-number">{{ $testimonials->count() ?? 0 }}+</div>
                            <div class="social-proof-label">Pelanggan Puas</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="social-proof-item">
                            <div class="social-proof-number">100%</div>
                            <div class="social-proof-label">Produk Asli</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="social-proof-item">
                            <div class="social-proof-number">24/7</div>
                            <div class="social-proof-label">Layanan</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif


    <!-- Artikel Section Start -->
    @php
        $latestArtikel = \App\Models\Artikel::with(['kategoriArtikel', 'user'])
            ->where('status', 'published')
            ->orderBy('created_at', 'desc')
            ->limit(4)
            ->get();
        $mainArtikel = $latestArtikel->first();
        $otherArtikel = $latestArtikel->slice(1);
    @endphp
    
    @if($latestArtikel->count() > 0)
    <div class="container-fluid bg-light py-5">
        <div class="container py-5">
            <div class="text-center mx-auto" style="max-width: 700px;">
                <h1 class="text-primary mb-4">Artikel & Berita</h1>
                <p class="mb-0" style="color: #495057 !important;">Temukan informasi menarik, tips berguna, dan berita terbaru dari kami</p>
            </div>
            
            <div class="row g-4 mt-4">
                <!-- Main Article (Left Side) -->
                @if($mainArtikel)
                    <div class="col-lg-6">
                        <div class="card h-100 shadow-lg main-artikel-card">
                            <a href="{{ route('artikel.show', $mainArtikel->slug) }}" class="product-image-link">
                                @if($mainArtikel->gambar_utama)
                                    <img src="{{ Storage::url($mainArtikel->gambar_utama) }}" 
                                         class="card-img-top main-artikel-image" 
                                         alt="{{ $mainArtikel->judul }}"
                                         style="height: 350px; object-fit: cover;">
                                @elseif($mainArtikel->gambar_thumbnail)
                                    <img src="{{ Storage::url($mainArtikel->gambar_thumbnail) }}" 
                                         class="card-img-top main-artikel-image" 
                                         alt="{{ $mainArtikel->judul }}"
                                         style="height: 350px; object-fit: cover;">
                                @else
                                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                                         style="height: 350px;">
                                        <i class="bx bx-image bx-5x" style="color: #6c757d !important;"></i>
                                    </div>
                                @endif
                            </a>
                            
                            <div class="card-body">
                                <!-- Category Badge -->
                                <div class="mb-3">
                                    <span class="badge bg-primary fs-6 px-3 py-2">{{ $mainArtikel->kategoriArtikel->nama }}</span>
                                    <span class="badge bg-success fs-6 px-3 py-2 ms-2">Terbaru</span>
                                </div>
                                
                                <!-- Title -->
                                <h2 class="card-title fw-bold mb-3">
                                    <a href="{{ route('artikel.show', $mainArtikel->slug) }}" 
                                       class="text-decoration-none text-dark main-artikel-title product-name-link">
                                        {{ $mainArtikel->judul }}
                                    </a>
                                </h2>
                                
                                <!-- Excerpt -->
                                <p class="card-text mb-4" style="color: #495057 !important;">
                                    {{ Str::limit(strip_tags($mainArtikel->konten), 200) }}
                                </p>
                                
                                <!-- Meta Info -->
                                <div class="d-flex justify-content-between align-items-center mb-4" style="color: #6c757d !important;">
                                    <div>
                                        <span class="me-3"><i class="bx bx-calendar me-1"></i> {{ $mainArtikel->created_at->format('d M Y') }}</span>
                                        <span class="me-3"><i class="bx bx-time me-1"></i> {{ $mainArtikel->reading_time }} menit</span>
                                        <span><i class="bx bx-user me-1"></i> {{ $mainArtikel->user ? $mainArtikel->user->name : 'Admin' }}</span>
                                    </div>
                                    <span><i class="bx bx-show me-1"></i> {{ number_format($mainArtikel->views) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                
                <!-- Other Articles (Right Side) -->
                <div class="col-lg-6">
                    <div class="row g-4 h-100">
                        @foreach($otherArtikel as $artikel)
                            <div class="col-12">
                                <div class="card h-100 shadow-sm side-artikel-card">
                                    <div class="row g-0 h-100">
                                        <div class="col-md-4">
                                            <a href="{{ route('artikel.show', $artikel->slug) }}" class="product-image-link">
                                                @if($artikel->gambar_thumbnail)
                                                    <img src="{{ Storage::url($artikel->gambar_thumbnail) }}" 
                                                         class="img-fluid rounded-start h-100 side-artikel-image" 
                                                         alt="{{ $artikel->judul }}"
                                                         style="object-fit: cover;">
                                                @elseif($artikel->gambar_utama)
                                                    <img src="{{ Storage::url($artikel->gambar_utama) }}" 
                                                         class="img-fluid rounded-start h-100 side-artikel-image" 
                                                         alt="{{ $artikel->judul }}"
                                                         style="object-fit: cover;">
                                                @else
                                                    <div class="bg-light d-flex align-items-center justify-content-center h-100 rounded-start">
                                                        <i class="bx bx-image bx-3x" style="color: #6c757d !important;"></i>
                                                    </div>
                                                @endif
                                            </a>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="card-body h-100 d-flex flex-column">
                                                <!-- Category Badge -->
                                                <div class="mb-2">
                                                    <span class="badge bg-primary">{{ $artikel->kategoriArtikel->nama }}</span>
                                                </div>
                                                
                                                <!-- Title -->
                                                <h5 class="card-title fw-bold mb-2">
                                                    <a href="{{ route('artikel.show', $artikel->slug) }}" 
                                                       class="text-decoration-none text-dark side-artikel-title product-name-link">
                                                        {{ Str::limit($artikel->judul, 50) }}
                                                    </a>
                                                </h5>
                                                
                                                <!-- Excerpt -->
                                                <p class="card-text small flex-grow-1 mb-2" style="color: #495057 !important;">
                                                    {{ Str::limit(strip_tags($artikel->konten), 80) }}
                                                </p>
                                                
                                                <!-- Meta Info -->
                                                <div class="d-flex justify-content-between align-items-center small" style="color: #6c757d !important;">
                                                    <div>
                                                        <span class="me-2"><i class="bx bx-calendar me-1"></i> {{ $artikel->created_at->format('d M') }}</span>
                                                        <span><i class="bx bx-time me-1"></i> {{ $artikel->reading_time }}m</span>
                                                    </div>
                                                    <span><i class="bx bx-show me-1"></i> {{ number_format($artikel->views) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            
            <!-- View All Articles Button -->
            <div class="text-center mt-5">
                <a href="{{ route('artikel.index') }}" class="btn btn-outline-primary btn-lg">
                    <i class="bx bx-file me-2"></i>Lihat Semua Artikel
                </a>
            </div>
        </div>
    </div>
    @endif
    <!-- Artikel Section End -->

<!-- Middle Banners Carousel Start -->
<div class="container-fluid service py-5">
    <div class="container py-5">
        <div class="owl-carousel banners-middle-carousel">
            @foreach($bannersMiddle as $banner)
                <div class="item px-2">
                    <a href="{{ $banner->button_url ?? '#' }}">
                        <div class="service-item bg-secondary rounded border border-secondary" style="height: 300px; overflow: hidden;">
                            <img src="{{ asset($banner->image_path) }}" class="img-fluid w-100 mt-2" style="object-fit: contain; height: 180px;" alt="">
                            <div class="px-4 rounded-bottom">
                                <div class="service-content bg-primary text-center p-4 rounded mt-4">
                                    <h5 class="text-white">{{ $banner->title }}</h5>
                                    @if($banner->subtitle) 
                                        <a href="{{ $banner->button_url ?? '#' }}" class="btn btn-outline-secondary btn-sm w-100 mt-3">
                                            <i class="bx bx-map me-2"></i> {{ $banner->subtitle }}
                                        </a>
                                    @endif
                                </div>
                            </div> 
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</div>
<!-- Middle Banners Carousel End -->

@push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function(){
      if (window.jQuery && jQuery.fn.owlCarousel) {
        const itemCount = {{ $bannersMiddle->count() ?? 0 }};
        const shouldLoop = itemCount > 3;
        jQuery('.banners-middle-carousel').owlCarousel({
          items: 3,
          margin: 16,
          loop: shouldLoop,
          autoplay: shouldLoop,
          autoplayTimeout: 3600,
          autoplayHoverPause: true,
          smartSpeed: 600,
          dotsSpeed: 600,
          dragEndSpeed: 500,
          dots: true,
          nav: true,
          navText: [
            '<span class="middle-arrow prev"><i class="bx bx-chevron-left"></i></span>',
            '<span class="middle-arrow next"><i class="bx bx-chevron-right"></i></span>'
          ],
          responsive: {
            0: { items: 1 },
            576: { items: 2 },
            992: { items: 3 }
          }
        });
      }
    });

    // Add to cart with loading state
    document.querySelectorAll('.add-to-cart-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            const btn = this.querySelector('.add-to-cart-btn');
            const btnText = btn.querySelector('.btn-text');
            const btnLoading = btn.querySelector('.btn-loading');
            
            if (btn && !btn.disabled) {
                btn.disabled = true;
                if (btnText) btnText.classList.add('d-none');
                if (btnLoading) btnLoading.classList.remove('d-none');
            }
        });
    });
    
    // Scroll animation
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.animationDelay = (Math.random() * 0.3) + 's';
                entry.target.classList.add('animate-on-scroll');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);
    
    // Observe product cards
    document.querySelectorAll('.product-card').forEach(card => {
        observer.observe(card);
    });
    
    // Observe feature items
    document.querySelectorAll('.featurs-item').forEach(item => {
        observer.observe(item);
    });
    
    // Store Selection Handler - Redirect to shop page
    document.querySelectorAll('.store-radio').forEach(radio => {
        radio.addEventListener('change', function() {
            const form = document.getElementById('store-selection-form');
            if (form) {
                // Add loading state
                const storeCards = document.querySelectorAll('.store-card');
                storeCards.forEach(card => {
                    card.style.opacity = '0.6';
                    card.style.pointerEvents = 'none';
                });
                
                // Submit form immediately to redirect to shop page
                form.submit();
            }
        });
    });
    
    // Category Tab Handler - Update active class on tab change
    document.querySelectorAll('.category-tab').forEach(tab => {
        tab.addEventListener('click', function(e) {
            // Remove active class from all tabs
            document.querySelectorAll('.category-tab').forEach(t => {
                t.classList.remove('active');
            });
            // Add active class to clicked tab
            this.classList.add('active');
        });
    });
    
    // Bootstrap pill event listener to sync active state
    document.querySelectorAll('[data-bs-toggle="pill"]').forEach(tab => {
        tab.addEventListener('shown.bs.tab', function(e) {
            // Update active class on category tabs
            document.querySelectorAll('.category-tab').forEach(t => {
                t.classList.remove('active');
                if (t.getAttribute('href') === e.target.getAttribute('href')) {
                    t.classList.add('active');
                }
            });
        });
    });
    
    // Counter animation
    function animateCounter(element, target, duration = 2000) {
        let start = 0;
        const increment = target / (duration / 16);
        const timer = setInterval(() => {
            start += increment;
            if (start >= target) {
                element.textContent = target + (element.textContent.includes('+') ? '+' : '');
                clearInterval(timer);
            } else {
                element.textContent = Math.floor(start) + (element.textContent.includes('+') ? '+' : '');
            }
        }, 16);
    }
    
    // Animate counters when visible
    const counterObserver = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const numberElement = entry.target.querySelector('.social-proof-number');
                if (numberElement) {
                    const text = numberElement.textContent;
                    const number = parseInt(text.replace(/\D/g, ''));
                    if (!isNaN(number)) {
                        animateCounter(numberElement, number, 2000);
                    }
                }
                counterObserver.unobserve(entry.target);
            }
        });
    }, observerOptions);
    
    document.querySelectorAll('.social-proof-item').forEach(item => {
        counterObserver.observe(item);
    });
    </script>
    @endpush
    @push('styles')
    <style>
    /* Bootstrap carousel custom styles */
    .banners-middle-carousel .owl-nav {
      position: absolute !important;
      top: 50% !important;
      transform: translateY(-50%) !important;
      width: 100% !important;
      display: flex !important;
      justify-content: space-between !important;
      pointer-events: none !important;
      padding: 0 30px !important;
    }

    .banners-middle-carousel .owl-nav button {
      width: 60px !important;
      height: 60px !important;
      background: rgba(255, 255, 255, 0.98) !important;
      border: 3px solid #007bff !important;
      border-radius: 50% !important;
      display: flex !important;
      align-items: center !important;
      justify-content: center !important;
      cursor: pointer !important;
      pointer-events: all !important;
      transition: all 0.3s ease !important;
      box-shadow: 0 6px 18px rgba(0, 123, 255, 0.35) !important;
      margin: 0 !important;
      font-size: 28px !important;
    }

    .banners-middle-carousel .owl-nav button .middle-arrow {
      width: 100%;
      height: 100%;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      color: #0f5132;
      font-size: 28px;
      font-weight: 700;
    }

    .banners-middle-carousel .owl-nav button:hover .middle-arrow {
      color: white;
    }

    .banners-middle-carousel .owl-nav button:hover {
      background: #007bff !important;
      border-color: #0056b3 !important;
      transform: scale(1.1) !important;
      box-shadow: 0 6px 20px rgba(0, 123, 255, 0.5) !important;
    }

    .banners-middle-carousel .owl-nav button i {
      font-size: 24px !important;
      color: #007bff !important;
      transition: color 0.3s ease !important;
      font-weight: bold !important;
      margin: 0 !important;
    }

    .banners-middle-carousel .owl-nav button:hover i {
      color: white !important;
    }

    .banners-middle-carousel .owl-nav .owl-prev {
      position: absolute !important;
      left: -25px !important;
      top: 50% !important;
      transform: translateY(-50%) !important;
    }

    .banners-middle-carousel .owl-nav .owl-next {
      position: absolute !important;
      right: -25px !important;
      top: 50% !important;
      transform: translateY(-50%) !important;
    }

    .banners-middle-carousel .owl-nav button:disabled {
      opacity: 0.4 !important;
      cursor: not-allowed !important;
      transform: scale(1) !important;
    }

    /* Vegetable carousel nav styling */
    .vegetable-section .owl-nav {
      position: absolute;
      top: 8px;
      right: 0;
      display: flex;
      gap: 12px;
      pointer-events: none;
    }

    .vegetable-section .owl-nav button {
      width: 96px;
      height: 46px;
      border-radius: 999px;
      border: 2px solid #f3b623;
      background: #fff;
      color: #157347;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-size: 18px;
      font-weight: 600;
      box-shadow: 0 4px 14px rgba(0, 0, 0, 0.08);
      transition: all 0.25s ease;
      pointer-events: all;
    }

    .vegetable-section .owl-nav button:hover {
      background: #fdf5e3;
      transform: translateY(-1px);
      box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
    }

    .vegetable-section .owl-nav button i {
      font-size: 18px;
      font-weight: 700;
      line-height: 1;
    }
    </style>
    @endpush
    <!-- Artikel Section End -->

    
 
    <!-- Vesitable Shop Start-->
    <div class="container-fluid vesitable py-5">
        <div class="container py-5 position-relative vegetable-section">
            <h1 class="mb-0">{{ $siteSettings['homepage_vegetables_title'] ?? 'Sayuran Organik Segar' }}</h1>
            @if(!empty($siteSettings['homepage_vegetables_subtitle']))
                <p class="mb-4" style="color: #495057 !important;">{{ $siteSettings['homepage_vegetables_subtitle'] }}</p>
            @endif
            <div class="owl-carousel vegetable-carousel justify-content-center">
                @foreach($vegetableProducts as $product)
                    <div class="border border-primary rounded position-relative vesitable-item">
                        <a href="{{ route('shop.detail', $product->slug ?? '') }}" class="product-image-link">
                            <div class="vesitable-img">
                                <img src="{{ asset($product->main_image_path ? ('storage/' . $product->main_image_path) : 'fruitables/img/vegetable-item-1.jpg') }}" class="img-fluid w-100 rounded-top" alt="{{ $product->name }}">
                            </div>
                        </a>
                        <div class="text-white bg-secondary px-3 py-1 rounded position-absolute" style="top: 10px; left: 10px; background: linear-gradient(135deg, #0f5c33 0%, #147440 100%) !important;">{{ $siteSettings['product_badge_text'] ?? 'Product' }}</div>
                        <div class="p-4 rounded-bottom">
                            @if($product->store_short_name)
                            <div class="mb-1">
                                <span class="badge bg-secondary" style="font-size: 0.75rem; font-weight: 500;">
                                    <i class="fas fa-store me-1"></i>{{ $product->store_short_name }}
                                </span>
                            </div>
                            @endif
                            <a href="{{ route('shop.detail', $product->slug ?? '') }}" class="product-name-link text-decoration-none">
                                <h4 class="text-dark mb-2">{{ $product->name }}</h4>
                            </a>
                            <div class="mb-2">
                                <p class="text-dark fs-5 fw-bold mb-0">{{ $siteSettings['currency_symbol'] ?? 'Rp.' }} {{ number_format($product->price, 0, ',', '.') }} {{ $product->unit ? '/ '.$product->unit : '' }}</p>
                            </div>
                            @php
                                $stockQty = $product->stock_qty ?? 0;
                                $isOutOfStock = $stockQty <= 0;
                            @endphp
                            @if($isOutOfStock)
                                <div class="mb-2">
                                    <span class="badge bg-danger">Stok Habis</span>
                                </div>
                            @elseif($stockQty < 10)
                                <div class="mb-2">
                                    <span class="badge bg-warning text-dark">Stok Terbatas ({{ $stockQty }})</span>
                                </div>
                            @else
                                <div class="mb-2">
                                    <span class="badge bg-success">Tersedia ({{ $stockQty }} {{ $product->unit ?? 'pcs' }})</span>
                                </div>
                            @endif
                            <div class="d-flex justify-content-between flex-lg-wrap">
                                <form action="{{ route('cart.add') }}" method="POST" class="add-to-cart-form">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $product->id }}">
                                    <input type="hidden" name="name" value="{{ $product->name }}">
                                    <input type="hidden" name="price" value="{{ $product->price }}">
                                    <input type="hidden" name="image" value="{{ $product->main_image_path ? ('storage/' . $product->main_image_path) : '' }}">
                                    <input type="hidden" name="qty" value="1" max="{{ $stockQty }}">
                                    @if($isOutOfStock)
                                        <button class="btn border border-secondary rounded-pill px-3 text-muted" type="button" disabled>
                                            <i class="fa fa-ban me-2"></i> Stok Habis
                                        </button>
                                    @else
                                        <button class="btn border border-secondary rounded-pill px-3 text-primary add-to-cart-btn" type="submit">
                                            <span class="btn-text"><i class="fa fa-shopping-bag me-2 text-primary"></i> {{ $siteSettings['add_to_cart_text'] ?? 'Tambah ke Keranjang' }}</span>
                                            <span class="btn-loading d-none"><span class="spinner-border spinner-border-sm me-2"></span>Menambahkan...</span>
                                        </button>
                                    @endif
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    <!-- Vesitable Shop End -->

    <!-- Banner Section Start-->
    @php $topCount = $bannersTop->count() ?? 0; @endphp
    <div class="container-fluid banner bg-secondary my-5">
        <div class="container py-5">
            @if($topCount > 1)
                <div class="owl-carousel banners-top-carousel">
                    @foreach($bannersTop as $banner)
                        <div class="item">
                            <div class="row g-4 align-items-center">
                                <div class="col-lg-6">
                                    <div class="py-4">
                                        <h1 class="display-3 text-white">{{ $banner->title ?? 'Welcome' }}</h1>
                                        @if(!empty($banner?->subtitle))
                                            <p class="fw-normal display-3 text-dark mb-4">{{ $banner->subtitle }}</p>
                                        @endif
                                        @if(!empty($banner?->button_text))
                                            <a href="{{ $banner->button_url ?? '#' }}" class="banner-btn btn border-2 border-white rounded-pill text-dark py-3 px-5">{{ $banner->button_text }}</a>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="position-relative">
                                        <img src="{{ asset($banner->image_path ?? 'fruitables/img/fruite-item-1.jpg') }}" class="img-fluid w-100 rounded" alt="">
                                        @if($banner->show_circle && $banner->circle_number)
                                        <div class="d-flex align-items-center justify-content-center bg-white rounded-circle position-absolute" style="width: 140px; height: 140px; top: 0; left: 0;">
                                            <h1 style="font-size: 100px;">{{ $banner->circle_number }}</h1>
                                            <div class="d-flex flex-column">
                                                <span class="h2 mb-0">{{ $banner->circle_value ?? '' }}</span>
                                                <span class="h4 text-muted mb-0">{{ $banner->circle_unit ?? '' }}</span>
                                            </div>
                                        </div>
                                    @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                @php $banner = $bannersTop->first(); @endphp
                <div class="row g-4 align-items-center">
                    <div class="col-lg-6">
                        <div class="py-4">
                            <h1 class="display-3 text-white">{{ $banner->title ?? 'Welcome' }}</h1>
                            @if(!empty($banner?->subtitle))
                                <p class="fw-normal display-3 text-dark mb-4">{{ $banner->subtitle }}</p>
                            @endif
                            @if(!empty($banner?->button_text))
                                <a href="{{ $banner->button_url ?? '#' }}" class="banner-btn btn border-2 border-white rounded-pill text-dark py-3 px-5">{{ $banner->button_text }}</a>
                            @endif
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="position-relative">
                            <img src="{{ asset($banner->image_path ?? 'fruitables/img/fruite-item-1.jpg') }}" class="img-fluid w-100 rounded" alt="">
                            @if($banner->show_circle && $banner->circle_number)
                                        <div class="d-flex align-items-center justify-content-center bg-white rounded-circle position-absolute" style="width: 140px; height: 140px; top: 0; left: 0;">
                                            <h1 style="font-size: 60px;">{{ $banner->circle_number }}</h1>
                                            <div class="d-flex flex-column">
                                                <span class="h2 mb-0">{{ $banner->circle_value ?? '' }}</span>
                                                <span class="h5 text-muted mb-0">{{ $banner->circle_unit ?? '' }}</span>
                                            </div>
                                        </div>
                                    @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function(){
      if (window.jQuery && jQuery.fn.owlCarousel) {
        const tCount = {{ $topCount }};
        if (tCount > 1) {
          jQuery('.banners-top-carousel').owlCarousel({
            items: 1,
            margin: 0,
            loop: true,
            autoplay: true,
            autoplayTimeout: 4500,
            autoplayHoverPause: true,
            smartSpeed: 650,
            dotsSpeed: 650,
            dragEndSpeed: 550,
            dots: true,
            nav: false
          });
        }
      }
    });
    </script>
    @endpush
    <!-- Banner Section End -->

    <!-- Bestsaler Product Start -->
    <div class="container-fluid py-5">
        <div class="container py-5">
            <div class="text-center mx-auto mb-5" style="max-width: 700px;">
                <h1 class="display-4">{{ $siteSettings['homepage_bestseller_title'] ?? 'Bestseller Products' }}</h1>
                @if(!empty($siteSettings['homepage_bestseller_subtitle']))
                    <p style="color: #495057 !important;">{{ $siteSettings['homepage_bestseller_subtitle'] }}</p>
                @else
                    <p style="color: #495057 !important;">Produk terlaris pilihan pelanggan kami dengan kualitas terbaik dan harga terjangkau.</p>
                @endif
            </div>
            <div class="row g-4">
                @foreach($bestsellerItems as $product)
                    <div class="col-lg-6 col-xl-4">
                        <div class="p-4 rounded bg-light">
                            <div class="row align-items-center">
                                <div class="col-6">
                                    <a href="{{ route('shop.detail', $product->slug ?? '') }}" class="product-image-link">
                                        <img src="{{ asset($product->main_image_path ? ('storage/' . $product->main_image_path) : 'fruitables/img/fruite-item-1.jpg') }}" class="img-fluid rounded-circle w-100" alt="{{ $product->name }}">
                                    </a>
                                </div>
                                <div class="col-6">
                                    <a href="{{ route('shop.detail', $product->slug ?? '') }}" class="product-name-link text-decoration-none">
                                        <h5 class="text-dark mb-0">{{ $product->name }}</h5>
                                    </a>
                                    <div class="d-flex my-3">
                                        <i class="fas fa-star text-primary"></i>
                                        <i class="fas fa-star text-primary"></i>
                                        <i class="fas fa-star text-primary"></i>
                                        <i class="fas fa-star"></i>
                                    </div>
                                    <h4 class="mb-3">{{ $siteSettings['currency_symbol'] ?? 'Rp.' }} {{ number_format($product->price, 0, ',', '.') }}</h4>
                                    <form action="{{ route('cart.add') }}" method="POST" class="add-to-cart-form">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $product->id }}">
                                        <input type="hidden" name="name" value="{{ $product->name }}">
                                        <input type="hidden" name="price" value="{{ $product->price }}">
                                        <input type="hidden" name="image" value="{{ $product->main_image_path ? ('storage/' . $product->main_image_path) : '' }}">
                                        <input type="hidden" name="qty" value="1">
                                        <button class="btn border border-secondary rounded-pill px-3 text-primary add-to-cart-btn" type="submit">
                                            <span class="btn-text"><i class="fa fa-shopping-bag me-2 text-primary"></i> {{ $siteSettings['add_to_cart_text'] ?? 'Tambah ke Keranjang' }}</span>
                                            <span class="btn-loading d-none"><span class="spinner-border spinner-border-sm me-2"></span>Menambahkan...</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    <!-- Bestsaler Product End -->

    <!-- Fact Start -->
    <!-- <div class="container-fluid py-5">
        <div class="container">
            <div class="bg-light p-5 rounded">
                <div class="row g-4 justify-content-center">
                    @foreach($facts as $fact)
                        <div class="col-md-6 col-lg-6 col-xl-3">
                            <div class="counter bg-white rounded p-5">
                                @if($fact->icon_class)
                                    <i class="{{ $fact->icon_class }} text-secondary"></i>
                                @endif
                                <h4>{{ $fact->label }}</h4>
                                <h1>{{ number_format($fact->value) }}</h1>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div> -->
    <!-- Fact End -->

    <!-- Testimonial Start -->
    <div class="container-fluid testimonial py-5">
        <div class="container py-5">
            <div class="testimonial-header text-center">
                <h4 class="text-primary">{{ $siteSettings['homepage_testimonial_title'] ?? 'Testimoni Kami' }}</h4>
                <h1 class="display-5 mb-5 text-dark">{{ $siteSettings['homepage_testimonial_subtitle'] ?? 'Apa Kata Pelanggan Kami!' }}</h1>
            </div>
            <div class="owl-carousel testimonial-carousel">
                @foreach($testimonials as $t)
                    <div class="testimonial-item img-border-radius bg-light rounded p-4">
                        <div class="position-relative">
                            <i class="fa fa-quote-right fa-2x text-secondary position-absolute" style="bottom: 30px; right: 0;"></i>
                            <div class="mb-4 pb-4 border-bottom border-secondary">
                                <p class="mb-0">{{ $t->content }}</p>
                            </div>
                            <div class="d-flex align-items-center flex-nowrap">
                                <div class="bg-secondary rounded">
                                    <img src="{{ $t->avatar_path ? asset($t->avatar_path) : asset('fruitables/img/testimonial-1.jpg') }}" class="img-fluid rounded" style="width: 100px; height: 100px;" alt="">
                                </div>
                                <div class="ms-4 d-block">
                                    <h4 class="text-dark">{{ $t->author_name }}</h4>
                                    <p class="m-0 pb-3">{{ $t->author_title ?? 'Customer' }}</p>
                                    <div class="d-flex pe-5">
                                        @for($i=1;$i<=5;$i++)
                                            <i class="fas fa-star {{ $i <= ($t->rating ?? 5) ? 'text-primary' : '' }}"></i>
                                        @endfor
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    <!-- Testimonial End -->

    

    <!-- Floating CTA Button -->
    <a href="{{ route('shop') }}" class="floating-cta" title="Belanja Sekarang">
        <i class="fas fa-shopping-cart"></i>
    </a>

    <!-- Back to Top -->
    <a href="#" class="btn btn-primary border-3 border-primary rounded-circle back-to-top"><i class="fa fa-arrow-up"></i></a>

@endsection
