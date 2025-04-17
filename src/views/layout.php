<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Netflix Clone</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/alert.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
    :root {
        --netflix-red: #dc3545;
        --netflix-red-hover: #c82333;
        --netflix-black: #141414;
        --netflix-dark: #000000;
        --netflix-gray: #333;
        --netflix-light-gray: #454545;
        --netflix-white: #ffffff;
    }

    a {
        text-decoration: none;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Netflix Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif;
        background-color: var(--netflix-black);
        color: var(--netflix-white);
        line-height: 1.5;
    }

    body::-webkit-scrollbar {
        display: none;
    }



    .main-header {
        background-color: var(--netflix-black);
        padding: 0.75rem 0;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 1000;
        transition: all 0.3s ease;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .main-header {
        transition: transform 0.3s ease, background-color 0.3s ease;
    }


    .main-header.scrolled {
        background-color: rgba(20, 20, 20, 0.95);
        backdrop-filter: blur(8px);
    }

    .container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 0 2rem;
    }

    .header-content {
        display: flex;
        align-items: center;
        justify-content: space-between;
        height: 3.5rem;
    }

    .header-left {
        display: flex;
        align-items: center;
        gap: 3rem;
    }

    .logo img {
        margin-top: 10px;
        height: 88px;
    }

    .nav-menu {
        display: flex;
        align-items: center;
        gap: 2rem;
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .nav-menu a {
        color: var(--netflix-white);
        text-decoration: none;
        font-size: 0.95rem;
        font-weight: 500;
        opacity: 0.85;
        transition: all 0.2s ease;
    }

    .nav-menu a:hover,
    .nav-menu a.active {
        opacity: 1;
        color: var(--netflix-red);
    }

    .header-right {
        display: flex;
        align-items: center;
        gap: 1.5rem;
    }

    .search-box {
        position: relative;
        width: 260px;
        height: 2.5rem;
    }

    .search-input {
        width: 260px;
        height: 100%;
        padding: 0 2.75rem 0 1rem;
        background-color: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 4px;
        color: var(--netflix-white);
        font-size: 0.95rem;
        transition: width 0.3s ease;
        position: absolute;
        right: 0;
        top: 0;
    }

    .search-input:focus {
        background-color: var(--netflix-gray);
        border-color: var(--netflix-red);
        outline: none;
        width: 300px;
    }

    .search-btn {
        position: absolute;
        right: 0;
        top: 0;
        height: 100%;
        width: 2.75rem;
        border: none;
        background: transparent;
        color: var(--netflix-white);
        cursor: pointer;
        opacity: 0.7;
        transition: opacity 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .search-btn:hover {
        opacity: 1;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 2.5rem;
        padding: 0.5rem 1.25rem;
        border: none;
        border-radius: 4px;
        font-size: 0.95rem;
        font-weight: 500;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-outline {
        border: 1px solid rgba(255, 255, 255, 0.5);
        color: var(--netflix-white);
        background: transparent;
    }

    .btn-outline:hover {
        border-color: var(--netflix-white);
        background: rgba(255, 255, 255, 0.1);
    }

    .btn-danger {
        background: var(--netflix-red);
        color: var(--netflix-white);
    }

    .btn-danger:hover {
        background: var(--netflix-red-hover);
        transform: translateY(-1px);
    }

    main {
        padding-top: 5rem;
        min-height: calc(100vh - 5rem);
    }


    /* ... existing code ... */

    /* Tùy chỉnh giao diện cho select box */
    select.form-control {
        appearance: none;
        /* Loại bỏ giao diện mặc định */
        -webkit-appearance: none;
        -moz-appearance: none;
        background-color: #333;
        /* Màu nền tối */
        color: #fff;
        /* Màu chữ trắng */
        border: 1px solid #555;
        /* Viền tối hơn */
        padding: 10px 15px;
        /* Tăng padding */
        border-radius: 5px;
        /* Bo góc */
        cursor: pointer;
        background-image: url('data:image/svg+xml;utf8,<svg fill="%23ffffff" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M7 10l5 5 5-5z"/><path d="M0 0h24v24H0z" fill="none"/></svg>');
        /* Mũi tên dropdown tùy chỉnh (màu trắng) */
        background-repeat: no-repeat;
        background-position: right 10px center;
        background-size: 16px;
        /* Kích thước mũi tên */
    }

    /* Style khi hover */
    select.form-control:hover {
        border-color: #777;
    }

    /* Style khi focus */
    select.form-control:focus {
        border-color: #e50914;
        /* Màu đỏ Netflix khi focus */
        box-shadow: 0 0 0 0.2rem rgba(229, 9, 20, 0.25);
        /* Thêm hiệu ứng bóng */
        outline: none;
    }

    /* Tùy chỉnh màu nền của option trong dropdown */
    select.form-control option {
        background-color: #333;
        color: #fff;
    }
    </style>
</head>

<body>
    <?php require_once __DIR__ . '/components/navbar.php'; ?>

    <main>
        <?php echo $content; ?>
    </main>

    <?php require_once __DIR__ . '/components/footer.php'; ?>

    <script src="/assets/js/alert.js"></script>
    <script src="/assets/js/main.js"></script>

    <script>
    // Hiển thị thông báo toast nếu có
    document.addEventListener('DOMContentLoaded', function() {
        // Kiểm tra thông báo thành công
        const successMessage = document.getElementById('success-message');
        if (successMessage) {
            showToast(successMessage.textContent, 'success');
        }

        // Kiểm tra thông báo lỗi
        const errorMessage = document.getElementById('error-message');
        if (errorMessage) {
            showToast(errorMessage.textContent, 'error');
        }
    });

    // Hiệu ứng cuộn header
    let lastScrollTop = 0;
    const header = document.querySelector('.main-header');

    window.addEventListener('scroll', function() {
        const currentScroll = window.scrollY;

        if (currentScroll > lastScrollTop && currentScroll > 100) {
            // Cuộn xuống: ẩn header
            header.style.transform = 'translateY(-100%)';
        } else {
            // Cuộn lên: hiện lại header
            header.style.transform = 'translateY(0)';
            header.classList.toggle('scrolled', currentScroll > 50);
        }

        lastScrollTop = currentScroll <= 0 ? 0 : currentScroll;
    });


    // Xử lý tìm kiếm
    const searchInput = document.querySelector('.search-input');
    const searchBtn = document.querySelector('.search-btn');

    function handleSearch() {
        const searchTerm = searchInput.value.trim();
        if (searchTerm) {
            window.location.href = `/search?q=${encodeURIComponent(searchTerm)}`;
        }
    }

    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            handleSearch();
        }
    });

    searchBtn.addEventListener('click', handleSearch);
    </script>
</body>

</html>