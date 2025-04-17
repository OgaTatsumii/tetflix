<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Lưu thông báo đăng xuất thành công
$_SESSION['success'] = 'Đăng xuất thành công!';

// Xóa tất cả các biến session
$_SESSION = array();

// Hủy session
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

session_destroy();

// Chuyển hướng đến trang đăng nhập
header('Location: /login');
exit;