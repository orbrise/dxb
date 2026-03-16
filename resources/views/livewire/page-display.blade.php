@push('css')
<style>
/* Sub-header bar */
.page-subheader {
    background: #131616;
    padding: 12px 0;
}
.page-subheader .ev-container {
    display: flex;
    align-items: center;
    position: relative;
}
.page-subheader .back-link {
    color: #C1F11D;
    text-decoration: none;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 4px;
}
.page-subheader .back-link:hover {
    color: #d4f84d;
}
.page-subheader .page-title {
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
    color: #fff;
    font-size: 16px;
    font-weight: 500;
    margin: 0;
    white-space: nowrap;
}

/* Page content area */
.page-display {
    background: #0a0a0a;
    min-height: 100vh;
    padding: 40px 0 60px;
}
.page-display .page-content-title {
    color: #fff;
    font-size: 24px;
    font-weight: 600;
    margin: 0 0 24px 0;
}

/* Dynamic content styling */
.page-display .page-content {
    color: #ccc;
    font-size: 15px;
    line-height: 1.7;
    margin-left:2rem;
}
.page-display .page-content h1,
.page-display .page-content h2,
.page-display .page-content h3,
.page-display .page-content h4,
.page-display .page-content h5,
.page-display .page-content h6 {
    color: #fff;
    margin-top: 32px;
    margin-bottom: 12px;
    font-weight: 600;
}
.page-display .page-content h2 { font-size: 22px; }
.page-display .page-content h3 { font-size: 19px; }
.page-display .page-content p {
    margin-bottom: 16px;
}
.page-display .page-content a {
    color: #C1F11D;
    text-decoration: none;
}
.page-display .page-content a:hover {
    color: #d4f84d;
    text-decoration: underline;
}
.page-display .page-content ul,
.page-display .page-content ol {
    padding-left: 24px;
    margin-bottom: 16px;
}
.page-display .page-content li {
    margin-bottom: 6px;
}
.page-display .page-content blockquote {
    border-left: 3px solid #C1F11D;
    padding: 12px 20px;
    margin: 20px 0;
    background: #1a1a1a;
    border-radius: 0 8px 8px 0;
    color: #ddd;
}
.page-display .page-content img {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
}
.page-display .page-content table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 16px;
}
.page-display .page-content table th,
.page-display .page-content table td {
    padding: 10px 14px;
    border: 1px solid #2a2a2a;
    color: #ccc;
}
.page-display .page-content table th {
    background: #1a1a1a;
    color: #fff;
    font-weight: 600;
}

/* Contact form section */
.page-display .contact-form-section {
    margin-top: 32px;
    padding-top: 32px;
    border-top: 1px solid #2a2a2a;
    margin-left:2rem;
}
</style>
@endpush

<div>
{{-- Sub-header --}}
<div class="page-subheader">
    <div class="ev-container">
        <a class="back-link" href="{{ url('/') }}" wire:navigate>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
            Escort in Dubai
        </a>
        <h1 class="page-title">{{ $page->title }} - evoory</h1>
    </div>
</div>

{{-- Main content --}}
<div class="page-display">
    <div class="ev-container" style="max-width: 900px; margin-left: 0;">

        {{-- Render processed content (with shortcodes removed) --}}
        @if($processedContent)
            <div class="page-content">
                {!! $processedContent !!}
            </div>
        @endif

        {{-- Render contact form if shortcode was detected --}}
        @if($hasContactForm)
            <div class="contact-form-section">
                <livewire:contact-form />
            </div>
        @endif
    </div>
</div>
</div>
