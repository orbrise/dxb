/*!
 * Evoory listing page bundle
 * Static, framework-agnostic JS extracted from home-page.blade.php so the HTML
 * payload stays small and Cloudflare can cache this script independently.
 *
 * Loaded with `defer` from the listing template. Reads CSRF token from the
 * <meta name="csrf-token"> tag in the layout.
 *
 * Includes:
 *   - Hover-prefetch for profile detail links (instant.page-style)
 *   - Custom dropdown/modal handler for the gender + advanced search modal
 *   - "What's new" horizontal scroll indicator
 *   - Country-name → ISO-2 lookup table (used by city search results)
 *   - Currency-update Livewire bridge
 */
(function () {
    'use strict';

    // -----------------------------------------------------------------------
    // 1. Hover-prefetch for profile detail links
    // -----------------------------------------------------------------------
    (function () {
        if (!('IntersectionObserver' in window)) return;
        var prefetched = new Set();
        var hoverTimer = null;
        var DELAY_MS = 65;
        var origin = location.origin;

        function isPrefetchable(a) {
            if (!a || a.tagName !== 'A' || !a.href) return false;
            if (prefetched.has(a.href)) return false;
            if (a.href.indexOf(origin) !== 0) return false;
            var url = new URL(a.href);
            if (url.pathname === location.pathname) return false;
            if (a.hasAttribute('download')) return false;
            if (a.target === '_blank') return false;
            if (a.getAttribute('rel') && a.getAttribute('rel').indexOf('noprefetch') > -1) return false;
            if (!/-escorts-in-[a-z0-9\-]+\/\d+\//i.test(url.pathname)) return false;
            return true;
        }

        function prefetch(href) {
            if (prefetched.has(href)) return;
            prefetched.add(href);
            var l = document.createElement('link');
            l.rel = 'prefetch';
            l.href = href;
            l.as = 'document';
            document.head.appendChild(l);
        }

        document.addEventListener('mouseover', function (e) {
            var a = e.target.closest('a');
            if (!isPrefetchable(a)) return;
            clearTimeout(hoverTimer);
            hoverTimer = setTimeout(function () { prefetch(a.href); }, DELAY_MS);
        }, { passive: true });

        document.addEventListener('touchstart', function (e) {
            var a = e.target.closest('a');
            if (isPrefetchable(a)) prefetch(a.href);
        }, { passive: true });

        document.addEventListener('mouseout', function () { clearTimeout(hoverTimer); }, { passive: true });
    })();

    // -----------------------------------------------------------------------
    // 2. Gender dropdown + advanced search modal
    // -----------------------------------------------------------------------
    (function () {
        function closeModal(modal) {
            modal.style.display = 'none';
            modal.classList.remove('in');
            document.body.classList.remove('modal-open');
            var backdrop = document.querySelector('.modal-backdrop');
            if (backdrop) backdrop.remove();
        }

        document.addEventListener('click', function (e) {
            // Gender dropdown toggle
            var genderBtn = e.target.closest('.search-bar--gender');
            var genderMenus = document.querySelectorAll('.dropdown-gender-menu');
            if (genderBtn) {
                e.preventDefault();
                e.stopPropagation();
                var menu = genderBtn.parentElement.querySelector('.dropdown-gender-menu');
                if (menu) {
                    var visible = menu.style.display === 'block';
                    genderMenus.forEach(function (m) { m.style.display = 'none'; });
                    if (!visible) menu.style.display = 'block';
                }
                return;
            }
            if (!e.target.closest('.dropdown-gender-menu')) {
                genderMenus.forEach(function (m) { m.style.display = 'none'; });
            }

            // Advanced search modal toggle
            var openBtn = e.target.closest('#toggle-search-more, [data-target="#search-more"]');
            if (openBtn) {
                e.preventDefault();
                e.stopPropagation();
                var modal = document.getElementById('search-more');
                if (modal) {
                    modal.style.display = 'block';
                    modal.classList.add('in');
                    document.body.classList.add('modal-open');
                    if (!document.querySelector('.modal-backdrop')) {
                        var backdrop = document.createElement('div');
                        backdrop.className = 'modal-backdrop fade in';
                        backdrop.style.cssText = 'position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.7);z-index:1040;';
                        document.body.appendChild(backdrop);
                        backdrop.addEventListener('click', function () { closeModal(modal); });
                    }
                }
            }
            var closeBtn = e.target.closest('#search-more .close, #search-more [data-dismiss="modal"]');
            if (closeBtn) {
                e.preventDefault();
                var m2 = document.getElementById('search-more');
                if (m2) closeModal(m2);
            }
        });
    })();

    // -----------------------------------------------------------------------
    // 3. "What's new" horizontal scroll indicator
    // -----------------------------------------------------------------------
    (function () {
        var scroll = document.querySelector('.ev-whatsnew-scroll');
        if (!scroll) return;
        var thumb = document.querySelector('.ev-scroll-thumb');
        var leftBtn = document.querySelector('.ev-scroll-left');
        var rightBtn = document.querySelector('.ev-scroll-right');
        if (thumb) {
            scroll.addEventListener('scroll', function () {
                var pct = scroll.scrollLeft / (scroll.scrollWidth - scroll.clientWidth);
                thumb.style.transform = 'translateX(' + (pct * 30) + 'px)';
            });
        }
        if (leftBtn) leftBtn.addEventListener('click', function () { scroll.scrollBy({ left: -210, behavior: 'smooth' }); });
        if (rightBtn) rightBtn.addEventListener('click', function () { scroll.scrollBy({ left: 210, behavior: 'smooth' }); });
    })();

    // -----------------------------------------------------------------------
    // 4. Country name → ISO 2-letter code (for flag rendering in city search)
    //    Exposed as window.EvooryCountryCodes so the city-search inline init
    //    block can reuse it without redefining the table.
    // -----------------------------------------------------------------------
    window.EvooryCountryCodes = {
        'United Arab Emirates': 'AE', 'Pakistan': 'PK', 'India': 'IN', 'United Kingdom': 'GB',
        'United States': 'US', 'Brazil': 'BR', 'Philippines': 'PH', 'Thailand': 'TH',
        'Singapore': 'SG', 'China': 'CN', 'Japan': 'JP', 'France': 'FR', 'Germany': 'DE',
        'Italy': 'IT', 'Spain': 'ES', 'Canada': 'CA', 'Australia': 'AU', 'Netherlands': 'NL',
        'Belgium': 'BE', 'Switzerland': 'CH', 'Austria': 'AT', 'Sweden': 'SE', 'Norway': 'NO',
        'Denmark': 'DK', 'Finland': 'FI', 'Poland': 'PL', 'Czech Republic': 'CZ', 'Czechia': 'CZ',
        'Hungary': 'HU', 'Turkey': 'TR', 'Egypt': 'EG', 'South Africa': 'ZA',
        'Saudi Arabia': 'SA', 'Qatar': 'QA', 'Kuwait': 'KW', 'Bahrain': 'BH', 'Oman': 'OM',
        'Lebanon': 'LB', 'Jordan': 'JO', 'Ireland': 'IE', 'Portugal': 'PT', 'Greece': 'GR',
        'Russia': 'RU', 'Ukraine': 'UA', 'Romania': 'RO', 'Bulgaria': 'BG', 'Croatia': 'HR',
        'Serbia': 'RS', 'Malaysia': 'MY', 'Indonesia': 'ID', 'Vietnam': 'VN',
        'South Korea': 'KR', 'Hong Kong': 'HK', 'Taiwan': 'TW', 'New Zealand': 'NZ',
        'Argentina': 'AR', 'Mexico': 'MX', 'Colombia': 'CO', 'Chile': 'CL', 'Peru': 'PE',
        'Venezuela': 'VE', 'Honduras': 'HN', 'Morocco': 'MA', 'Tunisia': 'TN', 'Kenya': 'KE',
        'Nigeria': 'NG', 'Ethiopia': 'ET', 'Sudan': 'SD', 'Israel': 'IL', 'Cyprus': 'CY',
        'Malta': 'MT', 'Luxembourg': 'LU', 'Monaco': 'MC', 'Iceland': 'IS', 'Estonia': 'EE',
        'Latvia': 'LV', 'Lithuania': 'LT', 'Slovenia': 'SI', 'Slovakia': 'SK',
        'Bosnia and Herzegovina': 'BA', 'Albania': 'AL', 'North Macedonia': 'MK',
        'Montenegro': 'ME', 'Armenia': 'AM', 'Georgia': 'GE', 'Azerbaijan': 'AZ',
        'Kazakhstan': 'KZ', 'Uzbekistan': 'UZ', 'Bangladesh': 'BD', 'Sri Lanka': 'LK',
        'Nepal': 'NP', 'Myanmar': 'MM', 'Cambodia': 'KH', 'Laos': 'LA', 'Brunei': 'BN',
        'Maldives': 'MV', 'Afghanistan': 'AF', 'Iran': 'IR', 'Iraq': 'IQ'
    };

    window.EvooryGetCountryCode = function (name) {
        return name && window.EvooryCountryCodes[name] ? window.EvooryCountryCodes[name] : null;
    };
})();
