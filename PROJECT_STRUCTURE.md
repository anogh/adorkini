# Ador Kini Theme Project Structure

## Project Overview
This project is a custom WordPress WooCommerce theme named "Ador Kini". It features a modern, responsive design with specific optimizations for mobile users and performance (including a preloader and critical CSS). It supports bilingual content (English and Bengali) and custom product ranking.

## File Descriptions

### Core Templates
- **front-page.php**: The main homepage template. It has separate structures for Desktop and Mobile.
  - **Desktop**: Hero section with vertical category sidebar, Top 10 Best Sellers grid, New Arrivals, Testimonials, and Promotional Banner.
  - **Mobile**: Hero slider, Horizontal scrolling categories, Vertical Best Sellers list, 2-column New Arrivals grid, Vertical Testimonials, and Promotional Banner.
- **header.php**: 
  - Contains HTML `<head>` with critical CSS and preloader logic (specially handled for Facebook WebViews).
  - **Desktop Header**: Logo, Search Bar, Navigation Links, Language Toggle, and Icons (Wishlist, Account, Cart).
  - **Mobile Header**: Dynamic header that changes based on the page (Cart, My Love, or Default with Logo/Search).
- **footer.php**: 
  - **Desktop Footer**: 4-column layout (Shop, Support, About, Newsletter) and copyright bar.
  - **Mobile Bottom Nav**: Fixed bottom navigation bar with Home, Categories, Cart, My Love, and Profile icons.
- **functions.php**: Main theme logic, including enqueueing scripts, custom post types, WooCommerce hooks, and helper functions.

### Page Templates
- **page-categories.php**: Template for the categories page.
- **page-my-account.php** & **page-my-account-simple.php**: Custom layouts for the My Account section.
- **single-product.php**: Custom product details page layout.
- **archive-product.php**: Usage for product listings/archives.
- **page-login.php**, **page-register.php**, **page-forgot-password.php**: Custom authentication pages.

### Assets & Configuration
- **style.css**: Main stylesheet (Tailwind CSS utilities are likely used in markup, but this file contains theme definitions).
- **translations.json**: JSON file handling translations (English/Bengali) for the custom `__t()` function.
- **CRITICAL_RULES.md**: Contains strict rules for development, particularly regarding SVG icons, mobile visibility classes, and code integrity.

## Key Features & Implementations
- **Icon System**: Uses SVG icons (Material Symbols) rendered via `data-icon` attributes and background images, not font files.
- **Mobile First/Responsive**: distinct HTML structures for mobile vs desktop in several templates (using `lg:hidden` / `hidden lg:block`).
- **Preloader**: A custom preloader that handles specific user agents (like Facebook WebView) to avoid display issues.
- **YouTube Video Gallery**: Products can have a YouTube video URL set in admin. If set, a "Video Gallery" section appears below the product image gallery on the product detail page (both desktop and mobile). The video field is added under "Product Data" → "General" tab in WooCommerce product edit page.

site link: adorkini.com