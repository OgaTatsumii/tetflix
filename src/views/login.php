<?php
require_once '../controllers/AuthController.php';
require_once 'components/alert.php';

// Đảm bảo session được khởi tạo
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$authController = new AuthController();

// Kiểm tra nếu đã đăng nhập
if (isset($_SESSION['user_id'])) {
    header('Location: /');
    exit;
}

// Xử lý đăng nhập POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $authController->login();
}

ob_start();
?>

<link rel="stylesheet" href="/css/auth.css">

<div class="auth-page">
    <div class="auth-container">
        <div class="auth-box">
            <h2>Đăng nhập</h2>

            <?php showAlert(); ?>

            <form action="/login" method="POST">
                <div class="form-group">
                    <label for="username" class="form-label">Tên đăng nhập</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password" class="form-label">Mật khẩu</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-danger w-100">Đăng nhập</button>

                <div class="text-center mt-3">
                    <p class="mb-0">Chưa có tài khoản? <a href="/register" class="text-danger">Đăng ký ngay</a></p>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once 'layout.php';
?>