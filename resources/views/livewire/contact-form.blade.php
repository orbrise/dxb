<div class="contact-form-wrapper">
    @if($successMessage)
        <div class="ev-alert ev-alert-success" role="alert">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            {{ $successMessage }}
        </div>
    @endif

    @if($errorMessage)
        <div class="ev-alert ev-alert-error" role="alert">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
            {{ $errorMessage }}
        </div>
    @endif

    <form wire:submit.prevent="submit" class="ev-contact-form">
        <div class="ev-form-row">
            <div class="ev-form-group">
                <label for="name">Your Name <span class="ev-required">*</span></label>
                <input type="text"
                       class="ev-input @error('name') ev-input-error @enderror"
                       id="name"
                       wire:model.live="name"
                       placeholder="Enter your name">
                @error('name')
                    <div class="ev-error-msg">{{ $message }}</div>
                @enderror
            </div>
            <div class="ev-form-group">
                <label for="email">Email Address <span class="ev-required">*</span></label>
                <input type="email"
                       class="ev-input @error('email') ev-input-error @enderror"
                       id="email"
                       wire:model.live="email"
                       placeholder="Enter your email">
                @error('email')
                    <div class="ev-error-msg">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="ev-form-group">
            <label for="subject">Subject <span class="ev-required">*</span></label>
            <input type="text"
                   class="ev-input @error('subject') ev-input-error @enderror"
                   id="subject"
                   wire:model.live="subject"
                   placeholder="What is this about?">
            @error('subject')
                <div class="ev-error-msg">{{ $message }}</div>
            @enderror
        </div>

        <div class="ev-form-group">
            <label for="message">Message <span class="ev-required">*</span></label>
            <textarea class="ev-input @error('message') ev-input-error @enderror"
                      id="message"
                      wire:model.live="message"
                      rows="6"
                      placeholder="Type your message here..."></textarea>
            @error('message')
                <div class="ev-error-msg">{{ $message }}</div>
            @enderror
        </div>

        <div class="ev-form-group">
            <button type="submit" class="ev-btn-submit" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="submit">Send Message</span>
                <span wire:loading wire:target="submit">
                    <svg class="ev-spinner" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v4m0 12v4m-7.07-3.93l2.83-2.83m8.48-8.48l2.83-2.83M2 12h4m12 0h4M4.93 4.93l2.83 2.83m8.48 8.48l2.83 2.83"/></svg>
                    Sending...
                </span>
            </button>
        </div>
    </form>

    <style>
    .contact-form-wrapper {
        max-width: 700px;
        margin: 20px 0;
    }

    /* Alerts */
    .ev-alert {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 14px 18px;
        border-radius: 8px;
        font-size: 14px;
        margin-bottom: 20px;
    }
    .ev-alert-success {
        background: rgba(193, 241, 29, 0.1);
        border: 1px solid rgba(193, 241, 29, 0.3);
        color: #C1F11D;
    }
    .ev-alert-error {
        background: rgba(255, 77, 77, 0.1);
        border: 1px solid rgba(255, 77, 77, 0.3);
        color: #ff4d4d;
    }

    /* Form layout */
    .ev-form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }
    @media (max-width: 600px) {
        .ev-form-row {
            grid-template-columns: 1fr;
        }
    }

    .ev-form-group {
        margin-bottom: 20px;
    }
    .ev-form-group label {
        display: block;
        color: #ccc;
        font-size: 14px;
        font-weight: 500;
        margin-bottom: 8px;
    }
    .ev-required {
        color: #C1F11D;
    }

    /* Inputs */
    .ev-input {
        width: 100%;
        background: #1a1a1a;
        border: 1px solid #2a2a2a;
        border-radius: 8px;
        color: #fff;
        padding: 12px 16px;
        font-size: 14px;
        font-family: inherit;
        transition: all 0.2s ease;
        outline: none;
        box-sizing: border-box;
    }
    .ev-input:focus {
        border-color: #C1F11D;
        box-shadow: 0 0 0 3px rgba(193, 241, 29, 0.15);
        background: #222;
    }
    .ev-input::placeholder {
        color: #666;
    }
    textarea.ev-input {
        resize: vertical;
        min-height: 120px;
    }

    /* Error state */
    .ev-input-error {
        border-color: #ff4d4d !important;
    }
    .ev-input-error:focus {
        box-shadow: 0 0 0 3px rgba(255, 77, 77, 0.15) !important;
    }
    .ev-error-msg {
        color: #ff4d4d;
        font-size: 13px;
        margin-top: 6px;
    }

    /* Submit button */
    .ev-btn-submit {
        background: #C1F11D;
        color: #000;
        border: none;
        padding: 12px 32px;
        border-radius: 22px;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-family: inherit;
    }
    .ev-btn-submit:hover {
        background: #d4f84d;
        transform: translateY(-1px);
    }
    .ev-btn-submit:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }

    /* Spinner animation */
    .ev-spinner {
        animation: ev-spin 1s linear infinite;
    }
    @keyframes ev-spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    </style>
</div>
