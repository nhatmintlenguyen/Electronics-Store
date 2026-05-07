<?php
declare(strict_types=1);

function getCurrentLanguage(): string
{
    return $_SESSION['lang'] ?? 'en';
}

function setLanguage(string $lang): void
{
    $_SESSION['lang'] = 'en';
}

function t(string $key, ?string $lang = null): string
{
    if ($lang === null) {
        $lang = getCurrentLanguage();
    }

    $translations = [
        'home' => 'Home',
        'products' => 'Products',
        'categories' => 'Categories',
        'deals' => 'Deals',
        'support' => 'Support',
        'my_orders' => 'My Orders',
        'all_categories' => 'All Categories',
        'account' => 'Account',
        'login' => 'Login',
        'register' => 'Register',
        'logout' => 'Logout',
        'profile' => 'Profile',
        'wishlist' => 'Wishlist',
        'cart' => 'Cart',
        'add_to_cart' => 'Add to Cart',
        'buy_now' => 'Buy Now',
        'checkout' => 'Checkout',
        'product_details' => 'Product Details',
        'view_details' => 'View Details',
        'in_stock' => 'In Stock',
        'out_of_stock' => 'Out of Stock',
        'price' => 'Price',
        'description' => 'Description',
        'specifications' => 'Specifications',
        'reviews' => 'Reviews',
        'rating' => 'Rating',
        'search' => 'Search',
        'search_placeholder' => 'Search for products, accessories...',
        'search_results' => 'Search Results',
        'welcome_title' => 'The New Era of Performance',
        'welcome_subtitle' => 'Experience the latest flagship technology with unparalleled speed, precision, and futuristic design.',
        'shop_now' => 'Shop Now',
        'learn_more' => 'Learn More',
        'featured_products' => 'Featured Products',
        'new_arrival' => 'New Arrival',
        'browse_categories' => 'Browse Categories',
        'laptops' => 'Laptops',
        'smartphones' => 'Smartphones',
        'audio' => 'Audio',
        'gaming' => 'Gaming',
        'wearables' => 'Wearables',
        'accessories' => 'Accessories',
        'tablets' => 'Tablets',
        'computing' => 'Computing',
        'filter' => 'Filter',
        'brand' => 'Brand',
        'price_range' => 'Price Range',
        'sort_by' => 'Sort By',
        'reset' => 'Reset',
        'email' => 'Email',
        'password' => 'Password',
        'confirm_password' => 'Confirm Password',
        'full_name' => 'Full Name',
        'phone' => 'Phone',
        'address' => 'Address',
        'submit' => 'Submit',
        'cancel' => 'Cancel',
        'save' => 'Save',
        'email_address' => 'Email Address',
        'forgot_password' => 'Forgot Password?',
        'remember_me' => 'Keep me logged in',
        'sign_in' => 'Sign In',
        'sign_up' => 'Sign Up',
        'or_continue_with' => 'Or continue with',
        'no_account' => "Don't have an account?",
        'have_account' => 'Already have an account?',
        'sign_up_free' => 'Sign up for free',
        'welcome_back' => 'Welcome Back',
        'access_hub' => 'Access your performance electronics hub',
        'free_shipping' => 'Free Shipping',
        'free_shipping_desc' => 'On orders over 1,000,000₫',
        'secure_payment' => 'Secure Payment',
        'warranty' => 'Warranty',
        'customer_support' => 'Customer Support',
        'about_us' => 'About Us',
        'contact' => 'Contact',
        'privacy_policy' => 'Privacy Policy',
        'terms_of_service' => 'Terms of Service',
        'all_rights_reserved' => 'All rights reserved',
        'store_availability' => 'Store Availability',
        'see_all' => 'See All',
        'add_to_wishlist' => 'Add to Wishlist',
        'quantity' => 'Quantity',
    ];

    return $translations[$key] ?? $key;
}

function formatPriceVND(float|int|string $price): string
{
    return number_format((float) $price, 0, ',', '.') . '₫';
}

if (!isset($_SESSION['lang']) || $_SESSION['lang'] !== 'en') {
    $_SESSION['lang'] = 'en';
}
