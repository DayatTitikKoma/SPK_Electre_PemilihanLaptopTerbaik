<?php
/**
 * Sistem Autentikasi dan Authorization
 */

session_start();
require_once __DIR__ . '/../config/database.php';

/**
 * Cek apakah user sudah login
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Cek apakah user adalah admin
 */
function isAdmin() {
    return isLoggedIn() && isset($_SESSION['role']) && $_SESSION['role'] == 'admin';
}

/**
 * Cek apakah user adalah user biasa
 */
function isUser() {
    return isLoggedIn() && isset($_SESSION['role']) && $_SESSION['role'] == 'user';
}

/**
 * Redirect jika belum login
 */
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

/**
 * Redirect jika bukan admin
 */
function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        header('Location: index.php');
        exit;
    }
}

/**
 * Login user
 */
function login($username, $password) {
    $database = new Database();
    $conn = $database->getConnection();
    
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $username]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id_user'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['email'] = $user['email'];
        return true;
    }
    
    return false;
}

/**
 * Register user baru
 */
function register($username, $email, $password, $nama_lengkap) {
    $database = new Database();
    $conn = $database->getConnection();
    
    // Cek username sudah ada
    $stmt = $conn->prepare("SELECT id_user FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);
    if ($stmt->fetch()) {
        return ['success' => false, 'message' => 'Username atau email sudah terdaftar'];
    }
    
    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert user
    $stmt = $conn->prepare("INSERT INTO users (username, email, password, nama_lengkap, role) VALUES (?, ?, ?, ?, 'user')");
    try {
        $stmt->execute([$username, $email, $hashedPassword, $nama_lengkap]);
        return ['success' => true, 'message' => 'Registrasi berhasil'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
}

/**
 * Logout user
 */
function logout() {
    session_unset();
    session_destroy();
    header('Location: login.php');
    exit;
}

/**
 * Get current user data
 */
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    return [
        'id_user' => $_SESSION['user_id'],
        'username' => $_SESSION['username'],
        'nama_lengkap' => $_SESSION['nama_lengkap'],
        'role' => $_SESSION['role'],
        'email' => $_SESSION['email']
    ];
}
?>

