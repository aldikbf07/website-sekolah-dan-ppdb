/**
 * Retro Main JavaScript
 * MI Muhammadiyah Bojongsana
 */

document.addEventListener('DOMContentLoaded', function() {
    initNavbar();
    initHeroCarousel();
    initSmoothScroll();
    initBackToTop();
    addHoverEffects();
});

/**
 * Navbar scroll behavior
 */
function initNavbar() {
    const navbar = document.getElementById('mainNavbar');
    if (!navbar) return;
    
    window.addEventListener('scroll', function() {
        if (window.scrollY > 50) {
            navbar.style.boxShadow = '0 4px 20px rgba(0,0,0,0.2)';
        } else {
            navbar.style.boxShadow = '0 4px 0px rgba(0,0,0,0.2)';
        }
    });
}

/**
 * Hero Carousel
 */
function initHeroCarousel() {
    const heroCarousel = document.getElementById('heroCarousel');
    if (!heroCarousel) return;
    
    const carousel = new bootstrap.Carousel(heroCarousel, {
        interval: 4000,
        ride: 'carousel'
    });
    
    // Pause on hover
    heroCarousel.addEventListener('mouseenter', () => carousel.pause());
    heroCarousel.addEventListener('mouseleave', () => carousel.cycle());
}

/**
 * Smooth scroll for anchor links
 */
function initSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href === '#') return;
            
            e.preventDefault();
            const target = document.querySelector(href);
            
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
}

/**
 * Back to top button
 */
function initBackToTop() {
    const btn = document.createElement('button');
    btn.innerHTML = '<i class="fa-solid fa-arrow-up" style="color: rgb(0, 0, 0);"></i>';
    btn.className = 'btn-retro btn-yellow';
    btn.style.cssText = `
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 999;
        display: none;
        width: 50px;
        height: 50px;
        padding: 0;
        border-radius: 50%;
        font-size: 1.2rem;
        justify-content: center;
    `;
    document.body.appendChild(btn);
    
    window.addEventListener('scroll', function() {
        btn.style.display = window.scrollY > 300 ? 'flex' : 'none';
    });
    
    btn.addEventListener('click', function() {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
}

/**
 * Random hover effects
 */
function addHoverEffects() {
    document.querySelectorAll('.card-retro, .berita-card, .guru-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            const rotation = Math.random() * 2 - 1;
            this.style.transform = `translateY(-5px) rotate(${rotation}deg)`;
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = '';
        });
    });
}


/**
 * Prestasi Horizontal Scroll
 */
function scrollPrestasi(amount) {
    const container = document.getElementById('prestasiScroll');
    if (container) {
        container.scrollBy({
            left: amount,
            behavior: 'smooth'
        });
        updateScrollIndicators();
    }
}

function goToSlide(index) {
    const container = document.getElementById('prestasiScroll');
    if (container) {
        const cardWidth = container.querySelector('.prestasi-card-horizontal').offsetWidth;
        const gap = 25; // gap antar card
        const scrollTo = index * (cardWidth + gap) * 3; // 3 card per slide
        container.scrollTo({
            left: scrollTo,
            behavior: 'smooth'
        });
        updateScrollIndicators();
    }
}

function updateScrollIndicators() {
    const container = document.getElementById('prestasiScroll');
    const dots = document.querySelectorAll('#scrollIndicators .scroll-dot');
    
    if (container && dots.length > 0) {
        const scrollLeft = container.scrollLeft;
        const cardWidth = container.querySelector('.prestasi-card-horizontal').offsetWidth;
        const gap = 25;
        const slideWidth = (cardWidth + gap) * 3;
        const currentSlide = Math.round(scrollLeft / slideWidth);
        
        dots.forEach((dot, index) => {
            dot.classList.toggle('active', index === currentSlide);
        });
    }
}

// Inisialisasi saat halaman dimuat
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('prestasiScroll');
    
    if (container) {
        // Update indikator saat scroll
        container.addEventListener('scroll', updateScrollIndicators);
        
        // Touch/swipe support
        let isDown = false;
        let startX;
        let scrollLeft;
        
        container.addEventListener('mousedown', (e) => {
            isDown = true;
            container.style.cursor = 'grabbing';
            startX = e.pageX - container.offsetLeft;
            scrollLeft = container.scrollLeft;
        });
        
        container.addEventListener('mouseleave', () => {
            isDown = false;
            container.style.cursor = 'grab';
        });
        
        container.addEventListener('mouseup', () => {
            isDown = false;
            container.style.cursor = 'grab';
        });
        
        container.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - container.offsetLeft;
            const walk = (x - startX) * 2;
            container.scrollLeft = scrollLeft - walk;
        });
        
        // Keyboard navigation
        container.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowLeft') {
                scrollPrestasi(-350);
            } else if (e.key === 'ArrowRight') {
                scrollPrestasi(350);
            }
        });
        
        // Update indikator awal
        updateScrollIndicators();
    }
});


/**
 * Buka Modal Fasilitas
 */
function openFasilitasModal(element) {
    var src = element.getAttribute('data-src');
    var title = element.getAttribute('data-title');
    var desc = element.getAttribute('data-desc');
    
    var modal = document.getElementById('fasilitasModal');
    var modalImage = document.getElementById('fasilitasModalImage');
    var modalTitle = document.getElementById('fasilitasModalTitle');
    var modalDesc = document.getElementById('fasilitasModalDesc');
    
    if (!modal || !modalImage) return;
    
    // Set konten
    modalImage.src = src;
    modalImage.alt = title;
    modalTitle.textContent = title || 'Fasilitas Sekolah';
    modalDesc.textContent = desc || '';
    
    // Tampilkan modal
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
    
    // Fallback gambar error
    modalImage.onerror = function() {
        this.src = 'https://via.placeholder.com/800x400/FFD93D/2D2D2D?text=Gambar+Fasilitas';
    };
}

/**
 * Tutup Modal Fasilitas
 */
function closeFasilitasModal() {
    var modal = document.getElementById('fasilitasModal');
    if (!modal) return;
    
    modal.style.display = 'none';
    document.body.style.overflow = '';
}

/**
 * Tutup modal dengan klik di luar konten
 */
document.addEventListener('click', function(e) {
    var modal = document.getElementById('fasilitasModal');
    if (!modal || modal.style.display === 'none') return;
    
    if (e.target === modal) {
        closeFasilitasModal();
    }
});

/**
 * Tutup modal dengan tombol Escape
 */
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeFasilitasModal();
    }
});