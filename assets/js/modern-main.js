/**
 * Modern Main JavaScript with Animations
 * MI Muhammadiyah Bojongsana
 */

document.addEventListener('DOMContentLoaded', function() {
    initNavbarScroll();
    initSmoothScroll();
    initBackToTop();
    initCounterAnimation();
    initIntersectionObserver();
    initRippleEffect();
    initParallax();
    initTiltEffect();
    createParticles();
});

/**
 * Navbar Sticky Scroll Effect
 */
function initNavbarScroll() {
    const navbar = document.getElementById('mainNavbar');
    if (!navbar) return;
    
    // Hanya untuk halaman home (navbar transparan)
    if (!navbar.classList.contains('navbar-transparent')) return;
    
    function updateNavbar() {
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    }
    
    window.addEventListener('scroll', updateNavbar, { passive: true });
    updateNavbar();
    
    // Close mobile menu on link click
    const navLinks = navbar.querySelectorAll('.nav-link-modern');
    const navbarCollapse = navbar.querySelector('.navbar-collapse');
    const bsCollapse = bootstrap.Collapse.getInstance(navbarCollapse) || new bootstrap.Collapse(navbarCollapse, { toggle: false });
    
    navLinks.forEach(link => {
        link.addEventListener('click', () => {
            if (navbarCollapse.classList.contains('show')) {
                bsCollapse.hide();
            }
        });
    });
}

document.addEventListener('DOMContentLoaded', initNavbarScroll);
/**
 * Smooth Scroll
 */
function initSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href === '#') return;
            
            e.preventDefault();
            const target = document.querySelector(href);
            if (target) {
                const offset = 80;
                const position = target.getBoundingClientRect().top + window.pageYOffset - offset;
                window.scrollTo({ top: position, behavior: 'smooth' });
            }
        });
    });
}

/**
 * Back to Top Button
 */
function initBackToTop() {
    const btn = document.createElement('button');
    btn.innerHTML = '<i class="fa-solid fa-arrow-up"></i>';
    btn.className = 'back-to-top';
    btn.setAttribute('aria-label', 'Back to top');
    document.body.appendChild(btn);
    
    window.addEventListener('scroll', () => {
        if (window.scrollY > 500) {
            btn.classList.add('visible');
        } else {
            btn.classList.remove('visible');
        }
    });
    
    btn.addEventListener('click', () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
}

/**
 * Counter Animation
 */
function initCounterAnimation() {
    const counters = document.querySelectorAll('.counter');
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const counter = entry.target;
                const target = parseInt(counter.getAttribute('data-target'));
                const duration = 2000;
                const steps = 60;
                const increment = target / steps;
                let current = 0;
                let step = 0;
                
                const animate = () => {
                    step++;
                    current = Math.min(increment * step, target);
                    counter.textContent = Math.floor(current);
                    
                    if (step < steps) {
                        requestAnimationFrame(animate);
                    } else {
                        counter.textContent = target;
                    }
                };
                
                requestAnimationFrame(animate);
                observer.unobserve(counter);
            }
        });
    }, { threshold: 0.5 });
    
    counters.forEach(counter => observer.observe(counter));
}

/**
 * Intersection Observer for animations
 */
function initIntersectionObserver() {
    const animatedElements = document.querySelectorAll('[data-animate]');
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.animation = entry.target.getAttribute('data-animate');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1, rootMargin: '0px 0px -30px 0px' });
    
    animatedElements.forEach(el => observer.observe(el));
}

/**
 * Ripple Effect on Buttons
 */
function initRippleEffect() {
    document.querySelectorAll('.ripple').forEach(button => {
        button.addEventListener('click', function(e) {
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;
            
            ripple.style.cssText = `
                position: absolute;
                width: ${size}px;
                height: ${size}px;
                left: ${x}px;
                top: ${y}px;
                border-radius: 50%;
                background: rgba(255,255,255,0.3);
                transform: scale(0);
                animation: ripple 0.6s ease-out;
                pointer-events: none;
            `;
            
            this.appendChild(ripple);
            setTimeout(() => ripple.remove(), 600);
        });
    });
}

/**
 * Parallax Effect
 */
function initParallax() {
    const parallaxElements = document.querySelectorAll('.parallax');
    
    window.addEventListener('scroll', () => {
        parallaxElements.forEach(el => {
            const speed = el.getAttribute('data-speed') || 0.3;
            const yPos = -(window.pageYOffset * speed);
            el.style.transform = `translateY(${yPos}px)`;
        });
    });
}

/**
 * Tilt Effect on Cards
 */
function initTiltEffect() {
    const cards = document.querySelectorAll('.tilt-card');
    
    cards.forEach(card => {
        card.addEventListener('mousemove', (e) => {
            const rect = card.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            const centerX = rect.width / 2;
            const centerY = rect.height / 2;
            const rotateX = (y - centerY) / 20;
            const rotateY = (centerX - x) / 20;
            
            card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale3d(1.02,1.02,1.02)`;
        });
        
        card.addEventListener('mouseleave', () => {
            card.style.transform = 'perspective(1000px) rotateX(0) rotateY(0) scale3d(1,1,1)';
        });
    });
}

/**
 * Create Floating Particles
 */
function createParticles() {
    const container = document.querySelector('.hero-particles');
    if (!container) return;
    
    for (let i = 0; i < 20; i++) {
        const particle = document.createElement('div');
        particle.className = 'hero-particle';
        particle.style.cssText = `
            top: ${Math.random() * 100}%;
            left: ${Math.random() * 100}%;
            width: ${Math.random() * 6 + 3}px;
            height: ${Math.random() * 6 + 3}px;
            animation-delay: ${Math.random() * 3}s;
            animation-duration: ${Math.random() * 3 + 2}s;
            opacity: ${Math.random() * 0.2 + 0.05};
        `;
        container.appendChild(particle);
    }
}



/**
 * Reveal on scroll helper
 */
function revealOnScroll() {
    const reveals = document.querySelectorAll('.reveal');
    
    reveals.forEach(reveal => {
        const windowHeight = window.innerHeight;
        const revealTop = reveal.getBoundingClientRect().top;
        const revealPoint = 50;
        
        if (revealTop < windowHeight - revealPoint) {
            reveal.classList.add('active');
        }
    });
}

window.addEventListener('scroll', revealOnScroll);
window.addEventListener('load', revealOnScroll);


/**
 * Hero Carousel Progress Bar
 */
function initHeroCarousel() {
    const carousel = document.getElementById('heroCarousel');
    if (!carousel) return;
    
    const progressBar = document.getElementById('carouselProgress');
    const totalSlides = carousel.querySelectorAll('.carousel-item').length;
    const interval = 5000; // Same as data-bs-interval
    let currentSlide = 0;
    let progressWidth = 0;
    let animationFrame;
    
    function updateProgress(timestamp) {
        if (!startTime) startTime = timestamp;
        const elapsed = timestamp - startTime;
        progressWidth = Math.min((elapsed / interval) * 100, 100);
        
        if (progressBar) {
            progressBar.style.width = progressWidth + '%';
        }
        
        if (elapsed < interval) {
            animationFrame = requestAnimationFrame(updateProgress);
        }
    }
    
    let startTime = null;
    
    function resetProgress() {
        if (animationFrame) cancelAnimationFrame(animationFrame);
        startTime = null;
        progressWidth = 0;
        if (progressBar) progressBar.style.width = '0%';
        animationFrame = requestAnimationFrame(updateProgress);
    }
    
    // Start progress
    resetProgress();
    
    // Reset on slide change
    carousel.addEventListener('slide.bs.carousel', function(e) {
        currentSlide = e.to;
        resetProgress();
    });
    
    // Pause on hover
    carousel.addEventListener('mouseenter', () => {
        if (animationFrame) cancelAnimationFrame(animationFrame);
    });
    
    carousel.addEventListener('mouseleave', () => {
        startTime = null;
        animationFrame = requestAnimationFrame(updateProgress);
    });
}

/**
 * Hero Carousel Progress & Controls
 */
function initHeroCarousel() {
    const carousel = document.getElementById('heroCarousel');
    if (!carousel) return;
    
    const progressBar = document.getElementById('carouselProgress');
    const totalSlides = carousel.querySelectorAll('.carousel-item').length;
    const interval = 5000;
    let currentSlide = 0;
    let startTime = null;
    let animationFrame;
    
    function updateProgress(timestamp) {
        if (!startTime) startTime = timestamp;
        const elapsed = timestamp - startTime;
        const progressWidth = Math.min((elapsed / interval) * 100, 100);
        
        if (progressBar) {
            progressBar.style.width = progressWidth + '%';
        }
        
        if (elapsed < interval) {
            animationFrame = requestAnimationFrame(updateProgress);
        }
    }
    
    function resetProgress() {
        if (animationFrame) cancelAnimationFrame(animationFrame);
        startTime = null;
        if (progressBar) progressBar.style.width = '0%';
        animationFrame = requestAnimationFrame(updateProgress);
    }
    
    // Initialize
    resetProgress();
    
    // Reset on slide change
    carousel.addEventListener('slide.bs.carousel', function(e) {
        currentSlide = e.to;
        resetProgress();
    });
    
    carousel.addEventListener('slid.bs.carousel', function(e) {
        currentSlide = e.to;
    });
    
    // Pause progress on hover
    carousel.addEventListener('mouseenter', function() {
        if (animationFrame) {
            cancelAnimationFrame(animationFrame);
            animationFrame = null;
        }
    });
    
    carousel.addEventListener('mouseleave', function() {
        if (!animationFrame) {
            startTime = null;
            animationFrame = requestAnimationFrame(updateProgress);
        }
    });
    
    // Initialize Bootstrap carousel
    const bsCarousel = new bootstrap.Carousel(carousel, {
        interval: interval,
        ride: 'carousel',
        pause: false,
        wrap: true
    });
}

/**
 * Prestasi Horizontal Scroll
 */
var prestasiCurrentSlide = 0;
var prestasiCardsPerSlide = 3;

function initPrestasiScroll() {
    var container = document.getElementById('prestasiScroll');
    var leftBtn = document.getElementById('prestasiScrollLeft');
    var rightBtn = document.getElementById('prestasiScrollRight');
    var dotsContainer = document.getElementById('prestasiDots');
    
    if (!container || !leftBtn || !rightBtn || !dotsContainer) return;
    
    var cards = container.querySelectorAll('.prestasi-card-vertical');
    if (!cards.length) return;
    
    function updateCardsPerSlide() {
        if (window.innerWidth > 991) { prestasiCardsPerSlide = 3; }
        else if (window.innerWidth > 767) { prestasiCardsPerSlide = 2; }
        else { prestasiCardsPerSlide = 1; }
    }
    
    updateCardsPerSlide();
    var totalSlides = Math.ceil(cards.length / prestasiCardsPerSlide);
    
    function createDots() {
        totalSlides = Math.ceil(cards.length / prestasiCardsPerSlide);
        dotsContainer.innerHTML = '';
        for (var i = 0; i < totalSlides; i++) {
            var dot = document.createElement('span');
            dot.className = 'dot' + (i === 0 ? ' active' : '');
            dot.setAttribute('data-index', i);
            dot.addEventListener('click', function() {
                goToSlide(parseInt(this.getAttribute('data-index')));
            });
            dotsContainer.appendChild(dot);
        }
    }
    
    function updateDots() {
        var dots = dotsContainer.querySelectorAll('.dot');
        dots.forEach(function(dot, i) {
            dot.classList.toggle('active', i === prestasiCurrentSlide);
        });
    }
    
    function updateButtons() {
        leftBtn.style.opacity = prestasiCurrentSlide <= 0 ? '0.3' : '1';
        leftBtn.style.pointerEvents = prestasiCurrentSlide <= 0 ? 'none' : 'auto';
        rightBtn.style.opacity = prestasiCurrentSlide >= totalSlides - 1 ? '0.3' : '1';
        rightBtn.style.pointerEvents = prestasiCurrentSlide >= totalSlides - 1 ? 'none' : 'auto';
    }
    
    function goToSlide(index) {
        prestasiCurrentSlide = Math.max(0, Math.min(index, totalSlides - 1));
        var cardWidth = cards[0].offsetWidth;
        var gap = 24;
        var scrollAmount = prestasiCurrentSlide * (cardWidth + gap) * prestasiCardsPerSlide;
        container.scrollTo({ left: scrollAmount, behavior: 'smooth' });
        updateDots();
        updateButtons();
    }
    
    leftBtn.addEventListener('click', function() { goToSlide(prestasiCurrentSlide - 1); });
    rightBtn.addEventListener('click', function() { goToSlide(prestasiCurrentSlide + 1); });
    
    container.addEventListener('scroll', function() {
        var cardWidth = cards[0].offsetWidth;
        var gap = 24;
        var scrollPos = container.scrollLeft;
        var newSlide = Math.round(scrollPos / ((cardWidth + gap) * prestasiCardsPerSlide));
        if (newSlide !== prestasiCurrentSlide) {
            prestasiCurrentSlide = newSlide;
            updateDots();
            updateButtons();
        }
    });
    
    createDots();
    updateButtons();
    container.setAttribute('tabindex', '0');
    container.style.cursor = 'grab';
}

document.addEventListener('DOMContentLoaded', initPrestasiScroll);

document.addEventListener('DOMContentLoaded', initPrestasiScroll);

// Initialize on load
document.addEventListener('DOMContentLoaded', function() {
    initHeroCarousel();
});
document.addEventListener('DOMContentLoaded', initHeroCarousel);