    </main>

    <footer class="bg-white dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="col-span-1">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="text-primary">
                            <span class="material-symbols-outlined text-3xl">bolt</span>
                        </div>
                        <span class="text-xl font-bold tracking-tight">TechStore</span>
                    </div>
                    <p class="text-sm text-slate-600 dark:text-slate-400">
                        Your trusted destination for quality electronics.
                    </p>
                </div>

                <div>
                    <h5 class="font-bold text-slate-900 dark:text-white mb-4">Quick Links</h5>
                    <ul class="space-y-2 text-sm">
                        <li><a href="<?php echo url('index.php'); ?>" class="text-slate-600 dark:text-slate-400 hover:text-primary transition-colors"><?php echo t('home'); ?></a></li>
                        <li><a href="<?php echo url('products.php'); ?>" class="text-slate-600 dark:text-slate-400 hover:text-primary transition-colors"><?php echo t('products'); ?></a></li>
                        <li><a href="<?php echo url('locations.php'); ?>" class="text-slate-600 dark:text-slate-400 hover:text-primary transition-colors">Stores</a></li>
                        <li><a href="<?php echo url('about.php'); ?>" class="text-slate-600 dark:text-slate-400 hover:text-primary transition-colors"><?php echo t('about_us'); ?></a></li>
                    </ul>
                </div>

                <div>
                    <h5 class="font-bold text-slate-900 dark:text-white mb-4"><?php echo t('customer_support'); ?></h5>
                    <ul class="space-y-2 text-sm">
                        <li><a href="<?php echo url('contact.php'); ?>" class="text-slate-600 dark:text-slate-400 hover:text-primary transition-colors"><?php echo t('contact'); ?></a></li>
                        <li><a href="<?php echo url('products.php'); ?>" class="text-slate-600 dark:text-slate-400 hover:text-primary transition-colors">Buying Guide</a></li>
                        <li><a href="<?php echo url('about.php'); ?>" class="text-slate-600 dark:text-slate-400 hover:text-primary transition-colors">Return Policy</a></li>
                        <li><a href="<?php echo url('locations.php'); ?>" class="text-slate-600 dark:text-slate-400 hover:text-primary transition-colors"><?php echo t('warranty'); ?></a></li>
                    </ul>
                </div>

                <div id="site-contact">
                    <h5 class="font-bold text-slate-900 dark:text-white mb-4"><?php echo t('contact'); ?></h5>
                    <div class="space-y-2 text-sm text-slate-600 dark:text-slate-400">
                        <p class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-lg">mail</span>
                            info@techstore.com
                        </p>
                        <p class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-lg">call</span>
                            +84 123 456 789
                        </p>
                        <p class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-lg">location_on</span>
                            Hanoi, Vietnam
                        </p>
                    </div>
                </div>
            </div>

            <div class="border-t border-slate-200 dark:border-slate-800 mt-8 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-xs text-slate-500">© <?php echo date('Y'); ?> TechStore. <?php echo t('all_rights_reserved'); ?></p>
                <div class="flex gap-6 text-xs">
                    <a href="<?php echo url('about.php'); ?>" class="text-slate-500 hover:text-primary transition-colors"><?php echo t('privacy_policy'); ?></a>
                    <a href="<?php echo url('about.php'); ?>" class="text-slate-500 hover:text-primary transition-colors"><?php echo t('terms_of_service'); ?></a>
                    <a href="<?php echo url('about.php'); ?>" class="text-slate-500 hover:text-primary transition-colors">Cookie Settings</a>
                </div>
            </div>
        </div>
    </footer>

    <script src="<?php echo asset('js/app.js'); ?>"></script>
</body>
</html>
