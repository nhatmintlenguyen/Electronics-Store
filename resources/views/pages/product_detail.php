
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
                <div class="inline-flex items-center gap-2 rounded-full px-2.5 py-1 text-xs font-bold uppercase tracking-wider <?php echo $availableLocationCount > 0 ? 'bg-primary/10 text-primary' : 'bg-slate-200 text-slate-600 dark:bg-slate-800 dark:text-slate-300'; ?>">
                    <span class="material-symbols-outlined text-xs"><?php echo $availableLocationCount > 0 ? 'verified' : 'storefront'; ?></span>
                    <?php echo $availableLocationCount > 0
                        ? t('in_stock')
                        : 'Out of stock at stores'; ?>
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


        </div>
    </div>
</div>

<section class="mt-10 grid grid-cols-1 gap-8 lg:grid-cols-12 lg:items-start">
    <div class="lg:col-span-7">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-900">
            <div class="mb-5 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.2em] text-primary">Specifications</p>
                    <h2 class="mt-2 text-xl font-bold text-slate-900 dark:text-white">Technical Specifications</h2>
                </div>
                <?php if (!empty($productSpecifications)): ?>
                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                        <?php echo count($productSpecifications); ?> items
                    </span>
                <?php endif; ?>
            </div>

            <?php if (!empty($productSpecifications)): ?>
                <div class="product-specification-table overflow-hidden rounded-xl border border-slate-200 dark:border-slate-800">
                    <table>
                        <tbody>
                            <?php foreach ($productSpecifications as $label => $value): ?>
                                <tr>
                                    <th scope="row"><?php echo htmlspecialchars($label); ?></th>
                                    <td><?php echo nl2br(htmlspecialchars($value)); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="rounded-xl border border-dashed border-slate-300 bg-white px-4 py-5 text-sm text-slate-600 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-400">
                    This product does not have specifications in the system yet.
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="lg:col-span-5">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-900 lg:sticky lg:top-24">
            <div class="mb-5 flex items-center justify-between gap-3">
                <h2 class="text-xl font-bold text-slate-900 dark:text-white">
                    Store Availability
                </h2>
                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                    <?php echo $availableLocationCount . ' ' . ($availableLocationCount === 1
                        ? 'location'
                        : 'locations'); ?>
                </span>
            </div>

            <?php if ($availableLocationCount > 0): ?>
                <div class="space-y-3">
                    <?php foreach ($productLocations as $location): ?>
                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-950">
                            <div class="flex items-start justify-between gap-4">
                                <div class="space-y-1">
                                    <p class="font-semibold text-slate-900 dark:text-white">
                                        <?php echo htmlspecialchars($location['name']); ?>
                                    </p>
                                    <?php if (!empty($location['district'])): ?>
                                        <p class="text-xs font-medium uppercase tracking-wide text-primary">
                                            <?php echo htmlspecialchars($location['district']); ?>
                                        </p>
                                    <?php endif; ?>
                                    <?php if (!empty($location['address'])): ?>
                                        <p class="text-sm text-slate-600 dark:text-slate-400">
                                            <?php echo htmlspecialchars($location['address']); ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                                <?php if (!empty($location['google_maps_url'])): ?>
                                    <a
                                        href="<?php echo htmlspecialchars($location['google_maps_url']); ?>"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="shrink-0 rounded-lg border border-primary/20 bg-primary/5 px-3 py-2 text-sm font-semibold text-primary transition-colors hover:bg-primary hover:text-white"
                                    >
                                        Open map
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="rounded-xl border border-dashed border-slate-300 bg-white px-4 py-5 text-sm text-slate-600 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-400">
                    This product has not been assigned to any store in the system yet.
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="mt-8">
    <details class="product-description-toggle rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-900" data-description-toggle>
        <summary>
            <span>
                <span class="block text-xs font-bold uppercase tracking-[0.2em] text-primary">Description</span>
                <span class="mt-2 block text-xl font-bold text-slate-900 dark:text-white"><?php echo t('description'); ?></span>
            </span>
                <span class="product-description-toggle-action">
                    <span class="product-description-toggle-label" data-description-toggle-label>View description</span>
                    <span class="product-description-toggle-icon" aria-hidden="true"></span>
                </span>
        </summary>
        <div class="product-description scraped-product-description mt-6">
            <?php echo $productDescription !== '' ? $productDescription : '<p>No description is available for this product.</p>'; ?>
        </div>
    </details>
</section>

<?php if (count($relatedProducts) > 0): ?>
<section class="mt-16 pt-12 border-t border-slate-200 dark:border-slate-800">
    <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-8">
        Related Products
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
