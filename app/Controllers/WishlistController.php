<?php
declare(strict_types=1);

class WishlistController
{
    public function index(): void
    {
        $wishlistIds = array_values(array_unique(array_map('intval', $_SESSION['wishlist'] ?? [])));

        view('pages/wishlist.php', [
            'page_title' => t('wishlist'),
            'page_description' => 'Danh sách sản phẩm yêu thích được lưu tạm trong phiên làm việc.',
            'wishlistProducts' => Product::findManyWithCategory(getDBConnection(), $wishlistIds),
        ]);
    }
}
