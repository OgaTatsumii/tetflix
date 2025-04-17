<?php
// Get all unique genres
$genres = $movieModel->getUniqueGenres();
?>

<div class="card-section">
    <div class="card-header">
        <h3 class="card-title">Movie Categories</h3>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Genre</th>
                    <th>Movie Count</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($genres && $genres->rowCount() > 0): ?>
                <?php while ($genre = $genres->fetch(PDO::FETCH_ASSOC)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($genre['genre']); ?></td>
                    <td>
                        <?php
                                $movies = $movieModel->getMoviesByGenre($genre['genre']);
                                echo $movies ? $movies->rowCount() : 0;
                                ?>
                    </td>
                    <td>
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-outline-primary edit-genre"
                                data-genre="<?php echo htmlspecialchars($genre['genre']); ?>">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger delete-genre"
                                data-genre="<?php echo htmlspecialchars($genre['genre']); ?>">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
                <?php else: ?>
                <tr>
                    <td colspan="3" class="text-center">No categories found</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Add New Genre Form -->
    <div class="mt-4">
        <form action="/admin/add-genre" method="POST" class="form-inline">
            <div class="form-group mr-2">
                <input type="text" name="genre" class="form-control" placeholder="New Genre Name" required>
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New Genre
            </button>
        </form>
    </div>
</div>

<!-- Edit Genre Modal -->
<div class="modal fade" id="editGenreModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Genre</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="/admin/update-genre" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="old_genre" id="old_genre">
                    <div class="form-group">
                        <label for="new_genre">Genre Name</label>
                        <input type="text" class="form-control" id="new_genre" name="new_genre" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Genre Modal -->
<div class="modal fade" id="deleteGenreModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Genre</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="/admin/delete-genre" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="genre" id="delete_genre">
                    <p>Are you sure you want to delete this genre? This action cannot be undone.</p>
                    <p>All movies in this genre will be set to "Uncategorized".</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete Genre</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Edit Genre
    const editButtons = document.querySelectorAll('.edit-genre');
    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const genre = this.getAttribute('data-genre');
            document.getElementById('old_genre').value = genre;
            document.getElementById('new_genre').value = genre;
            $('#editGenreModal').modal('show');
        });
    });

    // Delete Genre
    const deleteButtons = document.querySelectorAll('.delete-genre');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const genre = this.getAttribute('data-genre');
            document.getElementById('delete_genre').value = genre;
            $('#deleteGenreModal').modal('show');
        });
    });
});
</script>