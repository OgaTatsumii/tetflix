<?php
require_once '../controllers/MovieController.php';
require_once 'components/alert.php';

$movieController = new MovieController();
$latest_movies = $movieController->getLatestMovies(10);
$action_movies = $movieController->getMoviesByGenre('Action');
$comedy_movies = $movieController->getMoviesByGenre('Comedy');

ob_start();
?>

<div class="container">
    <?php showAlert(); ?>

    <div class="hero">
        <div class="hero-content">
            <h1>Chào mừng đến với Netflix Clone</h1>
            <p class="hero-text">Xem phim và chương trình truyền hình không giới hạn</p>
            <a href="/register" class="btn btn-danger btn-lg">Bắt đầu ngay</a>
        </div>
    </div>

    <section class="movies-section">
        <div class="container">
            <h2 class="section-title">Phim mới cập nhật</h2>
            <div class="movie-grid">
                <?php while ($movie = $latest_movies->fetch(PDO::FETCH_ASSOC)): ?>
                <div class="movie-card">
                    <a href="/movie?id=<?php echo $movie['id']; ?>">
                        <div class="poster-wrapper">
                            <img src="<?php echo $movie['poster_path']; ?>"
                                alt="<?php echo htmlspecialchars($movie['title']); ?>" class="movie-poster"
                                onerror="this.src='/assets/images/default-poster.jpg'">
                            <?php if (!empty($movie['is_premium'])): ?>
                                <span class="premium-badge"><i class="fas fa-crown"></i> Premium</span>
                            <?php endif; ?>
                        </div>
                        <div class="movie-info">
                            <h3><?php echo htmlspecialchars($movie['title']); ?></h3>
                            <p><?php echo $movie['release_year']; ?></p>
                        </div>
                    </a>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </section>

    <section class="movies-section">
        <div class="container">
            <h2 class="section-title">Phim Hành Động</h2>
            <div class="movie-grid">
                <?php while ($movie = $action_movies->fetch(PDO::FETCH_ASSOC)): ?>
                <div class="movie-card">
                    <a href="/movie?id=<?php echo $movie['id']; ?>">
                        <div class="poster-wrapper">
                            <img src="<?php echo $movie['poster_path']; ?>"
                                alt="<?php echo htmlspecialchars($movie['title']); ?>" class="movie-poster"
                                onerror="this.src='/assets/images/default-poster.jpg'">
                            <?php if (!empty($movie['is_premium'])): ?>
                                <span class="premium-badge"><i class="fas fa-crown"></i> Premium</span>
                            <?php endif; ?>
                        </div>
                        <div class="movie-info">
                            <h3><?php echo htmlspecialchars($movie['title']); ?></h3>
                            <p><?php echo $movie['release_year']; ?></p>
                        </div>
                    </a>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </section>

    <section class="movies-section">
        <div class="container">
            <h2 class="section-title">Phim Hài</h2>
            <div class="movie-grid">
                <?php while ($movie = $comedy_movies->fetch(PDO::FETCH_ASSOC)): ?>
                <div class="movie-card">
                    <a href="/movie?id=<?php echo $movie['id']; ?>">
                        <div class="poster-wrapper">
                            <img src="<?php echo $movie['poster_path']; ?>"
                                alt="<?php echo htmlspecialchars($movie['title']); ?>" class="movie-poster"
                                onerror="this.src='/assets/images/default-poster.jpg'">
                            <?php if (!empty($movie['is_premium'])): ?>
                                <span class="premium-badge"><i class="fas fa-crown"></i> Premium</span>
                            <?php endif; ?>
                        </div>
                        <div class="movie-info">
                            <h3><?php echo htmlspecialchars($movie['title']); ?></h3>
                            <p><?php echo $movie['release_year']; ?></p>
                        </div>
                    </a>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </section>
</div>

<style>
.alert-container {
    position: fixed;
    top: 80px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 1000;
    width: 100%;
    max-width: 600px;
    text-align: center;
    padding: 0 20px;
}

.alert {
    padding: 1rem 2rem;
    margin-bottom: 1rem;
    border-radius: 8px;
    color: #fff;
    font-weight: 500;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    opacity: 0.95;
    animation: slideDown 0.5s ease-out forwards;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
}

.alert-close {
    background: none;
    border: none;
    color: white;
    font-size: 1.5rem;
    cursor: pointer;
    padding: 0;
    line-height: 1;
    opacity: 0.8;
    transition: opacity 0.2s;
}

.alert-close:hover {
    opacity: 1;
}

.alert-text {
    flex: 1;
}

.alert.hide {
    animation: slideUp 0.5s ease-out forwards;
}

@keyframes slideUp {
    from {
        transform: translateY(0);
        opacity: 0.95;
    }

    to {
        transform: translateY(-100%);
        opacity: 0;
    }
}

.alert-success {
    background-color: #28a745;
    border-left: 5px solid #1e7e34;
}

.alert-danger {
    background-color: var(--netflix-red);
    border-left: 5px solid #bd2130;
}

@keyframes slideDown {
    from {
        transform: translateY(-100%);
        opacity: 0;
    }

    to {
        transform: translateY(0);
        opacity: 0.95;
    }
}

.hero {
    position: relative;
    height: 80vh;
    background: linear-gradient(to bottom, rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.8)),
        url('/assets/images/hero-bg.jpg') no-repeat center center;
    background-size: cover;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    margin: -5rem -15px 0;
    width: 100vw;
    position: relative;
    left: 50%;
    right: 50%;
    margin-left: -50vw;
    margin-right: -50vw;
}

.hero-content {
    max-width: 1035px;
    padding: 2rem;
    color: #fff;
}

.hero h1 {
    font-size: 3.5rem;
    font-weight: 700;
    margin-bottom: 1rem;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
}

.hero-text {
    font-size: 1.5rem;
    margin-bottom: 2rem;
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
}

.movies-section {
    padding: 4rem 0;
}

.section-title {
    font-size: 2rem;
    font-weight: 600;
    margin-bottom: 2rem;
    color: #fff;
}

.movie-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 2rem;
    padding: 1rem 0;
}

.movie-card {
    position: relative;
    border-radius: 4px;
    overflow: hidden;
    transition: transform 0.3s ease;
}

.movie-card:hover {
    transform: scale(1.05);
}

.movie-poster {
    width: 100%;
    height: 300px;
    object-fit: cover;
}

.movie-info {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 1rem;
    background: linear-gradient(transparent, rgba(0, 0, 0, 0.9));
}

.movie-info h3 {
    color: #fff;
    font-size: 1rem;
    margin: 0 0 0.5rem;
}

.movie-info p {
    color: rgba(255, 255, 255, 0.7);
    font-size: 0.9rem;
    margin: 0;
}

.poster-wrapper {
    position: relative;
}

.premium-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    background: gold;
    color: black;
    font-weight: bold;
    padding: 4px 10px;
    border-radius: 4px;
    font-size: 0.85rem;
    z-index: 2;
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    display: flex;
    align-items: center;
    gap: 4px;
}

@media (max-width: 768px) {
    .hero {
        height: 60vh;
    }

    .hero h1 {
        font-size: 2.5rem;
    }

    .hero-text {
        font-size: 1.25rem;
    }

    .movie-grid {
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 1rem;
    }

    .movie-poster {
        height: 225px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert');

    // Xử lý nút đóng alert
    document.querySelectorAll('.alert-close').forEach(button => {
        button.addEventListener('click', function() {
            const alert = this.closest('.alert');
            hideAlert(alert);
        });
    });

    // Tự động ẩn alerts sau 2 giây
    alerts.forEach(alert => {
        setTimeout(() => {
            hideAlert(alert);
        }, 2000);
    });

    function hideAlert(alert) {
        alert.classList.add('hide');
        setTimeout(() => {
            if (alert.parentElement) {
                alert.parentElement.removeChild(alert);
            }
        }, 500);
    }
});
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/layout.php';
?>