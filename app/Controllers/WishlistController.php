<?php
declare(strict_types=1);

class WishlistController
{
    public function index(): void
    {
        $wishlistIds = array_values(array_unique(array_map('intval', $_SESSION['wishlist'] ?? [])));

        view('pages/wishlist.php', [
            'page_title' => t('wishlist'),
            'page_description' => 'Wishlist products saved temporarily in the current session.',
            'wishlistProducts' => Product::findManyWithCategory(getDBConnection(), $wishlistIds),
        ]);
    }
}
