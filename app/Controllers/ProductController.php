<?php
declare(strict_types=1);

class ProductController
{
    public function index(): void
    {
        $conn = getDBConnection();
        $categoryFilter = isset($_GET['category']) ? (int) $_GET['category'] : null;
        $search = isset($_GET['search']) ? sanitize((string) ($_GET['search'] ?? '')) : '';
        $sort = isset($_GET['sort']) ? (string) $_GET['sort'] : 'name_asc';
        $perPage = 12;

        $pagination = Product::paginate(
            $conn,
            $categoryFilter,
            $search,
            $sort,
            max(1, (int) ($_GET['page'] ?? 1)),
            $perPage
        );

        view('pages/products.php', array_merge($pagination, [
            'page_title' => t('products'),
            'page_description' => 'TechStore product catalog with filters, sorting, and pagination.',
            'categoryFilter' => $categoryFilter,
            'search' => $search,
            'sort' => $sort,
            'categories' => Category::all($conn),
        ]));
    }

    public function show(string|int|null $id = null): void
    {
        $productId = $id !== null ? (int) $id : (isset($_GET['id']) ? (int) $_GET['id'] : 0);

        if ($productId <= 0) {
            redirectTo('products.php');
        }

        $conn = getDBConnection();
        $product = Product::findWithCategory($conn, $productId);

        if (!$product) {
            redirectTo('products.php');
        }

        $productLocations = Location::forProduct($conn, $productId);
        $productDescription = $product['description']
            ?: 'High-quality product with advanced technology and modern design.';
        $productDescription = html_entity_decode($productDescription, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $productDescription = normalizeProductDescriptionHtml($productDescription);
        $productSpecifications = $this->decodeSpecifications($product['specification'] ?? $product['specifications'] ?? null);

        view('pages/product_detail.php', [
            'page_title' => $product['name'],
            'productId' => $productId,
            'product' => $product,
            'productDescription' => $productDescription,
            'productSpecifications' => $productSpecifications,
            'productLocations' => $productLocations,
            'availableLocationCount' => count($productLocations),
            'relatedProducts' => Product::related($conn, (int) $product['category_id'], $productId, 4),
        ]);
    }

    private function decodeSpecifications(mixed $rawSpecifications): array
    {
        if (!is_string($rawSpecifications) || trim($rawSpecifications) === '') {
            return [];
        }

        $decoded = json_decode($rawSpecifications, true);

        if (!is_array($decoded)) {
            return [];
        }

        $specifications = [];
        foreach ($decoded as $label => $value) {
            if (!is_string($label) || $label === '') {
                continue;
            }

            $specifications[$label] = is_array($value)
                ? implode(', ', array_map('strval', $value))
                : (string) $value;
        }

        return $specifications;
    }
}
