// Image Lazy Loading and Loading Effects
document.addEventListener('DOMContentLoaded', function() {
    
    // Lazy loading for images
    const images = document.querySelectorAll('img[data-src]');
    
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.classList.remove('lazy');
                img.classList.add('loaded');
                observer.unobserve(img);
            }
        });
    });
    
    images.forEach(img => {
        imageObserver.observe(img);
    });
    
    // Add loading animation for service images
    const serviceImages = document.querySelectorAll('.service-image img');
    
    serviceImages.forEach(img => {
        // Add loading placeholder
        if (!img.complete) {
            img.style.opacity = '0';
            img.style.transition = 'opacity 0.3s ease';
            
            img.addEventListener('load', function() {
                this.style.opacity = '1';
            });
            
            img.addEventListener('error', function() {
                this.style.opacity = '1';
            });
        }
    });
    
    // Add hover effects for service cards
    const serviceCards = document.querySelectorAll('.service-card');
    
    serviceCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            const image = this.querySelector('.service-image img');
            if (image) {
                image.style.transform = 'scale(1.05)';
            }
        });
        
        card.addEventListener('mouseleave', function() {
            const image = this.querySelector('.service-image img');
            if (image) {
                image.style.transform = 'scale(1)';
            }
        });
    });
    
    // Add fade-in animation for images when they come into view
    const fadeInObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in');
            }
        });
    }, {
        threshold: 0.1
    });
    
    // Observe all images for fade-in effect
    document.querySelectorAll('img').forEach(img => {
        fadeInObserver.observe(img);
    });
});

// Add CSS for loading animations
const imageLoaderStyleElement = document.createElement('style');
imageLoaderStyleElement.textContent = `
    .lazy {
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .loaded {
        opacity: 1;
    }
    
    .fade-in {
        animation: fadeIn 0.6s ease-in-out;
    }
    
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .service-image img {
        transition: transform 0.3s ease, opacity 0.3s ease;
    }
`;
document.head.appendChild(imageLoaderStyleElement); 