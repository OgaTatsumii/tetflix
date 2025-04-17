<?php
require_once '../config/database.php';
require_once '../models/User.php';

class AuthController
{
    private $db;
    private $user;

    public function __construct()
    {
        // Đảm bảo session được bắt đầu ngay khi controller được khởi tạo
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $this->db = new Database();
        $this->user = new User($this->db->getConnection());
    }

    // Sửa lỗ hổng SQL injection
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Xóa các thông báo cũ nếu có
            unset($_SESSION['success']);
            unset($_SESSION['error']);

            $username = $_POST['username'];
            $password = $_POST['password'];

            $row = $this->user->login($username, $password);

            if ($row) {
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['fullname'] = $row['fullname'];
                $_SESSION['avatar_url'] = $row['avatar_url'];
                $_SESSION['is_premium'] = $row['is_premium'];
                $_SESSION['role'] = $row['role'];
                $_SESSION['success'] = 'Đăng nhập thành công!';

                if ($_SESSION['role'] === 'admin') {
                    header('Location: /admin-panel');
                } else {
                    header('Location: /');
                }
                exit();
            } else {
                $_SESSION['error'] = 'Tên đăng nhập hoặc mật khẩu không đúng!';
                header('Location: ' . $_SERVER['HTTP_REFERER']);
                exit();
            }
        }
    }

    // Intentionally vulnerable to XSS
    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Đảm bảo session được bắt đầu
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }

            // Xóa các thông báo cũ nếu có
            unset($_SESSION['success']);
            unset($_SESSION['error']);

            $username = $_POST['username'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';

            // Validate input
            if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
                $_SESSION['error'] = 'Vui lòng điền đầy đủ thông tin!';
                header('Location: ' . $_SERVER['HTTP_REFERER']);
                exit();
            }

            if ($password !== $confirm_password) {
                $_SESSION['error'] = 'Mật khẩu xác nhận không khớp!';
                header('Location: ' . $_SERVER['HTTP_REFERER']);
                exit();
            }

            // Check if username exists
            $stmt = $this->db->getConnection()->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->execute([$username]);
            if ($stmt->fetch()) {
                $_SESSION['error'] = 'Tên đăng nhập đã tồn tại!';
                header('Location: ' . $_SERVER['HTTP_REFERER']);
                exit();
            }

            // Check if email exists
            $stmt = $this->db->getConnection()->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $_SESSION['error'] = 'Email đã được sử dụng!';
                header('Location: ' . $_SERVER['HTTP_REFERER']);
                exit();
            }

            // Create user
            try {
                $hashed_password = md5($password);
                $stmt = $this->db->getConnection()->prepare(
                    "INSERT INTO users (username, email, password) VALUES (?, ?, ?)"
                );

                if ($stmt->execute([$username, $email, $hashed_password])) {
                    $_SESSION['success'] = 'Đăng ký tài khoản thành công! Vui lòng đăng nhập.';
                    header('Location: /login');
                    exit();
                }
            } catch (PDOException $e) {
                $_SESSION['error'] = 'Có lỗi xảy ra, vui lòng thử lại sau!';
                header('Location: ' . $_SERVER['HTTP_REFERER']);
                exit();
            }
        }
    }

    // Intentionally vulnerable to IDOR
    public function getUserProfile($id)
    {
        $result = $this->user->getUserById($id);
        if ($result->rowCount() > 0) {
            return $result->fetch(PDO::FETCH_ASSOC);
        }
        return null;
    }

    // Intentionally vulnerable to XSS
    public function updateProfile()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }

            // Xóa các thông báo cũ nếu có
            unset($_SESSION['success']);
            unset($_SESSION['error']);

            $id = $_SESSION['user_id'];
            $email = $_POST['email'];

            if ($this->user->updateProfile($id, $email)) {
                $_SESSION['success'] = 'Cập nhật thông tin thành công!';
                header('Location: /profile');
                exit();
            } else {
                $_SESSION['error'] = 'Cập nhật thông tin thất bại';
                header('Location: /profile');
                exit();
            }
        }
    }

    public function logout()
    {
        // Đảm bảo session đã được khởi tạo
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Kiểm tra xem người dùng đã đăng nhập chưa
        $wasLoggedIn = isset($_SESSION['user_id']);
        $username = $_SESSION['username'] ?? '';

        // Xóa tất cả dữ liệu session hiện tại
        $_SESSION = array();

        // Xóa cookie session nếu có
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }

        // Hủy session
        session_destroy();

        // Khởi tạo session mới
        session_start();

        // Set thông báo thành công nếu user đã đăng nhập trước đó
        if ($wasLoggedIn) {
            $_SESSION['success'] = 'Đăng xuất tài khoản ' . $username . ' thành công!';
        }

        // Chuyển hướng về trang đăng nhập
        header('Location: /login');
        exit();
    }
}
?>