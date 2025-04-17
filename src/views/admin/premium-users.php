<?php
// Get all premium users
$premiumUsers = $userModel->getPremiumUsers();

// Get subscription plans
$subscriptionPlans = [
    ['id' => 1, 'name' => 'Monthly', 'duration' => 1, 'price' => 9.99, 'currency' => 'USD'],
    ['id' => 2, 'name' => 'Quarterly', 'duration' => 3, 'price' => 24.99, 'currency' => 'USD'],
    ['id' => 3, 'name' => 'Annual', 'duration' => 12, 'price' => 89.99, 'currency' => 'USD']
];

// Get recent payments
$recentPayments = $userModel->getRecentPayments(10);
?>

<!-- Premium User Stats -->
<div class="dashboard-cards">
    <div class="dashboard-card">
        <div class="card-icon">
            <i class="fas fa-crown"></i>
        </div>
        <div class="card-info">
            <h3><?php echo isset($premiumUsers) ? $premiumUsers->rowCount() : 0; ?></h3>
            <p>Premium Users</p>
        </div>
    </div>

    <div class="dashboard-card">
        <div class="card-icon">
            <i class="fas fa-wallet"></i>
        </div>
        <div class="card-info">
            <h3>$<?php echo number_format(9887.50, 2); ?></h3>
            <p>Total Revenue</p>
        </div>
    </div>

    <div class="dashboard-card">
        <div class="card-icon">
            <i class="fas fa-chart-line"></i>
        </div>
        <div class="card-info">
            <h3>$<?php echo number_format(1245.00, 2); ?></h3>
            <p>This Month</p>
        </div>
    </div>

    <div class="dashboard-card">
        <div class="card-icon">
            <i class="fas fa-percentage"></i>
        </div>
        <div class="card-info">
            <h3>23%</h3>
            <p>Conversion Rate</p>
        </div>
    </div>
</div>

<!-- Subscription Plans -->
<div class="card-section mt-4">
    <div class="card-header">
        <h3 class="card-title">Subscription Plans</h3>
        <button type="button" class="btn btn-danger open-modal" data-target="#add-plan-modal">
            <i class="fas fa-plus"></i> Add Plan
        </button>
    </div>

    <div class="admin-table">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Plan Name</th>
                    <th>Duration</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($subscriptionPlans as $plan): ?>
                    <tr>
                        <td><?php echo $plan['id']; ?></td>
                        <td><?php echo htmlspecialchars($plan['name']); ?></td>
                        <td><?php echo $plan['duration']; ?> month<?php echo $plan['duration'] > 1 ? 's' : ''; ?></td>
                        <td><?php echo $plan['currency']; ?>     <?php echo number_format($plan['price'], 2); ?></td>
                        <td><span class="badge badge-success">Active</span></td>
                        <td>
                            <div class="action-buttons">
                                <button type="button" class="btn-icon btn-edit open-modal"
                                    data-target="#edit-plan-<?php echo $plan['id']; ?>" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" class="btn-icon btn-delete open-modal"
                                    data-target="#delete-plan-<?php echo $plan['id']; ?>" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>

                            <!-- Edit Plan Modal -->
                            <div class="modal-backdrop" id="edit-plan-<?php echo $plan['id']; ?>">
                                <div class="modal-dialog">
                                    <div class="modal-header">
                                        <h4 class="modal-title">Edit Subscription Plan</h4>
                                        <button type="button" class="modal-close">&times;</button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="/admin/update-plan" method="POST">
                                            <input type="hidden" name="plan_id" value="<?php echo $plan['id']; ?>">

                                            <div class="form-group">
                                                <label class="form-label" for="plan_name_<?php echo $plan['id']; ?>">Plan
                                                    Name</label>
                                                <input type="text" id="plan_name_<?php echo $plan['id']; ?>"
                                                    name="plan_name" class="form-control"
                                                    value="<?php echo htmlspecialchars($plan['name']); ?>" required>
                                            </div>

                                            <div class="form-group">
                                                <label class="form-label"
                                                    for="plan_duration_<?php echo $plan['id']; ?>">Duration (months)</label>
                                                <input type="number" id="plan_duration_<?php echo $plan['id']; ?>"
                                                    name="plan_duration" class="form-control"
                                                    value="<?php echo $plan['duration']; ?>" min="1" required>
                                            </div>

                                            <div class="form-row">
                                                <div class="form-group">
                                                    <label class="form-label"
                                                        for="plan_price_<?php echo $plan['id']; ?>">Price</label>
                                                    <input type="number" id="plan_price_<?php echo $plan['id']; ?>"
                                                        name="plan_price" class="form-control"
                                                        value="<?php echo $plan['price']; ?>" min="0.01" step="0.01"
                                                        required>
                                                </div>

                                                <div class="form-group">
                                                    <label class="form-label"
                                                        for="plan_currency_<?php echo $plan['id']; ?>">Currency</label>
                                                    <select id="plan_currency_<?php echo $plan['id']; ?>"
                                                        name="plan_currency" class="form-control">
                                                        <option value="USD" <?php echo ($plan['currency'] === 'USD') ? 'selected' : ''; ?>>USD</option>
                                                        <option value="EUR" <?php echo ($plan['currency'] === 'EUR') ? 'selected' : ''; ?>>EUR</option>
                                                        <option value="GBP" <?php echo ($plan['currency'] === 'GBP') ? 'selected' : ''; ?>>GBP</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="form-label"
                                                    for="plan_status_<?php echo $plan['id']; ?>">Status</label>
                                                <select id="plan_status_<?php echo $plan['id']; ?>" name="plan_status"
                                                    class="form-control">
                                                    <option value="active" selected>Active</option>
                                                    <option value="inactive">Inactive</option>
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <label class="form-label"
                                                    for="plan_features_<?php echo $plan['id']; ?>">Features (one per
                                                    line)</label>
                                                <textarea id="plan_features_<?php echo $plan['id']; ?>" name="plan_features"
                                                    class="form-control" rows="4">Ad-free viewing
    All content access
    HD Quality
    Multiple device streaming</textarea>
                                            </div>

                                            <div class="form-group mt-4">
                                                <button type="submit" class="btn btn-danger w-100">
                                                    <i class="fas fa-save"></i> Update Plan
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Delete Plan Modal -->
                            <div class="modal-backdrop" id="delete-plan-<?php echo $plan['id']; ?>">
                                <div class="modal-dialog">
                                    <div class="modal-header">
                                        <h4 class="modal-title">Confirm Delete</h4>
                                        <button type="button" class="modal-close">&times;</button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Are you sure you want to delete the
                                            "<?php echo htmlspecialchars($plan['name']); ?>" plan?</p>
                                        <p class="text-danger">This may affect users who are currently subscribed to this
                                            plan.</p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-outline modal-cancel">Cancel</button>
                                        <form action="/admin/delete-plan" method="POST" style="display:inline;">
                                            <input type="hidden" name="plan_id" value="<?php echo $plan['id']; ?>">
                                            <button type="submit" class="btn btn-danger">Delete</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Premium Users List -->
<div class="card-section mt-4">
    <div class="card-header">
        <h3 class="card-title">Premium Users</h3>
        <a href="/admin-panel?page=users" class="btn btn-outline">
            View All Users
        </a>
    </div>

    <div class="admin-table">
        <table>
            <thead>
                <tr>
                    <th>User</th>
                    <th>Subscription Plan</th>
                    <th>Start Date</th>
                    <th>Expiry Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (isset($premiumUsers) && $premiumUsers->rowCount() > 0): ?>
                    <?php while ($user = $premiumUsers->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr>
                            <td>
                                <div class="user-info">
                                    <div class="user-name"><?php echo htmlspecialchars($user['username']); ?></div>
                                    <div class="user-email"><?php echo htmlspecialchars($user['email']); ?></div>
                                </div>
                            </td>
                            <td>
                                <?php
                                // This is dummy data for the example
                                $planName = $user['id'] % 3 === 0 ? 'Annual' : ($user['id'] % 2 === 0 ? 'Quarterly' : 'Monthly');
                                echo $planName;
                                ?>
                            </td>
                            <td><?php echo date('M d, Y', strtotime('-' . (rand(1, 90)) . ' days')); ?></td>
                            <td>
                                <?php
                                echo isset($user['premium_until'])
                                    ? date('M d, Y', strtotime($user['premium_until']))
                                    : date('M d, Y', strtotime('+' . (rand(30, 365)) . ' days'));
                                ?>
                            </td>
                            <td>
                                <?php if (isset($user['premium_until']) && strtotime($user['premium_until']) < time()): ?>
                                    <span class="badge badge-danger">Expired</span>
                                <?php else: ?>
                                    <span class="badge badge-success">Active</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button type="button" class="btn-icon btn-edit open-modal"
                                        data-target="#extend-premium-<?php echo $user['id']; ?>" title="Extend">
                                        <i class="fas fa-clock"></i>
                                    </button>
                                    <button type="button" class="btn-icon btn-delete open-modal"
                                        data-target="#cancel-premium-<?php echo $user['id']; ?>" title="Cancel">
                                        <i class="fas fa-ban"></i>
                                    </button>
                                    <a href="/admin-panel?page=payment-history&user_id=<?php echo $user['id']; ?>"
                                        class="btn-icon btn-view" title="Payment History">
                                        <i class="fas fa-receipt"></i>
                                    </a>
                                </div>

                                <!-- Extend Premium Modal -->
                                <div class="modal-backdrop" id="extend-premium-<?php echo $user['id']; ?>">
                                    <div class="modal-dialog">
                                        <div class="modal-header">
                                            <h4 class="modal-title">Extend Premium Subscription</h4>
                                            <button type="button" class="modal-close">&times;</button>
                                        </div>
                                        <div class="modal-body">
                                            <form action="/admin/extend-premium" method="POST">
                                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">

                                                <div class="form-group">
                                                    <label class="form-label">User</label>
                                                    <div class="form-control-static">
                                                        <strong><?php echo htmlspecialchars($user['username']); ?></strong>
                                                        (<?php echo htmlspecialchars($user['email']); ?>)
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="form-label"
                                                        for="extension_type_<?php echo $user['id']; ?>">Extension Type</label>
                                                    <select id="extension_type_<?php echo $user['id']; ?>" name="extension_type"
                                                        class="form-control">
                                                        <option value="from_now">From Current Date</option>
                                                        <option value="from_expiry">From Expiry Date</option>
                                                    </select>
                                                </div>

                                                <div class="form-group">
                                                    <label class="form-label"
                                                        for="extension_duration_<?php echo $user['id']; ?>">Extension
                                                        Duration</label>
                                                    <select id="extension_duration_<?php echo $user['id']; ?>"
                                                        name="extension_duration" class="form-control">
                                                        <option value="1">1 Month</option>
                                                        <option value="3">3 Months</option>
                                                        <option value="6">6 Months</option>
                                                        <option value="12">12 Months</option>
                                                    </select>
                                                </div>

                                                <div class="form-group">
                                                    <label class="form-label"
                                                        for="payment_status_<?php echo $user['id']; ?>">Payment Status</label>
                                                    <select id="payment_status_<?php echo $user['id']; ?>" name="payment_status"
                                                        class="form-control">
                                                        <option value="paid">Paid</option>
                                                        <option value="pending">Pending</option>
                                                        <option value="free">Free (Complimentary)</option>
                                                    </select>
                                                </div>

                                                <div class="form-group">
                                                    <label class="form-label"
                                                        for="extension_notes_<?php echo $user['id']; ?>">Notes</label>
                                                    <textarea id="extension_notes_<?php echo $user['id']; ?>" name="notes"
                                                        class="form-control" rows="3"></textarea>
                                                </div>

                                                <div class="form-group mt-4">
                                                    <button type="submit" class="btn btn-danger w-100">
                                                        <i class="fas fa-check"></i> Extend Subscription
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- Cancel Premium Modal -->
                                <div class="modal-backdrop" id="cancel-premium-<?php echo $user['id']; ?>">
                                    <div class="modal-dialog">
                                        <div class="modal-header">
                                            <h4 class="modal-title">Cancel Premium Subscription</h4>
                                            <button type="button" class="modal-close">&times;</button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Are you sure you want to cancel the premium subscription for
                                                <strong><?php echo htmlspecialchars($user['username']); ?></strong>?</p>
                                            <p>Current subscription is valid until: <strong>
                                                    <?php
                                                    echo isset($user['premium_until'])
                                                        ? date('M d, Y', strtotime($user['premium_until']))
                                                        : date('M d, Y', strtotime('+' . (rand(30, 365)) . ' days'));
                                                    ?>
                                                </strong></p>

                                            <div class="form-group">
                                                <div class="form-check">
                                                    <input type="checkbox" id="cancel_immediately_<?php echo $user['id']; ?>"
                                                        name="cancel_immediately" class="form-check-input">
                                                    <label for="cancel_immediately_<?php echo $user['id']; ?>">Cancel
                                                        immediately (don't wait until expiry)</label>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="form-label"
                                                    for="cancellation_reason_<?php echo $user['id']; ?>">Reason for
                                                    cancellation</label>
                                                <textarea id="cancellation_reason_<?php echo $user['id']; ?>"
                                                    name="cancellation_reason" class="form-control" rows="3"></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-outline modal-cancel">Keep
                                                Subscription</button>
                                            <form action="/admin/cancel-premium" method="POST" style="display:inline;">
                                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                <button type="submit" class="btn btn-danger">Cancel Subscription</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">No premium users found</td>
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