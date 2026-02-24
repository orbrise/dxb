// Optimized JavaScript for Livewire components
class LivewireOptimizer {
    constructor() {
        this.debounceTimers = new Map();
        this.componentCache = new Map();
        this.init();
    }

    init() {
        // Optimize Select2 initialization
        this.initSelect2Optimization();
        
        // Optimize image lazy loading
        this.initLazyLoading();
        
        // Add debounced search
        this.initDebouncedSearch();
        
        // Optimize DOM updates
        this.initDOMOptimization();
    }

    initSelect2Optimization() {
        // Cache Select2 configurations
        const select2Config = {
            theme: 'bootstrap4',
            width: '100%',
            dropdownParent: $('body'),
            templateResult: this.formatSelectOption,
            templateSelection: this.formatSelectOption,
            escapeMarkup: function(markup) { return markup; }
        };

        // Debounced Select2 initialization
        const initSelect2 = this.debounce(() => {
            $('.select2:not(.select2-hidden-accessible)').each(function() {
                const $select = $(this);
                const model = $select.attr('wire:model');
                
                $select.select2(select2Config).on('change', function() {
                    if (model && window.Livewire) {
                        window.Livewire.first().set(model, $(this).val());
                    }
                });
            });
        }, 100);

        // Initialize on Livewire events
        document.addEventListener('livewire:initialized', initSelect2);
        document.addEventListener('livewire:update', initSelect2);
    }

    initLazyLoading() {
        // Intersection Observer for lazy loading images
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src;
                        img.classList.remove('lazy');
                        observer.unobserve(img);
                    }
                });
            });

            document.querySelectorAll('img.lazy').forEach(img => {
                imageObserver.observe(img);
            });
        }
    }

    initDebouncedSearch() {
        // Debounced search for better performance
        document.addEventListener('input', (e) => {
            if (e.target.hasAttribute('wire:model') && e.target.type === 'search') {
                const model = e.target.getAttribute('wire:model');
                const value = e.target.value;
                
                this.debounce(() => {
                    if (window.Livewire) {
                        window.Livewire.first().set(model, value);
                    }
                }, 300, `search_${model}`)();
            }
        });
    }

    initDOMOptimization() {
        // Virtual scrolling for large lists
        this.initVirtualScrolling();
        
        // Optimize form submissions
        this.initFormOptimization();
    }

    initVirtualScrolling() {
        const virtualScrollContainer = document.querySelector('.virtual-scroll');
        if (!virtualScrollContainer) return;

        const itemHeight = 60; // Adjust based on your item height
        const containerHeight = virtualScrollContainer.clientHeight;
        const visibleItems = Math.ceil(containerHeight / itemHeight);
        const bufferSize = 5;

        // Implementation would depend on your specific list structure
        // This is a simplified example
    }

    initFormOptimization() {
        // Prevent multiple form submissions (skip Livewire forms - they handle this internally)
        document.addEventListener('submit', (e) => {
            const form = e.target;
            
            // Skip Livewire forms - they handle their own submission prevention
            if (form.hasAttribute('wire:submit') || form.querySelector('[wire\\:submit]')) {
                return;
            }
            
            if (form.dataset.submitting === 'true') {
                e.preventDefault();
                return false;
            }
            
            form.dataset.submitting = 'true';
            
            // Reset after 2 seconds as fallback
            setTimeout(() => {
                form.dataset.submitting = 'false';
            }, 2000);
        });
    }

    debounce(func, wait, key = 'default') {
        return (...args) => {
            if (this.debounceTimers.has(key)) {
                clearTimeout(this.debounceTimers.get(key));
            }
            
            const timeout = setTimeout(() => {
                func.apply(this, args);
                this.debounceTimers.delete(key);
            }, wait);
            
            this.debounceTimers.set(key, timeout);
        };
    }

    formatSelectOption(option) {
        if (!option.id) return option.text;
        
        // Cache formatted options
        const cacheKey = `select_option_${option.id}`;
        if (this.componentCache.has(cacheKey)) {
            return this.componentCache.get(cacheKey);
        }
        
        const formatted = `<span>${option.text}</span>`;
        this.componentCache.set(cacheKey, formatted);
        return formatted;
    }

    // Utility method to clear caches
    clearCache() {
        this.componentCache.clear();
    }

    // Method to preload critical resources
    preloadResources() {
        const criticalResources = [
            '/assets/css/app.css',
            '/assets/js/app.js'
        ];

        criticalResources.forEach(resource => {
            const link = document.createElement('link');
            link.rel = 'preload';
            link.href = resource;
            link.as = resource.endsWith('.css') ? 'style' : 'script';
            document.head.appendChild(link);
        });
    }
}

// Initialize optimizer when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.livewireOptimizer = new LivewireOptimizer();
});

// Performance monitoring
if (typeof performance !== 'undefined') {
    window.addEventListener('load', () => {
        setTimeout(() => {
            const perfData = performance.getEntriesByType('navigation')[0];
            console.log('Page Load Time:', perfData.loadEventEnd - perfData.fetchStart, 'ms');
        }, 0);
    });
}
