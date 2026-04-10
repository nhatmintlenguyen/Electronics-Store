<?php
require_once __DIR__ . '/../app/bootstrap.php';

$conn = getDBConnection();

$categoryFilter = isset($_GET['category']) ? (int) $_GET['category'] : null;
$search = isset($_GET['search']) ? sanitize((string) ($_GET['search'] ?? '')) : '';
$sort = isset($_GET['sort']) ? (string) $_GET['sort'] : 'name_asc';
$currentPage = max(1, (int) ($_GET['page'] ?? 1));
$perPage = 12;

$page_title = t('products');
$page_description = getCurrentLanguage() === 'vi'
    ? 'Danh muc san pham TechStore voi bo loc, sap xep va phan trang.'
    : 'Browse the TechStore catalog with filters, sorting, and pagination.';

$baseQuery =
    'FROM products p
     JOIN categories c ON p.category_id = c.id
     WHERE 1=1';
$params = [];

if ($categoryFilter) {
    $baseQuery .= ' AND p.category_id = :category';
    $params[':category'] = $categoryFilter;
}

if ($search !== '') {
    $baseQuery .= ' AND p.name LIKE :search';
    $params[':search'] = '%' . $search . '%';
}

$orderBy = match ($sort) {
    'price_asc' => ' ORDER BY p.price ASC, p.name ASC',
    'price_desc' => ' ORDER BY p.price DESC, p.name ASC',
    'rating' => ' ORDER BY p.rating DESC, p.name ASC',
    default => ' ORDER BY p.name ASC',
};

$countStmt = $conn->prepare('SELECT COUNT(*) ' . $baseQuery);
$countStmt->execute($params);
$totalProducts = (int) $countStmt->fetchColumn();
$totalPages = max(1, (int) ceil($totalProducts / $perPage));
$currentPage = min($currentPage, $totalPages);
$offset = ($currentPage - 1) * $perPage;

$productsStmt = $conn->prepare(
    'SELECT p.*, c.name AS category_name ' . $baseQuery . $orderBy . ' LIMIT :limit OFFSET :offset'
);
foreach ($params as $key => $value) {
    $productsStmt->bindValue($key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
}
$productsStmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$productsStmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$productsStmt->execute();
$products = $productsStmt->fetchAll();

$categoriesStmt = $conn->query('SELECT id, name FROM categories ORDER BY name ASC');
$categories = $categoriesStmt->fetchAll();

$startPage = max(1, $currentPage - 2);
$endPage = min($totalPages, $currentPage + 2);

function productsPageUrl(?int $categoryFilter, string $search, string $sort, int $page): string
{
    $params = [
        'sort' => $sort,
        'page' => $page,
    ];

    if ($categoryFilter) {
        $params['category'] = $categoryFilter;
    }

    if ($search !== '') {
        $params['search'] = $search;
    }

    return url('products.php?' . http_build_query($params));
}

include APP_PATH . '/Views/layouts/header.php';
?>

<nav class="mb-6 flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
    <a class="transition-colors hover:text-primary" href="<?php echo url('index.php'); ?>"><?php echo t('home'); ?></a>
    <span class="material-symbols-outlined text-sm leading-none">chevron_right</span>
    <span class="font-medium text-slate-900 dark:text-slate-100"><?php echo t('products'); ?></span>
</nav>

<div class="flex flex-col gap-8 lg:flex-row">
    <aside class="w-full shrink-0 space-y-6 lg:w-72">
        <section class="rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900">
            <h2 class="mb-4 text-sm font-bold uppercase tracking-wider text-slate-400"><?php echo t('categories'); ?></h2>
            <div class="space-y-2">
                <a
                    class="flex items-center gap-3 rounded-xl px-3 py-2 <?php echo !$categoryFilter ? 'bg-primary/10 font-semibold text-primary' : 'text-slate-700 transition-colors hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800'; ?>"
                    href="<?php echo productsPageUrl(null, $search, $sort, 1); ?>"
                >
                    <span class="material-symbols-outlined text-lg">devices</span>
                    <span class="text-sm"><?php echo t('all_categories'); ?></span>
                </a>
                <?php foreach ($categories as $category): ?>
                    <a
                        class="flex items-center gap-3 rounded-xl px-3 py-2 <?php echo $categoryFilter === (int) $category['id'] ? 'bg-primary/10 font-semibold text-primary' : 'text-slate-700 transition-colors hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800'; ?>"
                        href="<?php echo productsPageUrl((int) $category['id'], $search, $sort, 1); ?>"
                    >
                        <span class="material-symbols-outlined text-lg <?php echo $categoryFilter === (int) $category['id'] ? '' : 'text-slate-400'; ?>">category</span>
                        <span class="text-sm"><?php echo htmlspecialchars($category['name']); ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </section>
    </aside>

    <section class="flex-1">
        <div class="mb-6 flex flex-col gap-4 rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900 lg:flex-row lg:items-center lg:justify-between">
            <div class="text-sm text-slate-600 dark:text-slate-400">
                <span class="font-semibold text-slate-900 dark:text-white"><?php echo number_format($totalProducts); ?></span>
                <?php echo getCurrentLanguage() === 'vi' ? ' sản phẩm' : ' products'; ?>
                <?php if ($search !== ''): ?>
                    <?php echo getCurrentLanguage() === 'vi' ? ' cho ' : ' for '; ?>
                    "<span class="font-semibold text-slate-900 dark:text-white"><?php echo htmlspecialchars($search); ?></span>"
                <?php endif; ?>
            </div>

            <form method="GET" class="flex flex-wrap items-center gap-2">
                <?php if ($categoryFilter): ?>
                    <input type="hidden" name="category" value="<?php echo $categoryFilter; ?>">
                <?php endif; ?>
                <?php if ($search !== ''): ?>
                    <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                <?php endif; ?>
                <select name="sort" class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm focus:border-primary focus:ring-2 focus:ring-primary dark:border-slate-700 dark:bg-slate-800" onchange="this.form.submit()">
                    <option value="name_asc" <?php echo $sort === 'name_asc' ? 'selected' : ''; ?>><?php echo getCurrentLanguage() === 'vi' ? 'Tên A-Z' : 'Name A-Z'; ?></option>
                    <option value="price_asc" <?php echo $sort === 'price_asc' ? 'selected' : ''; ?>><?php echo getCurrentLanguage() === 'vi' ? 'Giá thấp đến cao' : 'Price: Low to High'; ?></option>
                    <option value="price_desc" <?php echo $sort === 'price_desc' ? 'selected' : ''; ?>><?php echo getCurrentLanguage() === 'vi' ? 'Giá cao đến thấp' : 'Price: High to Low'; ?></option>
                    <option value="rating" <?php echo $sort === 'rating' ? 'selected' : ''; ?>><?php echo getCurrentLanguage() === 'vi' ? 'Đánh giá cao nhất' : 'Highest Rated'; ?></option>
                </select>
            </form>
        </div>

        <?php if ($products): ?>
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-3">
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
                                    <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="max-h-full object-contain transition-transform duration-500 group-hover:scale-110">
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
                            <h3 class="mb-2 text-sm font-semibold text-slate-800 dark:text-slate-100 line-clamp-2">
                                <a href="<?php echo url('product_detail.php?id=' . $product['id']); ?>"><?php echo htmlspecialchars($product['name']); ?></a>
                            </h3>
                            <div class="mb-3 flex items-center gap-1">
                                <span class="material-symbols-outlined text-sm text-yellow-400" style="font-variation-settings: 'FILL' 1">star</span>
                                <span class="text-xs font-bold text-slate-600 dark:text-slate-400"><?php echo number_format((float) $product['rating'], 1); ?></span>
                            </div>
                            <span class="text-lg font-black text-slate-900 dark:text-white"><?php echo formatPriceVND($product['price']); ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if ($totalPages > 1): ?>
                <nav class="mt-10 flex flex-wrap items-center justify-center gap-2" aria-label="Pagination">
                    <?php if ($currentPage > 1): ?>
                        <a href="<?php echo productsPageUrl($categoryFilter, $search, $sort, $currentPage - 1); ?>" class="rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition-colors hover:border-primary hover:text-primary dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200">
                            <?php echo getCurrentLanguage() === 'vi' ? 'Truoc' : 'Previous'; ?>
                        </a>
                    <?php endif; ?>

                    <?php if ($startPage > 1): ?>
                        <a href="<?php echo productsPageUrl($categoryFilter, $search, $sort, 1); ?>" class="rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition-colors hover:border-primary hover:text-primary dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200">1</a>
                        <?php if ($startPage > 2): ?>
                            <span class="px-2 text-sm text-slate-400">...</span>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php for ($page = $startPage; $page <= $endPage; $page++): ?>
                        <a
                            href="<?php echo productsPageUrl($categoryFilter, $search, $sort, $page); ?>"
                            class="<?php echo $page === $currentPage ? 'rounded-lg bg-primary px-4 py-2 text-sm font-bold text-white shadow-lg shadow-primary/20' : 'rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition-colors hover:border-primary hover:text-primary dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200'; ?>"
                            aria-current="<?php echo $page === $currentPage ? 'page' : 'false'; ?>"
                        >
                            <?php echo $page; ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ($endPage < $totalPages): ?>
                        <?php if ($endPage < $totalPages - 1): ?>
                            <span class="px-2 text-sm text-slate-400">...</span>
                        <?php endif; ?>
                        <a href="<?php echo productsPageUrl($categoryFilter, $search, $sort, $totalPages); ?>" class="rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition-colors hover:border-primary hover:text-primary dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200"><?php echo $totalPages; ?></a>
                    <?php endif; ?>

                    <?php if ($currentPage < $totalPages): ?>
                        <a href="<?php echo productsPageUrl($categoryFilter, $search, $sort, $currentPage + 1); ?>" class="rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition-colors hover:border-primary hover:text-primary dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200">
                            <?php echo getCurrentLanguage() === 'vi' ? 'Sau' : 'Next'; ?>
                        </a>
                    <?php endif; ?>
                </nav>
            <?php endif; ?>
        <?php else: ?>
            <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-16 text-center dark:border-slate-700 dark:bg-slate-900">
                <h2 class="text-xl font-bold text-slate-900 dark:text-white">
                    <?php echo getCurrentLanguage() === 'vi' ? 'Khong tim thay san pham' : 'No products found'; ?>
                </h2>
                <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">
                    <?php echo getCurrentLanguage() === 'vi' ? 'Hay thu thay doi bo loc hoac tu khoa tim kiem.' : 'Try adjusting the current filter or search term.'; ?>
                </p>
                <a href="<?php echo url('products.php'); ?>" class="mt-6 inline-flex items-center gap-2 rounded-lg bg-primary px-6 py-3 font-bold text-white transition-colors hover:bg-primary/90">
                    <span class="material-symbols-outlined">refresh</span>
                    <?php echo getCurrentLanguage() === 'vi' ? 'Xoa bo loc' : 'Clear Filters'; ?>
                </a>
            </div>
        <?php endif; ?>
    </section>
</div>

<?php include APP_PATH . '/Views/layouts/footer.php'; ?>
