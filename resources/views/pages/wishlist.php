<?php include VIEW_PATH . '/layouts/header.php'; ?>

<section class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900">
    <h1 class="text-3xl font-black text-slate-900 dark:text-white"><?php echo t('wishlist'); ?></h1>
    <p class="mt-3 text-sm text-slate-600 dark:text-slate-400">
        Danh sách sản phẩm bạn đã lưu trong phiên hiện tại.
    </p>
</section>

<?php if ($wishlistProducts): ?>
    <section class="mt-8 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <?php foreach ($wishlistProducts as $product): ?>
            <div class="product-card rounded-xl border border-slate-100 bg-white dark:border-slate-700 dark:bg-slate-800">
                <a href="<?php echo url('product_detail.php?id=' . $product['id']); ?>" class="block">
                    <div class="flex aspect-square items-center justify-center bg-slate-50 p-8 dark:bg-slate-900">
                        <?php if (!empty($product['image_url'])): ?>
                            <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="max-h-full object-contain">
                        <?php else: ?>
                            <span class="material-symbols-outlined text-slate-300" style="font-size: 80px;">image</span>
                        <?php endif; ?>
                    </div>
                </a>
                <div class="p-5">
                    <span class="mb-1 block text-[10px] font-bold uppercase tracking-widest text-primary"><?php echo htmlspecialchars($product['category_name']); ?></span>
                    <h2 class="line-clamp-2 text-sm font-semibold text-slate-800 dark:text-slate-100">
                        <a href="<?php echo url('product_detail.php?id=' . $product['id']); ?>"><?php echo htmlspecialchars($product['name']); ?></a>
                    </h2>
                    <p class="mt-3 text-lg font-black text-slate-900 dark:text-white"><?php echo formatPriceVND($product['price']); ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </section>
<?php else: ?>
    <section class="mt-8 rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-16 text-center dark:border-slate-700 dark:bg-slate-900">
        <h2 class="text-xl font-bold text-slate-900 dark:text-white">Chưa có sản phẩm yêu thích</h2>
    </section>
<?php endif; ?>

<?php include VIEW_PATH . '/layouts/footer.php'; ?>
