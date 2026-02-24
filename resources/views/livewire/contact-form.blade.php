<div class="contact-form-wrapper">
    @if($successMessage)
        <div class="alert alert-success" role="alert">
            <i class="fa fa-check-circle me-2"></i>
            {{ $successMessage }}
        </div>
    @endif

    @if($errorMessage)
        <div class="alert alert-danger" role="alert">
            <i class="fa fa-exclamation-circle me-2"></i>
            {{ $errorMessage }}
        </div>
    @endif

    <form wire:submit.prevent="submit" class="contact-form">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label for="name" class="form-label">Your Name <span class="text-danger">*</span></label>
                    <input type="text" 
                           class="form-control @error('name') is-invalid @enderror" 
                           id="name" 
                           wire:model.live="name"
                           placeholder="Enter your name">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                    <input type="email" 
                           class="form-control @error('email') is-invalid @enderror" 
                           id="email" 
                           wire:model.live="email"
                           placeholder="Enter your email">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="form-group mb-3">
            <label for="subject" class="form-label">Subject <span class="text-danger">*</span></label>
            <input type="text" 
                   class="form-control @error('subject') is-invalid @enderror" 
                   id="subject" 
                   wire:model.live="subject"
                   placeholder="What is this about?">
            @error('subject')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mb-3">
            <label for="message" class="form-label">Message <span class="text-danger">*</span></label>
            <textarea class="form-control @error('message') is-invalid @enderror" 
                      id="message" 
                      wire:model.live="message"
                      rows="6"
                      placeholder="Type your message here..."></textarea>
            @error('message')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-primary btn-lg" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="submit">
                    <i class="fa fa-paper-plane me-2"></i> Send Message
                </span>
                <span wire:loading wire:target="submit">
                    <i class="fa fa-spinner fa-spin me-2"></i> Sending...
                </span>
            </button>
        </div>
    </form>

    <style>
    .contact-form-wrapper {
        max-width: 700px;
        margin: 20px 0;
    }

    .contact-form .form-control {
        background-color: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: #fff;
        padding: 12px 15px;
    }

    .contact-form .form-control:focus {
        background-color: rgba(255, 255, 255, 0.15);
        border-color: #f4b827;
        box-shadow: 0 0 0 0.2rem rgba(244, 184, 39, 0.25);
        color: #fff;
    }

    .contact-form .form-control::placeholder {
        color: rgba(255, 255, 255, 0.5);
    }

    .contact-form .form-label {
        color: #c2c2c2;
        margin-bottom: 5px;
        font-weight: 500;
    }

    .contact-form .btn-primary {
        background: linear-gradient(180deg, #f4b827 0%, #d3980b 100%);
        border: none;
        color: #000;
        font-weight: 600;
        padding: 12px 30px;
        transition: all 0.3s ease;
    }

    .contact-form .btn-primary:hover {
        background: linear-gradient(180deg, #ffc832 0%, #e5a50c 100%);
        transform: translateY(-2px);
    }

    .contact-form .btn-primary:disabled {
        opacity: 0.7;
        cursor: not-allowed;
    }

    .contact-form .invalid-feedback {
        font-size: 0.875rem;
    }
    </style>
</div>
