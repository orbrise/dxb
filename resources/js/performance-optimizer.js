// High-Performance JavaScript Utilities
(function(window, document) {
    'use strict';
    
    // Performance optimization utilities
    const PerformanceOptimizer = {
        // Debounce function for search and input events
        debounce: function(func, wait, immediate) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    timeout = null;
                    if (!immediate) func.apply(this, args);
                };
                const callNow = immediate && !timeout;
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
                if (callNow) func.apply(this, args);
            };
        },

        // Throttle function for scroll events
        throttle: function(func, limit) {
            let inThrottle;
            return function executedFunction(...args) {
                if (!inThrottle) {
                    func.apply(this, args);
                    inThrottle = true;
                    setTimeout(() => inThrottle = false, limit);
                }
            };
        },

        // Lazy load images with Intersection Observer
        lazyLoadImages: function() {
            if ('IntersectionObserver' in window) {
                const imageObserver = new IntersectionObserver((entries, observer) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const img = entry.target;
                            if (img.dataset.src) {
                                img.src = img.dataset.src;
                                img.classList.remove('lazy');
                                img.classList.add('loaded');
                                observer.unobserve(img);
                            }
                        }
                    });
                }, {
                    rootMargin: '50px 0px',
                    threshold: 0.01
                });

                document.querySelectorAll('img[data-src]').forEach(img => {
                    imageObserver.observe(img);
                });
            } else {
                // Fallback for older browsers
                document.querySelectorAll('img[data-src]').forEach(img => {
                    img.src = img.dataset.src;
                });
            }
        },

        // Optimize Select2 initialization
        initSelect2: function() {
            // Cache jQuery if available
            if (typeof $ !== 'undefined' && $.fn.select2) {
                const select2Config = {
                    theme: 'bootstrap4',
                    width: '100%',
                    dropdownParent: $('body'),
                    minimumResultsForSearch: 10,
                    templateResult: function(option) {
                        if (!option.id) return option.text;
                        return $('<span>' + option.text + '</span>');
                    }
                };

                $('.select2:not(.select2-hidden-accessible)').each(function() {
                    const $select = $(this);
                    const model = $select.attr('wire:model');
                    
                    $select.select2(select2Config).on('change', PerformanceOptimizer.debounce(function() {
                        if (model && window.Livewire) {
                            window.Livewire.first().set(model, $(this).val());
                        }
                    }, 150));
                });
            }
        },

        // Preload critical resources
        preloadResources: function() {
            const criticalResources = [
                { href: '/assets/css/app.css', as: 'style' },
                { href: '/assets/js/app.js', as: 'script' }
            ];

            criticalResources.forEach(resource => {
                const link = document.createElement('link');
                link.rel = 'preload';
                link.href = resource.href;
                link.as = resource.as;
                if (resource.as === 'script') {
                    link.crossOrigin = 'anonymous';
                }
                document.head.appendChild(link);
            });
        },

        // Optimize form submissions
        optimizeFormSubmissions: function() {
            document.addEventListener('submit', function(e) {
                const form = e.target;
                
                // Skip Livewire forms - they handle their own submission
                if (form.hasAttribute('wire:submit') || form.querySelector('[wire\\:submit]')) {
                    return;
                }
                
                const submitBtn = form.querySelector('[type="submit"]');
                
                // Prevent double submissions
                if (form.dataset.submitting === 'true') {
                    e.preventDefault();
                    return false;
                }
                
                form.dataset.submitting = 'true';
                
                if (submitBtn) {
                    submitBtn.disabled = true;
                    const originalText = submitBtn.textContent;
                    submitBtn.innerHTML = '<span class="loading-spinner"></span> Processing...';
                    
                    // Reset after timeout as fallback
                    setTimeout(() => {
                        form.dataset.submitting = 'false';
                        submitBtn.disabled = false;
                        submitBtn.textContent = originalText;
                    }, 10000);
                }
            });
        },

        // Initialize all optimizations
        init: function() {
            // Wait for DOM to be ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', () => {
                    this.lazyLoadImages();
                    this.optimizeFormSubmissions();
                });
            } else {
                this.lazyLoadImages();
                this.optimizeFormSubmissions();
            }

            // Initialize Select2 after jQuery loads
            window.addEventListener('load', () => {
                this.initSelect2();
            });

            // Preload resources
            this.preloadResources();
        }
    };

    // Livewire-specific optimizations
    const LivewireOptimizations = {
        init: function() {
            // Optimize Livewire events
            document.addEventListener('livewire:initialized', function() {
                PerformanceOptimizer.initSelect2();
            });

            document.addEventListener('livewire:update', PerformanceOptimizer.debounce(function() {
                PerformanceOptimizer.initSelect2();
                PerformanceOptimizer.lazyLoadImages();
            }, 100));

            // Add loading states to Livewire components
            document.addEventListener('livewire:start', function() {
                document.body.classList.add('livewire-loading');
            });

            document.addEventListener('livewire:finish', function() {
                document.body.classList.remove('livewire-loading');
            });
        }
    };

    // Initialize all optimizations
    PerformanceOptimizer.init();
    LivewireOptimizations.init();

    // Export to global scope for external use
    window.PerformanceOptimizer = PerformanceOptimizer;

})(window, document);

// CSS for loading states
const loadingCSS = `
    .livewire-loading {
        cursor: wait;
    }
    
    .livewire-loading * {
        pointer-events: none;
    }
    
    img.lazy {
        opacity: 0;
        transition: opacity 0.3s;
    }
    
    img.loaded {
        opacity: 1;
    }
    
    .loading-spinner {
        display: inline-block;
        width: 16px;
        height: 16px;
        border: 2px solid #f3f3f3;
        border-top: 2px solid #3498db;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
`;

// Inject CSS
const style = document.createElement('style');
style.textContent = loadingCSS;
document.head.appendChild(style);
