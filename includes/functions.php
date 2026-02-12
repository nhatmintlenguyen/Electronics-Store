<?php
require_once 'config.php';

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Check if user is admin
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

// Redirect if not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}

// Redirect if not admin
function requireAdmin() {
    if (!isAdmin()) {
        header('Location: index.php');
        exit();
    }
}

// Sanitize input
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Format price in VND
function formatPrice($price) {
    return number_format($price, 0, ',', '.') . '₫';
}

// Hash password
function hashPassword($password) {
    return hash('sha256', $password);
}

// Verify password
function verifyPassword($password, $hash) {
    return hash('sha256', $password) === $hash;
}
?>
