<header id="header" style="margin-bottom:0px">
  {{-- ESCORTS / WHAT'S NEW Navigation Tabs - Mobile Only --}}
  <div class="visible-xs" style="padding: 8px 15px 0 15px; margin: 0; width: 100%;">
      <div class="btn-group" role="group" style="display: flex !important; width: 100%; margin: 0; border-radius: 4px; overflow: visible; position: relative;">
          <a class="btn" href="/{{ $gender ?? 'female' }}-escorts-in-{{ strtolower($selectedcity ?? 'dubai') }}" 
             style="flex: 1; background-color: #d4a017 !important; color: #000 !important; font-weight: normal; font-size: 13px; text-transform: uppercase; padding: 8px 15px; border: none; border-radius: 4px 0 0 4px; text-align: center; position: relative;">
              ESCORTS
              <span style="position: absolute; bottom: -8px; left: 50%; transform: translateX(-50%); width: 0; height: 0; border-left: 8px solid transparent; border-right: 8px solid transparent; border-top: 8px solid #d4a017;"></span>
          </a>
          <a class="btn" href="/{{ $gender ?? 'female' }}-escort-news-in-{{ strtolower($selectedcity ?? 'dubai') }}" 
             style="flex: 1; background-color: #4a4a4a !important; color: #fff !important; font-weight: normal; font-size: 13px; text-transform: uppercase; padding: 8px 15px; border: none; border-radius: 0 4px 4px 0; text-align: center;">
              WHAT'S NEW
          </a>
      </div>
  </div>
  
  <div  class="simple_form listings-search-form search-form nav-bar dark-form form-inline">
    <div class="container-fluid">
      <div class="listings-primary-search">
        <div class="visible-xs-inline">
          <a class="btn btn-dark btn-block" href="{{ route('mobile.search', ['gender' => $gender ?? 'female', 'city' => $selectedcity ?? 'Dubai']) }}" tabindex="3">
              <i class="fa fa-search fa-fw"></i>Search for {{ ucfirst($gender ?? 'female') }} escorts 
          </a>
      </div>
        <div class="action-group hidden-xs">
          <div class="form-group dropdown primary-search-gender">
            <button class="btn btn-dark search-bar--gender" data-toggle="dropdown" tabindex="2" type="button">{{ucfirst($gender)?? "Female"}} escorts <i class="fa fa-caret-down"></i>
            </button>
            <ul class="dropdown-menu nav nav-pills nav-stacked nav-dark dropdown-gender-menu">
              <li class="@if(empty($gender) or $gender=='female') active @endif">
                <a href="{{url('female-escorts-in-'.$selectedcity)}}" title="Escorts in Dubai">Female escorts </a>
              </li>
              <li class="@if($gender=='male') active @endif">
                <a href="{{url('male-escorts-in-'.$selectedcity)}}" title="Gay escorts in Dubai">Male escorts </a>
              </li>
              <li class="@if($gender=='shemale') active @endif">
                <a href="{{url('shemale-escorts-in-'.$selectedcity)}}" title="Escort shemales in Dubai">Shemale escorts </a>
              </li>
            </ul>
          </div>
          <div class="form-group city required q_city_name_eq primary-search-city">
            <div class='typeahead-city-wrapper search-icon-persistent' wire:ignore>
              <style>
                /* Override the default FontAwesome icon from application.css */
                .typeahead-city-wrapper .twitter-typeahead::before {
                  display: none !important;
                }
                
                /* Add our custom SVG icon on the wrapper instead */
                .typeahead-city-wrapper {
                  position: relative;
                }
                .typeahead-city-wrapper::before {
                  content: '';
                  position: absolute;
                  left: 12px;
                  top: 50%;
                  transform: translateY(-50%);
                  width: 22px;
                  height: 22px;
                  z-index: 1;
                  pointer-events: none;
                  background-image: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="white"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>');
                  background-size: contain;
                  background-repeat: no-repeat;
                }
              </style>
              <input tabindex="2" id="citysearch" wire:model='selectedcity' class="city required search-bar--city typeahead-dark typeahead-city no-typeahead" autocomplete="off"  placeholder="Type city..." data-placeholder="Type city..." type="text"  value="{{$selectedcity}}" style="padding-left: 40px; background-color: #333; color: white; border: 1px solid #555;" />
              <input type="hidden" wire:model.lazy='city' value="229" id="selectedcity">
              <div id="cityappend" class="citys"></div>
            </div>
          </div>

          <form  class="simple_form listings-search-form search-form nav-bar form-inline" wire:submit='search' style="display: inline;">
    {{csrf_field()}}
          <div class="listings-search-main-fields-secondary">
            <div class="form-group">
              <div class="price-control" title="Price / hour">
                <select wire:model='currency' data-currency-combobox="true"  class="price-currency form-control" >
                  @foreach($currencies as $cur)
                  <option value="{{$cur->id}}" @if($cur->id == $currency) selected @endif>{{$cur->code}}</option>
                  @endforeach
                </select>
             
                <input tabindex="7" 
                wire:model.live="rate" 
                autocomplete="off" 
                min="0" 
                step="50" 
                class="numeric numeric required form-control price-amount validate form-control" 
                data-validations="numerically" 
                placeholder="Price" 
                type="number" 
            />
                        </div>
            </div>
            <div class="form-group" wire:ignore>
                <div class="services-dropdown-container" style="position: relative;">
                    <!-- The display box that looks like an input -->
                    <div id="services-display-box" 
                         style="background-color: #2c2c2c; color: white; border: 1px solid rgb(68 68 68); cursor: pointer; font-size: 13px; height: 32px; padding: 6px 12px; display: flex; align-items: center; justify-content: space-between; font-weight: bold; border-radius: 4px; width: 100%; min-width: 200px; box-sizing: border-box;">
                        <span id="services-display-text" style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: calc(100% - 20px);">All Services</span>
                        <span style="color: #999; flex-shrink: 0;">▼</span>
                    </div>
                    
                    <!-- The dropdown list -->
                    <div id="services-dropdown-list" 
                         style="display: none; position: absolute; top: 100%; left: 0; right: 0; background-color: #2c2c2c; border: 1px solid rgb(68 68 68); border-top: none; border-radius: 0 0 4px 4px; max-height: 200px; min-width: 200px; overflow-y: auto; z-index: 1000; box-shadow: 0 4px 6px rgba(0,0,0,0.3);">
                        @foreach($services as $service)
                            <div class="service-option" 
                                 data-id="{{ $service->id }}" 
                                 data-name="{{ $service->name }}"
                                 style="padding: 10px 12px; color: white; cursor: pointer; border-bottom: 1px solid #404040; display: flex; align-items: center; font-size: 13px;">
                                <input type="checkbox" 
                                       id="service-{{ $service->id }}" 
                                       style="display: none;"
                                       @if(is_array($sservices) && in_array($service->id, $sservices)) checked 
                                       @elseif(is_string($sservices) && in_array($service->id, explode(',', $sservices))) checked @endif>
                                <label for="service-{{ $service->id }}" style="margin: 0; cursor: pointer;">
                                    {{ $service->name }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- Hidden input for Livewire -->
                    <input type="hidden" wire:model="sservices" id="services-hidden-input">
                </div>
            </div>
          </div>
          <button class="btn btn-dark" data-target="#search-more" data-toggle="modal" id="toggle-search-more" tabindex="8" type="button">
            <span class="sr-only">Advanced search options</span>
            <i class="fas fa-plus fa-lg"></i>
          </button>
         
          {{-- advance search --}}

          <div aria-hidden="true" class="modal" id="search-more" role="dialog">
            <div class="modal-dialog modal-lg">
              <div class="modal-content">
               
                  <div class="modal-header">
                    <button aria-hidden="true" class="close" data-dismiss="modal" type="button">
                      <i class="fa fa-times"></i>
                    </button>
                    <h4 class="modal-title">Advanced Search</h4>
                  </div>
                  <div class="modal-body">
                    <div class="listings-search-advanced-fields">
                      <div class="row mb-3">
                        <div class="col-sm-3">
                          <div class=" ">
                            <label class="select required control-label" for="q_cup_size_id_eq">
                              <abbr title="required"></abbr> Bust size </label><br>
                            <select wire:model.defer='buts' class="form-control adinput" >
                              <option value="">Any</option>
                              @foreach($busts as $bust)
                              <option value="{{$bust->id}}">{{$bust->name}}</option>
                              @endforeach
                            </select>
                          </div>
                        </div>
                        <div class="col-sm-3">
                          <div class="">
                            <label class="select required control-label" for="q_sexual_orientation_id_eq">
                              <abbr title="required"></abbr> Orientation </label>
                              <br>
                            <select wire:model.defer='ori' class="adinput form-control " >
                              <option value="">Any</option>
                              <option value="1">Heterosexual</option>
                              <option value="2">Bisexual</option>
                              <option value="3">Lesbian or Gay</option>
                            </select>
                          </div>
                        </div>
                        <div class="col-sm-6">
                          <div class="">
                            <label class="select required control-label">
                              <abbr title="required"></abbr> Profile type </label>
                              <br>
                            <select wire:model.defer='profiletype' class="form-control adinput">
                              <option value="">Any</option>
                              <option value="1">Independent</option>
                              <option value="2">Agency</option>
                            </select>
                          </div>
                        </div>
                      </div>
                      <div class="row mb-3">
                        <div class="col-sm-12">
                          <div class="advanced-search-checkboxes" style="display: flex; gap: 15px; align-items: center; padding-top: 0;">
                            <div title="Verified profiles" data-toggle="tooltip" data-placement="top" class="form-group boolean optional q_verified_true" style="margin-bottom: 0;">
                              <label for="q_verified_true" style="background-color: #f4b827; color: #000; padding: 8px 16px; border-radius: 4px; margin: 0; font-weight: bold; display: inline-flex; align-items: center; cursor: pointer; font-size: 14px;">
                                <input style="margin-top: 0px;margin-right: 4px" wire:model.defer='verified' autocomplete="off" type="checkbox" value="1" name="q[verified_true]" id="q_verified_true" style="margin-right: 8px;" />VERIFIED</label>
                            </div>
                            <div title="At their place" data-toggle="tooltip" data-placement="top" class="form-group boolean optional q_incalls_true" style="margin-bottom: 0;">
                              <label for="q_incalls_true" style="margin: 0; display: inline-flex; align-items: center; cursor: pointer; font-size: 14px;">
                                <input style="margin: 0px 5px;"  wire:model.defer='incall' autocomplete="off" type="checkbox" value="1" name="q[incalls_true]" id="q_incalls_true" style="margin-right: 5px;" />Incalls</label>
                            </div>
                            <div title="At your place" data-toggle="tooltip" data-placement="top" class="form-group boolean optional q_outcalls_true" style="margin-bottom: 0;">
                              <label for="q_outcalls_true" style="margin: 0; display: inline-flex; align-items: center; cursor: pointer; font-size: 14px;">
                                <input style="margin: 0px 5px;"  wire:model.defer='outcall' autocomplete="off" type="checkbox" value="1" name="q[outcalls_true]" id="q_outcalls_true" style="margin-right: 5px;" />Outcalls</label>
                            </div>
                            <div class="form-group boolean optional q_smokes_false" style="margin-bottom: 0;">
                              <label for="q_smokes_false" style="margin: 0; display: inline-flex; align-items: center; cursor: pointer; font-size: 14px;">
                                <input style="margin: 0px 5px;" wire:model.defer='nonsmoker' autocomplete="off" type="checkbox" value="1" name="q[smokes_false]" id="q_smokes_false" style="margin-right: 5px;" />Non-smoker</label>
                            </div>
                            <div class="form-group boolean optional q_has_reviews" style="margin-bottom: 0;">
                              <label for="q_has_reviews" style="margin: 0; display: inline-flex; align-items: center; cursor: pointer; font-size: 14px;">
                                <input style="margin: 0px 5px;" autocomplete="off" wire:model.defer='withreviews' type="checkbox" value="1" name="q[has_reviews]" id="q_has_reviews" style="margin-right: 5px;" />With reviews</label>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="row mb-4">
                        <div class="col-sm-3">
                          <div class="">
                            <label class="select required control-label" for="q_ethnicity_id_eq">
                              <abbr title="required"></abbr> Ethnicity </label><br>
                            <select wire:model.defer='ethnicity' class="form-control adinput">
                              <option value="">Any</option>
                              @foreach($ethnicities as $eth)
                              <option value="{{$eth->id}}">{{$eth->name}}</option>
                              @endforeach
                            </select>
                          </div>
                        </div>
                        <div class="col-sm-3">
                          <div class="">
                            <label class="select required control-label" for="q_nationality_id_eq">
                              <abbr title="required"></abbr> Nationality </label><br>
                            <select wire:model.defer='nationality' class="form-control adinput" autocomplete="off" name="q[nationality_id_eq]" id="q_nationality_id_eq">
                              <option value="">Any</option>
                              @foreach($countries as $country)
                              <option value="{{$country->id}}">{{$country->nicename}}</option>
                              @endforeach
                            </select>
                          </div>
                        </div>
                        <div class="col-sm-3">
                          <div class="">
                            <label class="string required control-label" for="q_age_gteq">
                              <abbr title="required"></abbr> Age </label>
                            <div class="num-range">
                              <div class="num-range-from">
                                <select wire:model.defer='agefrom' class="adinput form-control" autocomplete="off" name="q[age_gteq]" id="q_age_gteq">
                                  <option value="">from</option>
                                  <option value="18">18</option>
                                  <option value="21">21</option>
                                  <option value="25">25</option>
                                  <option value="30">30</option>
                                  <option value="35">35</option>
                                  <option value="40">40</option>
                                  <option value="45">45</option>
                                  <option value="50">50</option>
                                  <option value="55">55</option>
                                  <option value="60">60</option>
                                </select>
                              </div>
                              <div class="num-range-to">
                                <select wire:model.defer='ageto' class="form-control adinput" autocomplete="off" name="q[age_lteq]" id="q_age_lteq">
                                  <option value="">to</option>
                                  <option value="18">18</option>
                                  <option value="21">21</option>
                                  <option value="25">25</option>
                                  <option value="30">30</option>
                                  <option value="35">35</option>
                                  <option value="40">40</option>
                                  <option value="45">45</option>
                                  <option value="50">50</option>
                                  <option value="55">55</option>
                                  <option value="60">60</option>
                                </select>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="col-sm-3">
                          <div class="form-group">
                            <label for="q_height_cm_gteq">Height (cm)</label>
                            <div class="num-range">
                              <div class="num-range-to">
                                <select wire:model.defer='heightfrom' class="adinput form-control" autocomplete="off" name="q[height_cm_gteq]" id="q_height_cm_gteq">
                                  <option value="">from</option>
                                  <option value="140">140</option>
                                  <option value="150">150</option>
                                  <option value="160">160</option>
                                  <option value="170">170</option>
                                  <option value="180">180</option>
                                  <option value="190">190</option>
                                  <option value="200">200</option>
                                  <option value="210">210</option>
                                  <option value="220">220</option>
                                </select>
                              </div>
                              <div class="num-range-from">
                                <select wire:model.defer='heightto' class="adinput form-control" autocomplete="off" name="q[height_cm_lteq]" id="q_height_cm_lteq">
                                  <option value="">to</option>
                                  <option value="140">140</option>
                                  <option value="150">150</option>
                                  <option value="160">160</option>
                                  <option value="170">170</option>
                                  <option value="180">180</option>
                                  <option value="190">190</option>
                                  <option value="200">200</option>
                                  <option value="210">210</option>
                                  <option value="220">220</option>
                                </select>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-sm-3">
                          <div class="form-group string required q_name_cont">
                            <label class="string required control-label" for="q_name_cont">
                              <abbr title="required"></abbr> Name </label>
                            <input wire:model.defer='name' class="string required form-control adinput" type="text" name="q[name_cont]" id="q_name_cont" />
                          </div>
                        </div>
                        <div class="col-sm-3">
                          <div class="form-group select required q_listing_languages_language_id_in">
                            <label class="select required control-label" for="q_listing_languages_language_id_in">
                              <abbr title="required"></abbr> Languages </label>
                           
                            <select wire:model.defer='language' autocomplete="off"  class="adinput  form-control" placeholder="Any" name="q[listing_languages_language_id_in][]" id="q_listing_languages_language_id_in">
                              <option value="">Any</option>
                              @foreach($languages as $lang)
                              <option value="{{$lang->id}}">{{$lang->name}}</option>
                             @endforeach
                            </select>
                          </div>
                        </div>
                        <div class="col-sm-3">
                          <div class="form-group select required q_genitals_shaved_type_id_eq">
                            <label class="select required control-label" for="q_genitals_shaved_type_id_eq">
                              <abbr title="required"></abbr> Shaved </label>
                            <select wire:model.defer='isshaved' class="form-control adinput" autocomplete="off" name="q[genitals_shaved_type_id_eq]" id="q_genitals_shaved_type_id_eq">
                              <option value="">Any</option>
                              <option value="no">No</option>
                              <option value="partially">Partially</option>
                              <option value="yes">Yes</option>
                            </select>
                          </div>
                        </div>
                        <div class="col-sm-3">
                          <div class="form-group select required q_hair_color_id_eq">
                            <label class="select required control-label" for="q_hair_color_id_eq">
                              <abbr title="required"></abbr> Hair color </label>
                            <select wire:model.defer='haircolor' class="adinput form-control " autocomplete="off" name="q[hair_color_id_eq]" id="q_hair_color_id_eq">
                              <option value="">Any</option>
                              @foreach($hairs as $hair)
                                    <option value="{{$hair->id}}">{{$hair->name}}</option>
                                    @endforeach
                            </select>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button class="btn btn-primary btn-lg" wire:loading.attr="disabled" id="submit1" type="submit" wire:click="search" style="height:43px">
                      <i class="fa fa-search"></i> <span wire:loading.remove wire:target="search">Search</span>
                      <span wire:loading wire:target="search">Searching...</span> </button>
                  </div>
           
              </div>
            </div>
          </div>

          <button class="btn btn-primary" id="submit" type="submit" wire:loading.attr="disabled">
            <i class="fa fa-search"></i>  <span wire:loading.remove wire.target="search">Search</span>
            <span wire:loading wire.target="search">Searching...</span>
          </button>
        </form>
        </div>
      </div>
    </div>
  </div>
</header>
 
<script>
// Advanced Search Modal - Simple initialization that preserves Livewire bindings
(function() {
    function initAdvancedSearchModal() {
        const toggleBtn = document.getElementById('toggle-search-more');
        const modal = document.getElementById('search-more');
        
        if (!toggleBtn || !modal) {
            console.log('Modal elements not found, retrying...');
            return false;
        }
        
        // DO NOT move modal to body - it breaks wire:model bindings!
        // Instead, fix z-index with inline styles when showing
        
        // Remove any existing click handlers to avoid duplicates
        const newToggleBtn = toggleBtn.cloneNode(true);
        toggleBtn.parentNode.replaceChild(newToggleBtn, toggleBtn);
        
        // Click handler to show modal
        newToggleBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            // CRITICAL: Temporarily boost z-index of ALL parent elements to break stacking context
            const header = document.getElementById('header');
            if (header) {
                header.style.cssText += '; z-index: 99998 !important; position: relative !important;';
            }
            
            // Also boost any parent containers
            let parent = modal.parentElement;
            while (parent && parent !== document.body) {
                if (parent.style) {
                    parent.style.zIndex = '99998';
                }
                parent = parent.parentElement;
            }
            
            // Use Bootstrap modal if available
            if (typeof jQuery !== 'undefined' && jQuery.fn.modal) {
                jQuery('#search-more').modal('show');
            } else {
                // Fallback manual show
                modal.classList.add('show', 'in');
                modal.style.display = 'block';
                modal.setAttribute('aria-hidden', 'false');
                document.body.classList.add('modal-open');
            }
            
            // Create our own backdrop as a sibling to body
            let backdrop = document.getElementById('search-more-backdrop');
            if (!backdrop) {
                backdrop = document.createElement('div');
                backdrop.id = 'search-more-backdrop';
                backdrop.style.cssText = 'position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); z-index: 99997;';
                document.body.appendChild(backdrop);
                
                // Click on backdrop closes modal
                backdrop.addEventListener('click', function() {
                    closeModal();
                });
            }
            backdrop.style.display = 'block';
            
            // Fix z-index AFTER modal is shown - apply to all elements
            setTimeout(function() {
                // Force modal to have highest z-index
                modal.style.cssText = 'display: block !important; z-index: 99999 !important; position: fixed !important; top: 0 !important; left: 0 !important; width: 100% !important; height: 100% !important; overflow: auto !important;';
                
                const modalDialog = modal.querySelector('.modal-dialog');
                if (modalDialog) {
                    modalDialog.style.cssText = 'z-index: 100000 !important; position: relative !important; margin: 30px auto !important;';
                }
                
                const modalContent = modal.querySelector('.modal-content');
                if (modalContent) {
                    modalContent.style.cssText = 'z-index: 100001 !important; position: relative !important; background-color: #2d2d2d !important;';
                }
                
                // Hide Bootstrap's backdrop if it exists
                const bsBackdrop = document.querySelector('.modal-backdrop');
                if (bsBackdrop) {
                    bsBackdrop.style.display = 'none';
                }
            }, 10);
            
            // Toggle icon
            const icon = newToggleBtn.querySelector('.fa, .fas');
            if (icon) {
                icon.classList.remove('fa-plus');
                icon.classList.add('fa-minus');
            }
        });
        
        // Close button handler
        const closeBtn = modal.querySelector('.close[data-dismiss="modal"]');
        if (closeBtn) {
            closeBtn.addEventListener('click', function(e) {
                e.preventDefault();
                closeModal();
            });
        }
        
        // Close on ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && modal.style.display === 'block') {
                closeModal();
            }
        });
        
        function closeModal() {
            if (typeof jQuery !== 'undefined' && jQuery.fn.modal) {
                jQuery('#search-more').modal('hide');
            }
            
            modal.classList.remove('show', 'in');
            modal.style.display = 'none';
            modal.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('modal-open');
            
            // Hide our custom backdrop
            const backdrop = document.getElementById('search-more-backdrop');
            if (backdrop) {
                backdrop.style.display = 'none';
            }
            
            // Remove Bootstrap backdrop
            const bsBackdrop = document.querySelector('.modal-backdrop');
            if (bsBackdrop) {
                bsBackdrop.remove();
            }
            
            // Reset header z-index
            const header = document.getElementById('header');
            if (header) {
                header.style.zIndex = '';
            }
            
            // Reset icon
            const icon = document.querySelector('#toggle-search-more .fa, #toggle-search-more .fas');
            if (icon) {
                icon.classList.remove('fa-minus');
                icon.classList.add('fa-plus');
            }
        }
        
        // Make close function globally accessible
        window.closeAdvancedSearchModal = closeModal;
        
        console.log('✅ Advanced search modal initialized');
        return true;
    }
    
    // Initialize on various events
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => setTimeout(initAdvancedSearchModal, 100));
    } else {
        setTimeout(initAdvancedSearchModal, 100);
    }
    
    window.addEventListener('load', () => setTimeout(initAdvancedSearchModal, 200));
    document.addEventListener('livewire:load', () => setTimeout(initAdvancedSearchModal, 100));
    document.addEventListener('livewire:initialized', () => setTimeout(initAdvancedSearchModal, 100));
})();

// Listen for the closeSearchModal event from Livewire
document.addEventListener('livewire:init', () => {
    Livewire.on('closeSearchModal', () => {
        if (typeof window.closeAdvancedSearchModal === 'function') {
            window.closeAdvancedSearchModal();
        } else {
            $('#search-more').modal('hide');
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open');
        }
    });
});
</script>
