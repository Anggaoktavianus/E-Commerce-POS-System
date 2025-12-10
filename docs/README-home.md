# Dynamic Home Page CMS

This document describes the database schema and admin modules that power a fully dynamic home page (navbar, hero/carousel, features, products, banners, bestseller, facts, testimonials, footer).

## Entities & Tables

- Navbar (Menus)
  - navigation_menus: name, location, is_active
  - navigation_links: menu tree items with parent_id, label, url/route_name, target, sort_order, is_active

- Hero & Carousel
  - carousels: name, key (e.g., home_hero), is_active
  - carousel_slides: slide content (title, subtitle, button, images, sort_order, is_active)

- Features
  - features: title, description, icon_class/image_path, sort_order, is_active

- Catalog (Products)
  - categories: hierarchical categories
  - products: core product fields, flags (is_active, is_featured)
  - product_images: gallery per product
  - product_categories: pivot product-category

- Banners
  - banners: content + image + position (home_top|home_middle|home_bottom), sort_order, is_active

- Bestseller (Curated collection)
  - home_collections: key=bestseller
  - home_collection_items: ordered product list within a collection

- Facts (Counters)
  - facts: label + numeric value + optional icon, sort_order, is_active

- Testimonials
  - testimonials: author, avatar, content, rating, sort_order, is_active

- Footer
  - social_links: platform, url, icon_class, sort_order, is_active
  - settings: site-wide key/value (brand_name, logo_path, contacts, newsletter_text, payment_image_path, etc.)

## Admin Navigation (Suggested)

- Dashboard
- Catalog
  - Products
  - Categories
  - Collections (Bestseller)
- Content
  - Menus (Header, Footer Columns)
  - Hero / Carousel
  - Features
  - Banners
  - Testimonials
  - Facts
  - Footer Settings
  - Social Links
- Media (optional)
- Settings

## Implementation Notes

- Cache each home block for performance.
- Store uploads in storage public; render via Storage::url().
- Provide seeders for initial demo content.
- Keep Blade sections modular (partials) for clarity and caching.
