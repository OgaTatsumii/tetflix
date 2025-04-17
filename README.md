# Dự án Netflix Clone

Đây là dự án mô phỏng trang web Netflix, được phát triển bằng PHP với mục đích học tập.

## Tính năng

-   Đăng ký, đăng nhập và quản lý tài khoản người dùng
-   Xem danh sách phim, phim lẻ và phim bộ
-   Tính năng yêu thích phim
-   Bình luận về phim
-   Hệ thống người dùng premium
-   Quản lý danh sách phim (Admin)

## Cài đặt

1. Clone dự án về máy của bạn:

```
git clone https://github.com/OgaTatsumii/tetflix.git
```

2. Import database từ file `database.sql`

3. Cấu hình kết nối database trong file `src/config/database.php`

4. Chạy dự án trên máy chủ web của bạn (Apache, Nginx...)

## Cấu trúc dự án

```
src/
├── config/         # Cấu hình database, constants
├── controllers/    # Xử lý logic
├── models/         # Tương tác với database
├── public/         # Assets (CSS, JS, images)
│   └── uploads/    # Nơi lưu trữ file upload
└── views/          # Giao diện người dùng
    ├── admin/      # Giao diện quản trị
    └── components/ # Các thành phần giao diện tái sử dụng
```

## Công nghệ sử dụng

-   PHP
-   MySQL
-   HTML/CSS
-   JavaScript
-   Font Awesome (icons)

## Người đóng góp

-   OgaTatsumii
