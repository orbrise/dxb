<div>
    <div class="ev-auth-topbar">
        <div class="ev-auth-topbar-inner">
            <a class="ev-auth-back-link" href="{{ url('/') }}" wire:navigate>
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                <span>Back</span>
            </a>
            <div class="ev-auth-topbar-title">Register</div>
        </div>
    </div>

    <div class="ev-register-page">
        @if (session()->has('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        @if ($errorMessage)
            <div class="alert alert-danger">
                {{ $errorMessage }}
            </div>
        @endif

        <div class="ev-register-card">
            <h1 class="ev-register-title">Register</h1>

            <form class="simple_form" wire:submit.prevent="submitForm">
                <a href="{{ route('google.redirect') }}" class="ev-google-btn">
                    <svg width="18" height="18" viewBox="0 0 18 18" xmlns="http://www.w3.org/2000/svg">
                        <path d="M17.64 9.2c0-.637-.057-1.251-.164-1.84H9v3.481h4.844c-.209 1.125-.843 2.078-1.796 2.717v2.258h2.908c1.702-1.567 2.684-3.874 2.684-6.615z" fill="#4285F4"/>
                        <path d="M9.003 18c2.43 0 4.467-.806 5.956-2.184L12.05 13.56c-.806.54-1.836.86-3.047.86-2.344 0-4.328-1.584-5.036-3.711H.957v2.332C2.438 15.983 5.482 18 9.003 18z" fill="#34A853"/>
                        <path d="M3.964 10.712c-.18-.54-.282-1.117-.282-1.71 0-.593.102-1.17.282-1.71V4.96H.957C.347 6.175 0 7.55 0 9.002c0 1.452.348 2.827.957 4.042l3.007-2.332z" fill="#FBBC05"/>
                        <path d="M9.003 3.58c1.321 0 2.508.454 3.44 1.345l2.582-2.58C13.464.891 11.428 0 9.002 0 5.48 0 2.438 2.017.957 4.958L3.964 7.29c.708-2.127 2.692-3.71 5.036-3.71z" fill="#EA4335"/>
                    </svg>
                    <span>Continue with Google</span>
                </a>

                <div class="ev-register-divider"><span>or register with email</span></div>

                <div class="ev-form-group">
                    <label for="account_name">Username <span class="ev-required">*</span></label>
                    <input wire:model="name" class="ev-auth-input" placeholder="Username" type="text" id="account_name" />
                    @error('name')
                        <span class="validation-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="ev-form-group">
                    <label for="account_email">Email <span class="ev-required">*</span></label>
                    <input wire:model="email" class="ev-auth-input" placeholder="Email" type="email" id="account_email" />
                    @error('email')
                        <span class="validation-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="ev-form-group">
                    <label for="account_password">Password <span class="ev-required">*</span></label>
                    <input wire:model="password" class="ev-auth-input" placeholder="Password" type="password" id="account_password" />
                    @error('password')
                        <span class="validation-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="ev-form-group">
                    <label for="account_phone">Phone Number <span class="ev-required">*</span></label>
                    <div class="ev-phone-row">
                        <div class="ev-country-code-wrapper">
                            <button class="ev-country-code-display ev-auth-input" id="countryCodeDisplay" type="button">
                                <span class="ev-country-selected">
                                    <img id="selectedFlag" src="https://flagcdn.com/w40/ae.png" alt="Flag" />
                                    <span id="selectedCode">Select</span>
                                </span>
                                <svg class="ev-country-chevron" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                            </button>

                            <div class="country-dropdown ev-country-dropdown" id="countryDropdown" style="display: none;">
                                <input type="text" class="ev-country-search" id="countrySearch" placeholder="Search country..." />
                                <div id="countryList">
                                    @foreach($countries as $country)
                                        <div class="country-option ev-country-option" data-code="{{ $country->phonecode }}" data-iso="{{ strtolower($country->iso) }}" data-name="{{ $country->nicename }}">
                                            <img src="https://flagcdn.com/w40/{{ strtolower($country->iso) }}.png" alt="{{ $country->nicename }} flag" />
                                            <span class="ev-country-name">{{ $country->nicename }}</span>
                                            <span class="ev-country-code">+{{ $country->phonecode }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <input type="hidden" wire:model="countrycode" id="account_countrycode">
                        </div>

                        <input wire:model="phone" class="ev-auth-input" placeholder="Phone number" type="text" id="account_phone" />
                    </div>
                    @error('countrycode')
                        <span class="validation-error">{{ $message }}</span>
                    @enderror
                    @error('phone')
                        <span class="validation-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="ev-register-actions">
                    <button class="ev-btn-register-submit" type="submit" wire:loading.attr="disabled" wire:target="submitForm">
                        <span class="ev-btn-register-label" wire:loading.remove wire:target="submitForm">
                            <span>Register</span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="9 18 15 12 9 6"></polyline></svg>
                        </span>
                        <span wire:loading wire:target="submitForm">
                            <svg class="ev-spinner" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
                            Registering...
                        </span>
                    </button>
                </div>
            </form>

            <a class="ev-register-signin" href="{{ route('sign-in') }}" wire:navigate>Already have an account? Sign in</a>
        </div>
    </div>
</div>

@push('css')
<style>
.ev-auth-topbar{background:#12191c;border-top:1px solid #1f262f;border-bottom:1px solid #1f262f}
.ev-auth-topbar-inner{max-width:1180px;margin:0 auto;padding:10px 16px;display:flex;align-items:center;justify-content:center;position:relative}
.ev-auth-back-link{display:inline-flex;align-items:center;gap:4px;color:#b5df19;text-decoration:none;font-size:15px;position:absolute;left:16px}
.ev-auth-topbar-title{color:#d6dbe6;font-size:22px;font-weight:400;line-height:1}

.ev-register-page .alert{max-width:420px;margin:16px auto;padding:12px 16px;border-radius:8px;font-size:15px}
.ev-register-page .alert-success{background:rgba(193,241,29,.12);border:1px solid rgba(193,241,29,.3);color:#c1f11d}
.ev-register-page .alert-danger{background:rgba(239,68,68,.12);border:1px solid rgba(239,68,68,.3);color:#f87171}
.validation-error{color:#f87171;font-size:13px;margin-top:4px;display:block}

.ev-register-page{max-width:1180px;margin:0 auto;padding:36px 16px 60px}
.ev-register-card{max-width:420px;margin:0 auto}
.ev-register-title{margin:0 0 18px;color:#fff;font-size:30px;font-weight:500;line-height:1.1}

.ev-google-btn{height:46px;border:1px solid #31384a;border-radius:6px;color:#d8deea;text-decoration:none;display:flex;align-items:center;justify-content:center;gap:8px;font-size:14px}
.ev-register-divider{margin:16px 0 14px;display:flex;align-items:center;color:#dce1ec;font-size:32px;font-weight:400}
.ev-register-divider::before,.ev-register-divider::after{content:'';height:1px;background:#2a3241;flex:1}
.ev-register-divider span{padding:0 10px;font-size:16px;color:#d8deea}

.ev-form-group{margin-bottom:30px}
.ev-form-group label{display:block;color:#dce1ec;font-size:16px;margin:0 0 6px;font-weight:400}
.ev-required{color:#f87171}
.ev-auth-input{width:100%;height:46px;border-radius:4px;border:1px solid #2e3646;background:#0e121a;color:#fff;padding:0 12px;font-size:18px}
.ev-auth-input:focus{outline:none;border-color:#3d475d}

.ev-phone-row{display:flex;gap:10px}
.ev-country-code-wrapper{position:relative;width:160px;flex-shrink:0}
.ev-country-code-display{display:flex;align-items:center;justify-content:space-between;cursor:pointer}
.ev-country-selected{display:inline-flex;align-items:center;gap:8px;overflow:hidden;white-space:nowrap;color:#d8deea}
#selectedFlag{width:24px;height:16px;object-fit:cover;border-radius:2px;display:none}
.ev-country-chevron{color:#9ca3af;flex-shrink:0}

.ev-country-dropdown{position:absolute;top:calc(100% + 4px);left:0;right:0;max-height:250px;overflow-y:auto;background:#0e121a;border:1px solid #2e3646;border-radius:6px;z-index:1000;box-shadow:0 4px 12px rgba(0,0,0,0.35)}
.ev-country-search{width:100%;height:40px;background:#0e121a;color:#fff;border:0;border-bottom:1px solid #2e3646;padding:0 12px;outline:none}
.ev-country-option{display:flex;align-items:center;gap:10px;padding:8px 12px;cursor:pointer;border-bottom:1px solid #1f2937}
.ev-country-option:last-child{border-bottom:none}
.ev-country-option img{width:24px;height:16px;object-fit:cover;border-radius:2px}
.ev-country-name{color:#d8deea}
.ev-country-code{color:#9ca3af;margin-left:auto}
.ev-country-option:hover{background:#151c28}

.ev-register-actions{display:flex;align-items:center;margin-top:3rem}
.ev-btn-register-submit{height:34px;min-width:126px;border-radius:999px;border:none;background:#c1f11d;color:#111;font-size:20px;font-weight:500;padding:0 20px;cursor:pointer}
.ev-btn-register-submit[disabled]{opacity:.7;cursor:not-allowed}
.ev-btn-register-label{display:inline-flex;align-items:center;gap:2px}

.ev-register-signin{display:inline-block;margin-top:25px;color:#b5df19;text-decoration:none;font-size:14px}

.ev-spinner{animation:ev-spin 1s linear infinite;vertical-align:middle}
@keyframes ev-spin{to{transform:rotate(360deg)}}

.ev-country-dropdown::-webkit-scrollbar{width:6px}
.ev-country-dropdown::-webkit-scrollbar-thumb{background:#364152;border-radius:3px}
.ev-country-name {font-size: 12px;}
@media(max-width:768px){
  .ev-auth-topbar-title{font-size:26px}
  .ev-register-title{font-size:36px}
  .ev-google-btn{font-size:18px}
  .ev-btn-register-submit{font-size:24px}
}
</style>
@endpush

@push('js')
<script>
// Phone number masking based on country code
const phoneMasks = {
    '971': '## ### ####',      // UAE
    '92': '### #######',        // Pakistan
    '1': '### ### ####',        // USA/Canada
    '44': '#### ### ####',      // UK
    '91': '##### #####',        // India
    '966': '# ### ####',        // Saudi Arabia
    '974': '#### ####',         // Qatar
    '973': '#### ####',         // Bahrain
    '968': '#### ####',         // Oman
    '965': '#### ####',         // Kuwait
    '961': '# ### ###',         // Lebanon
    '962': '# #### ####',       // Jordan
    '20': '### ### ####',       // Egypt
    '90': '### ### ## ##',      // Turkey
    '33': '# ## ## ## ##',      // France
    '49': '### #######',        // Germany
    '39': '### ### ####',       // Italy
    '34': '### ### ###',        // Spain
    '7': '### ### ## ##',       // Russia
    '86': '### #### ####',      // China
    '81': '## #### ####',       // Japan
    '82': '## #### ####',       // South Korea
    '60': '## ### ####',        // Malaysia
    '65': '#### ####',          // Singapore
    '66': '## ### ####',        // Thailand
    '63': '### ### ####',       // Philippines
    '84': '### ### ####',       // Vietnam
    '62': '### ### ####',       // Indonesia
    '61': '### ### ###',        // Australia
    '64': '## ### ####',        // New Zealand
    '27': '## ### ####',        // South Africa
    '234': '### ### ####',      // Nigeria
    '254': '### ######',        // Kenya
    '55': '## ##### ####',      // Brazil
    '52': '## #### ####',       // Mexico
    '54': '## #### ####',       // Argentina
    '57': '### #######',        // Colombia
    '351': '### ### ###',       // Portugal
    '31': '## ########',        // Netherlands
    '32': '### ## ## ##',       // Belgium
    '41': '## ### ## ##',       // Switzerland
    '43': '### #######',        // Austria
    '45': '## ## ## ##',        // Denmark
    '46': '## ### ## ##',       // Sweden
    '47': '### ## ###',         // Norway
    '48': '### ### ###',        // Poland
    '30': '### ### ####',       // Greece
    '420': '### ### ###',       // Czech Republic
    '36': '## ### ####',        // Hungary
    '40': '### ### ###',        // Romania
    '380': '## ### ## ##',      // Ukraine
    '972': '## ### ####',       // Israel
    '212': '### ######',        // Morocco
    '213': '### ## ## ##',      // Algeria
    '216': '## ### ###',        // Tunisia
};

const exampleNumbers = {
    '971': '50 123 4567',      // UAE
    '92': '300 1234567',        // Pakistan
    '1': '202 555 0123',        // USA/Canada
    '44': '7700 900 123',       // UK
    '91': '98765 43210',        // India
    '966': '5 012 3456',        // Saudi Arabia
    '974': '3312 3456',         // Qatar
    '973': '3600 1234',         // Bahrain
    '968': '9123 4567',         // Oman
    '965': '9012 3456',         // Kuwait
    '961': '3 123 456',         // Lebanon
    '962': '7 9012 3456',       // Jordan
    '20': '100 123 4567',       // Egypt
    '90': '532 123 45 67',      // Turkey
    '33': '6 12 34 56 78',      // France
    '49': '151 2345678',        // Germany
    '39': '312 345 6789',       // Italy
    '34': '612 345 678',        // Spain
    '7': '912 345 67 89',       // Russia
    '86': '138 0013 8000',      // China
    '81': '90 1234 5678',       // Japan
    '82': '10 1234 5678',       // South Korea
    '60': '12 345 6789',        // Malaysia
    '65': '8123 4567',          // Singapore
    '66': '81 234 5678',        // Thailand
    '63': '912 345 6789',       // Philippines
    '84': '912 345 678',        // Vietnam
    '62': '812 345 6789',       // Indonesia
    '61': '412 345 678',        // Australia
    '64': '21 123 4567',        // New Zealand
    '27': '82 123 4567',        // South Africa
    '234': '802 345 6789',      // Nigeria
    '254': '712 345678',        // Kenya
    '55': '11 91234 5678',      // Brazil
    '52': '55 1234 5678',       // Mexico
    '54': '11 1234 5678',       // Argentina
    '57': '321 1234567',        // Colombia
    '351': '912 345 678',       // Portugal
    '31': '06 12345678',        // Netherlands
    '32': '470 12 34 56',       // Belgium
    '41': '78 123 45 67',       // Switzerland
    '43': '660 1234567',        // Austria
    '45': '31 23 45 67',        // Denmark
    '46': '70 123 45 67',       // Sweden
    '47': '412 34 567',         // Norway
    '48': '512 345 678',        // Poland
    '30': '690 123 4567',       // Greece
    '420': '601 234 567',       // Czech Republic
    '36': '20 123 4567',        // Hungary
    '40': '712 345 678',        // Romania
    '380': '50 123 45 67',      // Ukraine
    '972': '50 123 4567',       // Israel
    '212': '612 345678',        // Morocco
    '213': '551 23 45 67',      // Algeria
    '216': '20 123 456',        // Tunisia
};

function applyPhoneMask(input, mask) {
    const handler = function(e) {
        let value = this.value.replace(/\D/g, '');
        value = value.replace(/^0+/, '');
        
        let maskedValue = '';
        let valueIndex = 0;
        
        for (let i = 0; i < mask.length && valueIndex < value.length; i++) {
            if (mask[i] === '#') {
                maskedValue += value[valueIndex];
                valueIndex++;
            } else {
                if (valueIndex > 0) {
                    maskedValue += mask[i];
                }
            }
        }
        
        if (valueIndex < value.length) {
            maskedValue += ' ' + value.substring(valueIndex);
        }
        
        this.value = maskedValue;
        this.dispatchEvent(new Event('input', { bubbles: true }));
    };
    
    if (input._phoneMaskHandler) {
        input.removeEventListener('input', input._phoneMaskHandler);
    }
    input._phoneMaskHandler = handler;
    input.addEventListener('input', handler);
}

function updatePhoneMask(countryCode, phoneInputId) {
    const phoneInput = document.getElementById(phoneInputId);
    if (!phoneInput) return;
    
    if (countryCode && phoneMasks[countryCode]) {
        applyPhoneMask(phoneInput, phoneMasks[countryCode]);
        
        let placeholder = exampleNumbers[countryCode];
        if (!placeholder) {
            placeholder = phoneMasks[countryCode].replace(/#/g, '0');
        }
        phoneInput.setAttribute('placeholder', placeholder);
    } else {
        if (phoneInput._phoneMaskHandler) {
            phoneInput.removeEventListener('input', phoneInput._phoneMaskHandler);
            phoneInput._phoneMaskHandler = null;
        }
        phoneInput.setAttribute('placeholder', 'Phone number');
    }
}

function initCountryDropdown() {
    const display = document.getElementById('countryCodeDisplay');
    const dropdown = document.getElementById('countryDropdown');
    const searchInput = document.getElementById('countrySearch');
    const countryList = document.getElementById('countryList');
    const hiddenInput = document.getElementById('account_countrycode');
    const selectedFlag = document.getElementById('selectedFlag');
    const selectedCode = document.getElementById('selectedCode');
    
    if (!display || !dropdown || display.dataset.initialized) return;
    display.dataset.initialized = 'true';
    
    // Toggle dropdown
    display.addEventListener('click', function(e) {
        e.stopPropagation();
        dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
        if (dropdown.style.display === 'block') {
            searchInput.focus();
        }
    });
    
    // Search functionality
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const options = countryList.querySelectorAll('.country-option');
        options.forEach(function(option) {
            const name = option.dataset.name.toLowerCase();
            const code = option.dataset.code;
            if (name.includes(searchTerm) || code.includes(searchTerm)) {
                option.style.display = 'flex';
            } else {
                option.style.display = 'none';
            }
        });
    });
    
    // Select country
    countryList.addEventListener('click', function(e) {
        const option = e.target.closest('.country-option');
        if (option) {
            const code = option.dataset.code;
            const iso = option.dataset.iso;
            
            selectedFlag.src = 'https://flagcdn.com/w40/' + iso + '.png';
            selectedFlag.style.display = 'block';
            selectedCode.textContent = '+' + code;
            
            hiddenInput.value = code;
            hiddenInput.dispatchEvent(new Event('input', { bubbles: true }));
            
            // Apply phone mask for selected country
            updatePhoneMask(code, 'account_phone');
            
            dropdown.style.display = 'none';
            searchInput.value = '';
            countryList.querySelectorAll('.country-option').forEach(opt => opt.style.display = 'flex');
        }
    });
    
    // Close dropdown on outside click
    document.addEventListener('click', function(e) {
        if (!display.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.style.display = 'none';
        }
    });
}

document.addEventListener('DOMContentLoaded', initCountryDropdown);
document.addEventListener('livewire:navigated', initCountryDropdown);
document.addEventListener('livewire:initialized', initCountryDropdown);
setTimeout(initCountryDropdown, 100);
</script>
@endpush
