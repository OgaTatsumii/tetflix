<footer class="main-footer">
    <div class="container">
        <div class="footer-content">
            <div class="footer-section">
                <h3>Về Netflix Clone</h3>
                <p class="footer-description">
                    Netflix Clone là dự án xem phim trực tuyến với kho phim đa dạng,
                    chất lượng cao và trải nghiệm người dùng tuyệt vời.
                </p>
                <div class="social-links">
                    <a href="#" class="social-link" title="Facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" class="social-link" title="Twitter">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="#" class="social-link" title="Instagram">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="#" class="social-link" title="Youtube">
                        <i class="fab fa-youtube"></i>
                    </a>
                </div>
            </div>

            <div class="footer-section">
                <h3>Liên kết</h3>
                <ul class="footer-links">
                    <li><a href="/movies">Phim lẻ</a></li>
                    <li><a href="/series">Phim bộ</a></li>
                    <li><a href="/trending">Thịnh hành</a></li>
                    <li><a href="/premium">Gói Premium</a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h3>Hỗ trợ</h3>
                <ul class="footer-links">
                    <li><a href="/faq">FAQ</a></li>
                    <li><a href="/contact">Liên hệ</a></li>
                    <li><a href="/terms">Điều khoản sử dụng</a></li>
                    <li><a href="/privacy">Chính sách bảo mật</a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h3>Tải ứng dụng</h3>
                <div class="app-links">
                    <a href="#" class="app-link" title="Download on App Store">
                        <i class="fab fa-apple"></i>
                    </a>
                    <a href="#" class="app-link" title="Get it on Google Play">
                        <i class="fab fa-google-play"></i>
                    </a>
                </div>
            </div>
        </div>


    </div>
</footer>

<style>
.main-footer {
    background-color: #141414;
    color: #fff;
    padding: 4rem 0 2rem;
    margin-top: 4rem;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.footer-content {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 8rem;
    margin-bottom: 3rem;
}

.footer-section h3 {
    color: #fff;
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 1.5rem;
}

.footer-description {
    width: 475px;
    color: #999;
    font-size: 0.95rem;
    line-height: 1.6;
    margin-bottom: 1.5rem;
}

.social-links {
    display: flex;
    gap: 1rem;
}

.social-link {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background-color: rgba(255, 255, 255, 0.1);
    color: #fff;
    transition: all 0.3s ease;
}

.social-link:hover {
    background-color: var(--netflix-red);
    transform: translateY(-2px);
}

.footer-links {
    list-style: none;
    padding: 0;
    margin: 0;
}

.footer-links li {
    margin-bottom: 0.8rem;
}

.footer-links a {
    color: #999;
    text-decoration: none;
    font-size: 0.95rem;
    transition: color 0.3s ease;
}

.footer-links a:hover {
    color: #fff;
}

.app-links {
    display: flex;
    gap: 1rem;
    margin-top: 1rem;
}

.app-link {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background-color: rgba(255, 255, 255, 0.1);
    color: #fff;
    text-decoration: none;
    transition: all 0.3s ease;
}

.app-link:hover {
    background-color: var(--netflix-red);
    transform: translateY(-2px);
}

.app-link i {
    font-size: 1.2rem;
}

.footer-bottom {
    padding-top: 2rem;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.copyright {
    color: #999;
    font-size: 0.9rem;
}

.footer-bottom-links {
    display: flex;
    gap: 2rem;
}

.footer-bottom-links a {
    color: #999;
    text-decoration: none;
    font-size: 0.9rem;
    transition: color 0.3s ease;
}

.footer-bottom-links a:hover {
    color: #fff;
}

@media (max-width: 1024px) {
    .footer-content {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .main-footer {
        padding: 3rem 0 1.5rem;
    }

    .footer-content {
        grid-template-columns: 1fr;
        gap: 2.5rem;
    }

    .footer-bottom {
        flex-direction: column;
        text-align: center;
        gap: 1rem;
    }

    .footer-bottom-links {
        justify-content: center;
    }
}
</style>