<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$page_title = t('home');
include 'includes/header.php';

// Fetch featured products
$conn = getDBConnection();
$stmt = $conn->prepare("
    SELECT p.*, c.name as category_name 
    FROM products p 
    JOIN categories c ON p.category_id = c.id 
    ORDER BY p.rating DESC 
    LIMIT 8
");
$stmt->execute();
$featured_products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch categories
$stmt = $conn->prepare("SELECT * FROM categories LIMIT 6");
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Hero Section -->
<section class="mb-12">
    <div class="relative rounded-xl overflow-hidden bg-slate-900 aspect-[21/9] flex items-center group">
        <div class="absolute inset-0 bg-cover bg-center transition-transform duration-700 group-hover:scale-105" 
             style="background-image: url('https://images.unsplash.com/photo-1550745165-9bc0b252726f?auto=format&fit=crop&q=80&w=1920');">
            <div class="absolute inset-0 bg-gradient-to-r from-black/80 via-black/40 to-transparent"></div>
        </div>
        <div class="relative z-10 px-8 lg:px-16 max-w-2xl">
            <span class="inline-block px-3 py-1 bg-primary text-white text-xs font-bold rounded-full mb-4"><?php echo strtoupper(t('new_arrival')); ?></span>
            <h1 class="text-4xl lg:text-6xl font-black text-white leading-tight mb-6">
                <?php echo t('welcome_title'); ?>
            </h1>
            <p class="text-lg text-slate-200 mb-8 max-w-lg">
                <?php echo t('welcome_subtitle'); ?>
            </p>
            <div class="flex flex-wrap gap-4">
                <a href="products.php" class="bg-primary hover:bg-primary/90 text-white px-8 py-3.5 rounded-lg font-bold transition-all transform hover:scale-105 active:scale-95 shadow-lg shadow-primary/25">
                    <?php echo t('shop_now'); ?>
                </a>
                <a href="about.php" class="bg-white/10 hover:bg-white/20 backdrop-blur-md text-white border border-white/30 px-8 py-3.5 rounded-lg font-bold transition-all transform hover:scale-105">
                    <?php echo t('learn_more'); ?>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Category Grid -->
<section class="mb-12">
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        <?php 
        $category_icons = ['laptop_mac', 'smartphone', 'headphones', 'watch', 'sports_esports', 'mouse'];
        foreach ($categories as $index => $category): 
        ?>
        <a href="products.php?category=<?php echo $category['id']; ?>" class="flex flex-col items-center p-4 bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 hover:border-primary transition-all cursor-pointer group">
            <div class="w-16 h-16 rounded-full bg-primary/10 flex items-center justify-center text-primary mb-3 group-hover:bg-primary group-hover:text-white transition-colors">
                <span class="material-symbols-outlined text-3xl"><?php echo $category_icons[$index] ?? 'devices'; ?></span>
            </div>
            <span class="text-sm font-semibold"><?php echo htmlspecialchars($category['name']); ?></span>
        </a>
        <?php endforeach; ?>
    </div>
</section>

<!-- Featured Products Header -->
<div class="flex items-center justify-between mb-8">
    <h2 class="text-2xl font-bold text-slate-900 dark:text-white"><?php echo t('featured_products'); ?></h2>
    <a href="products.php" class="text-primary hover:underline font-medium"><?php echo t('see_all'); ?> →</a>
</div>

<!-- Product Grid -->
<section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-16">
    <?php foreach ($featured_products as $product): ?>
    <!-- Product Card -->
    <div class="product-card group relative bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 overflow-hidden hover:shadow-xl transition-all duration-300">
        <a href="product_detail.php?id=<?php echo $product['id']; ?>" class="block">
            <div class="aspect-square bg-slate-50 dark:bg-slate-900 p-8 flex items-center justify-center relative overflow-hidden">
                <div class="absolute top-2 right-2 flex flex-col gap-2 z-10">
                    <button class="p-1.5 bg-white/80 backdrop-blur-sm rounded-full text-slate-600 hover:text-red-500 transition-colors shadow-sm">
                        <span class="material-symbols-outlined text-xl">favorite</span>
                    </button>
                </div>
                <?php if ($product['image_url']): ?>
                    <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                         alt="<?php echo htmlspecialchars($product['name']); ?>" 
                         class="object-contain transition-transform duration-500 group-hover:scale-110 max-h-full">
                <?php else: ?>
                    <div class="text-slate-300">
                        <span class="material-symbols-outlined" style="font-size: 80px;">image</span>
                    </div>
                <?php endif; ?>
                <div class="cart-button absolute inset-x-0 bottom-0 p-4 opacity-0 translate-y-4 transition-all duration-300">
                    <button class="w-full bg-primary text-white py-2.5 rounded-lg font-bold shadow-lg shadow-primary/20 flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-lg">add_shopping_cart</span> <?php echo t('add_to_cart'); ?>
                    </button>
                </div>
            </div>
        </a>
        <div class="p-5">
            <span class="text-[10px] font-bold text-primary tracking-widest uppercase mb-1 block"><?php echo htmlspecialchars($product['category_name']); ?></span>
            <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100 mb-2 truncate">
                <a href="product_detail.php?id=<?php echo $product['id']; ?>"><?php echo htmlspecialchars($product['name']); ?></a>
            </h3>
            <div class="flex items-center gap-1 mb-3">
                <span class="material-symbols-outlined text-yellow-400 text-sm" style="font-variation-settings: 'FILL' 1">star</span>
                <span class="text-xs font-bold text-slate-600 dark:text-slate-400"><?php echo number_format($product['rating'], 1); ?></span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-lg font-black text-slate-900 dark:text-white"><?php echo formatPriceVND($product['price']); ?></span>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</section>

<!-- Features Section -->
<section class="mb-12">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="flex flex-col items-center text-center p-6 bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700">
            <div class="w-16 h-16 rounded-full bg-primary/10 flex items-center justify-center text-primary mb-4">
                <span class="material-symbols-outlined text-3xl">local_shipping</span>
            </div>
            <h4 class="font-bold text-slate-900 dark:text-white mb-2"><?php echo t('free_shipping'); ?></h4>
            <p class="text-sm text-slate-600 dark:text-slate-400"><?php echo t('free_shipping_desc'); ?></p>
        </div>
        <div class="flex flex-col items-center text-center p-6 bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700">
            <div class="w-16 h-16 rounded-full bg-primary/10 flex items-center justify-center text-primary mb-4">
                <span class="material-symbols-outlined text-3xl">verified_user</span>
            </div>
            <h4 class="font-bold text-slate-900 dark:text-white mb-2"><?php echo t('secure_payment'); ?></h4>
            <p class="text-sm text-slate-600 dark:text-slate-400"><?php echo getCurrentLanguage() == 'vi' ? '100% bảo mật thanh toán' : '100% secure payment'; ?></p>
        </div>
        <div class="flex flex-col items-center text-center p-6 bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700">
            <div class="w-16 h-16 rounded-full bg-primary/10 flex items-center justify-center text-primary mb-4">
                <span class="material-symbols-outlined text-3xl">support_agent</span>
            </div>
            <h4 class="font-bold text-slate-900 dark:text-white mb-2"><?php echo t('customer_support'); ?></h4>
            <p class="text-sm text-slate-600 dark:text-slate-400"><?php echo getCurrentLanguage() == 'vi' ? 'Hỗ trợ 24/7' : '24/7 support'; ?></p>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
