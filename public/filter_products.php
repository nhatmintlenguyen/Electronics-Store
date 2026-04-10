<?php
require_once __DIR__ . '/../app/bootstrap.php';

header('Content-Type: application/json; charset=UTF-8');

$conn = getDBConnection();
$perPage = 20;
$categoryId = (string) ($_GET['category_id'] ?? 'all');
$currentPage = max(1, (int) ($_GET['page'] ?? 1));
$params = [];

$countSql = 'SELECT COUNT(*) FROM products';
$productsSql =
    'SELECT p.id, p.name, p.image_url, p.price, p.rating, c.name AS category_name
     FROM products p
     JOIN categories c ON p.category_id = c.id';

if ($categoryId !== 'all' && ctype_digit($categoryId)) {
    $countSql .= ' WHERE category_id = :category_id';
    $productsSql .= ' WHERE p.category_id = :category_id';
    $params[':category_id'] = (int) $categoryId;
}

$productsSql .= ' ORDER BY p.created_at DESC, p.id DESC LIMIT :limit OFFSET :offset';

$countStmt = $conn->prepare($countSql);
foreach ($params as $key => $value) {
    $countStmt->bindValue($key, $value, PDO::PARAM_INT);
}
$countStmt->execute();
$totalProducts = (int) $countStmt->fetchColumn();
$totalPages = max(1, (int) ceil($totalProducts / $perPage));
$currentPage = min($currentPage, $totalPages);
$offset = ($currentPage - 1) * $perPage;

$productsStmt = $conn->prepare($productsSql);
foreach ($params as $key => $value) {
    $productsStmt->bindValue($key, $value, PDO::PARAM_INT);
}
$productsStmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$productsStmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$productsStmt->execute();
$products = $productsStmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    'success' => true,
    'products' => $products,
    'total_products' => $totalProducts,
    'current_page' => $currentPage,
    'total_pages' => $totalPages,
    'per_page' => $perPage,
    'category_id' => $categoryId,
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
