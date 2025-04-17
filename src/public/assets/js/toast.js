// Toast message function
function showToast(message, type = "success") {
    console.log("Showing toast:", message, type); // Debug log

    // Create toast container if it doesn't exist
    let toastContainer = document.getElementById("toast-container");
    if (!toastContainer) {
        toastContainer = document.createElement("div");
        toastContainer.id = "toast-container";
        document.body.appendChild(toastContainer);
    }

    // Create toast element
    const toast = document.createElement("div");
    toast.className = `toast toast-${type}`;
    toast.textContent = message;

    // Add toast to container
    toastContainer.appendChild(toast);

    // Show toast with animation
    setTimeout(() => {
        toast.classList.add("show");
    }, 10);

    // Remove toast after 3 seconds
    setTimeout(() => {
        toast.classList.remove("show");
        setTimeout(() => {
            if (toastContainer.contains(toast)) {
                toastContainer.removeChild(toast);
            }
            if (toastContainer.children.length === 0) {
                document.body.removeChild(toastContainer);
            }
        }, 300);
    }, 3000);
}

// Check for session messages on page load
document.addEventListener("DOMContentLoaded", function () {
    console.log("DOMContentLoaded event fired"); // Debug log

    // Check for success message
    const successMessage = document.getElementById("success-message");
    if (successMessage) {
        console.log("Success message found:", successMessage.textContent); // Debug log
        showToast(successMessage.textContent, "success");
    }

    // Check for error message
    const errorMessage = document.getElementById("error-message");
    if (errorMessage) {
        console.log("Error message found:", errorMessage.textContent); // Debug log
        showToast(errorMessage.textContent, "error");
    }
});
