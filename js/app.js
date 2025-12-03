class Magazine {
    constructor() {
        this.currentSlide = 0;
        this.totalSlides = 3;
        this.testimonialIndex = 0;
        this.testimonialTimer = null;
        this.lastScrollY = 0;
        this.isMenuOpen = false;
        
        this.init();
    }

    init() {
        this.setupCarousel();
        this.setupScrollBehavior();
        this.setupLikeSystem();
        this.setupTestimonials();
        this.setupFormValidation();
        this.setupMobileMenu();
        this.setupCommentModal();
    }

    setupCarousel() {
        const prevBtn = document.querySelector('.carousel-prev');
        const nextBtn = document.querySelector('.carousel-next');
        const dots = document.querySelectorAll('.dot');

        if (prevBtn) prevBtn.addEventListener('click', () => this.previousSlide());
        if (nextBtn) nextBtn.addEventListener('click', () => this.nextSlide());

        if (dots && dots.length) {
            dots.forEach((dot, index) => {
                dot.addEventListener('click', () => this.goToSlide(index));
            });
        }

        const slides = document.querySelectorAll('.carousel-item');
        if (slides && slides.length > 1) {
            setInterval(() => this.nextSlide(), 5000);
        }
    }

    previousSlide() {
        this.currentSlide = this.currentSlide === 0 ? this.totalSlides - 1 : this.currentSlide - 1;
        this.updateCarousel();
    }

    nextSlide() {
        this.currentSlide = (this.currentSlide + 1) % this.totalSlides;
        this.updateCarousel();
    }

    goToSlide(index) {
        this.currentSlide = index;
        this.updateCarousel();
    }

    updateCarousel() {
        const slides = document.querySelectorAll('.carousel-item');
        const dots = document.querySelectorAll('.dot');

        slides.forEach((slide, index) => {
            slide.classList.toggle('active', index === this.currentSlide);
        });

        dots.forEach((dot, index) => {
            dot.classList.toggle('active', index === this.currentSlide);
        });
    }

    setupScrollBehavior() {
        let ticking = false;

        window.addEventListener('scroll', () => {
            if (!ticking) {
                requestAnimationFrame(() => {
                    this.handleScroll();
                    ticking = false;
                });
                ticking = true;
            }
        });
    }

    handleScroll() {
        const currentScrollY = window.scrollY;
        const navbar = document.querySelector('.navbar');

        if (navbar) {
            if (currentScrollY > this.lastScrollY && currentScrollY > 100) {
                navbar.classList.add('hidden');
            } else {
                navbar.classList.remove('hidden');
            }
        }

        this.lastScrollY = currentScrollY;
        this.animateOnScroll();
    }

    animateOnScroll() {
        const elements = document.querySelectorAll('.article-card, .testimonial');
        
        elements.forEach(element => {
            const rect = element.getBoundingClientRect();
            const isVisible = rect.top < window.innerHeight && rect.bottom > 0;
            
            if (isVisible && !element.classList.contains('fade-in')) {
                element.classList.add('fade-in');
            }
        });
    }

    setupLikeSystem() {
        document.addEventListener('click', (e) => {
            const likeBtn = e.target.closest('.like-btn');
            if (likeBtn) {
                const form = likeBtn.closest('.like-form');
                
                if (form) {
                    e.preventDefault();
                    
                    likeBtn.style.transform = 'scale(0.9)';
                    
                    const isLiked = likeBtn.classList.contains('liked');
                    const countSpan = likeBtn.querySelector('.like-count');
                    const icon = likeBtn.querySelector('i');
                    let count = parseInt(countSpan.textContent) || 0;

                    if (isLiked) {
                        likeBtn.classList.remove('liked');
                        icon.className = 'far fa-heart';
                        count = Math.max(0, count - 1);
                    } else {
                        likeBtn.classList.add('liked');
                        icon.className = 'fas fa-heart';
                        count++;
                    }
                    countSpan.textContent = count;
                    
                    setTimeout(() => {
                        likeBtn.style.transform = '';
                        form.submit();
                    }, 200);
                }
            }
        });
    }

    setupCommentModal() {
        const commentForm = document.getElementById('comment-form');
        if (commentForm) {
            commentForm.addEventListener('submit', (e) => {
                const authorName = document.getElementById('author_name');
                const content = document.getElementById('comment_content');
                
                let isValid = true;
                
                if (!authorName.value.trim() || authorName.value.trim().length < 2) {
                    isValid = false;
                    authorName.classList.add('error');
                    this.showNotification('Le nom doit contenir au moins 2 caractères', 'error');
                } else {
                    authorName.classList.remove('error');
                }
                
                if (!content.value.trim() || content.value.trim().length < 3) {
                    isValid = false;
                    content.classList.add('error');
                    this.showNotification('Le commentaire est trop court', 'error');
                } else {
                    content.classList.remove('error');
                }
                
                if (!isValid) {
                    e.preventDefault();
                    return false;
                }
                
                this.showNotification('Envoi du commentaire...', 'success');
            });
        }
    }

    setupTestimonials() {
        this.startTestimonialSlider();
    }

    startTestimonialSlider() {
        const testimonials = document.querySelectorAll('.testimonial');
        if (testimonials.length === 0) return;

        this.testimonialTimer = setInterval(() => {
            testimonials[this.testimonialIndex].classList.remove('active');
            this.testimonialIndex = (this.testimonialIndex + 1) % testimonials.length;
            testimonials[this.testimonialIndex].classList.add('active');
        }, 4000);
    }

    setupFormValidation() {
        const form = document.getElementById('contact-form');
        if (!form) return;

        const inputs = form.querySelectorAll('input, select, textarea');
        
        inputs.forEach(input => {
            input.addEventListener('blur', () => this.validateField(input));
            input.addEventListener('input', () => this.clearFieldError(input));
        });

        form.addEventListener('submit', (e) => this.handleFormSubmit(e));
    }

    validateField(field) {
        const value = field.value.trim();
        const name = field.name;
        let isValid = true;
        let errorMessage = '';

        switch (name) {
            case 'name':
                if (value.length < 2) {
                    isValid = false;
                    errorMessage = 'Le nom doit contenir au moins 2 caractères';
                }
                break;
            case 'email':
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(value)) {
                    isValid = false;
                    errorMessage = 'Veuillez entrer une adresse email valide';
                }
                break;
            case 'subject':
                if (!value) {
                    isValid = false;
                    errorMessage = 'Veuillez choisir un sujet';
                }
                break;
            case 'message':
                if (value.length < 10) {
                    isValid = false;
                    errorMessage = 'Le message doit contenir au moins 10 caractères';
                }
                break;
        }

        this.showFieldError(field, errorMessage, !isValid);
        return isValid;
    }

    showFieldError(field, message, hasError) {
        const errorElement = document.getElementById(`${field.name}-error`);
        if (!errorElement) return;
        
        if (hasError) {
            field.classList.add('error');
            errorElement.textContent = message;
            errorElement.classList.add('show');
        } else {
            field.classList.remove('error');
            errorElement.classList.remove('show');
        }
    }

    clearFieldError(field) {
        field.classList.remove('error');
        const errorElement = document.getElementById(`${field.name}-error`);
        if (errorElement) {
            errorElement.classList.remove('show');
        }
    }

    handleFormSubmit(e) {
        const form = e.target;
        const submitBtn = form.querySelector('.submit-btn');
        
        let isFormValid = true;
        const fields = form.querySelectorAll('input, select, textarea');
        
        fields.forEach(field => {
            if (field.name && !this.validateField(field)) {
                isFormValid = false;
            }
        });

        if (!isFormValid) {
            e.preventDefault();
            this.showNotification('Veuillez corriger les erreurs avant d\'envoyer', 'error');
            return false;
        }

        if (submitBtn) {
            submitBtn.classList.add('loading');
            submitBtn.disabled = true;
        }
        
        this.showNotification('Envoi en cours...', 'success');
        
        return true;
    }

    setupMobileMenu() {
        const hamburger = document.querySelector('.hamburger');
        const navMenu = document.querySelector('.nav-menu');

        if (hamburger) {
            hamburger.addEventListener('click', () => {
                this.toggleMobileMenu();
            });
        }

        document.addEventListener('click', (e) => {
            if (this.isMenuOpen && !e.target.closest('.nav-container')) {
                this.toggleMobileMenu();
            }
        });
    }

    toggleMobileMenu() {
        const hamburger = document.querySelector('.hamburger');
        const navMenu = document.querySelector('.nav-menu');
        
        if (!hamburger || !navMenu) return;
        
        this.isMenuOpen = !this.isMenuOpen;
        
        hamburger.classList.toggle('active');
        navMenu.classList.toggle('active');
    }

    showNotification(message, type = 'info') {
        const existingMessage = document.querySelector('.form-message');
        if (existingMessage) {
            existingMessage.remove();
        }

        const messageEl = document.createElement('div');
        messageEl.className = `form-message ${type}`;
        messageEl.textContent = message;
        
        let bgColor = '#3498db';
        if (type === 'success') bgColor = '#27ae60';
        if (type === 'error') bgColor = '#e74c3c';
        
        messageEl.style.cssText = `
            position: fixed;
            top: 100px;
            left: 50%;
            transform: translateX(-50%);
            padding: 15px 30px;
            border-radius: 10px;
            color: white;
            font-weight: 500;
            z-index: 10000;
            transition: all 0.3s ease;
            background: ${bgColor};
        `;

        document.body.appendChild(messageEl);

        setTimeout(() => {
            messageEl.style.opacity = '0';
            messageEl.style.transform = 'translateX(-50%) translateY(-20px)';
            setTimeout(() => {
                if (messageEl.parentNode) {
                    messageEl.parentNode.removeChild(messageEl);
                }
            }, 300);
        }, 3000);
    }
}

function openCommentModal(articleId) {
    const modal = document.getElementById('comment-modal');
    const articleIdInput = document.getElementById('comment-article-id');
    
    if (modal && articleIdInput) {
        articleIdInput.value = articleId;
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
}

function closeCommentModal() {
    const modal = document.getElementById('comment-modal');
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = '';
        
        const form = document.getElementById('comment-form');
        if (form) form.reset();
    }
}

document.addEventListener('click', (e) => {
    const modal = document.getElementById('comment-modal');
    if (e.target === modal) {
        closeCommentModal();
    }
});

document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        closeCommentModal();
    }
});

document.addEventListener('DOMContentLoaded', () => {
    new Magazine();
});
