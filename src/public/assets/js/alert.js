document.addEventListener("DOMContentLoaded", function () {
    const alerts = document.querySelectorAll(".alert");

    // Xử lý nút đóng alert
    document.querySelectorAll(".alert-close").forEach((button) => {
        button.addEventListener("click", function () {
            const alert = this.closest(".alert");
            hideAlert(alert);
        });
    });

    // Tự động ẩn alerts sau 2 giây
    alerts.forEach((alert) => {
        setTimeout(() => {
            hideAlert(alert);
        }, 2000);
    });

    function hideAlert(alert) {
        alert.classList.add("hide");
        setTimeout(() => {
            if (alert.parentElement) {
                alert.parentElement.removeChild(alert);
            }
        }, 500);
    }
});
