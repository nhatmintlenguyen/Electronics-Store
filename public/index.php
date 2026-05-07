<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/bootstrap.php';

$router = new App\Core\Router();

require ROUTES_PATH . '/web.php';
require ROUTES_PATH . '/api.php';
require ROUTES_PATH . '/admin.php';

(new App\Core\App($router))->run();
