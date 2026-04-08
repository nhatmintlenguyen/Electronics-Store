<?php
require_once __DIR__ . '/../app/bootstrap.php';

$productId = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($productId <= 0) {
    redirectTo('products.php');
}

$conn = getDBConnection();

$productStmt = $conn->prepare(
    'SELECT p.*, c.name AS category_name
     FROM products p
     JOIN categories c ON p.category_id = c.id
     WHERE p.id = :id'
);
$productStmt->execute([':id' => $productId]);
$product = $productStmt->fetch();

if (!$product) {
    redirectTo('products.php');
}

$page_title = $product['name'];
$productDescription = $product['description']
    ?: (getCurrentLanguage() === 'vi'
        ? 'Sản phẩm chất lượng cao với công nghệ tiên tiến và thiết kế hiện đại.'
        : 'High quality product with advanced technology and modern design.');

include APP_PATH . '/Views/layouts/header.php';
?>

<nav class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400 mb-8">
    <a class="hover:text-primary" href="<?php echo url('index.php'); ?>"><?php echo t('home'); ?></a>
    <span class="material-symbols-outlined text-xs">chevron_right</span>
    <a class="hover:text-primary" href="<?php echo url('products.php'); ?>"><?php echo t('products'); ?></a>
    <span class="material-symbols-outlined text-xs">chevron_right</span>
    <a class="hover:text-primary" href="<?php echo url('products.php?category=' . $product['category_id']); ?>"><?php echo htmlspecialchars($product['category_name']); ?></a>
    <span class="material-symbols-outlined text-xs">chevron_right</span>
    <span class="text-slate-900 dark:text-white font-medium"><?php echo htmlspecialchars($product['name']); ?></span>
</nav>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
    <div class="lg:col-span-7 space-y-4">
        <div class="bg-white dark:bg-slate-900 rounded-xl overflow-hidden border border-slate-200 dark:border-slate-800 aspect-[4/3] flex items-center justify-center p-8 group">
            <?php if (!empty($product['image_url'])): ?>
                <img class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-500"
                     src="<?php echo htmlspecialchars($product['image_url']); ?>"
                     alt="<?php echo htmlspecialchars($product['name']); ?>">
            <?php else: ?>
                <div class="text-slate-300">
                    <span class="material-symbols-outlined" style="font-size: 150px;">image</span>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="lg:col-span-5">
        <div class="sticky top-24 space-y-8">
            <div class="space-y-4">
                <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full bg-primary/10 text-primary text-xs font-bold uppercase tracking-wider">
                    <span class="material-symbols-outlined text-xs">verified</span>
                    <?php echo t('in_stock'); ?>
                </div>

                <h1 class="text-4xl font-extrabold text-slate-900 dark:text-white leading-tight">
                    <?php echo htmlspecialchars($product['name']); ?>
                </h1>

                <p class="text-slate-500 dark:text-slate-400 font-medium">
                    SKU: TECH-<?php echo $product['id']; ?> •
                    <?php echo t('rating'); ?>:
                    <span class="text-yellow-400">★</span>
                    <?php echo number_format((float) $product['rating'], 1); ?>
                </p>

                <div class="pt-4 flex items-baseline gap-4">
                    <span class="text-5xl font-black text-slate-900 dark:text-white tracking-tighter">
                        <?php echo formatPriceVND($product['price']); ?>
                    </span>
                </div>
            </div>

            <div class="flex flex-col gap-4">
                <div class="flex gap-4">
                    <div class="w-24 h-12 flex items-center justify-between px-3 border border-slate-200 dark:border-slate-800 rounded-lg">
                        <button class="text-slate-400 hover:text-primary" onclick="decreaseQty()">
                            <span class="material-symbols-outlined text-lg">remove</span>
                        </button>
                        <span id="quantity" class="font-bold">1</span>
                        <button class="text-slate-400 hover:text-primary" onclick="increaseQty()">
                            <span class="material-symbols-outlined text-lg">add</span>
                        </button>
                    </div>
                    <button onclick="addToCart(<?php echo (int) $product['id']; ?>, qty)" class="flex-1 bg-primary hover:bg-primary/90 text-white h-12 rounded-lg font-bold text-lg flex items-center justify-center gap-2 transition-all shadow-lg shadow-primary/20">
                        <span class="material-symbols-outlined">shopping_bag</span>
                        <?php echo t('add_to_cart'); ?>
                    </button>
                </div>

                <button onclick="addToWishlist(<?php echo (int) $product['id']; ?>)" class="w-full h-12 border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800 rounded-lg font-bold text-slate-700 dark:text-slate-300 flex items-center justify-center gap-2 transition-colors">
                    <span class="material-symbols-outlined">favorite</span>
                    <?php echo t('add_to_wishlist'); ?>
                </button>
            </div>

            <div class="space-y-4 pt-6 border-t border-slate-200 dark:border-slate-800">
                <h3 class="font-bold text-slate-900 dark:text-white"><?php echo t('description'); ?></h3>
                <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                    <?php echo nl2br(htmlspecialchars($productDescription)); ?>
                </p>
            </div>

            <div class="space-y-3 pt-6 border-t border-slate-200 dark:border-slate-800">
                <div class="flex items-center gap-3 text-sm">
                    <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center text-primary">
                        <span class="material-symbols-outlined text-lg">local_shipping</span>
                    </div>
                    <div>
                        <p class="font-semibold text-slate-900 dark:text-white"><?php echo t('free_shipping'); ?></p>
                        <p class="text-slate-500 dark:text-slate-400 text-xs"><?php echo t('free_shipping_desc'); ?></p>
                    </div>
                </div>

                <div class="flex items-center gap-3 text-sm">
                    <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center text-primary">
                        <span class="material-symbols-outlined text-lg">verified_user</span>
                    </div>
                    <div>
                        <p class="font-semibold text-slate-900 dark:text-white"><?php echo getCurrentLanguage() === 'vi' ? 'Bảo hành 12 tháng' : '12 Month Warranty'; ?></p>
                        <p class="text-slate-500 dark:text-slate-400 text-xs"><?php echo getCurrentLanguage() === 'vi' ? 'Bảo hành chính hãng' : 'Official warranty'; ?></p>
                    </div>
                </div>

                <div class="flex items-center gap-3 text-sm">
                    <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center text-primary">
                        <span class="material-symbols-outlined text-lg">swap_horiz</span>
                    </div>
                    <div>
                        <p class="font-semibold text-slate-900 dark:text-white"><?php echo getCurrentLanguage() === 'vi' ? 'Đổi trả 7 ngày' : '7 Days Return'; ?></p>
                        <p class="text-slate-500 dark:text-slate-400 text-xs"><?php echo getCurrentLanguage() === 'vi' ? 'Đổi trả miễn phí' : 'Free return'; ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$relatedStmt = $conn->prepare(
    'SELECT p.*, c.name AS category_name
     FROM products p
     JOIN categories c ON p.category_id = c.id
     WHERE p.category_id = :category_id AND p.id != :id
     ORDER BY RAND()
     LIMIT 4'
);
$relatedStmt->execute([
    ':category_id' => $product['category_id'],
    ':id' => $productId,
]);
$relatedProducts = $relatedStmt->fetchAll();
?>

<?php if (count($relatedProducts) > 0): ?>
<section class="mt-16 pt-12 border-t border-slate-200 dark:border-slate-800">
    <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-8">
        <?php echo getCurrentLanguage() === 'vi' ? 'Sản phẩm liên quan' : 'Related Products'; ?>
    </h2>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <?php foreach ($relatedProducts as $relatedProduct): ?>
        <div class="product-card group relative bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 overflow-hidden hover:shadow-xl transition-all duration-300">
            <a href="<?php echo url('product_detail.php?id=' . $relatedProduct['id']); ?>" class="block">
                <div class="aspect-square bg-slate-50 dark:bg-slate-900 p-8 flex items-center justify-center relative overflow-hidden">
                    <?php if (!empty($relatedProduct['image_url'])): ?>
                        <img src="<?php echo htmlspecialchars($relatedProduct['image_url']); ?>"
                             alt="<?php echo htmlspecialchars($relatedProduct['name']); ?>"
                             class="object-contain transition-transform duration-500 group-hover:scale-110 max-h-full">
                    <?php else: ?>
                        <div class="text-slate-300">
                            <span class="material-symbols-outlined" style="font-size: 80px;">image</span>
                        </div>
                    <?php endif; ?>
                </div>
            </a>
            <div class="p-5">
                <span class="text-[10px] font-bold text-primary tracking-widest uppercase mb-1 block">
                    <?php echo htmlspecialchars($relatedProduct['category_name']); ?>
                </span>
                <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100 mb-2 truncate">
                    <a href="<?php echo url('product_detail.php?id=' . $relatedProduct['id']); ?>">
                        <?php echo htmlspecialchars($relatedProduct['name']); ?>
                    </a>
                </h3>
                <div class="flex items-center gap-1 mb-3">
                    <span class="material-symbols-outlined text-yellow-400 text-sm" style="font-variation-settings: 'FILL' 1">star</span>
                    <span class="text-xs font-bold text-slate-600 dark:text-slate-400">
                        <?php echo number_format((float) $relatedProduct['rating'], 1); ?>
                    </span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-lg font-black text-slate-900 dark:text-white">
                        <?php echo formatPriceVND($relatedProduct['price']); ?>
                    </span>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<script>
    let qty = 1;

    function increaseQty() {
        qty += 1;
        document.getElementById('quantity').textContent = qty;
    }

    function decreaseQty() {
        if (qty > 1) {
            qty -= 1;
            document.getElementById('quantity').textContent = qty;
        }
    }
</script>

<?php include APP_PATH . '/Views/layouts/footer.php'; ?>
