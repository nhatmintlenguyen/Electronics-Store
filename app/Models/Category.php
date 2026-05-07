<?php
declare(strict_types=1);

class Category
{
    public static function all(PDO $conn): array
    {
        return $conn->query('SELECT id, name FROM categories ORDER BY name ASC')->fetchAll();
    }
}
