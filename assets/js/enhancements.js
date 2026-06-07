// Enhanced JavaScript features for better UX

document.addEventListener('DOMContentLoaded', function() {
    // Back-to-top button
    const backToTopBtn = document.createElement('button');
    backToTopBtn.id = 'back-to-top';
    backToTopBtn.innerHTML = '↑';
    backToTopBtn.setAttribute('aria-label', 'กลับไปด้านบน');
    document.body.appendChild(backToTopBtn);
    
    window.addEventListener('scroll', function() {
        if (window.pageYOffset > 300) {
            backToTopBtn.classList.add('show');
        } else {
            backToTopBtn.classList.remove('show');
        }
    });
    
    backToTopBtn.addEventListener('click', function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
    
    // Confirm dialogs for destructive actions
    document.querySelectorAll('form[onsubmit*="confirm"]').forEach(function(form) {
        const confirmMsg = form.getAttribute('onsubmit')?.match(/'([^']+)'/)?.[1] || 'ยืนยันการดำเนินการ?';
        form.removeAttribute('onsubmit');
        form.addEventListener('submit', function(e) {
            if (!confirm(confirmMsg)) {
                e.preventDefault();
            }
        });
    });
    
    // Image lazy loading
    document.querySelectorAll('img[data-src]').forEach(function(img) {
        if ('IntersectionObserver' in window) {
            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src;
                        img.removeAttribute('data-src');
                        observer.unobserve(img);
                    }
                });
            });
            observer.observe(img);
        }
    });
    
    // Toast notifications (if needed)
    window.showToast = function(message, type = 'info', duration = 3000) {
        const toast = document.createElement('div');
        toast.className = 'toast ' + type;
        toast.textContent = message;
        document.body.appendChild(toast);
        
        setTimeout(function() {
            toast.style.opacity = '0';
            setTimeout(function() {
                toast.remove();
            }, 300);
        }, duration);
    };
    
    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(function(anchor) {
        anchor.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href === '#') return;
            const target = document.querySelector(href);
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });
    
    // Form validation
    document.querySelectorAll('form').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(function(field) {
                if (!field.value.trim()) {
                    isValid = false;
                    field.style.borderColor = '#ef4444';
                    field.style.background = 'rgba(239, 68, 68, 0.05)';
                } else {
                    field.style.borderColor = '';
                    field.style.background = '';
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                window.showToast('กรุณากรอกข้อมูลที่จำเป็นทั้งหมด', 'error');
            }
        });
    });
    
    // Preview image before upload
    document.querySelectorAll('input[type="file"]').forEach(function(input) {
        input.addEventListener('change', function() {
            const file = this.files[0];
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    let preview = document.querySelector('.thumbnail-preview[data-for="' + input.id + '"]');
                    if (!preview) {
                        preview = document.createElement('img');
                        preview.className = 'thumbnail-preview';
                        preview.setAttribute('data-for', input.id);
                        input.parentNode.appendChild(preview);
                    }
                    preview.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    });
});
