<?php
require_once __DIR__ . '/../app/bootstrap.php';

$page_title = t('cart');
$page_description = 'Giỏ hàng lưu trong session với danh sách sản phẩm đã chọn.';

$cart = $_SESSION['cart'] ?? [];
$cartItems = [];
$cartTotal = 0;

if ($cart) {
    $conn = getDBConnection();
    $productIds = array_values(array_map('intval', array_keys($cart)));
    $placeholders = implode(',', array_fill(0, count($productIds), '?'));
    $stmt = $conn->prepare(
        "SELECT p.*, c.name AS category_name
         FROM products p
         JOIN categories c ON p.category_id = c.id
         WHERE p.id IN ($placeholders)
         ORDER BY p.name ASC"
    );
    $stmt->execute($productIds);
    $products = $stmt->fetchAll();

    foreach ($products as $product) {
        $quantity = max(1, (int) ($cart[$product['id']] ?? 1));
        $lineTotal = (float) $product['price'] * $quantity;
        $cartTotal += $lineTotal;
        $cartItems[] = [
            'product' => $product,
            'quantity' => $quantity,
            'line_total' => $lineTotal,
        ];
    }
}

$_SESSION['cart_count'] = array_sum(array_map('intval', $_SESSION['cart'] ?? []));

include APP_PATH . '/Views/layouts/header.php';
?>

<section class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900">
    <h1 class="text-3xl font-black text-slate-900 dark:text-white"><?php echo t('cart'); ?></h1>
    <p class="mt-3 text-sm text-slate-600 dark:text-slate-400">
        Giỏ hàng hiện tại được lưu trong session để phục vụ demo đồ án.
    </p>
</section>

<?php if ($cartItems): ?>
    <section class="mt-8 grid grid-cols-1 gap-8 lg:grid-cols-[1.15fr_0.85fr]">
        <div class="space-y-4">
            <?php foreach ($cartItems as $item): $product = $item['product']; ?>
                <article class="flex gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <div class="flex h-24 w-24 shrink-0 items-center justify-center rounded-xl bg-slate-50 p-3 dark:bg-slate-800">
                        <?php if (!empty($product['image_url'])): ?>
                            <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="max-h-full object-contain">
                        <?php else: ?>
                            <span class="material-symbols-outlined text-slate-300">image</span>
                        <?php endif; ?>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-[10px] font-bold uppercase tracking-widest text-primary"><?php echo htmlspecialchars($product['category_name']); ?></p>
                        <h2 class="mt-1 text-sm font-semibold text-slate-900 dark:text-white"><?php echo htmlspecialchars($product['name']); ?></h2>
                        <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Số lượng: <?php echo $item['quantity']; ?></p>
                        <p class="mt-2 text-lg font-black text-slate-900 dark:text-white"><?php echo formatPriceVND($item['line_total']); ?></p>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <aside class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <h2 class="text-xl font-bold text-slate-900 dark:text-white"><?php echo t('checkout'); ?></h2>
            <div class="mt-5 flex items-center justify-between text-sm text-slate-600 dark:text-slate-400">
                <span>Tổng cộng</span>
                <span class="text-xl font-black text-slate-900 dark:text-white"><?php echo formatPriceVND($cartTotal); ?></span>
            </div>
        </aside>
    </section>
<?php else: ?>
    <section class="mt-8 rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-16 text-center dark:border-slate-700 dark:bg-slate-900">
        <h2 class="text-xl font-bold text-slate-900 dark:text-white">Giỏ hàng đang trống</h2>
    </section>
<?php endif; ?>

<?php include APP_PATH . '/Views/layouts/footer.php'; ?>
