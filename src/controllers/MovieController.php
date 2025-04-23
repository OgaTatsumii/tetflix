<?php
require_once '../config/database.php';
require_once '../models/Movie.php';

class MovieController
{
    private $db;
    private $movie;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->movie = new Movie($this->db);
    }

    // Get unique genres for filter
    public function getUniqueGenres()
    {
        return $this->movie->getUniqueGenres();
    }

    // Get unique years for filter
    public function getUniqueYears()
    {
        return $this->movie->getUniqueYears();
    }

    // Get movies with filters
    public function getAllMoviesWithFilters($genre = '', $year = '', $status = '', $keyword = '')
    {
        return $this->movie->getAllMoviesWithFilters($genre, $year, $status, $keyword);
    }

    // Intentionally vulnerable to SQL injection
    public function searchMovies()
    {
        if (isset($_GET['keyword'])) {
            $keyword = $_GET['keyword'];
            return $this->movie->search($keyword);
        }
        return null;
    }

    // Intentionally vulnerable to IDOR
    public function getMovieDetails($id)
    {
        return $this->movie->getMovieById($id);
    }

    // Sửa lỗ hổng Path Traversal
    public function getMoviePoster($id)
    {
        $poster_path = $this->movie->getMoviePoster($id);
        if ($poster_path) {
            // Loại bỏ các ký tự nguy hiểm như ../ để tránh Path Traversal
            $safe_path = basename($poster_path);
            $file_path = $_SERVER['DOCUMENT_ROOT'] . '/uploads/' . $safe_path;
            if (file_exists($file_path)) {
                return $file_path;
            }
        }
        return null;
    }

    // Sửa lỗ hổng SSRF
    public function fetchExternalPoster($url)
    {
        // Xác thực URL để ngăn SSRF
        $allowed_domains = [
            'image.tmdb.org',
            'themoviedb.org',
            'imdb.com',
            'ia.media-imdb.com',
            'm.media-amazon.com'
        ];

        $parsed_url = parse_url($url);
        if (!isset($parsed_url['host']) || !in_array($parsed_url['host'], $allowed_domains)) {
            return null; // URL không được phép
        }

        // Giới hạn protocol https để đảm bảo an toàn
        if (!isset($parsed_url['scheme']) || $parsed_url['scheme'] !== 'https') {
            return null;
        }

        // Chỉ định rõ context options để tăng tính an toàn
        $context_options = [
            'http' => [
                'method' => 'GET',
                'timeout' => 10,
                'header' => 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
            ],
            'ssl' => [
                'verify_peer' => true,
                'verify_peer_name' => true
            ]
        ];

        $context = stream_context_create($context_options);

        // Hạn chế kích thước tải xuống
        try {
            $image_data = file_get_contents($url, false, $context, 0, 5000000); // 5MB limit
            if ($image_data === false) {
                return null;
            }

            // Xác thực là file ảnh thực sự
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->buffer($image_data);

            $allowed_mimes = ['image/jpeg', 'image/png', 'image/gif'];
            if (!in_array($mime, $allowed_mimes)) {
                return null;
            }

            // Tạo tên file an toàn
            $filename = uniqid() . '.' . pathinfo(basename(parse_url($url, PHP_URL_PATH)), PATHINFO_EXTENSION);
            $file_path = $_SERVER['DOCUMENT_ROOT'] . '/uploads/' . $filename;
            file_put_contents($file_path, $image_data);
            return $filename;
        } catch (Exception $e) {
            error_log('Error fetching external image: ' . $e->getMessage());
            return null;
        }
    }

    public function getLatestMovies($limit = 10)
    {
        return $this->movie->getLatestMovies($limit);
    }

    public function getMoviesByGenre($genre)
    {
        return $this->movie->getMoviesByGenre($genre);
    }

    // Thêm phương thức uploadPoster để gửi hình ảnh lên FastAPI service
    public function uploadPoster($file)
    {
        // Trong Docker, container web phải gọi đến container image-service qua tên service
        $image_service_url = 'http://image-service:8000';

        // Tạo CURL request để gửi file lên FastAPI
        $ch = curl_init();
        $cfile = new CURLFile($file['tmp_name'], $file['type'], $file['name']);

        curl_setopt($ch, CURLOPT_URL, $image_service_url . '/upload');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, ['file' => $cfile]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); // Tăng timeout lên 30 giây

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);

        // Debug thông tin
        error_log("CURL HTTP Code: " . $http_code);
        error_log("CURL Error: " . $curl_error);
        error_log("CURL Response: " . $response);

        if ($http_code === 200) {
            $result = json_decode($response, true);
            if ($result && isset($result['filename'])) {
                // URL cho phía client web (browser) phải sử dụng localhost
                $poster_url = 'http://localhost:8000/poster/' . $result['filename'];
                error_log("Poster URL: " . $poster_url);
                return $poster_url;
            } else {
                error_log("Invalid response format: " . $response);
            }
        }

        return false;
    }

    // Lấy danh sách tập của một phim
    public function getEpisodesByMovieId($movie_id)
    {
        return $this->movie->getEpisodesByMovieId($movie_id);
    }
}
?>