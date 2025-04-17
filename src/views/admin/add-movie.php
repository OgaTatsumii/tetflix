<?php
// Get available genres for the dropdown
$genres = $movieModel->getUniqueGenres();
?>

<div class="card-section">
    <div class="card-header">
        <h3 class="card-title">Add New Movie</h3>
    </div>

    <form action="/admin/add-movie" method="POST" enctype="multipart/form-data" class="admin-form mt-3">
        <!-- Basic Information -->
        <div class="form-row">
            <div class="form-group-full">
                <label class="form-label" for="title">Movie Title *</label>
                <input type="text" id="title" name="title" class="form-control" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="release_year">Release Year *</label>
                <input type="number" id="release_year" name="release_year" class="form-control" min="1900"
                    max="<?php echo date('Y'); ?>" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="duration">Duration (minutes) *</label>
                <input type="number" id="duration" name="duration" class="form-control" min="1" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="genre">Genre *</label>
                <select id="genre" name="genre" class="form-control" required>
                    <option value="">Select Genre</option>
                    <?php if (isset($genres) && $genres->rowCount() > 0): ?>
                        <?php while ($genre = $genres->fetch(PDO::FETCH_ASSOC)): ?>
                            <option value="<?php echo htmlspecialchars($genre['genre']); ?>">
                                <?php echo htmlspecialchars($genre['genre']); ?>
                            </option>
                        <?php endwhile; ?>
                    <?php endif; ?>
                    <option value="other">Other (Add New)</option>
                </select>
            </div>

            <div class="form-group" id="new-genre-group" style="display: none;">
                <label class="form-label" for="new_genre">New Genre</label>
                <input type="text" id="new_genre" name="new_genre" class="form-control">
            </div>
        </div>

        <!-- Description -->
        <div class="form-row mt-4">
            <div class="form-group-full">
                <label class="form-label" for="description">Description *</label>
                <textarea id="description" name="description" class="form-control" rows="4" required></textarea>
            </div>
        </div>

        <!-- Type Selection -->
        <div class="form-row mt-4">
            <div class="form-group">
                <label class="form-label">Type *</label>
                <div class="form-check">
                    <input type="radio" id="type_movie" name="is_series" value="0" class="form-check-input" checked>
                    <label for="type_movie">Single Movie</label>
                </div>
                <div class="form-check">
                    <input type="radio" id="type_series" name="is_series" value="1" class="form-check-input">
                    <label for="type_series">TV Series</label>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Access Type</label>
                <div class="form-check">
                    <input type="radio" id="access_public" name="is_premium" value="0" class="form-check-input" checked>
                    <label for="access_public">Public (Free)</label>
                </div>
                <div class="form-check">
                    <input type="radio" id="access_premium" name="is_premium" value="1" class="form-check-input">
                    <label for="access_premium">Premium (Paid)</label>
                </div>
            </div>
        </div>

        <!-- Media Files -->
        <div class="form-row mt-4">
            <div class="form-group">
                <label class="form-label" for="poster">Poster Image *</label>
                <div class="file-upload">
                    <input type="file" id="poster" name="poster" accept="image/*" required>
                    <i class="fas fa-image"></i>
                    <p>Upload Poster Image</p>
                </div>
                <div class="file-preview" id="poster-preview"></div>
            </div>

            <div class="form-group">
                <label class="form-label" for="video">Video File (for single movies)</label>
                <div class="file-upload">
                    <input type="file" id="video" name="video" accept="video/*">
                    <i class="fas fa-video"></i>
                    <p>Upload Video File</p>
                </div>
                <small>Max file size: 2GB</small>
            </div>
        </div>

        <!-- Video Sources -->
        <div class="form-row mt-4">
            <div class="form-group-full">
                <label class="form-label" for="trailer_url">Trailer URL (YouTube)</label>
                <input type="url" id="trailer_url" name="trailer_url" class="form-control"
                    placeholder="https://www.youtube.com/watch?v=...">
            </div>
        </div>

        <!-- Server & Links Tab -->
        <div class="tab-nav mt-4">
            <a href="#" class="tab-link active" data-tab="tab-embed">Embed Links</a>
            <a href="#" class="tab-link" data-tab="tab-direct">Direct Links</a>
            <a href="#" class="tab-link" data-tab="tab-hls">HLS Streaming</a>
        </div>

        <div class="tab-content active" id="tab-embed">
            <div class="form-row">
                <div class="form-group-full">
                    <label class="form-label" for="embed_url">Embed URL</label>
                    <input type="url" id="embed_url" name="embed_url" class="form-control" placeholder="https://...">
                    <small>Iframe embed code from video hosting platforms like Vimeo, Dailymotion, etc.</small>
                </div>
            </div>
        </div>

        <div class="tab-content" id="tab-direct">
            <div class="form-row">
                <div class="form-group-full">
                    <label class="form-label" for="direct_url">Direct URL</label>
                    <input type="url" id="direct_url" name="direct_url" class="form-control" placeholder="https://...">
                    <small>Direct link to MP4 or other video file</small>
                </div>
            </div>

            <div class="form-group mt-2">
                <button type="button" class="btn btn-outline btn-sm" id="add-backup-link">
                    <i class="fas fa-plus"></i> Add Backup Link
                </button>
            </div>

            <div id="backup-links-container">
                <!-- Backup links will be added here dynamically -->
            </div>
        </div>

        <div class="tab-content" id="tab-hls">
            <div class="form-row">
                <div class="form-group-full">
                    <label class="form-label" for="hls_url">HLS URL (.m3u8)</label>
                    <input type="url" id="hls_url" name="hls_url" class="form-control" placeholder="https://...">
                    <small>Link to HLS manifest file (.m3u8) for adaptive streaming</small>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="form-row mt-5">
            <div class="form-group-full text-right">
                <button type="button" class="btn btn-outline">Cancel</button>
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-save"></i> Save Movie
                </button>
            </div>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Show/hide new genre field based on selection
        const genreSelect = document.getElementById('genre');
        const newGenreGroup = document.getElementById('new-genre-group');

        genreSelect.addEventListener('change', function () {
            if (this.value === 'other') {
                newGenreGroup.style.display = 'block';
                document.getElementById('new_genre').setAttribute('required', 'required');
            } else {
                newGenreGroup.style.display = 'none';
                document.getElementById('new_genre').removeAttribute('required');
            }
        });

        // Add backup link functionality
        const addBackupLinkBtn = document.getElementById('add-backup-link');
        const backupLinksContainer = document.getElementById('backup-links-container');
        let linkCounter = 0;

        addBackupLinkBtn.addEventListener('click', function () {
            linkCounter++;
            const linkHtml = `
            <div class="form-row backup-link-row">
                <div class="form-group-full">
                    <div class="d-flex">
                        <input type="url" name="backup_links[${linkCounter}][url]" class="form-control" placeholder="Backup URL ${linkCounter}">
                        <button type="button" class="btn btn-outline remove-backup-link">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <input type="text" name="backup_links[${linkCounter}][label]" class="form-control mt-2" placeholder="Label (e.g. Server 2, HD Quality, etc.)">
                </div>
            </div>
        `;

            backupLinksContainer.insertAdjacentHTML('beforeend', linkHtml);
        });

        // Remove backup link
        backupLinksContainer.addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-backup-link') || e.target.parentElement.classList
                .contains('remove-backup-link')) {
                const row = e.target.closest('.backup-link-row');
                row.remove();
            }
        });

        // File upload preview for poster
        const posterInput = document.getElementById('poster');
        const posterPreview = document.getElementById('poster-preview');

        posterInput.addEventListener('change', function () {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    posterPreview.innerHTML = `
                    <div class="preview-item">
                        <img src="${e.target.result}" alt="Poster Preview">
                        <span class="preview-remove"><i class="fas fa-times"></i></span>
                    </div>
                `;
                }
                reader.readAsDataURL(file);
            }
        });

        // Remove preview
        posterPreview.addEventListener('click', function (e) {
            if (e.target.closest('.preview-remove')) {
                this.innerHTML = '';
                posterInput.value = '';
            }
        });
    });
</script>