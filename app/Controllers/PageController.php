<?php
declare(strict_types=1);

class PageController
{
    public function about(): void
    {
        view('pages/about.php', [
            'page_title' => t('about_us'),
            'page_description' => 'Thông tin giới thiệu về TechStore và cách website đáp ứng yêu cầu đồ án.',
        ]);
    }

    public function contact(): void
    {
        view('pages/contact.php', [
            'page_title' => t('contact'),
            'page_description' => 'Thông tin liên hệ, giới thiệu website và cách kết nối với TechStore.',
        ]);
    }

    public function locations(): void
    {
        view('pages/locations.php', [
            'page_title' => 'Cửa hàng',
            'page_description' => 'Danh sách các cửa hàng TechStore kèm địa chỉ và liên kết Google Maps.',
            'locations' => Location::all(getDBConnection()),
        ]);
    }

    public function profile(): void
    {
        requireLogin();

        $user = User::findProfile(getDBConnection(), (int) $_SESSION['user_id']);

        if (!$user) {
            redirectTo('logout.php');
        }

        view('pages/profile.php', [
            'page_title' => t('profile'),
            'page_description' => 'Thông tin tài khoản người dùng đang đăng nhập tại TechStore.',
            'user' => $user,
        ]);
    }
}
