<?php
// Đảm bảo đây là dòng đầu tiên trong file
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: /');
    exit();
}

// Include the database connection
require_once BASE_PATH . '/config/database.php';

// Include necessary controllers and models
require_once BASE_PATH . '/models/Movie.php';
require_once BASE_PATH . '/models/User.php';

// Create instances of models
$movieModel = new Movie($pdo);
$userModel = new User($pdo);

// Get movie and user stats
$totalMovies = $movieModel->getMovieCount();
$totalUsers = $userModel->getUserCount();
$premiumUsers = $userModel->getPremiumUserCount();

// Get current page from URL
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Netflix Clone</title>
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Main CSS -->
    <link rel="stylesheet" href="/assets/css/style.css">
    <!-- Admin CSS -->
    <link rel="stylesheet" href="/assets/css/admin.css">
</head>

<style>
    body::-webkit-scrollbar {
        display: none;
    }

    .admin-sidebar {
        overflow-y: scroll;
        scrollbar-width: none;
        -ms-overflow-style: none;
    }

    .admin-sidebar::-webkit-scrollbar {
        display: none;
    }
</style>

<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <div class="admin-sidebar">
            <div class="sidebar-header">
                <div class="sidebar-logo">
                    <i class="fas fa-video"></i>
                    <span>Admin Panel</span>
                </div>
            </div>

            <ul class="sidebar-menu">
                <li>
                    <a href="/admin-panel?page=dashboard" class="<?php echo $page === 'dashboard' ? 'active' : ''; ?>">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                </li>

                <div class="sidebar-section">
                    <div class="sidebar-section-title">Movie Management</div>
                    <li>
                        <a href="/admin-panel?page=movies" class="<?php echo $page === 'movies' ? 'active' : ''; ?>">
                            <i class="fas fa-film"></i> All Movies
                        </a>
                    </li>
                    <li>
                        <a href="/admin-panel?page=add-movie"
                            class="<?php echo $page === 'add-movie' ? 'active' : ''; ?>">
                            <i class="fas fa-plus"></i> Add New Movie
                        </a>
                    </li>
                    <li>
                        <a href="/admin-panel?page=categories"
                            class="<?php echo $page === 'categories' ? 'active' : ''; ?>">
                            <i class="fas fa-tags"></i> Categories
                        </a>
                    </li>
                    <li>
                        <a href="/admin-panel?page=episodes"
                            class="<?php echo $page === 'episodes' ? 'active' : ''; ?>">
                            <i class="fas fa-list-ol"></i> Manage Episodes
                        </a>
                    </li>
                </div>

                <div class="sidebar-section">
                    <div class="sidebar-section-title">User Management</div>
                    <li>
                        <a href="/admin-panel?page=users" class="<?php echo $page === 'users' ? 'active' : ''; ?>">
                            <i class="fas fa-users"></i> All Users
                        </a>
                    </li>
                    <li>
                        <a href="/admin-panel?page=premium" class="<?php echo $page === 'premium' ? 'active' : ''; ?>">
                            <i class="fas fa-crown"></i> Premium Users
                        </a>
                    </li>
                </div>

                <div class="sidebar-section">
                    <div class="sidebar-section-title">Settings</div>
                    <li>
                        <a href="/admin-panel?page=settings"
                            class="<?php echo $page === 'settings' ? 'active' : ''; ?>">
                            <i class="fas fa-cog"></i> Site Settings
                        </a>
                    </li>
                    <li>
                        <a href="/logout">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </li>
                </div>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="admin-content">
            <!-- Header -->
            <header class="admin-header">
                <h2 class="content-title">
                    <?php
                    switch ($page) {
                        case 'dashboard':
                            echo 'Dashboard';
                            break;
                        case 'movies':
                            echo 'All Movies';
                            break;
                        case 'add-movie':
                            echo 'Add New Movie';
                            break;
                        case 'categories':
                            echo 'Categories';
                            break;
                        case 'episodes':
                            echo 'Manage Episodes';
                            break;
                        case 'users':
                            echo 'User Management';
                            break;
                        case 'premium':
                            echo 'Premium Users';
                            break;
                        case 'settings':
                            echo 'Site Settings';
                            break;
                        default:
                            echo 'Dashboard';
                    }
                    ?>
                </h2>
                <div class="header-profile">
                    <span>Welcome, Admin</span>
                </div>
            </header>

            <!-- Content Area -->
            <?php
            // Include the appropriate page content based on the 'page' parameter
            switch ($page) {
                case 'dashboard':
                    include 'admin/dashboard.php';
                    break;
                case 'movies':
                    include 'admin/movies.php';
                    break;
                case 'add-movie':
                    include 'admin/add-movie.php';
                    break;
                case 'edit-movie':
                    include 'admin/edit-movie.php';
                    break;
                case 'categories':
                    include 'admin/categories.php';
                    break;
                case 'episodes':
                    include 'admin/episodes.php';
                    break;
                case 'users':
                    include 'admin/users.php';
                    break;
                case 'premium':
                    include 'admin/premium-users.php';
                    break;
                case 'settings':
                    include 'admin/settings.php';
                    break;
                default:
                    include 'admin/dashboard.php';
            }
            ?>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            // Open modal
            $('.open-modal').on('click', function (e) {
                e.preventDefault();
                const target = $(this).data('target');
                $(target).addClass('show');
            });

            // Close modal
            $('.modal-close, .modal-cancel').on('click', function () {
                $('.modal-backdrop').removeClass('show');
            });

            // Tab functionality
            $('.tab-link').on('click', function (e) {
                e.preventDefault();
                const tab = $(this).data('tab');

                $('.tab-link').removeClass('active');
                $(this).addClass('active');

                $('.tab-content').removeClass('active');
                $('#' + tab).addClass('active');
            });

            // File upload preview
            $('.file-upload input[type="file"]').on('change', function () {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        $('.file-preview').html(`
                            <div class="preview-item">
                                <img src="${e.target.result}" alt="Preview">
                                <span class="preview-remove"><i class="fas fa-times"></i></span>
                            </div>
                        `);
                    }
                    reader.readAsDataURL(file);
                }
            });

            // Remove file preview
            $(document).on('click', '.preview-remove', function () {
                $(this).parent().remove();
                $('.file-upload input[type="file"]').val('');
            });
        });
    </script>
</body>

</html>