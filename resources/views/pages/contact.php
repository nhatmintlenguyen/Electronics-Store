
<section class="grid grid-cols-1 gap-8 lg:grid-cols-[1.1fr_0.9fr]">
    <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <p class="mb-2 text-xs font-bold uppercase tracking-[0.2em] text-primary">TechStore</p>
        <h1 class="text-3xl font-black text-slate-900 dark:text-white"><?php echo t('contact'); ?></h1>
        <p class="mt-4 text-sm leading-7 text-slate-600 dark:text-slate-400">
            TechStore is an Electronics Store website built for the Web Programming course. The system supports AJAX search, category filtering, email login, and store locations on Google Maps.
        </p>

        <div class="mt-8 grid gap-4 sm:grid-cols-2">
            <div class="rounded-2xl bg-slate-50 p-5 dark:bg-slate-800">
                <h2 class="font-bold text-slate-900 dark:text-white">Website Goal</h2>
                <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">
                    Present a dynamic, responsive e-commerce website built with PHP and MySQL.
                </p>
            </div>
            <div class="rounded-2xl bg-slate-50 p-5 dark:bg-slate-800">
                <h2 class="font-bold text-slate-900 dark:text-white">Core Features</h2>
                <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">
                    AJAX search, product categories, user authentication, cart, wishlist, and store availability.
                </p>
            </div>
        </div>
    </div>

    <aside class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <h2 class="text-xl font-bold text-slate-900 dark:text-white">Contact Information</h2>
        <div class="mt-6 space-y-5 text-sm text-slate-600 dark:text-slate-400">
            <p class="flex items-center gap-3">
                <span class="material-symbols-outlined text-primary">mail</span>
                info@techstore.com
            </p>
            <p class="flex items-center gap-3">
                <span class="material-symbols-outlined text-primary">call</span>
                +84 123 456 789
            </p>
            <p class="flex items-center gap-3">
                <span class="material-symbols-outlined text-primary">location_on</span>
                Hanoi, Vietnam
            </p>
            <p class="flex items-center gap-3">
                <span class="material-symbols-outlined text-primary">schedule</span>
                Support from 08:00 to 21:00 daily
            </p>
        </div>

        <div class="mt-8 flex flex-wrap gap-3">
            <a href="<?php echo url('locations.php'); ?>" class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2.5 font-bold text-white transition-colors hover:bg-primary/90">
                <span class="material-symbols-outlined text-lg">storefront</span>
                View stores
            </a>
            <a href="<?php echo url('products.php'); ?>" class="inline-flex items-center gap-2 rounded-lg border border-slate-200 px-4 py-2.5 font-bold text-slate-700 transition-colors hover:border-primary hover:text-primary dark:border-slate-700 dark:text-slate-200">
                <span class="material-symbols-outlined text-lg">devices</span>
                <?php echo t('products'); ?>
            </a>
        </div>
    </aside>
</section>

