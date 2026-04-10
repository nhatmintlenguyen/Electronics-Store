<?php
require_once __DIR__ . '/../app/bootstrap.php';

header('Content-Type: application/json; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$productId = (int) ($_POST['product_id'] ?? 0);

if ($productId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid product']);
    exit;
}

$conn = getDBConnection();
$stmt = $conn->prepare('SELECT id FROM products WHERE id = :id LIMIT 1');
$stmt->execute([':id' => $productId]);

if (!$stmt->fetch()) {
    echo json_encode(['success' => false, 'message' => 'Product not found']);
    exit;
}

$_SESSION['wishlist'] ??= [];
if (!in_array($productId, $_SESSION['wishlist'], true)) {
    $_SESSION['wishlist'][] = $productId;
}

echo json_encode([
    'success' => true,
    'message' => 'Đã thêm vào yêu thích.',
]);
