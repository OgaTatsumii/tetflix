<?php
// Bắt đầu output buffering
ob_start();

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit;
}

// Lấy thông tin user
$user = $userController->getUserById($_SESSION['user_id']);

// Debug để kiểm tra
// var_dump($_SESSION['user_id']);
// var_dump($user);
?>

<div class="profile-container">
    <div class="profile-header">
        <h1>Thông tin tài khoản</h1>
        <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?php echo $_SESSION['success']; ?></div>
        <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?php echo $_SESSION['error']; ?></div>
        <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
    </div>

    <div class="profile-content">
        <div class="profile-sidebar">
            <div class="avatar-section">
                <div class="avatar-container">
                    <img src="<?php
                    if (isset($user['avatar_url']) && $user['avatar_url']) {
                        echo ltrim(htmlspecialchars($user['avatar_url']), '/');
                    } else {
                        echo 'assets/images/netflix-logo.png';
                    }
                    ?>" alt="Avatar" class="profile-avatar">
                    <div class="avatar-overlay">
                        <form action="/profile/update-avatar" method="POST" enctype="multipart/form-data"
                            id="avatar-form">
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                            <input type="file" name="avatar" id="avatar-input" accept="image/*" class="hidden">
                            <button type="button" class="btn btn-light btn-sm"
                                onclick="document.getElementById('avatar-input').click()">
                                <i class="fas fa-camera"></i> Thay đổi
                            </button>
                        </form>
                    </div>
                </div>
                <h2 class="profile-name"><?php echo htmlspecialchars($user['fullname'] ?? 'User'); ?></h2>
            </div>

            <div class="account-info">
                <div class="info-item">
                    <i class="fas fa-user-tag"></i>
                    <div class="info-content">
                        <label>Vai trò</label>
                        <span><?php echo ucfirst($user['role'] ?? 'user'); ?></span>
                    </div>
                </div>
                <div class="info-item">
                    <i class="fas fa-calendar-alt"></i>
                    <div class="info-content">
                        <label>Ngày tạo</label>
                        <span><?php echo isset($user['created_at']) ? date('d/m/Y', strtotime($user['created_at'])) : 'N/A'; ?></span>
                    </div>
                </div>
                <div class="info-item">
                    <i class="fas fa-circle"></i>
                    <div class="info-content">
                        <label>Trạng thái</label>
                        <span
                            class="badge <?php echo isset($user['is_active']) && $user['is_active'] ? 'badge-success' : 'badge-danger'; ?>">
                            <?php echo isset($user['is_active']) && $user['is_active'] ? 'Hoạt động' : 'Bị khóa'; ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="profile-main">
            <div class="profile-section">
                <h3><i class="fas fa-user-edit"></i> Thông tin cá nhân</h3>
                <form action="/profile/update" method="POST" class="profile-form" id="profile-form">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">

                    <div class="form-group">
                        <label for="fullname">Họ và tên</label>
                        <input type="text" id="fullname" name="fullname"
                            value="<?php echo htmlspecialchars($user['fullname'] ?? ''); ?>" class="form-control"
                            required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email"
                            value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" class="form-control" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="birthdate">Ngày sinh</label>
                            <input type="date" id="birthdate" name="birthdate"
                                value="<?php echo $user['birthdate'] ?? ''; ?>" class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="gender">Giới tính</label>
                            <select id="gender" name="gender" class="form-control">
                                <option value="">Chọn giới tính</option>
                                <option value="male"
                                    <?php echo isset($user['gender']) && $user['gender'] === 'male' ? 'selected' : ''; ?>>
                                    Nam</option>
                                <option value="female"
                                    <?php echo isset($user['gender']) && $user['gender'] === 'female' ? 'selected' : ''; ?>>
                                    Nữ</option>
                                <option value="other"
                                    <?php echo isset($user['gender']) && $user['gender'] === 'other' ? 'selected' : ''; ?>>
                                    Khác</option>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Lưu thay đổi
                    </button>
                </form>
            </div>

            <div class="profile-section">
                <h3><i class="fas fa-lock"></i> Đổi mật khẩu</h3>
                <form action="/profile/change-password" method="POST" class="password-form" id="password-form">
                    <!-- <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>"> -->

                    <div class="form-group">
                        <label for="current_password">Mật khẩu hiện tại</label>
                        <div class="password-input">
                            <input type="password" id="current_password" name="current_password" class="form-control"
                                required>
                            <button type="button" class="toggle-password">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="new_password">Mật khẩu mới</label>
                        <div class="password-input">
                            <input type="password" id="new_password" name="new_password" class="form-control" required>
                            <button type="button" class="toggle-password">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">Xác nhận mật khẩu mới</label>
                        <div class="password-input">
                            <input type="password" id="confirm_password" name="confirm_password" class="form-control"
                                required>
                            <button type="button" class="toggle-password">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-key"></i> Đổi mật khẩu
                    </button>
                </form>
            </div>

            <div class="profile-section danger-zone">
                <h3><i class="fas fa-exclamation-triangle"></i> Xóa tài khoản</h3>
                <div class="warning-box">
                    <p class="warning-text">
                        <i class="fas fa-exclamation-circle"></i>
                        Cảnh báo: Hành động này không thể hoàn tác! Tất cả dữ liệu của bạn sẽ bị xóa vĩnh viễn.
                    </p>
                </div>
                <form action="/profile/delete" method="POST" id="delete-account-form"
                    onsubmit="return confirm('Bạn có chắc chắn muốn xóa tài khoản? Hành động này không thể hoàn tác!');">
                    <!-- <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>"> -->
                    <div class="form-group">
                        <label for="delete_password">Nhập mật khẩu để xác nhận</label>
                        <div class="password-input">
                            <input type="password" id="delete_password" name="delete_password" class="form-control"
                                required>
                            <button type="button" class="toggle-password">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash-alt"></i> Xóa tài khoản
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Xử lý upload avatar
document.getElementById('avatar-input').addEventListener('change', function() {
    if (this.files && this.files[0]) {
        document.getElementById('avatar-form').submit();
    }
});

// Toggle password visibility
document.querySelectorAll('.toggle-password').forEach(button => {
    button.addEventListener('click', function() {
        const input = this.parentElement.querySelector('input');
        const icon = this.querySelector('i');

        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    });
});

// Validate password match
document.getElementById('password-form').addEventListener('submit', function(e) {
    const newPass = document.getElementById('new_password').value;
    const confirmPass = document.getElementById('confirm_password').value;

    if (newPass !== confirmPass) {
        e.preventDefault();
        alert('Mật khẩu xác nhận không khớp!');
    }
});
</script>

<?php
$content = ob_get_clean();
require 'layout.php';
?>