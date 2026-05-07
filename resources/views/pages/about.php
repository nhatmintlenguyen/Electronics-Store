<?php include VIEW_PATH . '/layouts/header.php'; ?>

<section class="grid grid-cols-1 gap-8 lg:grid-cols-[1.15fr_0.85fr]">
    <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <p class="mb-2 text-xs font-bold uppercase tracking-[0.2em] text-primary">TechStore</p>
        <h1 class="text-3xl font-black text-slate-900 dark:text-white"><?php echo t('about_us'); ?></h1>
        <p class="mt-4 text-sm leading-7 text-slate-600 dark:text-slate-400">
            TechStore là website thương mại điện tử được phát triển cho đồ án Web Programming, tập trung vào tìm kiếm AJAX, danh mục sản phẩm, xác thực người dùng và hiển thị cửa hàng vật lý.
        </p>
        <div class="mt-6 grid gap-4 sm:grid-cols-2">
            <div class="rounded-2xl bg-slate-50 p-5 dark:bg-slate-800">
                <h2 class="font-bold text-slate-900 dark:text-white">Frontend</h2>
                <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">HTML5, CSS utility-first, JavaScript thuần, tìm kiếm AJAX và lọc danh mục.</p>
            </div>
            <div class="rounded-2xl bg-slate-50 p-5 dark:bg-slate-800">
                <h2 class="font-bold text-slate-900 dark:text-white">Backend</h2>
                <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">PHP, PDO, MySQL với sản phẩm, danh mục, người dùng và vị trí cửa hàng.</p>
            </div>
        </div>
    </div>

    <aside class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <h2 class="text-xl font-bold text-slate-900 dark:text-white">Liên hệ nhanh</h2>
        <div class="mt-5 space-y-4 text-sm text-slate-600 dark:text-slate-400">
            <p class="flex items-center gap-3"><span class="material-symbols-outlined text-primary">mail</span>info@techstore.com</p>
            <p class="flex items-center gap-3"><span class="material-symbols-outlined text-primary">call</span>+84 123 456 789</p>
            <p class="flex items-center gap-3"><span class="material-symbols-outlined text-primary">location_on</span>Hà Nội, Việt Nam</p>
        </div>
        <a href="<?php echo url('locations.php'); ?>" class="mt-6 inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2.5 font-bold text-white transition-colors hover:bg-primary/90">
            <span class="material-symbols-outlined text-lg">storefront</span>
            Xem cửa hàng
        </a>
    </aside>
</section>

<?php include VIEW_PATH . '/layouts/footer.php'; ?>
