
<section class="home-hero-panel overflow-hidden rounded-[2rem] border border-slate-200 bg-white p-3 shadow-sm dark:border-slate-800 dark:bg-slate-900">
    <div class="grid gap-3 lg:grid-cols-[1.35fr_0.65fr]">
        <div class="home-hero-card relative overflow-hidden rounded-[1.6rem] px-7 py-8 sm:px-10 sm:py-12">
            <div class="absolute inset-0">
                <div class="home-hero-image absolute inset-0"></div>
                <div class="absolute inset-0 bg-slate-950/65"></div>
            </div>
            <div class="relative z-10 max-w-md">
                <span class="mb-4 inline-flex rounded-full bg-primary px-3 py-1 text-[11px] font-bold uppercase tracking-[0.2em] text-white">New Arrival</span>
                <h1 class="text-4xl font-black leading-[1.05] text-white sm:text-5xl">
                    <?php echo t('welcome_title'); ?>
                </h1>
                <p class="mt-4 text-sm leading-6 text-slate-200 sm:text-base">
                    <?php echo t('welcome_subtitle'); ?>
                </p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="<?php echo url('products.php'); ?>" class="inline-flex items-center rounded-xl bg-primary px-5 py-3 text-sm font-bold text-white shadow-lg shadow-primary/20 transition-transform hover:-translate-y-0.5 hover:bg-primary/90">
                        <?php echo t('shop_now'); ?>
                    </a>
                    <a href="<?php echo url('about.php'); ?>" class="inline-flex items-center rounded-xl bg-white px-5 py-3 text-sm font-bold text-slate-900 transition-colors hover:bg-slate-100">
                        <?php echo t('learn_more'); ?>
                    </a>
                </div>
            </div>
        </div>

        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-1">
            <div class="rounded-[1.6rem] bg-slate-50 p-6 dark:bg-slate-800/80">
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-primary">TechStore</p>
                <p class="mt-4 text-sm leading-7 text-slate-600 dark:text-slate-300">
                    The homepage shows all products in a compact grid so customers can browse quickly and easily.
                </p>
                <div class="mt-6 inline-flex rounded-full bg-white px-4 py-2 text-sm text-slate-600 shadow-sm dark:bg-slate-900 dark:text-slate-300" data-total-products-badge>
                    Total products:
                    <span class="ml-2 font-bold text-slate-900 dark:text-white" data-total-products-value><?php echo number_format($totalProducts); ?></span>
                </div>
            </div>

            <div class="rounded-[1.6rem] border border-slate-200 bg-white p-6 text-slate-900 shadow-sm dark:border-slate-800 dark:bg-slate-900 dark:text-white">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-[0.18em] text-primary"><?php echo t('free_shipping'); ?></span>
                    <span class="material-symbols-outlined text-primary">local_shipping</span>
                </div>
                <p class="mt-6 text-2xl font-black leading-tight">
                    Free shipping for orders over 1,000,000₫
                </p>
                <p class="mt-3 text-sm text-slate-600 dark:text-slate-300">
                    Shop quickly, track products, and check in-store availability directly on the website.
                </p>
            </div>
        </div>
    </div>
</section>

<section class="mt-4">
    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-6">
        <?php foreach ($homeCategories as $index => $category): ?>
            <button
                type="button"
                class="home-category-tile rounded-[1.4rem] border border-slate-200 bg-white px-4 py-5 text-center shadow-sm transition-all hover:-translate-y-1 hover:shadow-md dark:border-slate-800 dark:bg-slate-900 category-pill"
                data-category-button
                data-category-id="<?php echo (int) $category['id']; ?>"
                aria-pressed="false"
            >
                <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-primary/10 text-primary">
                    <span class="material-symbols-outlined"><?php echo $categoryIcons[$index] ?? 'category'; ?></span>
                </span>
                <span class="mt-3 block text-sm font-semibold text-slate-700 dark:text-slate-200"><?php echo htmlspecialchars($category['name']); ?></span>
            </button>
        <?php endforeach; ?>
    </div>
</section>

<section class="mt-8 rounded-[2rem] border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
    <div class="mb-5 flex items-center justify-between">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.18em] text-primary">Featured</p>
            <h2 class="mt-2 text-2xl font-black text-slate-900 dark:text-white">Featured Products</h2>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4">
        <?php foreach ($featuredProducts as $product): ?>
            <div class="product-card group relative overflow-hidden rounded-[1.5rem] border border-slate-100 bg-white transition-all duration-300 hover:shadow-xl dark:border-slate-700 dark:bg-slate-800">
                <a href="<?php echo url('product_detail.php?id=' . $product['id']); ?>" class="block">
                    <div class="relative flex aspect-square items-center justify-center overflow-hidden bg-slate-50 p-8 dark:bg-slate-900">
                        <?php if (!empty($product['image_url'])): ?>
                            <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="max-h-full object-contain transition-transform duration-500 group-hover:scale-110">
                        <?php else: ?>
                            <span class="material-symbols-outlined text-slate-300" style="font-size: 80px;">image</span>
                        <?php endif; ?>
                    </div>
                </a>
                <div class="p-5">
                    <span class="mb-1 block text-[10px] font-bold uppercase tracking-widest text-primary"><?php echo htmlspecialchars($product['category_name']); ?></span>
                    <h3 class="line-clamp-2 text-sm font-semibold text-slate-800 dark:text-slate-100">
                        <a href="<?php echo url('product_detail.php?id=' . $product['id']); ?>"><?php echo htmlspecialchars($product['name']); ?></a>
                    </h3>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm text-yellow-400" style="font-variation-settings: 'FILL' 1">star</span>
                        <span class="text-xs font-bold text-slate-600 dark:text-slate-400"><?php echo number_format((float) $product['rating'], 1); ?></span>
                    </div>
                    <p class="mt-3 text-lg font-black text-slate-900 dark:text-white"><?php echo formatPriceVND($product['price']); ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<section class="mt-8 grid gap-5 lg:grid-cols-2">
    <?php foreach ($promoCards as $promoCard): ?>
        <?php $product = $promoCard['product']; ?>
        <?php if (!$product) { continue; } ?>
        <article class="home-promo-card overflow-hidden rounded-[2rem] border border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900">
            <div class="grid min-h-[220px] gap-6 p-6 sm:grid-cols-[1.2fr_0.8fr] sm:items-end">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.18em] text-primary"><?php echo htmlspecialchars($promoCard['eyebrow']); ?></p>
                    <h2 class="mt-3 text-2xl font-black text-slate-900 dark:text-white">
                        <?php echo htmlspecialchars($promoCard['title']); ?>
                    </h2>
                    <p class="mt-3 text-sm leading-6 text-slate-600 dark:text-slate-300">
                        <?php echo htmlspecialchars($promoCard['description']); ?>
                    </p>
                    <a href="<?php echo url('product_detail.php?id=' . $product['id']); ?>" class="mt-5 inline-flex items-center gap-2 text-sm font-bold text-primary">
                        View collection
                        <span class="material-symbols-outlined text-base">arrow_forward</span>
                    </a>
                </div>
                <div class="flex items-end justify-center">
                    <?php if (!empty($product['image_url'])): ?>
                        <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="max-h-52 object-contain">
                    <?php endif; ?>
                </div>
            </div>
        </article>
    <?php endforeach; ?>
</section>


<section class="mt-8 grid grid-cols-1 gap-4 md:grid-cols-3">
    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div class="flex items-center gap-3">
            <span class="flex h-11 w-11 items-center justify-center rounded-full bg-primary/10 text-primary"><span class="material-symbols-outlined">local_shipping</span></span>
            <div>
                <h3 class="font-bold text-slate-900 dark:text-white"><?php echo t('free_shipping'); ?></h3>
                <p class="text-sm text-slate-600 dark:text-slate-400"><?php echo t('free_shipping_desc'); ?></p>
            </div>
        </div>
    </article>
    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div class="flex items-center gap-3">
            <span class="flex h-11 w-11 items-center justify-center rounded-full bg-primary/10 text-primary"><span class="material-symbols-outlined">support_agent</span></span>
            <div>
                <h3 class="font-bold text-slate-900 dark:text-white">24/7 Support</h3>
                <p class="text-sm text-slate-600 dark:text-slate-400">Fast consultation and after-sales support.</p>
            </div>
        </div>
    </article>
    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div class="flex items-center gap-3">
            <span class="flex h-11 w-11 items-center justify-center rounded-full bg-primary/10 text-primary"><span class="material-symbols-outlined">verified_user</span></span>
            <div>
                <h3 class="font-bold text-slate-900 dark:text-white">Secure Payment</h3>
                <p class="text-sm text-slate-600 dark:text-slate-400">Protected payments and account information.</p>
            </div>
        </div>
    </article>
</section>
