<?php
require_once __DIR__ . '/../app/bootstrap.php';

header('Content-Type: application/json; charset=UTF-8');

$query = trim((string) ($_GET['q'] ?? ''));

if ($query === '') {
    echo json_encode([
        'success' => true,
        'products' => [],
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

$conn = getDBConnection();
$searchTerm = '%' . $query . '%';

$stmt = $conn->prepare(
    'SELECT p.id, p.name, p.image_url, p.price, c.name AS category_name
     FROM products p
     JOIN categories c ON c.id = p.category_id
     WHERE p.name LIKE :query
     ORDER BY p.name ASC
     LIMIT 6'
);
$stmt->execute([':query' => $searchTerm]);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    'success' => true,
    'products' => $products,
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
