<?php
declare(strict_types=1);

class ApiController
{
    public function searchProducts(): void
    {
        $this->jsonHeaders();
        $query = trim((string) ($_GET['q'] ?? ''));

        $this->json([
            'success' => true,
            'products' => $query === '' ? [] : Product::searchForApi(getDBConnection(), $query, 6),
        ]);
    }

    public function filterProducts(): void
    {
        $this->jsonHeaders();
        $perPage = 20;
        $categoryIdParam = (string) ($_GET['category_id'] ?? 'all');
        $categoryId = $categoryIdParam !== 'all' && ctype_digit($categoryIdParam)
            ? (int) $categoryIdParam
            : null;

        $payload = Product::filterForApi(getDBConnection(), $categoryId, (int) ($_GET['page'] ?? 1), $perPage);
        $payload['success'] = true;
        $payload['category_id'] = $categoryIdParam;

        $this->json($payload);
    }

    public function addToCart(): void
    {
        $this->jsonHeaders();
        $this->requirePost();

        $productId = (int) ($_POST['product_id'] ?? 0);
        $quantity = max(1, (int) ($_POST['quantity'] ?? 1));

        if ($productId <= 0) {
            $this->json(['success' => false, 'message' => 'Invalid product']);
        }

        if (!Product::exists(getDBConnection(), $productId)) {
            $this->json(['success' => false, 'message' => 'Product not found']);
        }

        $_SESSION['cart'] ??= [];
        $_SESSION['cart'][$productId] = (int) ($_SESSION['cart'][$productId] ?? 0) + $quantity;
        $_SESSION['cart_count'] = array_sum(array_map('intval', $_SESSION['cart']));

        $this->json([
            'success' => true,
            'message' => 'Added to cart.',
            'cart_count' => $_SESSION['cart_count'],
        ]);
    }

    public function addToWishlist(): void
    {
        $this->jsonHeaders();
        $this->requirePost();

        $productId = (int) ($_POST['product_id'] ?? 0);

        if ($productId <= 0) {
            $this->json(['success' => false, 'message' => 'Invalid product']);
        }

        if (!Product::exists(getDBConnection(), $productId)) {
            $this->json(['success' => false, 'message' => 'Product not found']);
        }

        $_SESSION['wishlist'] ??= [];
        if (!in_array($productId, $_SESSION['wishlist'], true)) {
            $_SESSION['wishlist'][] = $productId;
        }

        $this->json([
            'success' => true,
            'message' => 'Added to wishlist.',
        ]);
    }

    private function jsonHeaders(): void
    {
        header('Content-Type: application/json; charset=UTF-8');
    }

    private function requirePost(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            $this->json(['success' => false, 'message' => 'Method not allowed']);
        }
    }

    private function json(array $payload): never
    {
        echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }
}
