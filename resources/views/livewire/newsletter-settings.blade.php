@push('css')
<style>
    /* Same look as My Account → Membership (membership-management). */
    .nl-page { color: #fff; min-height: 100vh; padding: 24px 16px 60px; }
    .nl-wrap { max-width: 760px; margin: 0 auto; }
    .nl-head { text-align: center; margin: 10px 0 26px; }
    .nl-head h1 { margin: 0 0 8px; font-size: 28px; font-weight: 700; letter-spacing: -0.3px; }
    .nl-head p { margin: 0; color: #8e99a0; font-size: 14px; }

    .nl-card { margin-bottom: 20px; padding: 24px; border: 1px solid #23292B; border-radius: 14px; background: #15191B; }
    .nl-card-head { display: flex; justify-content: space-between; align-items: flex-start; gap: 14px; margin-bottom: 20px; }
    .nl-label { margin: 0 0 6px; color: #9aa6ac; font-size: 13px; }
    .nl-status { margin: 0; color: #fff; font-size: 24px; font-weight: 700; }
    .nl-badge {
        display: inline-flex; align-items: center; gap: 6px; padding: 5px 14px; border-radius: 999px;
        border: 1px solid rgba(193, 241, 29, 0.4); background: rgba(193, 241, 29, 0.07);
        color: #C1F11D; font-size: 12px; font-weight: 600; white-space: nowrap;
    }
    .nl-badge--off { border-color: rgba(255, 255, 255, 0.15); background: transparent; color: #9aa6ac; }

    .nl-info-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 14px; }
    .nl-info { min-width: 0; padding: 14px 16px; border: 1px solid #272d2f; border-radius: 10px; }
    .nl-info-label { margin: 0 0 4px; color: #8e99a0; font-size: 12px; }
    .nl-info-value { margin: 0; color: #fff; font-size: 15px; font-weight: 600; overflow-wrap: anywhere; }
    .nl-empty { margin: 0; color: #8e99a0; font-size: 14px; }
    .nl-warn { display: flex; gap: 10px; margin-top: 16px; padding: 12px 14px; border-radius: 10px; background: rgba(255, 170, 51, 0.08); color: #ffb44d; font-size: 13px; line-height: 1.45; }
    .nl-unsub { margin-top: 16px; padding: 0; border: 0; background: none; color: #ff7a7a; font: inherit; font-size: 13px; font-weight: 600; cursor: pointer; }
    .nl-unsub:hover { color: #ff9a9a; text-decoration: underline; }

    .nl-section-title { margin: 0 0 4px; font-size: 17px; font-weight: 700; }
    .nl-hint { margin: 0; color: #8e99a0; font-size: 13px; line-height: 1.45; }
    .nl-toggle-row { display: flex; justify-content: space-between; align-items: center; gap: 16px; }
    .nl-switch { position: relative; flex: none; width: 48px; height: 28px; margin: 0; cursor: pointer; }
    .nl-switch input { position: absolute; width: 0; height: 0; opacity: 0; }
    .nl-switch span { position: absolute; inset: 0; border-radius: 999px; background: #2a3133; transition: background .15s; }
    .nl-switch span::after { content: ""; position: absolute; top: 3px; left: 3px; width: 22px; height: 22px; border-radius: 50%; background: #fff; transition: transform .15s; }
    .nl-switch input:checked + span { background: #C1F11D; }
    .nl-switch input:checked + span::after { background: #0D1011; transform: translateX(20px); }
    .nl-switch input:focus-visible + span { box-shadow: 0 0 0 3px rgba(193, 241, 29, 0.3); }

    .nl-fields { margin-top: 22px; padding-top: 22px; border-top: 1px solid #23292B; }
    .nl-field-label { display: block; margin: 0 0 10px; color: #fff; font-size: 14px; font-weight: 600; }
    .nl-field + .nl-field { margin-top: 22px; }
    .nl-search { position: relative; }
    .nl-search-box { display: flex; align-items: center; gap: 10px; height: 48px; padding: 0 14px; border: 1px solid #2a3133; border-radius: 10px; background: #0D1011; }
    .nl-search-box:focus-within { border-color: #C1F11D; }
    .nl-search-box i { color: #8e99a0; }
    .nl-search-box input { flex: 1; min-width: 0; height: 100%; border: 0; outline: 0; background: transparent; color: #fff; font-size: 16px; }
    .nl-search-box input::placeholder { color: #6b767b; }
    .nl-search-clear { padding: 4px; border: 0; background: none; color: #8e99a0; cursor: pointer; }
    .nl-results { position: absolute; top: calc(100% + 6px); right: 0; left: 0; z-index: 20; max-height: 260px; overflow-y: auto; padding: 6px; border: 1px solid #2a3133; border-radius: 10px; background: #15191B; box-shadow: 0 12px 30px rgba(0, 0, 0, 0.45); }
    .nl-results button { display: block; width: 100%; padding: 10px 12px; border: 0; border-radius: 8px; background: none; color: #fff; font: inherit; font-size: 14px; text-align: left; cursor: pointer; }
    .nl-results button:hover { background: #1f2527; }
    .nl-results small, .nl-chip small { color: #8e99a0; }
    .nl-no-results { padding: 10px 12px; color: #8e99a0; font-size: 13px; }
    .nl-chips { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 12px; }
    .nl-chip { display: inline-flex; align-items: center; gap: 8px; padding: 7px 8px 7px 13px; border: 1px solid #2a3133; border-radius: 999px; background: #1b2022; font-size: 13.5px; }
    .nl-chip i { color: #C1F11D; }
    .nl-chip button { display: inline-flex; align-items: center; justify-content: center; width: 22px; height: 22px; padding: 0; border: 0; border-radius: 50%; background: #2a3133; color: #c9d0d3; cursor: pointer; }
    .nl-chip button:hover { background: #ff6b6b; color: #fff; }
    .nl-cats { display: flex; flex-wrap: wrap; gap: 10px; }
    .nl-cat { position: relative; margin: 0; cursor: pointer; }
    .nl-cat input { position: absolute; opacity: 0; pointer-events: none; }
    .nl-cat span { display: inline-flex; align-items: center; gap: 8px; padding: 10px 16px; border: 1px solid #2a3133; border-radius: 999px; color: #c9d0d3; font-size: 14px; font-weight: 600; transition: border-color .15s, background .15s, color .15s; }
    .nl-cat span i { display: none; }
    .nl-cat input:checked + span { border-color: #C1F11D; background: rgba(193, 241, 29, 0.08); color: #C1F11D; }
    .nl-cat input:checked + span i { display: inline; }
    .nl-cat input:focus-visible + span { box-shadow: 0 0 0 3px rgba(193, 241, 29, 0.3); }
    .nl-error { margin-top: 8px; color: #ff7a7a; font-size: 13px; }

    .nl-actions { display: flex; align-items: center; gap: 14px; margin-top: 24px; }
    .nl-save { display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 12px 30px; border: 0; border-radius: 999px; background: #C1F11D; color: #0D1011; font-size: 14px; font-weight: 700; cursor: pointer; transition: background .15s; }
    .nl-save:hover { background: #d4f84d; }
    .nl-save:disabled { opacity: .6; cursor: wait; }
    .nl-saved { display: inline-flex; align-items: center; gap: 6px; color: #C1F11D; font-size: 13.5px; font-weight: 600; }

    @media (max-width: 540px) {
        .nl-page { padding: 16px 10px 40px; }
        .nl-head h1 { font-size: 22px; }
        .nl-card { padding: 18px; border-radius: 12px; }
        .nl-status { font-size: 20px; }
        .nl-info-grid { grid-template-columns: 1fr; gap: 10px; }
        .nl-actions { flex-direction: column; align-items: stretch; }
        .nl-save { width: 100%; }
        .nl-saved { justify-content: center; }
    }
</style>
@endpush

@php $subscribed = $savedCities->isNotEmpty(); @endphp
<div class="nl-page">
    <div class="nl-wrap">
        <div class="nl-head">
            <h1>Newsletter</h1>
            <p>A weekly email with the newest profiles and reviews in the cities you choose.</p>
        </div>

        {{-- What's saved now --}}
        <div class="nl-card">
            <div class="nl-card-head">
                <div>
                    <p class="nl-label">Your subscription</p>
                    <h2 class="nl-status">{{ $subscribed ? 'Subscribed' : 'Not subscribed' }}</h2>
                </div>
                <span class="nl-badge {{ $subscribed ? '' : 'nl-badge--off' }}">
                    @if($subscribed)<i class="fa fa-check"></i>@endif
                    {{ $subscribed ? 'Active' : 'Off' }}
                </span>
            </div>

            @if($subscribed)
                <div class="nl-info-grid">
                    <div class="nl-info">
                        <p class="nl-info-label">{{ \Illuminate\Support\Str::plural('City', $savedCities->count()) }}</p>
                        <p class="nl-info-value">{{ $savedCities->pluck('name')->implode(', ') }}</p>
                    </div>
                    <div class="nl-info">
                        <p class="nl-info-label">Includes</p>
                        <p class="nl-info-value">{{ implode(', ', $savedCategories) }}</p>
                    </div>
                    <div class="nl-info">
                        <p class="nl-info-label">Sent to</p>
                        <p class="nl-info-value">{{ $email }}</p>
                    </div>
                    <div class="nl-info">
                        <p class="nl-info-label">How often</p>
                        <p class="nl-info-value">Every Monday</p>
                    </div>
                </div>
                @unless($emailVerified)
                    <div class="nl-warn" role="status">
                        <i class="fa fa-exclamation-triangle" style="margin-top:2px;"></i>
                        <span>Your email address isn't verified yet, so the newsletter can't be sent to it. Verify it from the link in the email we sent when you signed up.</span>
                    </div>
                @endunless
                <button type="button" class="nl-unsub" wire:click="unsubscribe"
                        wire:confirm="Unsubscribe from the newsletter? You can subscribe again here any time.">
                    Unsubscribe
                </button>
            @else
                <p class="nl-empty">You're not getting the newsletter. Turn it on below and choose your cities.</p>
            @endif
        </div>

        {{-- Settings --}}
        <form class="nl-card" wire:submit="save">
            <div class="nl-toggle-row">
                <div>
                    <h3 class="nl-section-title">Send me the newsletter</h3>
                    <p class="nl-hint">{{ $receiveNewsletter ? 'Choose the cities and categories you want to hear about.' : 'Saving with this off unsubscribes you.' }}</p>
                </div>
                <label class="nl-switch">
                    <input type="checkbox" wire:model.live="receiveNewsletter" aria-label="Send me the newsletter">
                    <span></span>
                </label>
            </div>

            @if($receiveNewsletter)
                <div class="nl-fields">
                    <div class="nl-field">
                        <label class="nl-field-label" for="nlCitySearch">Cities</label>
                        <div class="nl-search">
                            <div class="nl-search-box">
                                <i class="fa fa-map-marker-alt"></i>
                                <input id="nlCitySearch" type="text" placeholder="Search for a city…" autocomplete="off"
                                       wire:model.live.debounce.250ms="citySearch">
                                @if($citySearch !== '')
                                    <button type="button" class="nl-search-clear" wire:click="$set('citySearch', '')" aria-label="Clear search"><i class="fa fa-times"></i></button>
                                @endif
                            </div>
                            @if(mb_strlen(trim($citySearch)) >= 2)
                                <div class="nl-results">
                                    @forelse($searchResults as $city)
                                        <button type="button" wire:click="addCity({{ $city['id'] }})" wire:key="nl-result-{{ $city['id'] }}">
                                            {{ $city['name'] }}@if($city['country']) <small>· {{ $city['country'] }}</small>@endif
                                        </button>
                                    @empty
                                        <div class="nl-no-results">No cities match “{{ $citySearch }}”.</div>
                                    @endforelse
                                </div>
                            @endif
                        </div>
                        @if($selectedCities)
                            <div class="nl-chips">
                                @foreach($selectedCities as $index => $city)
                                    <span class="nl-chip" wire:key="nl-city-{{ $city['id'] }}">
                                        <i class="fa fa-map-marker-alt"></i>
                                        {{ $city['name'] }}@if($city['country']) <small>{{ $city['country'] }}</small>@endif
                                        <button type="button" wire:click="removeCity({{ $index }})" aria-label="Remove {{ $city['name'] }}"><i class="fa fa-times"></i></button>
                                    </span>
                                @endforeach
                            </div>
                        @endif
                        @error('selectedCities') <div class="nl-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="nl-field">
                        <span class="nl-field-label">Include</span>
                        <div class="nl-cats">
                            @foreach(\App\Livewire\NewsletterSettings::CATEGORIES as $value => $label)
                                <label class="nl-cat">
                                    <input type="checkbox" value="{{ $value }}" wire:model.live="selectedGenders">
                                    <span><i class="fa fa-check"></i>{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('selectedGenders') <div class="nl-error">{{ $message }}</div> @enderror
                    </div>
                </div>
            @endif

            <div class="nl-actions">
                <button type="submit" class="nl-save" wire:loading.attr="disabled" wire:target="save">
                    <span wire:loading.remove wire:target="save">Save</span>
                    <span wire:loading wire:target="save">Saving…</span>
                </button>
                @if($saved)
                    <span class="nl-saved" role="status"><i class="fa fa-check-circle"></i> Saved</span>
                @endif
            </div>
        </form>
    </div>
</div>
