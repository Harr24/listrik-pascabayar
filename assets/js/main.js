// main.js

document.addEventListener('DOMContentLoaded', function() {

    // Fetch jumlah pengguna dan animasi count up
    fetch('jumlah_pengguna.php')
        .then(response => response.json())
        .then(data => {
            const counterEl = document.getElementById('jumlah-pengguna');
            const target = parseInt(data.total);
            let current = 0;

            const step = Math.ceil(target / 50);
            const interval = setInterval(() => {
                current += step;
                if (current >= target) {
                    current = target;
                    clearInterval(interval);
                }
                counterEl.textContent = current.toLocaleString('id-ID');
            }, 30);
        });

    // Ambil daftar layanan daya
    fetch('get_layanan.php')
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('layanan-list');
            container.innerHTML = '';
            data.layanan.forEach(daya => {
                const span = document.createElement('span');
                span.className = 'badge bg-dark fs-6';
                span.textContent = new Intl.NumberFormat('id-ID').format(daya) + ' VA';
                container.appendChild(span);
            });
        });

    // Background slider
    const slides = document.querySelectorAll('.hero-slide');
    let currentSlide = 0;
    if (slides.length > 0) {
        function nextSlide() {
            slides[currentSlide].classList.remove('active');
            currentSlide = (currentSlide + 1) % slides.length;
            slides[currentSlide].classList.add('active');
        }
        setInterval(nextSlide, 5000);
    }

    // AOS (jika diaktifkan)
    if (typeof AOS !== 'undefined') {
        AOS.init({
            duration: 1000,
            once: true,
            offset: 100,
        });
    }
});