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
        // Inline-style positioning for the modal and its dialog. We force these
        // via element.style instead of trusting the cascade because Livewire's
        // wire:navigate head merge can drop Bootstrap 4's stylesheet
        // (app.min.css) when the user transitions between layouts (e.g.
        // news → listing). Without those rules, .modal renders as a regular
        // block element and the user sees a black backdrop with no popup.
        // Inline styles always win over missing/stale stylesheets.
        var MODAL_OPEN_STYLE =
            'display:block !important;' +
            'position:fixed !important;' +
            'top:0 !important;' +
            'right:0 !important;' +
            'bottom:0 !important;' +
            'left:0 !important;' +
            'z-index:1050 !important;' +
            'overflow-x:hidden !important;' +
            'overflow-y:auto !important;' +
            'outline:0 !important;';
        var DIALOG_STYLE =
            'position:relative !important;' +
            'width:auto !important;' +
            'max-width:800px !important;' +
            'margin:30px auto !important;' +
            'pointer-events:auto !important;';
        var CONTENT_STYLE =
            'position:relative !important;' +
            'background-color:#fff !important;' +
            'border-radius:6px !important;' +
            'pointer-events:auto !important;';

        function closeModal(modal) {
            modal.removeAttribute('style');
            modal.classList.remove('in', 'show');
            document.body.classList.remove('modal-open');
            document.body.style.removeProperty('overflow');
            var backdrop = document.querySelector('.modal-backdrop');
            if (backdrop) backdrop.remove();
        }

        function openModal(modal) {
            modal.setAttribute('style', MODAL_OPEN_STYLE);
            modal.classList.add('in', 'show');
            // Force the dialog/content too — on small screens a missing
            // .modal-dialog rule pins the popup width to viewport, and a
            // missing .modal-content makes it transparent over the backdrop.
            var dialog = modal.querySelector('.modal-dialog');
            if (dialog) dialog.setAttribute('style', DIALOG_STYLE);
            var content = modal.querySelector('.modal-content');
            if (content) content.setAttribute('style', CONTENT_STYLE);
            document.body.classList.add('modal-open');
            document.body.style.overflow = 'hidden';
            if (!document.querySelector('.modal-backdrop')) {
                var backdrop = document.createElement('div');
                backdrop.className = 'modal-backdrop fade in show';
                backdrop.style.cssText = 'position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.7);z-index:1040;';
                document.body.appendChild(backdrop);
                backdrop.addEventListener('click', function () { closeModal(modal); });
            }
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
                if (modal) openModal(modal);
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
    //    Thumb width is computed from viewport/content ratio; supports mouse
    //    drag, touch drag, click-on-track to jump, and arrow stepping.
    //    Vertical wheel is translated to horizontal scroll while pointer is
    //    over the row.
    // -----------------------------------------------------------------------
    (function () {
        var scroll = document.querySelector('.ev-whatsnew-scroll');
        if (!scroll) return;
        var track = document.querySelector('.ev-scroll-track');
        var thumb = document.querySelector('.ev-scroll-thumb');
        var leftBtn = document.querySelector('.ev-scroll-left');
        var rightBtn = document.querySelector('.ev-scroll-right');
        if (!track || !thumb) return;

        function update() {
            var sw = scroll.scrollWidth;
            var cw = scroll.clientWidth;
            var indicators = document.querySelector('.ev-whatsnew-indicators');
            if (sw <= cw + 1) {
                if (indicators) indicators.style.display = 'none';
                return;
            }
            if (indicators) indicators.style.display = '';

            var trackW = track.clientWidth;
            var ratio = cw / sw;
            var thumbW = Math.max(32, Math.floor(trackW * ratio));
            var maxThumbLeft = trackW - thumbW;
            var scrollRatio = (sw - cw) > 0 ? scroll.scrollLeft / (sw - cw) : 0;
            var thumbLeft = Math.round(maxThumbLeft * scrollRatio);

            thumb.style.width = thumbW + 'px';
            thumb.style.left = thumbLeft + 'px';

            if (leftBtn) leftBtn.disabled = scroll.scrollLeft <= 0;
            if (rightBtn) rightBtn.disabled = scroll.scrollLeft >= (sw - cw - 1);
        }

        update();
        window.addEventListener('resize', update);
        scroll.addEventListener('scroll', update, { passive: true });

        if (leftBtn) leftBtn.addEventListener('click', function () { scroll.scrollBy({ left: -210, behavior: 'smooth' }); });
        if (rightBtn) rightBtn.addEventListener('click', function () { scroll.scrollBy({ left: 210, behavior: 'smooth' }); });

        // Click on empty track → jump to that position.
        track.addEventListener('mousedown', function (e) {
            if (e.target === thumb) return;
            var rect = track.getBoundingClientRect();
            var clickX = e.clientX - rect.left;
            var thumbW = thumb.offsetWidth;
            var targetThumbLeft = Math.max(0, Math.min(track.clientWidth - thumbW, clickX - thumbW / 2));
            var maxThumbLeft = track.clientWidth - thumbW;
            var scrollRatio = maxThumbLeft > 0 ? targetThumbLeft / maxThumbLeft : 0;
            scroll.scrollTo({ left: scrollRatio * (scroll.scrollWidth - scroll.clientWidth), behavior: 'smooth' });
        });

        // Drag the thumb (mouse + touch).
        var dragging = false;
        var dragStartX = 0;
        var dragStartThumbLeft = 0;

        function startDrag(clientX) {
            dragging = true;
            dragStartX = clientX;
            dragStartThumbLeft = parseFloat(thumb.style.left || '0');
            thumb.classList.add('is-dragging');
        }
        function moveDrag(clientX) {
            if (!dragging) return;
            var dx = clientX - dragStartX;
            var thumbW = thumb.offsetWidth;
            var maxThumbLeft = track.clientWidth - thumbW;
            var newThumbLeft = Math.max(0, Math.min(maxThumbLeft, dragStartThumbLeft + dx));
            var scrollRatio = maxThumbLeft > 0 ? newThumbLeft / maxThumbLeft : 0;
            scroll.scrollLeft = scrollRatio * (scroll.scrollWidth - scroll.clientWidth);
        }
        function endDrag() {
            if (!dragging) return;
            dragging = false;
            thumb.classList.remove('is-dragging');
        }

        thumb.addEventListener('mousedown', function (e) {
            e.preventDefault();
            startDrag(e.clientX);
        });
        document.addEventListener('mousemove', function (e) {
            if (dragging) moveDrag(e.clientX);
        });
        document.addEventListener('mouseup', endDrag);

        thumb.addEventListener('touchstart', function (e) {
            if (!e.touches[0]) return;
            startDrag(e.touches[0].clientX);
        }, { passive: true });
        document.addEventListener('touchmove', function (e) {
            if (dragging && e.touches[0]) {
                e.preventDefault();
                moveDrag(e.touches[0].clientX);
            }
        }, { passive: false });
        document.addEventListener('touchend', endDrag);
        document.addEventListener('touchcancel', endDrag);

        // Vertical mouse wheel over the row → horizontal scroll.
        scroll.addEventListener('wheel', function (e) {
            if (Math.abs(e.deltaY) <= Math.abs(e.deltaX)) return;
            e.preventDefault();
            scroll.scrollLeft += e.deltaY;
        }, { passive: false });
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
