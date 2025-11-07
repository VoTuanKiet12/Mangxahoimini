

document.addEventListener("DOMContentLoaded", function () {
    // Sidebar Right
    const toggleRight = document.getElementById("sidebarToggle");
    const sidebarRight = document.getElementById("sidebarRight");

    if (toggleRight && sidebarRight) {
        toggleRight.addEventListener("click", () => {
            sidebarRight.classList.toggle("active");
        });
    }

    // Sidebar Left
    const toggleLeft = document.getElementById("sidebarToggleLeft");
    const sidebarLeft = document.getElementById("sidebarLeft");

    if (toggleLeft && sidebarLeft) {
        toggleLeft.addEventListener("click", () => {
            sidebarLeft.classList.toggle("active");
        });
    }
});

