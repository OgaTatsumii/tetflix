<?php
require_once '../config/database.php';
require_once '../models/Comment.php';

class CommentController
{
    private $db;
    private $comment;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->comment = new Comment($this->db);
    }

    // Đã sửa, an toàn hơn
    public function addComment($user_id, $movie_id, $comment_text)
    {
        // Validate input
        if (empty(trim($comment_text))) {
            return false;
        }

        return $this->comment->addComment($user_id, $movie_id, $comment_text);
    }

    // Đã sửa, an toàn hơn
    public function getMovieComments($movie_id)
    {
        return $this->comment->getMovieComments($movie_id);
    }

    // Đã sửa, an toàn hơn
    public function deleteComment($comment_id, $user_id)
    {
        return $this->comment->deleteComment($comment_id, $user_id);
    }

    // Hàm xử lý request POST để thêm comment
    public function handleAddCommentRequest()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_comment') {
            if (!isset($_SESSION['user_id'])) {
                $_SESSION['error'] = "Vui lòng đăng nhập để bình luận.";
                header('Location: ' . $_SERVER['HTTP_REFERER']); // Quay lại trang trước
                exit;
            }

            $user_id = $_SESSION['user_id'];
            $movie_id = isset($_POST['movie_id']) ? intval($_POST['movie_id']) : 0;
            $comment_text = $_POST['comment'] ?? '';

            if ($movie_id <= 0 || empty(trim($comment_text))) {
                $_SESSION['error'] = "Nội dung bình luận không được để trống.";
            } else {
                if ($this->addComment($user_id, $movie_id, $comment_text)) {
                    $_SESSION['success'] = "Bình luận đã được thêm.";
                } else {
                    $_SESSION['error'] = "Không thể thêm bình luận.";
                }
            }
            header('Location: ' . $_SERVER['HTTP_REFERER']); // Quay lại trang trước
            exit;
        }
    }

    // Hàm xử lý request POST để xóa comment
    public function handleDeleteCommentRequest()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_comment') {
            if (!isset($_SESSION['user_id'])) {
                $_SESSION['error'] = "Vui lòng đăng nhập.";
                header('Location: ' . $_SERVER['HTTP_REFERER']);
                exit;
            }

            $user_id = $_SESSION['user_id'];
            $comment_id = isset($_POST['comment_id']) ? intval($_POST['comment_id']) : 0;

            if ($comment_id > 0) {
                if ($this->deleteComment($comment_id, $user_id)) {
                    $_SESSION['success'] = "Bình luận đã được xóa.";
                } else {
                    $_SESSION['error'] = "Không thể xóa bình luận hoặc bạn không có quyền.";
                }
            } else {
                $_SESSION['error'] = "ID bình luận không hợp lệ.";
            }
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        }
    }
}
?>