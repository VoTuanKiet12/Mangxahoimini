document.addEventListener("DOMContentLoaded", function () {
    const counters = document.querySelectorAll(".dashboard-card strong");

    counters.forEach(counter => {
        const target = +counter.innerText;
        counter.innerText = "0";
        let count = 0;
        const increment = target / 50; // càng lớn càng nhanh
        const updateCounter = () => {
            count += increment;
            if (count < target) {
                counter.innerText = Math.ceil(count);
                requestAnimationFrame(updateCounter);
            } else {
                counter.innerText = target;
            }
        };
        updateCounter();
    });
});
