<?php
declare(strict_types=1);

/** @var App\Core\Router $router */

$router->get('/search-products', [ApiController::class, 'searchProducts']);
$router->get('/search_products.php', [ApiController::class, 'searchProducts']);
$router->get('/filter-products', [ApiController::class, 'filterProducts']);
$router->get('/filter_products.php', [ApiController::class, 'filterProducts']);
$router->post('/add-to-cart', [ApiController::class, 'addToCart']);
$router->post('/add_to_cart.php', [ApiController::class, 'addToCart']);
$router->post('/add-to-wishlist', [ApiController::class, 'addToWishlist']);
$router->post('/add_to_wishlist.php', [ApiController::class, 'addToWishlist']);
