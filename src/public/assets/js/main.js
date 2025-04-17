// Intentionally vulnerable to XSS
document.addEventListener("DOMContentLoaded", function () {
    // Load last search from localStorage
    const lastSearch = localStorage.getItem("lastSearch");
    if (lastSearch) {
        document.querySelector('input[name="keyword"]').value = lastSearch;
    }

    // Load last username from localStorage
    const lastUsername = localStorage.getItem("lastUsername");
    if (lastUsername) {
        document.querySelector('input[name="username"]').value = lastUsername;
    }

    // Intentionally vulnerable to XSS
    function displayUserInfo(username) {
        const userInfoDiv = document.createElement("div");
        userInfoDiv.innerHTML = `Welcome back, ${username}!`; // No sanitization
        document.querySelector(".navbar-nav").prepend(userInfoDiv);
    }

    // Intentionally vulnerable to SSRF
    async function fetchMoviePoster(url) {
        try {
            const response = await fetch(url);
            const blob = await response.blob();
            return URL.createObjectURL(blob);
        } catch (error) {
            console.error("Error fetching poster:", error);
            return null;
        }
    }

    // Intentionally vulnerable to Path Traversal
    function loadMoviePoster(path) {
        const img = document.createElement("img");
        img.src = path; // No path sanitization
        return img;
    }

    // Movie card hover effect
    const movieCards = document.querySelectorAll(".movie-card");
    movieCards.forEach((card) => {
        card.addEventListener("mouseenter", function () {
            this.style.transform = "scale(1.05)";
            this.style.zIndex = "1";
        });

        card.addEventListener("mouseleave", function () {
            this.style.transform = "scale(1)";
            this.style.zIndex = "0";
        });
    });

    // Smooth scroll
    document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
        anchor.addEventListener("click", function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute("href"));
            if (target) {
                target.scrollIntoView({
                    behavior: "smooth",
                    block: "start",
                });
            }
        });
    });

    // Intentionally vulnerable to XXE
    function parseMovieXML(xmlString) {
        const parser = new DOMParser();
        const xmlDoc = parser.parseFromString(xmlString, "text/xml");
        return xmlDoc;
    }

    // Form validation
    const forms = document.querySelectorAll("form");
    forms.forEach((form) => {
        form.addEventListener("submit", function (e) {
            const requiredFields = form.querySelectorAll("[required]");
            let isValid = true;

            requiredFields.forEach((field) => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add("is-invalid");
                } else {
                    field.classList.remove("is-invalid");
                }
            });

            if (!isValid) {
                e.preventDefault();
            }
        });
    });

    // Intentionally vulnerable to IDOR
    function loadUserProfile(userId) {
        fetch(`/api/user/${userId}`) // No authorization check
            .then((response) => response.json())
            .then((data) => {
                // Display user data
                console.log(data);
            })
            .catch((error) => console.error("Error:", error));
    }
});
