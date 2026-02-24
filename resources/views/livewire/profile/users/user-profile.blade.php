@section('headerform')
<div class="nav-bar navbar-top-nav">
    <div class="container-fluid">
      <div class="title">
        <h1>Add your profile</h1>
      </div>
    </div>
  </div>
@endsection

@push('css')
    <style>
      <style>
        /* Custom Select2 Styles */
        .custom-select2 {
            position: relative !important;
            width: 100%;
            display: inline-block;
        }
        
        /* Country code specific width */
        .select2-country + .custom-select2 {
            width: 100% !important;
            display: inline-block !important;
        }
        
        .custom-select2-selection {
            position: relative;
            background: #fff;
            border: 1px solid #ddd;
            padding: 2px 24px 1px 8px;
            cursor: pointer;
            min-height: 34px;
            display: flex;
            align-items: center;
            transition: all 0.2s ease;
            font-size: 14px;
            color: #333;
        }
        
        .custom-select2-selection:hover {
            border-color: #aaa;
        }
        
        .custom-select2-selection:focus,
        .custom-select2-selection.open {
            border-color: #66afe9;
            box-shadow: 0 0 0 3px rgba(102, 175, 233, 0.1);
            outline: none;
        }
        
        .custom-select2-selection::after {
            content: '▼';
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 10px;
            color: #888;
            pointer-events: none;
            transition: transform 0.2s ease;
        }
        
        .custom-select2-selection.open::after {
            transform: translateY(-50%) rotate(180deg);
        }
        
        .custom-select2-dropdown {
            position: absolute;
            top: calc(100% + 2px);
            left: 0;
            right: auto;
            min-width: 100%;
            width: max-content;
            max-width: 400px;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 4px;
            max-height: 320px;
            overflow: hidden;
            z-index: 99999;
            display: none;
            box-shadow: 0 6px 12px rgba(0,0,0,0.175);
            margin-top: 2px;
            flex-direction: column;
            pointer-events: auto;
        }
         
        .custom-select2-dropdown.open {
            display: flex;
            animation: slideDown 0.2s ease;
            pointer-events: auto;
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .custom-select2-search {
            padding: 10px;
            border-bottom: 1px solid #eee;
            background: #fff;
            flex-shrink: 0;
        }
        
        .custom-select2-search input {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            transition: border-color 0.2s ease;
            color: #333 !important;
            background-color: #fff !important;
            position: relative;
            z-index: 11;
        }
        
        .custom-select2-search input:focus {
            outline: none;
            border-color: #66afe9;
            box-shadow: 0 0 0 3px rgba(102, 175, 233, 0.1);
            color: #333 !important;
        }
        
        .custom-select2-search input::placeholder {
            color: #999 !important;
        }
        
        .custom-select2-results {
            list-style: none;
            padding: 0;
            margin: 0;
            overflow-y: auto;
            flex: 1;
            max-height: 260px;
        }
        
        .custom-select2-option {
            padding: 10px 15px;
            cursor: pointer;
            font-size: 14px;
            color: #333;
            transition: background-color 0.15s ease;
            border-bottom: 1px solid #f5f5f5;
            pointer-events: auto;
            user-select: none;
            -webkit-tap-highlight-color: transparent;
        }
        
        .custom-select2-option:last-child {
            border-bottom: none;
        }
        
        .custom-select2-option:hover,
        .custom-select2-option:active {
            background: #f5f5f5;
        }
        
        .custom-select2-option.selected {
            background: #337ab7;
            color: #fff;
            font-weight: 500;
        }
        
        .custom-select2-option.selected:hover {
            background: #286090;
        }
        
        .custom-select2-option.hidden {
            display: none;
        }
        
        .custom-select2-placeholder {
            color: #999;
            font-style: italic;
        }
        
        /* Border Radius Variants for Custom Select2 */
        /* Left side rounded only */
        .custom-select2.radius-left .custom-select2-selection {
            border-radius: 4px 0 0 4px;
        }
        
        /* Right side rounded only */
        .custom-select2.radius-right .custom-select2-selection {
            border-radius: 0 4px 4px 0;
        }
        
        /* Top side rounded only */
        .custom-select2.radius-top .custom-select2-selection {
            border-radius: 4px 4px 0 0;
        }
        
        /* Bottom side rounded only */
        .custom-select2.radius-bottom .custom-select2-selection {
            border-radius: 0 0 4px 4px;
        }
        
        /* No radius at all */
        .custom-select2.radius-none .custom-select2-selection {
            border-radius: 0;
        }
        
        /* All corners rounded (default) */
        .custom-select2.radius-all .custom-select2-selection {
            border-radius: 4px;
        }
        
        /* Custom radius sizes */
        .custom-select2.radius-small .custom-select2-selection {
            border-radius: 2px;
        }
        
        .custom-select2.radius-large .custom-select2-selection {
            border-radius: 8px;
        }
        
        /* Scrollbar styling for dropdown results */
        .custom-select2-results::-webkit-scrollbar {
            width: 8px;
        }
        
        .custom-select2-results::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }
        
        .custom-select2-results::-webkit-scrollbar-thumb {
            background: #ccc;
            border-radius: 4px;
        }
        
        .custom-select2-results::-webkit-scrollbar-thumb:hover {
            background: #999;
        }

       .citys {
            width: 25%;
            min-width: 200px;
            max-height: 250px;
            position: absolute;
            background: #474747;
            overflow-y: auto;
            overflow-x: hidden;
            display: none;
            z-index: 9999;
            border-radius: 4px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            -webkit-overflow-scrolling: touch; /* Smooth scrolling on iOS */
        }

        .opt {
            font-size: 14px;
            padding: 8px 12px;
            color: #fff;
            cursor: pointer;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .opt.optc-item {
            transition: background-color 0.15s ease;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .opt.optc-item:last-child {
            border-bottom: none;
        }
        
        .opt.optc-item:hover,
        .opt.optc-item.highlighted {
            background-color: #5a5a5a;
        }
        
        .opt.optc-item:active,
        .opt.optc-item.selecting {
            background-color: #4a4a4a !important;
            opacity: 0.8;
        }
        
        .flg {
            margin-right: 10px;
            vertical-align: middle;
            width: 16px;
            height: 12px;
        }

        form.listing .big-one-line .typeahead-city-wrapper input {
    padding-top: 5px;
    padding-bottom: 5px;
    padding-left: 37px;

}

.typeahead-city-wrapper::before {
    position: absolute;
    z-index: 1;
    top: 7px;
    font-family: FontAwesome !important;
    content: "";
    font-size: 1.2em;
    margin-inline: 10px;

}

.listinga {
  background: #565656;
    color: white;
    border: none;
    border-radius: 0px;
    height: 52px;
    font-size: 24px;
    border-bottom: 2px dashed #aaa;
}

.mi-new-image-input {display:none;}
.spinner {
    display: none;
}

.spinner {
    display: inline-block;
    border-radius: 50%;
    border-right: solid 4px #fafafa;
    border-top: solid 4px rgba(100, 100, 200, .9);
    background: #fafafa;
    -webkit-box-shadow: 0 0 0 5px #fafafa;
    box-shadow: 0 0 0 5px #fafafa;
    -webkit-animation: spinner-spin 1slinear infinite;
    animation: spinner-spin 1slinear infinite;
    width: 28px;
}

.spinner.is-loading {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 28px;
    width: 28px;
    vertical-align: middle;
}

div#basic {
    margin-top: 15px;
}

.img-footer {
    position: absolute;
    bottom: 19px;
    width: 100%;
    left: 0px;
    text-align: center;
    line-height: 1;
}

form.listing .image {
  padding: 15px 20px 5px;
}

.wrappper{
  float: left; width: 27%;
}

.insidewrapper {
  width: 40%;
}

/* Price Control Layout */
.price-control {
  display: flex;
  gap: 0px;
  align-items: flex-start;
  position: relative;
}

.price-control .price-amount {
  flex: 1;
  min-width: 0;
}

.price-control .price-currency {
  flex: 0 0 120px;
  width: 120px;
}

.price-control .custom-select2 {
  flex: 0 0 120px;
  width: 120px !important;
}

/* Modern Upload Area Styles */
.modern-upload-label {
    cursor: pointer;
    width: 100%;
    display: block;
}

.drag-drop {
    border: 3px dashed #dee2e6;
    border-radius: 12px;
    padding: 40px 20px;
    background: #fff;
    transition: all 0.3s ease;
    position: relative;
}



.drag-drop .icon-image {
    transition: all 0.3s ease;
}



.drag-drop-text-main {
    font-size: 18px !important;
    color: #333 !important;
    font-weight: 600 !important;
}

.drag-drop-text {
    color: #6c757d !important;
    font-size: 14px !important;
}

.drag-drop .btn-warning {
    background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
    border: none;
    transition: all 0.3s ease;
    font-weight: 600;
}

.drag-drop .btn-warning:hover {
    background: linear-gradient(135deg, #ff9800 0%, #f57c00 100%);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(255, 193, 7, 0.4);
}

.drag-drop .btn-warning .fa-upload {
    margin-right: 8px;
}

.upload-disabled {
    display: none;
}

/* Dragover effect */
.drag-drop.dragover {
    border-color: #28a745;
    background: #f0f8f0;
}

@media (max-width:768px){
  .typeahead-city-wrapper::before {
    top: 109px;

  }

  input#citysearch {
    width:100%;
  }

  .wrappper{
 width: 100%;
}


}
div#basic {
     margin-top: 0px; 
}

@media (max-width: 767px) {
    #header {
        margin-bottom: 0px;
    }
}

@media (max-width: 767px) {
    .mob-left {
        margin-left: 0px !important;
    }
}

        /* Drag and Drop Styles */
        .record.image {
            cursor: move;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .record.image:hover {
            transform: scale(1.02);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        
        .record.image.drag-over {
            border: 2px dashed #007bff;
            background-color: rgba(0, 123, 255, 0.1);
        }
        
        .record.image[draggable="true"] {
            cursor: grab;
        }
        
        .record.image[draggable="true"]:active {
            cursor: grabbing;
        }
        
        .badge-success {
            background-color: #28a745;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            display: inline-block;
        }
        
        #image-container {
            display: block;
            margin-bottom: 20px;
        }
        
        .record.image img {
            max-width: 100%;
            max-height: 100px;
            height: auto;
            display: block;
            margin: 0 auto;
        }
        
        .record.image .delete {
            position: absolute;
            top: 5px;
            right: 5px;
            cursor: pointer;
            background: rgba(255, 0, 0, 0.7);
            color: white;
            padding: 5px 7px;
            border-radius: 50%;
            z-index: 10;
            font-size: 11px;
        }
        
        .record.image .delete:hover {
            background: rgba(255, 0, 0, 1);
        }

        form.listing .image i.delete {
    position: absolute;
    top: 5px;
    right: 5px;
    cursor: pointer;
    font-size: 11px;
    border-radius: 50%;
    padding: 5px 7px;
}
        </style>
@endpush

<div class="row container">
            <div class="col-lg-offset-1 col-lg-10">
              <form class="simple_form listing js-only" id="new_listing" wire:submit.prevent='updateProfile'  enctype="multipart/form-data" >
               
                <div class="wrapper">
                  <div id="basic">
                    <div class="big-one-line left">
                      <div class="form-group string required listing_name">
                        <input class="string required form-control medium validate" value="{{$user->name}}" data-validations="presence doesNotContainEmails doesNotContainPhones doesNotContainUrls length(3,40)" data-error-position-my="center bottom" data-error-position-offset="0 0" data-error-position-at="center top" data-tooltip-class="tooltip tooltip-s" maxlength="40" placeholder="Name" size="40" type="text" wire:model='name' name="listing[name]" id="listing_name" />
                      </div>
                      <div class="form-group  listing_listed_as_id">
                        <select  class=" form-control listinga" wire:model="listing"  id="listing_listed_as_id">
                          @foreach($listings as $listingOption)
                            <option value="{{$listingOption->id}}">{{$listingOption->name}}</option>
                          @endforeach
                        </select> 
                      </div>
                      <div class="form-group city optional listing_city_url">
                        <label class="city optional control-label" for="listing_city_url">in</label>
                        <div class='typeahead-city-wrapper'>
                          <input class="city optional "   placeholder="Find city..." name="listing[city_url]" wire:model='selectedcity'  type="text"  value="" id="citysearch"/>
                          <input type="hidden" wire:model.lazy='city' value="{{$user->city}}" id="selectedcity">
                          
                        <div id="cityappend" class="citys"></div>
                        </div>
                      </div>
                    </div>
                    <span class="hint city-hint left">
                      <div class="clearfix"></div>
                      <p class="text-right small">Your city not available? <a href="/contact-us" tabindex="-1" target="_blank">Ask for it</a>
                      </p>
                    </span>
                    <div class="form-group text required listing_description">
                      <label class="text required control-label" for="listing_description">
                        <abbr title="required"></abbr> About me </label>
                      <textarea class="text required form-control validate large" data-validations="presence doesNotContainEmails doesNotContainPhones doesNotContainUrls length(50,2000)" maxlength="2000" wire:model='aboutme' name="listing[description]" id="listing_description" oninput="updateCharCount(this)"></textarea>
                      <div class="char-count-container" style="margin-top: 5px; font-size: 12px;">
                        <span id="char-count" style="color: #666;">0 characters</span>
                        <span id="char-count-warning" style="color: #dc3545; margin-left: 10px; display: none;">Minimum 50 characters required</span>
                        <span id="char-count-ok" style="color: #28a745; margin-left: 10px; display: none;">✓ Minimum reached</span>
                      </div>
                      @error('aboutme')
                        <span class="validation-error" style="color: #dc3545; font-size: 12px;">{{ $message }}</span>
                      @enderror
                    </div>
                  </div>
                  <div id="">
                    <h2 class="h3 title-block">Upload photos</h2>
                    <div class="ad-images string optional listing_listing_images">
                      <div class="multi-image-uploader1" >
                        <p>Please upload high quality images only. Only post pictures of yourself or of a person who has given you explicit permission to do so. If you post fake photos, your profile will be deleted and your account blocked - <a href="/help-for-advertisers#fake" target="_blank">more information</a>. <br /> Photos with full frontal nudity, genitalia or sexually explicit conduct are prohibited. </p>
                        
                        <div id="image-container">
                        @php
                        // Build unified image array based on positions
                        $allImages = [];
                        
                        if (!empty($imagePositions)) {
                            // Use stored positions
                            foreach ($imagePositions as $posData) {
                                if ($posData['type'] === 'existing') {
                                    $img = $pimgs->firstWhere('id', $posData['id']);
                                    if ($img) {
                                        $allImages[] = ['type' => 'existing', 'data' => $img, 'position' => $posData['position']];
                                    }
                                } else {
                                    if (isset($tempImages[$posData['index']])) {
                                        $allImages[] = ['type' => 'temp', 'data' => $tempImages[$posData['index']], 'index' => $posData['index'], 'position' => $posData['position']];
                                    }
                                }
                            }
                        } else {
                            // Default order: existing first, then temp
                            foreach ($pimgs as $key => $img) {
                                $allImages[] = ['type' => 'existing', 'data' => $img, 'position' => $key];
                            }
                            if ($tempImages) {
                                foreach ($tempImages as $key => $image) {
                                    $allImages[] = ['type' => 'temp', 'data' => $image, 'index' => $key, 'position' => count($pimgs) + $key];
                                }
                            }
                        }
                        @endphp
                        
                        @foreach($allImages as $imageItem)
                            @if($imageItem['type'] === 'existing')
                            <div class="record image existing-image" draggable="true" data-id="{{ $imageItem['data']->id }}" role="option" aria-grabbed="false">
                              <i wire:click="removeImg({{ $imageItem['data']->id }})" class="fa fa-times fa-lg delete"></i>
                              <img src="{{ smart_asset('userimages/'.$user->id.'/'.$this->id.'/'.$imageItem['data']->image) }}">
                              <div class="img-footer">
                                @if($imageItem['position'] === 0)
                                <span class="badge badge-success">Main Image</span>
                                @else
                                <span class="text-muted small">Drag to first position to set as main</span>
                                @endif
                              </div>
                            </div>
                            @else
                            <div class="record image" draggable="true" data-index="{{ $imageItem['index'] }}" role="option" aria-grabbed="false">
                              <i wire:click="removeTemporaryImage({{ $imageItem['index'] }})" class="fa fa-times fa-lg delete"></i>
                              <img src="{{ $imageItem['data']->temporaryUrl() }}">
                              <div class="img-footer">
                                @if($imageItem['position'] === 0)
                                <span class="badge badge-success">Main Image</span>
                                @else
                                <span class="text-muted small">Drag to first position to set as main</span>
                                @endif
                              </div>
                            </div>
                            @endif
                        @endforeach
                        </div>

                        <div wire:loading.block wire:target="mphoto" class="spinner">
                          <i class="fas fa-spinner fa-spin"></i>
                      </div>
                        <div class="record image-input new-img">
                          <div class="file optional add-img">
                            <label class="modern-upload-label" for="mphoto" style="cursor: pointer;">
                              <div class="text-center mb-4 drag-drop" id="drag-drop-area">
                                <img style="    width: 55px; margin-bottom: 10px;" src="{{smart_asset('assets/images/imgicon.png')}}" alt="Upload Icon" class="">

                                <div class="upload-available">
                                  <p class="m-0 font-weight-bold drag-drop-text-main" style="font-size: 18px; color: #333;">Drop files here</p>
                                  <p class="m-1 drag-drop-text" style="color: #6c757d;">or</p>
                                  <button class="m-1 mb-3 btn btn-primary" type="button" onclick="document.getElementById('mphoto').click(); return false;">Choose Files</button>
                                  <p class="m-0 mt-3 drag-drop-text" style="font-size: 13px; color: #6c757d;">Supports: JPG, PNG, GIF (Max 5MB)</p>
                                </div>
                              </div>
                            </label>
                            <input class="file optional" wire:model='mphoto' type="file" accept="image/*" id="mphoto" multiple style="display: none;">
                          </div>
                        </div>
                      </div>
                      
                    </div>
                  </div>
                  <div id="contact-information">
                    <h2 class="h3 title-block">Contact information</h2>
                    <label>phone:</label>
                    <div class="inline-group">
                      <div class="form-group phone_number">
                        <div style="margin-bottom:15px" class="d-flex align-items-center wrappper">
                          <div class="insidewrapper" wire:ignore>
                          <select class="select2-country apply-custom-select2 form-control" wire:model='countrycode' id="first_phone_code" data-radius="left" style="border-top-left-radius:4px">
                            <option value="">Select code</option>
                            @foreach($countries as $code)
                            <option value="{{$code->phonecode}}">+{{$code->phonecode}} - {{$code->nicename}}</option>
                            @endforeach
                          </select>
                        </div>
                        <div style="width: 50%;" wire:ignore>
                          <input class="phone_number_input--digits form-control" type="text" name="phone" style="width:100%" placeholder="Enter phone number" id="listing_phone_numbers_attributes_0_phone_digits" />
                        </div>
                        </div>
                        <div style="row">
                        <label class="inline-block checkbox col-6" style="margin-top:0px">
              
                          <input @if($user->iswhatsapp == 1) @checked(true) @endif wire:model='iswhatsapp' wrapper="false" label="false" class="boolean optional" type="checkbox" value="1" name="listing[phone_numbers_attributes][0][whatsapp]" id="listing_phone_numbers_attributes_0_whatsapp" />
                          <span class="icon-whatsapp icon-inline"></span> WhatsApp </label>
                        <label class="inline-block col-6 checkbox margin-left">
                        
                          <input wrapper="false" @if($user->iswechat == 1) @checked(true) @endif wire:model='iswechat' label="false" class="boolean optional" type="checkbox" value="1" name="listing[phone_numbers_attributes][0][wechat]" id="listing_phone_numbers_attributes_0_wechat" />
                          <span class="icon-wechat icon-inline"></span> WeChat </label>
                        <label class="inline-block col-6 checkbox margin-left mob-left">
                         
                          <input wrapper="false" @if($user->istelegram == 1) @checked(true) @endif wire:model='istelegram' label="false" class="boolean optional" type="checkbox" value="1" name="listing[phone_numbers_attributes][0][telegram]" id="listing_phone_numbers_attributes_0_telegram" />
                          <span class="icon-telegram icon-inline"></span> Telegram </label>
                        <label class="inline-block col-6 checkbox margin-left">
                          
                          <input wrapper="false" @if($user->issignal == 1) @checked(true) @endif wire:model='issignal' label="false" class="boolean optional" type="checkbox" value="1" name="listing[phone_numbers_attributes][0][signal]" id="listing_phone_numbers_attributes_0_signal" />
                          <span class="icon-signal icon-inline"></span> Signal </label>
                        </div>
                      </div>
                      <div class="form-group phone_number">
                        <div style="margin-bottom:15px" class="wrappper d-flex align-items-center">
                          <div class="insidewrapper" wire:ignore>
                          <select class="select2-country apply-custom-select2 form-control" wire:model='countrycode2' name="listing[phone_numbers_attributes][1][calling_code]" id="second_phone_code" data-radius="left" style="border-top-left-radius:4px">
                            <option value="">Select code</option>
                            @foreach($countries as $code)
                            <option value="{{$code->phonecode}}">+{{$code->phonecode}} - {{$code->nicename}}</option>
                            @endforeach
                          </select>
                        </div>
                        <div style="width: 50%;" wire:ignore>
                          <input class="phone_number_input--digits form-control" name="phone2" type="text" style="width:100%" placeholder="Enter phone number" id="listing_phone_numbers_attributes_1_phone_digits" />
                        </div>
                        </div>
                        <div style="row">
                        <label class="inline-block checkbox col-6" style="margin-top:0px">
                          <input wrapper="false" label="false" wire:model='iswhatsapp2' @if($user->iswhatsapp2 == 1) @checked(true) @endif class="boolean optional" type="checkbox" value="1" name="listing[phone_numbers_attributes][1][whatsapp]" id="listing_phone_numbers_attributes_1_whatsapp" />
                          <span class="icon-whatsapp icon-inline"></span> WhatsApp </label>
                        <label class="inline-block col-6 checkbox margin-left">
                          <input wrapper="false" wire:model='iswechat2' @if($user->iswechat2 == 1) @checked(true) @endif label="false" class="boolean optional" type="checkbox" value="1" name="listing[phone_numbers_attributes][1][wechat]" id="listing_phone_numbers_attributes_1_wechat" />
                          <span class="icon-wechat icon-inline"></span> WeChat </label>
                        <label class="inline-block col-6 checkbox margin-left mob-left">
                          
                          <input wrapper="false" @if($user->istelegram2 == 1) @checked(true) @endif wire:model='istelegram2' label="false" class="boolean optional" type="checkbox" value="1" name="listing[phone_numbers_attributes][1][telegram]" id="listing_phone_numbers_attributes_1_telegram" />
                          <span class="icon-telegram icon-inline"></span> Telegram </label>
                        <label class="inline-block col-6 checkbox margin-left">
                          <input wrapper="false" @if($user->issignal2 == 1) @checked(true) @endif wire:model='issignal2' label="false" class="boolean optional" type="checkbox" value="1" name="listing[phone_numbers_attributes][1][signal]" id="listing_phone_numbers_attributes_1_signal" />
                          <span class="icon-signal icon-inline"></span> Signal </label>
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-sm-6">
                        <div class="form-group">
                          <div class="form-group boolean optional listing_show_contact_form">
                            <input value="0" type="hidden" name="listing[show_contact_form]" />
                            <label class="boolean optional control-label checkbox" for="show-contact-form">
                              <input id="show-contact-form" class="boolean optional" type="checkbox" value="1" checked="checked" name="listing[show_contact_form]" />Show contact form and send messages to: </label>
                          </div>
                          <input class="string optional form-control validate" readonly data-validations="presence emailFormat" type="text" value="{{$user->email}}" name="listing[contact_email_address]" id="listing_contact_email_address" />
                        </div>
                      </div> 
                    </div>
                    <div class="row">
                      <div class="col-sm-6">
                        <div class="form-group">
                          <div class="form-group string optional listing_website">
                            <label class="string optional control-label" for="listing_website">Website</label>
                            <input wire:model='website' class="string optional form-control validate large" data-validations="urlFormat" maxlength="255" size="255" type="text" name="listing[website]" id="listing_website" />
                          </div>
                          <label class="new">OnlyFans</label>
                          <div class="input-group">
                            <div class="input-group-addon">https://onlyfans.com/</div>
                            <input wire:model='onlyfans' class="string optional form-control validate large" maxlength="24" size="24" type="text" name="listing[onlyfans]" id="listing_onlyfans" />
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div id="services">
                    <h2 class="h3 title-block">Services</h2>
                    <div class="overflow-list-xs row">
                      <ul class="first check_boxes list-unstyled col-sm-3">
                        <li class="checkbox">
                          <label class=" @if(in_array(1, $uservices)) checked @endif" for="l_f-1"  >
                            <input class="check_boxes" id="l_f-1" wire:model="services.1" name="services1[]" type="checkbox" @if(in_array(1, $uservices)) @checked(true) @endif value="1">Anal Sex 
                          </label>
                        </li>
                        <li class="checkbox">
                          <label class="collection_check_boxes @if(in_array(2, $uservices)) checked @endif" for="l_f-28" unselectable="on" >
                            <input class="check_boxes" id="l_f-28" wire:model="services.2" name="services[]" type="checkbox" @if(in_array(2, $uservices)) checked @endif  value="2">BDSM </label>
                        </li>
                        <li class="checkbox">
                          <label class="collection_check_boxes" for="l_f-4" unselectable="on" >
                            <input class="check_boxes" id="l_f-4" wire:model='services.3' name="services[]" type="checkbox" @if(in_array(3, $uservices)) checked @endif value="3">CIM - Come In Mouth </label>
                        </li>
                        <li class="checkbox">
                          <label class="collection_check_boxes" for="l_f-29" unselectable="on" >
                            <input class="check_boxes" id="l_f-29" wire:model='services.4' name="services[]" type="checkbox" @if(in_array(4, $uservices)) checked @endif value="4">COB - Come On Body </label>
                        </li>
                        <li class="checkbox">
                          <label class="collection_check_boxes" for="l_f-8" unselectable="on" >
                            <input class="check_boxes" id="l_f-8" wire:model='services.5' name="services1[]" type="checkbox" @if(in_array(5, $uservices)) checked @endif value="5">Couples </label>
                        </li>
                        <li class="checkbox">
                          <label class="collection_check_boxes" for="l_f-13" unselectable="on" >
                            <input class="check_boxes" id="l_f-13" wire:model='services.6' name="services1[]" type="checkbox" @if(in_array(6, $uservices)) checked @endif value="6">Deep throat </label>
                        </li>
                        <li class="checkbox">
                          <label class="collection_check_boxes" for="l_f-9" unselectable="on" >
                            <input class="check_boxes" id="l_f-9" wire:model='services.7' name="services1[]" type="checkbox" @if(in_array(7, $uservices)) checked @endif value="7">Domination </label>
                        </li>
                        <li class="checkbox">
                          <label class="collection_check_boxes" for="l_f-3" unselectable="on" >
                            <input class="check_boxes" id="l_f-3" wire:model='services.8' name="services1[]" type="checkbox" @if(in_array(8, $uservices)) checked @endif value="8">Face sitting </label>
                        </li>
                        <li class="checkbox">
                          <label class="collection_check_boxes" for="l_f-21" unselectable="on" >
                            <input class="check_boxes" id="l_f-21" wire:model='services.9' name="services1[]" type="checkbox" @if(in_array(9, $uservices)) checked @endif value="9">Fingering </label>
                        </li>
                        <li class="checkbox">
                          <label class="collection_check_boxes" for="l_f-27" unselectable="on" >
                            <input class="check_boxes" id="l_f-27" wire:model='services.10' name="services1[]" type="checkbox" @if(in_array(10, $uservices)) checked @endif value="10">Fisting </label>
                        </li>
                      </ul>
                      <ul class="check_boxes list-unstyled col-sm-3">
                        <li class="checkbox">
                          <label class="collection_check_boxes" for="l_f-22" unselectable="on" >
                            <input class="check_boxes" id="l_f-22" wire:model='services.11' name="services1[]" type="checkbox" @if(in_array(11, $uservices)) checked @endif value="11">Foot fetish </label>
                        </li>
                        <li class="checkbox">
                          <label class="collection_check_boxes" for="l_f-12" unselectable="on" >
                            <input class="check_boxes" id="l_f-12" wire:model='services.12' name="services1[]" type="checkbox" @if(in_array(12, $uservices)) checked @endif value="12">French kissing </label>
                        </li>
                        <li class="checkbox">
                          <label class="collection_check_boxes" for="l_f-31" unselectable="on" >
                            <input class="check_boxes" id="l_f-31" wire:model='services.13' name="services1[]" type="checkbox" @if(in_array(13, $uservices)) checked @endif value="13">GFE </label>
                        </li>
                        <li class="checkbox">
                          <label class="collection_check_boxes" for="l_f-18" unselectable="on" >
                            <input class="check_boxes" id="l_f-18" wire:model='services.14' name="services[]" type="checkbox" @if(in_array(14, $uservices)) checked @endif value="14">Giving hardsports </label>
                        </li>
                        <li class="checkbox">
                          <label class="collection_check_boxes" for="l_f-32" unselectable="on" >
                            <input class="check_boxes" id="l_f-32" wire:model='services.15' name="services[]" type="checkbox" @if(in_array(15, $uservices)) checked @endif value="15">Receiving hardsports </label>
                        </li>
                        <li class="checkbox">
                          <label class="collection_check_boxes" for="l_f-5" unselectable="on" >
                            <input class="check_boxes" id="l_f-5" wire:model='services.16' name="services[]" type="checkbox" @if(in_array(16, $uservices)) checked @endif value="16">Lap dancing </label>
                        </li>
                        <li class="checkbox">
                          <label class="collection_check_boxes" for="l_f-14" unselectable="on" >
                            <input class="check_boxes" id="l_f-14" wire:model='services.17' name="services[]" type="checkbox" @if(in_array(17, $uservices)) checked @endif value="17">Massage </label>
                        </li>
                        <li class="checkbox">
                          <label class="collection_check_boxes" for="l_f-38" unselectable="on" >
                            <input class="check_boxes" id="l_f-38" wire:model='services.18' name="services[]" type="checkbox" @if(in_array(18, $uservices)) checked @endif value="18">Nuru massage </label>
                        </li>
                        <li class="checkbox">
                          <label class="collection_check_boxes" for="l_f-35" unselectable="on" >
                            <input class="check_boxes" id="l_f-35" wire:model='services.19' name="services[]" type="checkbox" @if(in_array(19, $uservices)) checked @endif value="19">Oral sex - blowjob </label>
                        </li>
                        <li class="checkbox">
                          <label class="collection_check_boxes" for="l_f-34" unselectable="on" >
                            <input class="check_boxes" id="l_f-34" wire:model='services.20' name="services[]" type="checkbox"  @if(in_array(20, $uservices)) checked @endif value="20">OWO - Oral without condom </label>
                        </li>
                      </ul>
                      <ul class="check_boxes list-unstyled col-sm-3">
                        <li class="checkbox">
                          <label class="collection_check_boxes" for="l_f-23" unselectable="on" >
                            <input class="check_boxes" id="l_f-23" wire:model='services.21' name="services[]" type="checkbox" @if(in_array(21, $uservices)) checked @endif value="21">Parties </label>
                        </li>
                        <li class="checkbox">
                          <label class="collection_check_boxes" for="l_f-17" unselectable="on" >
                            <input class="check_boxes" id="l_f-17" wire:model='services.22' name="services[]" type="checkbox" @if(in_array(22, $uservices)) checked @endif value="22">Reverse oral </label>
                        </li>
                        <li class="checkbox">
                          <label class="collection_check_boxes" for="l_f-24" unselectable="on" >
                            <input class="check_boxes" id="l_f-24" wire:model='services.23' name="services[]" type="checkbox"  @if(in_array(23, $uservices)) checked @endif value="23">Giving rimming </label>
                        </li>
                        <li class="checkbox">
                          <label class="collection_check_boxes" for="l_f-25" unselectable="on" >
                            <input class="check_boxes" id="l_f-25" wire:model='services.24' name="services[]" type="checkbox" @if(in_array(24, $uservices)) checked @endif  value="24">Rimming receiving </label>
                        </li>
                        <li class="checkbox">
                          <label class="collection_check_boxes" for="l_f-6" unselectable="on" >
                            <input class="check_boxes" id="l_f-6" wire:model='services.25' name="services[]" type="checkbox" @if(in_array(25, $uservices)) checked @endif  value="25">Role play </label>
                        </li>
                        <li class="checkbox">
                          <label class="collection_check_boxes" for="l_f-15" unselectable="on" >
                            <input class="check_boxes" id="l_f-15" wire:model='services.26' name="services[]" type="checkbox" @if(in_array(26, $uservices)) checked @endif  value="26">Sex toys </label>
                        </li>
                        <li class="checkbox">
                          <label class="collection_check_boxes" for="l_f-11" unselectable="on" >
                            <input class="check_boxes" id="l_f-11" wire:model='services.27' name="services[]" type="checkbox" @if(in_array(27, $uservices)) checked @endif  value="27">Spanking </label>
                        </li>
                        <li class="checkbox">
                          <label class="collection_check_boxes" for="l_f-2" unselectable="on" >
                            <input class="check_boxes" id="l_f-2" wire:model='services.28' name="services[]" type="checkbox" @if(in_array(28, $uservices)) checked @endif  value="28">Strapon </label>
                        </li>
                        <li class="checkbox">
                          <label class="collection_check_boxes" for="l_f-30" unselectable="on" >
                            <input class="check_boxes" id="l_f-30" wire:model='services.29' name="services[]" type="checkbox" @if(in_array(29, $uservices)) checked @endif  value="29">Striptease </label>
                        </li>
                      </ul>
                      <ul class="last check_boxes list-unstyled col-sm-3">
                        <li class="checkbox">
                          <label class="collection_check_boxes" for="l_f-7" unselectable="on" >
                            <input class="check_boxes" id="l_f-7" wire:model='services.30' name="services[]" type="checkbox" @if(in_array(30, $uservices)) checked @endif  value="30">Submissive </label>
                        </li>
                        <li class="checkbox">
                          <label class="collection_check_boxes" for="l_f-40" unselectable="on" >
                            <input class="check_boxes" id="l_f-40" wire:model='services.31' name="services[]" type="checkbox" @if(in_array(31, $uservices)) checked @endif value="31">Squirting </label>
                        </li>
                        <li class="checkbox">
                          <label class="collection_check_boxes" for="l_f-37" unselectable="on" >
                            <input class="check_boxes" id="l_f-37" wire:model='services.32' name="services[]" type="checkbox" @if(in_array(32, $uservices)) checked @endif value="32">Tantric massage </label>
                        </li>
                        <li class="checkbox">
                          <label class="collection_check_boxes" for="l_f-10" unselectable="on" >
                            <input class="check_boxes" id="l_f-10" wire:model='services.33' name="services[]" type="checkbox" @if(in_array(33, $uservices)) checked @endif value="33">Teabagging </label>
                        </li>
                        <li class="checkbox">
                          <label class="collection_check_boxes" for="l_f-26" unselectable="on" >
                            <input class="check_boxes" id="l_f-26" wire:model='services.34' name="services[]" type="checkbox" @if(in_array(34, $uservices)) checked @endif value="34">Tie and tease </label>
                        </li>
                        <li class="checkbox">
                          <label class="collection_check_boxes" for="l_f-16" unselectable="on" >
                            <input class="check_boxes" id="l_f-16" wire:model='services.35' name="services[]" type="checkbox" @if(in_array(35, $uservices)) checked @endif value="3">Uniforms </label>
                        </li>
                        <li class="checkbox">
                          <label class="collection_check_boxes" for="l_f-19" unselectable="on" >
                            <input class="check_boxes" id="l_f-19" wire:model='services.36' name="services[]" type="checkbox" @if(in_array(36, $uservices)) checked @endif value="36">Giving watersports </label>
                        </li>
                        <li class="checkbox">
                          <label class="collection_check_boxes" for="l_f-33" unselectable="on" >
                            <input class="check_boxes" id="l_f-33" wire:model='services.37' name="services[]"type="checkbox" @if(in_array(37, $uservices)) checked @endif value="37">Receiving watersports </label>
                        </li>
                        <li class="checkbox">
                          <label class="collection_check_boxes" for="l_f-20" unselectable="on" >
                            <input class="check_boxes" id="l_f-20" wire:model='services.38' name="services[]" type="checkbox" @if(in_array(38, $uservices)) checked @endif value="38">Webcam sex </label>
                        </li>
                      </ul>
                      <input name="listing[fetish_ids][]" type="hidden" value="">
                    </div>
                    <div class="clearfix"></div>
                  </div>
                  <div data-outlier-price-check-url="/check_outlier_price" id="fees">
                    <h2 class="h3 title-block">Prices</h2>
                    <div class="row d-flex align-items-center">
                      <div class="col-xs-4 col-sm-2">
                        <div class="form-group boolean optional listing_incalls mb-0">
      
                          <label class="boolean optional control-label checkbox" for="listing_incalls">
                            <input class="boolean optional" 
                            wire:model='incall' 
                            type="checkbox" 
                            value="1" 
                            name="listing[incalls]" 
                            id="listing_incalls" />Incall                   </div>
                      </div>
                      <div class="col-md-3 col-xs-8">
                        <div class="form-group price optional listing_incalls_price_per_hour">
                          <label class="price optional control-label" for="listing_incalls_price_per_hour">Per hour from:</label>
                          <div class="price-control"  wire:ignore>
                            <input wire:model='incallprice' data-validations="numericality" class="string optional form-control validate price-amount form-control" type="text" name="listing[incalls_price_per_hour_amount]" id="listing_incalls_price_per_hour_amount" />
                            <select data-radius="right" wire:model='incallcurr' class="apply-custom-select2 price-currency form-control" name="listing[incalls_price_per_hour_currency]" id="listing_incalls_price_per_hour_currency">
                              @foreach($currencies as $curr)
                              <option value="{{$curr->code}}" @if($curr->code == 'AED') selected @endif>{{$curr->code}} </option>
                              @endforeach
                            </select>
                          </div>
                        </div>
                        <p class="alert alert-warning average-price-alert">Price is outside the normal range for this region. Are you sure this is correct?</p>
                      </div>
                    </div>
                    <div class="row d-flex align-items-center">
                      <div class="col-xs-4 col-sm-2">
                        <div class="form-group boolean optional listing_outcalls mb-0">
                          <label class="boolean optional control-label checkbox" for="listing_outcalls">
<input class="boolean optional" 
    wire:model='outcall' 
    type="checkbox" 
    value="1" 
    name="listing[outcalls]" 
    id="listing_outcalls" /> Outcall                        </div>
                      </div>
                      <div class="col-md-3 col-xs-8">
                        <div class="form-group price optional listing_outcalls_price_per_hour">
                          <label class="price optional control-label" for="listing_outcalls_price_per_hour">Per hour from:</label>
                          <div class="price-control"  wire:ignore>
                            <input wire:model='outcallprice' data-validations="numericality" class="string optional form-control validate price-amount form-control" type="text" name="listing[outcalls_price_per_hour_amount]" id="listing_outcalls_price_per_hour_amount" />
                            <select data-radius="right" wire:model='outcallcurr'  class="apply-custom-select2 price-currency form-control" name="listing[outcalls_price_per_hour_currency]" id="listing_outcalls_price_per_hour_currency">
                              @foreach($currencies as $curr)
                              <option value="{{$curr->code}}" @if($curr->code == 'AED') selected @endif>{{$curr->code}} </option>
                              @endforeach
                            </select>
                          </div>
                        </div>
                        <p class="alert alert-warning average-price-alert">Price is outside the normal range for this region. Are you sure this is correct?</p>
                      </div>
                    </div>
                  </div>
                  <div id="about-me">
                    <h2 class="h3 title-block">About me</h2>
                    <div class="form-group radio_buttons required listing_gender_id validate" data-validations="presenceAny">
                      <label class="radio_buttons required control-label">
                        <abbr title="required"></abbr> Your gender (cannot be changed later) </label>
                      <input type="hidden" name="listing[gender_id]" value="" />
                      <span class="radio-inline">
                        <label for="listing_gender_id_1">
                          <input class="radio_buttons required " wire:model='gender' type="radio" value="1" name="listing[gender_id]" id="listing_gender_id_1" />Female </label>
                      </span>
                      <span class="radio-inline">
                        <label for="listing_gender_id_2">
                          <input class="radio_buttons required " wire:model='gender' type="radio" value="2" name="listing[gender_id]" id="listing_gender_id_2" />Male </label>
                      </span>
                      <span class="radio-inline">
                        <label for="listing_gender_id_4">
                          <input class="radio_buttons required " wire:model='gender' type="radio" value="3" name="listing[gender_id]" id="listing_gender_id_4" />Transsexual </label>
                      </span>
                      @error('gender')
                    <span class="validation-error">{{ $message }}</span>
                @enderror
                    </div>
                    <div class="form-group radio_buttons optional listing_sexual_orientation_id">
                      <label class="radio_buttons optional control-label">Orientation</label>
                      <input type="hidden" name="listing[sexual_orientation_id]" value="" />
                      <span class="radio-inline">
                        <label for="listing_sexual_orientation_id_1">
                          <input wire:model='ori' class="radio_buttons optional " type="radio" value="1" checked="checked" name="listing[sexual_orientation_id]" id="listing_sexual_orientation_id_1" />Heterosexual </label>
                      </span>
                      <span class="radio-inline">
                        <label for="listing_sexual_orientation_id_2">
                          <input wire:model='ori' class="radio_buttons optional " type="radio" value="2" name="listing[sexual_orientation_id]" id="listing_sexual_orientation_id_2" />Bisexual </label>
                      </span>
                      <span class="radio-inline">
                        <label for="listing_sexual_orientation_id_3">
                          <input wire:model='ori' class="radio_buttons optional " type="radio" value="3" name="listing[sexual_orientation_id]" id="listing_sexual_orientation_id_3" />Lesbian or Gay </label>
                      </span>
                    </div>
                    <div class="row">
                      <div class="col-md-2 col-sm-4">
                        <div class="form-group select optional listing_ethnicity_id"  wire:ignore>
                          <label class="select optional control-label" for="listing_ethnicity_id">Ethnicity</label>
                          <select data-radius="all" wire:model='ethnicity' class="apply-custom-select2 select optional form-control " name="listing[ethnicity_id]" id="listing_ethnicity_id">
                            <option value=""></option>
                            @foreach($ethnicities as $eth)
                            <option value="{{$eth->id}}">{{$eth->name}}</option>
                            @endforeach
                          </select>
                        </div>
                      </div>
                      <div class="col-md-2 col-sm-4">
                        <div class="form-group string optional listing_height_cm">
                          <label class="string optional control-label" for="listing_height_cm">Height (cm)</label>
                          <input class="string optional form-control " wire:model='height' maxlength="4" size="4" type="text" name="listing[height_cm]" id="listing_height_cm" />
                        </div>
                      </div>
                      <div class="col-md-2 col-sm-4">
                        <div class="form-group integer optional listing_age">
                          <label class="integer optional control-label" for="listing_age">Age</label>
                          <input min="18" max="60" wire:model='age' class="numeric integer optional form-control  form-control" type="number" step="1" name="listing[age]" id="listing_age" />
                        </div>
                      </div>
                      <div class="col-md-2 col-sm-4">
                        <div class="form-group select optional listing_cup_size_id"  wire:ignore>
                          <label class="select optional control-label" for="listing_cup_size_id">Bust</label>
                          <select data-radius="all" class="apply-custom-select2 select optional form-control " wire:model='bust' name="listing[cup_size_id]" id="listing_cup_size_id">
                            <option value=""></option>
                            @foreach($busts as $bust)
                            <option value="{{$bust->id}}">{{$bust->name}}</option>
                            @endforeach
                          </select>
                        </div>
                      </div>
                      <div class="col-md-2 col-sm-4">
                        <div class="form-group select optional listing_hair_color_id"  wire:ignore>
                          <label class="select optional control-label" for="listing_hair_color_id">Hair color</label>
                          <select data-radius="all" class="apply-custom-select2 select optional form-control " wire:model='haircolor' name="listing[hair_color_id]" id="listing_hair_color_id">
                            <option value=""></option>
                            @foreach($hairs as $hair)
                            <option value="{{$hair->id}}">{{$hair->name}}</option>
                            @endforeach
                          </select>
                        </div>
                      </div>
                      <div class="col-md-2 col-sm-4">
                        <div class="form-group select optional listing_nationality_id"  wire:ignore>
                          <label class="select optional control-label" for="listing_nationality_id">Nationality</label>
                          <select data-radius="all" wire:model='nationality' class="apply-custom-select2 select optional form-control " >
                            <option value=""></option>
                            @foreach($countries as $country)
                            <option value="{{$country->id}}">{{$country->nationality}}</option>
                            @endforeach
                          </select>
                        </div>
                      </div>
                    </div>
                    <div id="languages">
                      <label class="label-block">Languages I speak</label>

                      <div id="remove1">
                        <div id="remove" data="1" class="rm-lang-field fa fa-trash-alt fa-lg" data-language-index="1728906815958" rm-lang-field=""></div>

                        <div class="form-group select optional listing_listing_languages_language_id"  wire:ignore>
                          <select data-radius="all" wire:model='language1'  class=" apply-custom-select2 select select21 optional form-control" name="listing[listing_languages_attributes][1728906815958][language_id]" id="listing_listing_languages_attributes_1728906815958_language_id" tabindex="-1" title="">
                            <option value="">Select language...</option>
                          
                           @foreach($languages as $lang)
                            <option value="{{$lang->id}}"  {{ $language1 == $lang->id ? 'selected' : '' }}>{{$lang->name}}</option>
                           @endforeach
                          </select>
                        </div>
                        <div class="form-group radio_buttons optional listing_listing_languages_language_level_id">
                          <input type="hidden" name="listing[listing_languages_attributes][1728906815958][language_level_id]" value="">
                          <span class="radio-inline">
                            <label for="expert1_fluent">
                                <input class="radio_buttons optional radio" wire:model="expert1" type="radio" value="Fluent" id="expert1_fluent">Fluent
                            </label>
                        </span>
                        <span class="radio-inline">
                            <label for="expert1_good">
                                <input class="radio_buttons optional radio" wire:model="expert1" type="radio" value="Good" id="expert1_good">Good
                            </label>
                        </span>
                        <span class="radio-inline">
                            <label for="expert1_basic">
                                <input class="radio_buttons optional radio" wire:model="expert1" type="radio" value="Basic" id="expert1_basic">Basic
                            </label>
                        </span>
                        </div>
                      </div>
                      <div class="clearfix"></div>
                        <div  id="remove2">
                          <div id="remove" data="2" class="rm-lang-field fa fa-trash-alt fa-lg" data-language-index="1728906815958" rm-lang-field=""></div>
                          
                          <div class="form-group select optional listing_listing_languages_language_id"  wire:ignore>
                            <select data-radius="all" data-blank-on-list="true" wire:model="language2" class="apply-custom-select2 select optional form-control" name="listing[listing_languages_attributes][1728906815958][language_id]" id="listing_listing_languages_attributes_1728906815958_language_id" tabindex="-1" title="">
                              <option value="">Select language...</option>
                             @foreach($languages as $lang)
                              <option value="{{$lang->id}}">{{$lang->name}}</option>
                             @endforeach
                            </select>
                          </div>
                          <div class="form-group radio_buttons optional listing_listing_languages_language_level_id">
                            <input type="hidden" name="listing[listing_languages_attributes][1728906815958][language_level_id]" value="">
                            <span class="radio-inline">
                              <label for="expert2_fluent">
                                  <input class="radio_buttons optional radio" wire:model="expert2" type="radio" value="Fluent" id="expert2_fluent">Fluent
                              </label>
                          </span>
                          <span class="radio-inline">
                              <label for="expert2_good">
                                  <input class="radio_buttons optional radio" wire:model="expert2" type="radio" value="Good" id="expert2_good">Good
                              </label>
                          </span>
                          <span class="radio-inline">
                              <label for="expert2_basic">
                                  <input class="radio_buttons optional radio" wire:model="expert2" type="radio" value="Basic" id="expert2_basic">Basic
                              </label>
                          </span>
                          </div>
                        </div>
                        <div class="clearfix"></div>

                        <div  id="remove3">
                          <div id="remove" data="3" class="rm-lang-field fa fa-trash-alt fa-lg" data-language-index="1728906815958" rm-lang-field=""></div>
                          
                          <div class="form-group select optional listing_listing_languages_language_id"  wire:ignore>
                            <select data-radius="all" data-blank-on-list="true" wire:model="language3" class="apply-custom-select2 select optional form-control" name="listing[listing_languages_attributes][1728906815958][language_id]" id="listing_listing_languages_attributes_1728906815958_language_id" tabindex="-1" title="">
                              <option value="">Select language...</option>
                             @foreach($languages as $lang)
                              <option value="{{$lang->id}}">{{$lang->name}}</option>
                             @endforeach
                            </select>
                          </div>
                          <div class="form-group radio_buttons optional listing_listing_languages_language_level_id">
                            <input type="hidden" name="listing[listing_languages_attributes][1728906815958][language_level_id]" value="">
                            <span class="radio-inline">
                              <label for="expert3_fluent">
                                  <input class="radio_buttons optional radio" wire:model="expert3" type="radio" value="Fluent" id="expert3_fluent">Fluent
                              </label>
                          </span>
                          <span class="radio-inline">
                              <label for="expert3_good">
                                  <input class="radio_buttons optional radio" wire:model="expert3" type="radio" value="Good" id="expert3_good">Good
                              </label>
                          </span>
                          <span class="radio-inline">
                              <label for="expert3_basic">
                                  <input class="radio_buttons optional radio" wire:model="expert3" type="radio" value="Basic" id="expert3_basic">Basic
                              </label>
                          </span>
                          </div>
                        </div>
                    
                    </div>

                    <div class="clearfix"></div>
                    <div class="aboutme-radios margin-top">
                      <div class="form-group radio_buttons optional listing_genitals_shaved_type_id">
                        <label class="radio_buttons optional control-label">Shaved</label>
                        <input type="hidden" name="listing[genitals_shaved_type_id]" value="" />
                        <span class="radio-inline">
                          <label for="listing_genitals_shaved_type_id_1">
                            <input class="radio_buttons optional " wire:model='shaved' type="radio" value="no" name="listing[genitals_shaved_type_id]" id="listing_genitals_shaved_type_id_1" />No </label>
                        </span>
                        <span class="radio-inline">
                          <label for="listing_genitals_shaved_type_id_2">
                            <input class="radio_buttons optional " wire:model='shaved' type="radio" value="partialy" name="listing[genitals_shaved_type_id]" id="listing_genitals_shaved_type_id_2" />Partially </label>
                        </span>
                        <span class="radio-inline">
                          <label for="listing_genitals_shaved_type_id_3">
                            <input class="radio_buttons optional " wire:model='shaved' type="radio" value="yes" name="listing[genitals_shaved_type_id]" id="listing_genitals_shaved_type_id_3" />Yes </label>
                        </span>
                      </div>
                    </div>
                    <div class="form-group boolean optional listing_smokes">
                      <input value="0" type="hidden" name="listing[smokes]" />
                      <label class="boolean optional control-label checkbox" for="listing_smokes">
                        <input class="boolean optional" type="checkbox" wire:model='smoke' value="1" name="listing[smokes]" id="listing_smokes" />I smoke </label>
                    </div>
                  </div>
                  <div id="video">
                    <h2 class="h3 title-block">Add video</h2>
                    <div class="input video-embedder" data-height="320" data-service-url="https://massagerepublic.com/action/videos/preview" data-width="510">
                      <div class="row margin-bottom">
                        <div class="col-sm-6">
                          <div id="listing-video-url-input">
                            <div class="input-group1">
                              <input class="form-control validate" wire:model='video' data-validations="urlFormat" placeholder="Video URL" data-validations-error-container="#listing-video-url-input" type="text" name="listing[video_attributes][url]" id="listing_video_attributes_url" />
                              <span class="input-group-btn">
          
                                
                              </span>
                            </div>
                          </div>
                        </div>
                       
                      </div>
                    </div>
                  </div>
                  <div id="social">
                    <h2 class="h3 title-block">Show your X posts</h2>
                    <div class="row">
                      <div class="col-sm-6">
                        <div class="form-group string optional listing_twitter_user">
                          <input class="string optional form-control" maxlength="255" placeholder="Put your X name here to show your recent X posts" size="255" type="text" name="listing[twitter_user]" id="listing_twitter_user" />
                        </div>
                      </div>
                      <div class="col-sm-6">
                        <svg class="margin-bottom" xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="currentColor" style="color: #ffffffff;">
                          <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                        </svg>
                      </div>
                    </div>
                  </div>
                </div>
                <hr />
                
                <button class="btn btn-xs-block btn-primary btn-lg margin-bottom" id="submit" data-btn-submit="" type="submit">
                  <span wire:loading.remove wire:target="updateProfile">
                      Update profile
                  </span>
                  <span wire:loading wire:target="updateProfile">
                      <i class="fa fa-spinner fa-spin"></i> Processing...
                  </span>
              </button>
                          </form>
            </div>
            <input type="hidden" wire:model='num'>
          </div>
          
          @push('js')

                    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

          <script>

// ============================================
// CROSS-BROWSER CITY SEARCH - Robust Implementation
// Works on: Chrome, Safari, Edge, Firefox (Windows, Mac, iOS, Android)
// ============================================

(function() {
    'use strict';
    
    // City Search Module - Singleton pattern to prevent multiple initializations
    var CitySearch = {
        initialized: false,
        input: null,
        dropdown: null,
        results: null,
        cache: {},
        debounceTimer: null,
        currentXHR: null,
        isSelecting: false,
        lastQuery: '',
        
        // Configuration
        config: {
            minChars: 2,
            debounceMs: 250,
            endpoint: '/searchcity'
        },
        
        // Initialize the city search
        init: function() {
            // Prevent double initialization
            if (this.initialized) {
                return;
            }
            
            // Get DOM elements
            this.input = document.getElementById('citysearch');
            this.dropdown = document.querySelector('.citys');
            this.results = document.getElementById('cityappend');
            
            if (!this.input || !this.dropdown || !this.results) {
                // Retry after a short delay if elements not found
                var self = this;
                setTimeout(function() { self.init(); }, 300);
                return;
            }
            
            this.initialized = true;
            this.bindEvents();
        },
        
        // Bind all event listeners
        bindEvents: function() {
            var self = this;
            
            // Input events - using multiple for cross-browser support
            // Safari sometimes doesn't fire 'input' reliably
            var inputHandler = function(e) {
                self.handleInput(e);
            };
            
            this.input.addEventListener('input', inputHandler, false);
            this.input.addEventListener('keyup', inputHandler, false);
            this.input.addEventListener('paste', function(e) {
                // Delay to get pasted value
                setTimeout(function() { self.handleInput(e); }, 10);
            }, false);
            
            // Focus event
            this.input.addEventListener('focus', function(e) {
                var query = self.input.value.trim();
                if (query.length >= self.config.minChars) {
                    self.handleInput(e);
                }
            }, false);
            
            // Blur event - hide dropdown with delay (to allow click on option)
            this.input.addEventListener('blur', function(e) {
                setTimeout(function() {
                    if (!self.isSelecting) {
                        self.hideDropdown();
                    }
                }, 200);
            }, false);
            
            // Click event on results container (event delegation)
            this.results.addEventListener('click', function(e) {
                self.handleSelect(e);
            }, false);
            
            // Touch events for mobile
            this.results.addEventListener('touchend', function(e) {
                self.handleSelect(e);
            }, false);
            
            // Prevent mousedown from triggering blur before click registers
            this.results.addEventListener('mousedown', function(e) {
                e.preventDefault();
                self.isSelecting = true;
            }, false);
            
            // Close on outside click
            document.addEventListener('click', function(e) {
                if (!self.input.contains(e.target) && !self.dropdown.contains(e.target)) {
                    self.hideDropdown();
                }
            }, false);
            
            // Handle keyboard navigation
            this.input.addEventListener('keydown', function(e) {
                self.handleKeydown(e);
            }, false);
        },
        
        // Handle input changes
        handleInput: function(e) {
            var self = this;
            var query = this.input.value.trim();
            
            // Clear any pending request
            if (this.debounceTimer) {
                clearTimeout(this.debounceTimer);
                this.debounceTimer = null;
            }
            
            // Abort any pending XHR request
            if (this.currentXHR) {
                this.currentXHR.abort();
                this.currentXHR = null;
            }
            
            // Check minimum characters
            if (query.length < this.config.minChars) {
                this.hideDropdown();
                this.lastQuery = '';
                return;
            }
            
            // Don't search for same query
            if (query === this.lastQuery && this.dropdown.style.display === 'block') {
                return;
            }
            
            // Show loading state immediately
            this.showLoading();
            
            // Check cache first
            if (this.cache[query]) {
                this.renderResults(this.cache[query]);
                this.lastQuery = query;
                return;
            }
            
            // Debounce the actual search
            this.debounceTimer = setTimeout(function() {
                self.searchCity(query);
            }, this.config.debounceMs);
        },
        
        // Perform the search request
        searchCity: function(query) {
            var self = this;
            
            // Get CSRF token
            var tokenMeta = document.querySelector('meta[name="csrf-token"]');
            var token = tokenMeta ? tokenMeta.getAttribute('content') : '';
            
            if (!token) {
                this.showError('Security token missing. Please refresh the page.');
                return;
            }
            
            // Use XMLHttpRequest for maximum browser compatibility
            var xhr = new XMLHttpRequest();
            this.currentXHR = xhr;
            
            xhr.open('POST', this.config.endpoint, true);
            xhr.setRequestHeader('Content-Type', 'application/json');
            xhr.setRequestHeader('Accept', 'application/json');
            xhr.setRequestHeader('X-CSRF-TOKEN', token);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            
            xhr.timeout = 10000; // 10 second timeout
            
            xhr.onreadystatechange = function() {
                if (xhr.readyState !== 4) return;
                
                self.currentXHR = null;
                
                if (xhr.status === 200) {
                    try {
                        var cities = JSON.parse(xhr.responseText);
                        // Cache the results
                        self.cache[query] = cities;
                        self.lastQuery = query;
                        self.renderResults(cities);
                    } catch (e) {
                        self.showError('Invalid response. Please try again.');
                    }
                } else if (xhr.status === 0) {
                    // Request was aborted or network error
                    // Don't show error for aborted requests
                } else {
                    self.showError('Search failed. Please try again.');
                }
            };
            
            xhr.ontimeout = function() {
                self.currentXHR = null;
                self.showError('Request timed out. Please try again.');
            };
            
            xhr.onerror = function() {
                self.currentXHR = null;
                self.showError('Network error. Please check your connection.');
            };
            
            try {
                xhr.send(JSON.stringify({ val: query }));
            } catch (e) {
                this.showError('Failed to send request.');
            }
        },
        
        // Render search results
        renderResults: function(cities) {
            if (!cities || cities.length === 0) {
                this.results.innerHTML = '<div class="opt" style="color: #ccc; padding: 10px; text-align: center;">No cities found</div>';
                this.showDropdown();
                return;
            }
            
            var html = '';
            for (var i = 0; i < cities.length; i++) {
                var city = cities[i];
                var flagUrl = 'https://flagcdn.com/16x12/' + (city.iso || '').toLowerCase() + '.png';
                html += '<div class="opt optc-item" data-city-id="' + this.escapeHtml(city.id) + '" data-city-name="' + this.escapeHtml(city.name) + '" data-currency="' + this.escapeHtml(city.currency_code || 'USD') + '">' +
                        '<img class="flg" src="' + flagUrl + '" onerror="this.style.display=\'none\'" alt="">' +
                        this.escapeHtml(city.name) +
                        '</div>';
            }
            
            this.results.innerHTML = html;
            this.showDropdown();
        },
        
        // Handle city selection
        handleSelect: function(e) {
            var target = e.target;
            
            // Find the optc-item element
            while (target && !target.classList.contains('optc-item')) {
                target = target.parentElement;
            }
            
            if (!target) return;
            
            e.preventDefault();
            e.stopPropagation();
            
            var cityId = target.getAttribute('data-city-id');
            var cityName = target.getAttribute('data-city-name');
            var currencyCode = target.getAttribute('data-currency');
            
            if (!cityId || !cityName) return;
            
            // Update input field
            this.input.value = cityName;
            
            // Update hidden field
            var hiddenInput = document.getElementById('selectedcity');
            if (hiddenInput) {
                hiddenInput.value = cityId;
            }
            
            // Auto-select currency based on city's country
            if (currencyCode) {
                this.updateCurrency(currencyCode);
            }
            
            // Update Livewire
            this.updateLivewire(cityId, cityName, currencyCode);
            
            // Hide dropdown
            this.hideDropdown();
            this.isSelecting = false;
            
            // Trigger change event for any other listeners
            var changeEvent = document.createEvent('HTMLEvents');
            changeEvent.initEvent('change', true, false);
            this.input.dispatchEvent(changeEvent);
        },
        
        // Handle keyboard navigation
        handleKeydown: function(e) {
            var items = this.results.querySelectorAll('.optc-item');
            if (items.length === 0) return;
            
            var current = this.results.querySelector('.optc-item.highlighted');
            var index = -1;
            
            if (current) {
                for (var i = 0; i < items.length; i++) {
                    if (items[i] === current) {
                        index = i;
                        break;
                    }
                }
            }
            
            if (e.keyCode === 40) { // Down arrow
                e.preventDefault();
                if (current) current.classList.remove('highlighted');
                index = (index + 1) % items.length;
                items[index].classList.add('highlighted');
                items[index].scrollIntoView({ block: 'nearest' });
            } else if (e.keyCode === 38) { // Up arrow
                e.preventDefault();
                if (current) current.classList.remove('highlighted');
                index = index <= 0 ? items.length - 1 : index - 1;
                items[index].classList.add('highlighted');
                items[index].scrollIntoView({ block: 'nearest' });
            } else if (e.keyCode === 13) { // Enter
                e.preventDefault();
                if (current) {
                    this.handleSelect({ target: current, preventDefault: function(){}, stopPropagation: function(){} });
                }
            } else if (e.keyCode === 27) { // Escape
                this.hideDropdown();
            }
        },
        
        // Update currency selects based on city's country
        updateCurrency: function(currencyCode) {
            if (!currencyCode) return;
            
            // Update both incall and outcall currency selects
            var incallSelect = document.getElementById('listing_incalls_price_per_hour_currency');
            var outcallSelect = document.getElementById('listing_outcalls_price_per_hour_currency');
            
            // Function to update a select element and its custom select2
            var updateSelect = function(selectElement, value) {
                if (!selectElement) return;
                
                // Check if the value exists in the select options
                var optionExists = false;
                for (var i = 0; i < selectElement.options.length; i++) {
                    if (selectElement.options[i].value === value) {
                        optionExists = true;
                        break;
                    }
                }
                
                if (!optionExists) return;
                
                // Update the native select value
                selectElement.value = value;
                
                // Trigger change event for Livewire and custom select2
                var event = new Event('change', { bubbles: true });
                selectElement.dispatchEvent(event);
                
                // Update custom select2 display if it exists
                var customSelect2 = selectElement.customSelect2Instance;
                if (customSelect2) {
                    // Find the option element in custom select2
                    var options = customSelect2.resultsList.querySelectorAll('.custom-select2-option');
                    options.forEach(function(opt) {
                        if (opt.dataset.value === value) {
                            customSelect2.selectOption(opt, false);
                        }
                    });
                }
                
                // Also try updating Select2 library if it's used
                if (typeof $ !== 'undefined' && $.fn && $.fn.select2) {
                    try {
                        $(selectElement).val(value).trigger('change');
                    } catch (e) {}
                }
            };
            
            // Update both selects
            updateSelect(incallSelect, currencyCode);
            updateSelect(outcallSelect, currencyCode);
            
            // Also try to update Livewire directly
            try {
                @this.set('incallcurr', currencyCode);
                @this.set('outcallcurr', currencyCode);
            } catch (e) {}
        },
        
        // Update Livewire component
        updateLivewire: function(cityId, cityName, currencyCode) {
            // Method 1: Try @this (Blade inline)
            try {
                if (typeof Livewire !== 'undefined') {
                    // Find the Livewire component
                    var component = Livewire.find(
                        this.input.closest('[wire\\:id]')?.getAttribute('wire:id')
                    );
                    if (component) {
                        component.set('city', cityId);
                        component.set('selectedcity', cityName);
                        if (currencyCode) {
                            component.set('incallcurr', currencyCode);
                            component.set('outcallcurr', currencyCode);
                        }
                        return;
                    }
                }
            } catch (e) {}
            
            // Method 2: Try window.Livewire emit
            try {
                if (typeof Livewire !== 'undefined' && Livewire.emit) {
                    Livewire.emit('citySelected', cityId, cityName, currencyCode);
                }
            } catch (e) {}
            
            // Method 3: Direct @this call (this works in Blade context)
            try {
                @this.set('city', cityId);
                @this.set('selectedcity', cityName);
                if (currencyCode) {
                    @this.set('incallcurr', currencyCode);
                    @this.set('outcallcurr', currencyCode);
                }
            } catch (e) {}
        },
        
        // Show the dropdown
        showDropdown: function() {
            this.dropdown.style.display = 'block';
        },
        
        // Hide the dropdown
        hideDropdown: function() {
            this.dropdown.style.display = 'none';
            this.results.innerHTML = '';
        },
        
        // Show loading state
        showLoading: function() {
            this.results.innerHTML = '<div class="opt" style="color: #999; padding: 10px; text-align: center;"><i class="fa fa-spinner fa-spin"></i> Searching...</div>';
            this.showDropdown();
        },
        
        // Show error message
        showError: function(message) {
            this.results.innerHTML = '<div class="opt" style="color: #ff6b6b; padding: 10px; text-align: center;">' + this.escapeHtml(message) + '</div>';
            this.showDropdown();
            var self = this;
            setTimeout(function() {
                self.hideDropdown();
            }, 3000);
        },
        
        // Escape HTML to prevent XSS
        escapeHtml: function(text) {
            if (!text) return '';
            var div = document.createElement('div');
            div.appendChild(document.createTextNode(text));
            return div.innerHTML;
        }
    };
    
    // Initialize when DOM is ready
    function initCitySearch() {
        CitySearch.init();
    }
    
    // Multiple initialization points for maximum compatibility
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCitySearch);
    } else {
        // DOM already loaded
        initCitySearch();
    }
    
    // Also initialize on window load (fallback)
    window.addEventListener('load', function() {
        if (!CitySearch.initialized) {
            initCitySearch();
        }
    });
    
    // Livewire integration
    if (typeof Livewire !== 'undefined') {
        document.addEventListener('livewire:load', function() {
            if (!CitySearch.initialized) {
                initCitySearch();
            }
        });
        
        // Re-init after Livewire updates (in case DOM changes)
        document.addEventListener('livewire:update', function() {
            if (!document.getElementById('citysearch')) {
                CitySearch.initialized = false;
                initCitySearch();
            }
        });
    }
    
    // Expose for debugging
    window.CitySearch = CitySearch;
    
})();

// Character count function for About Me textarea
function updateCharCount(textarea) {
    var text = textarea.value.trim();
    var charCount = text.length;
    
    var countDisplay = document.getElementById('char-count');
    var warningDisplay = document.getElementById('char-count-warning');
    var okDisplay = document.getElementById('char-count-ok');
    
    if (countDisplay) {
        countDisplay.textContent = charCount + ' character' + (charCount !== 1 ? 's' : '');
        
        if (charCount < 50) {
            countDisplay.style.color = '#dc3545';
            if (warningDisplay) warningDisplay.style.display = 'inline';
            if (okDisplay) okDisplay.style.display = 'none';
        } else {
            countDisplay.style.color = '#28a745';
            if (warningDisplay) warningDisplay.style.display = 'none';
            if (okDisplay) okDisplay.style.display = 'inline';
        }
    }
}

// Initialize character count on page load
document.addEventListener('DOMContentLoaded', function() {
    var aboutMeTextarea = document.getElementById('listing_description');
    if (aboutMeTextarea) {
        updateCharCount(aboutMeTextarea);
        // Also listen for Livewire updates
        aboutMeTextarea.addEventListener('input', function() {
            updateCharCount(this);
        });
    }
});

// Re-initialize after Livewire updates
if (typeof Livewire !== 'undefined') {
    document.addEventListener('livewire:update', function() {
        var aboutMeTextarea = document.getElementById('listing_description');
        if (aboutMeTextarea) {
            updateCharCount(aboutMeTextarea);
        }
    });
}

        var num = 2;
   $("a#appendlang").click(function(){
    num++;
  
        var template = '<div id="remove'+num+'"> <div id="remove" data="'+num+'" class="rm-lang-field fa fa-trash-alt fa-lg" data-language-index="new_record_tpl_id" rm-lang-field=""></div> <div class="form-group hidden listing_listing_languages__destroy"> <input class="hidden destroy" type="hidden" value="false" name="listing[listing_languages_attributes][new_record_tpl_id][_destroy]" id="listing_listing_languages_attributes_new_record_tpl_id__destroy" /> </div> <div class="form-group select optional listing_listing_languages_language_id"> <select wire:model="language'+num+'" data-blank-on-list="true" class="select optional form-control" name="listing[listing_languages_attributes][new_record_tpl_id][language_id]" id="listing_listing_languages_attributes_new_record_tpl_id_language_id"> <option value="">Select language...</option> <option value="1">Arabic</option> <option value="2">Azerbaijani</option> <option value="3">Bengali</option> <option value="4">Bulgarian</option> <option value="5">Catalan</option> <option value="6">Czech</option> <option value="7">Danish</option> <option value="8">German</option> <option value="9">Greek</option> <option value="10">English</option> <option value="11">Estonian</option> <option value="12">Persian</option> <option value="13">Finnish</option> <option value="14">French</option> <option value="15">Irish</option> <option value="16">Gujarati</option> <option value="17">Hebrew</option> <option value="18">Hindi</option> <option value="19">Hungarian</option> <option value="20">Icelandic</option> <option value="21">Italian</option> <option value="22">Javanese</option> <option value="23">Japanese</option> <option value="24">Kannada</option> <option value="25">Korean</option> <option value="62">Laotian</option> <option value="26">Latvian</option> <option value="27">Lithuanian</option> <option value="28">Malayalam</option> <option value="29">Marathi</option> <option value="30">Maltese</option> <option value="31">Malay</option> <option value="32">Dutch</option> <option value="33">Norwegian</option> <option value="34">Polish</option> <option value="35">Portuguese</option> <option value="36">Romanian</option> <option value="37">Russian</option> <option value="38">Slovak</option> <option value="39">Spanish</option> <option value="40">Swedish</option> <option value="41">Tamil</option> <option value="42">Telugu</option> <option value="43">Thai</option> <option value="44">Turkish</option> <option value="45">Ukrainian</option> <option value="46">Urdu</option> <option value="47">Vietnamese</option> <option value="48">Chinese</option> <option value="50">Macedonian</option> <option value="51">Punjabi</option> <option value="52">Croatian</option> <option value="53">Igbo</option> <option value="54">Swahili</option> <option value="55">Zulu</option> <option value="56">Yoruba</option> <option value="57">Indonesian</option> <option value="58">Hausa</option> <option value="59">Serbian</option> <option value="60">Afrikaans</option> </select> </div> <div class="form-group radio_buttons optional listing_listing_languages_language_level_id"> <input type="hidden" name="listing[listing_languages_attributes][1728906815958][language_level_id]" value=""> <span class="radio-inline"> <label for="listing_listing_languages_attributes_1728906815958_language_level_id_4"> <input class="radio_buttons optional radio" wire:model="expert'+num+'" type="radio" value="Fluent" name="listing[listing_languages_attributes][1728906815958][language_level_id'+num+']" id="listing_listing_languages_attributes_1728906815958_language_level_id_4">Fluent </label> </span> <span class="radio-inline"> <label for="listing_listing_languages_attributes_1728906815958_language_level_id_3"> <input class="radio_buttons optional radio" wire:model="expert'+num+'" type="radio" value="Good" name="listing[listing_languages_attributes][1728906815958][language_level_id'+num+']" id="listing_listing_languages_attributes_1728906815958_language_level_id_3">Good </label> </span> <span class="radio-inline"> <label for="listing_listing_languages_attributes_1728906815958_language_level_id_1"> <input class="radio_buttons optional radio" type="radio" wire:model="expert'+num+'" value="1" name="listing[listing_languages_attributes][1728906815958][language_level_id'+num+']" id="listing_listing_languages_attributes_1728906815958_language_level_id_1">Basic </label> </span> </div> </div> <div class="clearfix"></div>';

    $("div#languagesappend").append(template);
 

    $("div#remove").click(function(){
    var id = $(this).attr('data');
    console.log(id);
    $("div#remove"+id).remove();
    n--;

   });
   });


   $("div#remove").click(function(){
    var id = $(this).attr('data');
    console.log(id);
    $("div#remove"+id).remove();
    n--;
    $("input#num").val(num);
   });

   $("button#submit").click(function(){
    
    @this.set('num', num);
   });


   $(document).ready(function() {
    // Check if select2 is available before trying to use it
    if (typeof $.fn.select2 === 'undefined') {
        console.log('Select2 not loaded, skipping initialization');
        return;
    }
    
    // Initialize all Select2 instances
    
    // Country code selects
    $('.select2-country').select2({
        placeholder: 'Select country code',
        allowClear: true,
        width: '100%',
        theme: 'bootstrap4'
    });

    // Listing type select
    $('#listing_listed_as_id').select2({
        theme: 'bootstrap4',
        width: '100%'
    }).val('{{$user->city}}').trigger('change');

    // Ethnicity select
    $('#listing_ethnicity_id').select2({
        theme: 'bootstrap4',
        width: '100%',
        placeholder: 'Select ethnicity'
    }).val('{{$user->ethnicity}}').trigger('change');

    // Cup size select
    $('#listing_cup_size_id').select2({
        theme: 'bootstrap4',
        width: '100%',
        placeholder: 'Select bust size'
    }).val('{{$user->bust}}').trigger('change');

    // Hair color select
    $('#listing_hair_color_id').select2({
        theme: 'bootstrap4',
        width: '100%',
        placeholder: 'Select hair color'
    }).val('{{$user->haircolor}}').trigger('change');

    // Nationality select
    $('select[wire\\:model="nationality"]').select2({
        theme: 'bootstrap4',
        width: '100%',
        placeholder: 'Select nationality'
    }).val('{{$user->nationality}}').trigger('change');

    // Currency selects for pricing
    $('#listing_incalls_price_per_hour_currency, #listing_outcalls_price_per_hour_currency').select2({
        theme: 'bootstrap4',
        width: '50%'
    });
    
    // Set values for currency selects if they exist
    if ('{{$user->incallcurr}}') {
        $('#listing_incalls_price_per_hour_currency').val('{{$user->incallcurr}}').trigger('change');
    }
    
    if ('{{$user->outcallcurr}}') {
        $('#listing_outcalls_price_per_hour_currency').val('{{$user->outcallcurr}}').trigger('change');
    }

    // Language selects
    $('select[wire\\:model^="language"]').each(function() {
        $(this).select2({
            theme: 'bootstrap4',
            width: '100%',
            placeholder: 'Select language'
        });
    });
    
    // Set values for language selects if they exist
    if ('{{$language1}}') {
        $('select[wire\\:model="language1"]').val('{{$language1}}').trigger('change');
    }
    
    if ('{{$language2}}') {
        $('select[wire\\:model="language2"]').val('{{$language2}}').trigger('change');
    }
    
    if ('{{$language3}}') {
        $('select[wire\\:model="language3"]').val('{{$language3}}').trigger('change');
    }

    // Handle Livewire updates for Select2 elements
    // This ensures the selected values are properly sent to the Livewire component
    $('.select2-country').on('change', function() {
        let modelName = $(this).attr('id') === 'first_phone_code' ? 'countrycode' : 'countrycode2';
        @this.set(modelName, $(this).val());
    });

    $('#listing_listed_as_id').on('change', function() {
        @this.set('listing', $(this).val());
    });

    $('#listing_ethnicity_id').on('change', function() {
        @this.set('ethnicity', $(this).val());
    });

    $('#listing_cup_size_id').on('change', function() {
        @this.set('bust', $(this).val());
    });

    $('#listing_hair_color_id').on('change', function() {
        @this.set('haircolor', $(this).val());
    });

    $('select[wire\\:model="nationality"]').on('change', function() {
        @this.set('nationality', $(this).val());
    });

    $('#listing_incalls_price_per_hour_currency').on('change', function() {
        @this.set('incallcurr', $(this).val());
    });

    $('#listing_outcalls_price_per_hour_currency').on('change', function() {
        @this.set('outcallcurr', $(this).val());
    });

    $('select[wire\\:model="language1"]').on('change', function() {
        @this.set('language1', $(this).val());
    });

    $('select[wire\\:model="language2"]').on('change', function() {
        @this.set('language2', $(this).val());
    });

    $('select[wire\\:model="language3"]').on('change', function() {
        @this.set('language3', $(this).val());
    });

    // Handle Livewire updates
    if (typeof Livewire !== 'undefined') {
        Livewire.on('contentChanged', () => {
            // Check if select2 is available
            if (typeof $.fn.select2 === 'undefined') {
                return;
            }
            
            // Refresh all Select2 instances after Livewire updates the DOM
            $('.select2-country').select2({
            placeholder: 'Select country code',
            allowClear: true,
            width: '100%',
            theme: 'bootstrap4'
        });
        
        $('#listing_listed_as_id, #listing_ethnicity_id, #listing_cup_size_id, #listing_hair_color_id').select2({
            theme: 'bootstrap4',
            width: '100%'
        });
        
        $('select[wire\\:model="nationality"]').select2({
            theme: 'bootstrap4',
            width: '100%'
        });
        
        $('#listing_incalls_price_per_hour_currency, #listing_outcalls_price_per_hour_currency').select2({
            theme: 'bootstrap4',
            width: '100%'
        });
        
        $('select[wire\\:model^="language"]').each(function() {
            $(this).select2({
                theme: 'bootstrap4',
                width: '100%',
                placeholder: 'Select language'
            });
        });
        });
    }
});
 
// Drag and Drop functionality
let dragDropInitialized = false;

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing drag and drop...');
    setTimeout(function() {
        initDragDrop();
    }, 500);
});

function initDragDrop() {
    if (dragDropInitialized) {
        console.log('Drag drop already initialized');
        return;
    }

    const dragDropArea = document.getElementById('drag-drop-area');
    const fileInput = document.getElementById('mphoto');

    if (!dragDropArea || !fileInput) {
        console.log('Drag and drop elements not found, retrying...');
        setTimeout(initDragDrop, 500);
        return;
    }

    console.log('Drag and drop elements found');

    dragDropArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        e.stopPropagation();
        this.classList.add('dragover');
    });

    dragDropArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        e.stopPropagation();
        this.classList.remove('dragover');
    });

    dragDropArea.addEventListener('drop', function(e) {
        e.preventDefault();
        e.stopPropagation();
        this.classList.remove('dragover');
        
        const files = e.dataTransfer.files;
        console.log('Files dropped:', files.length);
        
        if (files.length > 0 && files[0].type.startsWith('image/')) {
            console.log('Image file detected:', files[0].name);
            
            // Use DataTransfer to properly set files
            const dataTransfer = new DataTransfer();
            for (let i = 0; i < files.length; i++) {
                if (files[i].type.startsWith('image/')) {
                    dataTransfer.items.add(files[i]);
                }
            }
            fileInput.files = dataTransfer.files;
            
            // Trigger Livewire update
            fileInput.dispatchEvent(new Event('change', { bubbles: true }));
            
            console.log('Upload initiated');
        }
    });

    // Click handler for drag-drop area (backup for non-label clicks)
    dragDropArea.addEventListener('click', function(e) {
        // If clicking the button, let the inline onclick handle it
        if (e.target.closest('.btn-primary')) {
            return;
        }
        // For clicks on other areas, trigger file input
        console.log('Area clicked - opening file dialog');
        e.preventDefault();
        e.stopPropagation();
        
        // Use setTimeout for iOS Safari compatibility
        setTimeout(function() {
            fileInput.click();
        }, 0);
    });

    dragDropInitialized = true;
    console.log('Drag and drop ready');
}

// jQuery code with safety check
if (typeof jQuery !== 'undefined') {
    jQuery(document).ready(function($) {
        // Any additional jQuery code can go here
    });
}

// Custom Select2 Implementation
class CustomSelect2 {
    constructor(selectElement, options = {}) {
        this.select = selectElement;
        this.options = {
            placeholder: options.placeholder || 'Select an option',
            searchable: options.searchable !== false,
            onChange: options.onChange || null
        };
        
        this.isOpen = false;
        this.selectedValue = this.select.value;
        this.init();
    }

    init() {
        // Hide original select
        this.select.style.display = 'none';
        
        // Get initial value from select or from Livewire wire:model
        const wireModel = this.select.getAttribute('wire:model');
        if (!this.selectedValue && wireModel && typeof Livewire !== 'undefined') {
            // Try to get value from Livewire component
            try {
                const componentId = this.select.closest('[wire\\:id]')?.getAttribute('wire:id');
                if (componentId && window.Livewire) {
                    const component = window.Livewire.find(componentId);
                    if (component && component.get) {
                        this.selectedValue = component.get(wireModel);
                    }
                }
            } catch (e) {
                console.log('Could not get Livewire value:', e);
            }
        }
        
        // Create custom select structure
        this.createCustomSelect();
        this.attachEventListeners();
        
        // Set initial value if exists
        if (this.selectedValue) {
            this.selectOption(this.selectedValue);
        }
    }

    createCustomSelect() {
        // Create wrapper
        this.wrapper = document.createElement('div');
        this.wrapper.className = 'custom-select2';
        
        // For country code selects, set specific width
        if (this.select.classList.contains('select2-country')) {
            this.wrapper.style.width = '34%';
            this.wrapper.style.display = 'inline-block';
        } else {
            this.wrapper.style.width = '100%';
            this.wrapper.style.display = 'inline-block';
        }
        
        this.wrapper.style.position = 'relative';
        
        // Apply radius class if specified
        const radiusClass = this.select.getAttribute('data-radius');
        if (radiusClass) {
            this.wrapper.classList.add('radius-' + radiusClass);
        }
        
        // Create selection box
        this.selectionBox = document.createElement('div');
        this.selectionBox.className = 'custom-select2-selection';
        this.selectionBox.textContent = this.options.placeholder;
        
        // Create dropdown
        this.dropdown = document.createElement('div');
        this.dropdown.className = 'custom-select2-dropdown';
        
        // Create search input if searchable
        if (this.options.searchable) {
            const searchContainer = document.createElement('div');
            searchContainer.className = 'custom-select2-search';
            
            this.searchInput = document.createElement('input');
            this.searchInput.type = 'text';
            this.searchInput.placeholder = 'Search...';
            
            searchContainer.appendChild(this.searchInput);
            this.dropdown.appendChild(searchContainer);
        }
        
        // Create results list
        this.resultsList = document.createElement('ul');
        this.resultsList.className = 'custom-select2-results';
        
        // Populate options
        Array.from(this.select.options).forEach(option => {
            if (option.value === '') return; // Skip placeholder option
            
            const li = document.createElement('li');
            li.className = 'custom-select2-option';
            li.textContent = option.textContent;
            li.dataset.value = option.value;
            
            if (option.value === this.selectedValue) {
                li.classList.add('selected');
            }
            
            this.resultsList.appendChild(li);
        });
        
        this.dropdown.appendChild(this.resultsList);
        this.wrapper.appendChild(this.selectionBox);
        this.wrapper.appendChild(this.dropdown);
        
        // Insert after select element
        this.select.parentNode.insertBefore(this.wrapper, this.select.nextSibling);
    }

    attachEventListeners() {
        // Toggle dropdown
        this.selectionBox.addEventListener('click', (e) => {
            e.stopPropagation();
            this.toggleDropdown();
        });
        
        // Search functionality
        if (this.searchInput) {
            this.searchInput.addEventListener('input', (e) => {
                e.stopPropagation();
                this.filterOptions(e.target.value);
            });
            
            this.searchInput.addEventListener('click', (e) => {
                e.stopPropagation();
            });
        }
        
        // Option selection - handle both click and touch
        const handleOptionSelect = (e) => {
            e.preventDefault();
            e.stopPropagation();
            
            let target = e.target;
            // Find the option element if clicked on child
            while (target && !target.classList.contains('custom-select2-option')) {
                target = target.parentElement;
                if (target === this.resultsList) {
                    target = null;
                    break;
                }
            }
            
            if (target && target.classList.contains('custom-select2-option')) {
                this.selectOption(target.dataset.value);
                this.closeDropdown();
            }
        };
        
        this.resultsList.addEventListener('click', handleOptionSelect);
        this.resultsList.addEventListener('touchend', handleOptionSelect);
        
        // Close on outside click
        document.addEventListener('click', (e) => {
            if (!this.wrapper.contains(e.target)) {
                this.closeDropdown();
            }
        });
        
        // Prevent dropdown from closing when clicking inside
        this.dropdown.addEventListener('click', (e) => {
            e.stopPropagation();
        });
    }

    toggleDropdown() {
        if (this.isOpen) {
            this.closeDropdown();
        } else {
            this.openDropdown();
        }
    }

    openDropdown() {
        this.dropdown.classList.add('open');
        this.selectionBox.classList.add('open');
        this.isOpen = true;
        
        if (this.searchInput) {
            setTimeout(() => this.searchInput.focus(), 100);
        }
    }

    closeDropdown() {
        this.dropdown.classList.remove('open');
        this.selectionBox.classList.remove('open');
        this.isOpen = false;
        
        if (this.searchInput) {
            this.searchInput.value = '';
            this.filterOptions('');
        }
    }

    filterOptions(searchTerm) {
        const options = this.resultsList.querySelectorAll('.custom-select2-option');
        const term = searchTerm.toLowerCase();
        
        options.forEach(option => {
            const text = option.textContent.toLowerCase();
            if (text.includes(term)) {
                option.classList.remove('hidden');
            } else {
                option.classList.add('hidden');
            }
        });
    }

    selectOption(value) {
        // Update select element
        this.select.value = value;
        this.selectedValue = value;
        
        // Trigger change event on original select
        const event = new Event('change', { bubbles: true });
        this.select.dispatchEvent(event);
        
        // Update UI
        const selectedOption = Array.from(this.select.options).find(opt => opt.value === value);
        if (selectedOption) {
            const fullText = selectedOption.textContent;
            let displayText = fullText;
            
            // Only extract country code for country code selects (select2-country class)
            if (this.select.classList.contains('select2-country')) {
                const codeMatch = fullText.match(/^(\+\d+)/);
                displayText = codeMatch ? codeMatch[1] : fullText;
            }
            // For currency selects in price-control, show only the currency code
            else if (this.select.classList.contains('price-currency')) {
                displayText = fullText.trim();
            }
            
            this.selectionBox.textContent = displayText;
            this.selectionBox.classList.remove('custom-select2-placeholder');
        }
        
        // Update selected state in list
        this.resultsList.querySelectorAll('.custom-select2-option').forEach(li => {
            if (li.dataset.value === value) {
                li.classList.add('selected');
            } else {
                li.classList.remove('selected');
            }
        });
        
        // Call onChange callback if provided
        if (this.options.onChange) {
            this.options.onChange(value);
        }
        
        // Update Livewire if wire:model exists
        const wireModel = this.select.getAttribute('wire:model');
        if (wireModel && typeof Livewire !== 'undefined') {
            @this.set(wireModel, value);
        }
    }

    destroy() {
        // Remove the wrapper from DOM
        if (this.wrapper && this.wrapper.parentNode) {
            this.wrapper.parentNode.removeChild(this.wrapper);
        }
        // Show original select
        this.select.style.display = '';
        // Clear reference
        this.wrapper = null;
    }
}

// Initialize Custom Select2 for elements with apply-custom-select2 class
function initializeCustomSelect2() {
    console.log('Initializing Custom Select2...');
    const customSelects = document.querySelectorAll('select.apply-custom-select2');
    console.log('Found custom selects:', customSelects.length);
    
    customSelects.forEach(select => {
        // Check if there's already a wrapper next to this select
        const existingWrapper = select.nextElementSibling;
        if (existingWrapper && existingWrapper.classList.contains('custom-select2')) {
            console.log('Wrapper already exists for', select.id, '- skipping');
            return;
        }
        
        // Destroy existing instance if present
        if (select.customSelect2Instance) {
            console.log('Destroying existing instance for', select.id);
            select.customSelect2Instance.destroy();
            select.customSelect2Instance = null;
        }
        
        const placeholder = select.options[0]?.textContent || 'Select an option';
        const wireModel = select.getAttribute('wire:model');
        
        console.log('Creating new instance for', select.id);
        select.customSelect2Instance = new CustomSelect2(select, {
            placeholder: placeholder,
            searchable: true,
            onChange: (value) => {
                // Trigger native change event for wire:model to pick up
                select.value = value;
                select.dispatchEvent(new Event('change', { bubbles: true }));
                select.dispatchEvent(new Event('input', { bubbles: true }));
                
                // Also try direct Livewire sync
                if (wireModel && typeof Livewire !== 'undefined') {
                    try {
                        // Find the Livewire component
                        const component = Livewire.find(select.closest('[wire\\:id]')?.getAttribute('wire:id'));
                        if (component) {
                            component.set(wireModel, value);
                        }
                    } catch (e) {
                        console.log('Livewire sync fallback:', e);
                    }
                }
            }
        });
    });
    console.log('Custom Select2 initialization complete');
}

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', function() {
    // Wait a bit for Livewire to finish rendering
    setTimeout(initializeCustomSelect2, 100);
});

// Re-initialize after Livewire updates
if (typeof Livewire !== 'undefined') {
    Livewire.hook('message.processed', (message, component) => {
        setTimeout(initializeCustomSelect2, 100);
    });
}

// CRITICAL: Re-initialize on Livewire navigation events
document.addEventListener('livewire:navigated', function() {
    console.log('Livewire navigated - reinitializing custom select2');
    setTimeout(initializeCustomSelect2, 200);
});

document.addEventListener('livewire:load', function() {
    console.log('Livewire loaded - reinitializing custom select2');
    setTimeout(initializeCustomSelect2, 200);
});

// Drag and Drop Image Reordering Functionality
function initializeDragAndDrop() {
    console.log('=== INITIALIZING DRAG AND DROP ===');
    const container = document.getElementById('image-container');
    if (!container) {
        console.log('❌ Image container not found');
        return;
    }

    console.log('✅ Container found:', container);

    const imageCards = container.querySelectorAll('.record.image[draggable="true"]');
    console.log('📸 Found', imageCards.length, 'draggable images');

    if (imageCards.length === 0) {
        console.log('⚠️ No draggable images found');
        return;
    }

    // Log each image found
    imageCards.forEach((card, idx) => {
        const isExisting = card.classList.contains('existing-image');
        const id = card.getAttribute('data-id');
        const index = card.getAttribute('data-index');
        console.log(`  Image ${idx}: ${isExisting ? 'Existing ID=' + id : 'Temp Index=' + index}`);
    });

    let draggedElement = null;
    let draggedId = null;
    let draggedIndex = null;
    let isExistingImage = false;

    imageCards.forEach((card, cardIndex) => {
        // Skip if already initialized
        if (card.hasAttribute('data-drag-initialized')) {
            console.log('  ⏭️ Skipping already initialized card');
            return;
        }
        
        // Mark as initialized
        card.setAttribute('data-drag-initialized', 'true');
        
        // Dragstart event
        card.addEventListener('dragstart', function(e) {
            console.log('🎬 Drag started');
            draggedElement = this;
            isExistingImage = this.classList.contains('existing-image');
            
            if (isExistingImage) {
                draggedId = this.getAttribute('data-id');
                console.log('  → Existing image ID:', draggedId);
            } else {
                draggedIndex = parseInt(this.getAttribute('data-index'));
                console.log('  → Temp image index:', draggedIndex);
            }
            
            this.style.opacity = '0.5';
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/html', this.innerHTML);
        });

        // Dragend event
        card.addEventListener('dragend', function(e) {
            console.log('🏁 Drag ended');
            this.style.opacity = '';
            const allCards = container.querySelectorAll('.record.image[draggable="true"]');
            allCards.forEach(c => c.classList.remove('drag-over'));
        });

        // Dragover event
        card.addEventListener('dragover', function(e) {
            if (e.preventDefault) {
                e.preventDefault();
            }
            e.dataTransfer.dropEffect = 'move';
            this.classList.add('drag-over');
            return false;
        });

        // Dragenter event
        card.addEventListener('dragenter', function(e) {
            this.classList.add('drag-over');
        });

        // Dragleave event
        card.addEventListener('dragleave', function(e) {
            this.classList.remove('drag-over');
        });

        // Drop event
        card.addEventListener('drop', function(e) {
            console.log('📦 Drop event triggered');
            if (e.stopPropagation) {
                e.stopPropagation();
            }
            e.preventDefault();

            const dropIsExisting = this.classList.contains('existing-image');
            const dropId = dropIsExisting ? this.getAttribute('data-id') : null;
            const dropIndex = !dropIsExisting ? parseInt(this.getAttribute('data-index')) : null;

            console.log('  → Drop target:', dropIsExisting ? 'Existing ID=' + dropId : 'Temp Index=' + dropIndex);

            // UNIFIED REORDERING: Allow dragging across all images
            const allImages = Array.from(container.querySelectorAll('.record.image[draggable="true"]'));
            console.log('  → Total images:', allImages.length);
            
            // Find positions of dragged and drop target
            let draggedPos = -1;
            let dropPos = -1;
            
            allImages.forEach((img, idx) => {
                const imgIsExisting = img.classList.contains('existing-image');
                const imgId = img.getAttribute('data-id');
                const imgIndex = img.getAttribute('data-index');
                
                // Check if this is the dragged image
                if (isExistingImage && imgIsExisting && imgId === draggedId) {
                    draggedPos = idx;
                    console.log('  → Found dragged existing image at position', idx);
                } else if (!isExistingImage && !imgIsExisting && parseInt(imgIndex) === draggedIndex) {
                    draggedPos = idx;
                    console.log('  → Found dragged temp image at position', idx);
                }
                
                // Check if this is the drop target
                if (dropIsExisting && imgIsExisting && imgId === dropId) {
                    dropPos = idx;
                    console.log('  → Found drop target existing image at position', idx);
                } else if (!dropIsExisting && !imgIsExisting && parseInt(imgIndex) === dropIndex) {
                    dropPos = idx;
                    console.log('  → Found drop target temp image at position', idx);
                }
            });
            
            console.log('  → Dragged position:', draggedPos, 'Drop position:', dropPos);
            
            if (draggedPos === -1 || dropPos === -1 || draggedPos === dropPos) {
                console.log('  → Invalid positions, aborting');
                this.classList.remove('drag-over');
                return false;
            }
            
            // Build new order array with all images
            const newOrder = allImages.map(img => {
                const imgIsExisting = img.classList.contains('existing-image');
                if (imgIsExisting) {
                    return {
                        isExisting: true,
                        id: img.getAttribute('data-id'),
                        index: null
                    };
                } else {
                    return {
                        isExisting: false,
                        id: null,
                        index: parseInt(img.getAttribute('data-index'))
                    };
                }
            });
            
            // Remove dragged item and reinsert at drop position
            const draggedItem = newOrder.splice(draggedPos, 1)[0];
            newOrder.splice(dropPos, 0, draggedItem);
            
            console.log('  → New unified order:', newOrder);
            
            // Call unified reorder method
            @this.call('reorderAllImages', newOrder).then(() => {
                console.log('✅ All images reordered successfully');
            }).catch(err => {
                console.error('❌ Error reordering:', err);
            });

            this.classList.remove('drag-over');
            return false;
        });
    });

    console.log('✅ Drag and drop initialized successfully');
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded - initializing drag and drop');
    setTimeout(initializeDragAndDrop, 500);
});

// Reinitialize after Livewire updates
if (typeof Livewire !== 'undefined') {
    Livewire.hook('message.processed', (message, component) => {
        console.log('Livewire message processed - reinitializing drag and drop');
        setTimeout(initializeDragAndDrop, 500);
    });
    
    // Also listen for element.updated event
    Livewire.hook('element.updated', (el, component) => {
        console.log('Livewire element updated - reinitializing drag and drop');
        setTimeout(initializeDragAndDrop, 500);
    });
    
    // Listen for component updated
    Livewire.hook('commit', ({ component, commit, respond }) => {
        console.log('Livewire commit - reinitializing drag and drop');
        setTimeout(initializeDragAndDrop, 500);
    });
}

// Reinitialize after file upload
document.addEventListener('livewire:load', function() {
    console.log('Livewire loaded - initializing drag and drop');
    setTimeout(initializeDragAndDrop, 500);
    
    Livewire.on('fileUploaded', function() {
        console.log('File uploaded event - reinitializing drag and drop');
        setTimeout(initializeDragAndDrop, 800);
    });
    
    // Listen for image removal
    Livewire.on('imageRemoved', function() {
        console.log('Image removed event - reinitializing drag and drop');
        setTimeout(initializeDragAndDrop, 500);
    });
});

// Also watch for DOM mutations as a fallback
const observer = new MutationObserver(function(mutations) {
    mutations.forEach(function(mutation) {
        if (mutation.addedNodes.length > 0) {
            mutation.addedNodes.forEach(function(node) {
                if (node.nodeType === 1 && (node.classList.contains('record') || node.querySelector('.record.image'))) {
                    console.log('New image detected via MutationObserver - reinitializing drag and drop');
                    setTimeout(initializeDragAndDrop, 600);
                }
            });
        }
    });
});

// Start observing the image container
setTimeout(function() {
    const container = document.getElementById('image-container');
    if (container) {
        observer.observe(container, { childList: true, subtree: true });
        console.log('MutationObserver started watching image-container');
    }
}, 1000);

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
    '994': '## ### ## ##',      // Azerbaijan
    '995': '### ## ## ##',      // Georgia
    '998': '## ### ## ##',      // Uzbekistan
    '996': '### ### ###',       // Kyrgyzstan
    '992': '## ### ####',       // Tajikistan
    '993': '## ######',         // Turkmenistan
    '374': '## ######',         // Armenia
    '375': '## ### ## ##',      // Belarus
    '977': '## ### ####',       // Nepal
    '880': '#### ######',       // Bangladesh
    '94': '## ### ####',        // Sri Lanka
    '98': '### ### ####',       // Iran
    '964': '### ### ####',      // Iraq
    '963': '## #### ###',       // Syria
    '962': '# #### ####',       // Jordan
    '970': '## ### ####',       // Palestine
    '972': '## ### ####',       // Israel
    '212': '### ######',        // Morocco
    '213': '### ## ## ##',      // Algeria
    '216': '## ### ###',        // Tunisia
    '218': '## ### ####',       // Libya
    '249': '## ### ####',       // Sudan
    '251': '## ### ####',       // Ethiopia
    '252': '# ### ####',        // Somalia
    '255': '### ### ###',       // Tanzania
    '256': '### ### ###',       // Uganda
    '260': '## ### ####',       // Zambia
    '263': '# ### ####',        // Zimbabwe
    '233': '## ### ####',       // Ghana
    '237': '### ## ## ##',      // Cameroon
    '225': '## ## ## ##',       // Ivory Coast
    '221': '## ### ## ##',      // Senegal
};

function applyPhoneMask(input, mask) {
    let handler = function(e) {
        // Get only digits
        let value = this.value.replace(/\D/g, '');
        
        // Remove leading zeros
        value = value.replace(/^0+/, '');
        
        let maskedValue = '';
        let valueIndex = 0;
        
        // Apply mask pattern
        for (let i = 0; i < mask.length && valueIndex < value.length; i++) {
            if (mask[i] === '#') {
                maskedValue += value[valueIndex];
                valueIndex++;
            } else {
                // Add separator (space or dash)
                if (valueIndex > 0) {
                    maskedValue += mask[i];
                }
            }
        }
        
        // Add remaining digits beyond the mask
        if (valueIndex < value.length) {
            maskedValue += ' ' + value.substring(valueIndex);
        }
        
        this.value = maskedValue;
        
        // Update Livewire model
        this.dispatchEvent(new Event('input', { bubbles: true }));
    };
    
    input.removeEventListener('input', input._phoneMaskHandler);
    input._phoneMaskHandler = handler;
    input.addEventListener('input', handler);
}

function updatePhoneMask(countryCode, phoneInputId) {
    const phoneInput = document.getElementById(phoneInputId);
    if (!phoneInput) return;
    
    // Define example phone numbers for each country
    const exampleNumbers = {
        '971': '50 123 4567',      // UAE
        '92': '300 1234567',        // Pakistan
        '1': '202 555 0123',        // USA/Canada
        '44': '7700 900123',        // UK
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
        '994': '50 123 45 67',      // Azerbaijan
        '995': '555 12 34 56',      // Georgia
        '998': '90 123 45 67',      // Uzbekistan
        '996': '700 123 456',       // Kyrgyzstan
        '992': '91 123 4567',       // Tajikistan
        '993': '65 123456',         // Turkmenistan
        '374': '91 123456',         // Armenia
        '375': '29 123 45 67',      // Belarus
        '977': '98 123 4567',       // Nepal
        '880': '1712 345678',       // Bangladesh
        '94': '71 234 5678',        // Sri Lanka
        '98': '912 345 6789',       // Iran
        '964': '770 123 4567',      // Iraq
        '963': '94 1234 567',       // Syria
        '970': '59 123 4567',       // Palestine
        '972': '50 123 4567',       // Israel
        '212': '612 345678',        // Morocco
        '213': '551 23 45 67',      // Algeria
        '216': '20 123 456',        // Tunisia
        '218': '91 234 5678',       // Libya
        '249': '91 123 4567',       // Sudan
        '251': '91 123 4567',       // Ethiopia
        '252': '7 123 4567',        // Somalia
        '255': '712 345 678',       // Tanzania
        '256': '712 345 678',       // Uganda
        '260': '95 123 4567',       // Zambia
        '263': '7 123 4567',        // Zimbabwe
        '233': '24 123 4567',       // Ghana
        '237': '670 12 34 56',      // Cameroon
        '225': '01 23 45 67',       // Ivory Coast
        '221': '77 123 45 67',      // Senegal
    };
    
    // Apply mask if exists for country code
    if (countryCode && phoneMasks[countryCode]) {
        applyPhoneMask(phoneInput, phoneMasks[countryCode]);
        
        // Get example number or fallback to mask pattern
        let placeholder = exampleNumbers[countryCode];
        if (!placeholder || placeholder === 'undefined' || placeholder === undefined) {
            placeholder = phoneMasks[countryCode].replace(/#/g, '0');
        }
        
        // Store placeholder in data attribute to protect it
        phoneInput.dataset.customPlaceholder = placeholder;
        
        // Disconnect old observer if exists
        if (phoneInput._placeholderObserver) {
            phoneInput._placeholderObserver.disconnect();
        }
        
        // Create aggressive MutationObserver to protect placeholder
        phoneInput._placeholderObserver = new MutationObserver(function(mutations) {
            const customPlaceholder = phoneInput.dataset.customPlaceholder;
            if (customPlaceholder && phoneInput.getAttribute('placeholder') !== customPlaceholder) {
                phoneInput.setAttribute('placeholder', customPlaceholder);
            }
        });
        
        phoneInput._placeholderObserver.observe(phoneInput, {
            attributes: true,
            attributeFilter: ['placeholder']
        });
        
        // Set placeholder and force it to stay with interval check
        if (placeholder) {
            phoneInput.setAttribute('placeholder', placeholder);
        }
        
        // Additional protection with interval check (first 3 seconds)
        if (phoneInput._placeholderInterval) {
            clearInterval(phoneInput._placeholderInterval);
        }
        
        let intervalCount = 0;
        phoneInput._placeholderInterval = setInterval(() => {
            const currentPlaceholder = phoneInput.getAttribute('placeholder');
            const correctPlaceholder = phoneInput.dataset.customPlaceholder;
            
            if (correctPlaceholder && currentPlaceholder !== correctPlaceholder) {
                phoneInput.setAttribute('placeholder', correctPlaceholder);
            }
            
            intervalCount++;
            if (intervalCount >= 30) { // Stop after 3 seconds (30 * 100ms)
                clearInterval(phoneInput._placeholderInterval);
            }
        }, 100);
        
    } else {
        // Remove mask handler if no mask for this country
        if (phoneInput._phoneMaskHandler) {
            phoneInput.removeEventListener('input', phoneInput._phoneMaskHandler);
            phoneInput._phoneMaskHandler = null;
        }
        phoneInput.setAttribute('placeholder', 'Enter phone number');
        phoneInput.dataset.customPlaceholder = 'Enter phone number';
    }
}

// Listen for country code changes on first phone
$(document).on('change', '#first_phone_code', function() {
    const countryCode = $(this).val();
    const phoneInput = document.getElementById('listing_phone_numbers_attributes_0_phone_digits');
    
    if (!phoneInput) return;
    
    // Clear the phone number field
    phoneInput.value = '';
    
    // Update mask and placeholder
    updatePhoneMask(countryCode, 'listing_phone_numbers_attributes_0_phone_digits');
});

// Sync phone input with Livewire on input (debounced)
let phoneDebounce;
$(document).on('input', '#listing_phone_numbers_attributes_0_phone_digits', function() {
    clearTimeout(phoneDebounce);
    const value = this.value;
    phoneDebounce = setTimeout(() => {
        @this.set('phone', value);
    }, 500);
});

// Listen for country code changes on second phone
$(document).on('change', '#second_phone_code', function() {
    const countryCode = $(this).val();
    const phoneInput = document.getElementById('listing_phone_numbers_attributes_1_phone_digits');
    
    if (!phoneInput) return;
    
    // Clear the phone number field
    phoneInput.value = '';
    
    // Update mask and placeholder
    updatePhoneMask(countryCode, 'listing_phone_numbers_attributes_1_phone_digits');
});

// Sync second phone input with Livewire on input (debounced)
let phone2Debounce;
$(document).on('input', '#listing_phone_numbers_attributes_1_phone_digits', function() {
    clearTimeout(phone2Debounce);
    const value = this.value;
    phone2Debounce = setTimeout(() => {
        @this.set('phone2', value);
    }, 500);
});

// Initialize mask on page load if country code already selected
$(document).ready(function() {
    const firstCountryCode = $('#first_phone_code').val();
    if (firstCountryCode) {
        updatePhoneMask(firstCountryCode, 'listing_phone_numbers_attributes_0_phone_digits');
    }
    
    const secondCountryCode = $('#second_phone_code').val();
    if (secondCountryCode) {
        updatePhoneMask(secondCountryCode, 'listing_phone_numbers_attributes_1_phone_digits');
    }
});

            </script>
          @endpush