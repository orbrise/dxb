@section('headerform')
<div class="nav-bar navbar-top-nav">
    <div class="container-fluid"> 
        <a class="back-link" href="/my-account">
            <i class="fa fa-angle-left fa-fw"></i>
            <span class="hidden-xs">My Account</span>
          </a>
        <div class="title">
            <h1> <a href="/my-account/edit">My Account - Edit</a></h1>
        </div>
    </div>
</div>
@endsection

<div class="container-fluid">
    <div class="content-wrapper no-sidebar">
     @if(session()->has('message'))
              <div class="alert alert-success mt-3" style="margin:4px 2px;">
                  {{ session('message') }}
              </div>
          @endif
          
        <!-- Profile Completion Progress -->
        @php $profileCompletion = $this->profileCompletion; @endphp
        <div class="profile-completion-card" style="background: rgba(220, 166, 35, 0.1); border: 1px solid rgba(220, 166, 35, 0.3); border-radius: 12px; padding: 20px; margin: 0px 0; color: #dca623; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
            <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 15px;">
                <div style="flex: 1; min-width: 200px;">
                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                        @if($profileCompletion['percentage'] >= 100)
                            <i class="fa fa-check-circle" style="font-size: 24px;"></i>
                            <h4 style="margin: 0; font-weight: 600; color: #dca623;">Profile Complete!</h4>
                        @else
                            <i class="fa fa-user-circle" style="font-size: 24px;"></i>
                            <h4 style="margin: 0; font-weight: 600; color: #dca623;">Complete Your Profile</h4>
                        @endif
                    </div>
                    
                    @if(!$profileCompletion['isComplete'])
                        <p style="margin: 0 0 10px 0; opacity: 0.9; font-size: 14px; color: #dca623;">
                            Complete your profile to attract more visitors and build trust
                        </p>
                        @if(count($profileCompletion['incomplete']) > 0)
                            <div style="font-size: 13px; color: #dca623;">
                                <i class="fa fa-lightbulb-o"></i> 
                                Missing: @foreach($profileCompletion['incomplete'] as $index => $field)<span style="color: #dca623;">{{ $field }} <span style="color: #ff0000; font-weight: bold;">*</span></span>{{ $index < count($profileCompletion['incomplete']) - 1 ? ', ' : '' }}@endforeach
                            </div>
                        @endif
                    @else
                        <p style="margin: 0; opacity: 0.9; font-size: 14px; color: #dca623;">
                            Great job! Your profile is fully complete and ready to attract visitors.
                        </p>
                    @endif
                </div>
                
                <div style="text-align: center; min-width: 100px;">
                    <div style="position: relative; display: inline-block;">
                        <svg width="80" height="80" viewBox="0 0 80 80">
                            <circle cx="40" cy="40" r="35" fill="none" stroke="rgba(220, 166, 35, 0.3)" stroke-width="6"/>
                            <circle cx="40" cy="40" r="35" fill="none" stroke="#dca623" stroke-width="6" 
                                stroke-dasharray="{{ 2 * 3.14159 * 35 }}" 
                                stroke-dashoffset="{{ 2 * 3.14159 * 35 * (1 - $profileCompletion['percentage'] / 100) }}"
                                transform="rotate(-90 40 40)"
                                style="transition: stroke-dashoffset 0.5s ease;"/>
                        </svg>
                        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-size: 18px; font-weight: 700; color: #dca623;">
                            {{ $profileCompletion['percentage'] }}%
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Progress bar -->
            <div style="margin-top: 15px;">
                <div style="background: rgba(220, 166, 35, 0.2); height: 8px; border-radius: 4px; overflow: hidden;">
                    <div style="background: #dca623; height: 100%; width: {{ $profileCompletion['percentage'] }}%; border-radius: 4px; transition: width 0.5s ease;"></div>
                </div>
            </div>
        </div>

        <div id="content">
            @include('components.account-nav')
        <form wire:submit.prevent="save" class="simple_form validate bright-form edit-account" id="edit_my_account" novalidate="novalidate" enctype="multipart/form-data" action="/my-account" accept-charset="UTF-8" method="post">
          <input name="utf8" type="hidden" value="&#x2713;" />
          <input type="hidden" name="_method" value="patch" />
          <input type="hidden" name="authenticity_token" value="CZfbClvVIgKzCm18prPm1IbohnD8cVtJQnO/q7LSnCtnnJYIn7zefulMCEtJWAI9vMY9NmYsesPgmjh6bSs8zA==" />
          <div class="row">
            <div class="col-sm-10">
              <fieldset class="row">
                <div class="col-sm-6">
                  <div class="form-group radio_buttons required my_account_account_type">
                    <label class="radio_buttons required control-label">
                      <abbr title="required"></abbr> Account type </label>
                    <input type="hidden" name="my_account[account_type]" value="" />
                    <span class="radio-inline">
                      <label for="my_account_account_type_individual_escort">
                        <input wire:model.live="account_type" class="radio_buttons required" type="radio" value="2"  name="my_account[account_type]" id="my_account_account_type_individual_escort" />Individual advertiser </label>
                    </span>
                    <span class="radio-inline">
                      <label for="my_account_account_type_escort_agency">
                        <input wire:model.live="account_type" class="radio_buttons required" type="radio" value="3" name="my_account[account_type]" id="my_account_account_type_escort_agency" />Escort agency </label>
                    </span>
                  </div>
                  <div class="form-group email optional my_account_email">
                    <label class="email optional control-label" for="my_account_email">Email</label>
                    <input disabled wire:model="email" class="string email optional form-control form-control" maxlength="191" type="email" size="191" value="orbrise2@gmail.com" name="my_account[email]" id="my_account_email" />
                    @error('email') <span class="text-danger">{{ $message }}</span> @enderror
  
                </div>
                  <div class="form-group string required my_account_display_name">
                    <label class="string required control-label" for="my_account_display_name">
                      <abbr title="required"></abbr> Display name </label>
                    <input wire:model.live.debounce.500ms="display_name" class="string required form-control" maxlength="191" size="191" type="text" />
                    @error('display_name') <span class="text-danger">{{ $message }}</span> @enderror
 
                </div>
                  <div class="form-group text optional my_account_about_me">
                    <label class="text optional control-label" for="my_account_about_me">About me</label>
                    <textarea wire:model.live.debounce.500ms="about_me" rows="4" class="text optional form-control" maxlength="4294967295" name="my_account[about_me]" id="my_account_about_me"></textarea>
                  </div>
                  
                  <div class="form-group phone optional">
                    <label class="control-label" for="my_account_phone">Phone Number</label>
                    <div class="d-flex" style="display: flex; gap: 10px;">
                      <div class="country-code-wrapper" style="position: relative; width: 160px; flex-shrink: 0;">
                        <div class="country-code-display form-control" id="countryCodeDisplay" style="cursor: pointer; display: flex; align-items: center; justify-content: space-between; padding: 6px 10px; height: 34px;">
                          <div style="display: flex; align-items: center; gap: 8px;">
                            <img id="selectedFlag" src="https://flagcdn.com/w40/{{ $countrycode ? strtolower(\App\Models\Country::where('phonecode', $countrycode)->first()?->iso ?? 'ae') : 'ae' }}.png" style="width: 24px; height: 16px; object-fit: cover; border-radius: 2px; {{ $countrycode ? '' : 'display: none;' }}">
                            <span id="selectedCode">{{ $countrycode ? '+' . $countrycode : 'Select' }}</span>
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
                        <input type="hidden" wire:model.live="countrycode" id="my_account_countrycode">
                      </div>
                      <input wire:model.live.debounce.500ms="phone" class="form-control" placeholder="Phone number" type="text" id="my_account_phone" style="flex: 1;"/>
                    </div>
                    @error('countrycode') <span class="text-danger">{{ $message }}</span> @enderror
                    @error('phone') <span class="text-danger">{{ $message }}</span> @enderror
                  </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group file optional my_account_avatar">
                        <label class="file optional control-label" for="my_account_avatar">Profile photo</label>
                        <div class="picture-field-container">
                            <div class="avatar-upload-wrapper">
                                <div class="preview" style="height: 128px; width: 128px; overflow: hidden; border-radius: 4px; flex-shrink: 0;">
                                    @if(!empty(auth()->user()->avatar))
                                    <img src="{{ Storage::url(auth()->user()->avatar) }}" width="128" height="128" style="object-fit: cover;">
                                    @else
                                    <img src="https://www.gravatar.com/avatar/28c5175931d8449e6acb2b72d8900455?s=128&amp;d=identicon" width="128" height="128">
                                    @endif
                                </div>
                                <div class="upload-controls">
                                    @if(!empty(auth()->user()->avatar))
                                    <button wire:click="removePhoto" class="btn-link remove-picture" type="button" style="text-align: left; padding: 0; color: #f4b827; margin-bottom: 10px; display: block;"><i class="fa fa-times fa-inline"></i> Remove photo</button>
                                    @endif
                                    <div>
                                        <input wire:model="avatar" accept="image/jpeg,image/jpg,image/png,image/gif" class="file optional picture-field" type="file" name="my_account[avatar]" id="my_account_avatar" style="position: relative !important; opacity: 1 !important; z-index: 1 !important;">
                                        @error('avatar') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>
                            <div style="margin-top: 10px;">
                                <span class="help-block hint" style="color: #999; font-size: 13px;">JPEG, JPG, GIF, PNG, maximum size of 4MB</span> 
                            </div>
                        </div>
                    </div>
                    
                    <style>
                        .avatar-upload-wrapper {
                            display: flex;
                            align-items: flex-start;
                            gap: 15px;
                        }
                        
                        .upload-controls {
                            display: flex;
                            flex-direction: column;
                            gap: 10px;
                            flex: 1;
                            margin-top: 15px;
                        }
                        
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
                        
                        @media (max-width: 768px) {
                            .avatar-upload-wrapper {
                                flex-direction: column;
                                gap: 10px;
                            }
                            
                            .upload-controls {
                                margin-top: 0;
                            }
                        }
                    </style>
              </fieldset>
              <button class="btn btn-primary btn-lg btn-xs-block margin-top" data-btn-submit="" type="submit">Save</button>
             
            </div>
          </div>
        </form>
        </div>
    </div>
</div>

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

function initAccountCountryDropdown() {
    const display = document.getElementById('countryCodeDisplay');
    const dropdown = document.getElementById('countryDropdown');
    const searchInput = document.getElementById('countrySearch');
    const countryList = document.getElementById('countryList');
    const hiddenInput = document.getElementById('my_account_countrycode');
    const selectedFlag = document.getElementById('selectedFlag');
    const selectedCode = document.getElementById('selectedCode');
    
    if (!display || !dropdown || display.dataset.initialized) return;
    display.dataset.initialized = 'true';
    
    // Apply mask for existing country code on page load
    if (hiddenInput && hiddenInput.value) {
        updatePhoneMask(hiddenInput.value, 'my_account_phone');
    }
    
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
            updatePhoneMask(code, 'my_account_phone');
            
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

document.addEventListener('DOMContentLoaded', initAccountCountryDropdown);
document.addEventListener('livewire:navigated', initAccountCountryDropdown);
document.addEventListener('livewire:initialized', initAccountCountryDropdown);
setTimeout(initAccountCountryDropdown, 100);
</script>
@endpush
