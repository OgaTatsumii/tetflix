<?php
require_once '../controllers/MovieController.php';
require_once '../controllers/CommentController.php';
require_once 'components/alert.php';

$movieController = new MovieController();
$commentController = new CommentController();

// Lấy ID phim từ URL
$movie_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($movie_id <= 0) {
    // Nếu không có ID hợp lệ, chuyển hướng về trang chủ
    header('Location: /');
    exit;
}

$movie_result = $movieController->getMovieDetails($movie_id);
$movie = $movie_result->fetch(PDO::FETCH_ASSOC);

// Nếu không tìm thấy phim, chuyển hướng về trang chủ
if (!$movie) {
    header('Location: /');
    exit;
}

// Lấy danh sách các tập nếu là phim bộ
$episodes = null;
if ($movie['is_series']) {
    // Sử dụng MovieController để lấy danh sách tập
    $episodes = $movieController->getEpisodesByMovieId($movie_id);
}

// Lấy phim cùng thể loại
$related_movies = $movieController->getMoviesByGenre($movie['genre']);

// Lấy danh sách bình luận
$comments_stmt = $commentController->getMovieComments($movie_id);

ob_start();
?>

<?php showAlert(); ?>

<div class="movie-detail-container">
    <div class="movie-backdrop" style="background-image: url('<?php echo htmlspecialchars($movie['poster_path']); ?>')">
        <div class="backdrop-overlay"></div>
    </div>

    <div class="container movie-content">
        <div class="movie-poster-container">
            <img src="<?php echo htmlspecialchars($movie['poster_path']); ?>"
                alt="<?php echo htmlspecialchars($movie['title']); ?>" class="movie-poster"
                onerror="this.src='/assets/images/default-poster.jpg'">
            <?php if ($movie['is_premium']): ?>
            <span class="premium-badge">Premium</span>
            <?php endif; ?>
        </div>

        <div class="movie-info">
            <h1><?php echo htmlspecialchars($movie['title']); ?></h1>

            <div class="movie-meta">
                <span class="year"><?php echo $movie['release_year']; ?></span>
                <span class="dot"></span>
                <span class="duration"><?php echo $movie['duration']; ?> phút</span>
                <span class="dot"></span>
                <span class="genre"><?php echo htmlspecialchars($movie['genre']); ?></span>
                <span class="dot"></span>
                <span class="type"><?php echo $movie['is_series'] ? 'Phim bộ' : 'Phim lẻ'; ?></span>
            </div>

            <div class="rating">
                <div class="stars">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star-half-alt"></i>
                </div>
                <span>4.5/5</span>
            </div>

            <div class="description">
                <?php echo nl2br(htmlspecialchars($movie['description'])); ?>
            </div>

            <div class="action-buttons">
                <?php if ($movie['is_premium'] && (!isset($_SESSION['is_premium']) || !$_SESSION['is_premium'])): ?>
                <a href="/premium" class="btn btn-premium">
                    <i class="fas fa-crown"></i> Nâng cấp Premium để xem
                </a>
                <?php else: ?>
                <a href="/watch?id=<?php echo $movie['id']; ?>" class="btn btn-danger">
                    <i class="fas fa-play"></i> Xem phim
                </a>
                <?php if ($movie['trailer_url']): ?>
                <a href="<?php echo htmlspecialchars($movie['trailer_url']); ?>"
                    class="btn btn-outline-light trailer-btn">
                    <i class="fas fa-film"></i> Xem trailer
                </a>
                <?php endif; ?>
                <?php endif; ?>
                <button class="btn btn-outline-light add-favorite">
                    <i class="far fa-heart"></i> Yêu thích
                </button>
            </div>
        </div>
    </div>

    <?php if ($movie['is_series'] && $episodes && $episodes->rowCount() > 0): ?>
    <div class="container">
        <div class="episodes-section">
            <h2 class="section-title">Danh sách tập</h2>
            <div class="episodes-list">
                <?php while ($episode = $episodes->fetch(PDO::FETCH_ASSOC)): ?>
                <div class="episode-item">
                    <div class="episode-number"><?php echo $episode['episode_number']; ?></div>
                    <div class="episode-info">
                        <h3><?php echo htmlspecialchars($episode['title']); ?></h3>
                        <p class="episode-duration"><?php echo $episode['duration']; ?> phút</p>
                    </div>
                    <a href="/watch?id=<?php echo $movie['id']; ?>&episode=<?php echo $episode['episode_number']; ?>"
                        class="btn btn-sm btn-danger">
                        <i class="fas fa-play"></i>
                    </a>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Comments Section -->
    <div class="container">
        <div class="comments-section">
            <h2 class="section-title">Bình luận</h2>

            <!-- Comment Form -->
            <?php if (isset($_SESSION['user_id'])): ?>
            <div class="comment-form">
                <form action="/comment" method="POST">
                    <input type="hidden" name="action" value="add_comment">
                    <input type="hidden" name="movie_id" value="<?php echo $movie_id; ?>">
                    <textarea name="comment" class="form-control" rows="3" placeholder="Viết bình luận của bạn..."
                        required></textarea>
                    <button type="submit" class="btn btn-danger mt-2">Gửi bình luận</button>
                </form>
            </div>
            <?php else: ?>
            <div class="login-prompt text-center my-3">
                <a href="/login?redirect=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>" class="btn btn-danger">
                    <i class="fas fa-sign-in-alt"></i> Đăng nhập để bình luận
                </a>
            </div>
            <?php endif; ?>

            <!-- Comments List -->
            <div class="comments-list mt-4">
                <?php if ($comments_stmt->rowCount() > 0): ?>
                <?php while ($comment = $comments_stmt->fetch(PDO::FETCH_ASSOC)): ?>
                <div class="comment-card">
                    <div class="comment-header">
                        <span class="comment-author"><?php echo htmlspecialchars($comment['username']); ?></span>
                        <span
                            class="comment-date"><?php echo date('d/m/Y H:i', strtotime($comment['created_at'])); ?></span>
                    </div>
                    <div class="comment-body">
                        <?php echo nl2br(htmlspecialchars($comment['comment'])); ?>
                    </div>
                    <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $comment['user_id']): ?>
                    <div class="comment-actions">
                        <form action="/comment" method="POST" style="display:inline;">
                            <input type="hidden" name="action" value="delete_comment">
                            <input type="hidden" name="comment_id" value="<?php echo $comment['id']; ?>">
                            <button type="submit" class="btn btn-sm btn-outline-light"
                                onclick="return confirm('Bạn có chắc muốn xóa bình luận này?');">Xóa</button>
                        </form>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endwhile; ?>
                <?php else: ?>
                <p>Chưa có bình luận nào.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="related-section">
            <h2 class="section-title">Phim tương tự</h2>
            <div class="movie-grid">
                <?php
                $count = 0;
                while ($related = $related_movies->fetch(PDO::FETCH_ASSOC)):
                    // Bỏ qua phim hiện tại và chỉ hiển thị tối đa 6 phim
                    if ($related['id'] == $movie_id || $count >= 6)
                        continue;
                    $count++;
                    ?>
                <div class="movie-card">
                    <a href="/movie?id=<?php echo $related['id']; ?>">
                        <img src="<?php echo htmlspecialchars($related['poster_path']); ?>"
                            alt="<?php echo htmlspecialchars($related['title']); ?>" class="movie-poster"
                            onerror="this.src='/assets/images/default-poster.jpg'">
                        <div class="movie-info">
                            <h3><?php echo htmlspecialchars($related['title']); ?></h3>
                            <p><?php echo $related['release_year']; ?></p>
                        </div>
                    </a>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</div>

<style>
.comments-list {
    margin-top: 30px;
}

.movie-detail-container {
    padding-top: 0;
    position: relative;
    z-index: 1;
}

.movie-backdrop {
    position: relative;
    filter: blur(5px);
    height: 70vh;
    background-size: cover;
    background-position: center top;
    margin-top: -5rem;
}

.backdrop-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(to bottom, rgba(20, 20, 20, 0.5), var(--netflix-black));
}

.movie-content {
    display: flex;
    position: relative;
    margin-top: -250px;
    z-index: 2;
}

.movie-poster-container {
    position: relative;
    flex-shrink: 0;
    width: 300px;
    margin-right: 2rem;
}

.movie-poster {
    width: 100%;
    height: 450px;
    object-fit: cover;
    border-radius: 8px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
}

.premium-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    background: gold;
    color: black;
    font-weight: bold;
    padding: 5px 10px;
    border-radius: 3px;
    font-size: 0.8rem;
}

.movie-info {
    flex: 1;
    color: #fff;
}

.movie-info h1 {
    font-size: 2.5rem;
    margin-bottom: 1rem;
}

.movie-meta {
    display: flex;
    align-items: center;
    margin-bottom: 1.5rem;
    font-size: 1rem;
}

.dot {
    width: 4px;
    height: 4px;
    background: rgba(255, 255, 255, 0.5);
    border-radius: 50%;
    margin: 0 10px;
}

.rating {
    display: flex;
    align-items: center;
    margin-bottom: 1.5rem;
}

.stars {
    color: gold;
    margin-right: 10px;
}

.description {
    font-size: 1.1rem;
    line-height: 1.6;
    margin-bottom: 2rem;
    max-width: 800px;
    color: rgba(255, 255, 255, 0.8);
}

.action-buttons {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.btn-premium {
    background-color: gold;
    color: black;
}

.btn-premium:hover {
    background-color: #ffd700;
}

.section-title {
    font-size: 1.8rem;
    margin: 3rem 0 1.5rem;
}

.episodes-list {
    margin-bottom: 3rem;
}

.episode-item {
    display: flex;
    align-items: center;
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 0.5rem;
    background: rgba(255, 255, 255, 0.05);
    transition: background 0.3s ease;
}

.episode-item:hover {
    background: rgba(255, 255, 255, 0.1);
}

.episode-number {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--netflix-red);
    color: white;
    border-radius: 50%;
    font-weight: bold;
    margin-right: 1rem;
}

.episode-info {
    flex: 1;
}

.episode-info h3 {
    margin: 0 0 0.2rem;
    font-size: 1.1rem;
}

.episode-duration {
    margin: 0;
    font-size: 0.9rem;
    color: rgba(255, 255, 255, 0.6);
}

.related-section {
    margin-bottom: 4rem;
}

.comments-section {
    margin-top: 3rem;
    margin-bottom: 3rem;
    padding: 2rem;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 8px;
}

.comment-form textarea {
    background-color: #333;
    color: #fff;
    border: 1px solid #555;
    margin-bottom: 0.5rem;
}

.comment-form textarea:focus {
    border-color: var(--netflix-red);
    box-shadow: 0 0 0 0.2rem rgba(229, 9, 20, 0.25);
    background-color: #444;
}

.comment-card {
    background: rgba(255, 255, 255, 0.08);
    padding: 1rem 1.5rem;
    border-radius: 5px;
    margin-bottom: 1rem;
}

.comment-header {
    display: flex;
    justify-content: space-between;
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
}

.comment-author {
    font-weight: bold;
    color: var(--netflix-red);
}

.comment-date {
    color: rgba(255, 255, 255, 0.6);
}

.comment-body {
    line-height: 1.5;
    margin-bottom: 0.5rem;
}

.comment-actions {
    text-align: right;
}

.alert-container {
    position: fixed;
    top: 100px;
}

.alert {
    padding: 1rem;
    margin-bottom: 1rem;
    border-radius: 4px;
    color: #fff;
    display: inline-block;
    min-width: 300px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    opacity: 0.95;
}

.alert-success {
    background-color: #28a745;
    border: 1px solid #1e7e34;
}

.alert-danger {
    background-color: var(--netflix-red);
    border: 1px solid #bd2130;
}

@media (max-width: 992px) {
    .movie-content {
        flex-direction: column;
        align-items: center;
    }

    .movie-poster-container {
        margin-right: 0;
        margin-bottom: 2rem;
        width: 230px;
    }

    .movie-info {
        text-align: center;
    }

    .action-buttons {
        justify-content: center;
    }
}

@media (max-width: 768px) {
    .movie-backdrop {
        height: 50vh;
    }

    .movie-content {
        margin-top: -150px;
    }

    .movie-info h1 {
        font-size: 2rem;
    }

    .description {
        font-size: 1rem;
    }


}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tự động ẩn alerts sau 3 giây
    const alerts = document.querySelectorAll('.auto-dismiss');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s ease';
            alert.style.opacity = '0';
            setTimeout(() => {
                alert.style.display = 'none';
            }, 500);
        }, 3000);
    });

    // Xử lý nút yêu thích
    const favoriteBtn = document.querySelector('.add-favorite');
    if (favoriteBtn) {
        favoriteBtn.addEventListener('click', function() {
            const icon = this.querySelector('i');
            if (icon.classList.contains('far')) {
                icon.classList.remove('far');
                icon.classList.add('fas');
                icon.style.color = 'var(--netflix-red)';
            } else {
                icon.classList.remove('fas');
                icon.classList.add('far');
                icon.style.color = '';
            }
        });
    }

    // Mở trailer trong lightbox nếu có
    const trailerBtn = document.querySelector('.trailer-btn');
    if (trailerBtn) {
        trailerBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const trailerUrl = this.getAttribute('href');

            // Tạo lightbox
            const lightbox = document.createElement('div');
            lightbox.className = 'trailer-lightbox';

            // Tạo iframe cho video
            let videoHtml = '';

            // Kiểm tra loại URL (YouTube, Vimeo, etc)
            if (trailerUrl.includes('youtube.com') || trailerUrl.includes('youtu.be')) {
                // Xử lý URL YouTube
                let youtubeId = '';
                if (trailerUrl.includes('watch?v=')) {
                    youtubeId = trailerUrl.split('watch?v=')[1].split('&')[0];
                } else if (trailerUrl.includes('youtu.be/')) {
                    youtubeId = trailerUrl.split('youtu.be/')[1];
                }
                videoHtml =
                    `<iframe width="100%" height="100%" src="https://www.youtube.com/embed/${youtubeId}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>`;
            } else {
                // URL video khác, dùng thẻ video
                videoHtml =
                    `<video width="100%" height="100%" controls autoplay><source src="${trailerUrl}" type="video/mp4">Your browser does not support the video tag.</video>`;
            }

            lightbox.innerHTML = `
                <div class="trailer-container">
                    <button class="close-trailer">&times;</button>
                    <div class="trailer-video">
                        ${videoHtml}
                    </div>
                </div>
            `;

            document.body.appendChild(lightbox);
            document.body.style.overflow = 'hidden';

            // Đóng lightbox
            lightbox.querySelector('.close-trailer').addEventListener('click', function() {
                document.body.removeChild(lightbox);
                document.body.style.overflow = '';
            });

            // Đóng lightbox khi click bên ngoài
            lightbox.addEventListener('click', function(e) {
                if (e.target === lightbox) {
                    document.body.removeChild(lightbox);
                    document.body.style.overflow = '';
                }
            });
        });
    }
});
</script>

<style>
.trailer-lightbox {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.9);
    z-index: 1000;
    display: flex;
    align-items: center;
    justify-content: center;
}

.trailer-container {
    position: relative;
    width: 90%;
    max-width: 900px;
    height: 0;
    padding-bottom: 56.25%;
    /* 16:9 Aspect Ratio */
}

.trailer-video {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
}

.close-trailer {
    position: absolute;
    top: -40px;
    right: 0;
    background: none;
    border: none;
    color: white;
    font-size: 2rem;
    cursor: pointer;
    z-index: 1001;
}
</style>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/layout.php';
?>