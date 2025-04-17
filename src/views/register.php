<?php
require_once '../controllers/AuthController.php';
require_once 'components/alert.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$authController = new AuthController();

// Kiểm tra nếu đã đăng nhập
if (isset($_SESSION['user_id'])) {
    header('Location: /');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $authController->register();
    if (is_array($result)) {
        if ($result['success']) {
            $success = $result['message'];
            // Redirect after successful registration
            echo "<script>window.location.href = '/login';</script>";
            exit();
        } else {
            $error = $result['message'];
        }
    }
}

// Check for session messages
if (isset($_SESSION['success'])) {
    $success = $_SESSION['success'];
    unset($_SESSION['success']);
}

ob_start();
?>

<link rel="stylesheet" href="/css/auth.css">

<div class="auth-page">
    <div class="auth-container">
        <div class="auth-box">
            <h2>Đăng ký</h2>

            <?php showAlert(); ?>

            <form action="/register" method="POST">
                <div class="form-group">
                    <label for="username" class="form-label">Tên đăng nhập</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password" class="form-label">Mật khẩu</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password" class="form-label">Xác nhận mật khẩu</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                </div>
                <button type="submit" class="btn btn-danger w-100">Đăng ký</button>

                <div class="text-center mt-3">
                    <p class="mb-0">Đã có tài khoản? <a href="/login" class="text-danger">Đăng nhập ngay</a></p>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once 'layout.php';
?>