<?php
// Get movie ID from URL
$movie_id = isset($_GET['movie_id']) ? intval($_GET['movie_id']) : 0;

// Get movie details
$movie = null;
if ($movie_id > 0) {
    $stmt = $movieModel->getMovieById($movie_id);
    $movie = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Get all episodes for this movie
$episodes = [];
if ($movie) {
    $episodes = $movieModel->getEpisodesByMovieId($movie_id);
}
?>

<?php if (!$movie): ?>
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle"></i> Movie not found.
        <a href="/admin-panel?page=movies" class="btn btn-outline btn-sm ml-3">Back to Movies</a>
    </div>
<?php else: ?>

    <div class="content-header">
        <h3 class="content-title">
            Episodes for: <?php echo htmlspecialchars($movie['title']); ?>
        </h3>
        <a href="/admin-panel?page=movies" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i> Back to Movies
        </a>
    </div>

    <!-- Movie Info Summary -->
    <div class="card-section">
        <div class="card-header">
            <h3 class="card-title">Series Information</h3>
            <a href="/admin-panel?page=edit-movie&id=<?php echo $movie_id; ?>" class="btn btn-outline">
                <i class="fas fa-edit"></i> Edit Series
            </a>
        </div>

        <div class="movie-info-summary">
            <div class="row">
                <div class="col-auto">
                    <?php if (!empty($movie['poster_path'])): ?>
                        <img src="<?php echo htmlspecialchars($movie['poster_path']); ?>" alt="Poster" width="120" height="180">
                    <?php else: ?>
                        <div
                            style="width: 120px; height: 180px; background-color: #333; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-film fa-3x"></i>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col">
                    <table class="table-info">
                        <tr>
                            <th>Title:</th>
                            <td><?php echo htmlspecialchars($movie['title']); ?></td>
                        </tr>
                        <tr>
                            <th>Genre:</th>
                            <td><?php echo htmlspecialchars($movie['genre']); ?></td>
                        </tr>
                        <tr>
                            <th>Release Year:</th>
                            <td><?php echo htmlspecialchars($movie['release_year']); ?></td>
                        </tr>
                        <tr>
                            <th>Status:</th>
                            <td>
                                <?php if ($movie['is_premium']): ?>
                                    <span class="badge badge-warning">Premium</span>
                                <?php else: ?>
                                    <span class="badge badge-success">Public</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Episodes:</th>
                            <td><strong><?php echo is_object($episodes) ? $episodes->rowCount() : 0; ?></strong> episodes
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Episode Form -->
    <div class="card-section mt-4">
        <div class="card-header">
            <h3 class="card-title">Add New Episode</h3>
            <button type="button" class="btn btn-danger open-modal" data-target="#batch-add-episodes">
                <i class="fas fa-plus"></i> Batch Add Episodes
            </button>
        </div>

        <form action="/admin/add-episode" method="POST" enctype="multipart/form-data" class="admin-form mt-3">
            <input type="hidden" name="movie_id" value="<?php echo $movie_id; ?>">

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="episode_number">Episode Number *</label>
                    <input type="number" id="episode_number" name="episode_number" class="form-control" min="1" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="episode_title">Episode Title *</label>
                    <input type="text" id="episode_title" name="episode_title" class="form-control" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="episode_duration">Duration (minutes) *</label>
                    <input type="number" id="episode_duration" name="episode_duration" class="form-control" min="1"
                        required>
                </div>
            </div>

            <div class="form-row mt-3">
                <div class="form-group-full">
                    <label class="form-label" for="episode_description">Description</label>
                    <textarea id="episode_description" name="episode_description" class="form-control" rows="3"></textarea>
                </div>
            </div>

            <!-- Server & Links Tab -->
            <div class="tab-nav mt-4">
                <a href="#" class="tab-link active" data-tab="episode-tab-embed">Embed Links</a>
                <a href="#" class="tab-link" data-tab="episode-tab-direct">Direct Links</a>
                <a href="#" class="tab-link" data-tab="episode-tab-file">Upload File</a>
            </div>

            <div class="tab-content active" id="episode-tab-embed">
                <div class="form-row">
                    <div class="form-group-full">
                        <label class="form-label" for="episode_embed_url">Embed URL</label>
                        <input type="url" id="episode_embed_url" name="episode_embed_url" class="form-control"
                            placeholder="https://...">
                    </div>
                </div>
            </div>

            <div class="tab-content" id="episode-tab-direct">
                <div class="form-row">
                    <div class="form-group-full">
                        <label class="form-label" for="episode_direct_url">Direct URL</label>
                        <input type="url" id="episode_direct_url" name="episode_direct_url" class="form-control"
                            placeholder="https://...">
                    </div>
                </div>

                <div class="form-group mt-2">
                    <button type="button" class="btn btn-outline btn-sm" id="add-episode-backup-link">
                        <i class="fas fa-plus"></i> Add Backup Link
                    </button>
                </div>

                <div id="episode-backup-links-container">
                    <!-- Backup links will be added here dynamically -->
                </div>
            </div>

            <div class="tab-content" id="episode-tab-file">
                <div class="form-row">
                    <div class="form-group-full">
                        <label class="form-label" for="episode_video">Video File</label>
                        <div class="file-upload">
                            <input type="file" id="episode_video" name="episode_video" accept="video/*">
                            <i class="fas fa-video"></i>
                            <p>Upload Episode Video File</p>
                        </div>
                        <small>Max file size: 2GB</small>
                    </div>
                </div>
            </div>

            <div class="form-row mt-4">
                <div class="form-group-full text-right">
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-save"></i> Add Episode
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Episodes List -->
    <div class="card-section mt-4">
        <div class="card-header">
            <h3 class="card-title">All Episodes</h3>
            <div class="header-actions">
                <button class="btn btn-outline">
                    <i class="fas fa-sort-numeric-down"></i> Sort by Number
                </button>
            </div>
        </div>

        <div class="admin-table">
            <table>
                <thead>
                    <tr>
                        <th style="width: 80px;">Episode #</th>
                        <th>Title</th>
                        <th>Duration</th>
                        <th>Sources</th>
                        <th>Added Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($episodes) && is_object($episodes) && $episodes->rowCount() > 0): ?>
                        <?php while ($episode = $episodes->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                                <td class="text-center"><?php echo htmlspecialchars($episode['episode_number']); ?></td>
                                <td><?php echo htmlspecialchars($episode['title']); ?></td>
                                <td><?php echo htmlspecialchars($episode['duration']); ?> min</td>
                                <td>
                                    <?php
                                    $sources = [];
                                    if (!empty($episode['video_path']))
                                        $sources[] = 'File';
                                    if (!empty($episode['embed_url']))
                                        $sources[] = 'Embed';
                                    if (!empty($episode['direct_url']))
                                        $sources[] = 'Direct';
                                    echo !empty($sources) ? implode(', ', $sources) : 'None';
                                    ?>
                                </td>
                                <td><?php echo date('M d, Y', strtotime($episode['created_at'])); ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="/admin-panel?page=edit-episode&id=<?php echo $episode['id']; ?>"
                                            class="btn-icon btn-edit" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="/watch?movie_id=<?php echo $movie_id; ?>&episode=<?php echo $episode['episode_number']; ?>"
                                            target="_blank" class="btn-icon btn-view" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button type="button" class="btn-icon btn-delete open-modal"
                                            data-target="#delete-episode-<?php echo $episode['id']; ?>" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>

                                    <!-- Delete Modal -->
                                    <div class="modal-backdrop" id="delete-episode-<?php echo $episode['id']; ?>">
                                        <div class="modal-dialog">
                                            <div class="modal-header">
                                                <h4 class="modal-title">Confirm Delete</h4>
                                                <button type="button" class="modal-close">&times;</button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Are you sure you want to delete Episode
                                                    <?php echo htmlspecialchars($episode['episode_number']); ?>:
                                                    "<?php echo htmlspecialchars($episode['title']); ?>"?</p>
                                                <p class="text-danger">This action cannot be undone.</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-outline modal-cancel">Cancel</button>
                                                <form action="/admin/delete-episode" method="POST" style="display:inline;">
                                                    <input type="hidden" name="episode_id" value="<?php echo $episode['id']; ?>">
                                                    <input type="hidden" name="movie_id" value="<?php echo $movie_id; ?>">
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
                            <td colspan="6" class="text-center">No episodes found. Add episodes using the form above.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Batch Add Episodes Modal -->
    <div class="modal-backdrop" id="batch-add-episodes">
        <div class="modal-dialog" style="max-width: 700px;">
            <div class="modal-header">
                <h4 class="modal-title">Batch Add Episodes</h4>
                <button type="button" class="modal-close">&times;</button>
            </div>
            <div class="modal-body">
                <form action="/admin/batch-add-episodes" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="movie_id" value="<?php echo $movie_id; ?>">

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="start_episode">Start Episode Number</label>
                            <input type="number" id="start_episode" name="start_episode" class="form-control" min="1"
                                value="1" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="episode_count">Number of Episodes</label>
                            <input type="number" id="episode_count" name="episode_count" class="form-control" min="1"
                                max="50" value="1" required>
                        </div>
                    </div>

                    <div class="form-group mt-3">
                        <label class="form-label">Episode Source Type</label>
                        <div class="form-check">
                            <input type="radio" id="batch_source_pattern" name="batch_source_type" value="pattern"
                                class="form-check-input" checked>
                            <label for="batch_source_pattern">URL Pattern</label>
                        </div>
                        <div class="form-check">
                            <input type="radio" id="batch_source_csv" name="batch_source_type" value="csv"
                                class="form-check-input">
                            <label for="batch_source_csv">CSV Upload</label>
                        </div>
                    </div>

                    <div id="pattern-container">
                        <div class="form-group mt-3">
                            <label class="form-label" for="url_pattern">URL Pattern</label>
                            <input type="text" id="url_pattern" name="url_pattern" class="form-control"
                                placeholder="https://example.com/series/episode{number}.mp4">
                            <small>Use {number} as a placeholder for the episode number</small>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="title_pattern">Title Pattern (optional)</label>
                            <input type="text" id="title_pattern" name="title_pattern" class="form-control"
                                placeholder="Episode {number}">
                            <small>Use {number} as a placeholder for the episode number</small>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="padding">Number Padding</label>
                            <select id="padding" name="padding" class="form-control">
                                <option value="0">No Padding (1, 2, 3...)</option>
                                <option value="2">2 Digits (01, 02, 03...)</option>
                                <option value="3">3 Digits (001, 002, 003...)</option>
                            </select>
                        </div>
                    </div>

                    <div id="csv-container" style="display: none;">
                        <div class="form-group mt-3">
                            <label class="form-label">Upload CSV</label>
                            <div class="file-upload">
                                <input type="file" name="episodes_csv" accept=".csv">
                                <i class="fas fa-file-csv"></i>
                                <p>Upload CSV File</p>
                            </div>
                            <small>CSV should have columns: episode_number,title,duration,direct_url (or embed_url)</small>
                        </div>
                    </div>

                    <div class="form-group mt-4">
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-plus"></i> Add Episodes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Batch add episodes - toggle between pattern and CSV
            const batchSourcePattern = document.getElementById('batch_source_pattern');
            const batchSourceCsv = document.getElementById('batch_source_csv');
            const patternContainer = document.getElementById('pattern-container');
            const csvContainer = document.getElementById('csv-container');

            batchSourcePattern.addEventListener('change', function () {
                if (this.checked) {
                    patternContainer.style.display = 'block';
                    csvContainer.style.display = 'none';
                }
            });

            batchSourceCsv.addEventListener('change', function () {
                if (this.checked) {
                    patternContainer.style.display = 'none';
                    csvContainer.style.display = 'block';
                }
            });

            // Add backup link functionality for episodes
            const addEpisodeBackupLinkBtn = document.getElementById('add-episode-backup-link');
            const episodeBackupLinksContainer = document.getElementById('episode-backup-links-container');
            let episodeLinkCounter = 0;

            if (addEpisodeBackupLinkBtn) {
                addEpisodeBackupLinkBtn.addEventListener('click', function () {
                    episodeLinkCounter++;
                    const linkHtml = `
                <div class="form-row backup-link-row">
                    <div class="form-group-full">
                        <div class="d-flex">
                            <input type="url" name="episode_backup_links[${episodeLinkCounter}][url]" class="form-control" placeholder="Backup URL ${episodeLinkCounter}">
                            <button type="button" class="btn btn-outline remove-backup-link">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <input type="text" name="episode_backup_links[${episodeLinkCounter}][label]" class="form-control mt-2" placeholder="Label (e.g. Server 2, HD Quality, etc.)">
                    </div>
                </div>
            `;

                    episodeBackupLinksContainer.insertAdjacentHTML('beforeend', linkHtml);
                });

                // Remove backup link
                episodeBackupLinksContainer.addEventListener('click', function (e) {
                    if (e.target.classList.contains('remove-backup-link') || e.target.parentElement.classList.contains('remove-backup-link')) {
                        const row = e.target.closest('.backup-link-row');
                        row.remove();
                    }
                });
            }
        });
    </script>

<?php endif; ?>