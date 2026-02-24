@section('headerform')
<div class="nav-bar navbar-top-nav">
    <div class="container-fluid"> 
        <a class="back-link" href="{{url('/')}}" wire:navigate>
            <i class="fa fa-angle-left fa-fw"></i><span class="hidden-xs">Back</span></a>
        <div class="title">
            <h1><a href="/register">Register</a></h1></div>
    </div>
</div>
@endsection

@section("registertype")
@include('components.layouts.registertype')
@endsection

<div>
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

    <div class="row container mt-3">
        <div class="col-sm-6 col-lg-5 col-lg-offset-1">
            <div class="auth-form sign-in-form">
                <h1 class="margin-bottom hidden-xs">Register</h1>
                <form class="simple_form" wire:submit.prevent="submitForm">

                    <div class="form-group text-center">
                        <a href="{{ route('google.redirect') }}" class="btn btn-lg btn-xs-block" style="color: #e9ad1e;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    padding: 10px 20px;
    border: 2px solid white;">
                            <svg width="18" height="18" viewBox="0 0 18 18" xmlns="http://www.w3.org/2000/svg">
                                <path d="M17.64 9.2c0-.637-.057-1.251-.164-1.84H9v3.481h4.844c-.209 1.125-.843 2.078-1.796 2.717v2.258h2.908c1.702-1.567 2.684-3.874 2.684-6.615z" fill="#4285F4"/>
                                <path d="M9.003 18c2.43 0 4.467-.806 5.956-2.184L12.05 13.56c-.806.54-1.836.86-3.047.86-2.344 0-4.328-1.584-5.036-3.711H.957v2.332C2.438 15.983 5.482 18 9.003 18z" fill="#34A853"/>
                                <path d="M3.964 10.712c-.18-.54-.282-1.117-.282-1.71 0-.593.102-1.17.282-1.71V4.96H.957C.347 6.175 0 7.55 0 9.002c0 1.452.348 2.827.957 4.042l3.007-2.332z" fill="#FBBC05"/>
                                <path d="M9.003 3.58c1.321 0 2.508.454 3.44 1.345l2.582-2.58C13.464.891 11.428 0 9.002 0 5.48 0 2.438 2.017.957 4.958L3.964 7.29c.708-2.127 2.692-3.71 5.036-3.71z" fill="#EA4335"/>
                            </svg>
                            <span>Continue with Google</span>
                        </a>
                    </div>

                    <div class="divider"> <span style="margin-inline: 9px;font-size: 15px;color: #e9ad1e;">or register with email</span> </div>

                    <div class="form-group string required account_name">
                        <label class="string required control-label" for="account_name">Username <abbr title="required">*</abbr></label>
                        <input wire:model="name" class="string required form-control input-lg validate" placeholder="Username" type="text" id="account_name"/>
                        @error('name')
                            <span class="validation-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group email required account_email">
                        <label class="email required control-label" for="account_email">Email <abbr title="required">*</abbr></label>
                        <input wire:model="email" class="string email required form-control input-lg validate" placeholder="Email" type="email" id="account_email"/>
                        @error('email')
                            <span class="validation-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group password required account_password">
                        <label class="password required control-label" for="account_password">Password <abbr title="required">*</abbr></label>
                        <input wire:model="password" class="password required form-control input-lg validate" placeholder="Password (min. 6 characters)" type="password" id="account_password"/>
                        @error('password')
                            <span class="validation-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group phone required">
                        <label class="control-label" for="account_phone">Phone Number <abbr title="required">*</abbr></label>
                        <div class="d-flex" style="display: flex; gap: 10px;">
                            <div class="country-code-wrapper" style="position: relative; width: 160px; flex-shrink: 0;">
                                <div class="country-code-display form-control input-lg" id="countryCodeDisplay" style="cursor: pointer; display: flex; align-items: center; justify-content: space-between; padding: 6px 10px;">
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <img id="selectedFlag" src="https://flagcdn.com/w40/ae.png" style="width: 24px; height: 16px; object-fit: cover; border-radius: 2px; display: none;">
                                        <span id="selectedCode">Select</span>
                                    </div>
                                    <i class="fa fa-chevron-down" style="font-size: 10px;"></i>
                                </div>
                                <div class="country-dropdown" id="countryDropdown" style="display: none; position: absolute; top: 100%; left: 0; right: 0; max-height: 250px; overflow-y: auto; background: #fff; border: 1px solid #ccc; border-radius: 4px; z-index: 1000; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                                    <input type="text" class="form-control" id="countrySearch" placeholder="Search country..." style="border: none; border-bottom: 1px solid #eee; border-radius: 0;">
                                    <div id="countryList">
                                        @foreach($countries as $country)
                                            <div class="country-option" data-code="{{ $country->phonecode }}" data-iso="{{ strtolower($country->iso) }}" data-name="{{ $country->nicename }}" style="display: flex; align-items: center; gap: 10px; padding: 8px 12px; cursor: pointer; border-bottom: 1px solid #f0f0f0;">
                                                <img src="https://flagcdn.com/w40/{{ strtolower($country->iso) }}.png" style="width: 24px; height: 16px; object-fit: cover; border-radius: 2px;">
                                                <span style="color: #333;">{{ $country->nicename }}</span>
                                                <span style="color: #888; margin-left: auto;">+{{ $country->phonecode }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <input type="hidden" wire:model="countrycode" id="account_countrycode">
                            </div>
                            <input wire:model="phone" class="form-control input-lg" placeholder="Phone number" type="text" id="account_phone" style="flex: 1;"/>
                        </div>
                        @error('countrycode')
                            <span class="validation-error">{{ $message }}</span>
                        @enderror
                        @error('phone')
                            <span class="validation-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <button class="btn btn-primary btn-lg btn-xs-block" type="submit" wire:loading.attr="disabled" wire:target="submitForm">
                            <span wire:loading.remove wire:target="submitForm">Register</span>
                            <span wire:loading wire:target="submitForm">
                                <i class="fa fa-spinner fa-spin"></i> Registering...
                            </span>
                        </button>
                    </div>

                </form>

                <div class="auth-other-links padding-bottom">
                    <div class="list-group list-group-dark">
                        <a class="list-group-item" href="{{route('sign-in')}}">Already have an account? Sign in</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-5">
            <div id="user-register-incentive"><p class="h1">We know you want to!</p><div class="register-graphic" id="user-incentive"></div></div>
        </div>
    </div>
</div>

@push('css')
<style>
    .country-option:hover {
        background-color: #f5f5f5 !important;
    }
    .country-dropdown::-webkit-scrollbar {
        width: 6px;
    }
    .country-dropdown::-webkit-scrollbar-thumb {
        background: #ccc;
        border-radius: 3px;
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
