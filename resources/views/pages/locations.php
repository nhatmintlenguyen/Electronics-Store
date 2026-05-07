
<section class="mb-8 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
    <p class="mb-2 text-xs font-bold uppercase tracking-[0.2em] text-primary">TechStore</p>
    <h1 class="text-3xl font-black text-slate-900 dark:text-white">
        Hệ thống cửa hàng
    </h1>
    <p class="mt-3 max-w-2xl text-sm text-slate-600 dark:text-slate-400">
        Kiểm tra địa chỉ, khu vực và mở nhanh Google Maps cho từng cửa hàng.
    </p>
</section>

<section class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
    <?php foreach ($locations as $location): ?>
        <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="mb-4 flex items-start gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-primary/10 text-primary">
                    <span class="material-symbols-outlined">storefront</span>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white"><?php echo htmlspecialchars($location['name']); ?></h2>
                    <?php if (!empty($location['district'])): ?>
                        <p class="text-xs font-bold uppercase tracking-wider text-primary"><?php echo htmlspecialchars($location['district']); ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="space-y-3 text-sm text-slate-600 dark:text-slate-400">
                <p class="flex items-start gap-2">
                    <span class="material-symbols-outlined text-lg text-slate-400">location_on</span>
                    <span><?php echo htmlspecialchars($location['address'] ?? ''); ?></span>
                </p>
                <p class="flex items-start gap-2">
                    <span class="material-symbols-outlined text-lg text-slate-400">schedule</span>
                    <span>Mở cửa 09:00 - 21:00 mỗi ngày</span>
                </p>
            </div>

            <?php if (!empty($location['google_maps_url'])): ?>
                <a
                    href="<?php echo htmlspecialchars($location['google_maps_url']); ?>"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="mt-5 inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-bold text-white transition-colors hover:bg-primary/90"
                >
                    <span class="material-symbols-outlined text-lg">map</span>
                    Mở Google Maps
                </a>
            <?php endif; ?>
        </article>
    <?php endforeach; ?>
</section>

