// theme.js
function applyTheme() {
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'dark') {
        document.body.classList.add('dark-mode');
        document.documentElement.classList.add('dark-mode');
        const btn = document.getElementById('toggle-dark');
        if (btn) btn.textContent = 'Chế độ sáng';
    }
}

document.addEventListener('DOMContentLoaded', () => {
    applyTheme();

    const toggleBtn = document.getElementById('toggle-dark');
    if (toggleBtn) {
        toggleBtn.addEventListener('click', function () {
            document.body.classList.toggle('dark-mode');
            document.documentElement.classList.toggle('dark-mode');

            const isDark = document.body.classList.contains('dark-mode');
            this.textContent = isDark ? 'Chế độ sáng' : 'Chế độ tối';
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
        });
    }
});
