<?php
session_start();
require_once 'database.php';

// Cek apakah user sudah login
function isLoggedIn() {
    return isset($_SESSION['admin_id']);
}

// Redirect jika belum login
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: index.php');
        exit();
    }
}

// Redirect jika sudah login (untuk halaman login)
function redirectIfLoggedIn() {
    if (isLoggedIn()) {
        header('Location: dashboard.php');
        exit();
    }
}

// Proses login
function loginAdmin($username, $password) {
    $pdo = getConnection();
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['admin_id'] = $user['id'];
        $_SESSION['admin_username'] = $user['username'];
        $_SESSION['admin_nama'] = $user['nama'];
        return true;
    }
    return false;
}

// Logout
function logoutAdmin() {
    session_destroy();
    header('Location: index.php');
    exit();
}
?>