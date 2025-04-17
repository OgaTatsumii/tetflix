<?php
class User
{
    private $conn;
    private $table_name = "users";
    public $id;
    public $username;
    public $password;
    public $email;
    public $is_premium;
    public $role;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Sửa lỗ hổng SQL injection
    public function login($username, $password)
    {
        $hashed_password = md5($password);
        $query = "SELECT * FROM " . $this->table_name . " WHERE username = ? AND password = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $username);
        $stmt->bindParam(2, $hashed_password);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $_SESSION['role'] = $row['role'];
        }
        return $row ?: false;
    }

    // Sửa lỗ hổng IDOR
    public function getUserById($id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt;
    }

    // Sửa lỗ hổng XSS
    public function updateProfile($id, $email)
    {
        $query = "UPDATE " . $this->table_name . " SET email = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $email);
        $stmt->bindParam(2, $id);
        return $stmt->execute();
    }

    public function register($username, $password, $email)
    {
        $query = "INSERT INTO " . $this->table_name . " (username, password, email) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $username);
        $stmt->bindParam(2, $password);
        $stmt->bindParam(3, $email);
        return $stmt->execute();
    }

    // Thêm phương thức đếm tổng số người dùng
    public function getUserCount()
    {
        $query = "SELECT COUNT(*) as count FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['count'];
    }

    // Thêm phương thức đếm số người dùng premium
    public function getPremiumUserCount()
    {
        $query = "SELECT COUNT(*) as count FROM " . $this->table_name . " WHERE is_premium = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['count'];
    }

    // Lấy danh sách người dùng đăng ký gần đây
    public function getRecentUsers($limit = 5)
    {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY created_at DESC LIMIT ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }

    // Lấy danh sách người dùng với bộ lọc
    public function getAllUsersWithFilters($roleFilter = '', $statusFilter = '', $searchTerm = '')
    {
        // Khởi tạo câu truy vấn cơ bản
        $query = "SELECT * FROM " . $this->table_name . " WHERE 1=1";
        $params = [];
        $paramIndex = 1;

        // Thêm điều kiện lọc theo vai trò nếu có
        if (!empty($roleFilter)) {
            $query .= " AND role = ?";
            $params[$paramIndex++] = $roleFilter;
        }

        // Thêm điều kiện lọc theo trạng thái premium nếu có
        if ($statusFilter !== '') {
            $query .= " AND is_premium = ?";
            $params[$paramIndex++] = $statusFilter;
        }

        // Thêm điều kiện tìm kiếm theo tên người dùng hoặc email nếu có
        if (!empty($searchTerm)) {
            $query .= " AND (username LIKE ? OR email LIKE ?)";
            $searchPattern = "%" . $searchTerm . "%";
            $params[$paramIndex++] = $searchPattern;
            $params[$paramIndex++] = $searchPattern;
        }

        // Sắp xếp kết quả theo thời gian tạo giảm dần
        $query .= " ORDER BY created_at DESC";

        $stmt = $this->conn->prepare($query);

        // Bind các tham số
        foreach ($params as $i => $param) {
            $stmt->bindValue($i, $param);
        }

        $stmt->execute();

        return $stmt;
    }
}
?>