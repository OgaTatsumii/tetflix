<?php
class Comment
{
    private $conn;
    private $table_name = "comments";

    public $id;
    public $user_id;
    public $movie_id;
    public $comment;
    public $created_at;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Cố tình tạo lỗ hổng XSS
    public function addComment($user_id, $movie_id, $comment)
    {
        $query = "INSERT INTO " . $this->table_name . " (user_id, movie_id, comment, created_at) VALUES (?, ?, ?, NOW())";
        $stmt = $this->conn->prepare($query);
        // Bỏ qua việc làm sạch comment để tạo lỗ hổng XSS
        $stmt->bindParam(1, $user_id);
        $stmt->bindParam(2, $movie_id);
        $stmt->bindParam(3, $comment);
        return $stmt->execute();
    }

    // Lấy bình luận, đã an toàn
    public function getMovieComments($movie_id)
    {
        $query = "SELECT c.id, c.user_id, c.comment, c.created_at, u.username 
                  FROM " . $this->table_name . " c 
                  JOIN users u ON c.user_id = u.id 
                  WHERE c.movie_id = ? 
                  ORDER BY c.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $movie_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }

    // Sửa lỗ hổng IDOR
    public function deleteComment($comment_id, $user_id)
    {
        // Lấy user_id của comment trước khi xóa để đảm bảo đúng người xóa
        $query = "SELECT user_id FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $comment_id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row && $row['user_id'] == $user_id) {
            $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $comment_id);
            return $stmt->execute();
        }
        return false; // Không tìm thấy comment hoặc không đúng quyền xóa
    }
}
?>