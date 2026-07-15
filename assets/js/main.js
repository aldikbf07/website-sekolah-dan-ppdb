/**
 * Main JavaScript - MI Muhammadiyah Bojongsana
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize all components
    initNavbar();
    initScrollToTop();
    initSmoothScroll();
    initGalleryLightbox();
    initAnimationOnScroll();
});

/**
 * Navbar scroll effect
 */
function initNavbar() {
    const navbar = document.querySelector('.navbar');
    
    if (navbar) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
    }
}

/**
 * Scroll to top button
 */
function initScrollToTop() {
    const scrollTopBtn = document.getElementById('scrollTopBtn');
    
    if (scrollTopBtn) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 300) {
                scrollTopBtn.style.display = 'block';
                scrollTopBtn.style.animation = 'fadeInUp 0.3s ease';
            } else {
                scrollTopBtn.style.display = 'none';
            }
        });
        
        scrollTopBtn.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }
}

/**
 * Smooth scroll for anchor links
 */
function initSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            
            if (href !== '#') {
                e.preventDefault();
                const target = document.querySelector(href);
                
                if (target) {
                    const offsetTop = target.offsetTop - 80;
                    
                    window.scrollTo({
                        top: offsetTop,
                        behavior: 'smooth'
                    });
                    
                    // Close mobile menu if open
                    const navbarCollapse = document.querySelector('.navbar-collapse');
                    if (navbarCollapse && navbarCollapse.classList.contains('show')) {
                        const bsCollapse = new bootstrap.Collapse(navbarCollapse, {
                            toggle: false
                        });
                        bsCollapse.hide();
                    }
                }
            }
        });
    });
}

/**
 * Gallery lightbox
 */
function initGalleryLightbox() {
    const galleryItems = document.querySelectorAll('.gallery-item');
    
    galleryItems.forEach(item => {
        item.addEventListener('click', function() {
            const img = this.querySelector('img');
            const description = this.getAttribute('data-description') || '';
            
            if (img) {
                showLightbox(img.src, description);
            }
        });
    });
}

/**
 * Show image in lightbox modal
 * @param {string} src - Image source
 * @param {string} description - Image description
 */
function showLightbox(src, description) {
    // Create modal if not exists
    let modal = document.getElementById('lightboxModal');
    
    if (!modal) {
        modal = document.createElement('div');
        modal.id = 'lightboxModal';
        modal.className = 'modal fade';
        modal.innerHTML = `
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content bg-dark">
                    <div class="modal-header border-0">
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center p-0">
                        <img src="" id="lightboxImage" class="img-fluid" alt="">
                        <p class="text-white p-3" id="lightboxDescription"></p>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
    }
    
    const lightboxImage = document.getElementById('lightboxImage');
    const lightboxDescription = document.getElementById('lightboxDescription');
    
    lightboxImage.src = src;
    lightboxDescription.textContent = description;
    
    const bsModal = new bootstrap.Modal(modal);
    bsModal.show();
}

/**
 * Animation on scroll using Intersection Observer
 */
function initAnimationOnScroll() {
    const animatedElements = document.querySelectorAll('[data-animate]');
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.animation = entry.target.getAttribute('data-animate');
                observer.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.1
    });
    
    animatedElements.forEach(element => {
        observer.observe(element);
    });
}

/**
 * Form validation helper
 * @param {HTMLFormElement} form - Form element
 * @returns {boolean} - Whether form is valid
 */
function validateForm(form) {
    let isValid = true;
    const inputs = form.querySelectorAll('input[required], textarea[required], select[required]');
    
    inputs.forEach(input => {
        if (!input.value.trim()) {
            input.classList.add('is-invalid');
            isValid = false;
        } else {
            input.classList.remove('is-invalid');
        }
    });
    
    return isValid;
}

/**
 * Handle file input preview
 * @param {HTMLInputElement} input - File input element
 * @param {string} previewId - ID of preview image element
 */
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        }
        
        reader.readAsDataURL(input.files[0]);
    }
}

/**
 * Confirm delete with Sweet Alert style
 * @param {string} message - Confirmation message
 * @returns {boolean} - Whether user confirmed
 */
function confirmDelete(message = 'Apakah Anda yakin ingin menghapus data ini?') {
    return confirm(message);
}

/**
 * Handle AJAX form submission
 * @param {string} formId - Form element ID
 * @param {string} url - Submit URL
 * @param {function} callback - Success callback
 */
function submitFormAjax(formId, url, callback) {
    const form = document.getElementById(formId);
    const formData = new FormData(form);
    
    fetch(url, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            callback(data);
        } else {
            alert(data.message || 'Terjadi kesalahan');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan jaringan');
    });
}

/**
 * Hero Carousel Full Width
 */
function initHeroCarouselFull() {
    const heroCarousel = document.getElementById('heroMainCarousel');
    
    if (heroCarousel) {
        const carouselInstance = new bootstrap.Carousel(heroCarousel, {
            interval: 5000,
            pause: 'hover',
            wrap: true,
            keyboard: true
        });
        
        // Pause carousel when not in viewport
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    carouselInstance.cycle();
                } else {
                    carouselInstance.pause();
                }
            });
        }, {
            threshold: 0.5
        });
        
        observer.observe(heroCarousel);
        
        // Parallax effect on scroll
        window.addEventListener('scroll', function() {
            const scrolled = window.pageYOffset;
            const slides = heroCarousel.querySelectorAll('.hero-slide-bg');
            
            slides.forEach(slide => {
                const speed = 0.3;
                const yPos = scrolled * speed;
                slide.style.backgroundPosition = `center ${-yPos}px`;
            });
        });
        
        // Keyboard navigation
        document.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowLeft') {
                carouselInstance.prev();
            } else if (e.key === 'ArrowRight') {
                carouselInstance.next();
            }
        });
        
        // Touch swipe support
        let touchStartX = 0;
        let touchEndX = 0;
        
        heroCarousel.addEventListener('touchstart', function(e) {
            touchStartX = e.changedTouches[0].screenX;
        }, {passive: true});
        
        heroCarousel.addEventListener('touchend', function(e) {
            touchEndX = e.changedTouches[0].screenX;
            
            const diff = touchStartX - touchEndX;
            
            if (Math.abs(diff) > 50) {
                if (diff > 0) {
                    carouselInstance.next();
                } else {
                    carouselInstance.prev();
                }
            }
        }, {passive: true});
        
        // Preload next slide image
        function preloadImages() {
            const slides = heroCarousel.querySelectorAll('.hero-slide-bg');
            slides.forEach(slide => {
                const bgImage = slide.style.backgroundImage;
                if (bgImage) {
                    const img = new Image();
                    img.src = bgImage.replace(/url\(['"]?(.*?)['"]?\)/i, '$1');
                }
            });
        }
        
        preloadImages();
    }
}

// Initialize on DOM load
document.addEventListener('DOMContentLoaded', function() {
    initHeroCarouselFull();
});
/**
 * Vision Mission - Scroll Animation
 */
function initVisionMissionAnimation() {
    const missionItems = document.querySelectorAll('.mission-item');
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateX(0)';
            }
        });
    }, {
        threshold: 0.2,
        rootMargin: '0px 0px -50px 0px'
    });
    
    missionItems.forEach((item, index) => {
        item.style.opacity = '0';
        item.style.transform = 'translateX(-30px)';
        item.style.transition = `all 0.5s ease ${index * 0.1}s`;
        observer.observe(item);
    });
}

document.addEventListener('DOMContentLoaded', function() {
    initVisionMissionAnimation();
});

// Call in DOMContentLoaded
document.addEventListener('DOMContentLoaded', function() {
    initHeroCarousel();
});


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
 * Modern Date Picker Enhancement
 */
function initDatePickers() {
    const dateInputs = document.querySelectorAll('input[type="date"]');
    
    dateInputs.forEach(input => {
        // Set max date to today (opsional)
        const today = new Date();
        const maxDate = today.toISOString().split('T')[0];
        // input.setAttribute('max', maxDate); // Uncomment untuk batasi maksimal hari ini
        
        // Set min date (opsional)
        const minDate = new Date();
        minDate.setFullYear(minDate.getFullYear() - 7); // 7 tahun yang lalu
        input.setAttribute('min', '2015-01-01');
        
        // Add focus effect
        input.addEventListener('focus', function() {
            this.parentElement.querySelector('.calendar-icon').style.color = '#2563eb';
        });
        
        input.addEventListener('blur', function() {
            if (!this.value) {
                this.parentElement.querySelector('.calendar-icon').style.color = '#64748b';
            }
        });
        
        // Show calendar icon color when date is selected
        if (input.value) {
            const icon = input.parentElement.querySelector('.calendar-icon');
            if (icon) icon.style.color = '#2563eb';
        }
    });
}

// Initialize on load
document.addEventListener('DOMContentLoaded', function() {
    initDatePickers();
});



// Export functions for global use
window.previewImage = previewImage;
window.confirmDelete = confirmDelete;
window.showLightbox = showLightbox;