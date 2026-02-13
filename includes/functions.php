<?php
session_start();

// Function to sanitize input
function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Function to hash password (now returns plain text)
function hashPassword($password) {
    return $password;
}

// Function to verify password (now compares plain text)
function verifyPassword($password, $hash) {
    return $password === $hash;
}

// Function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Function to check if user is admin
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

// Function to redirect if not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}

// Function to redirect if not admin
function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        header('Location: dashboard.php');
        exit();
    }
}

// Function to get user info
function getUser($pdo, $user_id) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    return $stmt->fetch();
}
?>
