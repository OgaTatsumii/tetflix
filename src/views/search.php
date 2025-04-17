<?php
require_once '../controllers/MovieController.php';

$movieController = new MovieController();
$movies = null;
$keyword = '';

if (isset($_GET['keyword'])) {
    $keyword = $_GET['keyword'];
    $movies = $movieController->searchMovies();
}
?>

<div class="search-container">
    <div class="row">
        <div class="col-12">
            <form action="/search.php" method="GET" class="search-form mb-4">
                <div class="input-group">
                    <input type="text" class="form-control" name="keyword"
                        value="<?php echo htmlspecialchars($keyword); ?>" placeholder="Search movies...">
                    <button class="btn btn-danger" type="submit">Search</button>
                </div>
            </form>
        </div>
    </div>

    <?php if ($movies): ?>
        <div class="row">
            <div class="col-12">
                <h2>Search Results for "<?php echo htmlspecialchars($keyword); ?>"</h2>
            </div>
        </div>

        <div class="row">
            <?php while ($movie = $movies->fetch(PDO::FETCH_ASSOC)): ?>
                <div class="col-md-3 mb-4">
                    <div class="movie-card">
                        <img src="<?php echo $movie['poster_path']; ?>" alt="<?php echo $movie['title']; ?>"
                            class="movie-poster">
                        <div class="movie-info">
                            <h3><?php echo $movie['title']; ?></h3>
                            <p><?php echo $movie['release_year']; ?></p>
                            <a href="/movie.php?id=<?php echo $movie['id']; ?>" class="btn btn-sm btn-danger">View Details</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php elseif ($keyword): ?>
        <div class="row">
            <div class="col-12">
                <div class="alert alert-info">No movies found for "<?php echo htmlspecialchars($keyword); ?>"</div>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
    .search-container {
        padding: 40px 0;
    }

    .search-form {
        max-width: 600px;
        margin: 0 auto;
    }

    .search-form .form-control {
        background-color: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: white;
    }

    .search-form .form-control:focus {
        background-color: rgba(255, 255, 255, 0.15);
        border-color: rgba(255, 255, 255, 0.3);
        color: white;
        box-shadow: none;
    }

    .movie-card {
        position: relative;
        transition: transform 0.3s;
    }

    .movie-card:hover {
        transform: scale(1.05);
    }

    .movie-poster {
        width: 100%;
        height: 300px;
        object-fit: cover;
        border-radius: 4px;
    }

    .movie-info {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        padding: 10px;
        background: linear-gradient(transparent, rgba(0, 0, 0, 0.9));
        border-radius: 0 0 4px 4px;
    }

    .movie-info h3 {
        margin: 0;
        font-size: 16px;
        font-weight: bold;
    }

    .movie-info p {
        margin: 5px 0;
        font-size: 14px;
        opacity: 0.8;
    }
</style>

<script>
    // Intentionally vulnerable to XSS
    document.querySelector('.search-form').addEventListener('submit', function (e) {
        const keyword = document.querySelector('input[name="keyword"]').value;
        localStorage.setItem('lastSearch', keyword);
    });
</script>