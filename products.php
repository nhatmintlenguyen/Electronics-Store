<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$page_title = t('products');
include 'includes/header.php';

$conn = getDBConnection();

// Get filter parameters
$category_filter = isset($_GET['category']) ? (int)$_GET['category'] : null;
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'name_asc';

// Build query
$query = "SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE 1=1";
$params = [];

if ($category_filter) {
    $query .= " AND p.category_id = :category";
    $params[':category'] = $category_filter;
}

if ($search) {
    $query .= " AND p.name LIKE :search";
    $params[':search'] = '%' . $search . '%';
}

// Add sorting
switch ($sort) {
    case 'price_asc':
        $query .= " ORDER BY p.price ASC";
        break;
    case 'price_desc':
        $query .= " ORDER BY p.price DESC";
        break;
    case 'rating':
        $query .= " ORDER BY p.rating DESC";
        break;
    default:
        $query .= " ORDER BY p.name ASC";
}

$stmt = $conn->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get all categories for filter
$stmt = $conn->prepare("SELECT * FROM categories ORDER BY name");
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get brands (if available)
$stmt = $conn->prepare("SELECT DISTINCT brand FROM products WHERE brand IS NOT NULL AND brand != '' ORDER BY brand");
$stmt->execute();
$brands = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Breadcrumbs -->
<nav class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400 mb-6">
    <a class="hover:text-primary transition-colors" href="index.php"><?php echo t('home'); ?></a>
    <span class="material-symbols-outlined text-sm leading-none">chevron_right</span>
    <span class="text-slate-900 dark:text-slate-100 font-medium"><?php echo t('products'); ?></span>
</nav>

<div class="flex flex-col lg:flex-row gap-8">
    <!-- Sidebar Filters -->
    <aside class="w-full lg:w-64 shrink-0 space-y-8">
        <!-- Categories -->
        <section>
            <h3 class="text-sm font-bold uppercase tracking-wider text-slate-400 mb-4"><?php echo t('categories'); ?></h3>
            <div class="space-y-1">
                <a class="flex items-center gap-3 px-3 py-2 rounded-lg <?php echo !$category_filter ? 'bg-primary/10 text-primary font-medium' : 'hover:bg-slate-100 dark:hover:bg-slate-800'; ?> transition-colors" 
                   href="products.php<?php echo $search ? '?search=' . urlencode($search) : ''; ?>">
                    <span class="material-symbols-outlined text-lg">devices</span>
                    <span class="text-sm"><?php echo t('all_categories'); ?></span>
                </a>
                <?php 
                $cat_icons = ['laptop_mac', 'smartphone', 'headphones', 'watch', 'sports_esports', 'mouse', 'tablet', 'keyboard'];
                foreach ($categories as $index => $cat): 
                ?>
                <a class="flex items-center gap-3 px-3 py-2 rounded-lg <?php echo $category_filter == $cat['id'] ? 'bg-primary/10 text-primary font-medium' : 'hover:bg-slate-100 dark:hover:bg-slate-800'; ?> transition-colors" 
                   href="products.php?category=<?php echo $cat['id']; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>">
                    <span class="material-symbols-outlined text-lg <?php echo $category_filter == $cat['id'] ? '' : 'text-slate-400'; ?>"><?php echo $cat_icons[$index] ?? 'category'; ?></span>
                    <span class="text-sm"><?php echo htmlspecialchars($cat['name']); ?></span>
                </a>
                <?php endforeach; ?>
            </div>
        </section>
        
        <?php if (count($brands) > 0): ?>
        <!-- Brand Filter -->
        <section>
            <h3 class="text-sm font-bold uppercase tracking-wider text-slate-400 mb-4"><?php echo t('brand'); ?></h3>
            <div class="space-y-3 px-1">
                <?php foreach ($brands as $brand): ?>
                <label class="flex items-center gap-3 cursor-pointer group">
                    <input class="w-4 h-4 rounded border-slate-300 text-primary focus:ring-primary transition-all" type="checkbox"/>
                    <span class="text-sm group-hover:text-primary transition-colors"><?php echo htmlspecialchars($brand['brand']); ?></span>
                </label>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>
    </aside>
    
    <!-- Main Content -->
    <div class="flex-1">
        <!-- Toolbar -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6 gap-4">
            <div class="text-sm text-slate-600 dark:text-slate-400">
                <?php echo count($products); ?> <?php echo getCurrentLanguage() == 'vi' ? 'sản phẩm' : 'products'; ?>
                <?php if ($search): ?>
                    <?php echo getCurrentLanguage() == 'vi' ? 'cho' : 'for'; ?> "<span class="font-semibold text-slate-900 dark:text-white"><?php echo htmlspecialchars($search); ?></span>"
                <?php endif; ?>
            </div>
            
            <form method="GET" class="flex items-center gap-2">
                <?php if ($category_filter): ?>
                    <input type="hidden" name="category" value="<?php echo $category_filter; ?>">
                <?php endif; ?>
                <?php if ($search): ?>
                    <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                <?php endif; ?>
                <select name="sort" class="text-sm border border-slate-200 dark:border-slate-700 rounded-lg px-3 py-2 bg-white dark:bg-slate-800 focus:ring-2 focus:ring-primary focus:border-primary" onchange="this.form.submit()">
                    <option value="name_asc" <?php echo $sort == 'name_asc' ? 'selected' : ''; ?>><?php echo getCurrentLanguage() == 'vi' ? 'Tên A-Z' : 'Name A-Z'; ?></option>
                    <option value="price_asc" <?php echo $sort == 'price_asc' ? 'selected' : ''; ?>><?php echo getCurrentLanguage() == 'vi' ? 'Giá thấp đến cao' : 'Price: Low to High'; ?></option>
                    <option value="price_desc" <?php echo $sort == 'price_desc' ? 'selected' : ''; ?>><?php echo getCurrentLanguage() == 'vi' ? 'Giá cao đến thấp' : 'Price: High to Low'; ?></option>
                    <option value="rating" <?php echo $sort == 'rating' ? 'selected' : ''; ?>><?php echo getCurrentLanguage() == 'vi' ? 'Đánh giá cao nhất' : 'Highest Rated'; ?></option>
                </select>
            </form>
        </div>
        
        <!-- Products Grid -->
        <?php if (count($products) > 0): ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($products as $product): ?>
                <!-- Product Card -->
                <div class="product-card group relative bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 overflow-hidden hover:shadow-xl transition-all duration-300">
                    <a href="product_detail.php?id=<?php echo $product['id']; ?>" class="block">
                        <div class="aspect-square bg-slate-50 dark:bg-slate-900 p-8 flex items-center justify-center relative overflow-hidden">
                            <div class="absolute top-2 right-2 flex flex-col gap-2 z-10">
                                <button onclick="event.preventDefault();" class="p-1.5 bg-white/80 backdrop-blur-sm rounded-full text-slate-600 hover:text-red-500 transition-colors shadow-sm">
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
                                <button onclick="event.preventDefault();" class="w-full bg-primary text-white py-2.5 rounded-lg font-bold shadow-lg shadow-primary/20 flex items-center justify-center gap-2">
                                    <span class="material-symbols-outlined text-lg">add_shopping_cart</span> <?php echo t('add_to_cart'); ?>
                                </button>
                            </div>
                        </div>
                    </a>
                    <div class="p-5">
                        <span class="text-[10px] font-bold text-primary tracking-widest uppercase mb-1 block"><?php echo htmlspecialchars($product['category_name']); ?></span>
                        <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100 mb-2 line-clamp-2">
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
            </div>
        <?php else: ?>
            <div class="text-center py-16">
                <div class="w-24 h-24 mx-auto mb-4 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center">
                    <span class="material-symbols-outlined text-5xl text-slate-400">search_off</span>
                </div>
                <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">
                    <?php echo getCurrentLanguage() == 'vi' ? 'Không tìm thấy sản phẩm' : 'No products found'; ?>
                </h3>
                <p class="text-slate-600 dark:text-slate-400 mb-6">
                    <?php echo getCurrentLanguage() == 'vi' ? 'Hãy thử điều chỉnh bộ lọc hoặc tìm kiếm khác' : 'Try adjusting your filters or search term'; ?>
                </p>
                <a href="products.php" class="inline-flex items-center gap-2 bg-primary text-white px-6 py-3 rounded-lg font-bold hover:bg-primary/90 transition-colors">
                    <span class="material-symbols-outlined">refresh</span>
                    <?php echo getCurrentLanguage() == 'vi' ? 'Xóa bộ lọc' : 'Clear Filters'; ?>
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
