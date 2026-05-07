<?php
declare(strict_types=1);

class User
{
    public static function findByEmail(PDO $conn, string $email): ?array
    {
        $stmt = $conn->prepare(
            'SELECT id, username, email, password, role
             FROM users
             WHERE email = :email
             LIMIT 1'
        );
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();

        return $user ?: null;
    }

    public static function usernameOrEmailExists(PDO $conn, string $username, string $email): bool
    {
        $stmt = $conn->prepare(
            'SELECT id
             FROM users
             WHERE username = :username OR email = :email
             LIMIT 1'
        );
        $stmt->execute([
            ':username' => $username,
            ':email' => $email,
        ]);

        return (bool) $stmt->fetch();
    }

    public static function createCustomer(PDO $conn, string $username, string $email, string $password, string $fullName): void
    {
        $stmt = $conn->prepare(
            'INSERT INTO users (username, email, password, full_name, role)
             VALUES (:username, :email, :password, :full_name, :role)'
        );
        $stmt->execute([
            ':username' => $username,
            ':email' => $email,
            ':password' => hashPassword($password),
            ':full_name' => $fullName,
            ':role' => 'customer',
        ]);
    }

    public static function updatePasswordByEmail(PDO $conn, string $email, string $password): void
    {
        $stmt = $conn->prepare(
            'UPDATE users
             SET password = :password
             WHERE email = :email'
        );
        $stmt->execute([
            ':password' => hashPassword($password),
            ':email' => $email,
        ]);
    }

    public static function findProfile(PDO $conn, int $id): ?array
    {
        $stmt = $conn->prepare('SELECT id, username, email, full_name, role, created_at FROM users WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $user = $stmt->fetch();

        return $user ?: null;
    }
}
