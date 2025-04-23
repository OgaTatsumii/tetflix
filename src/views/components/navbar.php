<header class="main-header">
    <div class="container">
        <div class="header-content">
            <div class="header-left">
                <a href="/" class="logo">
                    <img src="/assets/images/netflix-logo.png" alt="Netflix">
                </a>
                <nav>
                    <ul class="nav-menu">
                        <li><a href="/" <?php echo $_SERVER['REQUEST_URI'] == '/' ? 'class="active"' : ''; ?>>Trang
                                chủ</a></li>
                        <li><a href="/movies"
                                <?php echo strpos($_SERVER['REQUEST_URI'], '/movies') === 0 ? 'class="active"' : ''; ?>>Phim
                                lẻ</a></li>
                        <li><a href="/series"
                                <?php echo strpos($_SERVER['REQUEST_URI'], '/series') === 0 ? 'class="active"' : ''; ?>>Phim
                                bộ</a></li>
                        <li><a href="/favorites"
                                <?php echo strpos($_SERVER['REQUEST_URI'], '/favorites') === 0 ? 'class="active"' : ''; ?>>Yêu
                                thích</a></li>
                    </ul>
                </nav>
            </div>

            <div class="header-right">
                <div class="search-box">
                    <form id="searchForm" onsubmit="return false;">
                        <input type="text" name="keyword" class="search-input" placeholder="Nhập tên phim cần tìm..."
                            autocomplete="off" value="<?php
                            if (strpos($_SERVER['REQUEST_URI'], '/search') === 0 && isset($_GET['keyword'])) {
                                echo htmlspecialchars($_GET['keyword']);
                            }
                            ?>">
                        <button type="button" class="search-btn">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
                <?php if (isset($_SESSION['user_id'])): ?>
                <div class="user-profile">
                    <a href="/profile" class="profile-link">
                        <?php if (isset($_SESSION['avatar_url']) && $_SESSION['avatar_url']): ?>
                        <img src="<?php echo htmlspecialchars($_SESSION['avatar_url']); ?>" alt="Ảnh đại diện"
                            class="user-avatar">
                        <?php else: ?>
                        <i class="fas fa-user"></i>
                        <?php endif; ?>
                        <span
                            class="user-name"><?php echo isset($_SESSION['fullname']) && $_SESSION['fullname'] ? htmlspecialchars($_SESSION['fullname']) : htmlspecialchars($_SESSION['username']); ?></span>
                    </a>
                    <a href="/logout" class="btn btn-danger btn-sm">Đăng xuất</a>
                </div>
                <?php else: ?>
                <a href="/login" class="btn btn-outline">Đăng nhập</a>
                <a href="/register" class="btn btn-danger">Đăng ký</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>

<style>
.user-profile {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.profile-link {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #fff;
    text-decoration: none;
    padding: 0.5rem;
    border-radius: 25px;
    background: rgba(255, 255, 255, 0.1);
    transition: all 0.3s ease;
}

.profile-link:hover {
    background: rgba(255, 255, 255, 0.2);
}

.user-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    object-fit: cover;
}

.profile-link i {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    font-size: 1rem;
}

.user-name {
    font-size: 0.95rem;
    font-weight: 500;
    margin-right: 0.5rem;
    max-width: 150px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.btn-sm {
    padding: 0.4rem 1rem;
    font-size: 0.9rem;
}

@media (max-width: 768px) {
    .user-name {
        display: none;
    }

    .profile-link {
        padding: 0.25rem;
    }
}
</style>