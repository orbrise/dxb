<div>
    <!-- Debug: Component is loaded -->
    <button type="button" class="btn btn-sm btn-secondary d-none" wire:click="openModal">Test Newsletter</button>
    
    @if($showModal)
    <div class="modal fade show" style="display: block; background: rgba(0,0,0,0.5);" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content" style="background: #2a2a2a; color: #fff;">
                <div class="modal-header" style="border-bottom: 1px solid #444;">
                    <h2 class="modal-title">
                        <i class="fa fa-newspaper fa-fw" style="color: #d4af37;"></i> Newsletter
                    </h2>
                    <button type="button" class="close text-white" wire:click="closeModal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="receiveNewsletter" wire:model="receiveNewsletter">
                            <label class="form-check-label" for="receiveNewsletter">
                                Send me newsletter for:
                            </label>
                        </div>
                    </div>

                    @if($receiveNewsletter)
                        <!-- City Search -->
                        <div class="form-group position-relative">
                            <div class="input-group">
                                <span class="input-group-text bg-dark border-secondary">
                                    <i class="fa fa-map-marker-alt"></i>
                                </span>
                                <input 
                                    type="text" 
                                    class="form-control bg-dark border-secondary text-white" 
                                    placeholder="Find city..."
                                    wire:model.live="citySearch"
                                    autocomplete="off"
                                >
                            </div>
                            
                            @if(count($searchResults) > 0)
                                <div class="list-group position-absolute w-100" style="z-index: 1000; max-height: 200px; overflow-y: auto;">
                                    @foreach($searchResults as $city)
                                        <button 
                                            type="button"
                                            class="list-group-item list-group-item-action bg-dark text-white border-secondary"
                                            wire:click="addCity({{ $city['id'] }})"
                                        >
                                            {{ $city['name'] }}@if($city['country']) ({{ $city['country'] }})@endif
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <!-- Selected Cities -->
                        @foreach($selectedCities as $index => $city)
                            <div class="d-flex align-items-center mb-2 p-2 bg-dark rounded">
                                <span class="flex-grow-1">
                                    {{ $city['name'] }}@if($city['country']) ({{ $city['country'] }})@endif
                                </span>
                                <button 
                                    type="button" 
                                    class="btn btn-sm btn-link text-danger"
                                    wire:click="removeCity({{ $index }})"
                                >
                                    <i class="fa fa-times"></i>
                                </button>
                            </div>
                        @endforeach

                        <!-- Add City Button -->
                        <button type="button" class="btn btn-dark mb-3" wire:click="$set('citySearch', '')">
                            <i class="fa fa-plus"></i> Add city
                        </button>

                        <!-- Include Genders -->
                        <div class="form-group">
                            <label class="d-block mb-2">Include</label>
                            <div class="form-check form-check-inline">
                                <input 
                                    class="form-check-input" 
                                    type="checkbox" 
                                    id="gender_female" 
                                    value="female" 
                                    wire:model="selectedGenders"
                                >
                                <label class="form-check-label" for="gender_female">
                                    Escorts
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input 
                                    class="form-check-input" 
                                    type="checkbox" 
                                    id="gender_male" 
                                    value="male" 
                                    wire:model="selectedGenders"
                                >
                                <label class="form-check-label" for="gender_male">
                                    Male Escorts
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input 
                                    class="form-check-input" 
                                    type="checkbox" 
                                    id="gender_shemale" 
                                    value="shemale" 
                                    wire:model="selectedGenders"
                                >
                                <label class="form-check-label" for="gender_shemale">
                                    Shemale Escorts
                                </label>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="modal-footer" style="border-top: 1px solid #444;">
                    <button type="button" class="btn btn-primary btn-lg" wire:click="save" style="background: #d4af37; border: none; color: #000;">
                        Save <i class="fa fa-chevron-right ml-2"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show"></div>
    @endif
</div>
