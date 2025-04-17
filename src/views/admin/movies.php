<?php
// Handle filtering
$genreFilter = isset($_GET['genre']) ? $_GET['genre'] : '';
$yearFilter = isset($_GET['year']) ? $_GET['year'] : '';
$statusFilter = isset($_GET['status']) ? $_GET['status'] : '';
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

// Get all movies with filters
$movies = $movieModel->getAllMoviesWithFilters($genreFilter, $yearFilter, $statusFilter, $searchTerm);

// Get unique genres and years for filter dropdowns
$genres = $movieModel->getUniqueGenres();
$years = $movieModel->getUniqueYears();
?>

<!-- Filter and Search Section -->
<div class="card-section">
    <div class="card-header">
        <h3 class="card-title">Filter Movies</h3>
        <a href="/admin-panel?page=add-movie" class="btn btn-danger">
            <i class="fas fa-plus"></i> Add New Movie
        </a>
    </div>

    <form action="/admin-panel" method="GET" class="admin-form mt-3">
        <input type="hidden" name="page" value="movies">

        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="search">Search</label>
                <input type="text" id="search" name="search" class="form-control"
                    value="<?php echo htmlspecialchars($searchTerm); ?>" placeholder="Search by title...">
            </div>

            <div class="form-group">
                <label class="form-label" for="genre">Genre</label>
                <select id="genre" name="genre" class="form-control">
                    <option value="">All Genres</option>
                    <?php if (isset($genres) && $genres->rowCount() > 0): ?>
                    <?php while ($genre = $genres->fetch(PDO::FETCH_ASSOC)): ?>
                    <option value="<?php echo htmlspecialchars($genre['genre']); ?>"
                        <?php echo ($genreFilter === $genre['genre']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($genre['genre']); ?>
                    </option>
                    <?php endwhile; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" for="year">Release Year</label>
                <select id="year" name="year" class="form-control">
                    <option value="">All Years</option>
                    <?php if (isset($years) && $years->rowCount() > 0): ?>
                    <?php while ($year = $years->fetch(PDO::FETCH_ASSOC)): ?>
                    <option value="<?php echo htmlspecialchars($year['release_year']); ?>"
                        <?php echo ($yearFilter === $year['release_year']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($year['release_year']); ?>
                    </option>
                    <?php endwhile; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" for="status">Status</label>
                <select id="status" name="status" class="form-control">
                    <option value="">All Status</option>
                    <option value="1" <?php echo ($statusFilter === '1') ? 'selected' : ''; ?>>Premium</option>
                    <option value="0" <?php echo ($statusFilter === '0') ? 'selected' : ''; ?>>Public</option>
                </select>
            </div>

            <div class="form-group" style="display: flex; align-items: flex-end;">
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-filter"></i> Apply Filters
                </button>
                <a href="/admin-panel?page=movies" class="btn btn-outline ml-2">Clear</a>
            </div>
        </div>
    </form>
</div>

<!-- Movies List -->
<div class="card-section mt-4">
    <div class="card-header">
        <h3 class="card-title">All Movies</h3>
        <div class="header-actions">
            <button class="btn btn-outline open-modal" data-target="#import-movies">
                <i class="fas fa-file-import"></i> Import
            </button>
            <button class="btn btn-outline">
                <i class="fas fa-file-export"></i> Export
            </button>
        </div>
    </div>

    <div class="admin-table">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Poster</th>
                    <th>Title</th>
                    <th>Genre</th>
                    <th>Year</th>
                    <th>Duration</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (isset($movies) && $movies->rowCount() > 0): ?>
                <?php while ($movie = $movies->fetch(PDO::FETCH_ASSOC)): ?>
                <tr>
                    <td><?php echo $movie['id']; ?></td>
                    <td>
                        <?php if (!empty($movie['poster_path'])): ?>
                        <img src="<?php echo htmlspecialchars($movie['poster_path']); ?>" alt="Poster" width="50"
                            height="70">
                        <?php else: ?>
                        <div
                            style="width: 50px; height: 70px; background-color: #333; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-film"></i>
                        </div>
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($movie['title']); ?></td>
                    <td><?php echo htmlspecialchars($movie['genre']); ?></td>
                    <td><?php echo htmlspecialchars($movie['release_year']); ?></td>
                    <td><?php echo htmlspecialchars($movie['duration']); ?> min</td>
                    <td>
                        <?php if (isset($movie['is_series']) && $movie['is_series']): ?>
                        <span class="badge badge-info">Series</span>
                        <?php else: ?>
                        <span class="badge badge-info">Movie</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($movie['is_premium']): ?>
                        <span class="badge badge-warning">Premium</span>
                        <?php else: ?>
                        <span class="badge badge-success">Public</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <a href="/admin-panel?page=edit-movie&id=<?php echo $movie['id']; ?>"
                                class="btn-icon btn-edit" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>

                            <?php if (isset($movie['is_series']) && $movie['is_series']): ?>
                            <a href="/admin-panel?page=episodes&movie_id=<?php echo $movie['id']; ?>"
                                class="btn-icon btn-view" title="Manage Episodes">
                                <i class="fas fa-list-ol"></i>
                            </a>
                            <?php endif; ?>

                            <a href="/movie?id=<?php echo $movie['id']; ?>" target="_blank" class="btn-icon btn-view"
                                title="View">
                                <i class="fas fa-eye"></i>
                            </a>

                            <button type="button" class="btn-icon btn-delete open-modal"
                                data-target="#delete-movie-<?php echo $movie['id']; ?>" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>

                        <!-- Delete Modal -->
                        <div class="modal-backdrop" id="delete-movie-<?php echo $movie['id']; ?>">
                            <div class="modal-dialog">
                                <div class="modal-header">
                                    <h4 class="modal-title">Confirm Delete</h4>
                                    <button type="button" class="modal-close">&times;</button>
                                </div>
                                <div class="modal-body">
                                    <p>Are you sure you want to delete
                                        "<?php echo htmlspecialchars($movie['title']); ?>"?</p>
                                    <p class="text-danger">This action cannot be undone.</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-outline modal-cancel">Cancel</button>
                                    <form action="/admin/delete-movie" method="POST" style="display:inline;">
                                        <input type="hidden" name="movie_id" value="<?php echo $movie['id']; ?>">
                                        <button type="submit" class="btn btn-danger">Delete</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
                <?php else: ?>
                <tr>
                    <td colspan="9" class="text-center">No movies found</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="pagination mt-4">
        <div class="page-item">
            <a href="#" class="page-link">
                <i class="fas fa-angle-left"></i>
            </a>
        </div>
        <div class="page-item"><a href="#" class="page-link active">1</a></div>
        <div class="page-item"><a href="#" class="page-link">2</a></div>
        <div class="page-item"><a href="#" class="page-link">3</a></div>
        <div class="page-item">
            <a href="#" class="page-link">
                <i class="fas fa-angle-right"></i>
            </a>
        </div>
    </div>
</div>

<!-- Import Movies Modal -->
<div class="modal-backdrop" id="import-movies">
    <div class="modal-dialog">
        <div class="modal-header">
            <h4 class="modal-title">Import Movies</h4>
            <button type="button" class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <form action="/admin/import-movies" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label class="form-label">Select CSV File</label>
                    <div class="file-upload">
                        <input type="file" name="import_file" accept=".csv">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <p>Drop CSV file here or click to browse</p>
                    </div>
                </div>
                <p class="mt-3">
                    <small>The CSV file should include the following columns: title, description, release_year, genre,
                        duration, poster_path, video_path, trailer_url, is_premium</small>
                </p>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-outline modal-cancel">Cancel</button>
            <button type="submit" class="btn btn-danger">Import</button>
        </div>
    </div>
</div>