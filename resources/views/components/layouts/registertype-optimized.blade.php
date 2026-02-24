<div class="modal" id="registertype" style="display: none">
    <div class="modal-md modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" type="button" id="closereg" aria-label="Close">
                    <i class="fa fa-times"></i>
                </button>
                <h2 class="modal-title">Register now</h2>
            </div>
            <div class="modal-body">
                <div class="row" id="register-account-type-selection">
                    <div class="col-xs-6 text-center">
                        <div class="account-type" id="account-type-user">
                            <h3>User</h3>
                            <a href="/register" wire:navigate>
                                <span class="register-graphic user-graphic" role="img" aria-label="User registration icon"></span>
                            </a>
                            <p>Keep updated on <br> activity in your area!</p>
                            <a class="btn btn-lg btn-primary btn-xs-block" href="{{ route('register') }}" wire:navigate>Register</a>
                        </div>
                    </div>
                    <div class="col-xs-6 text-center">
                        <div class="account-type border-left" id="account-type-escort">
                            <h3>Advertiser</h3>
                            <a href="/escort-sign-up" wire:navigate>
                                <span class="register-graphic escort-graphic" role="img" aria-label="Advertiser registration icon"></span>
                            </a>
                            <p>Get listed <br> for free today!</p>
                            <a class="btn btn-lg btn-primary btn-xs-block" href="{{ route('escort.signup') }}" wire:navigate>Register</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Optimized JavaScript with event delegation and performance improvements -->
<script>
    // Use event delegation for better performance
    document.addEventListener('DOMContentLoaded', function() {
        const registerModal = document.getElementById('registertype');
        
        // Open modal handler
        document.addEventListener('click', function(e) {
            if (e.target && e.target.matches('a#registerpopup')) {
                e.preventDefault();
                registerModal.style.display = 'block';
                document.body.classList.add('modal-open'); // Prevent scrolling
            }
        });
        
        // Close modal handler
        document.addEventListener('click', function(e) {
            if (e.target && (e.target.matches('button#closereg') || e.target.matches('#closereg i'))) {
                registerModal.style.display = 'none';
                document.body.classList.remove('modal-open');
            }
        });
        
        // Close modal on backdrop click
        registerModal.addEventListener('click', function(e) {
            if (e.target === registerModal) {
                registerModal.style.display = 'none';
                document.body.classList.remove('modal-open');
            }
        });
        
        // ESC key handler
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && registerModal.style.display === 'block') {
                registerModal.style.display = 'none';
                document.body.classList.remove('modal-open');
            }
        });
    });
</script>

<style>
    /* Performance-optimized modal styles */
    .modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 1050;
        transform: translateZ(0); /* Enable hardware acceleration */
    }
    
    .modal-dialog {
        position: relative;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: auto;
        max-width: 500px;
        margin: 0;
    }
    
    .modal-content {
        background: white;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        will-change: transform; /* Optimize for animations */
    }
    
    .modal-open {
        overflow: hidden;
    }
    
    /* Optimize register graphics for faster loading */
    .register-graphic {
        display: inline-block;
        width: 60px;
        height: 60px;
        background-size: contain;
        background-repeat: no-repeat;
        background-position: center;
        will-change: transform;
        transition: transform 0.2s ease;
    }
    
    .register-graphic:hover {
        transform: scale(1.1);
    }
</style>
