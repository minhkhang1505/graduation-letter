document.addEventListener("DOMContentLoaded", () => {
    const googleButton = document.querySelector(
        ".g_id_signin"
    );

    if (googleButton) {
        googleButton.style.opacity = "0";
        googleButton.style.transition = "opacity 0.3s";

        const observer = new MutationObserver(() => {
            const iframe = googleButton.querySelector("iframe");

            if (iframe) {
                googleButton.style.opacity = "1";
                observer.disconnect();
            }
        });

        observer.observe(googleButton, {
            childList: true,
            subtree: true
        });

        setTimeout(() => {
            googleButton.style.opacity = "1";
            observer.disconnect();
        }, 5000);
    }
});