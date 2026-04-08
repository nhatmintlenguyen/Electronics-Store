<?php
declare(strict_types=1);

function url(string $path = ''): string
{
    $path = ltrim($path, '/');

    return $path === ''
        ? appBaseUrl()
        : appBaseUrl() . '/' . $path;
}

function asset(string $path): string
{
    return url('assets/' . ltrim($path, '/'));
}

function redirectTo(string $path): void
{
    header('Location: ' . url($path));
    exit();
}

function isLoggedIn(): bool
{
    return isset($_SESSION['user_id']);
}

function isAdmin(): bool
{
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function requireLogin(): void
{
    if (!isLoggedIn()) {
        redirectTo('login.php');
    }
}

function requireAdmin(): void
{
    if (!isAdmin()) {
        redirectTo('index.php');
    }
}

function sanitize(string $data): string
{
    return htmlspecialchars(strip_tags(trim($data)));
}

function formatPrice(float|int|string $price): string
{
    return number_format((float) $price, 0, ',', '.') . '₫';
}

function hashPassword(string $password): string
{
    return hash('sha256', $password);
}

function verifyPassword(string $password, string $hash): bool
{
    return hash('sha256', $password) === $hash;
}
