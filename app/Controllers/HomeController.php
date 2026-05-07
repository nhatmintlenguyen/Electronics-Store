<?php
declare(strict_types=1);

class HomeController
{
    public function index(): void
    {
        $conn = getDBConnection();
        $categories = Category::all($conn);
        $products = Product::latest($conn, 20);

        view('pages/home.php', [
            'page_title' => t('home'),
            'page_description' => 'Trang chủ TechStore với giao diện dạng tạp chí, hero banner, danh mục và sản phẩm nổi bật.',
            'totalProducts' => Product::countAll($conn),
            'products' => $products,
            'featuredProducts' => Product::featured($conn, 4),
            'categories' => $categories,
            'homeCategories' => array_slice($categories, 0, 6),
            'categoryIcons' => [
                'laptop_mac',
                'smartphone',
                'headphones',
                'watch',
                'sports_esports',
                'memory',
            ],
            'promoProducts' => array_slice($products, 0, 2),
        ]);
    }
}
