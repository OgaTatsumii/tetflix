<?php
class Movie
{
    private $conn;
    private $table_name = "movies";
    private $episodes_table = "episodes";

    public $id;
    public $title;
    public $description;
    public $release_year;
    public $genre;
    public $duration;
    public $poster_path;
    public $video_path;
    public $trailer_url;
    public $is_premium;
    public $is_series;
    public $created_at;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Intentionally vulnerable to Blind SQL Injection
    public function search($keyword)
    {
        // WARNING: This is intentionally vulnerable to SQL injection for educational purposes
        // DO NOT use this in production!
        $query = "SELECT * FROM " . $this->table_name . " WHERE title LIKE '%" . $keyword . "%' OR description LIKE '%" . $keyword . "%'";

        // For blind SQL injection testing, we'll add a sleep if the condition is true
        // Example payload: test' AND IF(SUBSTRING((SELECT password FROM users WHERE username='admin'),1,1)='a',SLEEP(2),0)-- -

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Sửa lỗ hổng IDOR
    public function getMovieById($id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt;
    }

    // Sửa lỗ hổng Path Traversal
    public function getMoviePoster($id)
    {
        $query = "SELECT poster_path FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['poster_path'];
    }

    public function getLatestMovies($limit = 10)
    {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY created_at DESC LIMIT ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }

    public function getMoviesByGenre($genre)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE genre = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $genre);
        $stmt->execute();
        return $stmt;
    }

    public function getMovieCount()
    {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    public function getAllMoviesWithFilters($genreFilter = '', $yearFilter = '', $statusFilter = '', $searchTerm = '')
    {
        // WARNING: This is intentionally vulnerable to SQL injection for educational purposes
        // DO NOT use this in production!

        $query = "SELECT * FROM " . $this->table_name . " WHERE 1=1";

        if (!empty($genreFilter)) {
            $query .= " AND genre = '" . $genreFilter . "'";
        }

        if (!empty($yearFilter)) {
            $query .= " AND release_year = '" . $yearFilter . "'";
        }

        if ($statusFilter !== '') {
            $query .= " AND is_premium = '" . $statusFilter . "'";
        }

        if (!empty($searchTerm)) {
            $query .= " AND (title LIKE '%" . $searchTerm . "%' OR description LIKE '%" . $searchTerm . "%')";
        }

        $query .= " ORDER BY id DESC";

        // Example of Blind SQL Injection vulnerability:
        // searchTerm = test' AND (SELECT CASE WHEN (SELECT password FROM users WHERE username='admin' AND LENGTH(password)>5) THEN pg_sleep(5) ELSE pg_sleep(0) END)--

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function getUniqueGenres()
    {
        $query = "SELECT DISTINCT genre FROM " . $this->table_name . " WHERE genre IS NOT NULL ORDER BY genre";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function getUniqueYears()
    {
        $query = "SELECT DISTINCT release_year FROM " . $this->table_name . " WHERE release_year IS NOT NULL ORDER BY release_year DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function addMovie($title, $description, $release_year, $genre, $duration, $poster_path, $video_path, $trailer_url, $is_premium, $is_series)
    {
        $query = "INSERT INTO " . $this->table_name . " (title, description, release_year, genre, duration, poster_path, video_path, trailer_url, is_premium, is_series, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $title);
        $stmt->bindParam(2, $description);
        $stmt->bindParam(3, $release_year);
        $stmt->bindParam(4, $genre);
        $stmt->bindParam(5, $duration);
        $stmt->bindParam(6, $poster_path);
        $stmt->bindParam(7, $video_path);
        $stmt->bindParam(8, $trailer_url);
        $stmt->bindParam(9, $is_premium);
        $stmt->bindParam(10, $is_series);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }

        return false;
    }

    public function updateMovie($id, $title, $description, $release_year, $genre, $duration, $poster_path, $video_path, $trailer_url, $is_premium, $is_series)
    {
        // If poster_path is empty, don't update it (keep existing)
        if (empty($poster_path)) {
            $query = "UPDATE " . $this->table_name . " SET title = ?, description = ?, release_year = ?, genre = ?, duration = ?, video_path = ?, trailer_url = ?, is_premium = ?, is_series = ? WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $title);
            $stmt->bindParam(2, $description);
            $stmt->bindParam(3, $release_year);
            $stmt->bindParam(4, $genre);
            $stmt->bindParam(5, $duration);
            $stmt->bindParam(6, $video_path);
            $stmt->bindParam(7, $trailer_url);
            $stmt->bindParam(8, $is_premium);
            $stmt->bindParam(9, $is_series);
            $stmt->bindParam(10, $id);
        } else {
            $query = "UPDATE " . $this->table_name . " SET title = ?, description = ?, release_year = ?, genre = ?, duration = ?, poster_path = ?, video_path = ?, trailer_url = ?, is_premium = ?, is_series = ? WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $title);
            $stmt->bindParam(2, $description);
            $stmt->bindParam(3, $release_year);
            $stmt->bindParam(4, $genre);
            $stmt->bindParam(5, $duration);
            $stmt->bindParam(6, $poster_path);
            $stmt->bindParam(7, $video_path);
            $stmt->bindParam(8, $trailer_url);
            $stmt->bindParam(9, $is_premium);
            $stmt->bindParam(10, $is_series);
            $stmt->bindParam(11, $id);
        }

        return $stmt->execute();
    }

    public function deleteMovie($id)
    {
        // First delete all episodes for this movie (if it's a series)
        $query = "DELETE FROM " . $this->episodes_table . " WHERE movie_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();

        // Then delete the movie
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        return $stmt->execute();
    }

    // Episode functions
    public function getEpisodesByMovieId($movie_id)
    {
        $query = "SELECT * FROM " . $this->episodes_table . " WHERE movie_id = ? ORDER BY episode_number";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $movie_id);

        try {
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            // Table might not exist yet
            return [];
        }
    }

    public function getEpisodeById($id)
    {
        $query = "SELECT * FROM " . $this->episodes_table . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt;
    }

    public function getEpisodeByNumber($movie_id, $episode_number)
    {
        $query = "SELECT * FROM " . $this->episodes_table . " WHERE movie_id = ? AND episode_number = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $movie_id);
        $stmt->bindParam(2, $episode_number);
        $stmt->execute();
        return $stmt;
    }

    public function addEpisode($movie_id, $episode_number, $title, $description, $duration, $video_path, $embed_url, $direct_url)
    {
        $query = "INSERT INTO " . $this->episodes_table . " (movie_id, episode_number, title, description, duration, video_path, embed_url, direct_url, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $movie_id);
        $stmt->bindParam(2, $episode_number);
        $stmt->bindParam(3, $title);
        $stmt->bindParam(4, $description);
        $stmt->bindParam(5, $duration);
        $stmt->bindParam(6, $video_path);
        $stmt->bindParam(7, $embed_url);
        $stmt->bindParam(8, $direct_url);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }

        return false;
    }

    public function updateEpisode($id, $episode_number, $title, $description, $duration, $video_path, $embed_url, $direct_url)
    {
        // If video_path is empty, don't update it (keep existing)
        if (empty($video_path)) {
            $query = "UPDATE " . $this->episodes_table . " SET episode_number = ?, title = ?, description = ?, duration = ?, embed_url = ?, direct_url = ? WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $episode_number);
            $stmt->bindParam(2, $title);
            $stmt->bindParam(3, $description);
            $stmt->bindParam(4, $duration);
            $stmt->bindParam(5, $embed_url);
            $stmt->bindParam(6, $direct_url);
            $stmt->bindParam(7, $id);
        } else {
            $query = "UPDATE " . $this->episodes_table . " SET episode_number = ?, title = ?, description = ?, duration = ?, video_path = ?, embed_url = ?, direct_url = ? WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $episode_number);
            $stmt->bindParam(2, $title);
            $stmt->bindParam(3, $description);
            $stmt->bindParam(4, $duration);
            $stmt->bindParam(5, $video_path);
            $stmt->bindParam(6, $embed_url);
            $stmt->bindParam(7, $direct_url);
            $stmt->bindParam(8, $id);
        }

        return $stmt->execute();
    }

    public function deleteEpisode($id)
    {
        $query = "DELETE FROM " . $this->episodes_table . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        return $stmt->execute();
    }

    public function batchAddEpisodes($movie_id, $start_episode, $count, $url_pattern, $title_pattern, $duration = 45, $is_embed = false)
    {
        $success = 0;

        for ($i = 0; $i < $count; $i++) {
            $episode_number = $start_episode + $i;
            $title = str_replace('{number}', $episode_number, $title_pattern);
            $url = str_replace('{number}', $episode_number, $url_pattern);

            if ($is_embed) {
                $embed_url = $url;
                $direct_url = '';
            } else {
                $embed_url = '';
                $direct_url = $url;
            }

            if ($this->addEpisode($movie_id, $episode_number, $title, '', $duration, '', $embed_url, $direct_url)) {
                $success++;
            }
        }

        return $success;
    }

    public function updateMovieLinks($id, $embed_url = '', $direct_url = '', $hls_url = '')
    {
        $query = "UPDATE " . $this->table_name . " SET embed_url = ?, direct_url = ?, hls_url = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $embed_url);
        $stmt->bindParam(2, $direct_url);
        $stmt->bindParam(3, $hls_url);
        $stmt->bindParam(4, $id);
        return $stmt->execute();
    }
}
?>