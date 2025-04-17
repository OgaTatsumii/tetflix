<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session
session_start();

// Set base path
define('BASE_PATH', dirname(__DIR__));

// Include database configuration
require_once BASE_PATH . '/config/database.php';

// Tạo đối tượng Database và lấy kết nối PDO
$database = new Database();
$pdo = $database->getConnection();

// Include controllers
require_once BASE_PATH . '/controllers/AuthController.php';
require_once BASE_PATH . '/controllers/MovieController.php';
require_once BASE_PATH . '/controllers/CommentController.php';
require_once BASE_PATH . '/controllers/AdminController.php';
require_once BASE_PATH . '/controllers/UserController.php';

// Khởi tạo controllers
$authController = new AuthController();
$movieController = new MovieController();
$adminController = new AdminController();
$userController = new UserController();

// Get the requested URL
$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);

// Basic routing
switch ($path) {
    case '/':
        require_once BASE_PATH . '/views/home.php';
        break;

    case '/profile':
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        require_once BASE_PATH . '/views/profile.php';
        break;

    case '/profile/update':
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        try {
            $userController->updateProfile($_SESSION['user_id'], $_POST);
            $_SESSION['success'] = 'Cập nhật thông tin thành công!';
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }
        header('Location: /profile');
        exit;

    case '/profile/update-avatar':
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        // var_dump($_FILES['avatar']);

        try {
            $userController->updateAvatar($_SESSION['user_id'], $_FILES['avatar']);
            $_SESSION['success'] = 'Cập nhật avatar thành công!';
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }
        header('Location: /profile');
        exit;

    case '/profile/change-password':
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        try {
            $userController->changePassword($_SESSION['user_id'], $_POST);
            $_SESSION['success'] = 'Đổi mật khẩu thành công!';
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }
        header('Location: /profile');
        exit;

    case '/profile/delete':
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        try {
            $userController->deleteAccount($_SESSION['user_id'], $_POST['delete_password']);
            session_destroy();
            header('Location: /login?message=account_deleted');
            exit;
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: /profile');
            exit;
        }

    case '/admin-panel':
        require_once BASE_PATH . '/views/admin-panel.php';
        break;

    case '/search':
        require_once BASE_PATH . '/views/search.php';
        break;

    case '/test_db':
        require_once BASE_PATH . '/test_db.php';
        break;

    case '/login':
        require_once BASE_PATH . '/views/login.php';
        break;

    case '/register':
        require_once BASE_PATH . '/views/register.php';
        break;

    case '/movie':
        require_once BASE_PATH . '/views/movie-detail.php';
        break;

    case '/logout':
        require_once BASE_PATH . '/views/logout.php';
        break;

    // Route cho xử lý comment (sửa thành /comment)
    case '/comment':
        // CommentController đã được khởi tạo ở đầu file
        $commentController = new CommentController(); // Đảm bảo đã khởi tạo
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
            if ($_POST['action'] === 'add_comment') {
                $commentController->handleAddCommentRequest();
            } elseif ($_POST['action'] === 'delete_comment') {
                $commentController->handleDeleteCommentRequest();
            } else {
                $_SESSION['error'] = "Hành động không hợp lệ.";
                header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/'));
                exit;
            }
        } else {
            // Nếu không phải POST hoặc không có action, chuyển hướng
            header('Location: /');
            exit;
        }
        break;

    // Admin routes
    case '/admin/add-movie':
        $adminController->addMovie();
        break;

    case '/admin/update-movie':
        $adminController->updateMovie();
        break;

    case '/admin/delete-movie':
        $adminController->deleteMovie();
        break;

    case '/admin/add-episode':
        $adminController->addEpisode();
        break;

    case '/admin/update-episode':
        $adminController->updateEpisode();
        break;

    case '/admin/delete-episode':
        $adminController->deleteEpisode();
        break;

    default:
        // 404 page
        header('HTTP/1.0 404 Not Found');
        echo '404 - Page not found';
        break;
}
?>