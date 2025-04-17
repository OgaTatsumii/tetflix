<?php
// Handle filtering
$roleFilter = isset($_GET['role']) ? $_GET['role'] : '';
$statusFilter = isset($_GET['status']) ? $_GET['status'] : '';
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

// Get all users with filters
$users = $userModel->getAllUsersWithFilters($roleFilter, $statusFilter, $searchTerm);
?>

<!-- Filter and Search Section -->
<div class="card-section">
    <div class="card-header">
        <h3 class="card-title">Filter Users</h3>
        <button type="button" class="btn btn-danger open-modal" data-target="#add-user-modal">
            <i class="fas fa-plus"></i> Add New User
        </button>
    </div>

    <form action="/admin-panel" method="GET" class="admin-form mt-3">
        <input type="hidden" name="page" value="users">

        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="search">Search</label>
                <input type="text" id="search" name="search" class="form-control"
                    value="<?php echo htmlspecialchars($searchTerm); ?>" placeholder="Search by username or email...">
            </div>

            <div class="form-group">
                <label class="form-label" for="role">Role</label>
                <select id="role" name="role" class="form-control">
                    <option value="">All Roles</option>
                    <option value="admin" <?php echo ($roleFilter === 'admin') ? 'selected' : ''; ?>>Admin</option>
                    <option value="user" <?php echo ($roleFilter === 'user') ? 'selected' : ''; ?>>User</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" for="status">Premium Status</label>
                <select id="status" name="status" class="form-control">
                    <option value="">All Status</option>
                    <option value="1" <?php echo ($statusFilter === '1') ? 'selected' : ''; ?>>Premium</option>
                    <option value="0" <?php echo ($statusFilter === '0') ? 'selected' : ''; ?>>Free</option>
                </select>
            </div>

            <div class="form-group" style="display: flex; align-items: flex-end;">
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-filter"></i> Apply Filters
                </button>
                <a href="/admin-panel?page=users" class="btn btn-outline ml-2">Clear</a>
            </div>
        </div>
    </form>
</div>

<!-- Users List -->
<div class="card-section mt-4">
    <div class="card-header">
        <h3 class="card-title">All Users</h3>
        <div class="header-actions">
            <button class="btn btn-outline open-modal" data-target="#export-users">
                <i class="fas fa-file-export"></i> Export
            </button>
        </div>
    </div>

    <div class="admin-table">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Registration Date</th>
                    <th>Last Login</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (isset($users) && $users->rowCount() > 0): ?>
                <?php while ($user = $users->fetch(PDO::FETCH_ASSOC)): ?>
                <tr>
                    <td><?php echo $user['id']; ?></td>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td>
                        <?php if ($user['role'] === 'admin'): ?>
                        <span class="badge badge-danger">Admin</span>
                        <?php else: ?>
                        <span class="badge badge-info">User</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($user['is_premium']): ?>
                        <span class="badge badge-warning">Premium</span>
                        <?php else: ?>
                        <span class="badge badge-success">Free</span>
                        <?php endif; ?>

                        <?php if (isset($user['is_active']) && !$user['is_active']): ?>
                        <span class="badge badge-danger">Blocked</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                    <td>
                        <?php
                                echo isset($user['last_login']) && $user['last_login']
                                    ? date('M d, Y H:i', strtotime($user['last_login']))
                                    : 'Never';
                                ?>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <button type="button" class="btn-icon btn-edit open-modal"
                                data-target="#edit-user-<?php echo $user['id']; ?>" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>

                            <?php if (isset($user['is_active']) && $user['is_active']): ?>
                            <button type="button" class="btn-icon btn-delete open-modal"
                                data-target="#block-user-<?php echo $user['id']; ?>" title="Block">
                                <i class="fas fa-ban"></i>
                            </button>
                            <?php else: ?>
                            <button type="button" class="btn-icon btn-success open-modal"
                                data-target="#unblock-user-<?php echo $user['id']; ?>" title="Unblock">
                                <i class="fas fa-check"></i>
                            </button>
                            <?php endif; ?>

                            <a href="/admin-panel?page=user-activity&user_id=<?php echo $user['id']; ?>"
                                class="btn-icon btn-view" title="Activity">
                                <i class="fas fa-history"></i>
                            </a>

                            <button type="button" class="btn-icon btn-delete open-modal"
                                data-target="#delete-user-<?php echo $user['id']; ?>" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>

                        <!-- Edit User Modal -->
                        <div class="modal-backdrop" id="edit-user-<?php echo $user['id']; ?>">
                            <div class="modal-dialog">
                                <div class="modal-header">
                                    <h4 class="modal-title">Edit User</h4>
                                    <button type="button" class="modal-close">&times;</button>
                                </div>
                                <div class="modal-body">
                                    <form action="/admin/update-user" method="POST">
                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">

                                        <div class="form-group">
                                            <label class="form-label"
                                                for="username_<?php echo $user['id']; ?>">Username</label>
                                            <input type="text" id="username_<?php echo $user['id']; ?>" name="username"
                                                class="form-control"
                                                value="<?php echo htmlspecialchars($user['username']); ?>" required>
                                        </div>

                                        <div class="form-group">
                                            <label class="form-label"
                                                for="email_<?php echo $user['id']; ?>">Email</label>
                                            <input type="email" id="email_<?php echo $user['id']; ?>" name="email"
                                                class="form-control"
                                                value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                        </div>

                                        <div class="form-group">
                                            <label class="form-label" for="role_<?php echo $user['id']; ?>">Role</label>
                                            <select id="role_<?php echo $user['id']; ?>" name="role"
                                                class="form-control">
                                                <option value="user"
                                                    <?php echo ($user['role'] === 'user') ? 'selected' : ''; ?>>User
                                                </option>
                                                <option value="admin"
                                                    <?php echo ($user['role'] === 'admin') ? 'selected' : ''; ?>>Admin
                                                </option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label class="form-label"
                                                for="is_premium_<?php echo $user['id']; ?>">Premium Status</label>
                                            <select id="is_premium_<?php echo $user['id']; ?>" name="is_premium"
                                                class="form-control">
                                                <option value="0"
                                                    <?php echo ($user['is_premium'] == 0) ? 'selected' : ''; ?>>Free
                                                </option>
                                                <option value="1"
                                                    <?php echo ($user['is_premium'] == 1) ? 'selected' : ''; ?>>Premium
                                                </option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label class="form-label"
                                                for="premium_until_<?php echo $user['id']; ?>">Premium Until</label>
                                            <input type="date" id="premium_until_<?php echo $user['id']; ?>"
                                                name="premium_until" class="form-control"
                                                value="<?php echo isset($user['premium_until']) ? date('Y-m-d', strtotime($user['premium_until'])) : ''; ?>">
                                        </div>

                                        <div class="form-group">
                                            <label class="form-label" for="new_password_<?php echo $user['id']; ?>">New
                                                Password (leave blank to keep current)</label>
                                            <input type="password" id="new_password_<?php echo $user['id']; ?>"
                                                name="new_password" class="form-control">
                                        </div>

                                        <div class="form-group mt-4">
                                            <button type="submit" class="btn btn-danger w-100">
                                                <i class="fas fa-save"></i> Update User
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Block User Modal -->
                        <div class="modal-backdrop" id="block-user-<?php echo $user['id']; ?>">
                            <div class="modal-dialog">
                                <div class="modal-header">
                                    <h4 class="modal-title">Block User</h4>
                                    <button type="button" class="modal-close">&times;</button>
                                </div>
                                <div class="modal-body">
                                    <p>Are you sure you want to block user
                                        "<?php echo htmlspecialchars($user['username']); ?>"?</p>
                                    <p>Blocked users will not be able to login or access their account.</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-outline modal-cancel">Cancel</button>
                                    <form action="/admin/block-user" method="POST" style="display:inline;">
                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                        <button type="submit" class="btn btn-danger">Block User</button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Unblock User Modal -->
                        <div class="modal-backdrop" id="unblock-user-<?php echo $user['id']; ?>">
                            <div class="modal-dialog">
                                <div class="modal-header">
                                    <h4 class="modal-title">Unblock User</h4>
                                    <button type="button" class="modal-close">&times;</button>
                                </div>
                                <div class="modal-body">
                                    <p>Are you sure you want to unblock user
                                        "<?php echo htmlspecialchars($user['username']); ?>"?</p>
                                    <p>This will restore their account access.</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-outline modal-cancel">Cancel</button>
                                    <form action="/admin/unblock-user" method="POST" style="display:inline;">
                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                        <button type="submit" class="btn btn-success">Unblock User</button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Delete User Modal -->
                        <div class="modal-backdrop" id="delete-user-<?php echo $user['id']; ?>">
                            <div class="modal-dialog">
                                <div class="modal-header">
                                    <h4 class="modal-title">Confirm Delete</h4>
                                    <button type="button" class="modal-close">&times;</button>
                                </div>
                                <div class="modal-body">
                                    <p>Are you sure you want to delete user
                                        "<?php echo htmlspecialchars($user['username']); ?>"?</p>
                                    <p class="text-danger">This action cannot be undone. All user data, including watch
                                        history and comments, will be permanently deleted.</p>
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
                    <td colspan="8" class="text-center">No users found</td>
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

<!-- Add User Modal -->
<div class="modal-backdrop" id="add-user-modal">
    <div class="modal-dialog">
        <div class="modal-header">
            <h4 class="modal-title">Add New User</h4>
            <button type="button" class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <form action="/admin/add-user" method="POST">
                <div class="form-group">
                    <label class="form-label" for="new_username">Username *</label>
                    <input type="text" id="new_username" name="username" class="form-control" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="new_email">Email *</label>
                    <input type="email" id="new_email" name="email" class="form-control" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="new_password">Password *</label>
                    <input type="password" id="new_password" name="password" class="form-control" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="new_role">Role</label>
                    <select id="new_role" name="role" class="form-control">
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="new_premium">Premium Status</label>
                    <select id="new_premium" name="is_premium" class="form-control">
                        <option value="0">Free</option>
                        <option value="1">Premium</option>
                    </select>
                </div>

                <div class="form-group" id="premium-until-container" style="display: none;">
                    <label class="form-label" for="new_premium_until">Premium Until</label>
                    <input type="date" id="new_premium_until" name="premium_until" class="form-control">
                </div>

                <div class="form-group mt-4">
                    <button type="submit" class="btn btn-danger w-100">
                        <i class="fas fa-plus"></i> Add User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Export Users Modal -->
<div class="modal-backdrop" id="export-users">
    <div class="modal-dialog">
        <div class="modal-header">
            <h4 class="modal-title">Export Users</h4>
            <button type="button" class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <form action="/admin/export-users" method="POST">
                <div class="form-group">
                    <label class="form-label">Export Format</label>
                    <div class="form-check">
                        <input type="radio" id="export_csv" name="export_format" value="csv" class="form-check-input"
                            checked>
                        <label for="export_csv">CSV</label>
                    </div>
                    <div class="form-check">
                        <input type="radio" id="export_json" name="export_format" value="json" class="form-check-input">
                        <label for="export_json">JSON</label>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Include Fields</label>
                    <div class="form-check">
                        <input type="checkbox" id="include_id" name="fields[]" value="id" class="form-check-input"
                            checked>
                        <label for="include_id">ID</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" id="include_username" name="fields[]" value="username"
                            class="form-check-input" checked>
                        <label for="include_username">Username</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" id="include_email" name="fields[]" value="email" class="form-check-input"
                            checked>
                        <label for="include_email">Email</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" id="include_role" name="fields[]" value="role" class="form-check-input"
                            checked>
                        <label for="include_role">Role</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" id="include_premium" name="fields[]" value="is_premium"
                            class="form-check-input" checked>
                        <label for="include_premium">Premium Status</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" id="include_dates" name="fields[]" value="dates" class="form-check-input"
                            checked>
                        <label for="include_dates">Registration Date & Last Login</label>
                    </div>
                </div>

                <div class="form-group mt-4">
                    <button type="submit" class="btn btn-danger w-100">
                        <i class="fas fa-download"></i> Export
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Show/hide premium until date based on premium status
    const premiumSelect = document.getElementById('new_premium');
    const premiumUntilContainer = document.getElementById('premium-until-container');

    premiumSelect.addEventListener('change', function() {
        if (this.value === '1') {
            premiumUntilContainer.style.display = 'block';
        } else {
            premiumUntilContainer.style.display = 'none';
        }
    });

    // For edit user modals
    document.querySelectorAll('[id^="is_premium_"]').forEach(function(select) {
        const userId = select.id.split('_').pop();
        const dateField = document.getElementById('premium_until_' + userId);

        select.addEventListener('change', function() {
            if (this.value === '1') {
                dateField.parentElement.style.display = 'block';
            } else {
                dateField.parentElement.style.display = 'none';
            }
        });

        // Set initial state
        if (select.value === '1') {
            dateField.parentElement.style.display = 'block';
        } else {
            dateField.parentElement.style.display = 'none';
        }
    });
});
</script>