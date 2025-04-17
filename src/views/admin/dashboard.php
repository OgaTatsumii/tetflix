<?php
// Get statistics for the dashboard
$recentMovies = $movieModel->getLatestMovies(5);
$recentUsers = $userModel->getRecentUsers(5);
?>

<!-- Dashboard Cards -->
<div class="dashboard-cards">
    <div class="dashboard-card">
        <div class="card-icon">
            <i class="fas fa-film"></i>
        </div>
        <div class="card-info">
            <h3><?php echo $totalMovies ?? 0; ?></h3>
            <p>Total Movies</p>
        </div>
    </div>

    <div class="dashboard-card">
        <div class="card-icon">
            <i class="fas fa-users"></i>
        </div>
        <div class="card-info">
            <h3><?php echo $totalUsers ?? 0; ?></h3>
            <p>Total Users</p>
        </div>
    </div>

    <div class="dashboard-card">
        <div class="card-icon">
            <i class="fas fa-crown"></i>
        </div>
        <div class="card-info">
            <h3><?php echo $premiumUsers ?? 0; ?></h3>
            <p>Premium Users</p>
        </div>
    </div>

    <div class="dashboard-card">
        <div class="card-icon">
            <i class="fas fa-eye"></i>
        </div>
        <div class="card-info">
            <h3>1,258</h3>
            <p>Total Views</p>
        </div>
    </div>
</div>

<!-- Recent Content -->
<div class="row">
    <!-- Recent Movies -->
    <div class="card-section">
        <div class="card-header">
            <h3 class="card-title">Recently Added Movies</h3>
            <a href="/admin-panel?page=movies" class="btn btn-danger">View All</a>
        </div>

        <div class="admin-table">
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Genre</th>
                        <th>Release Year</th>
                        <th>Added Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($recentMovies) && $recentMovies->rowCount() > 0): ?>
                        <?php while ($movie = $recentMovies->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($movie['title']); ?></td>
                                <td><?php echo htmlspecialchars($movie['genre']); ?></td>
                                <td><?php echo htmlspecialchars($movie['release_year']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($movie['created_at'])); ?></td>
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
                                        <a href="/movie?id=<?php echo $movie['id']; ?>" target="_blank"
                                            class="btn-icon btn-view" title="View">
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
                            <td colspan="6" class="text-center">No movies found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Users -->
    <div class="card-section mt-4">
        <div class="card-header">
            <h3 class="card-title">Recently Registered Users</h3>
            <a href="/admin-panel?page=users" class="btn btn-danger">View All</a>
        </div>

        <div class="admin-table">
            <table>
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Registered Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($recentUsers) && $recentUsers->rowCount() > 0): ?>
                        <?php while ($user = $recentUsers->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                <td>
                                    <?php if ($user['is_premium']): ?>
                                        <span class="badge badge-warning">Premium</span>
                                    <?php else: ?>
                                        <span class="badge badge-info">Regular</span>
                                    <?php endif; ?>

                                    <?php if ($user['role'] === 'admin'): ?>
                                        <span class="badge badge-danger">Admin</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="/admin-panel?page=edit-user&id=<?php echo $user['id']; ?>"
                                            class="btn-icon btn-edit" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn-icon btn-delete open-modal"
                                            data-target="#delete-user-<?php echo $user['id']; ?>" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>

                                    <!-- Delete Modal -->
                                    <div class="modal-backdrop" id="delete-user-<?php echo $user['id']; ?>">
                                        <div class="modal-dialog">
                                            <div class="modal-header">
                                                <h4 class="modal-title">Confirm Delete</h4>
                                                <button type="button" class="modal-close">&times;</button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Are you sure you want to delete user
                                                    "<?php echo htmlspecialchars($user['username']); ?>"?</p>
                                                <p class="text-danger">This action cannot be undone.</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-outline modal-cancel">Cancel</button>
                                                <form action="/admin/delete-user" method="POST" style="display:inline;">
                                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
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
                            <td colspan="5" class="text-center">No users found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>