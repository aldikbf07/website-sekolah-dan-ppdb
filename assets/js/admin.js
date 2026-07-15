/**
 * Admin Panel JavaScript - MI Muhammadiyah Bojongsana
 */

document.addEventListener('DOMContentLoaded', function() {
    initSidebar();
    initAutoHideAlert();
    initConfirmDelete();
    initImagePreview();
});

/**
 * Initialize sidebar toggle for mobile
 */
function initSidebar() {
    const sidebarToggle = document.querySelector('.sidebar-toggle');
    
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('active');
        });
    }
}

/**
 * Auto hide alert messages
 */
function initAutoHideAlert() {
    const alerts = document.querySelectorAll('.alert:not(.alert-dismissible)');
    
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s ease';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    });
}

/**
 * Confirm delete with custom dialog
 */
function initConfirmDelete() {
    document.querySelectorAll('.btn-delete').forEach(button => {
        button.addEventListener('click', function(e) {
            if (!confirm('Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.')) {
                e.preventDefault();
            }
        });
    });
}

/**
 * Image preview for file inputs
 */
function initImagePreview() {
    document.querySelectorAll('input[type="file"][data-preview]').forEach(input => {
        input.addEventListener('change', function() {
            const previewId = this.getAttribute('data-preview');
            const preview = document.getElementById(previewId);
            
            if (this.files && this.files[0] && preview) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                
                reader.readAsDataURL(this.files[0]);
            }
        });
    });
}

/**
 * Toggle element visibility
 * @param {string} elementId - Element ID to toggle
 */
function toggleElement(elementId) {
    const element = document.getElementById(elementId);
    if (element) {
        if (element.style.display === 'none') {
            element.style.display = 'block';
        } else {
            element.style.display = 'none';
        }
    }
}