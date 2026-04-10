<!DOCTYPE html>
<html class="light" lang="<?php echo getCurrentLanguage(); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' . SITE_NAME : SITE_NAME; ?></title>
    <meta
        name="description"
        content="<?php echo htmlspecialchars($page_description ?? 'TechStore là website điện tử động, hỗ trợ tìm kiếm AJAX, danh mục sản phẩm, vị trí cửa hàng và mua sắm trực tuyến.'); ?>"
    >
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#0f6eeb",
                        "background-light": "#f6f7f8",
                        "background-dark": "#101822",
                    },
                    fontFamily: {
                        "display": ["Inter", "sans-serif"]
                    },
                    borderRadius: {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                },
            },
        }
    </script>
    <link rel="stylesheet" href="<?php echo asset('css/style.css'); ?>">
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            display: inline-block;
            vertical-align: middle;
        }
    </style>
</head>
<body data-app-base-url="<?php echo htmlspecialchars(url()); ?>" class="bg-background-light dark:bg-background-dark font-display text-slate-900 dark:text-slate-100 antialiased">
    <?php
    $currentScript = basename((string) ($_SERVER['SCRIPT_NAME'] ?? ''));
    $isHomeActive = $currentScript === 'index.php';
    $isProductsActive = in_array($currentScript, ['products.php', 'product_detail.php'], true);
    $isContactActive = $currentScript === 'contact.php';
    $navLinkClass = static function (bool $isActive): string {
        return $isActive
            ? 'text-sm font-semibold text-primary'
            : 'text-sm font-semibold text-slate-600 transition-colors hover:text-primary dark:text-slate-300 dark:hover:text-primary';
    };
    ?>
    <div class="topbar border-b border-slate-200/80 bg-slate-950 text-slate-100 dark:border-slate-800 dark:bg-black">
        <div class="max-w-7xl mx-auto flex min-h-9 items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
            <p class="topbar-copy truncate text-[11px] font-medium uppercase tracking-[0.18em] text-slate-300">
                Thiết bị điện tử cao cấp và phụ kiện công nghệ chính hãng
            </p>
            <div class="flex items-center gap-2 text-[11px] font-semibold text-slate-100">
                <span class="material-symbols-outlined text-sm text-primary">local_shipping</span>
                <span class="whitespace-nowrap">Miễn phí Ship cho đơn hàng trên 1,000,000₫</span>
            </div>
        </div>
    </div>
    <nav class="sticky top-0 z-50 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 gap-8">
                <div class="flex items-center gap-6 flex-shrink-0">
                    <a href="<?php echo url('index.php'); ?>" class="flex items-center gap-2">
                        <div class="text-primary">
                            <span class="material-symbols-outlined text-3xl">bolt</span>
                        </div>
                        <span class="text-xl font-bold tracking-tight text-slate-900 dark:text-white">TechStore</span>
                    </a>

                    <div class="hidden lg:flex items-center gap-5">
                        <a href="<?php echo url('index.php'); ?>" class="<?php echo $navLinkClass($isHomeActive); ?>">
                            <?php echo t('home'); ?>
                        </a>
                        <a href="<?php echo url('products.php'); ?>" class="<?php echo $navLinkClass($isProductsActive); ?>">
                            <?php echo t('products'); ?>
                        </a>
                        <a href="<?php echo url('contact.php'); ?>" class="<?php echo $navLinkClass($isContactActive); ?>">
                            <?php echo t('contact'); ?>
                        </a>
                    </div>
                </div>

                <div class="flex-1 max-w-2xl hidden md:block">
                    <form action="<?php echo url('products.php'); ?>" method="GET" class="relative" data-search-form>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="material-symbols-outlined text-slate-400 group-focus-within:text-primary transition-colors">search</span>
                            </div>
                            <input class="block w-full pl-10 pr-3 py-2 border border-slate-200 dark:border-slate-700 rounded-lg leading-5 bg-slate-50 dark:bg-slate-800 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary sm:text-sm transition-all"
                                   placeholder="<?php echo t('search_placeholder'); ?>"
                                   type="text"
                                   name="search"
                                   autocomplete="off"
                                   data-search-input
                                   data-search-endpoint="<?php echo url('search_products.php'); ?>"/>
                        </div>
                        <div
                            class="absolute left-0 right-0 top-[calc(100%+0.5rem)] hidden overflow-hidden rounded-xl border border-slate-200 bg-white shadow-2xl dark:border-slate-700 dark:bg-slate-900"
                            data-search-results
                        >
                            <div class="max-h-[28rem] overflow-y-auto" data-search-results-list></div>
                        </div>
                    </form>
                </div>

                <div class="flex items-center gap-4">
                    <a class="flex flex-col items-center text-slate-600 dark:text-slate-400 hover:text-primary dark:hover:text-primary transition-colors" href="<?php echo isLoggedIn() ? url('profile.php') : url('login.php'); ?>">
                        <span class="material-symbols-outlined">person</span>
                        <span class="text-[10px] font-medium mt-0.5"><?php echo t('account'); ?></span>
                    </a>
                    <a class="flex flex-col items-center text-slate-600 dark:text-slate-400 hover:text-primary dark:hover:text-primary transition-colors" href="<?php echo url('wishlist.php'); ?>">
                        <span class="material-symbols-outlined">favorite</span>
                        <span class="text-[10px] font-medium mt-0.5"><?php echo t('wishlist'); ?></span>
                    </a>
                    <?php $cartCount = (int) ($_SESSION['cart_count'] ?? 0); ?>
                    <a href="<?php echo url('cart.php'); ?>" class="relative flex flex-col items-center text-slate-600 dark:text-slate-400 hover:text-primary dark:hover:text-primary transition-colors">
                        <span class="material-symbols-outlined">shopping_cart</span>
                        <span class="text-[10px] font-medium mt-0.5"><?php echo t('cart'); ?></span>
                        <span
                            data-cart-count
                            class="absolute -top-1 -right-1 bg-primary text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full ring-2 ring-white dark:ring-slate-900 <?php echo $cartCount > 0 ? '' : 'hidden'; ?>"
                        >
                            <?php echo $cartCount; ?>
                        </span>
                    </a>

                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
