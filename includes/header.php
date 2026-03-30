<!DOCTYPE html>
<html class="light" lang="<?php echo getCurrentLanguage(); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' . SITE_NAME : SITE_NAME; ?></title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Material Symbols -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
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
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            display: inline-block;
            vertical-align: middle;
        }
        .product-card:hover .cart-button {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark font-display text-slate-900 dark:text-slate-100 antialiased">
    <!-- Top Navigation Bar -->
    <nav class="sticky top-0 z-50 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 gap-8">
                <!-- Logo -->
                <div class="flex items-center gap-2 flex-shrink-0">
                    <a href="index.php" class="flex items-center gap-2">
                        <div class="text-primary">
                            <span class="material-symbols-outlined text-3xl">bolt</span>
                        </div>
                        <span class="text-xl font-bold tracking-tight text-slate-900 dark:text-white">TechStore</span>
                    </a>
                </div>
                
                <!-- Search Bar (AJAX Style) -->
                <div class="flex-1 max-w-2xl hidden md:block">
                    <form action="products.php" method="GET">
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="material-symbols-outlined text-slate-400 group-focus-within:text-primary transition-colors">search</span>
                            </div>
                            <input class="block w-full pl-10 pr-3 py-2 border border-slate-200 dark:border-slate-700 rounded-lg leading-5 bg-slate-50 dark:bg-slate-800 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary sm:text-sm transition-all" 
                                   placeholder="<?php echo t('search_placeholder'); ?>" 
                                   type="text" 
                                   name="search"/>
                        </div>
                    </form>
                </div>
                
                <!-- User Actions -->
                <div class="flex items-center gap-4">
                    <a class="flex flex-col items-center text-slate-600 dark:text-slate-400 hover:text-primary dark:hover:text-primary transition-colors" href="<?php echo isLoggedIn() ? 'profile.php' : 'login.php'; ?>">
                        <span class="material-symbols-outlined">person</span>
                        <span class="text-[10px] font-medium mt-0.5"><?php echo t('account'); ?></span>
                    </a>
                    <a class="flex flex-col items-center text-slate-600 dark:text-slate-400 hover:text-primary dark:hover:text-primary transition-colors" href="wishlist.php">
                        <span class="material-symbols-outlined">favorite</span>
                        <span class="text-[10px] font-medium mt-0.5"><?php echo t('wishlist'); ?></span>
                    </a>
                    <a href="cart.php" class="relative flex flex-col items-center text-slate-600 dark:text-slate-400 hover:text-primary dark:hover:text-primary transition-colors">
                        <span class="material-symbols-outlined">shopping_cart</span>
                        <span class="text-[10px] font-medium mt-0.5"><?php echo t('cart'); ?></span>
                        <?php if (isset($_SESSION['cart_count']) && $_SESSION['cart_count'] > 0): ?>
                        <span class="absolute -top-1 -right-1 bg-primary text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full ring-2 ring-white dark:ring-slate-900"><?php echo $_SESSION['cart_count']; ?></span>
                        <?php endif; ?>
                    </a>
                    
                    <!-- Language Switcher -->
                    <div class="flex items-center gap-1 ml-2 border-l border-slate-200 dark:border-slate-700 pl-4">
                        <a href="?lang=vi" class="text-sm font-medium <?php echo getCurrentLanguage() == 'vi' ? 'text-primary' : 'text-slate-400 hover:text-slate-600'; ?> transition-colors">VI</a>
                        <span class="text-slate-300">|</span>
                        <a href="?lang=en" class="text-sm font-medium <?php echo getCurrentLanguage() == 'en' ? 'text-primary' : 'text-slate-400 hover:text-slate-600'; ?> transition-colors">EN</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Sub Navigation (Categories) -->
    <div class="bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center gap-8 h-12 overflow-x-auto no-scrollbar">
                <a class="text-sm font-medium text-slate-600 dark:text-slate-400 hover:text-primary py-3 shrink-0 transition-colors" href="products.php">
                    <span class="material-symbols-outlined text-lg">devices</span> <?php echo t('all_categories'); ?>
                </a>
                <?php
                $conn = getDBConnection();
                $stmt = $conn->prepare("SELECT * FROM categories LIMIT 6");
                $stmt->execute();
                $nav_categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($nav_categories as $cat):
                ?>
                <a class="text-sm font-medium text-slate-600 dark:text-slate-400 hover:text-primary py-3 shrink-0 transition-colors" href="products.php?category=<?php echo $cat['id']; ?>">
                    <?php echo htmlspecialchars($cat['name']); ?>
                </a>
                <?php endforeach; ?>
                
                <div class="ml-auto hidden lg:flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">local_shipping</span>
                    <span class="text-sm font-bold text-slate-700 dark:text-slate-300"><?php echo t('free_shipping_desc'); ?></span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    </nav>
    
    <!-- Main Content -->
    <main class="py-4">
