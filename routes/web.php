<?php
declare(strict_types=1);

/** @var App\Core\Router $router */

$router->get('/', [HomeController::class, 'index']);
$router->get('/index.php', [HomeController::class, 'index']);

$router->get('/products', [ProductController::class, 'index']);
$router->get('/products.php', [ProductController::class, 'index']);
$router->get('/product/{id}', [ProductController::class, 'show']);
$router->get('/product_detail.php', [ProductController::class, 'show']);

$router->get('/about', [PageController::class, 'about']);
$router->get('/about.php', [PageController::class, 'about']);
$router->get('/contact', [PageController::class, 'contact']);
$router->get('/contact.php', [PageController::class, 'contact']);
$router->get('/locations', [PageController::class, 'locations']);
$router->get('/locations.php', [PageController::class, 'locations']);

$router->get('/login', [AuthController::class, 'login']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/login.php', [AuthController::class, 'login']);
$router->post('/login.php', [AuthController::class, 'login']);
$router->get('/logout', [AuthController::class, 'logout']);
$router->get('/logout.php', [AuthController::class, 'logout']);

$router->get('/profile', [PageController::class, 'profile']);
$router->get('/profile.php', [PageController::class, 'profile']);
$router->get('/cart', [CartController::class, 'index']);
$router->get('/cart.php', [CartController::class, 'index']);
$router->get('/wishlist', [WishlistController::class, 'index']);
$router->get('/wishlist.php', [WishlistController::class, 'index']);
