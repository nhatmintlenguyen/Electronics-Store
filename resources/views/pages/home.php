
<section class="home-hero-panel overflow-hidden rounded-[2rem] border border-slate-200 bg-white p-3 shadow-sm dark:border-slate-800 dark:bg-slate-900">
    <div class="grid gap-3 lg:grid-cols-[1.35fr_0.65fr]">
        <div class="home-hero-card relative overflow-hidden rounded-[1.6rem] px-7 py-8 sm:px-10 sm:py-12">
            <div class="absolute inset-0">
                <div class="home-hero-image absolute inset-0"></div>
                <div class="absolute inset-0 bg-gradient-to-r from-slate-950/85 via-slate-900/58 to-slate-900/20"></div>
            </div>
            <div class="relative z-10 max-w-md">
                <span class="mb-4 inline-flex rounded-full bg-primary px-3 py-1 text-[11px] font-bold uppercase tracking-[0.2em] text-white">Hàng mới về</span>
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
                    <a href="<?php echo url('about.php'); ?>" class="inline-flex items-center rounded-xl bg-white/10 px-5 py-3 text-sm font-bold text-white backdrop-blur transition-colors hover:bg-white/20">
                        <?php echo t('learn_more'); ?>
                    </a>
                </div>
            </div>
        </div>

        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-1">
            <div class="rounded-[1.6rem] bg-slate-50 p-6 dark:bg-slate-800/80">
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-primary">TechStore</p>
                <p class="mt-4 text-sm leading-7 text-slate-600 dark:text-slate-300">
                    Trang chủ hiển thị toàn bộ sản phẩm theo dạng lưới ngắn gọn để người dùng duyệt nhanh và dễ theo dõi hơn.
                </p>
                <div class="mt-6 inline-flex rounded-full bg-white px-4 py-2 text-sm text-slate-600 shadow-sm dark:bg-slate-900 dark:text-slate-300" data-total-products-badge>
                    Tổng sản phẩm:
                    <span class="ml-2 font-bold text-slate-900 dark:text-white" data-total-products-value><?php echo number_format($totalProducts); ?></span>
                </div>
            </div>

            <div class="rounded-[1.6rem] bg-gradient-to-br from-primary to-sky-500 p-6 text-white shadow-lg shadow-primary/20">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-[0.18em] text-white/70"><?php echo t('free_shipping'); ?></span>
                    <span class="material-symbols-outlined">local_shipping</span>
                </div>
                <p class="mt-6 text-2xl font-black leading-tight">
                    Miễn phí giao hàng cho đơn từ 1.000.000₫
                </p>
                <p class="mt-3 text-sm text-white/80">
                    Mua sắm nhanh, theo dõi sản phẩm và kiểm tra cửa hàng còn hàng ngay trên website.
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
            <p class="text-xs font-bold uppercase tracking-[0.18em] text-primary">Nổi bật</p>
            <h2 class="mt-2 text-2xl font-black text-slate-900 dark:text-white">Sản phẩm nổi bật</h2>
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
    <?php foreach ($promoProducts as $index => $product): ?>
        <article class="home-promo-card overflow-hidden rounded-[2rem] border border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900">
            <div class="grid min-h-[220px] gap-6 p-6 sm:grid-cols-[1.2fr_0.8fr] sm:items-end">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.18em] text-primary"><?php echo $index === 0 ? 'Nâng cấp' : 'Thiết yếu'; ?></p>
                    <h2 class="mt-3 text-2xl font-black text-slate-900 dark:text-white">
                        <?php echo $index === 0
                            ? 'Nâng cấp góc làm việc tại nhà'
                            : 'Góc gaming tối giản'; ?>
                    </h2>
                    <p class="mt-3 text-sm leading-6 text-slate-600 dark:text-slate-300">
                        <?php echo $index === 0
                            ? 'Không gian học tập và làm việc hiệu quả với các thiết bị màn hình, laptop và phụ kiện.'
                            : 'Chọn các thiết bị âm thanh, gaming và phụ kiện đang được quan tâm nhất.'; ?>
                    </p>
                    <a href="<?php echo url('product_detail.php?id=' . $product['id']); ?>" class="mt-5 inline-flex items-center gap-2 text-sm font-bold text-primary">
                        Xem bộ sưu tập
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

<section class="mt-8">
    <div class="rounded-[2rem] border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div class="mb-5 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.18em] text-primary">Danh mục</p>
                <h2 class="mt-2 text-2xl font-black text-slate-900 dark:text-white">Tất cả sản phẩm</h2>
            </div>
            <div
                class="category-pill-row flex flex-nowrap gap-3 overflow-x-auto pb-2 no-scrollbar lg:flex-wrap"
                data-category-filters
                data-category-endpoint="<?php echo url('filter_products.php'); ?>"
            >
                <button type="button" class="category-pill is-active" data-category-button data-category-id="all" aria-pressed="true">
                    Tất cả
                </button>
                <?php foreach ($categories as $category): ?>
                    <button type="button" class="category-pill" data-category-button data-category-id="<?php echo (int) $category['id']; ?>" aria-pressed="false">
                        <?php echo htmlspecialchars($category['name']); ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4" data-product-grid>
            <?php foreach ($products as $product): ?>
                <div class="product-card group relative overflow-hidden rounded-[1.5rem] border border-slate-100 bg-white transition-all duration-300 hover:shadow-xl dark:border-slate-700 dark:bg-slate-800">
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
                                <span class="material-symbols-outlined text-slate-300" style="font-size: 80px;">image</span>
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
        </div>
    </div>
</section>

<section class="mt-8 rounded-[2rem] border border-slate-200 bg-gradient-to-r from-slate-100 to-slate-50 p-6 shadow-sm dark:border-slate-800 dark:from-slate-900 dark:to-slate-800">
    <div class="grid gap-6 lg:grid-cols-[1fr_0.9fr] lg:items-center">
        <div>
            <h2 class="text-2xl font-black text-slate-900 dark:text-white">Đăng ký nhận ưu đãi công nghệ</h2>
            <p class="mt-3 text-sm leading-6 text-slate-600 dark:text-slate-300">
                Nhận cập nhật về sản phẩm mới, ưu đãi độc quyền và các bộ sưu tập đang được quan tâm.
            </p>
        </div>
        <form class="grid gap-3 sm:grid-cols-[1fr_auto]">
            <input type="email" placeholder="Nhập email của bạn" class="rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm focus:border-primary focus:ring-2 focus:ring-primary dark:border-slate-700 dark:bg-slate-900">
            <button type="button" class="rounded-xl bg-primary px-5 py-3 text-sm font-bold text-white transition-colors hover:bg-primary/90">
                Đăng ký
            </button>
        </form>
    </div>
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
                <h3 class="font-bold text-slate-900 dark:text-white">Hỗ trợ 24/7</h3>
                <p class="text-sm text-slate-600 dark:text-slate-400">Tư vấn nhanh và hỗ trợ sau bán hàng.</p>
            </div>
        </div>
    </article>
    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div class="flex items-center gap-3">
            <span class="flex h-11 w-11 items-center justify-center rounded-full bg-primary/10 text-primary"><span class="material-symbols-outlined">verified_user</span></span>
            <div>
                <h3 class="font-bold text-slate-900 dark:text-white">Thanh toán an toàn</h3>
                <p class="text-sm text-slate-600 dark:text-slate-400">Bảo mật thanh toán và thông tin tài khoản.</p>
            </div>
        </div>
    </article>
</section>

