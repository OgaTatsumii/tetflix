<?php
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/models/Movie.php';
require_once BASE_PATH . '/models/User.php';
require_once BASE_PATH . '/controllers/MovieController.php';

class AdminController
{
    private $db;
    private $movie;
    private $user;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->movie = new Movie($this->db);
        $this->user = new User($this->db);
    }

    public function addMovie()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Lấy thông tin phim từ form
            $title = $_POST['title'];
            $description = $_POST['description'];
            $release_year = $_POST['release_year'];
            $genre = isset($_POST['new_genre']) && !empty($_POST['new_genre']) ? $_POST['new_genre'] : $_POST['genre'];
            $duration = $_POST['duration'];
            $is_premium = $_POST['is_premium'];
            $is_series = $_POST['is_series'];
            $trailer_url = $_POST['trailer_url'] ?? '';

            // Xử lý upload poster
            $poster_path = '';
            if (isset($_FILES['poster']) && $_FILES['poster']['error'] === UPLOAD_ERR_OK) {
                $movieController = new MovieController();

                // Upload lên FastAPI service
                $poster_path = $movieController->uploadPoster($_FILES['poster']);

                // Nếu upload thất bại, sử dụng poster mặc định
                if (!$poster_path) {
                    error_log("FastAPI upload failed, using default poster");
                    $poster_path = '/assets/images/default-poster.jpg';
                }
            }

            // Xử lý upload video (nếu có)
            $video_path = '';
            if (isset($_FILES['video']) && $_FILES['video']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = BASE_PATH . '/public/uploads/videos/';

                // Tạo thư mục nếu chưa tồn tại
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }

                $filename = uniqid() . '_' . basename($_FILES['video']['name']);
                $target_file = $upload_dir . $filename;

                // Upload file
                if (move_uploaded_file($_FILES['video']['tmp_name'], $target_file)) {
                    $video_path = 'videos/' . $filename;
                }
            }

            // Thêm phim vào cơ sở dữ liệu
            $movie_id = $this->movie->addMovie(
                $title,
                $description,
                $release_year,
                $genre,
                $duration,
                $poster_path,
                $video_path,
                $trailer_url,
                $is_premium,
                $is_series
            );

            if ($movie_id) {
                // Nếu là series, chuyển hướng đến trang thêm tập phim
                if ($is_series == 1) {
                    header('Location: /admin-panel?page=episodes&movie_id=' . $movie_id);
                    exit();
                } else {
                    // Nếu là phim đơn, xử lý các liên kết nhúng và liên kết trực tiếp
                    $embed_url = $_POST['embed_url'] ?? '';
                    $direct_url = $_POST['direct_url'] ?? '';
                    $hls_url = $_POST['hls_url'] ?? '';

                    // Cập nhật thông tin phim với các liên kết
                    $this->movie->updateMovieLinks($movie_id, $embed_url, $direct_url, $hls_url);

                    // Chuyển hướng đến trang danh sách phim
                    header('Location: /admin-panel?page=movies&success=1');
                    exit();
                }
            } else {
                // Có lỗi khi thêm phim
                header('Location: /admin-panel?page=add-movie&error=1');
                exit();
            }
        }
    }

    public function updateMovie()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['movie_id'])) {
            $movie_id = $_POST['movie_id'];
            $title = $_POST['title'];
            $description = $_POST['description'];
            $release_year = $_POST['release_year'];
            $genre = isset($_POST['new_genre']) && !empty($_POST['new_genre']) ? $_POST['new_genre'] : $_POST['genre'];
            $duration = $_POST['duration'];
            $is_premium = $_POST['is_premium'];
            $is_series = $_POST['is_series'];
            $trailer_url = $_POST['trailer_url'] ?? '';

            // Xử lý upload poster
            $poster_path = '';
            if (isset($_FILES['poster']) && $_FILES['poster']['error'] === UPLOAD_ERR_OK) {
                $movieController = new MovieController();

                // Upload lên FastAPI service
                $poster_path = $movieController->uploadPoster($_FILES['poster']);

                // Nếu upload thất bại, sử dụng poster mặc định
                if (!$poster_path) {
                    error_log("FastAPI upload failed, using default poster");
                    $poster_path = '/assets/images/default-poster.jpg';
                }
            }

            // Xử lý upload video (nếu có)
            $video_path = '';
            if (isset($_FILES['video']) && $_FILES['video']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = BASE_PATH . '/public/uploads/videos/';

                // Tạo thư mục nếu chưa tồn tại
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }

                $filename = uniqid() . '_' . basename($_FILES['video']['name']);
                $target_file = $upload_dir . $filename;

                // Upload file
                if (move_uploaded_file($_FILES['video']['tmp_name'], $target_file)) {
                    $video_path = 'videos/' . $filename;
                }
            }

            // Cập nhật phim
            $result = $this->movie->updateMovie(
                $movie_id,
                $title,
                $description,
                $release_year,
                $genre,
                $duration,
                $poster_path, // Nếu poster_path trống, Movie::updateMovie sẽ giữ nguyên poster cũ
                $video_path, // Nếu video_path trống, Movie::updateMovie sẽ giữ nguyên video cũ
                $trailer_url,
                $is_premium,
                $is_series
            );

            if ($result) {
                // Nếu là phim đơn, xử lý các liên kết nhúng và liên kết trực tiếp
                if ($is_series == 0) {
                    $embed_url = $_POST['embed_url'] ?? '';
                    $direct_url = $_POST['direct_url'] ?? '';
                    $hls_url = $_POST['hls_url'] ?? '';

                    // Cập nhật thông tin phim với các liên kết
                    $this->movie->updateMovieLinks($movie_id, $embed_url, $direct_url, $hls_url);
                }

                // Chuyển hướng đến trang danh sách phim
                header('Location: /admin-panel?page=movies&updated=1');
                exit();
            } else {
                // Có lỗi khi cập nhật phim
                header('Location: /admin-panel?page=edit-movie&id=' . $movie_id . '&error=1');
                exit();
            }
        }
    }

    public function deleteMovie()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['movie_id'])) {
            $movie_id = $_POST['movie_id'];

            if ($this->movie->deleteMovie($movie_id)) {
                header('Location: /admin-panel?page=movies&deleted=1');
                exit();
            } else {
                header('Location: /admin-panel?page=movies&error=1');
                exit();
            }
        }
    }

    public function addEpisode()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // TODO: Xử lý thêm tập phim
        }
    }

    public function updateEpisode()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // TODO: Xử lý cập nhật tập phim
        }
    }

    public function deleteEpisode()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // TODO: Xử lý xóa tập phim
        }
    }
}
?>