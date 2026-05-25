// PurgeCSS config for trimming evoory-homepage.css. The safelist contains
// ONLY classes added at runtime by JS (Bootstrap modal/dropdown states,
// Select2, FontAwesome, Typeahead). Classes that literally appear in any
// scanned blade/JS file are kept automatically — no need to safelist them.
module.exports = {
    // Scope the scan to ONLY the templates rendered on the listings page,
    // since evoory-homepage.css is loaded exclusively from there. Including
    // unrelated blade files (admin, registration, dashboards, etc.) keeps
    // hundreds of KB of rules whose selectors happen to appear on other pages.
    content: [
        'resources/views/livewire/home-page.blade.php',
        'resources/views/components/search-header.blade.php',
        'resources/views/components/mobile-user-bottom-nav.blade.php',
        'resources/views/components/layouts/app-evoory.blade.php',
        'resources/views/components/layouts/header-evoory.blade.php',
        'resources/views/components/layouts/footer-evoory.blade.php',
        'resources/views/components/layouts/headerform.blade.php',
        'resources/views/livewire/page-list.blade.php',
        // Only the JS files actually loaded on the listings page. Including
        // app2.js (481 KB legacy bundle, not used here) keeps hundreds of
        // unused class names alive in the safelist.
        'public/assets/js/listing-page.js',
        'public/assets/js/evoory-theme.js',
        'public/assets/js/app.js',
        'resources/**/*.js',
    ],
    css: ['public/assets/css/evoory-homepage.css'],
    output: 'public/assets/css/evoory-homepage.purged.css',
    safelist: {
        standard: [
            // Bootstrap dynamic state classes (added by Bootstrap JS at runtime)
            'show', 'in', 'fade', 'collapsing', 'modal-open', 'modal-backdrop',
            'dropdown-backdrop', 'open', 'active',
            'has-error', 'has-success', 'has-warning', 'has-feedback',
            'is-valid', 'is-invalid', 'was-validated',
            // jQuery tooltip / popover injected DOM
            'tooltip', 'tooltip-inner', 'tooltip-arrow',
            'popover', 'popover-inner', 'popover-content', 'popover-title', 'popover-arrow',
            // Affix / scrollspy added by JS
            'affix', 'affix-top', 'affix-bottom',
            // Common ajax/loading states added by site JS
            'loading', 'loaded',
        ],
        deep: [
            // FontAwesome — icon classes are routinely created at runtime
            /^fa-/, /^fas$/, /^far$/, /^fab$/, /^fa$/,
            // Select2 — its JS rewrites markup adding many classes that don't appear in source
            /^select2/, /select2-/,
            // Twitter Typeahead (Bootstrap)
            /^tt-/, /^twitter-typeahead/,
            // Bootstrap modal/dropdown/carousel/collapse/tooltip/popover states
            // (some compound classes like .modal.in, .dropdown.open)
            /\.modal\./, /\.dropdown\./, /\.carousel\./,
        ],
    },
    defaultExtractor: (content) => content.match(/[A-Za-z0-9_\-:\/]+/g) || [],
    // Strip unused @keyframes, @font-face, and CSS variable declarations too.
    keyframes: true,
    fontFace: true,
    variables: true,
};
