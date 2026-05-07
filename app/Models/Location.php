<?php
declare(strict_types=1);

class Location
{
    public static function all(PDO $conn): array
    {
        return $conn->query('SELECT * FROM locations ORDER BY name ASC')->fetchAll();
    }

    public static function forProduct(PDO $conn, int $productId): array
    {
        $stmt = $conn->prepare(
            'SELECT l.*
             FROM product_locations pl
             JOIN locations l ON l.id = pl.location_id
             WHERE pl.product_id = :product_id
             ORDER BY l.name ASC'
        );
        $stmt->execute([':product_id' => $productId]);

        return $stmt->fetchAll();
    }
}
