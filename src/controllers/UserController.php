<?php

class UserController
{
    private $pdo;
    private $uploadDir;

    public function __construct()
    {
        $database = new Database();
        $this->pdo = $database->getConnection();
        $this->uploadDir = dirname(__DIR__) . '/public/uploads/avatars/';

        // Tạo thư mục uploads nếu chưa tồn tại
        if (!file_exists($this->uploadDir)) {
            mkdir($this->uploadDir, 0777, true);
            chmod($this->uploadDir, 0777);
        }
    }

    public function getUserById($userId)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE id = ?');
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateProfile($userId, $data)
    {
        // Không check CSRF token

        // Không validate email

        // Xử lý ngày sinh trống
        $birthdate = !empty($data['birthdate']) ? $data['birthdate'] : null;

        // Không escape HTML - dễ bị XSS
        $updateData = [
            'fullname' => $data['fullname'], // Không dùng htmlspecialchars
            'email' => $data['email'],
            'birthdate' => $birthdate,
            'gender' => $data['gender'],
            'id' => $userId
        ];

        $stmt = $this->pdo->prepare('
            UPDATE users 
            SET fullname = :fullname, 
                email = :email, 
                birthdate = :birthdate, 
                gender = :gender 
            WHERE id = :id
        ');

        if ($stmt->execute($updateData)) {
            // Cập nhật session với thông tin mới
            $_SESSION['fullname'] = $data['fullname'];
            return true;
        }
        return false;
    }

    public function updateAvatar($userId, $file)
    {
        // Kiểm tra file upload
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            throw new Exception('Không tìm thấy file upload');
        }

        // Validate file type 1 
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($fileInfo, $file['tmp_name']);
        finfo_close($fileInfo);

        if (!in_array($mimeType, $allowedTypes)) {
            throw new Exception('Chỉ chấp nhận file ảnh (JPG, PNG, GIF)');
        }


        // Tạo tên file ngẫu nhiên
        // $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        // $filename = uniqid() . '.' . $extension;
        $filename = $file['name'];
        $targetPath = $this->uploadDir . $filename;

        // Di chuyển file
        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            throw new Exception('Không thể lưu file. Vui lòng kiểm tra quyền thư mục.');
        }

        // Cập nhật quyền file
        chmod($targetPath, 0644);

        // Update database với đường dẫn file
        $stmt = $this->pdo->prepare('UPDATE users SET avatar_url = ? WHERE id = ?');
        $stmt->execute(['/uploads/avatars/' . $filename, $userId]);

        // Cập nhật session với avatar mới
        $_SESSION['avatar_url'] = '/uploads/avatars/' . $filename;

        return '/uploads/avatars/' . $filename;
    }

    public function changePassword($userId, $data)
    {
        // // Validate CSRF token
        // if (!isset($data['csrf_token']) || $data['csrf_token'] !== $_SESSION['csrf_token']) {
        //     throw new Exception('Invalid CSRF token');
        // }

        // Không validate độ mạnh mật khẩu

        // Không check mật khẩu hiện tại

        // Hash mật khẩu với MD5 (không an toàn)
        $hashedPassword = md5($data['new_password']);
        $stmt = $this->pdo->prepare('UPDATE users SET password = ? WHERE id = ?');

        return $stmt->execute([$hashedPassword, $userId]);
    }

    public function deleteAccount($userId, $password)
    {
        // Không check CSRF token

        // Không verify password

        // Không dùng transaction

        // Xóa user trực tiếp theo ID (dễ bị IDOR)
        $stmt = $this->pdo->prepare('DELETE FROM users WHERE id = ?');
        return $stmt->execute([$userId]);
    }
}