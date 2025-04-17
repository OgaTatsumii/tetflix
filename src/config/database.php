<?php
class Database
{
    private $host = 'db';  // tên service là 'db' trong docker-compose
    private $db_name = 'netflix_db';
    private $username = 'netflix_user';
    private $password = 'netflix_password';
    public $conn;

    public function __construct()
    {
        // Sử dụng giá trị mặc định từ docker-compose nếu không có biến môi trường
        $this->host = getenv('DB_HOST') ?: 'db';
        $this->db_name = getenv('DB_NAME') ?: 'netflix_db';
        $this->username = getenv('DB_USER') ?: 'netflix_user';
        $this->password = getenv('DB_PASS') ?: 'netflix_password';
    }

    public function getConnection()
    {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Connection Error: " . $e->getMessage();
        }

        return $this->conn;
    }

    public function testConnection()
    {
        try {
            $conn = $this->getConnection();
            if ($conn) {
                echo "Kết nối database thành công!";
                return true;
            }
            return false;
        } catch (Exception $e) {
            echo "Lỗi: " . $e->getMessage();
            return false;
        }
    }
}