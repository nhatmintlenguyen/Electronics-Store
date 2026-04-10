<?php
declare(strict_types=1);

function getCurrentLanguage(): string
{
    return $_SESSION['lang'];
}

function setLanguage(string $lang): void
{
    $_SESSION['lang'] = 'vi';
}

function t(string $key, ?string $lang = null): string
{
    if ($lang === null) {
        $lang = getCurrentLanguage();
    }

    $translations = [
        'home' => ['vi' => 'Trang chủ', 'en' => 'Home'],
        'products' => ['vi' => 'Sản phẩm', 'en' => 'Products'],
        'categories' => ['vi' => 'Danh mục', 'en' => 'Categories'],
        'deals' => ['vi' => 'Khuyến mãi', 'en' => 'Deals'],
        'support' => ['vi' => 'Hỗ trợ', 'en' => 'Support'],
        'my_orders' => ['vi' => 'Đơn hàng', 'en' => 'My Orders'],
        'all_categories' => ['vi' => 'Tất cả danh mục', 'en' => 'All Categories'],
        'account' => ['vi' => 'Tài khoản', 'en' => 'Account'],
        'login' => ['vi' => 'Đăng nhập', 'en' => 'Login'],
        'register' => ['vi' => 'Đăng ký', 'en' => 'Register'],
        'logout' => ['vi' => 'Đăng xuất', 'en' => 'Logout'],
        'profile' => ['vi' => 'Hồ sơ', 'en' => 'Profile'],
        'wishlist' => ['vi' => 'Yêu thích', 'en' => 'Wishlist'],
        'cart' => ['vi' => 'Giỏ hàng', 'en' => 'Cart'],
        'add_to_cart' => ['vi' => 'Thêm vào giỏ', 'en' => 'Add to Cart'],
        'buy_now' => ['vi' => 'Mua ngay', 'en' => 'Buy Now'],
        'checkout' => ['vi' => 'Thanh toán', 'en' => 'Checkout'],
        'product_details' => ['vi' => 'Chi tiết sản phẩm', 'en' => 'Product Details'],
        'view_details' => ['vi' => 'Xem chi tiết', 'en' => 'View Details'],
        'in_stock' => ['vi' => 'Còn hàng', 'en' => 'In Stock'],
        'out_of_stock' => ['vi' => 'Hết hàng', 'en' => 'Out of Stock'],
        'price' => ['vi' => 'Giá', 'en' => 'Price'],
        'description' => ['vi' => 'Mô tả', 'en' => 'Description'],
        'specifications' => ['vi' => 'Thông số kỹ thuật', 'en' => 'Specifications'],
        'reviews' => ['vi' => 'Đánh giá', 'en' => 'Reviews'],
        'rating' => ['vi' => 'Xếp hạng', 'en' => 'Rating'],
        'search' => ['vi' => 'Tìm kiếm', 'en' => 'Search'],
        'search_placeholder' => ['vi' => 'Tìm kiếm sản phẩm, phụ kiện...', 'en' => 'Search for products, accessories...'],
        'search_results' => ['vi' => 'Kết quả tìm kiếm', 'en' => 'Search Results'],
        'welcome_title' => ['vi' => 'Kỷ nguyên mới của hiệu suất', 'en' => 'The New Era of Performance'],
        'welcome_subtitle' => ['vi' => 'Trải nghiệm công nghệ hàng đầu với tốc độ, độ chính xác và thiết kế tương lai vượt trội.', 'en' => 'Experience the latest flagship technology with unparalleled speed, precision, and futuristic design.'],
        'shop_now' => ['vi' => 'Mua ngay', 'en' => 'Shop Now'],
        'learn_more' => ['vi' => 'Tìm hiểu thêm', 'en' => 'Learn More'],
        'featured_products' => ['vi' => 'Sản phẩm nổi bật', 'en' => 'Featured Products'],
        'new_arrival' => ['vi' => 'Hàng mới về', 'en' => 'New Arrival'],
        'browse_categories' => ['vi' => 'Duyệt danh mục', 'en' => 'Browse Categories'],
        'laptops' => ['vi' => 'Laptop', 'en' => 'Laptops'],
        'smartphones' => ['vi' => 'Điện thoại', 'en' => 'Smartphones'],
        'audio' => ['vi' => 'Âm thanh', 'en' => 'Audio'],
        'gaming' => ['vi' => 'Gaming', 'en' => 'Gaming'],
        'wearables' => ['vi' => 'Thiết bị đeo', 'en' => 'Wearables'],
        'accessories' => ['vi' => 'Phụ kiện', 'en' => 'Accessories'],
        'tablets' => ['vi' => 'Máy tính bảng', 'en' => 'Tablets'],
        'computing' => ['vi' => 'Máy tính', 'en' => 'Computing'],
        'filter' => ['vi' => 'Lọc', 'en' => 'Filter'],
        'brand' => ['vi' => 'Thương hiệu', 'en' => 'Brand'],
        'price_range' => ['vi' => 'Khoảng giá', 'en' => 'Price Range'],
        'sort_by' => ['vi' => 'Sắp xếp theo', 'en' => 'Sort By'],
        'reset' => ['vi' => 'Đặt lại', 'en' => 'Reset'],
        'email' => ['vi' => 'Email', 'en' => 'Email'],
        'password' => ['vi' => 'Mật khẩu', 'en' => 'Password'],
        'confirm_password' => ['vi' => 'Xác nhận mật khẩu', 'en' => 'Confirm Password'],
        'full_name' => ['vi' => 'Họ và tên', 'en' => 'Full Name'],
        'phone' => ['vi' => 'Số điện thoại', 'en' => 'Phone'],
        'address' => ['vi' => 'Địa chỉ', 'en' => 'Address'],
        'submit' => ['vi' => 'Gửi', 'en' => 'Submit'],
        'cancel' => ['vi' => 'Hủy', 'en' => 'Cancel'],
        'save' => ['vi' => 'Lưu', 'en' => 'Save'],
        'email_address' => ['vi' => 'Địa chỉ Email', 'en' => 'Email Address'],
        'forgot_password' => ['vi' => 'Quên mật khẩu?', 'en' => 'Forgot Password?'],
        'remember_me' => ['vi' => 'Ghi nhớ đăng nhập', 'en' => 'Keep me logged in'],
        'sign_in' => ['vi' => 'Đăng nhập', 'en' => 'Sign In'],
        'sign_up' => ['vi' => 'Đăng ký', 'en' => 'Sign Up'],
        'or_continue_with' => ['vi' => 'Hoặc tiếp tục với', 'en' => 'Or continue with'],
        'no_account' => ['vi' => 'Chưa có tài khoản?', 'en' => "Don't have an account?"],
        'have_account' => ['vi' => 'Đã có tài khoản?', 'en' => 'Already have an account?'],
        'sign_up_free' => ['vi' => 'Đăng ký miễn phí', 'en' => 'Sign up for free'],
        'welcome_back' => ['vi' => 'Chào mừng trở lại', 'en' => 'Welcome Back'],
        'access_hub' => ['vi' => 'Truy cập trung tâm điện tử hiệu năng của bạn', 'en' => 'Access your performance electronics hub'],
        'free_shipping' => ['vi' => 'Miễn phí vận chuyển', 'en' => 'Free Shipping'],
        'free_shipping_desc' => ['vi' => 'Cho đơn hàng trên 1,000,000₫', 'en' => 'On orders over 1,000,000₫'],
        'secure_payment' => ['vi' => 'Thanh toán an toàn', 'en' => 'Secure Payment'],
        'warranty' => ['vi' => 'Bảo hành', 'en' => 'Warranty'],
        'customer_support' => ['vi' => 'Hỗ trợ khách hàng', 'en' => 'Customer Support'],
        'about_us' => ['vi' => 'Về chúng tôi', 'en' => 'About Us'],
        'contact' => ['vi' => 'Liên hệ', 'en' => 'Contact'],
        'privacy_policy' => ['vi' => 'Chính sách bảo mật', 'en' => 'Privacy Policy'],
        'terms_of_service' => ['vi' => 'Điều khoản dịch vụ', 'en' => 'Terms of Service'],
        'all_rights_reserved' => ['vi' => 'Mọi quyền được bảo lưu', 'en' => 'All rights reserved'],
        'store_availability' => ['vi' => 'Tình trạng cửa hàng', 'en' => 'Store Availability'],
        'see_all' => ['vi' => 'Xem tất cả', 'en' => 'See All'],
        'add_to_wishlist' => ['vi' => 'Thêm vào yêu thích', 'en' => 'Add to Wishlist'],
        'quantity' => ['vi' => 'Số lượng', 'en' => 'Quantity'],
    ];

    return $translations[$key][$lang] ?? $key;
}

function formatPriceVND(float|int|string $price): string
{
    return number_format((float) $price, 0, ',', '.') . '₫';
}

if (!isset($_SESSION['lang']) || $_SESSION['lang'] !== 'vi') {
    $_SESSION['lang'] = 'vi';
}
