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
              <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="margin-right:6px;vertical-align:middle;"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>Search for {{ ucfirst($gender ?? 'female') }} escorts 
          </a>
      </div>
        <div class="action-group hidden-xs">
          <div class="form-group dropdown primary-search-gender">
            <button class="btn btn-dark search-bar--gender" data-toggle="dropdown" data-display="static" tabindex="2" type="button">{{ucfirst($gender)?? "Female"}} escorts <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="margin-left:6px;vertical-align:middle;"><polyline points="6 9 12 15 18 9"></polyline></svg>
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
                @php
                    $currentCurrencyCode = optional($currencies->firstWhere('id', $currency))->code ?? ($currencies->first()->code ?? '');
                @endphp
                <style>
                    .currency-dd-container { position: relative; display: inline-block; vertical-align: middle; }
                    .currency-dd-trigger {
                        background-color: #1a1a1a !important;
                        color: #fff !important;
                        border: 1px solid #2a2a2a !important;
                        /* Match the height of the sibling .btn (Female escorts,
                           Price, All Services) buttons. They come from Bootstrap
                           4's .btn (padding 0.375rem 0.75rem + 1.5 line-height +
                           1px borders ≈ 38px). The trigger previously had a
                           hard-coded 32px which made it visibly shorter in the
                           search bar row. */
                        height: 38px;
                        min-width: 90px;
                        padding: 0 28px 0 12px;
                        font-size: 13px;
                        font-weight: bold;
                        border-radius: 4px;
                        cursor: pointer;
                        text-align: left;
                        position: relative;
                        white-space: nowrap;
                        line-height: 1.5;
                        display: inline-flex;
                        align-items: center;
                    }
                    .currency-dd-trigger .currency-dd-chevron {
                        position: absolute; right: 8px; top: 50%;
                        transform: translateY(-50%); pointer-events: none;
                    }
                    .currency-dd-panel {
                        display: none;
                        position: absolute; top: calc(100% + 4px); left: 0;
                        min-width: 180px;
                        background-color: #1a1a1a !important;
                        border: 1px solid #2a2a2a !important;
                        border-radius: 6px;
                        box-shadow: 0 8px 24px rgba(0,0,0,0.5);
                        z-index: 9999;
                        overflow: hidden;
                    }
                    .currency-dd-panel.is-open { display: block; }
                    .currency-dd-search-wrap { padding: 8px; border-bottom: 1px solid #2a2a2a; }
                    .currency-dd-search {
                        width: 100%;
                        background-color: #0f0f0f !important;
                        color: #fff !important;
                        border: 1px solid #2a2a2a !important;
                        border-radius: 4px;
                        padding: 6px 10px;
                        font-size: 13px;
                        outline: none;
                    }
                    .currency-dd-search:focus { border-color: #C1F11D !important; }
                    .currency-dd-list {
                        list-style: none;
                        margin: 0;
                        padding: 4px 0;
                        max-height: 220px;
                        overflow-y: auto;
                    }
                    .currency-dd-list li {
                        padding: 8px 14px;
                        color: #fff !important;
                        font-size: 13px;
                        cursor: pointer;
                        transition: background 0.12s ease, color 0.12s ease;
                    }
                    .currency-dd-list li:hover,
                    .currency-dd-list li.is-active {
                        background-color: #C1F11D !important;
                        color: #000 !important;
                    }
                    .currency-dd-list li.is-empty { color: #999 !important; cursor: default; }
                    .currency-dd-list li.is-empty:hover { background: transparent !important; color: #999 !important; }
                    .currency-dd-list::-webkit-scrollbar { width: 6px; }
                    .currency-dd-list::-webkit-scrollbar-track { background: transparent; }
                    .currency-dd-list::-webkit-scrollbar-thumb { background: #2a2a2a; border-radius: 3px; }
                </style>
                <div class="currency-dd-container" wire:ignore>
                    <button type="button" class="currency-dd-trigger" id="currency-dd-trigger" aria-haspopup="listbox" aria-expanded="false">
                        <span id="currency-dd-label">{{ $currentCurrencyCode }}</span>
                        <svg class="currency-dd-chevron" xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#C1F11D" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </button>
                    <div class="currency-dd-panel" id="currency-dd-panel" role="listbox">
                        <div class="currency-dd-search-wrap">
                            <input type="text" class="currency-dd-search" id="currency-dd-search" placeholder="Search currency..." autocomplete="off">
                        </div>
                        <ul class="currency-dd-list" id="currency-dd-list">
                            @foreach($currencies as $cur)
                                <li data-id="{{ $cur->id }}" data-code="{{ $cur->code }}" data-search="{{ strtolower($cur->code) }}" @if($cur->id == $currency) class="is-active" @endif>{{ $cur->code }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <input type="hidden" wire:model="currency" data-currency-combobox="true" id="currency-hidden-input" value="{{ $currency }}">
             
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
                <style>
                    /* Force theme colors on services dropdown — overrides legacy/Bootstrap label styling that paints text purple. */
                    .services-dropdown-container #services-display-box {
                        background-color: #1a1a1a !important;
                        color: #fff !important;
                        border: 1px solid #2a2a2a !important;
                    }
                    .services-dropdown-container #services-display-text {
                        color: #fff !important;
                    }
                    .services-dropdown-container #services-dropdown-list {
                        background-color: #1a1a1a !important;
                        border: 1px solid #2a2a2a !important;
                        border-top: none !important;
                    }
                    .services-dropdown-container .service-option,
                    .services-dropdown-container .service-option label {
                        color: #fff !important;
                        background-color: transparent;
                    }
                    .services-dropdown-container .service-option {
                        border-bottom: 1px solid #2a2a2a !important;
                    }
                    .services-dropdown-container .service-option:hover,
                    .services-dropdown-container .service-option:hover label {
                        background-color: #C1F11D !important;
                        color: #000 !important;
                    }
                    .services-dropdown-container .service-option.is-selected,
                    .services-dropdown-container .service-option.is-selected label {
                        background-color: #C1F11D !important;
                        color: #000 !important;
                    }
                    .services-dropdown-container .service-option.is-selected .checkmark {
                        color: #000 !important;
                    }
                </style>
                <div class="services-dropdown-container" style="position: relative;">
                    <!-- The display box that looks like an input -->
                    <div id="services-display-box"
                         style="cursor: pointer; font-size: 13px; height: 32px; padding: 6px 12px; display: flex; align-items: center; justify-content: space-between; font-weight: bold; border-radius: 4px; width: 100%; min-width: 200px; box-sizing: border-box;">
                        <span id="services-display-text" style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: calc(100% - 20px);">All Services</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#C1F11D" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </div>

                    <!-- The dropdown list -->
                    <div id="services-dropdown-list"
                         style="display: none; position: absolute; top: 100%; left: 0; right: 0; border-radius: 0 0 4px 4px; max-height: 200px; min-width: 200px; overflow-y: auto; z-index: 1000; box-shadow: 0 4px 6px rgba(0,0,0,0.3);">
                        @foreach($services as $service)
                            <div class="service-option"
                                 data-id="{{ $service->id }}"
                                 data-name="{{ $service->name }}"
                                 style="padding: 10px 12px; cursor: pointer; display: flex; align-items: center; font-size: 13px;">
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
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
          </button>
         
          {{-- advance search --}}

          <div aria-hidden="true" class="modal" id="search-more" role="dialog">
            <div class="modal-dialog modal-lg">
              <div class="modal-content">
               
                  <div class="modal-header">
                    <button aria-hidden="true" class="close" data-dismiss="modal" type="button">
                      <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
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
                              <label for="q_verified_true" style="background-color: #C1F11D; color: #000000 !important; padding: 5px 22px; border-radius: 999px; margin: 0; font-weight: 600; display: inline-flex; align-items: center; cursor: pointer; font-size: 14px; border: 1px solid #C1F11D;">
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
                      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg> <span wire:loading.remove wire:target="search">Search</span>
                      <span wire:loading wire:target="search">Searching...</span> </button>
                  </div>
           
              </div>
            </div>
          </div>

          <button class="btn btn-primary" id="submit" type="submit" wire:loading.attr="disabled" wire:target="search">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>  <span wire:loading.remove wire:target="search">Search</span>
            <span wire:loading wire:target="search">Searching...</span>
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

    // Re-init after every Livewire morph. wire:model.live="rate" (the price
    // field) triggers a morph on every keystroke and replaces the toggle
    // button + modal DOM, which wipes the click handler that boosts modal
    // z-index. Without this hook, the modal opens via Bootstrap's default
    // handler but sits BEHIND the backdrop because of ancestor stacking
    // contexts in the search header.
    document.addEventListener('livewire:init', () => {
        if (window.Livewire && typeof window.Livewire.hook === 'function') {
            window.Livewire.hook('morphed', () => {
                setTimeout(initAdvancedSearchModal, 50);
            });
        }
    });
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

// Themed currency dropdown — replaces the native <select> so options render in
// theme colors (white-on-dark) and adds an in-panel search filter.
(function() {
    function initCurrencyDropdown() {
        var trigger = document.getElementById('currency-dd-trigger');
        var panel   = document.getElementById('currency-dd-panel');
        var label   = document.getElementById('currency-dd-label');
        var search  = document.getElementById('currency-dd-search');
        var list    = document.getElementById('currency-dd-list');
        var hidden  = document.getElementById('currency-hidden-input');
        if (!trigger || !panel || !label || !list || !hidden) return false;
        if (trigger.dataset.bound === '1') return true;
        trigger.dataset.bound = '1';

        function open() {
            panel.classList.add('is-open');
            trigger.setAttribute('aria-expanded', 'true');
            if (search) {
                search.value = '';
                Array.prototype.forEach.call(list.querySelectorAll('li'), function (li) { li.style.display = ''; });
                setTimeout(function () { search.focus(); }, 30);
            }
        }
        function close() {
            panel.classList.remove('is-open');
            trigger.setAttribute('aria-expanded', 'false');
        }

        trigger.addEventListener('click', function (e) {
            e.stopPropagation();
            panel.classList.contains('is-open') ? close() : open();
        });

        panel.addEventListener('click', function (e) { e.stopPropagation(); });

        if (search) {
            search.addEventListener('input', function () {
                var q = this.value.toLowerCase().trim();
                var any = false;
                Array.prototype.forEach.call(list.querySelectorAll('li:not(.is-empty)'), function (li) {
                    var match = q === '' || (li.getAttribute('data-search') || '').indexOf(q) !== -1;
                    li.style.display = match ? '' : 'none';
                    if (match) any = true;
                });
                var emptyRow = list.querySelector('li.is-empty');
                if (!any) {
                    if (!emptyRow) {
                        emptyRow = document.createElement('li');
                        emptyRow.className = 'is-empty';
                        emptyRow.textContent = 'No matches';
                        list.appendChild(emptyRow);
                    }
                    emptyRow.style.display = '';
                } else if (emptyRow) {
                    emptyRow.style.display = 'none';
                }
            });
        }

        list.addEventListener('click', function (e) {
            var li = e.target.closest('li');
            if (!li || li.classList.contains('is-empty')) return;
            var id = li.getAttribute('data-id');
            var code = li.getAttribute('data-code');
            if (!id) return;

            Array.prototype.forEach.call(list.querySelectorAll('li'), function (other) {
                other.classList.remove('is-active');
            });
            li.classList.add('is-active');

            label.textContent = code;
            hidden.value = id;
            // Notify Livewire so wire:model picks up the change.
            hidden.dispatchEvent(new Event('input',  { bubbles: true }));
            hidden.dispatchEvent(new Event('change', { bubbles: true }));

            close();
        });

        document.addEventListener('click', function (e) {
            if (!panel.contains(e.target) && e.target !== trigger) close();
        });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && panel.classList.contains('is-open')) close();
        });

        // Listen for currency-updated events fired by Livewire on city change.
        if (window.Livewire && !window.__currencyDdLivewireBound) {
            window.__currencyDdLivewireBound = true;
            try {
                Livewire.on('currency-updated', function (data) {
                    var ev = Array.isArray(data) ? data[0] : data;
                    if (!ev || !ev.currencyId) return;
                    var match = list.querySelector('li[data-id="' + ev.currencyId + '"]');
                    if (!match) return;
                    Array.prototype.forEach.call(list.querySelectorAll('li'), function (other) {
                        other.classList.remove('is-active');
                    });
                    match.classList.add('is-active');
                    label.textContent = match.getAttribute('data-code');
                    hidden.value = ev.currencyId;
                });
            } catch (e) { /* Livewire not ready yet; binding will retry on next init */ }
        }

        return true;
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCurrencyDropdown);
    } else {
        initCurrencyDropdown();
    }
    document.addEventListener('livewire:navigated', initCurrencyDropdown);
    document.addEventListener('livewire:initialized', initCurrencyDropdown);

    // Re-bind after every Livewire morph. wire:model.live="rate" triggers a
    // morph on every keystroke. Even though .currency-dd-container has
    // wire:ignore, morphdom can still replace the trigger element through
    // sibling-reorder edge cases; when that happens the click handler is
    // lost. initCurrencyDropdown() guards itself with dataset.bound so it
    // returns early when the existing element is still good.
    document.addEventListener('livewire:init', function () {
        if (window.Livewire && typeof window.Livewire.hook === 'function') {
            window.Livewire.hook('morphed', function () {
                // If the trigger was replaced, dataset.bound is gone — clear
                // the flag on the new element so the binding re-runs.
                var trigger = document.getElementById('currency-dd-trigger');
                if (trigger && trigger.dataset.bound !== '1') {
                    initCurrencyDropdown();
                } else if (!trigger) {
                    // Element disappeared entirely — try once when it's back.
                    setTimeout(initCurrencyDropdown, 50);
                }
            });
        }
    });
})();
</script>
