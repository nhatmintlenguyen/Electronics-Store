<?php
declare(strict_types=1);

class Product
{
    public static function countAll(PDO $conn): int
    {
        return (int) $conn->query('SELECT COUNT(*) FROM products')->fetchColumn();
    }

    public static function exists(PDO $conn, int $id): bool
    {
        $stmt = $conn->prepare('SELECT id FROM products WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);

        return (bool) $stmt->fetch();
    }

    public static function latest(PDO $conn, int $limit = 20): array
    {
        $stmt = $conn->prepare(
            'SELECT p.*, c.name AS category_name
             FROM products p
             JOIN categories c ON p.category_id = c.id
             ORDER BY p.created_at DESC, p.id DESC
             LIMIT :limit'
        );
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public static function featured(PDO $conn, int $limit = 4): array
    {
        $stmt = $conn->prepare(
            'SELECT p.*, c.name AS category_name
             FROM products p
             JOIN categories c ON p.category_id = c.id
             ORDER BY p.rating DESC, p.id DESC
             LIMIT :limit'
        );
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public static function featuredByIds(PDO $conn, array $ids): array
    {
        $productIds = array_values(array_unique(array_filter(array_map('intval', $ids))));

        if (!$productIds) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($productIds), '?'));
        $orderExpression = implode(',', $productIds);
        $stmt = $conn->prepare(
            "SELECT p.*, c.name AS category_name
             FROM products p
             JOIN categories c ON p.category_id = c.id
             WHERE p.id IN ($placeholders)
             ORDER BY FIELD(p.id, $orderExpression)"
        );
        $stmt->execute($productIds);

        return $stmt->fetchAll();
    }

    public static function paginate(PDO $conn, ?int $categoryId, string $search, string $sort, int $page, int $perPage): array
    {
        $baseQuery =
            'FROM products p
             JOIN categories c ON p.category_id = c.id
             WHERE 1=1';
        $params = [];

        if ($categoryId) {
            $baseQuery .= ' AND p.category_id = :category';
            $params[':category'] = $categoryId;
        }

        if ($search !== '') {
            $baseQuery .= ' AND p.name LIKE :search';
            $params[':search'] = '%' . $search . '%';
        }

        $orderBy = match ($sort) {
            'price_asc' => ' ORDER BY p.price ASC, p.name ASC',
            'price_desc' => ' ORDER BY p.price DESC, p.name ASC',
            'rating' => ' ORDER BY p.rating DESC, p.name ASC',
            default => ' ORDER BY p.name ASC',
        };

        $countStmt = $conn->prepare('SELECT COUNT(*) ' . $baseQuery);
        $countStmt->execute($params);
        $totalProducts = (int) $countStmt->fetchColumn();
        $totalPages = max(1, (int) ceil($totalProducts / $perPage));
        $currentPage = min(max(1, $page), $totalPages);
        $offset = ($currentPage - 1) * $perPage;

        $productsStmt = $conn->prepare(
            'SELECT p.*, c.name AS category_name ' . $baseQuery . $orderBy . ' LIMIT :limit OFFSET :offset'
        );

        foreach ($params as $key => $value) {
            $productsStmt->bindValue($key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }

        $productsStmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $productsStmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $productsStmt->execute();

        return [
            'products' => $productsStmt->fetchAll(),
            'totalProducts' => $totalProducts,
            'totalPages' => $totalPages,
            'currentPage' => $currentPage,
            'startPage' => max(1, $currentPage - 2),
            'endPage' => min($totalPages, $currentPage + 2),
        ];
    }

    public static function filterForApi(PDO $conn, ?int $categoryId, int $page, int $perPage): array
    {
        $countSql = 'SELECT COUNT(*) FROM products';
        $productsSql =
            'SELECT p.id, p.name, p.image_url, p.price, p.rating, c.name AS category_name
             FROM products p
             JOIN categories c ON p.category_id = c.id';
        $params = [];

        if ($categoryId !== null) {
            $countSql .= ' WHERE category_id = :category_id';
            $productsSql .= ' WHERE p.category_id = :category_id';
            $params[':category_id'] = $categoryId;
        }

        $productsSql .= ' ORDER BY p.created_at DESC, p.id DESC LIMIT :limit OFFSET :offset';

        $countStmt = $conn->prepare($countSql);
        foreach ($params as $key => $value) {
            $countStmt->bindValue($key, $value, PDO::PARAM_INT);
        }
        $countStmt->execute();

        $totalProducts = (int) $countStmt->fetchColumn();
        $totalPages = max(1, (int) ceil($totalProducts / $perPage));
        $currentPage = min(max(1, $page), $totalPages);
        $offset = ($currentPage - 1) * $perPage;

        $productsStmt = $conn->prepare($productsSql);
        foreach ($params as $key => $value) {
            $productsStmt->bindValue($key, $value, PDO::PARAM_INT);
        }
        $productsStmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $productsStmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $productsStmt->execute();

        return [
            'products' => $productsStmt->fetchAll(PDO::FETCH_ASSOC),
            'total_products' => $totalProducts,
            'current_page' => $currentPage,
            'total_pages' => $totalPages,
            'per_page' => $perPage,
        ];
    }

    public static function searchForApi(PDO $conn, string $query, int $limit = 6): array
    {
        $stmt = $conn->prepare(
            'SELECT p.id, p.name, p.image_url, p.price, c.name AS category_name
             FROM products p
             JOIN categories c ON c.id = p.category_id
             WHERE p.name LIKE :query
             ORDER BY p.name ASC
             LIMIT :limit'
        );
        $stmt->bindValue(':query', '%' . $query . '%');
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function findWithCategory(PDO $conn, int $id): ?array
    {
        $stmt = $conn->prepare(
            'SELECT p.*, c.name AS category_name
             FROM products p
             JOIN categories c ON p.category_id = c.id
             WHERE p.id = :id'
        );
        $stmt->execute([':id' => $id]);
        $product = $stmt->fetch();

        return $product ?: null;
    }

    public static function related(PDO $conn, int $categoryId, int $excludeProductId, int $limit = 4): array
    {
        $stmt = $conn->prepare(
            'SELECT p.*, c.name AS category_name
             FROM products p
             JOIN categories c ON p.category_id = c.id
             WHERE p.category_id = :category_id AND p.id != :id
             ORDER BY RAND()
             LIMIT :limit'
        );
        $stmt->bindValue(':category_id', $categoryId, PDO::PARAM_INT);
        $stmt->bindValue(':id', $excludeProductId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public static function findManyWithCategory(PDO $conn, array $ids): array
    {
        $productIds = array_values(array_unique(array_map('intval', $ids)));

        if (!$productIds) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($productIds), '?'));
        $stmt = $conn->prepare(
            "SELECT p.*, c.name AS category_name
             FROM products p
             JOIN categories c ON p.category_id = c.id
             WHERE p.id IN ($placeholders)
             ORDER BY p.name ASC"
        );
        $stmt->execute($productIds);

        return $stmt->fetchAll();
    }
}
