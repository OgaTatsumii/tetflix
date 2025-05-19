<?php
require_once __DIR__ . '/../controllers/MovieController.php';

$movieController = new MovieController();
$movies = null;
$keyword = '';
$genre = '';
$year = '';
$status = '';

// Get all available genres and years for filters
$genres = $movieController->getUniqueGenres();
$years = $movieController->getUniqueYears();

if (isset($_GET['keyword']) || isset($_GET['genre']) || isset($_GET['year']) || isset($_GET['status'])) {
    $keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
    $genre = isset($_GET['genre']) ? $_GET['genre'] : '';
    $year = isset($_GET['year']) ? $_GET['year'] : '';
    $status = isset($_GET['status']) ? $_GET['status'] : '';
    
    $movies = $movieController->getAllMoviesWithFilters($genre, $year, $status, $keyword);
}

ob_start();
?>

<style>
.search-page-content {
    padding-top: 2rem;
    min-height: calc(100vh - 200px);
}

.search-page-title {
    font-size: 2rem;
    font-weight: bold;
    margin-bottom: 1.5rem;
}

.search-filter-container {
    background: rgba(0, 0, 0, 0.4);
    border-radius: 4px;
    padding: 20px;
    margin-bottom: 30px;
}

.search-page-input {
    background: #2b2b2b;
    border: 1px solid #404040;
    color: white;
    padding: 10px 15px;
    border-radius: 4px;
    width: 100%;
    margin-bottom: 15px;
}

.search-page-input:focus {
    outline: none;
    border-color: #e50914;
    box-shadow: 0 0 0 2px rgba(229, 9, 20, 0.2);
}

.search-filter-group {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
}

.search-filter-select {
    background: #2b2b2b;
    border: 1px solid #404040;
    color: white;
    padding: 8px 15px;
    border-radius: 4px;
    min-width: 150px;
}

.search-filter-select:focus {
    outline: none;
    border-color: #e50914;
}

.search-submit-btn {
    background: #e50914;
    color: white;
    border: none;
    padding: 8px 20px;
    border-radius: 4px;
    cursor: pointer;
    transition: background 0.3s;
}

.search-submit-btn:hover {
    background: #b2070e;
}

.search-results-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 20px;
    padding: 20px 0;
}

.search-movie-card {
    background: #2b2b2b;
    border-radius: 4px;
    overflow: hidden;
    transition: transform 0.3s;
}

.search-movie-card:hover {
    transform: scale(1.05);
}

.search-movie-poster {
    width: 100%;
    aspect-ratio: 2/3;
    object-fit: cover;
}

.search-movie-info {
    padding: 15px;
}

.search-movie-title {
    font-size: 1rem;
    font-weight: bold;
    margin: 0 0 10px 0;
}

.search-movie-meta {
    font-size: 0.9rem;
    color: #999;
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.search-premium-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    background: #ffd700;
    color: black;
    padding: 3px 8px;
    border-radius: 3px;
    font-size: 0.8rem;
    font-weight: bold;
}

.search-no-results {
    text-align: center;
    padding: 30px;
    background: rgba(0, 0, 0, 0.4);
    border-radius: 4px;
    margin-top: 20px;
    color: #f44336;
    font-size: 18px;
    font-weight: 500;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    border-left: 4px solid #f44336;
}

.search-no-results i {
    margin-right: 10px;
    font-size: 20px;
}

.search-found {
    text-align: center;
    padding: 30px;
    background: rgba(0, 0, 0, 0.4);
    border-radius: 4px;
    margin-top: 20px;
    color: #4CAF50;
    font-size: 18px;
    font-weight: 500;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    border-left: 4px solid #4CAF50;
}

.search-found i {
    margin-right: 10px;
    font-size: 20px;
}
</style>

<div class="search-page-content container">
    <h1 class="search-page-title">Search Movies</h1>

    <div class="search-filter-container">
        <input type="text" id="searchInput" class="search-page-input" placeholder="Search movies..."
            value="<?php echo htmlspecialchars($keyword); ?>">

        <div class="search-filter-group">
            <select id="genreFilter" class="search-filter-select">
                <option value="">All Genres</option>
                <?php while ($genre_row = $genres->fetch(PDO::FETCH_ASSOC)): ?>
                <option value="<?php echo htmlspecialchars($genre_row['genre']); ?>"
                    <?php echo $genre === $genre_row['genre'] ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($genre_row['genre']); ?>
                </option>
                <?php endwhile; ?>
            </select>

            <select id="yearFilter" class="search-filter-select">
                <option value="">All Years</option>
                <?php while ($year_row = $years->fetch(PDO::FETCH_ASSOC)): ?>
                <option value="<?php echo $year_row['release_year']; ?>"
                    <?php echo $year == $year_row['release_year'] ? 'selected' : ''; ?>>
                    <?php echo $year_row['release_year']; ?>
                </option>
                <?php endwhile; ?>
            </select>

            <select id="statusFilter" class="search-filter-select">
                <option value="">All Status</option>
                <option value="1" <?php echo $status === '1' ? 'selected' : ''; ?>>Premium</option>
                <option value="0" <?php echo $status === '0' ? 'selected' : ''; ?>>Free</option>
            </select>

            <button onclick="search()" class="search-submit-btn">
                <i class="fas fa-search"></i> Search
            </button>
        </div>
    </div>

    <!-- Common SQLi -- -->
    <!-- <?php if ($movies): ?>
    <div class="search-results-grid">
        <?php 
            $count = 0;
            while ($movie = $movies->fetch(PDO::FETCH_ASSOC)):
                $count++;
            ?>
        <div class="search-movie-card">
            <div style="position: relative;">
                <img src="<?php echo htmlspecialchars($movie['poster_path']); ?>"
                    alt="<?php echo htmlspecialchars($movie['title']); ?>" class="search-movie-poster">
                <?php if ($movie['is_premium']): ?>
                <div class="search-premium-badge">
                    <i class="fas fa-crown"></i> Premium
                </div>
                <?php endif; ?>
            </div>
            <div class="search-movie-info">
                <h3 class="search-movie-title"><?php echo htmlspecialchars($movie['title']); ?></h3>
                <div class="search-movie-meta">
                    <span><i class="fas fa-calendar"></i> <?php echo $movie['release_year']; ?></span>
                    <span><i class="fas fa-clock"></i> <?php echo $movie['duration']; ?>m</span>
                    <?php if ($movie['is_series']): ?>
                    <span><i class="fas fa-tv"></i> Series</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>

    <?php if ($count === 0): ?>
    <div class="search-no-results">
        <i class="fas fa-info-circle"></i> No movies found matching your criteria.
    </div>
    <?php endif; ?>
    <?php endif; ?> -->

    <!-- Boolean Based Blind SQLi -->
    <!-- <?php if ($movies): ?>
    <?php 
        // Kiểm tra xem có kết quả nào không bằng cách đếm số dòng
        $rowCount = $movies->rowCount();
        if ($rowCount > 0):
    ?>
        <div class="search-found">
            <i class="fas fa-check-circle"></i> Đã tìm thấy phim.
        </div>
    <?php else: ?>
        <div class="search-no-results">
            <i class="fas fa-info-circle"></i> Không tìm thấy phim.
        </div>
    <?php endif; ?>
<?php endif; ?> -->

    <!-- Time Based Blind SQLi -->
    <?php if ($movies): ?>
        <div class="search-found">
        <i class="fas fa-search"></i> Đã thực hiện tìm kiếm.
        </div>
        <?php endif; ?>
</div>

<script>
function search() {
    const keyword = document.getElementById('searchInput').value;
    const genre = document.getElementById('genreFilter').value;
    const year = document.getElementById('yearFilter').value;
    const status = document.getElementById('statusFilter').value;

    let url = '/search?keyword=' + encodeURIComponent(keyword);
    if (genre) url += '&genre=' + encodeURIComponent(genre);
    if (year) url += '&year=' + encodeURIComponent(year);
    if (status) url += '&status=' + encodeURIComponent(status);

    window.location.href = url;
}

document.getElementById('searchInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        search();
    }
});
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/layout.php';
?>