@push('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
<style>
    .ev-header {
        background: #0D1011;
    }
    .ev-back-bar {
        background: #131616;
        padding: 12px 0;
    }
    .ev-back-bar a {
        color: var(--accent, #C1F11D);
        text-decoration: none;
        font-size: 16px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-weight: 400;
    }
    .ev-back-bar h1 {
        color: #fff;
        font-size: 18px;
        font-weight: 600;
        margin: 0;
        text-align: center;
    }
    .ev-back-bar h1 a {
        color: #fff;
        text-decoration: none;
    }
    .ev-account-tabs {
        display: flex;
        gap: 8px;
        padding: 20px 0;
        flex-wrap: wrap;
    }
    .ev-account-tabs a {
        display: inline-flex;
        align-items: center;
        width: 170px;
        gap: 8px;
        padding: 6px 0px;
        border-radius: 5px;
        color: #fff;
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
        transition: all 0.2s ease;
        background: #1D2224;
        justify-content: center;
    }
    .ev-account-tabs a:hover {
        border-color: var(--accent, #C1F11D);
        color: var(--accent, #C1F11D);
    }
    .ev-account-tabs a.active {
        color: #C1F11D;
    }
    .ev-alert-success {
        background: rgba(193, 241, 29, 0.1);
        border: 1px solid var(--accent, #C1F11D);
        color: var(--accent, #C1F11D);
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 16px;
        font-size: 14px;
    }
    /* Profile completion card */
    .ev-completion-card {
        background: rgba(193, 241, 29, 0.06);
        border: 1px solid rgba(193, 241, 29, 0.25);
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
        color: var(--accent, #C1F11D);
    }
    .ev-completion-card h4 {
        margin: 0;
        font-weight: 600;
        color: var(--accent, #C1F11D);
        font-size: 16px;
    }
    .ev-completion-card p {
        margin: 0 0 10px 0;
        opacity: 0.9;
        font-size: 14px;
        color: var(--accent, #C1F11D);
    }
    .ev-completion-card .ev-missing {
        font-size: 13px;
        color: var(--accent, #C1F11D);
    }
    .ev-completion-card .ev-missing span {
        color: var(--accent, #C1F11D);
    }
    .ev-progress-bar-bg {
        background: rgba(193, 241, 29, 0.15);
        height: 8px;
        border-radius: 4px;
        overflow: hidden;
        margin-top: 15px;
    }
    .ev-progress-bar-fill {
        background: var(--accent, #C1F11D);
        height: 100%;
        border-radius: 4px;
        transition: width 0.5s ease;
    }
    /* Form styles */
    .ev-edit-form {
        color: #fff;
    }
    .ev-form-row {
        display: flex;
        gap: 40px;
        flex-wrap: wrap;
    }
    .ev-form-col {
        flex: 1 1 400px;
        min-width: 0;
    }
    .ev-form-group {
        margin-bottom: 20px;
    }
    .ev-form-group label {
        display: block;
        color: #fff;
        font-size: 14px;
        font-weight: 500;
        margin-bottom: 8px;
    }
    .ev-form-group .ev-input {
        width: 100%;
        padding: 10px 14px;
        background: var(--bg-secondary, #111111);
        border: 1px solid var(--border-color, #2a2a2a);
        border-radius: 8px;
        color: #fff;
        font-size: 14px;
        font-family: inherit;
        outline: none;
        transition: border-color 0.2s;
        box-sizing: border-box;
    }
    .ev-form-group .ev-input:focus {
        border-color: var(--accent, #C1F11D);
    }
    .ev-form-group .ev-input:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
    .ev-form-group .ev-input::placeholder {
        color: var(--text-muted, #666);
    }
    .ev-form-group textarea.ev-input {
        resize: vertical;
        min-height: 120px;
    }
    .ev-radio-group {
        display: flex;
        gap: 20px;
        flex-wrap: wrap;
    }
    .ev-radio-group label {
        display: flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
        font-weight: 400;
        margin-bottom: 0;
    }
    .ev-radio-group input[type="radio"] {
        accent-color: var(--accent, #C1F11D);
        width: 16px;
        height: 16px;
    }
    .ev-text-danger {
        color: #dc3545;
        font-size: 13px;
        margin-top: 4px;
        display: block;
    }
    /* Phone input */
    .ev-phone-row {
        display: flex;
        gap: 10px;
    }
    .ev-country-select {
        position: relative;
        width: 160px;
        flex-shrink: 0;
    }
    .ev-country-display {
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 12px;
        background: var(--bg-secondary, #111111);
        border: 1px solid var(--border-color, #2a2a2a);
        border-radius: 8px;
        color: #fff;
        font-size: 14px;
        transition: border-color 0.2s;
    }
    .ev-country-display:hover {
        border-color: var(--accent, #C1F11D);
    }
    .ev-country-dropdown {
        display: none;
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        max-height: 250px;
        overflow-y: auto;
        background: var(--bg-card, #1a1a1a);
        border: 1px solid var(--border-color, #2a2a2a);
        border-radius: 8px;
        z-index: 1000;
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        margin-top: 4px;
    }
    .ev-country-dropdown input {
        width: 100%;
        padding: 10px 12px;
        background: var(--bg-secondary, #111);
        border: none;
        border-bottom: 1px solid var(--border-color, #2a2a2a);
        color: #fff;
        font-size: 14px;
        font-family: inherit;
        outline: none;
        box-sizing: border-box;
    }
    .ev-country-dropdown input::placeholder {
        color: var(--text-muted, #666);
    }
    .country-option {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 12px;
        cursor: pointer;
        border-bottom: 1px solid var(--border-color, #2a2a2a);
        transition: background 0.15s;
    }
    .country-option:hover {
        background: var(--bg-card-hover, #222) !important;
    }
    .country-option span {
        color: #fff !important;
    }
    .country-option span:last-child {
        color: var(--text-muted, #666) !important;
        margin-left: auto;
    }
    .ev-country-dropdown::-webkit-scrollbar {
        width: 6px;
    }
    .ev-country-dropdown::-webkit-scrollbar-thumb {
        background: #444;
        border-radius: 3px;
    }
    /* Profile photo section */
    .ev-avatar-section {
        display: flex;
        align-items: flex-start;
        gap: 15px;
    }
    .ev-avatar-preview {
        width: 128px;
        height: 128px;
        overflow: hidden;
        border-radius: 8px;
        flex-shrink: 0;
        border: 1px solid var(--border-color, #2a2a2a);
    }
    .ev-avatar-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .ev-avatar-controls {
        display: flex;
        flex-direction: column;
        gap: 10px;
        flex: 1;
        margin-top: 15px;
    }
    .ev-remove-photo {
        background: none;
        border: none;
        color: var(--accent, #C1F11D);
        cursor: pointer;
        padding: 0;
        font-size: 14px;
        text-align: left;
        font-family: inherit;
    }
    .ev-remove-photo:hover {
        opacity: 0.8;
    }
    .ev-file-input {
        font-size: 14px;
        color: #fff;
    }
    .ev-file-input::file-selector-button {
        padding: 8px 16px;
        background: var(--accent, #C1F11D);
        color: #000;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
        font-size: 13px;
        margin-right: 12px;
        transition: background 0.2s;
    }
    .ev-file-input::file-selector-button:hover {
        background: var(--accent-hover, #d4f84d);
    }
    .ev-hint {
        color: var(--text-muted, #666);
        font-size: 13px;
        margin-top: 10px;
    }
    /* Save button */
    .ev-save-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 28px;
        background: var(--accent, #C1F11D);
        color: #000;
        border: none;
        border-radius: 21.5px;
        font-size: 15px;
        font-weight: 600;
        font-family: inherit;
        cursor: pointer;
        transition: all 0.2s ease;
        margin-top: 10px;
    }
    .ev-save-btn:hover {
        background: var(--accent-hover, #d4f84d);
        transform: translateY(-1px);
    }
    @media (max-width: 768px) {
        .ev-back-bar {
            padding: 10px 0;
        }
        .ev-back-bar a {
            font-size: 14px;
            color: #C1F11D;
        }
        .ev-back-bar h1 {
            font-size: 16px;
        }
        .ev-account-tabs {
            padding: 12px 0;
            gap: 6px;
            justify-content: center;
        }
        .ev-account-tabs a {
            width: auto;
            flex: 1;
            min-width: 0;
            padding: 8px 12px;
            font-size: 13px;
            border-radius: 5px;
            background: #1D2224;
            border: 1px solid #333;
        }
        .ev-account-tabs a.active {
            border-color: #C1F11D;
        }
        .ev-completion-card {
            border-radius: 5px;
            padding: 14px 16px;
            margin-top: 10px !important;
        }
        .ev-completion-card h4 {
            font-size: 15px;
        }
        .ev-completion-card p {
            font-size: 13px;
            margin-bottom: 6px;
        }
        .ev-completion-card .ev-missing {
            font-size: 12px;
        }
        .ev-completion-inner {
            flex-wrap: nowrap !important;
        }
        .ev-completion-inner > div:last-child {
            min-width: 60px !important;
            flex-shrink: 0;
        }
        .ev-completion-card svg {
            width: 55px !important;
            height: 55px !important;
        }
        .ev-completion-card svg circle {
            stroke-width: 5 !important;
        }
        .ev-completion-inner > div:last-child div[style*="font-size: 18px"] {
            font-size: 14px !important;
        }
        .ev-progress-bar-bg {
            margin-top: 10px;
            height: 6px;
        }
        .ev-form-row {
            flex-direction: column-reverse;
            gap: 0;
        }
        .ev-form-col {
            flex-basis: auto;
        }
        .ev-form-group {
            margin-bottom: 16px;
        }
        .ev-form-group label {
            font-size: 13px;
            color: #ccc;
            margin-bottom: 6px;
        }
        .ev-form-group .ev-input {
            border-radius: 5px;
            padding: 10px 12px;
            font-size: 14px;
            border-color: #333;
            background: #0a0a0a;
        }
        .ev-avatar-section {
            flex-direction: row;
            align-items: center;
            gap: 12px;
        }
        .ev-avatar-preview {
            width: 64px;
            height: 64px;
            border-radius: 50%;
        }
        .ev-avatar-controls {
            margin-top: 0;
        }
        .ev-file-input::file-selector-button {
            border-radius: 5px;
            padding: 6px 14px;
            font-size: 12px;
        }
        .ev-phone-row {
            gap: 8px;
        }
        .ev-country-select {
            width: 120px;
        }
        .ev-country-display {
            border-radius: 5px;
            padding: 10px 10px;
            font-size: 13px;
            border-color: #333;
            background: #0a0a0a;
        }
        .ev-country-dropdown {
            border-radius: 5px;
        }
        .ev-save-btn {
            width: 100%;
            justify-content: center;
            border-radius: 5px;
            padding: 14px 28px;
            font-size: 16px;
            margin-top: 16px;
        }
        .ev-alert-success {
            border-radius: 5px;
        }
        .ev-radio-group {
            gap: 16px;
        }
        .ev-radio-group label {
            font-size: 13px;
        }
    }
</style>
@endpush

<div>
    {{-- Back bar --}}
    <div class="ev-back-bar">
        <div class="ev-container" style="display: flex; align-items: center; justify-content: center; position: relative;">
            <a href="/" style="position: absolute; left: 16px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                Home
            </a>
            <h1>Edit Account</h1>
        </div>
    </div>

    <div class="ev-container" style="padding-top: 8px; padding-bottom: 40px;">

        @if(session()->has('message'))
            <div class="ev-alert-success" style="margin-top: 16px;">
                {{ session('message') }}
            </div>
        @endif

        {{-- Account tabs --}}
        <div class="ev-account-tabs">
            <a href="/my-account" wire:navigate class="{{ request()->is('my-account') && !request()->is('my-account/*') ? 'active' : '' }}">
                <i class="fa fa-user"></i> Account
            </a>
            <a href="/my-account/edit" wire:navigate class="{{ request()->is('my-account/edit') ? 'active' : '' }}">
                <i class="fa fa-pencil-alt"></i> Edit
            </a>
            <a href="/my-password/edit" wire:navigate class="{{ request()->is('my-password/edit') ? 'active' : '' }}">
                <i class="fa fa-key"></i> Password
            </a>
        </div>

        {{-- Profile Completion Progress --}}
        @php $profileCompletion = $this->profileCompletion; @endphp
        <div class="ev-completion-card">
            <div class="ev-completion-inner" style="display: flex; align-items: center; justify-content: space-between; gap: 15px;">
                <div style="flex: 1; min-width: 0;">
                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                        @if($profileCompletion['percentage'] >= 100)
                            <i class="fa fa-check-circle" style="font-size: 24px;"></i>
                            <h4>Profile Complete!</h4>
                        @else
                            <i class="fa fa-user-circle" style="font-size: 24px;"></i>
                            <h4>Complete Your Profile</h4>
                        @endif
                    </div>

                    @if(!$profileCompletion['isComplete'])
                        <p>Complete your profile to attract more visitors and build trust</p>
                        @if(count($profileCompletion['incomplete']) > 0)
                            <div class="ev-missing">
                                <i class="fa fa-lightbulb"></i>
                                Missing: @foreach($profileCompletion['incomplete'] as $index => $field)<span>{{ $field }} <span style="color: #ff0000; font-weight: bold;">*</span></span>{{ $index < count($profileCompletion['incomplete']) - 1 ? ', ' : '' }}@endforeach
                            </div>
                        @endif
                    @else
                        <p>Great job! Your profile is fully complete and ready to attract visitors.</p>
                    @endif
                </div>

                <div style="text-align: center; min-width: 60px; flex-shrink: 0;">
                    <div style="position: relative; display: inline-block;">
                        <svg width="80" height="80" viewBox="0 0 80 80">
                            <circle cx="40" cy="40" r="35" fill="none" stroke="rgba(193,241,29,0.2)" stroke-width="6"/>
                            <circle cx="40" cy="40" r="35" fill="none" stroke="#C1F11D" stroke-width="6"
                                stroke-dasharray="{{ 2 * 3.14159 * 35 }}"
                                stroke-dashoffset="{{ 2 * 3.14159 * 35 * (1 - $profileCompletion['percentage'] / 100) }}"
                                transform="rotate(-90 40 40)"
                                style="transition: stroke-dashoffset 0.5s ease;"/>
                        </svg>
                        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-size: 18px; font-weight: 700; color: var(--accent, #C1F11D);">
                            {{ $profileCompletion['percentage'] }}%
                        </div>
                    </div>
                </div>
            </div>

            <div class="ev-progress-bar-bg">
                <div class="ev-progress-bar-fill" style="width: {{ $profileCompletion['percentage'] }}%;"></div>
            </div>
        </div>

        {{-- Edit Form --}}
        <form wire:submit.prevent="save" class="ev-edit-form" id="edit_my_account" novalidate="novalidate" enctype="multipart/form-data">
            <div class="ev-form-row">
                {{-- Left column --}}
                <div class="ev-form-col">
                    <div class="ev-form-group">
                        <label>Account type</label>
                        <div class="ev-radio-group">
                            <label>
                                <input wire:model.live="account_type" type="radio" value="2" name="my_account[account_type]" />
                                Individual advertiser
                            </label>
                            <label>
                                <input wire:model.live="account_type" type="radio" value="3" name="my_account[account_type]" />
                                Escort agency
                            </label>
                        </div>
                    </div>

                    <div class="ev-form-group">
                        <label for="my_account_email">Email</label>
                        <input disabled wire:model="email" class="ev-input" maxlength="191" type="email" id="my_account_email" />
                        @error('email') <span class="ev-text-danger">{{ $message }}</span> @enderror
                    </div>

                    <div class="ev-form-group">
                        <label for="my_account_display_name">Display name</label>
                        <input wire:model.live.debounce.500ms="display_name" class="ev-input" maxlength="191" type="text" id="my_account_display_name" />
                        @error('display_name') <span class="ev-text-danger">{{ $message }}</span> @enderror
                    </div>

                    <div class="ev-form-group">
                        <label for="my_account_about_me">About me</label>
                        <textarea wire:model.live.debounce.500ms="about_me" rows="4" class="ev-input" id="my_account_about_me"></textarea>
                    </div>

                    <div class="ev-form-group">
                        <label>Phone Number</label>
                        <div class="ev-phone-row">
                            <div class="ev-country-select">
                                <div class="ev-country-display" id="countryCodeDisplay">
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <img id="selectedFlag" src="https://flagcdn.com/w40/{{ $countrycode ? strtolower(\App\Models\Country::where('phonecode', $countrycode)->first()?->iso ?? 'ae') : 'ae' }}.png" style="width: 24px; height: 16px; object-fit: cover; border-radius: 2px; {{ $countrycode ? '' : 'display: none;' }}">
                                        <span id="selectedCode">{{ $countrycode ? '+' . $countrycode : 'Select' }}</span>
                                    </div>
                                    <i class="fa fa-chevron-down" style="font-size: 10px; color: var(--text-muted, #666);"></i>
                                </div>
                                <div class="ev-country-dropdown" id="countryDropdown">
                                    <input type="text" id="countrySearch" placeholder="Search country..." autocomplete="off">
                                    <div id="countryList">
                                        @foreach($countries as $country)
                                            <div class="country-option" data-code="{{ $country->phonecode }}" data-iso="{{ strtolower($country->iso) }}" data-name="{{ $country->nicename }}">
                                                <img src="https://flagcdn.com/w40/{{ strtolower($country->iso) }}.png" style="width: 24px; height: 16px; object-fit: cover; border-radius: 2px;">
                                                <span>{{ $country->nicename }}</span>
                                                <span>+{{ $country->phonecode }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <input type="hidden" wire:model.live="countrycode" id="my_account_countrycode">
                            </div>
                            <input wire:model.live.debounce.500ms="phone" class="ev-input" placeholder="Phone number" type="text" id="my_account_phone" style="flex: 1;" />
                        </div>
                        @error('countrycode') <span class="ev-text-danger">{{ $message }}</span> @enderror
                        @error('phone') <span class="ev-text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

                {{-- Right column --}}
                <div class="ev-form-col">
                    <div class="ev-form-group">
                        <label>Profile photo</label>
                        <div class="ev-avatar-section">
                            <div class="ev-avatar-preview">
                                @if(!empty(auth()->user()->avatar))
                                    <img src="{{ Storage::url(auth()->user()->avatar) }}" alt="Profile photo">
                                @else
                                    <img src="https://www.gravatar.com/avatar/{{ md5(strtolower(trim(auth()->user()->email))) }}?s=128&d=identicon" alt="Profile photo">
                                @endif
                            </div>
                            <div class="ev-avatar-controls">
                                @if(!empty(auth()->user()->avatar))
                                    <button wire:click="removePhoto" class="ev-remove-photo" type="button">
                                        <i class="fa fa-times"></i> Remove photo
                                    </button>
                                @endif
                                <div>
                                    <input wire:model="avatar" accept="image/jpeg,image/jpg,image/png,image/gif" class="ev-file-input" type="file" name="my_account[avatar]" id="my_account_avatar">
                                    @error('avatar') <span class="ev-text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                        <p class="ev-hint">JPEG, JPG, GIF, PNG, maximum size of 4MB</p>
                    </div>
                </div>
            </div>

            <button class="ev-save-btn" type="submit">
                Save <i class="fa fa-chevron-right"></i>
            </button>
        </form>
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
