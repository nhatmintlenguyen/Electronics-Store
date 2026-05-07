<?php
declare(strict_types=1);

class HomeController
{
    public function index(): void
    {
        $conn = getDBConnection();
        $categories = Category::all($conn);
        $featuredProductIds = [1, 15, 67, 140];
        $promoProductIds = [635, 436];
        $promoProducts = Product::featuredByIds($conn, $promoProductIds);
        $promoCards = [
            [
                'eyebrow' => 'Upgrade',
                'title' => 'Upgrade Your Home Office',
                'description' => 'Build an efficient study and work setup with monitors, laptops, and accessories.',
                'product' => $promoProducts[0] ?? null,
            ],
            [
                'eyebrow' => 'Essentials',
                'title' => 'Minimal Gaming Setup',
                'description' => 'Explore popular audio, gaming, and accessory picks.',
                'product' => $promoProducts[1] ?? null,
            ],
        ];

        view('pages/home.php', [
            'page_title' => t('home'),
            'page_description' => 'TechStore homepage with a magazine-style layout, hero banner, categories, and featured products.',
            'totalProducts' => Product::countAll($conn),
            'featuredProducts' => Product::featuredByIds($conn, $featuredProductIds),
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
            'promoCards' => $promoCards,
        ]);
    }
}
