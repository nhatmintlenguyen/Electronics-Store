<?php
require_once __DIR__ . '/../app/bootstrap.php';

$page_title = t('home');
$conn = getDBConnection();

$perPage = 20;
$currentPage = max(1, (int) ($_GET['page'] ?? 1));

$totalStmt = $conn->query('SELECT COUNT(*) FROM products');
$totalProducts = (int) $totalStmt->fetchColumn();
$totalPages = max(1, (int) ceil($totalProducts / $perPage));
$currentPage = min($currentPage, $totalPages);
$offset = ($currentPage - 1) * $perPage;

$productsStmt = $conn->prepare(
    'SELECT p.*, c.name AS category_name
     FROM products p
     JOIN categories c ON p.category_id = c.id
     ORDER BY p.created_at DESC, p.id DESC
     LIMIT :limit OFFSET :offset'
);
$productsStmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$productsStmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$productsStmt->execute();
$products = $productsStmt->fetchAll();

$categoriesStmt = $conn->query('SELECT id, name FROM categories ORDER BY name ASC');
$categories = $categoriesStmt->fetchAll();

$paginationRange = 2;
$startPage = max(1, $currentPage - $paginationRange);
$endPage = min($totalPages, $currentPage + $paginationRange);

include APP_PATH . '/Views/layouts/header.php';
?>

<section class="mb-8">
    <div class="hero-filter-panel rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 lg:p-8">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-3xl">
                <p class="mb-2 text-xs font-bold uppercase tracking-[0.2em] text-primary">TechStore</p>
                <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">
                    <?php echo getCurrentLanguage() === 'vi'
                        ? 'Trang chu hien thi toan bo san pham va chia thanh tung trang de de duyet hon.'
                        : 'Browse the full catalog from the homepage, with 20 products per page for a simpler flow.'; ?>
                </p>
            </div>
            <div class="rounded-full bg-slate-50 px-4 py-3 text-sm text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                <?php echo getCurrentLanguage() === 'vi'
                    ? 'Tong san pham: '
                    : 'Total products: '; ?>
                <span class="font-bold text-slate-900 dark:text-white"><?php echo number_format($totalProducts); ?></span>
            </div>
        </div>

        <div class="category-pill-row mt-6 flex gap-3 overflow-x-auto pb-2 no-scrollbar" data-category-filters>
            <button
                type="button"
                class="category-pill is-active"
                data-category-button
                data-category-id="all"
                aria-pressed="true"
            >
                <?php echo getCurrentLanguage() === 'vi' ? 'Tat ca' : 'All'; ?>
            </button>
            <?php foreach ($categories as $category): ?>
                <button
                    type="button"
                    class="category-pill"
                    data-category-button
                    data-category-id="<?php echo (int) $category['id']; ?>"
                    aria-pressed="false"
                >
                    <?php echo htmlspecialchars($category['name']); ?>
                </button>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4" data-product-grid>
    <?php foreach ($products as $product): ?>
    <div class="product-card group relative overflow-hidden rounded-xl border border-slate-100 bg-white transition-all duration-300 hover:shadow-xl dark:border-slate-700 dark:bg-slate-800">
        <a href="<?php echo url('product_detail.php?id=' . $product['id']); ?>" class="block">
            <div class="relative flex aspect-square items-center justify-center overflow-hidden bg-slate-50 p-8 dark:bg-slate-900">
                <div class="absolute right-2 top-2 z-10 flex flex-col gap-2">
                    <button onclick="event.preventDefault(); addToWishlist(<?php echo (int) $product['id']; ?>);" class="rounded-full bg-white/80 p-1.5 text-slate-600 shadow-sm backdrop-blur-sm transition-colors hover:text-red-500">
                        <span class="material-symbols-outlined text-xl">favorite</span>
                    </button>
                </div>
                <?php if (!empty($product['image_url'])): ?>
                    <img
                        src="<?php echo htmlspecialchars($product['image_url']); ?>"
                        alt="<?php echo htmlspecialchars($product['name']); ?>"
                        class="max-h-full object-contain transition-transform duration-500 group-hover:scale-110"
                    >
                <?php else: ?>
                    <div class="text-slate-300">
                        <span class="material-symbols-outlined" style="font-size: 80px;">image</span>
                    </div>
                <?php endif; ?>
                <div class="cart-button absolute inset-x-0 bottom-0 translate-y-4 p-4 opacity-0 transition-all duration-300">
                    <button onclick="event.preventDefault(); addToCart(<?php echo (int) $product['id']; ?>);" class="flex w-full items-center justify-center gap-2 rounded-lg bg-primary py-2.5 font-bold text-white shadow-lg shadow-primary/20">
                        <span class="material-symbols-outlined text-lg">add_shopping_cart</span> <?php echo t('add_to_cart'); ?>
                    </button>
                </div>
            </div>
        </a>
        <div class="p-5">
            <span class="mb-1 block text-[10px] font-bold uppercase tracking-widest text-primary"><?php echo htmlspecialchars($product['category_name']); ?></span>
            <h2 class="mb-2 text-sm font-semibold text-slate-800 dark:text-slate-100">
                <a href="<?php echo url('product_detail.php?id=' . $product['id']); ?>" class="line-clamp-2"><?php echo htmlspecialchars($product['name']); ?></a>
            </h2>
            <div class="mb-3 flex items-center gap-1">
                <span class="material-symbols-outlined text-sm text-yellow-400" style="font-variation-settings: 'FILL' 1">star</span>
                <span class="text-xs font-bold text-slate-600 dark:text-slate-400"><?php echo number_format((float) $product['rating'], 1); ?></span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-lg font-black text-slate-900 dark:text-white"><?php echo formatPriceVND($product['price']); ?></span>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</section>

<?php if ($totalProducts === 0): ?>
<section class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-16 text-center dark:border-slate-700 dark:bg-slate-900">
    <h2 class="text-xl font-bold text-slate-900 dark:text-white">
        <?php echo getCurrentLanguage() === 'vi' ? 'Chua co san pham nao' : 'No products available yet'; ?>
    </h2>
    <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">
        <?php echo getCurrentLanguage() === 'vi'
            ? 'Hay kiem tra lai du lieu MySQL sau khi migrate.'
            : 'Check the MySQL data after migration if the catalog should already be populated.'; ?>
    </p>
</section>
<?php endif; ?>

<?php if ($totalPages > 1): ?>
<nav class="mt-10 flex flex-wrap items-center justify-center gap-2" aria-label="Pagination">
    <?php if ($currentPage > 1): ?>
        <a href="<?php echo url('index.php?page=' . ($currentPage - 1)); ?>" class="rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition-colors hover:border-primary hover:text-primary dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200">
            <?php echo getCurrentLanguage() === 'vi' ? 'Truoc' : 'Previous'; ?>
        </a>
    <?php endif; ?>

    <?php if ($startPage > 1): ?>
        <a href="<?php echo url('index.php?page=1'); ?>" class="rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition-colors hover:border-primary hover:text-primary dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200">1</a>
        <?php if ($startPage > 2): ?>
            <span class="px-2 text-sm text-slate-400">...</span>
        <?php endif; ?>
    <?php endif; ?>

    <?php for ($page = $startPage; $page <= $endPage; $page++): ?>
        <a
            href="<?php echo url('index.php?page=' . $page); ?>"
            class="<?php echo $page === $currentPage
                ? 'rounded-lg bg-primary px-4 py-2 text-sm font-bold text-white shadow-lg shadow-primary/20'
                : 'rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition-colors hover:border-primary hover:text-primary dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200'; ?>"
            aria-current="<?php echo $page === $currentPage ? 'page' : 'false'; ?>"
        >
            <?php echo $page; ?>
        </a>
    <?php endfor; ?>

    <?php if ($endPage < $totalPages): ?>
        <?php if ($endPage < $totalPages - 1): ?>
            <span class="px-2 text-sm text-slate-400">...</span>
        <?php endif; ?>
        <a href="<?php echo url('index.php?page=' . $totalPages); ?>" class="rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition-colors hover:border-primary hover:text-primary dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200"><?php echo $totalPages; ?></a>
    <?php endif; ?>

    <?php if ($currentPage < $totalPages): ?>
        <a href="<?php echo url('index.php?page=' . ($currentPage + 1)); ?>" class="rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition-colors hover:border-primary hover:text-primary dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200">
            <?php echo getCurrentLanguage() === 'vi' ? 'Sau' : 'Next'; ?>
        </a>
    <?php endif; ?>
</nav>
<?php endif; ?>

<?php include APP_PATH . '/Views/layouts/footer.php'; ?>
