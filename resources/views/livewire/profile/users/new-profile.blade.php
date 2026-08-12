@section('headerform')
<div class="nav-bar navbar-top-nav" style="background:#111213;padding: 5px 0px;">
    <div class="container-fluid" style="text-align:center;">
      <div class="title">
        <h1 style="color:#fff;font-size:16px;font-weight:500;margin-top:6px;">Add your profile</h1>
      </div>
    </div>
  </div>
@endsection

{{-- Single root element required by Livewire 3. Without it, Livewire picks
     the first child (the <style> tag) as the component root and every
     wire:* directive on the form ends up outside the tracked DOM, which
     breaks wire:model on the file input (silent uploads). --}}
<div>
<style>
        /* ===== EVOORY DARK THEME FOR NEW PROFILE ===== */
        /* Reserve scrollbar gutter so the page never shifts horizontally
           when a Livewire commit changes the height of the document
           (which briefly toggled the vertical scrollbar on the first
           commit after page load and made everything — dropdowns and
           text alike — "shake" left/right). */
        html {
            scrollbar-gutter: stable !important;
            overflow-y: scroll !important;
        }
        body {
            overflow-x: hidden !important;
        }
        body, .content-wrapper, #content {
            background-color: #0a0b0d !important;
        }
        .row.container, .col-lg-offset-1.col-lg-10 {
            background-color: #0a0b0d !important;
        }

        /* Align the form wrapper with the evoory header and footer
           (max-width 1300px, centered, 16px side padding). The outer
           .row.container combines Bootstrap's .row and .container on the
           same element — .row's `margin: 0 -15px` is defined after
           .container, killing the auto-centering — so we re-apply it
           explicitly. The legacy `col-lg-offset-1` class is a Bootstrap 3
           selector and has no effect under Bootstrap 4, so we zero out any
           inherited offset on the inner column. */
        @media (min-width: 992px) {
            .row.container {
                margin-left: auto !important;
                margin-right: auto !important;
                max-width: 1300px !important;
                padding-left: 16px !important;
                padding-right: 16px !important;
                box-sizing: border-box !important;
            }
            .row.container > .col-lg-offset-1.col-lg-10 {
                margin-left: 0 !important;
                padding-left: 0 !important;
                padding-right: 0 !important;
            }
        }

        /* Section title blocks with left accent border */
        .h3.title-block, h2.h3.title-block {
            color: #fff !important;
            font-size: 18px !important;
            font-weight: 600 !important;
            background: #1a1b1e;
            
            padding: 12px 18px !important;
            margin: 30px 0 20px !important;
            border-radius: 0 6px 6px 0;
        }

        /* Labels */
        label, .control-label, .label-block {
            color: #ccc !important;
            font-weight: 400 !important;
        }
        .required-star {
            color: #ff4444 !important;
        }

        /* Big one line row - full width to match textarea/upload sections */
        #basic { width: 100% !important; }
        form.listing #basic .big-one-line,
        form.listing #basic .big-one-line.left,
        form.listing .big-one-line,
        form.listing .big-one-line.left {
            width: 100% !important;
            max-width: 100% !important;
            display: flex !important;
            flex-wrap: wrap !important;
            gap: 12px !important;
            align-items: flex-start !important;
            margin-right: 0 !important;
            float: none !important;
            box-sizing: border-box !important;
        }
        form.listing .big-one-line .form-group {
            flex: 1 1 0 !important;
            min-width: 200px !important;
            margin-right: 0 !important;
        }
        form.listing .big-one-line .form-group.listing_name { flex: 3 1 0 !important; max-width: none !important; }
        form.listing .big-one-line .form-group.listing_listed_as_id { flex: 0 0 200px !important; max-width: 200px !important; }
        @media (max-width: 768px) {
            form.listing .big-one-line .form-group.listing_listed_as_id {
                flex: 1 1 100% !important;
                max-width: 100% !important;
                width: 100% !important;
            }
            form.listing .big-one-line .listinga { width: 100% !important; }
        }
        form.listing .big-one-line .form-group.listing_city_url { flex: 3 1 0 !important; max-width: none !important; }
        form.listing .big-one-line input#listing_name,
        form.listing .big-one-line .listinga,
        form.listing .big-one-line .typeahead-city-wrapper input {
            width: 100% !important;
        }
        form.listing .big-one-line .form-group.listing_city_url label.city {
            display: none !important;
        }

        /* About me textarea - force dark theme + full width */
        #basic .form-group.listing_description,
        .form-group.listing_description {
            width: 100% !important;
            max-width: 100% !important;
            display: block !important;
            float: none !important;
            clear: both !important;
            margin-left: 0 !important;
            margin-right: 0 !important;
        }
        textarea#listing_description {
            background-color: #000 !important;
            border: 1px solid #2e3033 !important;
            color: #fff !important;
            border-radius: 6px !important;
            width: 100% !important;
            max-width: 100% !important;
            min-height: 180px !important;
            padding: 14px 16px !important;
            display: block !important;
            box-sizing: border-box !important;
        }
        textarea#listing_description:focus {
            border-color: #c8ff00 !important;
            box-shadow: 0 0 0 2px rgba(200,255,0,0.1) !important;
            outline: none !important;
        }

        /* Text inputs & textareas */
        .form-control,
        input[type="text"].form-control,
        input[type="email"].form-control,
        input[type="number"].form-control,
        input[type="tel"].form-control,
        textarea.form-control,
        select.form-control {
            background-color: #1a1b1e !important;
            border: 1px solid #2e3033 !important;
            color: #fff !important;
            border-radius: 4px !important;
        }
        .form-control:focus {
            border-color: #c8ff00 !important;
            box-shadow: 0 0 0 2px rgba(200,255,0,0.1) !important;
            outline: none !important;
        }
        .form-control::placeholder {
            color: #666 !important;
        }

        /* Big-one-line overrides (override app2.css border:0 + border-radius:0) */
        form.listing .big-one-line input#listing_name,
        form.listing .big-one-line .typeahead-city-wrapper input,
        form.listing .big-one-line .listinga {
            background: #1a1b1e !important;
            color: #fff !important;
            border: 1px solid #2e3033 !important;
            border-bottom: 3px dashed #A6B4B8 !important;
            border-radius: 6px !important;
            height: 48px !important;
            font-size: 16px !important;
            padding: 10px 14px !important;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
        }
        form.listing .big-one-line .listinga {
            padding-right: 32px !important;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath fill='%23888' d='M6 8L0 0h12z'/%3E%3C/svg%3E") !important;
            background-repeat: no-repeat !important;
            background-position: right 12px center !important;
        }
        form.listing .big-one-line .typeahead-city-wrapper input {
            padding-left: 37px !important;
        }
        form.listing .big-one-line .listing_city_url label.city {
            line-height: 48px !important;
            color: #fff !important;
        }

        /* City search input */
        input#citysearch {
            background-color: #1a1b1e !important;
            border: 1px solid #2e3033 !important;
            color: #fff !important;
            border-bottom: 3px dashed #A6B4B8 !important;
        }

        /* City dropdown */
        .citys {
            background: #1a1b1e !important;
            border: 1px solid #2e3033 !important;
        }
        .opt.optc-item:hover, .opt.optc-item.highlighted {
            background-color: #2e3033 !important;
        }

        /* Custom Select2 - Dark Theme */
        .custom-select2-selection {
            background: #1a1b1e !important;
            border: 1px solid #2e3033 !important;
            color: #fff !important;
        }
        .custom-select2-selection:hover {
            border-color: #444 !important;
        }
        .custom-select2-selection:focus,
        .custom-select2-selection.open {
            border-color: #c8ff00 !important;
            box-shadow: 0 0 0 2px rgba(200,255,0,0.1) !important;
        }
        .custom-select2-selection::after {
            color: #888 !important;
        }
        .custom-select2-dropdown {
            background: #1a1b1e !important;
            border: 1px solid #2e3033 !important;
            box-shadow: 0 6px 20px rgba(0,0,0,0.5) !important;
        }
        .custom-select2-search {
            border-bottom-color: #2e3033 !important;
            background: #1a1b1e !important;
        }
        .custom-select2-search input {
            background-color: #111 !important;
            border-color: #2e3033 !important;
            color: #fff !important;
        }
        .custom-select2-search input:focus {
            border-color: #c8ff00 !important;
            box-shadow: 0 0 0 2px rgba(200,255,0,0.1) !important;
        }
        .custom-select2-option {
            color: #ccc !important;
            border-bottom-color: #222 !important;
        }
        .custom-select2-option:hover,
        .custom-select2-option:active {
            background: #2e3033 !important;
            color: #fff !important;
        }
        .custom-select2-option.selected {
            background: #c8ff00 !important;
            color: #000 !important;
        }
        .custom-select2-option.selected:hover {
            background: #b5e600 !important;
        }
        .custom-select2-placeholder {
            color: #666 !important;
        }
        .custom-select2-results::-webkit-scrollbar-track {
            background: #111 !important;
        }
        .custom-select2-results::-webkit-scrollbar-thumb {
            background: #444 !important;
        }
        /* Flag-enabled dropdown options (country code) */
        .custom-select2-option--with-flag {
            display: flex !important;
            align-items: center !important;
            gap: 10px !important;
            padding: 10px 14px !important;
        }
        .custom-select2-flag {
            display: inline-block !important;
            width: 22px !important;
            height: 16px !important;
            border-radius: 2px !important;
            object-fit: cover !important;
            flex-shrink: 0 !important;
            transform: none !important;
            -webkit-transform: none !important;
            margin: 0 !important;
        }
        .custom-select2-option-name {
            flex: 1 !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
        }
        .custom-select2-option-dial {
            color: #8b9298 !important;
            font-variant-numeric: tabular-nums !important;
            margin-left: auto !important;
        }
        /* Closed-state: flag sits left of the dial code inside the selection box. */
        .custom-select2-selection .custom-select2-flag {
            margin-right: 6px !important;
        }
        .custom-select2-selection-code {
            font-weight: 500;
        }

        /* Price input */
        .price-amount {
            background-color: #1a1b1e !important;
            border: 1px solid #2e3033 !important;
            color: #fff !important;
        }

        /* Input group addon */
        .input-group-addon {
            background-color: #1a1b1e !important;
            border: 1px solid #2e3033 !important;
            color: #999 !important;
        }

        /* Upload area */
        .drag-drop {
            border: 3px dashed #2e3033 !important;
            background: #111213 !important;
            border-radius: 8px !important;
        }
        .drag-drop.dragover {
            border-color: #c8ff00 !important;
            background: rgba(200,255,0,0.03) !important;
        }
        .drag-drop-text-main {
            color: #fff !important;
        }
        .drag-drop-text {
            color: #888 !important;
        }
        .drag-drop .icon-image {
            background-color: transparent !important;
            box-shadow: none !important;
            color: #888 !important;
        }
        .drag-drop .btn-primary,
        .drag-drop button.btn-primary {
            background: #c8ff00 !important;
            color: #000 !important;
            border: none !important;
            border-radius: 50px !important;
            font-weight: 500 !important;
            padding: 5px 30px !important;
            font-size: 16px !important;
        }

        /* Radio buttons & checkboxes */
        input[type="radio"],
        input[type="checkbox"] {
            accent-color: #c8ff00;
        }
        .radio-inline label, .checkbox label {
            color: #ccc !important;
        }

        /* Buttons */
        .btn-dark, .add-language-btn, .add-second-phone {
            background: #1a1b1e !important;
            border: 1px solid #2e3033 !important;
            color: #ccc !important;
        }
        .btn-dark:hover, .add-language-btn:hover, .add-second-phone:hover {
            border-color: #c8ff00 !important;
            color: #fff !important;
        }
        .btn-primary.btn-lg#submit {
            background: #c8ff00 !important;
    color: #000 !important;
    border: none !important;
    border-radius: 50px !important;
    font-weight: 500 !important;
    padding: 7px 40px !important;
    font-size: 16px !important;
        }
        .btn-primary.btn-lg#submit:hover {
            background: #b5e600 !important;
        }

        /* Hints and small text */
        .hint, .hint a, .small, p.text-right.small a {
            color: #888 !important;
        }
        p.text-right.small a {
            color: #c8ff00 !important;
        }
        .char-count-container span#char-count {
            color: #888 !important;
        }

        /* Text colors */
        p, .ad-images p, .multi-image-uploader1 p {
            color: #ffffff !important;
        }
        a {
            color: #c8ff00;
        }
        a:hover {
            color: #b5e600;
        }

        /* HR line */
        hr {
            border-color: #2e3033 !important;
        }

        /* Alert messages */
        .alert-danger {
            background: #2a1a1a !important;
            border-color: #4a2020 !important;
            color: #ff6b6b !important;
        }
        .alert-warning {
            background: rgba(200,255,0,0.05) !important;
            border-color: rgba(200,255,0,0.2) !important;
            color: #c8ff00 !important;
        }

        /* Image records */
        .record.image {
            position: relative !important;
            width: 140px !important;
            height: 140px !important;
            min-height: 140px !important;
            max-height: 140px !important;
            padding: 0 !important;
            margin: 0 !important;
            background: #1a1b1e !important;
            border: 1px solid #2e3033 !important;
            border-radius: 6px !important;
            overflow: hidden !important;
            flex-shrink: 0 !important;
            box-sizing: border-box !important;
        }
        .record.image img {
            width: 100% !important;
            height: 100% !important;
            min-width: 100% !important;
            min-height: 100% !important;
            max-width: none !important;
            max-height: 100% !important;
            object-fit: cover !important;
            object-position: center !important;
            display: block !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        .record.image .delete {
            position: absolute;
            top: 5px;
            right: 5px;
            cursor: pointer;
            background: rgba(255, 0, 0, 0.85);
            color: #fff;
            padding: 4px 7px;
            border-radius: 50%;
            font-size: 11px;
            line-height: 1;
            z-index: 10;
        }
        .record.image .delete:hover {
            background: rgba(255, 0, 0, 1);
        }
        .record.image .img-footer {
            position: absolute;
            left: 0 !important;
            right: 0;
            bottom: 0 !important;
            width: 100% !important;
            padding: 4px 6px;
            background: rgba(0, 0, 0, 0.65);
            text-align: center;
        }
        .record.image .img-footer .badge-success {
            font-size: 10px;
            padding: 2px 6px;
        }
        .record.image .img-footer .text-muted.small {
            font-size: 9px;
            color: #ddd !important;
        }
        .record.image .img-pending {
            position: absolute;
            top: 5px;
            left: 5px;
            background: rgba(0, 0, 0, 0.6);
            border-radius: 4px;
            padding: 2px 4px;
        }
        .record.image:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.4) !important;
        }
        .record.image.drag-over {
            border-color: #c8ff00 !important;
            background-color: rgba(200,255,0,0.05) !important;
        }

        /* Services section checkbox styling */
        .overflow-list-xs label {
            color: #ccc !important;
        }

        /* Language remove icon */
        .rm-lang-field {
            color: #888 !important;
            cursor: pointer;
        }
        .rm-lang-field:hover {
            color: #ff4444 !important;
        }

        /* OnlyFans label */
        label.new {
            color: #ccc !important;
        }
        label.new::after {
            color: #ff4444 !important;
        }

        /* Nav bar styling */
        .nav-bar.navbar-top-nav {
            background: #1f2222 !important;
        }
        .nav-bar.navbar-top-nav .title h1 {
            color: #fff !important;
        }

        /* X/Twitter icon */
        #social svg {
            color: #fff !important;
        }

        /* Validation errors */
        .validation-error {
            color: #ff4444 !important;
        }

        /* Image Drag and Drop Styles */
        .record.image {
            cursor: move;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .record.image:hover {
            transform: scale(1.02);
        }
        
        .record.image[draggable="true"] {
            cursor: grab;
        }
        
        .record.image[draggable="true"]:active {
            cursor: grabbing;
        }
        
        .badge-success {
            background-color: #c8ff00;
            color: #000;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
        }

        .btn-set-main {
            background: transparent;
            color: #c8ff00;
            border: 1px solid #c8ff00;
            padding: 3px 10px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
            cursor: pointer;
        }
        .btn-set-main:hover {
            background: #c8ff00;
            color: #000;
        }
        
        #image-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        
        /* Custom Select2 Styles */
        .custom-select2 {
            position: relative;
            width: 100%;
            z-index: 100;
        }
        
        .custom-select2.open {
            z-index: 10000;
        }
        
        .custom-select2-selection {
            position: relative;
            background: #1a1b1e;
            border: 1px solid #2e3033;
            padding: 0px 24px 0px 8px;
            cursor: pointer;
            min-height: 34px;
            height: 34px;
            display: flex;
            align-items: center;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
            font-size: 12px;
            color: #fff;
            border-radius: 5px;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
        }
        .custom-select2-selection > * {
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
            min-width: 0;
        }
        
        .custom-select2-selection:hover {
            border-color: #444;
        }
        
        .custom-select2-selection:focus,
        .custom-select2-selection.open {
            border-color: #c8ff00;
            box-shadow: 0 0 0 2px rgba(200,255,0,0.1);
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
            background: #1a1b1e;
            border: 1px solid #2e3033;
            border-radius: 4px;
            max-height: 320px;
            overflow: hidden;
            z-index: 99999;
            display: none;
            box-shadow: 0 6px 20px rgba(0,0,0,0.5);
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
            border-bottom: 1px solid #2e3033;
            background: #1a1b1e;
            flex-shrink: 0;
        }
        
        .custom-select2-search input {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #2e3033;
            border-radius: 4px;
            font-size: 14px;
            color: #fff;
            background-color: #111;
            transition: border-color 0.2s ease;
            position: relative;
            z-index: 11;
        }
        
        .custom-select2-search input:focus {
            outline: none;
            border-color: #c8ff00;
            box-shadow: 0 0 0 2px rgba(200,255,0,0.1);
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
            color: #ccc;
            transition: background-color 0.15s ease;
            border-bottom: 1px solid #222;
            pointer-events: auto;
            user-select: none;
            -webkit-tap-highlight-color: transparent;
        }
        
        .custom-select2-option:last-child {
            border-bottom: none;
        }
        
        .custom-select2-option:hover,
        .custom-select2-option:active {
            background: #2e3033;
            color: #fff;
        }
        
        .custom-select2-option.selected {
            background: #c8ff00;
            color: #000;
            font-weight: 500;
        }
        
        .custom-select2-option.selected:hover {
            background: #b5e600;
        }
        
        .custom-select2-option.hidden {
            /* !important is required because .custom-select2-option--with-flag
               sets display:flex !important, which would otherwise win and
               leave filtered-out options visible during search. */
            display: none !important;
        }
        
        .custom-select2-placeholder {
            color: #666;
        }
        
        /* Required field asterisk */
        .required-star {
            color: #ff4444;
            margin-right: 3px;
            font-weight: bold;
        }
        
        /* Scrollbar styling for dropdown results */
        .custom-select2-results::-webkit-scrollbar {
            width: 8px;
        }
        
        .custom-select2-results::-webkit-scrollbar-track {
            background: #111;
            border-radius: 4px;
        }
        
        .custom-select2-results::-webkit-scrollbar-thumb {
            background: #444;
            border-radius: 4px;
        }
        
        .custom-select2-results::-webkit-scrollbar-thumb:hover {
            background: #666;
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

        /* Price control layout fix */
        .price-control {
            display: flex;
            align-items: stretch;
            gap: 0;
            position: relative;
        }

        .price-control .price-amount {
            flex: 1;
            border-top-right-radius: 0 !important;
            border-bottom-right-radius: 0 !important;
            border-right: none;
            z-index: 1;
        }

        .price-control .custom-select2 {
            width: 120px !important;
            flex-shrink: 0;
            z-index: 2;
        }
        
        .price-control .custom-select2.open {
            z-index: 10001 !important;
        }

        .price-control .custom-select2 .custom-select2-selection {
            border-top-left-radius: 0 !important;
            border-bottom-left-radius: 0 !important;
        }

        /* Ensure dropdown appears above other elements */
        .price-control .custom-select2 .custom-select2-dropdown.open {
            z-index: 10002 !important;
        }

       .citys {
            width: 25%;
            min-width: 200px;
            max-height: 250px;
            position: absolute;
            background: #1a1b1e;
            border: 1px solid #2e3033;
            overflow-y: auto;
            overflow-x: hidden;
            display: none;
            z-index: 9999;
            border-radius: 4px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.5);
            -webkit-overflow-scrolling: touch;
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

.typeahead-city-wrapper {
    position: relative;
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
    background: #1a1b1e;
    color: #fff;
    border: 1px solid #2e3033;
    border-radius: 4px;
    height: 42px;
    font-size: 16px;
    border-bottom: 2px dashed #555;
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    padding-right: 32px;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath fill='%23888' d='M6 8L0 0h12z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 12px center;
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

/* Legacy .img-footer + form.listing .image padding rules from app2.css would
   push the footer label off-screen and add 20px gutters inside each preview
   card. Reset both — the per-card layout is fully handled by .record.image
   rules above. */
form.listing .image,
form.listing .record.image {
    padding: 0 !important;
}

.wrappper{
  float: left; width: 27%;
}

.insidewrapper {
  width: 40%;
  margin-right:5px;
}

/* Modern Upload Area Styles */
.modern-upload-label {
    cursor: pointer;
    width: 100%;
    display: block;
}

.drag-drop {
    border: 3px dashed #2e3033;
    border-radius: 8px;
    padding: 40px 20px;
    background: #111213;
    transition: all 0.3s ease;
    position: relative;
}



.drag-drop .icon-image {
    color: #888;
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background-color: transparent;
    box-shadow: none;
    -webkit-transition: all .3s;
    transition: all .3s;
    padding: 20px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 20px;
}



.drag-drop-text-main {
    font-size: 25px !important;
    color: #fff !important;
    font-weight: 400 !important;
}

.drag-drop-text {
    color: #ffffff !important;
    font-size: 14px !important;
}

.drag-drop .btn-warning {
    background: #c8ff00;
    color: #000;
    border: none;
    transition: all 0.3s ease;
    font-weight: 600;
    border-radius: 50px;
}

.drag-drop .btn-warning:hover {
    background: #b5e600;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(200,255,0,0.3);
}

.drag-drop .btn-warning .fa-upload {
    margin-right: 8px;
}

.upload-disabled {
    display: none;
}

/* Dragover effect */
.drag-drop.dragover {
    border-color: #c8ff00;
    background: rgba(200,255,0,0.03);
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

.citys
 {
    width: 50%;
 }

}
div#basic {
     margin-top: 0px; 
}

.btn ,.btn-dark, #add-phone {box-shadow: none !important;}

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

/* ═══════════════════════════════════════════
   MOBILE CREATE PROFILE VIEW
   ═══════════════════════════════════════════ */
@media (max-width: 768px) {
    /* Prevent horizontal overflow */
    body, html {
        overflow-x: hidden !important;
        max-width: 100vw !important;
    }
    /* Universal box-sizing fix */
    *, *::before, *::after {
        box-sizing: border-box !important;
    }
    /* Kill all flex on root container */
    div.row.container {
        display: block !important;
    }
    .wrapper, form#new_listing {
        display: block !important;
        width: 100% !important;
        box-sizing: border-box !important;
    }
    /* Fix all rows and containers to not overflow */
    .row, .container, .container-fluid {
        margin-left: 0 !important;
        margin-right: 0 !important;
        padding-left: 16px !important;
        padding-right: 16px !important;
        max-width: 100% !important;
        box-sizing: border-box !important;
    }
    /* Inner column already has padding, so reset parent row padding */
    div.row.container {
        padding-left: 0 !important;
        padding-right: 0 !important;
    }
    .big-one-line, .big-one-line.left {
        margin: 0 !important;
        padding: 0 !important;
        width: 100% !important;
        box-sizing: border-box !important;
    }
    /* Form wrapper */
    form#new_listing {
        padding: 0 !important;
    }
    .wrapper {
        padding: 0 !important;
    }
    /* Reset column offsets and margins */
    [class*="col-lg-offset"],
    [class*="col-md-offset"] {
        margin-left: 0 !important;
    }
    /* Ensure inputs don't overflow */
    input, select, textarea {
        max-width: 100% !important;
        box-sizing: border-box !important;
    }
    /* Hide desktop header, show mobile header */
    .nav-bar.navbar-top-nav {
        display: none !important;
    }

    /* Page container - force single column, override Bootstrap .row flex */
    div.row.container,
    .row.container {
        display: block !important;
        flex-direction: column !important;
        flex-wrap: nowrap !important;
        padding: 0 !important;
        margin: 0 auto !important;
        width: 100% !important;
        max-width: 100vw !important;
        overflow-x: hidden !important;
    }
    div.row.container::before,
    div.row.container::after,
    .row.container::before,
    .row.container::after {
        display: none !important;
    }
    .col-lg-offset-1,
    .col-lg-10,
    .col-lg-offset-1.col-lg-10,
    div.col-lg-offset-1.col-lg-10 {
        display: block !important;
        padding: 0 16px !important;
        width: 100% !important;
        max-width: 100vw !important;
        margin: 0 !important;
        margin-left: 0 !important;
        float: none !important;
        left: 0 !important;
        position: relative !important;
        box-sizing: border-box !important;
    }
    /* Hide desktop header/nav on mobile */
    #header, .navbar-top-nav, .nav-bar.navbar-top-nav {
        display: none !important;
    }

    /* Remove ::before pseudo from row container */

    /* Section title blocks - card style */
    .h3.title-block, h2.h3.title-block {
        background: #111 !important;
        border: 1px solid #222 !important;
        border-radius: 5px !important;
        padding: 14px 16px !important;
        margin: 24px 0 16px !important;
        font-size: 16px !important;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    /* Basic Information: wrap whole section as one card; heading becomes
       an internal header instead of a stand-alone card. Scoped to #basic
       so other sections (Upload photos, About me, etc.) keep their own
       card-per-heading look. */
    #basic {
        background: #111 !important;
        border: 1px solid #222 !important;
        border-radius: 10px !important;
        padding: 18px !important;
        margin: 24px 0 16px !important;
    }
    #basic .h3.title-block,
    #basic h2.h3.title-block {
        background: transparent !important;
        border: none !important;
        border-radius: 0 !important;
        padding: 0 !important;
        margin: 0 0 18px !important;
        font-size: 17px !important;
        gap: 12px !important;
    }
    #basic .h3.title-block svg {
        background: #283813;
        border: 1px solid #3a4a2a;
        border-radius: 8px;
        padding: 7px;
        width: 36px !important;
        height: 36px !important;
        box-sizing: border-box;
        flex-shrink: 0;
    }
    /* City input keeps a dashed bottom border via #citysearch id selector
       (specificity beats the .big-one-line class override above) */
    input#citysearch {
        border: 1px solid #333 !important;
        border-bottom: 1px solid #333 !important;
    }

    /* Photos: wrap whole section as one card; replace long desktop header
       and disclaimer with a simple icon header + minimal hint text. */
    #photos {
        background: #111 !important;
        border: 1px solid #222 !important;
        border-radius: 10px !important;
        padding: 18px !important;
        margin: 24px 0 16px !important;
    }
    #photos .ev-photos-desktop-title,
    #photos .ev-photos-disclaimer {
        display: none !important;
    }
    #photos .ev-photos-mobile-title {
        background: transparent !important;
        border: none !important;
        border-radius: 0 !important;
        padding: 0 !important;
        margin: 0 0 16px !important;
        font-size: 17px !important;
        gap: 12px !important;
    }
    #photos .ev-photos-mobile-title svg {
        background: #283813;
        border: 1px solid #3a4a2a;
        border-radius: 8px;
        padding: 7px;
        width: 36px !important;
        height: 36px !important;
        box-sizing: border-box;
        flex-shrink: 0;
    }
    /* Dotted border via SVG background — gives full control over dot-to-gap
       spacing that CSS `border-style: dotted` can't express (it locks the
       gap to the dot diameter). stroke-dasharray="2,10" + linecap=round
       paints 2px round dots with 10px gaps. */
    #photos .drag-drop {
        border: none !important;
        border-radius: 10px !important;
        padding: 28px 18px !important;
        background-color: transparent !important;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100%25' height='100%25'%3E%3Crect width='100%25' height='100%25' fill='none' stroke='%23727b7e' stroke-width='2' stroke-dasharray='2%2C 10' stroke-linecap='round' rx='10' ry='10'/%3E%3C/svg%3E") !important;
        background-repeat: no-repeat !important;
    }
    #photos .drag-drop svg {
        width: 65px !important;
        height: 65px !important;
        margin-bottom: 14px !important;
        stroke: #727B7E !important;
    }
    /* Hide desktop-only text on mobile */
    #photos .ev-photos-desktop-only {
        display: none !important;
    }
    /* Show the simplified mobile hint */
    #photos .ev-photos-mobile-hint {
        display: block !important;
        color: #888 !important;
        font-size: 12px !important;
        text-align: center;
        margin: 14px 0 0 !important;
        line-height: 1.5 !important;
    }
    /* Outlined "Choose file" button to match reference */
    #photos .ev-photos-choose-btn {
        background: transparent !important;
        color: #fff !important;
        border: 1px solid #364153 !important;
        border-radius: 8px !important;
        font-weight: 500 !important;
        padding: 7px 50px !important;
        font-size: 14px !important;
        margin: 4px 0 !important;
    }

    /* Form inputs - consistent dark style */
    .form-control,
    input.form-control,
    textarea.form-control,
    select.form-control {
        background: #111 !important;
        border: 1px solid #333 !important;
        border-radius: 5px !important;
        color: #fff !important;
        font-size: 14px !important;
        padding: 10px 12px !important;
        height: 44px !important;
        box-sizing: border-box !important;
        width: 100% !important;
    }
    textarea.form-control {
        min-height: 140px !important;
        height: auto !important;
    }

    /* Big one line inputs - override dashed borders and icons */
    form.listing .big-one-line input#listing_name,
    form.listing .big-one-line .typeahead-city-wrapper input,
    form.listing .big-one-line .listinga {
        border: 1px solid #333 !important;
        border-bottom: 1px solid #333 !important;
        border-radius: 5px !important;
        height: 44px !important;
        font-size: 14px !important;
        background: #111 !important;
        background-image: none !important;
        padding: 10px 12px !important;
    }
    /* Restore dropdown arrow on the category select — the rule above wipes
       background-image for all big-one-line inputs, but the <select> still
       needs a visible chevron so users know it's a dropdown. */
    form.listing .big-one-line .listinga {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath fill='%23aaa' d='M6 8L0 0h12z'/%3E%3C/svg%3E") !important;
        background-repeat: no-repeat !important;
        background-position: right 12px center !important;
        padding-right: 32px !important;
    }
    /* Remove map pin icon from city input */
    form.listing .big-one-line .typeahead-city-wrapper input {
        padding-left: 12px !important;
    }
    .typeahead-city-wrapper::before {
        display: none !important;
    }
    /* Show "City" label on mobile (desktop rule hides it) and restyle */
    form.listing .big-one-line .form-group.listing_city_url label.city,
    form.listing .big-one-line .listing_city_url label.city,
    .ev-city-label {
        display: block !important;
        font-size: 13px !important;
        color: #ccc !important;
        line-height: normal !important;
        margin-bottom: 6px !important;
    }
    .city-hint,
    .hint.city-hint {
        display: none !important;
    }
    /* Category select on mobile — chevron restored via the earlier rule
       ("Restore dropdown arrow on the category select"). Just kill the
       native browser arrow so we don't get two indicators stacked. */
    form.listing .big-one-line .listinga {
        -webkit-appearance: none !important;
        appearance: none !important;
    }

    form.listing .big-one-line,
    form.listing .big-one-line.left,
    .big-one-line,
    .big-one-line.left {
        display: block !important;
        float: none !important;
        width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    form.listing .big-one-line .form-group,
    .big-one-line .form-group {
        display: block !important;
        width: 100% !important;
        margin-bottom: 16px !important;
        float: none !important;
        padding: 0 !important;
        margin-left: 0 !important;
    }
    /* Ensure big-one-line inputs match other inputs width */
    form.listing .big-one-line .form-group input,
    form.listing .big-one-line .form-group select {
        width: 100% !important;
        max-width: 100% !important;
    }

    /* Labels */
    label, .control-label {
        font-size: 13px !important;
        margin-bottom: 6px !important;
    }

    /* Custom select */
    .custom-select2-selection {
        border-radius: 5px !important;
        height: 44px !important;
        font-size: 14px !important;
    }
    .custom-select2-dropdown {
        border-radius: 5px !important;
    }

    /* Phone row */
    .phone-row, .ev-phone-row {
        display: flex !important;
        gap: 8px !important;
    }

    /* Services checkboxes - pill tags */
    .checkbox-columns {
        display: flex !important;
        flex-wrap: wrap !important;
        gap: 8px !important;
    }
    .checkbox-columns label,
    .checkbox-columns .checkbox {
        display: inline-flex !important;
        align-items: center !important;
        gap: 6px !important;
        padding: 6px 14px !important;
        background: #111 !important;
        border: 1px solid #333 !important;
        border-radius: 5px !important;
        margin: 0 !important;
        font-size: 13px !important;
        cursor: pointer;
        white-space: nowrap;
    }
    .checkbox-columns label.active,
    .checkbox-columns .checkbox.active,
    .checkbox-columns label:has(input:checked),
    .checkbox-columns .checkbox:has(input:checked) {
        border-color: #C1F11D !important;
        color: #C1F11D !important;
    }

    /* Price inputs side by side */
    .price-row, .row:has(.price-amount) {
        display: flex !important;
        gap: 10px !important;
    }
    .price-amount {
        border-radius: 5px !important;
        font-size: 14px !important;
    }

    /* Photo upload area */
    .drag-drop {
        border: 2px dashed #333 !important;
        border-radius: 5px !important;
        padding: 30px 20px !important;
        text-align: center !important;
    }

    /* Messaging app checkboxes */
    .messaging-apps label {
        border-radius: 5px !important;
    }

    /* Submit button */
    #submit, .btn-primary.btn-lg {
        width: 100% !important;
        background: #C1F11D !important;
        color: #000 !important;
        border: none !important;
        border-radius: 5px !important;
        font-size: 16px !important;
        font-weight: 600 !important;
        padding: 14px !important;
        margin-top: 20px !important;
    }

    /* Alert messages */
    .alert {
        border-radius: 5px !important;
    }

    /* Two column rows on mobile */
    .row .col-sm-3,
    .row .col-sm-4,
    .row .col-sm-6 {
        width: 50% !important;
        float: left !important;
        padding: 0 6px !important;
    }
    .row .col-sm-12 {
        width: 100% !important;
        padding: 0 !important;
    }

    /* Form group spacing */
    .form-group {
        margin-bottom: 16px !important;
    }

    /* Hint text */
    .hint, .city-hint, .char-count-container {
        font-size: 11px !important;
    }

    /* Image previews */
    #image-container {
        gap: 8px !important;
    }
    .record.image {
        width: calc(33.33% - 6px) !important;
        height: auto !important;
        aspect-ratio: 1 / 1;
        border-radius: 5px !important;
    }
    .record.image img {
        max-height: 100% !important;
    }
    .record.image .delete {
        top: 2px !important;
        right: 2px !important;
        padding: 3px 5px !important;
        font-size: 9px !important;
    }
    .record.image .img-footer .text-muted.small {
        display: none !important;
    }
    .record.image .img-pending {
        display: none !important;
    }
    /* On mobile, drag doesn't fire — make the Set as Main button big and
       easy to tap, and the Main badge stand out at the bottom of each card.
       touch-action: none allows our JS-driven long-press drag to take over
       without the browser hijacking the gesture for scroll/select.
       3-per-row layout: cards are narrower so badge/button text and padding
       are reduced to keep everything legible without truncating. */
    .record.image {
        width: calc(33.33% - 6px) !important;
        touch-action: none;
        -webkit-user-select: none;
        user-select: none;
    }
    .record.image img {
        -webkit-user-drag: none;
        pointer-events: none;
    }
    .record.image.drag-over {
        outline: 2px dashed #C1F11D !important;
        outline-offset: -2px;
    }
    .record.image .img-footer {
        position: absolute !important;
        left: 0 !important;
        right: 0 !important;
        bottom: 0 !important;
        padding: 4px !important;
        background: rgba(0, 0, 0, 0.75) !important;
    }
    /* Main Image badge + Set as Main button share the img-footer strip.
       They must be the SAME height (uniform pill row across thumbnails) but
       the STYLES must differ so users can tell at a glance which thumbnail
       is currently the primary and which are candidates:
       - Main Image (badge) → solid lime fill, black text  (selected state)
       - Set as Main (button) → transparent fill, lime text + lime outline
                                (unselected state, tap-to-promote)
       Padding, font-size, line-height, and border-width all match so both
       compute to the same box height. */
    .record.image .btn-set-main {
        display: block !important;
        width: 100% !important;
        padding: 3px 6px !important;
        font-size: 10px !important;
        line-height: 1.2 !important;
        background: transparent !important;
        color: #c8ff00 !important;
        border: 1px solid #c8ff00 !important;
        border-radius: 4px !important;
        min-height: 0 !important;
        height: auto !important;
        font-weight: 600;
        /* Ensure tap goes straight through — no double-tap zoom, no 300ms delay,
           no parent drag interference */
        touch-action: manipulation;
        -webkit-tap-highlight-color: rgba(200,255,0,0.2);
        pointer-events: auto !important;
        position: relative;
        z-index: 5;
    }
    .record.image .btn-set-main:hover,
    .record.image .btn-set-main:focus {
        background: rgba(200,255,0,0.12) !important;
        color: #c8ff00 !important;
    }
    .record.image .badge-success {
        display: block;
        padding: 3px 6px !important;
        font-size: 10px !important;
        line-height: 1.2 !important;
        background-color: #c8ff00 !important;
        color: #000 !important;
        border: 1px solid #c8ff00 !important;
        border-radius: 4px !important;
        font-weight: 700;
    }
    .record.image .delete {
        top: 3px !important;
        right: 3px !important;
        padding: 2px 4px !important;
        font-size: 9px !important;
    }

    /* Terms text at bottom */
    .terms-text {
        text-align: center;
        font-size: 13px;
        color: #888;
    }
    .terms-text a {
        color: #C1F11D;
    }

    /* Hide elements not needed on mobile */
    .hidden-mobile {
        display: none !important;
    }
}

@media (min-width: 992px) {
    form.listing .big-one-line .form-group, form.listing .big-one-line div.typeahead-city-wrapper, form.listing .big-one-line label {
        float: left;
        margin-right: 10px;
        width: 100%;
    }
}

#footer {
    background-color: #0a0b0d  !important;
}
#footer > .container-fluid {
    margin-left:6rem;}

        </style>

<div class="row container">

{{-- Mobile Header --}}
<div class="ev-newprofile-mobile-header" style="display:none;">
    <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 16px;">
        <a href="javascript:history.back()" style="color:#C1F11D;text-decoration:none;font-size:14px;display:flex;align-items:center;gap:4px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
            Back
        </a>
        <a href="/" style="display:flex;align-items:center;gap:6px;text-decoration:none;">
            @if(isset($setting) && $setting->app_logo)
                <img src="{{ smart_asset($setting->app_logo) }}" alt="{{ $setting->app_name ?? 'evoory' }}" style="height:28px;width:auto;display:block;">
            @else
                <span style="color:#C1F11D;font-size:18px;font-weight:600;font-style:italic;">{{ $setting->app_name ?? 'evoory' }}</span>
            @endif
        </a>
        <div style="width:40px;"></div>
    </div>
    <div style="padding:8px 25px 16px;">
        <h1 style="color:#fff;font-size:26px;font-weight:600;margin:0 0 4px;">Create Your Listing</h1>
        <p style="color: #A6B4B8 !important;font-size:15px;margin:0;">Complete all sections to publish your profile</p>
    </div>
</div>
<style>
@media (max-width: 768px) {
    .ev-newprofile-mobile-header { display: block !important; }
}
</style>

            <div class="col-lg-offset-1 col-lg-10">
             
          
              <form class="simple_form listing js-only" id="new_listing" wire:submit.prevent='updateProfile'  enctype="multipart/form-data" >
               
                <div class="wrapper">
                  <div id="basic">
                    <h2 class="h3 title-block ev-mobile-section-title" style="display:none;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#C1F11D" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        Basic Information
                    </h2>
                    <style>@media(max-width:768px){.ev-mobile-section-title{display:flex!important}}</style>
                    <div class="big-one-line left">
                      <div class="form-group string required listing_name">
                        <label class="ev-mobile-label" style="display:none;">Profile Name <span style="color:#f87171">*</span></label>
                        <style>@media(max-width:768px){.ev-mobile-label{display:block!important;color:#ccc;font-size:13px;margin-bottom:6px}}</style>
                        <input class="string required form-control medium validate" value="" data-validations="presence doesNotContainEmails doesNotContainPhones doesNotContainUrls length(3,40)" data-error-position-my="center bottom" data-error-position-offset="0 0" data-error-position-at="center top" data-tooltip-class="tooltip tooltip-s" maxlength="40" placeholder="Your professional name" size="40" type="text" wire:model='name' name="listing[name]" id="listing_name" />
                      </div>
                      <div class="form-group  listing_listed_as_id">
                        <label class="ev-mobile-label" style="display:none;">Category</label>
                        <select class="form-control listinga" wire:model="listing" id="listing_listed_as_id">
                          @foreach($listings as $listingOption)
                            <option value="{{$listingOption->id}}">{{$listingOption->name}}</option>
                          @endforeach
                        </select>
                      </div> 
                      <div class="form-group city optional listing_city_url">
                        <label class="city optional control-label ev-city-label" for="listing_city_url">City <span class="required-star" style="color:#f87171">*</span></label>
                        <div class='typeahead-city-wrapper'>
                          <input class="city optional form-control" placeholder="Enter city name"
    wire:model.lazy='selectedcity' type="text" id="citysearch"/>
<input type="hidden" wire:model.lazy='city' id="selectedcityid">
                          
                        <div id="cityappend" class="citys"></div>
                        <span class="hint city-hint left">
                      <div class="clearfix"></div>
                      <p class="text-right small" style="font-size:11px">Your city not available? <a href="/contact-us" tabindex="-1" target="_blank">Ask for it</a>
                      </p>
                    </span>
                        </div>
                      </div>
                    </div>
                    
                    <div class="form-group text required listing_description">
                      <label class="text required control-label" for="listing_description">
                        About <span class="required-star">*</span></label>
                      <textarea class="text required form-control validate large" data-validations="presence doesNotContainEmails doesNotContainPhones doesNotContainUrls length(50,2000)" maxlength="2000" wire:model='aboutme' name="listing[description]" id="listing_description" oninput="updateCharCount(this)" placeholder="Write a brief description of yourself..."></textarea>
                      <div class="char-count-container" style="margin-top: 5px; font-size: 12px;">
                        <span id="char-count" style="color: #666;">Minimum 50 characters</span>
                        <span id="char-count-warning" style="color: #dc3545; margin-left: 10px; display: none;">Minimum 50 characters required</span>
                        <span id="char-count-ok" style="color: #28a745; margin-left: 10px; display: none;">✓ Minimum reached</span>
                      </div>
                      @error('aboutme')
                        <span class="validation-error" style="color: #dc3545; font-size: 12px;">{{ $message }}</span>
                      @enderror
                    </div>
                  </div>
                  <div id="photos">
                    <h2 class="h3 title-block ev-photos-desktop-title">Upload photos</h2>
                    <h2 class="h3 title-block ev-mobile-section-title ev-photos-mobile-title" style="display:none;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#C1F11D" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                        Photos
                    </h2>
                    <div class="ad-images string optional listing_listing_images">
                      <div class="multi-image-uploader1" >
                        <p class="ev-photos-disclaimer">Please upload high quality images only. Only post pictures of yourself or of a person who has given you explicit permission to do so. If you post fake photos, your profile will be deleted and your account blocked - <a href="/help-for-advertisers#fake" target="_blank">more information</a>. <br /> Photos with full frontal nudity, genitalia or sexually explicit conduct are prohibited. </p>

                        <div wire:loading.flex wire:target="mphoto" style="align-items:center;justify-content:center;gap:10px;color:#0a0b0d;background:#c8ff00;margin-bottom:14px;padding:10px 14px;border-radius:6px;font-size:14px;font-weight:600;">
                            <i class="fas fa-spinner fa-spin"></i> Uploading photo…
                        </div>

                        <div id="image-container">
                        @if($tempImages)
                        @foreach($tempImages  as $key => $image)
                        <div class="record image" draggable="true" role="option" aria-grabbed="false" data-index="{{ $key }}">
                          <i wire:click="removeTemporaryImage({{ $key }})" class="fa fa-times fa-lg delete"></i>
                          <span class="img-name" ></span>
                          {{-- draggable=false on the img prevents the browser from
                               claiming the image element as the drag source; the
                               wrapper's draggable=true then takes effect cleanly. --}}
                          <img src="{{ $image->temporaryUrl() }}" draggable="false">
                          <div class="img-footer">
                            @if($key === 0)
                                <span class="badge badge-success">Main Image</span>
                            @else
                                {{-- Plain button (no wrapping <label>). On touch devices
                                     a label can swallow / re-target the synthesized click;
                                     desktop tolerates it, mobile doesn't. Also stop the
                                     mousedown / touch so the parent's draggable=true
                                     doesn't claim the gesture as a drag. --}}
                                <button type="button"
                                        class="btn-set-main"
                                        wire:click="setAsMain({{ $key }})"
                                        onmousedown="event.stopPropagation()"
                                        ontouchstart="event.stopPropagation()"
                                        ondragstart="event.preventDefault(); event.stopPropagation(); return false;"
                                        draggable="false">Set as Main</button>
                            @endif
                        </div>
                        </div>
                        @endforeach
                        @endif
                        </div>
                        <div class="record image-input new-img">
                          <div class="file optional add-img">
                            <label class="modern-upload-label" for="mphoto" style="cursor: pointer;">
                              <div class="text-center mb-4 drag-drop" id="drag-drop-area">
                                <svg xmlns="http://www.w3.org/2000/svg" width="65" height="65" viewBox="0 0 24 24" fill="none" stroke="#727B7E" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 20px;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                               
                                <div class="upload-available">
                                  <p class="m-0 font-weight-bold drag-drop-text-main ev-photos-desktop-only" style="font-size: 12px; color: #ffffff; font-weight:400; margin-bottom:20px">Drop files here</p>
                                  <p class="m-1 drag-drop-text ev-photos-desktop-only" style="color: #6c757d;padding-top:10px; padding-bottom:15px">or</p>
                                  <button class="m-1 mb-3 btn btn-primary ev-photos-choose-btn" type="button" onclick="document.getElementById('mphoto').click(); return false;" style="background:#c8ff00;color:#000;border:none;border-radius:50px;font-weight:600;padding:8px 24px;">Choose file</button>
                                  <p class="m-0 mt-3 drag-drop-text ev-photos-desktop-only" style="font-size: 13px; color: #6c757d;">Pick a file up to 8MB and at least 400×400 px</p>
                                  <p class="m-0 drag-drop-text ev-photos-desktop-only" style="font-size: 13px; color: #6c757d;">Allowed file formats: jpg, jpeg, gif, png, webp</p>
                                  <p class="ev-photos-mobile-hint" style="display:none;">JPG, PNG up to 8MB each. <br>Min 3 photos required.</p>
                                </div>
                                <div class="upload-disabled">
                                  <p class="m-0 drag-drop-text" style="color: #dc3545; font-weight: 500;">You have reached the limit of 30 images. Remove one to add a new image.</p>
                                </div>
                              </div>
                            </label>
                            <input class="file optional" wire:model='mphoto' type="file" accept="image/*" id="mphoto" multiple style="display: none;">
                          </div>
                        </div>

                        @error('mphoto')
                            <div class="alert alert-danger" style="margin-top:10px;">{{ $message }}</div>
                        @enderror
                        @error('mphoto.*')
                            <div class="alert alert-danger" style="margin-top:10px;">{{ $message }}</div>
                        @enderror

                      </div>

                    </div>
                  </div>


                  {{-- Mobile Contact Information --}}
                  <div class="ev-mobile-contact" style="display:none;">
                    <h2 class="h3 title-block">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#C1F11D" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                        Contact Information
                    </h2>

                    <div class="ev-mc-group">
                        <label>Phone Number</label>
                        <div style="display:flex;gap:8px;">
                            <div style="width:110px;flex-shrink:0;" wire:ignore>
                                <select data-radius="all" data-with-flags="1" wire:model='countrycode' class="apply-custom-select2 form-control" id="ev_mobile_phone_code" style="height:44px;font-size:13px;">
                                    <option value="">Select</option>
                                    @foreach($countries as $code)
                                    <option value="{{$code->phonecode}}" data-iso="{{ strtolower($code->iso) }}" data-name="{{ $code->nicename }}" data-dial="{{ $code->phonecode }}" {{ $countrycode == $code->phonecode ? 'selected' : '' }}>+{{$code->phonecode}} - {{$code->nicename}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <input wire:model.lazy="phone" class="form-control" type="text" placeholder="Phone number" style="flex:1;height:44px;">
                        </div>
                    </div>

                    <div class="ev-mc-group">
                        <label>Messaging Apps</label>
                        <div class="ev-msg-grid" style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
                            <label class="ev-msg-pill">
                                <input wire:model='iswhatsapp' type="checkbox" value="1" style="display:none;">
                                <svg class="ev-msg-pill__icon" width="16" height="16" viewBox="0 0 24 24" fill="#25D366" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413"/>
                                </svg>
                                <span>WhatsApp</span>
                            </label>
                            <label class="ev-msg-pill">
                                <input wire:model='istelegram' type="checkbox" value="1" style="display:none;">
                                <svg class="ev-msg-pill__icon" width="16" height="16" viewBox="0 0 24 24" fill="#229ED9" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0m5.894 8.221-1.97 9.28c-.145.658-.537.818-1.084.508l-3-2.21-1.446 1.394c-.16.16-.295.295-.605.295l.213-3.053 5.56-5.022c.243-.213-.054-.334-.373-.121l-6.871 4.326-2.962-.924c-.643-.204-.657-.643.136-.953l11.566-4.458c.538-.196 1.006.128.832.938"/>
                                </svg>
                                <span>Telegram</span>
                            </label>
                            <label class="ev-msg-pill">
                                <input wire:model='iswechat' type="checkbox" value="1" style="display:none;">
                                <svg class="ev-msg-pill__icon" width="16" height="16" viewBox="0 0 24 24" fill="#07C160" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path d="M8.691 2.188C3.891 2.188 0 5.476 0 9.53c0 2.212 1.17 4.203 3.002 5.55a.59.59 0 0 1 .213.665l-.39 1.48c-.019.07-.048.141-.048.213 0 .163.13.295.29.295a.32.32 0 0 0 .167-.054l1.903-1.114a.93.93 0 0 1 .48-.117c.085 0 .172.013.252.034.94.288 1.952.45 3.013.45.176 0 .349-.013.522-.025-.111-.395-.176-.812-.176-1.252 0-3.626 3.5-6.564 7.81-6.564.166 0 .332.013.495.025-.626-3.286-3.92-5.842-7.842-5.842M5.785 5.667a1.052 1.052 0 1 1 .003 2.105 1.052 1.052 0 0 1-.003-2.105m5.812 0a1.052 1.052 0 1 1 .003 2.105 1.052 1.052 0 0 1-.003-2.105"/>
                                    <path d="M24 14.66c0-3.385-3.262-6.13-7.286-6.13s-7.286 2.745-7.286 6.13c0 3.388 3.262 6.131 7.286 6.131.846 0 1.66-.131 2.418-.355a.79.79 0 0 1 .205-.027c.135 0 .26.039.376.097l1.591.93a.27.27 0 0 0 .14.046.245.245 0 0 0 .244-.248c0-.062-.024-.12-.04-.178l-.327-1.237a.49.49 0 0 1 .175-.554C22.991 18.142 24 16.495 24 14.66m-9.701-1.018a.879.879 0 0 1 0-1.756.879.879 0 0 1 0 1.756m4.83 0a.879.879 0 0 1 0-1.756.879.879 0 0 1 0 1.756"/>
                                </svg>
                                <span>WeChat</span>
                            </label>
                            <label class="ev-msg-pill">
                                <input wire:model='issignal' type="checkbox" value="1" style="display:none;">
                                <svg class="ev-msg-pill__icon" width="16" height="16" viewBox="0 0 24 24" fill="#3A76F0" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path d="M9.12.35a.4.4 0 0 1 .29.484l-.286 1.14a9.85 9.85 0 0 0-2.583 1.07l-.604-1.005a.4.4 0 1 1 .686-.411l.5.83a10.7 10.7 0 0 1 2.014-.835l.235-.937a.4.4 0 0 1 .483-.291zm6.05.292.236.936a10.7 10.7 0 0 1 2.012.834l.5-.83a.4.4 0 1 1 .686.411l-.604 1.006a9.85 9.85 0 0 0-2.581-1.07l-.286-1.14A.4.4 0 1 1 15.17.642zM4.21 3.117a.4.4 0 0 1 .025.566 9.9 9.9 0 0 0-1.512 2.234l1.04.518a.4.4 0 0 1-.358.715l-1.052-.525a10.8 10.8 0 0 0-.71 2.564l1.155.193a.4.4 0 1 1-.13.789L1.51 9.978a10.7 10.7 0 0 0 0 2.661l1.155-.192a.4.4 0 1 1 .13.789l-1.155.192a10.7 10.7 0 0 0 .71 2.557l1.05-.524a.4.4 0 0 1 .358.715l-1.04.518a9.9 9.9 0 0 0 1.513 2.236.4.4 0 0 1-.591.539 10.7 10.7 0 0 1-1.633-2.414l-.71.355a.4.4 0 0 1-.564-.452L1.32 14.91A11.5 11.5 0 0 1 .7 12.3a11.5 11.5 0 0 1 0-2.984.4.4 0 0 1-.07-.255l.704-3.519a.4.4 0 0 1 .564-.452l.71.355A10.7 10.7 0 0 1 3.644 3.09a.4.4 0 0 1 .566-.025zM12 1.5a10.5 10.5 0 0 0-3.027.443l.298 1.187a.4.4 0 0 1-.776.196L8.197 2.139A9.85 9.85 0 0 0 5.86 3.31l.687 1.144a.4.4 0 1 1-.686.412L5.175 3.72A9.9 9.9 0 0 0 3.722 5.17l1.142.686a.4.4 0 1 1-.412.686l-1.144-.687A9.85 9.85 0 0 0 2.137 8.19l1.189.297a.4.4 0 0 1-.196.776l-1.187-.297A10.5 10.5 0 0 0 1.5 12c0 1.034.149 2.034.443 2.974l1.187-.297a.4.4 0 0 1 .196.776l-1.189.297a9.85 9.85 0 0 0 1.171 2.336l1.144-.686a.4.4 0 1 1 .412.686l-1.142.686a9.9 9.9 0 0 0 1.452 1.452l.687-1.144a.4.4 0 0 1 .686.412L5.86 20.69a9.85 9.85 0 0 0 2.336 1.171l.297-1.189a.4.4 0 0 1 .777.196l-.297 1.189A10.5 10.5 0 0 0 11.5 22.5l-.005-1.225a.4.4 0 1 1 .8 0L12.3 22.5a10.5 10.5 0 0 0 2.532-.32 9.4 9.4 0 0 0-3.052-7.022.4.4 0 0 1 .55-.58 10.2 10.2 0 0 1 3.32 7.484 10.5 10.5 0 0 0 1.66-.852 9.4 9.4 0 0 0-3.69-3.464.4.4 0 1 1 .378-.706 10.2 10.2 0 0 1 3.946 3.674A10.4 10.4 0 0 0 19.21 19.46l-.687-1.144a.4.4 0 1 1 .686-.412l.686 1.144a9.85 9.85 0 0 0 1.452-1.452l-1.144-.686a.4.4 0 1 1 .412-.686l1.144.686a9.85 9.85 0 0 0 1.171-2.336l-1.189-.297a.4.4 0 0 1 .196-.776l1.187.297c.294-.94.443-1.94.443-2.974s-.149-2.034-.443-2.974l-1.187.297a.4.4 0 0 1-.196-.776l1.189-.297A9.85 9.85 0 0 0 21.76 5.86l-1.144.687a.4.4 0 1 1-.412-.686l1.142-.686a9.9 9.9 0 0 0-1.452-1.452l-.686 1.144a.4.4 0 1 1-.686-.412L18.825 3.31a9.85 9.85 0 0 0-2.336-1.171l-.297 1.189a.4.4 0 1 1-.777-.196l.298-1.187A10.5 10.5 0 0 0 12 1.5z"/>
                                </svg>
                                <span>Signal</span>
                            </label>
                        </div>
                    </div>

                    <div class="ev-mc-group">
                        <label>Email</label>
                        <input class="form-control" type="email" value="{{$user->email}}" readonly placeholder="your@mail.com">
                    </div>

                    <div class="ev-mc-group">
                        <label>Website</label>
                        <input wire:model='website' class="form-control" type="text" placeholder="https://yourwebsite.com">
                    </div>

                    <div class="ev-mc-group">
                        <label>OnlyFans</label>
                        <input wire:model='onlyfans' class="form-control" type="text" placeholder="@username">
                    </div>
                  </div>
                  <style>
                    @media(max-width:768px){
                        /* Contact Information wrapped as a single card */
                        .ev-mobile-contact{
                            display:block!important;
                            background:#111!important;
                            border:1px solid #222!important;
                            border-radius:10px!important;
                            padding:18px!important;
                            margin:24px 0 16px!important;
                        }
                        /* Heading becomes internal header, not its own card */
                        .ev-mobile-contact .h3.title-block,
                        .ev-mobile-contact h2.h3.title-block{
                            background:transparent!important;
                            border:none!important;
                            border-radius:0!important;
                            padding:0!important;
                            margin:0 0 16px!important;
                            font-size:17px!important;
                            gap:12px!important;
                        }
                        .ev-mobile-contact .h3.title-block svg{
                            background:#283813;
                            border:1px solid #3a4a2a;
                            border-radius:8px;
                            padding:7px;
                            width:36px!important;
                            height:36px!important;
                            box-sizing:border-box;
                            flex-shrink:0;
                        }
                        #contact-information{display:none!important}
                        .ev-mc-group{margin-bottom:16px}
                        .ev-mc-group>label{display:block;color:#ccc;font-size:13px;font-weight:500;margin-bottom:6px}
                        /* 4 pills in a 2x2 grid (grid-template-columns: 1fr 1fr
                           on the wrapper) — each cell is the same size, so
                           each pill inherits identical width. Fixed height
                           locks the row height too. Content is centered
                           inside via flex so the shorter label ("Signal")
                           doesn't shift its icon vs. the wider label. */
                        .ev-msg-pill{
                            display:flex;align-items:center;justify-content:center;gap:6px;
                            background:#111;border:1px solid #333;border-radius:5px;
                            padding:0 12px;cursor:pointer;color:#fff;font-size:13px;
                            height:40px;width:100%;box-sizing:border-box;
                            white-space:nowrap;overflow:hidden;text-overflow:ellipsis;
                        }
                        .ev-msg-pill__icon{
                            display:block;
                            width:16px !important;
                            height:16px !important;
                            transform:none !important;
                            -webkit-transform:none !important;
                            margin:0 !important;
                            flex-shrink:0;
                        }
                        .ev-msg-pill:has(input:checked){
                            border-color:#C1F11D;color:#C1F11D;
                        }
                    }

                    /* Extra small: make the phone-code dropdown span the whole
                       card and stop it running past the OnlyFans row. The
                       trigger sits in a 110px flex column so the default
                       width:max-content grows only rightward from that column
                       and can either clip against the card edge or overlay the
                       fields below. Wrap the phone row in a positioning
                       parent (ev-mc-group is already relative here) and shift
                       the dropdown back to the card's inner-left edge. */
                    @media (max-width:480px){
                        .ev-mobile-contact .ev-mc-group{
                            position:relative;
                        }
                        .ev-mobile-contact .ev-mc-group .custom-select2{
                            position:static;
                        }
                        /* Position the dropdown against the ev-mc-group so it
                           spans the full inner card width (from left of +93 to
                           right of phone input) and drops just below the whole
                           phone row instead of the 110px column. */
                        .ev-mobile-contact .ev-mc-group .custom-select2-dropdown{
                            left:0!important;
                            right:0!important;
                            width:auto!important;
                            min-width:0!important;
                            max-width:none!important;
                            top:calc(100% + 4px)!important;
                        }
                        .ev-mobile-contact .ev-mc-group .custom-select2-results{
                            max-height:min(48vh,260px)!important;
                        }
                    }
                  </style>

                  <div id="contact-information">
                    <h2 class="h3 title-block">Contact information</h2>
                    <label>phone:</label>
                    {{-- wire:ignore is critical here: legacy app2.js inserts an
                         "+ Add another phone" button between the two
                         phone_number blocks after page load. That button isn't
                         in the server HTML, so on the first Livewire commit
                         morph removes it — the vertical collapse of ~40px
                         causes every element below to jump, which visually
                         reads as the whole page "shaking" left/right.
                         wire:model inputs inside still submit via change
                         events, so form functionality is unaffected. --}}
                    <div class="inline-group" wire:ignore>
                      <div class="form-group phone_number">
                        <div style="margin-bottom:15px" class="d-flex align-items-center wrappper" >
                          <div class="insidewrapper" wire:ignore>
                              <select  class="select2-country apply-custom-select2  form-control"
                                  wire:model='countrycode'
                                  style="border-radius:5px"
                                  id="first_phone_code" >
                                  <option value="">Select code</option>
                                  @foreach($countries as $code)
                                  <option value="{{$code->phonecode}}" {{ $countrycode == $code->phonecode ? 'selected' : '' }}>
                                      +{{$code->phonecode}} - {{$code->nicename}}
                                  </option>
                                  @endforeach
                              </select>
                          </div>
                          <div style="width: 50%; ">
                              <input class="phone_number_input--digits form-control" 
                                     type="text" 
                                     name="phone"
                                     wire:model.lazy="phone"
                                     style="width:100%"
                                     placeholder="Enter phone number"
                                     id="listing_phone_numbers_attributes_0_phone_digits" />
                          </div>
                      </div>
                    
                         <div style="row">
                        <label class="inline-block checkbox col-6" style="margin-top:0px">
              
                          <input  wire:model='iswhatsapp' wrapper="false" label="false" class="boolean optional" type="checkbox" value="1" name="listing[phone_numbers_attributes][0][whatsapp]" id="listing_phone_numbers_attributes_0_whatsapp" />
                          <span class="icon-whatsapp icon-inline"></span> WhatsApp </label>
                        <label class="inline-block col-6 checkbox margin-left">
                        
                          <input wrapper="false"  wire:model='iswechat' label="false" class="boolean optional" type="checkbox" value="1" name="listing[phone_numbers_attributes][0][wechat]" id="listing_phone_numbers_attributes_0_wechat" />
                          <span class="icon-wechat icon-inline"></span> WeChat </label>
                        <label class="inline-block  col-6 checkbox margin-left mob-left">
                         
                          <input wrapper="false"  wire:model='istelegram' label="false" class="boolean optional" type="checkbox" value="1" name="listing[phone_numbers_attributes][0][telegram]" id="listing_phone_numbers_attributes_0_telegram" />
                          <span class="icon-telegram icon-inline"></span> Telegram </label>
                        <label class="inline-block col-6 checkbox margin-left">
                          
                          <input wrapper="false"  wire:model='issignal' label="false" class="boolean optional" type="checkbox" value="1" name="listing[phone_numbers_attributes][0][signal]" id="listing_phone_numbers_attributes_0_signal" />
                          <span class="icon-signal icon-inline"></span> Signal </label>
                        </div>
                       
                      </div>
                      <div class="form-group phone_number second-phone-section" style="display:none">
                        <div style="float: left;width: 27%;" class="d-flex align-items-center">
                          <div style="width: 40%;margin-right:5px" wire:ignore>
                          <select style="margin-right:5px" class="select2-country apply-custom-select2" wire:model='countrycode2' name="listing[phone_numbers_attributes][1][calling_code]" id="second_phone_code" >
                            <option value="">Intl. code</option>
                            @foreach($countries as $code)
                            <option value="{{$code->phonecode}}">+{{$code->phonecode}} - {{$code->nicename}}</option>
                            @endforeach
                          </select>
                          </div>
                          <div style="width: 50%; " wire:ignore>
                          <input style="width:100%" class="phone_number_input--digits form-control" name="phone2" type="text" name="listing[phone_numbers_attributes][1][phone_digits]" id="listing_phone_numbers_attributes_1_phone_digits" placeholder="Enter phone number" />
                          </div>
                        </div>
                        <label class="inline-block checkbox" style="margin-top:0px">
                          <input wrapper="false" label="false" wire:model='iswhatsapp2'  class="boolean optional" type="checkbox" value="1" name="listing[phone_numbers_attributes][1][whatsapp]" id="listing_phone_numbers_attributes_1_whatsapp" />
                          <span class="icon-whatsapp icon-inline"></span> WhatsApp </label>
                        <label class="inline-block checkbox margin-left">
                          <input wrapper="false" wire:model='iswechat2'  label="false" class="boolean optional" type="checkbox" value="1" name="listing[phone_numbers_attributes][1][wechat]" id="listing_phone_numbers_attributes_1_wechat" />
                          <span class="icon-wechat icon-inline"></span> WeChat </label>
                        <label class="inline-block checkbox margin-left">
                          
                          <input wrapper="false" @if($user->istelegram2 == 1) @checked(true) @endif wire:model='istelegram2' label="false" class="boolean optional" type="checkbox" value="1" name="listing[phone_numbers_attributes][1][telegram]" id="listing_phone_numbers_attributes_1_telegram" />
                          <span class="icon-telegram icon-inline"></span> Telegram </label>
                        <label class="inline-block checkbox margin-left">
                          <input wrapper="false" @if($user->issignal2 == 1) @checked(true) @endif wire:model='issignal2' label="false" class="boolean optional" type="checkbox" value="1" name="listing[phone_numbers_attributes][1][signal]" id="listing_phone_numbers_attributes_1_signal" />
                          <span class="icon-signal icon-inline"></span> Signal </label>
                      </div>
                      {{-- <a class="btn btn-dark add-second-phone" href="javascript:void(0)" style="box-shadow:none">
                      + Add another phone
                   </a> --}}
                    </div>
                    <div class="row">
                      <div class="col-sm-6">
                        <div class="form-group">
                          <div class="form-group boolean optional listing_show_contact_form">
                            <input value="0" type="hidden" name="listing[show_contact_form]" />
                            <!-- <label class="boolean optional control-label checkbox" for="show-contact-form"> -->
                              <!-- <input id="show-contact-form" class="boolean optional" type="checkbox" value="1" checked="checked" name="listing[show_contact_form]" />Show contact form and send messages to: </label> -->
                          </div>
                          <label class="string optional control-label" for="listing_contact_email_address">Email</label>
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
                  {{-- Mobile Services Section --}}
                  <div class="ev-mobile-services" style="display:none;">
                    <h2 class="h3 title-block">Services Offered</h2>
                    <div class="ev-svc-grid" id="evSvcGrid">
                        @php
                        $servicesList = [
                            ['id'=>1,'name'=>'Anal Sex'],['id'=>2,'name'=>'BDSM'],['id'=>3,'name'=>'Come In Mouth'],
                            ['id'=>4,'name'=>'Come On Body'],['id'=>5,'name'=>'Couples'],['id'=>6,'name'=>'Deep throat'],
                            ['id'=>7,'name'=>'Domination'],['id'=>8,'name'=>'Face sitting'],['id'=>9,'name'=>'Fingering'],
                            ['id'=>10,'name'=>'Fisting'],['id'=>11,'name'=>'Foot fetish'],['id'=>12,'name'=>'French kissing'],
                            ['id'=>13,'name'=>'GFE'],['id'=>14,'name'=>'Giving hardsports'],['id'=>15,'name'=>'Lap dancing'],
                            ['id'=>16,'name'=>'Massage'],['id'=>17,'name'=>'Nuru massage'],['id'=>18,'name'=>'Oral sex - blowjob'],
                            ['id'=>19,'name'=>'Fisting'],['id'=>20,'name'=>'OWO'],['id'=>21,'name'=>'Parties'],
                            ['id'=>22,'name'=>'Reverse oral'],['id'=>23,'name'=>'Giving rimming'],['id'=>24,'name'=>'Rimming receiving'],
                            ['id'=>25,'name'=>'Role play'],['id'=>26,'name'=>'Sex toys'],['id'=>27,'name'=>'Spanking'],
                            ['id'=>28,'name'=>'Strapon'],['id'=>29,'name'=>'Striptease'],['id'=>30,'name'=>'Submissive'],
                            ['id'=>31,'name'=>'Squirting'],['id'=>32,'name'=>'Tantric massage'],['id'=>33,'name'=>'Teabagging'],
                            ['id'=>34,'name'=>'Tie and tease'],['id'=>35,'name'=>'Uniforms'],['id'=>36,'name'=>'Giving watersports'],
                            ['id'=>37,'name'=>'Receiving watersports'],['id'=>38,'name'=>'Webcam sex'],
                        ];
                        @endphp
                        @foreach($servicesList as $idx => $svc)
                        <label class="ev-svc-pill {{ $idx >= 20 ? 'ev-svc-hidden' : '' }}">
                            <input wire:model="services" type="checkbox" value="{{ $svc['id'] }}" style="display:none;">
                            <span>{{ $svc['name'] }}</span>
                        </label>
                        @endforeach
                    </div>
                    <button type="button" class="ev-svc-loadmore" id="evSvcLoadMore" onclick="document.querySelectorAll('.ev-svc-hidden').forEach(function(e){e.classList.remove('ev-svc-hidden')});this.style.display='none';">
                        Load more <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                    </button>
                  </div>
                  <style>
                    @media(max-width:768px){
                        /* Services Offered wrapped as a single card */
                        .ev-mobile-services{
                            display:block!important;
                            background:#111!important;
                            border:1px solid #222!important;
                            border-radius:10px!important;
                            padding:18px!important;
                            margin:24px 0 16px!important;
                        }
                        /* Heading becomes internal header, not its own card */
                        .ev-mobile-services .h3.title-block,
                        .ev-mobile-services h2.h3.title-block{
                            background:transparent!important;
                            border:none!important;
                            border-radius:0!important;
                            padding:0!important;
                            margin:0 0 16px!important;
                            font-size:17px!important;
                        }
                        #services{display:none!important}
                        .ev-svc-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px}
                        .ev-svc-pill{
                            display:flex;align-items:center;justify-content:center;
                            background:#111;border:1px solid #333;border-radius:8px;
                            padding:7px 8px;cursor:pointer;color:#fff;font-size:13px;
                            text-align:center;
                        }
                        .ev-svc-pill:has(input:checked){
                            border-color:#C1F11D;color:#C1F11D;
                        }
                        .ev-svc-pill:has(input:checked)::before{
                            content:'';
                            display:inline-block;
                            width:16px;height:16px;
                            margin-right:8px;
                            flex-shrink:0;
                            background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23C1F11D' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Ccircle cx='12' cy='12' r='10'/%3E%3Cpolyline points='8 12 11 15 16 9'/%3E%3C/svg%3E");
                            background-repeat:no-repeat;
                            background-position:center;
                            background-size:contain;
                        }
                        .ev-svc-hidden{display:none!important}
                        .ev-svc-loadmore{
                            background:none;border:none;color:#C1F11D;
                            font-size:14px;font-weight:500;cursor:pointer;
                            padding:14px 0 0;display:flex;align-items:center;gap:6px;
                        }
                    }
                  </style>

                  <div id="services">
                    <h2 class="h3 title-block">Services</h2>
                    <div class="overflow-list-xs row">
                      <ul class="first check_boxes list-unstyled col-sm-3">
                        <li class="checkbox">
                          <label class="" for="l_f-1"  >
                            <input class="check_boxes" id="l_f-1" wire:model="services" name="services1[]" type="checkbox"  value="1">Anal Sex 
                          </label>
                        </li>
                        <li class="checkbox">
                          <label class="collection_check_boxes" for="l_f-28" unselectable="on" >
                            <input class="check_boxes" id="l_f-28" wire:model="services" name="services[]" type="checkbox"  value="2">BDSM </label>
                        </li>
                        <li class="checkbox">
                          <label class="collection_check_boxes" for="l_f-4" unselectable="on" >
                            <input class="check_boxes" id="l_f-4" wire:model='services' name="services[]" type="checkbox"  value="3">CIM - Come In Mouth </label>
                        </li>
                        <li class="checkbox">
                          <label class="collection_check_boxes" for="l_f-29" unselectable="on" >
                            <input class="check_boxes" id="l_f-29" wire:model='services' name="services[]" type="checkbox"  value="4">COB - Come On Body </label>
                        </li>
                        <li class="checkbox">
                          <label class="collection_check_boxes" for="l_f-8" unselectable="on" >
                            <input class="check_boxes" id="l_f-8" wire:model='services' name="services1[]" type="checkbox"  value="5">Couples </label>
                        </li>
                        <li class="checkbox">
                          <label class="collection_check_boxes" for="l_f-13" unselectable="on" >
                            <input class="check_boxes" id="l_f-13" wire:model='services' name="services1[]" type="checkbox"  value="6">Deep throat </label>
                        </li>
                        <li class="checkbox">
                          <label class="collection_check_boxes" for="l_f-9" unselectable="on" >
                            <input class="check_boxes" id="l_f-9" wire:model='services' name="services1[]" type="checkbox"  value="7">Domination </label>
                        </li>
                        <li class="checkbox">
                          <label class="collection_check_boxes" for="l_f-3" unselectable="on" >
                            <input class="check_boxes" id="l_f-3" wire:model='services' name="services1[]" type="checkbox" value="8">Face sitting </label>
                        </li>
                        <li class="checkbox">
                          <label class="collection_check_boxes" for="l_f-21" unselectable="on" >
                            <input class="check_boxes" id="l_f-21" wire:model='services' name="services1[]" type="checkbox"  value="9">Fingering </label>
                        </li>
                        <li class="checkbox">
                          <label class="collection_check_boxes" for="l_f-27" unselectable="on" >
                            <input class="check_boxes" id="l_f-27" wire:model='services' name="services1[]" type="checkbox"  value="10">Fisting </label>
                        </li>
                      </ul>
                      <ul class="check_boxes list-unstyled col-sm-3">
                        <li class="checkbox">
                          <label class="collection_check_boxes" for="l_f-22" unselectable="on" >
                            <input class="check_boxes" id="l_f-22" wire:model='services' name="services1[]" type="checkbox"  value="11">Foot fetish </label>
                        </li>
                        <li class="checkbox">
                          <label class="collection_check_boxes" for="l_f-12" unselectable="on" >
                            <input class="check_boxes" id="l_f-12" wire:model='services' name="services1[]" type="checkbox"  value="12">French kissing </label>
                        </li>
                        <li class="checkbox">
                          <label class="collection_check_boxes" for="l_f-31" unselectable="on" >
                            <input class="check_boxes" id="l_f-31" wire:model='services' name="services1[]" type="checkbox"  value="13">GFE </label>
                        </li>
                        <li class="checkbox">
                          <label class="collection_check_boxes" for="l_f-18" unselectable="on" >
                            <input class="check_boxes" id="l_f-18" wire:model='services' name="services[]" type="checkbox"  value="14">Giving hardsports </label>
                        </li>
                        <li class="checkbox">
                          <label class="collection_check_boxes" for="l_f-32" unselectable="on" >
                            <input class="check_boxes" id="l_f-32" wire:model='services' name="services[]" type="checkbox"  value="15">Receiving hardsports </label>
                        </li>
                        <li class="checkbox">
                          <label class="collection_check_boxes" for="l_f-5" unselectable="on" >
                            <input class="check_boxes" id="l_f-5" wire:model='services' name="services[]" type="checkbox"  value="16">Lap dancing </label>
                        </li>
                        <li class="checkbox">
                          <label class="collection_check_boxes" for="l_f-14" unselectable="on" >
                            <input class="check_boxes" id="l_f-14" wire:model='services' name="services[]" type="checkbox"  value="17">Massage </label>
                        </li>
                        <li class="checkbox">
                          <label class="collection_check_boxes" for="l_f-38" unselectable="on" >
                            <input class="check_boxes" id="l_f-38" wire:model='services' name="services[]" type="checkbox"  value="18">Nuru massage </label>
                        </li>
                        <li class="checkbox">
                          <label class="collection_check_boxes" for="l_f-35" unselectable="on" >
                            <input class="check_boxes" id="l_f-35" wire:model='services' name="services[]" type="checkbox"  value="19">Oral sex - blowjob </label>
                        </li>
                        <li class="checkbox">
                          <label class="collection_check_boxes" for="l_f-34" unselectable="on" >
                            <input class="check_boxes" id="l_f-34" wire:model='services' name="services[]" type="checkbox"   value="20">OWO - Oral without condom </label>
                        </li>
                      </ul>
                      <ul class="check_boxes list-unstyled col-sm-3">
                        <li class="checkbox">
                          <label class="collection_check_boxes" for="l_f-23" unselectable="on" >
                            <input class="check_boxes" id="l_f-23" wire:model='services' name="services[]" type="checkbox"  value="21">Parties </label>
                        </li>
                        <li class="checkbox">
                          <label class="collection_check_boxes" for="l_f-17" unselectable="on" >
                            <input class="check_boxes" id="l_f-17" wire:model='services' name="services[]" type="checkbox"  value="22">Reverse oral </label>
                        </li>
                        <li class="checkbox">
                          <label class="collection_check_boxes" for="l_f-24" unselectable="on" >
                            <input class="check_boxes" id="l_f-24" wire:model='services' name="services[]" type="checkbox"   value="23">Giving rimming </label>
                        </li>
                        <li class="checkbox">
                          <label class="collection_check_boxes" for="l_f-25" unselectable="on" >
                            <input class="check_boxes" id="l_f-25" wire:model='services' name="services[]" type="checkbox"   value="24">Rimming receiving </label>
                        </li>
                        <li class="checkbox">
                          <label class="collection_check_boxes" for="l_f-6" unselectable="on" >
                            <input class="check_boxes" id="l_f-6" wire:model='services' name="services[]" type="checkbox"   value="25">Role play </label>
                        </li>
                        <li class="checkbox">
                          <label class="collection_check_boxes" for="l_f-15" unselectable="on" >
                            <input class="check_boxes" id="l_f-15" wire:model='services' name="services[]" type="checkbox"   value="26">Sex toys </label>
                        </li>
                        <li class="checkbox">
                          <label class="collection_check_boxes" for="l_f-11" unselectable="on" >
                            <input class="check_boxes" id="l_f-11" wire:model='services' name="services[]" type="checkbox"   value="27">Spanking </label>
                        </li>
                        <li class="checkbox">
                          <label class="collection_check_boxes" for="l_f-2" unselectable="on" >
                            <input class="check_boxes" id="l_f-2" wire:model='services' name="services[]" type="checkbox"   value="28">Strapon </label>
                        </li>
                        <li class="checkbox">
                          <label class="collection_check_boxes" for="l_f-30" unselectable="on" >
                            <input class="check_boxes" id="l_f-30" wire:model='services' name="services[]" type="checkbox"   value="29">Striptease </label>
                        </li>
                      </ul>
                      <ul class="last check_boxes list-unstyled col-sm-3">
                        <li class="checkbox">
                          <label class="collection_check_boxes" for="l_f-7" unselectable="on" >
                            <input class="check_boxes" id="l_f-7" wire:model='services' name="services[]" type="checkbox"   value="30">Submissive </label>
                        </li>
                        <li class="checkbox">
                          <label class="collection_check_boxes" for="l_f-40" unselectable="on" >
                            <input class="check_boxes" id="l_f-40" wire:model='services' name="services[]" type="checkbox"  value="31">Squirting </label>
                        </li>
                        <li class="checkbox">
                          <label class="collection_check_boxes" for="l_f-37" unselectable="on" >
                            <input class="check_boxes" id="l_f-37" wire:model='services' name="services[]" type="checkbox"  value="32">Tantric massage </label>
                        </li>
                        <li class="checkbox">
                          <label class="collection_check_boxes" for="l_f-10" unselectable="on" >
                            <input class="check_boxes" id="l_f-10" wire:model='services' name="services[]" type="checkbox"  value="33">Teabagging </label>
                        </li>
                        <li class="checkbox">
                          <label class="collection_check_boxes" for="l_f-26" unselectable="on" >
                            <input class="check_boxes" id="l_f-26" wire:model='services' name="services[]" type="checkbox"  value="34">Tie and tease </label>
                        </li>
                        <li class="checkbox">
                          <label class="collection_check_boxes" for="l_f-16" unselectable="on" >
                            <input class="check_boxes" id="l_f-16" wire:model='services' name="services[]" type="checkbox"  value="3">Uniforms </label>
                        </li>
                        <li class="checkbox">
                          <label class="collection_check_boxes" for="l_f-19" unselectable="on" >
                            <input class="check_boxes" id="l_f-19" wire:model='services' name="services[]" type="checkbox"  value="36">Giving watersports </label>
                        </li>
                        <li class="checkbox">
                          <label class="collection_check_boxes" for="l_f-33" unselectable="on" >
                            <input class="check_boxes" id="l_f-33" wire:model='services' name="services[]"type="checkbox"  value="37">Receiving watersports </label>
                        </li>
                        <li class="checkbox">
                          <label class="collection_check_boxes" for="l_f-20" unselectable="on" >
                            <input class="check_boxes" id="l_f-20" wire:model='services' name="services[]" type="checkbox"  value="38">Webcam sex </label>
                        </li>
                      </ul>
                      <input name="listing[fetish_ids][]" type="hidden" value="">
                    </div>
                    <div class="clearfix"></div>
                  </div>
                  {{-- Mobile Pricing Section --}}
                  <div class="ev-mobile-pricing" style="display:none;">
                    <h2 class="h3 title-block">
                        <span style="color:#C1F11D;font-weight:700;font-size:18px;">$</span>
                        Pricing
                    </h2>
                    <div class="ev-mc-group">
                        <label>Currency</label>
                        <select data-radius="all" wire:model='incallcurr' class="apply-custom-select2 form-control">
                            @foreach($currencies as $curr)
                            <option value="{{$curr->code}}" @if($curr->code == 'AED') selected @endif>{{$curr->code}} ({{$curr->symbol ?? $curr->code}})</option>
                            @endforeach
                        </select>
                    </div>
                    <div style="display:flex;gap:12px;">
                        <div style="flex:1;">
                            <label style="display:block;color:#ccc;font-size:13px;margin-bottom:6px;">Incalls (per hour)</label>
                            <input wire:model='incallprice' class="form-control" type="number" placeholder="300">
                        </div>
                        <div style="flex:1;">
                            <label style="display:block;color:#ccc;font-size:13px;margin-bottom:6px;">Outcalls (per hour)</label>
                            <input wire:model='outcallprice' class="form-control" type="number" placeholder="400">
                        </div>
                    </div>
                  </div>
                  <style>
                    @media(max-width:768px){
                        /* Pricing wrapped as a single card */
                        .ev-mobile-pricing{
                            display:block!important;
                            background:#111!important;
                            border:1px solid #222!important;
                            border-radius:10px!important;
                            padding:18px!important;
                            margin:24px 0 16px!important;
                        }
                        /* Heading becomes internal header, not its own card */
                        .ev-mobile-pricing .h3.title-block,
                        .ev-mobile-pricing h2.h3.title-block{
                            background:transparent!important;
                            border:none!important;
                            border-radius:0!important;
                            padding:0!important;
                            margin:0 0 16px!important;
                            font-size:17px!important;
                            gap:12px!important;
                        }
                        /* $ glyph rendered as a green-tinted icon tile */
                        .ev-mobile-pricing .h3.title-block > span:first-child{
                            background:#283813;
                            border:1px solid #3a4a2a;
                            border-radius:8px;
                            width:36px;
                            height:36px;
                            display:inline-flex;
                            align-items:center;
                            justify-content:center;
                            box-sizing:border-box;
                            flex-shrink:0;
                            font-size:18px!important;
                        }
                        #fees{display:none!important}
                    }
                  </style>

                  <div data-outlier-price-check-url="/check_outlier_price" id="fees">
                    <h2 class="h3 title-block">Prices</h2>
                    <div class="row d-flex align-items-center">
                      <div class="col-xs-4 col-sm-2">
                        <div class="form-group boolean optional listing_incalls mb-0">
      
                          <label class="boolean optional control-label checkbox" for="listing_incalls">
                            <input class="boolean optional" wire:model='incall' type="checkbox" value="1" checked="checked" name="listing[incalls]" id="listing_incalls" />Incalls </label>
                        </div>
                      </div>
                      <div class="col-md-3 col-xs-8">
                        <div class="form-group price optional listing_incalls_price_per_hour">
                          <label class="price optional control-label" for="listing_incalls_price_per_hour">Per hour from:</label>
                          <div class="price-control" wire:ignore>
                            <input style="margin-right:5px" wire:model='incallprice' data-validations="numericality" class="string optional form-control validate price-amount form-control" type="text" name="listing[incalls_price_per_hour_amount]" id="listing_incalls_price_per_hour_amount" />
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
                            <input class="boolean optional" wire:model='outcall' type="checkbox" value="1" checked="checked" name="listing[outcalls]" id="listing_outcalls" />Outcalls </label>
                        </div>
                      </div>
                      <div class="col-md-3 col-xs-8">
                        <div class="form-group price optional listing_outcalls_price_per_hour">
                          <label class="price optional control-label" for="listing_outcalls_price_per_hour">Per hour from:</label>
                          <div class="price-control" wire:ignore>
                            <input style="margin-right:5px" wire:model='outcallprice' data-validations="numericality" class="string optional form-control validate price-amount form-control" type="text" name="listing[outcalls_price_per_hour_amount]" id="listing_outcalls_price_per_hour_amount" />
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
                  {{-- Mobile Personal Details --}}
                  <div class="ev-mobile-personal" style="display:none;">
                    <h2 class="h3 title-block">Personal Details</h2>
                    <div style="display:flex;gap:12px;margin-bottom:16px;">
                        <div style="flex:1;">
                            <label style="display:block;color:#ccc;font-size:13px;margin-bottom:6px;">Gender <span style="color:#f87171">*</span></label>
                            <select data-radius="all" wire:model='gender' class="apply-custom-select2 form-control">
                                <option value="">Select</option>
                                <option value="1">Female</option>
                                <option value="2">Male</option>
                                <option value="3">Trans</option>
                            </select>
                        </div>
                        <div style="flex:1;">
                            <label style="display:block;color:#ccc;font-size:13px;margin-bottom:6px;">Orientation</label>
                            <select data-radius="all" wire:model='ori' class="apply-custom-select2 form-control">
                                <option value="">Select</option>
                                <option value="1">Heterosexual</option>
                                <option value="2">Bisexual</option>
                                <option value="3">Lesbian or Gay</option>
                            </select>
                        </div>
                    </div>
                    <div class="ev-mc-group">
                        <label>Ethnicity</label>
                        <select data-radius="all" wire:model='ethnicity' class="apply-custom-select2 form-control">
                            <option value="">Select</option>
                            @foreach($ethnicities as $eth)
                            <option value="{{$eth->id}}">{{$eth->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div style="display:flex;gap:12px;margin-bottom:16px;">
                        <div style="flex:1;">
                            <label style="display:block;color:#ccc;font-size:13px;margin-bottom:6px;">Height (cm)</label>
                            <input wire:model='height' class="form-control" type="text" placeholder="175" maxlength="4">
                        </div>
                        <div style="flex:1;">
                            <label style="display:block;color:#ccc;font-size:13px;margin-bottom:6px;">Age <span style="color:#f87171">*</span></label>
                            <input wire:model='age' class="form-control" type="number" placeholder="25" min="18" max="60">
                        </div>
                    </div>
                    <div style="display:flex;gap:12px;margin-bottom:16px;">
                        <div style="flex:1;">
                            <label style="display:block;color:#ccc;font-size:13px;margin-bottom:6px;">Bust Size</label>
                            <select data-radius="all" wire:model='bust' class="apply-custom-select2 form-control">
                                <option value="">Select</option>
                                @foreach($busts as $bust)
                                <option value="{{$bust->id}}">{{$bust->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div style="flex:1;">
                            <label style="display:block;color:#ccc;font-size:13px;margin-bottom:6px;">Hair Color</label>
                            <select data-radius="all" wire:model='haircolor' class="apply-custom-select2 form-control">
                                <option value="">Select</option>
                                @foreach($hairs as $hair)
                                <option value="{{$hair->id}}">{{$hair->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                  </div>
                  {{-- Mobile Languages Spoken --}}
                  <div class="ev-mobile-languages" style="display:none;">
                    <h2 class="h3 title-block">Languages Spoken</h2>
                    <div class="ev-mc-group">
                        <select data-radius="all" wire:model='language1' class="apply-custom-select2 form-control" style="margin-bottom:10px;">
                            <option value="">Select language...</option>
                            @foreach($languages as $lang)
                            <option value="{{$lang->id}}">{{$lang->name}}</option>
                            @endforeach
                        </select>
                        <select data-radius="all" wire:model='language2' class="apply-custom-select2 form-control" style="margin-bottom:10px;">
                            <option value="">Select language...</option>
                            @foreach($languages as $lang)
                            <option value="{{$lang->id}}">{{$lang->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    {{-- Add the `apply-custom-select2` class on inserted selects
                         and re-run initializeCustomSelect2() so they get wrapped
                         the same way as language1 / language2. Without the
                         re-init, the newly-inserted <select> stays as a native
                         element and visually mismatches the first two. --}}
                    <button type="button" class="ev-add-lang-btn" onclick="this.previousElementSibling.insertAdjacentHTML('beforeend','<select data-radius=\'all\' class=\'apply-custom-select2 form-control\' style=\'margin-bottom:10px;\'><option value=\'\'>Select language...</option>@foreach($languages as $lang)<option value=\'{{$lang->id}}\'>{{$lang->name}}</option>@endforeach</select>');if(typeof initializeCustomSelect2===&quot;function&quot;)initializeCustomSelect2();">
                        + Add Language
                    </button>
                  </div>

                  {{-- Mobile Additional Information --}}
                  <div class="ev-mobile-additional" style="display:none;">
                    <h2 class="h3 title-block">Additional Information</h2>
                    <div class="ev-mc-group">
                        <label>Shaved</label>
                        <select data-radius="all" wire:model='shaved' class="apply-custom-select2 form-control">
                            <option value="">Select</option>
                            <option value="no">No</option>
                            <option value="partially">Partially</option>
                            <option value="yes">Yes</option>
                        </select>
                    </div>
                    <div class="ev-mc-group">
                        <label>Smoking</label>
                        <select data-radius="all" wire:model='smoke' class="apply-custom-select2 form-control">
                            <option value="">Select</option>
                            <option value="0">No</option>
                            <option value="1">Yes</option>
                        </select>
                    </div>
                    <div class="ev-mc-group">
                        <label>Add Video URL</label>
                        <input wire:model='video' class="form-control" type="text" placeholder="https://yourchannel.com">
                        <span style="color:#666;font-size:11px;margin-top:4px;display:block;">YouTube, Vimeo or other video platform URL</span>
                    </div>
                  </div>

                  <style>
                    @media(max-width:768px){
                        /* Personal Details wrapped as a single card */
                        .ev-mobile-personal{
                            display:block!important;
                            background:#111!important;
                            border:1px solid #222!important;
                            border-radius:10px!important;
                            padding:18px!important;
                            margin:24px 0 16px!important;
                        }
                        .ev-mobile-personal .h3.title-block,
                        .ev-mobile-personal h2.h3.title-block{
                            background:transparent!important;
                            border:none!important;
                            border-radius:0!important;
                            padding:0!important;
                            margin:0 0 16px!important;
                            font-size:17px!important;
                        }
                        /* Two-column rows (Gender|Orientation, Height|Age,
                           Bust Size|Hair Color): the select trigger sits in a
                           narrow flex:1 column and the default
                           `.custom-select2-dropdown { width: max-content;
                           max-width: 400px }` overflows to the right past the
                           card and even the viewport. Anchor the dropdown to
                           the row wrapper instead so it spans the full card
                           width and drops below the whole row. */
                        .ev-mobile-personal > div[style*="display:flex"]{
                            position:relative!important;
                        }
                        .ev-mobile-personal > div[style*="display:flex"] > div{
                            position:static!important;
                        }
                        .ev-mobile-personal > div[style*="display:flex"] .custom-select2{
                            position:static!important;
                        }
                        .ev-mobile-personal > div[style*="display:flex"] .custom-select2-dropdown{
                            left:0!important;
                            right:0!important;
                            width:auto!important;
                            min-width:0!important;
                            max-width:none!important;
                            top:calc(100% + 4px)!important;
                        }
                        .ev-mobile-personal > div[style*="display:flex"] .custom-select2-results{
                            max-height:min(48vh,260px)!important;
                        }
                        /* Languages Spoken wrapped as a single card */
                        .ev-mobile-languages{
                            display:block!important;
                            background:#111!important;
                            border:1px solid #222!important;
                            border-radius:10px!important;
                            padding:18px!important;
                            margin:24px 0 16px!important;
                        }
                        /* The inline `margin-bottom:10px` on each <select> is
                           hidden (display:none) after custom-select2 wraps it,
                           so the visible `.custom-select2` div carries no gap.
                           NOTE: the JS wraps each select+widget pair in a
                           `.custom-select2-holder` div (display:contents), so
                           every `.custom-select2` is the :last-child of its own
                           holder — a `.custom-select2:last-child { margin:0 }`
                           rule silently zeroes out spacing on every wrapper.
                           Space the holder instead. */
                        .ev-mobile-languages .ev-mc-group > .custom-select2-holder{
                            display:block!important;
                            margin-bottom:12px!important;
                        }
                        .ev-mobile-languages .ev-mc-group > .custom-select2-holder:last-of-type{
                            margin-bottom:0!important;
                        }
                        .ev-mobile-languages .ev-add-lang-btn{
                            margin-top:14px;
                        }
                        .ev-mobile-languages .h3.title-block,
                        .ev-mobile-languages h2.h3.title-block{
                            background:transparent!important;
                            border:none!important;
                            border-radius:0!important;
                            padding:0!important;
                            margin:0 0 16px!important;
                            font-size:17px!important;
                        }
                        /* Additional Information wrapped as a single card */
                        .ev-mobile-additional{
                            display:block!important;
                            background:#111!important;
                            border:1px solid #222!important;
                            border-radius:10px!important;
                            padding:18px!important;
                            margin:24px 0 16px!important;
                        }
                        .ev-mobile-additional .h3.title-block,
                        .ev-mobile-additional h2.h3.title-block{
                            background:transparent!important;
                            border:none!important;
                            border-radius:0!important;
                            padding:0!important;
                            margin:0 0 16px!important;
                            font-size:17px!important;
                        }
                        #about-me, #languages, #add-video, #video, #social{display:none!important}
                        /* Submit button: less wide, centered */
                        #submit, .btn-primary.btn-lg{
                            width:auto!important;
                            max-width:304px!important;
                            display:block!important;
                            margin:20px auto 0!important;
                            padding:14px 40px!important;
                        }
                        .ev-add-lang-btn{
                            width:100%;background:#111;border:1px solid #333;border-radius:5px;
                            color:#fff;font-size:14px;padding:12px;cursor:pointer;
                            display:flex;align-items:center;justify-content:center;gap:6px;
                        }
                    }
                  </style>

                  <div id="about-me">
                    <h2 class="h3 title-block">About me</h2>
                    <div class="form-group radio_buttons required listing_gender_id validate" data-validations="presenceAny">
                      <label class="radio_buttons required control-label">
                        Your gender (cannot be changed later) <span class="required-star">*</span></label>
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
                        <div class="form-group select optional listing_ethnicity_id" wire:ignore>
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
                          <label class="integer optional control-label" for="listing_age">Age <span class="required-star">*</span></label>
                          <input min="18" max="60" wire:model='age' class="numeric integer optional form-control  form-control" type="number" step="1" name="listing[age]" id="listing_age" />
                        </div>
                      </div>
                      <div class="col-md-2 col-sm-4">
                        <div class="form-group select optional listing_cup_size_id" wire:ignore>
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
                        <div class="form-group select optional listing_hair_color_id" wire:ignore>
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
                        <div class="form-group select optional listing_nationality_id" wire:ignore>
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

                        <div class="form-group select optional listing_listing_languages_language_id" wire:ignore>
                          <select data-radius="all" wire:model='language1'  class="apply-custom-select2 select select21 optional form-control" name="listing[listing_languages_attributes][1728906815958][language_id]" id="listing_listing_languages_attributes_1728906815958_language_id" tabindex="-1" title="">
                            <option value="">Select language...</option>
                           @foreach($languages as $lang)
                            <option value="{{$lang->id}}">{{$lang->name}}</option>
                           @endforeach
                          </select>
                        </div>
                        <div class="form-group radio_buttons optional listing_listing_languages_language_level_id">
                          <input type="hidden" name="listing[listing_languages_attributes][1728906815958][language_level_id]" value="">
                          <span class="radio-inline">
                            <label for="listing_listing_languages_attributes_1728906815958_language_level_id_4">
                              <input class="radio_buttons optional radio" wire:model="expert1" type="radio" value="Fluent" name="listing[listing_languages_attributes][1728906815958][language_level_id1]" id="listing_listing_languages_attributes_1728906815958_language_level_id_4">Fluent </label>
                          </span>
                          <span class="radio-inline">
                            <label for="listing_listing_languages_attributes_1728906815958_language_level_id_3">
                              <input class="radio_buttons optional radio" wire:model="expert1" type="radio" value="Good" name="listing[listing_languages_attributes][1728906815958][language_level_id1]" id="listing_listing_languages_attributes_1728906815958_language_level_id_3">Good </label>
                          </span>
                          <span class="radio-inline">
                            <label for="listing_listing_languages_attributes_1728906815958_language_level_id_1">
                              <input class="radio_buttons optional radio" wire:model="expert1" type="radio" value="Basic" name="listing[listing_languages_attributes][1728906815958][language_level_id1]" id="listing_listing_languages_attributes_1728906815958_language_level_id_1">Basic </label>
                          </span>
                        </div>
                      </div>
                      <div class="clearfix"></div>
                        <div  id="remove2">
                          <div id="remove" data="2" class="rm-lang-field fa fa-trash-alt fa-lg" data-language-index="1728906815958" rm-lang-field=""></div>
                          
                          <div class="form-group select optional listing_listing_languages_language_id" wire:ignore>
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
                              <label for="listing_listing_languages_attributes_1728906815958_language_level_id_4">
                                <input class="radio_buttons optional radio" wire:model="expert2" type="radio" value="Fluent" name="listing[listing_languages_attributes][1728906815958][language_level_id2]" id="listing_listing_languages_attributes_1728906815958_language_level_id_4">Fluent </label>
                            </span>
                            <span class="radio-inline">
                              <label for="listing_listing_languages_attributes_1728906815958_language_level_id_3">
                                <input class="radio_buttons optional radio" wire:model="expert2"  type="radio" value="Good" name="listing[listing_languages_attributes][1728906815958][language_level_id2]" id="listing_listing_languages_attributes_1728906815958_language_level_id_3">Good </label>
                            </span>
                            <span class="radio-inline">
                              <label for="listing_listing_languages_attributes_1728906815958_language_level_id_1">
                                <input class="radio_buttons optional radio" wire:model="expert2"  type="radio" value="Basic" name="listing[listing_languages_attributes][1728906815958][language_level_id2]" id="listing_listing_languages_attributes_1728906815958_language_level_id_1">Basic </label>
                            </span>
                          </div>
                        </div>
                        <div class="clearfix"></div>

                      <div  id="languagesappend"></div>
                      <a style="display: none" id="appendlang" add-language-btn="" class="add-language-btn btn btn-dark" data-template="&lt;div language-fields=&quot;&quot;&gt;&lt;div id=&quot;remove&quot; data=&quot;1&quot; class=&quot;rm-lang-field fa fa-trash-alt fa-lg&quot; data-language-index=&quot;new_record_tpl_id&quot; rm-lang-field=&quot;&quot;&gt;&lt;/div&gt;&lt;div class=&quot;form-group hidden listing_listing_languages__destroy&quot;&gt;&lt;input class=&quot;hidden destroy&quot; type=&quot;hidden&quot; wire:model='langss' value=&quot;false&quot; name=&quot;listing[listing_languages_attributes][new_record_tpl_id][_destroy]&quot; id=&quot;listing_listing_languages_attributes_new_record_tpl_id__destroy&quot; /&gt;&lt;/div&gt;&lt;div class=&quot;form-group select optional listing_listing_languages_language_id&quot;&gt;&lt;select data-blank-on-list=&quot;true&quot; class=&quot;select optional form-control&quot; name=&quot;listing[listing_languages_attributes][new_record_tpl_id][language_id]&quot; id=&quot;listing_listing_languages_attributes_new_record_tpl_id_language_id&quot;&gt;&lt;option value=&quot;&quot;&gt;Select language...&lt;/option&gt; &lt;option value=&quot;1&quot;&gt;Arabic&lt;/option&gt; &lt;option value=&quot;2&quot;&gt;Azerbaijani&lt;/option&gt; &lt;option value=&quot;3&quot;&gt;Bengali&lt;/option&gt; &lt;option value=&quot;4&quot;&gt;Bulgarian&lt;/option&gt; &lt;option value=&quot;5&quot;&gt;Catalan&lt;/option&gt; &lt;option value=&quot;6&quot;&gt;Czech&lt;/option&gt; &lt;option value=&quot;7&quot;&gt;Danish&lt;/option&gt; &lt;option value=&quot;8&quot;&gt;German&lt;/option&gt; &lt;option value=&quot;9&quot;&gt;Greek&lt;/option&gt; &lt;option value=&quot;10&quot;&gt;English&lt;/option&gt; &lt;option value=&quot;11&quot;&gt;Estonian&lt;/option&gt; &lt;option value=&quot;12&quot;&gt;Persian&lt;/option&gt; &lt;option value=&quot;13&quot;&gt;Finnish&lt;/option&gt; &lt;option value=&quot;14&quot;&gt;French&lt;/option&gt; &lt;option value=&quot;15&quot;&gt;Irish&lt;/option&gt; &lt;option value=&quot;16&quot;&gt;Gujarati&lt;/option&gt; &lt;option value=&quot;17&quot;&gt;Hebrew&lt;/option&gt; &lt;option value=&quot;18&quot;&gt;Hindi&lt;/option&gt; &lt;option value=&quot;19&quot;&gt;Hungarian&lt;/option&gt; &lt;option value=&quot;20&quot;&gt;Icelandic&lt;/option&gt; &lt;option value=&quot;21&quot;&gt;Italian&lt;/option&gt; &lt;option value=&quot;22&quot;&gt;Javanese&lt;/option&gt; &lt;option value=&quot;23&quot;&gt;Japanese&lt;/option&gt; &lt;option value=&quot;24&quot;&gt;Kannada&lt;/option&gt; &lt;option value=&quot;25&quot;&gt;Korean&lt;/option&gt; &lt;option value=&quot;62&quot;&gt;Laotian&lt;/option&gt; &lt;option value=&quot;26&quot;&gt;Latvian&lt;/option&gt; &lt;option value=&quot;27&quot;&gt;Lithuanian&lt;/option&gt; &lt;option value=&quot;28&quot;&gt;Malayalam&lt;/option&gt; &lt;option value=&quot;29&quot;&gt;Marathi&lt;/option&gt; &lt;option value=&quot;30&quot;&gt;Maltese&lt;/option&gt; &lt;option value=&quot;31&quot;&gt;Malay&lt;/option&gt; &lt;option value=&quot;32&quot;&gt;Dutch&lt;/option&gt; &lt;option value=&quot;33&quot;&gt;Norwegian&lt;/option&gt; &lt;option value=&quot;34&quot;&gt;Polish&lt;/option&gt; &lt;option value=&quot;35&quot;&gt;Portuguese&lt;/option&gt; &lt;option value=&quot;36&quot;&gt;Romanian&lt;/option&gt; &lt;option value=&quot;37&quot;&gt;Russian&lt;/option&gt; &lt;option value=&quot;38&quot;&gt;Slovak&lt;/option&gt; &lt;option value=&quot;39&quot;&gt;Spanish&lt;/option&gt; &lt;option value=&quot;40&quot;&gt;Swedish&lt;/option&gt; &lt;option value=&quot;41&quot;&gt;Tamil&lt;/option&gt; &lt;option value=&quot;42&quot;&gt;Telugu&lt;/option&gt; &lt;option value=&quot;43&quot;&gt;Thai&lt;/option&gt; &lt;option value=&quot;44&quot;&gt;Turkish&lt;/option&gt; &lt;option value=&quot;45&quot;&gt;Ukrainian&lt;/option&gt; &lt;option value=&quot;46&quot;&gt;Urdu&lt;/option&gt; &lt;option value=&quot;47&quot;&gt;Vietnamese&lt;/option&gt; &lt;option value=&quot;48&quot;&gt;Chinese&lt;/option&gt; &lt;option value=&quot;50&quot;&gt;Macedonian&lt;/option&gt; &lt;option value=&quot;51&quot;&gt;Punjabi&lt;/option&gt; &lt;option value=&quot;52&quot;&gt;Croatian&lt;/option&gt; &lt;option value=&quot;53&quot;&gt;Igbo&lt;/option&gt; &lt;option value=&quot;54&quot;&gt;Swahili&lt;/option&gt; &lt;option value=&quot;55&quot;&gt;Zulu&lt;/option&gt; &lt;option value=&quot;56&quot;&gt;Yoruba&lt;/option&gt; &lt;option value=&quot;57&quot;&gt;Indonesian&lt;/option&gt; &lt;option value=&quot;58&quot;&gt;Hausa&lt;/option&gt; &lt;option value=&quot;59&quot;&gt;Serbian&lt;/option&gt; &lt;option value=&quot;60&quot;&gt;Afrikaans&lt;/option&gt;&lt;/select&gt;&lt;/div&gt;&lt;div class=&quot;form-group radio_buttons optional listing_listing_languages_language_level_id&quot;&gt;&lt;input type=&quot;hidden&quot; name=&quot;listing[listing_languages_attributes][new_record_tpl_id][language_level_id]&quot; value=&quot;&quot; /&gt;&lt;span class=&quot;radio-inline&quot;&gt;&lt;label for=&quot;listing_listing_languages_attributes_new_record_tpl_id_language_level_id_4&quot;&gt;&lt;input class=&quot;radio_buttons optional radio&quot; type=&quot;radio&quot; value=&quot;4&quot; name=&quot;listing[listing_languages_attributes][new_record_tpl_id][language_level_id]&quot; id=&quot;listing_listing_languages_attributes_new_record_tpl_id_language_level_id_4&quot; /&gt;Fluent&lt;/label&gt;&lt;/span&gt;&lt;span class=&quot;radio-inline&quot;&gt;&lt;label for=&quot;listing_listing_languages_attributes_new_record_tpl_id_language_level_id_3&quot;&gt;&lt;input class=&quot;radio_buttons optional radio&quot; type=&quot;radio&quot; value=&quot;3&quot; name=&quot;listing[listing_languages_attributes][new_record_tpl_id][language_level_id]&quot; id=&quot;listing_listing_languages_attributes_new_record_tpl_id_language_level_id_3&quot; /&gt;Good&lt;/label&gt;&lt;/span&gt;&lt;span class=&quot;radio-inline&quot;&gt;&lt;label for=&quot;listing_listing_languages_attributes_new_record_tpl_id_language_level_id_1&quot;&gt;&lt;input class=&quot;radio_buttons optional radio&quot; type=&quot;radio&quot; value=&quot;1&quot; name=&quot;listing[listing_languages_attributes][new_record_tpl_id][language_level_id]&quot; id=&quot;listing_listing_languages_attributes_new_record_tpl_id_language_level_id_1&quot; /&gt;Basic&lt;/label&gt;&lt;/span&gt;&lt;/div&gt;&lt;/div&gt;&lt;div class=&quot;clearfix&quot;&gt;&lt;/div&gt;" href="#add-language">+ Add language</a>
                    </div>
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
                      <div class="row margin-bottom" style="padding-inline:0px !important">
                        <div class="col-sm-12">
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
                
                <button class="btn btn-xs-block btn-primary btn-lg margin-bottom"
                id="submit"
                type="submit"
                wire:loading.attr="disabled"
                wire:target="updateProfile">
            <span wire:loading.remove wire:target="updateProfile">Add Profile</span>
            <span wire:loading wire:target="updateProfile">
                <i class="fa fa-spinner fa-spin"></i> Processing...
            </span>
        </button>
        <p class="ev-terms-bottom" style="display:none;text-align:center;color:#888;font-size:13px;margin-top:12px;">By submitting, you agree to our <a href="/terms" style="color:#C1F11D;text-decoration:none;">Terms of Service</a></p>
        <style>@media(max-width:768px){.ev-terms-bottom{display:block!important}}</style>                
        @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif
            
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
              </form>
             
            </div>
            <input type="hidden" wire:model='num'>

          
          </div>
          @script
          <script>
// Custom Select2 Implementation
window.CustomSelect2 = class CustomSelect2 {
    constructor(selectElement, options = {}) {
        this.selectElement = selectElement;
        this.options = {
            placeholder: options.placeholder || 'Select...',
            searchable: options.searchable !== false,
            allowClear: options.allowClear || false,
            width: options.width || '100%',
            onChange: options.onChange || null
        };
        
        this.init();
    }
    
    init() {
        // Wrap the select + custom wrapper inside a wire:ignore holder so
        // Livewire's morph cannot descend into this subtree at all. Without
        // this, morph would either strip our JS-added wire:ignore/display
        // attributes or remove the sibling wrapper as an "extra child" —
        // both cause the visible dropdown-shake on the first commit and,
        // worse, leave the raw native <select> exposed.
        //
        // display:contents makes the holder invisible to CSS layout: its
        // children (the select and our wrapper) render exactly as if they
        // were direct children of the original parent, so no flex/grid
        // layout is disturbed.
        if (!this.selectElement.parentElement || !this.selectElement.parentElement.classList.contains('custom-select2-holder')) {
            const holder = document.createElement('div');
            holder.className = 'custom-select2-holder';
            holder.setAttribute('wire:ignore', '');
            holder.style.display = 'contents';
            this.selectElement.parentNode.insertBefore(holder, this.selectElement);
            holder.appendChild(this.selectElement);
        }
        this.holder = this.selectElement.parentElement;

        // Hide original select
        this.selectElement.style.display = 'none';

        // Create custom select container
        this.container = document.createElement('div');
        this.container.className = 'custom-select2';
        this.container.style.width = this.options.width;
        
        // Apply radius class from data-radius attribute
        const radiusClass = this.selectElement.getAttribute('data-radius');
        if (radiusClass) {
            this.container.classList.add('radius-' + radiusClass);
        }
        
        // Create selection box
        this.selectionBox = document.createElement('div');
        this.selectionBox.className = 'custom-select2-selection';
        this.selectionBox.innerHTML = `<span class="custom-select2-placeholder">${this.options.placeholder}</span>`;
        
        // Create dropdown
        this.dropdown = document.createElement('div');
        this.dropdown.className = 'custom-select2-dropdown';
        
        // Create search if enabled
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
        this.dropdown.appendChild(this.resultsList);
        
        // Append elements. The wrapper goes inside the wire:ignore holder,
        // right after the (hidden) original select.
        this.container.appendChild(this.selectionBox);
        this.container.appendChild(this.dropdown);
        this.holder.appendChild(this.container);
        
        // Populate options
        this.populateOptions();
        
        // Bind events
        this.bindEvents();
    }
    
    populateOptions() {
        this.resultsList.innerHTML = '';
        const options = this.selectElement.querySelectorAll('option');
        // Opt-in flag rendering — set data-with-flags="1" on the <select>
        // and data-iso="xx" on each <option> to enable.
        const withFlags = this.selectElement.getAttribute('data-with-flags') === '1';

        options.forEach((option, index) => {
            if (index === 0 && option.value === '') {
                return;
            }

            const li = document.createElement('li');
            li.className = 'custom-select2-option';
            li.dataset.value = option.value;

            const iso = (option.dataset.iso || '').toLowerCase();
            const name = option.dataset.name || '';
            const dial = option.dataset.dial || '';

            if (withFlags && iso) {
                li.classList.add('custom-select2-option--with-flag');
                li.dataset.iso = iso;
                li.dataset.dial = dial;
                li.innerHTML =
                    '<img class="custom-select2-flag" src="https://flagcdn.com/w40/' + iso + '.png" ' +
                    'alt="" width="22" height="16" loading="lazy">' +
                    '<span class="custom-select2-option-name">' + (name || option.textContent.trim()) + '</span>' +
                    (dial ? '<span class="custom-select2-option-dial">+' + dial + '</span>' : '');
            } else {
                li.textContent = option.textContent;
            }

            if (option.selected) {
                this.selectOption(li, false);
            }

            this.resultsList.appendChild(li);
        });
    }
    
    bindEvents() {
        // Toggle dropdown
        this.selectionBox.addEventListener('click', (e) => {
            e.stopPropagation();
            this.toggleDropdown();
        });
        
        // Search functionality
        if (this.searchInput) {
            this.searchInput.addEventListener('input', (e) => {
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
                this.selectOption(target);
                this.closeDropdown();
            }
        };
        
        this.resultsList.addEventListener('click', handleOptionSelect);

        // Track finger movement so a scroll gesture on mobile doesn't get
        // treated as an option tap. Without this, the user's finger lifts
        // on top of an option after scrolling → touchend fires → we select
        // that option and close the dropdown mid-scroll.
        let touchStartY = 0;
        let touchMoved = false;
        this.resultsList.addEventListener('touchstart', (e) => {
            touchStartY = e.touches[0]?.clientY || 0;
            touchMoved = false;
        }, { passive: true });
        this.resultsList.addEventListener('touchmove', (e) => {
            const y = e.touches[0]?.clientY || 0;
            if (Math.abs(y - touchStartY) > 8) touchMoved = true;
        }, { passive: true });
        this.resultsList.addEventListener('touchend', (e) => {
            if (touchMoved) return; // it was a scroll, not a tap
            handleOptionSelect(e);
        });
        
        // Close on outside click
        const outsideClickHandler = (e) => {
            // Don't close if clicking inside the dropdown or selection box
            if (!this.container.contains(e.target)) {
                this.closeDropdown();
            }
        };
        
        document.addEventListener('click', outsideClickHandler);
        
        // Store the handler so we can remove it later if needed
        this.outsideClickHandler = outsideClickHandler;
    }
    
    toggleDropdown() {
        const isOpen = this.dropdown.classList.contains('open');
        if (isOpen) {
            this.closeDropdown();
        } else {
            this.openDropdown();
        }
    }
    
    openDropdown() {
        // Close all other open dropdowns first
        document.querySelectorAll('.custom-select2.open').forEach(openContainer => {
            if (openContainer !== this.container) {
                openContainer.classList.remove('open');
                openContainer.querySelector('.custom-select2-dropdown')?.classList.remove('open');
                openContainer.querySelector('.custom-select2-selection')?.classList.remove('open');
            }
        });
        
        this.dropdown.classList.add('open');
        this.selectionBox.classList.add('open');
        this.container.classList.add('open');
        if (this.searchInput) {
            this.searchInput.value = '';
            this.searchInput.focus();
            this.filterOptions('');
        }
    }
    
    closeDropdown() {
        this.dropdown.classList.remove('open');
        this.selectionBox.classList.remove('open');
        this.container.classList.remove('open');
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
    
    selectOption(optionElement, triggerChange = true) {
        // Remove previous selection
        const previousSelected = this.resultsList.querySelector('.custom-select2-option.selected');
        if (previousSelected) {
            previousSelected.classList.remove('selected');
        }
        
        // Add new selection
        optionElement.classList.add('selected');

        // Closed-state display:
        // - Flag-enabled dropdowns → show <flag> +dial (compact for the
        //   narrow phone-code column).
        // - Otherwise → show the full option text so users can see what
        //   they picked (country name, currency name, etc.).
        const iso = optionElement.dataset.iso;
        const dial = optionElement.dataset.dial;
        if (iso && dial) {
            this.selectionBox.innerHTML =
                '<img class="custom-select2-flag" src="https://flagcdn.com/w40/' + iso + '.png" ' +
                'alt="" width="22" height="16" loading="lazy">' +
                '<span class="custom-select2-selection-code">+' + dial + '</span>';
        } else {
            this.selectionBox.textContent = optionElement.textContent.trim();
        }
        
        // Update original select
        this.selectElement.value = optionElement.dataset.value;
        
        // Trigger change event
        if (triggerChange) {
            const event = new Event('change', { bubbles: true });
            this.selectElement.dispatchEvent(event);
            
            // Call custom onChange if provided
            if (this.options.onChange) {
                this.options.onChange(optionElement.dataset.value);
            }
        }
    }
    
    destroy() {
        if (this.container && this.container.parentNode) {
            this.container.parentNode.removeChild(this.container);
        }
        this.selectElement.style.display = '';
    }
}

// Initialize custom select2 ONLY for selects with .apply-custom-select2 class
function initializeCustomSelect2() {
    // Find all selects with the class .apply-custom-select2
    const customSelects = document.querySelectorAll('select.apply-custom-select2');

    customSelects.forEach(select => {
        // Re-init if the previous instance's DOM was ripped out by Livewire
        // morph (the holder or wrapper is no longer connected to document).
        const inst = select.customSelect2Instance;
        const instanceIsLive = inst && inst.container && document.body.contains(inst.container);
        if (instanceIsLive) return;

        // Clear any stale instance reference before re-wrapping.
        select.customSelect2Instance = null;

        const wireModel = select.getAttribute('wire:model');
        const placeholder = select.querySelector('option[value=""]')?.textContent || 'Select...';

        select.customSelect2Instance = new CustomSelect2(select, {
            placeholder: placeholder,
            searchable: true,
            onChange: (value) => {
                // Sync the value into the native <select> and fire input/change
                // so wire:model captures it — but DO NOT call component.set(),
                // which would force an immediate Livewire commit → full DOM
                // morph → visible page "shake"/"reload" feel. wire:model is
                // deferred by default in Livewire 3: the value is sent with
                // the next real server action (e.g. form submit). That's all
                // we need for the form to submit correctly, without paying
                // for a round-trip on every dropdown selection.
                select.value = value;
                select.dispatchEvent(new Event('input', { bubbles: true }));
                select.dispatchEvent(new Event('change', { bubbles: true }));
            }
        });
    });
}

// Watchdog: Livewire's morph is stripping our custom-select2 wrappers and
// resetting `display:none` on the underlying <select>, even inside wire:ignore.
// A MutationObserver fires in a microtask (before the browser paints the
// next frame), so restoring the wrapping here happens without any visible
// flash — the user never sees the raw native <select>.
let _select2WatchdogInstalled = false;
function installSelect2Watchdog() {
    if (_select2WatchdogInstalled) return;
    _select2WatchdogInstalled = true;

    const root = document.querySelector('[wire\\:id]') || document.body;
    const observer = new MutationObserver(() => {
        // Fast-path: bail if every instance is still healthy.
        let needsRestore = false;
        document.querySelectorAll('select.apply-custom-select2').forEach(select => {
            const inst = select.customSelect2Instance;
            if (!inst || !inst.container || !document.body.contains(inst.container)) {
                needsRestore = true;
            } else if (select.style.display !== 'none') {
                // Morph reset our inline style — hide again.
                select.style.display = 'none';
            }
        });
        if (needsRestore) initializeCustomSelect2();
    });
    observer.observe(root, { childList: true, subtree: true });
}

// Destroy stale / orphaned custom select2 instances. Selects that are
// still wrapped correctly are left alone — otherwise every Livewire
// commit would tear down and rebuild every dropdown on the page, causing
// them all to visibly shake and briefly expose the native <select>.
function destroyCustomSelect2() {
    document.querySelectorAll('select.apply-custom-select2').forEach(select => {
        // Selects inside wire:ignore are never touched by Livewire morph,
        // so their wrapper is guaranteed intact — skip them entirely.
        if (select.closest('[wire\\:ignore]')) return;

        const wrapper = select.nextElementSibling;
        const wrapperOk = wrapper && wrapper.classList && wrapper.classList.contains('custom-select2');

        // Already wrapped and the wrapper is still adjacent → morph didn't
        // affect this one, leave the existing instance alive.
        if (select.customSelect2Instance && wrapperOk) return;

        // Otherwise the instance is stale (select was replaced by morph or
        // its wrapper was detached). Tear it down and let init rebuild it.
        if (select.customSelect2Instance) {
            try { select.customSelect2Instance.destroy(); } catch (e) {}
            select.customSelect2Instance = null;
        }
        select.style.display = '';
    });

    // Sweep leftover wrappers whose <select> is gone.
    document.querySelectorAll('.custom-select2').forEach(wrap => {
        const prev = wrap.previousElementSibling;
        if (!prev || prev.tagName !== 'SELECT' || !prev.classList.contains('apply-custom-select2')) {
            wrap.remove();
        }
    });
}

// Refresh function
function refreshCustomSelect2() {
    destroyCustomSelect2();
    initializeCustomSelect2();
}

// Initialize on different events
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing Custom Select2...');
    setTimeout(initializeCustomSelect2, 500);
});

window.addEventListener('load', function() {
    const firstSelect = document.getElementById('first_phone_code');
    if (firstSelect && !firstSelect.customSelect2Instance) {
        initializeCustomSelect2();
    }
});

// wire:navigate swaps the DOM without firing DOMContentLoaded/window.load
// or livewire:initialized again — those all ran on the previous page. The
// canonical Livewire 3 event that DOES fire after every wire:navigate
// page swap is `livewire:navigated`. Defer to the next tick so the new
// DOM is fully in place before we try to wrap the selects.
document.addEventListener('livewire:navigated', function () {
    setTimeout(function () {
        if (typeof initializeCustomSelect2 === 'function') {
            initializeCustomSelect2();
        }
        if (typeof installSelect2Watchdog === 'function') {
            installSelect2Watchdog();
        }
        ensureAddPhoneButton();
    }, 0);
});

// The legacy app2.js inserts the "+ Add another phone" button on initial
// document.ready. That doesn't fire on wire:navigate, so the button is
// missing after SPA navigation. Recreate it here (idempotent — bails if
// already present) so the button appears regardless of how the user got
// to this page.
function ensureAddPhoneButton() {
    if (document.getElementById('add-phone')) return; // already there
    var sections = document.querySelectorAll('.form-group.phone_number');
    if (sections.length < 2) return;
    var secondSection = sections[sections.length - 1];
    // Hide the second phone section by default (the user reveals it by
    // clicking the button we're about to insert).
    secondSection.style.display = 'none';

    var btn = document.createElement('a');
    btn.id = 'add-phone';
    btn.href = '#add-alternate-phone';
    btn.className = 'btn btn-dark';
    btn.textContent = '+ Add another phone';
    btn.addEventListener('click', function (e) {
        e.preventDefault();
        btn.style.display = 'none';
        secondSection.style.display = '';
    });
    secondSection.parentNode.insertBefore(btn, secondSection);
}

// Livewire integration. After Livewire morphs the DOM (file upload,
// validation refresh, etc.) every select that wasn't inside `wire:ignore`
// loses its CustomSelect2 wrapper and reverts to a native <select>.
// Multiple defensive hooks cover all the Livewire 3 lifecycle entry
// points where the DOM might change.
var _select2RefreshDebounce = null;
function scheduleSelect2Refresh(label) {
    clearTimeout(_select2RefreshDebounce);
    _select2RefreshDebounce = setTimeout(function () {
        console.log('[select2] refresh triggered by:', label);
        refreshCustomSelect2();
    }, 80);
}

if (typeof Livewire !== 'undefined') {
    document.addEventListener('livewire:initialized', () => {
        refreshCustomSelect2();

        // Install the DOM watchdog that immediately restores our wrappers if
        // Livewire's morph strips them. It fires in a microtask before paint,
        // so the raw <select> is never visible to the user.
        installSelect2Watchdog();

        try {
            if (typeof Livewire.hook === 'function') {
                // Best-effort: also try to prevent morph from removing the
                // wrapper in the first place. The watchdog above catches the
                // cases where this hook is bypassed.
                Livewire.hook('morph.removing', ({ el, skip }) => {
                    if (el && el.classList && el.classList.contains('custom-select2')) {
                        skip();
                    }
                });
            }
        } catch (e) { console.warn('[select2] could not bind Livewire.hook:', e); }

        // The component dispatches `fileUploaded` after a photo upload —
        // that re-renders large chunks of the DOM. Only refresh in that
        // specific case, not on every commit.
        try {
            Livewire.on('fileUploaded', () => scheduleSelect2Refresh('event:fileUploaded'));
        } catch (e) { console.warn('[select2] could not bind Livewire.on:', e); }
    });
}

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
            debounceMs: 150,
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
            this.watchForDomReplacement();
        },

        // Watch the wrapper for Livewire morphs that swap our input element
        // out from under us. Livewire 3 doesn't fire livewire:update, and the
        // morph replaces #citysearch with a fresh DOM node — our old listeners
        // stay bound to the detached element and every future keystroke is
        // lost. When we detect the swap, tear down and re-init against the
        // new element.
        watchForDomReplacement: function() {
            var self = this;
            var parent = this.input && this.input.parentElement;
            if (!parent || typeof MutationObserver === 'undefined') return;

            if (this._domObserver) { this._domObserver.disconnect(); }

            this._domObserver = new MutationObserver(function() {
                var current = document.getElementById('citysearch');
                if (current && current !== self.input) {
                    self.initialized = false;
                    self.input = null;
                    self.dropdown = null;
                    self.results = null;
                    if (self._domObserver) { self._domObserver.disconnect(); self._domObserver = null; }
                    self.init();
                }
            });
            this._domObserver.observe(parent, { childList: true, subtree: true });
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

            // Skip the ONE synthetic input event that handleSelect fires so
            // wire:model listeners see the new value. That echo used to
            // trigger our own re-search and leave lastQuery pointing at the
            // selected city, which blocked the dropdown on backspace+retype.
            // Uses a dedicated one-shot flag (NOT isSelecting) so any stuck
            // mousedown state can't lock the user out of typing.
            if (this._suppressInputEvent) {
                this._suppressInputEvent = false;
                return;
            }

            // Check minimum characters
            if (query.length < this.config.minChars) {
                this.hideDropdown();
                this.lastQuery = '';
                return;
            }

            // Don't search for same query — but ONLY if there are actual
            // results currently rendered. After a selection, results.innerHTML
            // is cleared, so we must always re-run in that case even if the
            // query somehow matches lastQuery.
            if (query === this.lastQuery
                && this.dropdown.style.display === 'block'
                && this.results.children.length > 0
                && !this.results.querySelector('[style*="Searching"]')) {
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

            // Not on an actual result item (e.g. clicked scrollbar or padding).
            // Reset isSelecting so a stray mousedown doesn't leave the blur
            // handler permanently disabled.
            if (!target) { this.isSelecting = false; return; }

            e.preventDefault();
            e.stopPropagation();

            var cityId = target.getAttribute('data-city-id');
            var cityName = target.getAttribute('data-city-name');
            var currencyCode = target.getAttribute('data-currency');

            if (!cityId || !cityName) { this.isSelecting = false; return; }
            
            // Update input field
            this.input.value = cityName;

            // Dispatch input event to trigger wire:model.lazy. Wrap with a
            // one-shot suppress flag so our own handleInput ignores this
            // synthetic dispatch (see the guard in handleInput).
            this._suppressInputEvent = true;
            var inputEvent = new Event('input', { bubbles: true });
            this.input.dispatchEvent(inputEvent);
            this._suppressInputEvent = false;
            
            // Update hidden field for city ID
            var hiddenInput = document.getElementById('selectedcityid');
            if (hiddenInput) {
                hiddenInput.value = cityId;
                // Dispatch input event to trigger wire:model.lazy on hidden field
                var hiddenInputEvent = new Event('input', { bubbles: true });
                hiddenInput.dispatchEvent(hiddenInputEvent);
            }
            
            // Auto-select currency based on city's country
            if (currencyCode) {
                this.updateCurrency(currencyCode);
            }
            
            // Update Livewire directly as fallback
            this.updateLivewire(cityId, cityName, currencyCode);
            
            // Cancel any queued search so the selection doesn't get overwritten
            // by a stale XHR that finishes after the click.
            if (this.debounceTimer) { clearTimeout(this.debounceTimer); this.debounceTimer = null; }
            if (this.currentXHR)   { this.currentXHR.abort();          this.currentXHR = null; }

            // Reset lastQuery so a later backspace+retype to the same value
            // still triggers a fresh search (otherwise the guard in handleInput
            // would short-circuit and the dropdown wouldn't reappear).
            this.lastQuery = '';

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
        
        // Re-init after Livewire updates (in case DOM changes). Detect BOTH
        // "input vanished" AND "input replaced with a fresh element" — the
        // latter happens after handleSelect calls component.set(), which
        // fires a server round-trip whose morph swaps our element. Our
        // stored CitySearch.input then points to a detached node with no
        // live listeners, and every subsequent keystroke goes into the void.
        document.addEventListener('livewire:update', function() {
            var domInput = document.getElementById('citysearch');
            if (!domInput || domInput !== CitySearch.input) {
                CitySearch.initialized = false;
                CitySearch.input = null;
                CitySearch.dropdown = null;
                CitySearch.results = null;
                initCitySearch();
            }
        });
    }
    
    // Expose for debugging
    window.CitySearch = CitySearch;

    // ----------------------------------------------------------------------
    // Bulletproof fallback: document-level event delegation. Any keystroke on
    // whatever #citysearch element is currently in the DOM will be handled,
    // even if Livewire has swapped the element and our direct listeners are
    // bound to a stale (detached) node. On every event we refresh references
    // to point at the CURRENT DOM elements.
    // ----------------------------------------------------------------------
    function refreshRefs(inputEl) {
        CitySearch.input = inputEl;
        CitySearch.dropdown = document.querySelector('.citys');
        CitySearch.results = document.getElementById('cityappend');
        CitySearch.initialized = true;
    }

    document.addEventListener('input', function(e) {
        if (!e.target || e.target.id !== 'citysearch') return;
        if (CitySearch.input !== e.target) refreshRefs(e.target);
        CitySearch.handleInput(e);
    }, true);

    document.addEventListener('keyup', function(e) {
        if (!e.target || e.target.id !== 'citysearch') return;
        if (CitySearch.input !== e.target) refreshRefs(e.target);
        CitySearch.handleInput(e);
    }, true);

    document.addEventListener('focus', function(e) {
        if (!e.target || e.target.id !== 'citysearch') return;
        if (CitySearch.input !== e.target) refreshRefs(e.target);
        var query = e.target.value.trim();
        if (query.length >= CitySearch.config.minChars) {
            CitySearch.handleInput(e);
        }
    }, true);

    document.addEventListener('mousedown', function(e) {
        var results = document.getElementById('cityappend');
        if (results && results.contains(e.target)) {
            e.preventDefault();
            CitySearch.isSelecting = true;
        }
    }, true);

    document.addEventListener('click', function(e) {
        var results = document.getElementById('cityappend');
        if (results && results.contains(e.target)) {
            var input = document.getElementById('citysearch');
            if (input && CitySearch.input !== input) refreshRefs(input);
            CitySearch.handleSelect(e);
        }
    }, true);

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
  
        var template = '<div id="remove'+num+'"> <div id="remove" data="'+num+'" class="rm-lang-field fa fa-trash-alt fa-lg" data-language-index="new_record_tpl_id" rm-lang-field=""></div> <div class="form-group hidden listing_listing_languages__destroy"> <input class="hidden destroy" type="hidden" value="false" name="listing[listing_languages_attributes][new_record_tpl_id][_destroy]" id="listing_listing_languages_attributes_new_record_tpl_id__destroy" /> </div> <div class="form-group select optional listing_listing_languages_language_id" wire:ignore> <select wire:model="language'+num+'" data-blank-on-list="true" data-radius="all" class="apply-custom-select2 select optional form-control" name="listing[listing_languages_attributes][new_record_tpl_id][language_id]" id="listing_listing_languages_attributes_new_record_tpl_id_language_id"> <option value="">Select language...</option> <option value="1">Arabic</option> <option value="2">Azerbaijani</option> <option value="3">Bengali</option> <option value="4">Bulgarian</option> <option value="5">Catalan</option> <option value="6">Czech</option> <option value="7">Danish</option> <option value="8">German</option> <option value="9">Greek</option> <option value="10">English</option> <option value="11">Estonian</option> <option value="12">Persian</option> <option value="13">Finnish</option> <option value="14">French</option> <option value="15">Irish</option> <option value="16">Gujarati</option> <option value="17">Hebrew</option> <option value="18">Hindi</option> <option value="19">Hungarian</option> <option value="20">Icelandic</option> <option value="21">Italian</option> <option value="22">Javanese</option> <option value="23">Japanese</option> <option value="24">Kannada</option> <option value="25">Korean</option> <option value="62">Laotian</option> <option value="26">Latvian</option> <option value="27">Lithuanian</option> <option value="28">Malayalam</option> <option value="29">Marathi</option> <option value="30">Maltese</option> <option value="31">Malay</option> <option value="32">Dutch</option> <option value="33">Norwegian</option> <option value="34">Polish</option> <option value="35">Portuguese</option> <option value="36">Romanian</option> <option value="37">Russian</option> <option value="38">Slovak</option> <option value="39">Spanish</option> <option value="40">Swedish</option> <option value="41">Tamil</option> <option value="42">Telugu</option> <option value="43">Thai</option> <option value="44">Turkish</option> <option value="45">Ukrainian</option> <option value="46">Urdu</option> <option value="47">Vietnamese</option> <option value="48">Chinese</option> <option value="50">Macedonian</option> <option value="51">Punjabi</option> <option value="52">Croatian</option> <option value="53">Igbo</option> <option value="54">Swahili</option> <option value="55">Zulu</option> <option value="56">Yoruba</option> <option value="57">Indonesian</option> <option value="58">Hausa</option> <option value="59">Serbian</option> <option value="60">Afrikaans</option> </select> </div> <div class="form-group radio_buttons optional listing_listing_languages_language_level_id"> <input type="hidden" name="listing[listing_languages_attributes][1728906815958][language_level_id]" value=""> <span class="radio-inline"> <label for="listing_listing_languages_attributes_1728906815958_language_level_id_4"> <input class="radio_buttons optional radio" wire:model="expert'+num+'" type="radio" value="Fluent" name="listing[listing_languages_attributes][1728906815958][language_level_id'+num+']" id="listing_listing_languages_attributes_1728906815958_language_level_id_4">Fluent </label> </span> <span class="radio-inline"> <label for="listing_listing_languages_attributes_1728906815958_language_level_id_3"> <input class="radio_buttons optional radio" wire:model="expert'+num+'" type="radio" value="Good" name="listing[listing_languages_attributes][1728906815958][language_level_id'+num+']" id="listing_listing_languages_attributes_1728906815958_language_level_id_3">Good </label> </span> <span class="radio-inline"> <label for="listing_listing_languages_attributes_1728906815958_language_level_id_1"> <input class="radio_buttons optional radio" type="radio" wire:model="expert'+num+'" value="1" name="listing[listing_languages_attributes][1728906815958][language_level_id'+num+']" id="listing_listing_languages_attributes_1728906815958_language_level_id_1">Basic </label> </span> </div> </div> <div class="clearfix"></div>';

    $("div#languagesappend").append(template);

    // Newly-appended language <select> is not yet a Select2 — initialize.
    // initializeCustomSelect2() is idempotent (skips selects that already
    // have a live wrapper), so this only wraps the just-inserted row.
    if (typeof initializeCustomSelect2 === 'function') {
        initializeCustomSelect2();
    }

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

   $("button#submit").click(function(e){
    // Sync all form values before submit (Safari/Mac fix)
    var phoneInput = document.getElementById('listing_phone_numbers_attributes_0_phone_digits');
    var cityIdInput = document.getElementById('selectedcityid');
    var cityNameInput = document.getElementById('citysearch');
    
    // Trigger change events to ensure wire:model.lazy syncs
    if (phoneInput && phoneInput.value) {
        phoneInput.dispatchEvent(new Event('change', { bubbles: true }));
        @this.set('phone', phoneInput.value);
    }
    
    if (cityIdInput && cityIdInput.value) {
        cityIdInput.dispatchEvent(new Event('change', { bubbles: true }));
        @this.set('city', cityIdInput.value);
    }
    
    if (cityNameInput && cityNameInput.value) {
        cityNameInput.dispatchEvent(new Event('change', { bubbles: true }));
        @this.set('selectedcity', cityNameInput.value);
    }
    
    @this.set('num', num);
   });

// Drag-drop init lives in a regular script tag at the bottom of this file
// (outside the Livewire script block) so any bug there cannot break the eval.

// Phone button with jQuery (if available)
if (typeof jQuery !== 'undefined') {
    jQuery(document).ready(function($) {
        let secondPhoneVisible = false;
        
        $('.second-phone-section').hide();
        
        $(document).on('click', '.add-second-phone', function(e) {
            e.preventDefault();
            e.stopPropagation();
            secondPhoneVisible = true;
            $('.second-phone-section').show();
            $(this).hide();
            
            // Initialize Custom Select2 for the second phone dropdown
            setTimeout(function() {
                const secondPhoneSelect = document.getElementById('second_phone_code');
                if (secondPhoneSelect && !secondPhoneSelect.customSelect2Instance) {
                    secondPhoneSelect.customSelect2Instance = new CustomSelect2(secondPhoneSelect, {
                        placeholder: 'Select code',
                        searchable: true,
                        onChange: (value) => {
                            const wireModel = secondPhoneSelect.getAttribute('wire:model');
                            if (wireModel && typeof Livewire !== 'undefined') {
                                @this.set(wireModel, value);
                            }
                        }
                    });
                    console.log('Initialized custom select2 for second phone');
                }
            }, 100);
        });
        
        // Prevent second phone section from hiding when clicking inside it
        $(document).on('click', '.second-phone-section', function(e) {
            e.stopPropagation();
        });
        
        // Prevent hiding when clicking on custom select2 elements
        $(document).on('click', '.custom-select2', function(e) {
            e.stopPropagation();
        });
        
        // Force second phone to stay visible if it was shown
        setInterval(function() {
            if (secondPhoneVisible && !$('.second-phone-section').is(':visible')) {
                $('.second-phone-section').show();
            }
        }, 100);
    });
}
   
// Image Drag and Drop Reordering
document.addEventListener('DOMContentLoaded', function() {
    let draggedElement = null;
    let draggedIndex = null;
    
    function initializeDragAndDrop() {
        const container = document.getElementById('image-container');
        if (!container) return;
        
        const imageCards = container.querySelectorAll('.record.image');
        
        imageCards.forEach((card, index) => {
            // Drag start
            card.addEventListener('dragstart', function(e) {
                draggedElement = this;
                draggedIndex = parseInt(this.getAttribute('data-index'));
                this.style.opacity = '0.5';
                e.dataTransfer.effectAllowed = 'move';
            });
            
            // Drag end
            card.addEventListener('dragend', function(e) {
                this.style.opacity = '';
                
                // Remove all drag-over classes
                imageCards.forEach(c => c.classList.remove('drag-over'));
            });
            
            // Drag over
            card.addEventListener('dragover', function(e) {
                e.preventDefault();
                e.dataTransfer.dropEffect = 'move';
                
                if (this !== draggedElement) {
                    this.classList.add('drag-over');
                }
                return false;
            });
            
            // Drag enter
            card.addEventListener('dragenter', function(e) {
                if (this !== draggedElement) {
                    this.classList.add('drag-over');
                }
            });
            
            // Drag leave
            card.addEventListener('dragleave', function(e) {
                this.classList.remove('drag-over');
            });
            
            // Drop
            card.addEventListener('drop', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                if (draggedElement !== this) {
                    // Get all current indices in order
                    const allCards = Array.from(container.querySelectorAll('.record.image'));
                    const currentOrder = allCards.map(c => parseInt(c.getAttribute('data-index')));
                    
                    // Remove dragged index from current position
                    currentOrder.splice(currentOrder.indexOf(draggedIndex), 1);
                    
                    // Get drop target index
                    const dropIndex = parseInt(this.getAttribute('data-index'));
                    const dropPosition = currentOrder.indexOf(dropIndex);
                    
                    // Insert at new position
                    currentOrder.splice(dropPosition, 0, draggedIndex);
                    
                    // Call Livewire method to reorder
                    @this.call('reorderImages', currentOrder);
                }
                
                this.classList.remove('drag-over');
                return false;
            });
        });
    }
    
    // Initialize on page load
    initializeDragAndDrop();

    // Reinitialize after Livewire updates
    document.addEventListener('livewire:update', function() {
        setTimeout(initializeDragAndDrop, 100);
    });

    // Listen for file uploaded event
    Livewire.on('fileUploaded', function() {
        setTimeout(initializeDragAndDrop, 100);
    });
});

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

// Listen for country code changes on first phone. Clear the value
// locally so the user sees an empty field for the new format, but do NOT
// dispatch input/change events — those would trigger a second Livewire
// commit right after the countrycode commit, causing double-morph and
// a visible reflow ("shake" / apparent page reload). The .inline-group
// wrapper has wire:ignore, so this local change persists across morphs.
$(document).on('change', '#first_phone_code', function() {
    const countryCode = $(this).val();
    const phoneInput = document.getElementById('listing_phone_numbers_attributes_0_phone_digits');
    if (phoneInput) phoneInput.value = '';
    updatePhoneMask(countryCode, 'listing_phone_numbers_attributes_0_phone_digits');
});

// Sync phone input with Livewire on blur (for Safari compatibility)
$(document).on('blur', '#listing_phone_numbers_attributes_0_phone_digits', function() {
    const value = this.value;
    // Dispatch change event to ensure wire:model.lazy syncs
    this.dispatchEvent(new Event('change', { bubbles: true }));
    // Also try direct Livewire sync as fallback
    try {
        @this.set('phone', value);
    } catch (e) {}
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

// Re-apply placeholder after Livewire updates
if (typeof Livewire !== 'undefined') {
    document.addEventListener('livewire:update', function() {
        setTimeout(function() {
            const firstCountryCode = $('#first_phone_code').val();
            if (firstCountryCode) {
                updatePhoneMask(firstCountryCode, 'listing_phone_numbers_attributes_0_phone_digits');
            }
            
            const secondCountryCode = $('#second_phone_code').val();
            if (secondCountryCode) {
                updatePhoneMask(secondCountryCode, 'listing_phone_numbers_attributes_1_phone_digits');
            }
        }, 50);
    });
}
            </script>
          @endscript

          {{-- Plain script tag (outside the Livewire script block) so any error here cannot break the eval. --}}
          <script>
          (function () {
              function initMphotoUpload() {
                  var dragDropArea = document.getElementById('drag-drop-area');
                  var fileInput = document.getElementById('mphoto');
                  if (!dragDropArea || !fileInput) {
                      setTimeout(initMphotoUpload, 300);
                      return;
                  }
                  if (fileInput.dataset.mphotoBound === '1') return;
                  fileInput.dataset.mphotoBound = '1';

                  console.log('[mphoto] init - Livewire?', typeof window.Livewire);
                  console.log('[mphoto] Livewire keys:', window.Livewire ? Object.keys(window.Livewire) : 'N/A');
                  console.log('[mphoto] livewire (lower) keys:', window.livewire ? Object.keys(window.livewire) : 'N/A');
                  var anyWireId = document.querySelector('[wire\\:id]');
                  console.log('[mphoto] any [wire\\:id] on page?', anyWireId, anyWireId ? anyWireId.getAttribute('wire:id') : null);
                  console.log('[mphoto] file input closest wire:id:', fileInput.closest('[wire\\:id]'));

                  function findComponent() {
                      var lw = window.Livewire || window.livewire;
                      if (!lw) return null;
                      var root = fileInput.closest('[wire\\:id]');
                      if (root && typeof lw.find === 'function') {
                          var c = lw.find(root.getAttribute('wire:id'));
                          if (c) return c;
                      }
                      try {
                          if (typeof lw.all === 'function') {
                              var all = lw.all();
                              console.log('[mphoto] Livewire.all() returned:', all);
                              if (all && all.length) return all[0];
                          }
                      } catch (e) { console.warn('[mphoto] all() threw', e); }
                      try {
                          if (typeof lw.first === 'function') {
                              var f = lw.first();
                              console.log('[mphoto] Livewire.first() returned:', f);
                              if (f) return f;
                          }
                      } catch (e) { console.warn('[mphoto] first() threw', e); }
                      // Last resort: any wire:id on page
                      if (anyWireId && typeof lw.find === 'function') {
                          var c2 = lw.find(anyWireId.getAttribute('wire:id'));
                          console.log('[mphoto] Livewire.find(anyWireId) returned:', c2);
                          if (c2) return c2;
                      }
                      return null;
                  }

                  // Resolve the CSRF token once.
                  var csrfMeta = document.querySelector('meta[name="csrf-token"]');
                  var csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

                  function postToFallbackRoute(file) {
                      var fd = new FormData();
                      fd.append('file', file);
                      return fetch({!! json_encode(route('listings.temp-image')) !!}, {
                          method: 'POST',
                          headers: {
                              'X-CSRF-TOKEN': csrfToken,
                              'Accept': 'application/json',
                              'X-Requested-With': 'XMLHttpRequest'
                          },
                          credentials: 'same-origin',
                          body: fd
                      }).then(function (r) {
                          if (!r.ok) {
                              return r.text().then(function (t) {
                                  throw new Error('upload http ' + r.status + ': ' + t.slice(0, 200));
                              });
                          }
                          return r.json();
                      });
                  }

                  function callLivewire(method, arg) {
                      var component = findComponent();
                      if (!component) return Promise.reject(new Error('no Livewire component'));
                      // Try the modern .call() API first, then fall back.
                      if (typeof component.call === 'function') {
                          return Promise.resolve(component.call(method, arg));
                      }
                      if (typeof component.$call === 'function') {
                          return Promise.resolve(component.$call(method, arg));
                      }
                      return Promise.reject(new Error('component has no call() method'));
                  }

                  function uploadFiles(fileList) {
                      var files = [];
                      for (var i = 0; i < (fileList ? fileList.length : 0); i++) {
                          var f = fileList[i];
                          if (f && f.type && f.type.indexOf('image/') === 0) files.push(f);
                      }
                      console.log('[mphoto] uploadFiles count:', files.length);
                      if (!files.length) return;

                      // If Livewire's native upload pipeline is wired up (wire:model
                      // works), let it handle the upload via its own bubble-phase
                      // change listener. We are in capture phase, so doing nothing
                      // here means the event still reaches Livewire normally.
                      var component = findComponent();
                      if (component && typeof component.uploadMultiple === 'function') {
                          console.log('[mphoto] native wire:model pipeline available, deferring to it');
                          return;
                      }

                      console.log('[mphoto] native pipeline missing, using fallback route');

                      // Fallback: post each file to the custom route, then tell the
                      // Livewire component to attach the resulting temp filename to
                      // $tempImages.
                      var i2 = 0;
                      function next() {
                          if (i2 >= files.length) {
                              try { fileInput.value = ''; } catch (e) {}
                              return;
                          }
                          var f = files[i2++];
                          console.log('[mphoto] posting file', f.name, f.size);
                          postToFallbackRoute(f)
                              .then(function (data) {
                                  console.log('[mphoto] uploaded, filename:', data.filename);
                                  return callLivewire('addUploadedTempFile', data.filename);
                              })
                              .then(function () {
                                  console.log('[mphoto] addUploadedTempFile done');
                                  next();
                              })
                              .catch(function (err) {
                                  console.error('[mphoto] upload chain failed:', err);
                                  next();
                              });
                      }
                      next();
                  }

                  fileInput.addEventListener('change', function (e) {
                      console.log('[mphoto] change fired, files:', e.target.files);
                      uploadFiles(e.target.files);
                  }, true);

                  dragDropArea.addEventListener('dragover', function (e) {
                      e.preventDefault(); e.stopPropagation();
                      this.classList.add('dragover');
                  });
                  dragDropArea.addEventListener('dragleave', function (e) {
                      e.preventDefault(); e.stopPropagation();
                      this.classList.remove('dragover');
                  });
                  dragDropArea.addEventListener('drop', function (e) {
                      e.preventDefault(); e.stopPropagation();
                      this.classList.remove('dragover');
                      uploadFiles(e.dataTransfer.files);
                  });
              }

              if (document.readyState === 'loading') {
                  document.addEventListener('DOMContentLoaded', initMphotoUpload);
              } else {
                  initMphotoUpload();
              }
          })();
          </script>

          {{-- Isolated drag-to-reorder using event delegation on document.
               Single set of listeners, never needs re-binding when Livewire
               morphs the DOM, works for cards added/removed after upload.
               Plain script tag outside the Livewire block so any error here
               cannot break the eval. Uses Livewire.dispatch paired with
               an On listener on the PHP side. All names prefixed evDrag*
               to avoid colliding with anything else. --}}
          <script>
          (function () {
              var dragged = null;

              function findCard(target) {
                  while (target && target.nodeType === 1) {
                      if (target.classList && target.classList.contains('record') && target.classList.contains('image')) {
                          // Exclude .record.image-input (the upload widget) which has class
                          // "record image-input new-img" - it lacks the bare "image" class
                          // because token matching in classList is by full token. Safe.
                          return target;
                      }
                      target = target.parentNode;
                  }
                  return null;
              }

              document.addEventListener('dragstart', function (e) {
                  var card = findCard(e.target);
                  if (!card) return;
                  // Only allow drag for cards inside our image container.
                  if (!card.parentNode || card.parentNode.id !== 'image-container') return;
                  dragged = card;
                  card.style.opacity = '0.5';
                  if (e.dataTransfer) {
                      e.dataTransfer.effectAllowed = 'move';
                      try { e.dataTransfer.setData('text/plain', card.getAttribute('data-index') || ''); } catch (err) {}
                  }
                  console.log('[evDrag] start', card.getAttribute('data-index'));
              }, true);

              document.addEventListener('dragend', function (e) {
                  var card = findCard(e.target);
                  if (card) card.style.opacity = '';
                  var container = document.getElementById('image-container');
                  if (container) {
                      container.querySelectorAll('.record.image').forEach(function (c) {
                          c.classList.remove('drag-over');
                      });
                  }
                  dragged = null;
              }, true);

              document.addEventListener('dragover', function (e) {
                  if (!dragged) return;
                  var card = findCard(e.target);
                  if (!card || card.parentNode.id !== 'image-container') return;
                  e.preventDefault();
                  if (e.dataTransfer) e.dataTransfer.dropEffect = 'move';
                  if (card !== dragged) card.classList.add('drag-over');
              }, true);

              document.addEventListener('dragleave', function (e) {
                  var card = findCard(e.target);
                  if (card) card.classList.remove('drag-over');
              }, true);

              document.addEventListener('drop', function (e) {
                  if (!dragged) return;
                  var card = findCard(e.target);
                  if (!card || card.parentNode.id !== 'image-container') return;
                  e.preventDefault();
                  e.stopPropagation();
                  card.classList.remove('drag-over');
                  if (dragged === card) { dragged = null; return; }

                  var container = card.parentNode;
                  var all = Array.prototype.slice.call(container.querySelectorAll('.record.image'));
                  var order = all.map(function (c) { return parseInt(c.getAttribute('data-index'), 10); });
                  var fromIdx = parseInt(dragged.getAttribute('data-index'), 10);
                  var toIdx = parseInt(card.getAttribute('data-index'), 10);

                  var fromPos = order.indexOf(fromIdx);
                  if (fromPos > -1) order.splice(fromPos, 1);
                  var toPos = order.indexOf(toIdx);
                  if (toPos < 0) toPos = order.length;
                  order.splice(toPos, 0, fromIdx);

                  console.log('[evDrag] drop', { from: fromIdx, to: toIdx, order: order });

                  if (window.Livewire && typeof window.Livewire.dispatch === 'function') {
                      window.Livewire.dispatch('reorderImages', { orderedIndexes: order });
                  } else {
                      console.warn('[evDrag] Livewire.dispatch not available');
                  }
                  dragged = null;
              }, true);

              console.log('[evDrag] delegated listeners attached');
          })();
          </script>

          {{-- Touch-based drag-to-reorder for mobile. HTML5 dragstart/dragover/
               drop above never fires on touch devices, so we replicate the
               flow with delegated touch* events on document. Long-press a card
               (~280ms hold) → drag mode → a floating clone follows the finger
               → release on another card dispatches `reorderImages` to Livewire,
               same payload shape as the desktop path. --}}
          <script>
          (function () {
              var dragged = null;
              var draggedIdx = null;
              var clone = null;
              var offX = 0, offY = 0;
              var startX = 0, startY = 0;
              var pressTimer = null;
              var active = false;
              var lastOver = null;
              var LONG_PRESS_MS = 280;
              var MOVE_TOLERANCE = 8;

              function findCard(target) {
                  while (target && target.nodeType === 1) {
                      if (target.classList && target.classList.contains('record') && target.classList.contains('image')
                          && target.parentNode && target.parentNode.id === 'image-container') {
                          return target;
                      }
                      target = target.parentNode;
                  }
                  return null;
              }

              function clearHighlights() {
                  var container = document.getElementById('image-container');
                  if (!container) return;
                  Array.prototype.forEach.call(container.querySelectorAll('.record.image.drag-over'), function (c) {
                      c.classList.remove('drag-over');
                  });
              }

              function cleanup() {
                  if (pressTimer) { clearTimeout(pressTimer); pressTimer = null; }
                  if (clone && clone.parentNode) clone.parentNode.removeChild(clone);
                  clone = null;
                  if (dragged) dragged.style.opacity = '';
                  clearHighlights();
                  document.body.style.overflow = '';
                  dragged = null;
                  draggedIdx = null;
                  active = false;
                  lastOver = null;
              }

              document.addEventListener('touchstart', function (e) {
                  // Skip taps on the Set-as-Main button or delete icon — they
                  // need their own click flow.
                  if (e.target.closest && (e.target.closest('.btn-set-main') || e.target.closest('.delete'))) return;
                  var card = findCard(e.target);
                  if (!card) return;

                  var t = e.touches[0];
                  startX = t.clientX;
                  startY = t.clientY;
                  dragged = card;
                  draggedIdx = parseInt(card.getAttribute('data-index'), 10);

                  pressTimer = setTimeout(function () {
                      if (!dragged) return;
                      active = true;
                      var rect = card.getBoundingClientRect();
                      offX = startX - rect.left;
                      offY = startY - rect.top;

                      clone = card.cloneNode(true);
                      clone.style.position = 'fixed';
                      clone.style.left = (startX - offX) + 'px';
                      clone.style.top = (startY - offY) + 'px';
                      clone.style.width = rect.width + 'px';
                      clone.style.height = rect.height + 'px';
                      clone.style.pointerEvents = 'none';
                      clone.style.opacity = '0.85';
                      clone.style.zIndex = '99999';
                      clone.style.transform = 'scale(1.05)';
                      clone.style.boxShadow = '0 8px 24px rgba(0,0,0,0.5)';
                      clone.style.margin = '0';
                      document.body.appendChild(clone);

                      card.style.opacity = '0.35';
                      document.body.style.overflow = 'hidden';
                      if (navigator.vibrate) { try { navigator.vibrate(15); } catch (_) {} }
                      console.log('[evTouchDrag] start', draggedIdx);
                  }, LONG_PRESS_MS);
              }, { passive: true, capture: true });

              document.addEventListener('touchmove', function (e) {
                  if (!dragged) return;
                  var t = e.touches[0];

                  if (!active) {
                      // Long-press hasn't fired yet — if finger moves too far,
                      // treat it as a scroll, abort.
                      if (Math.abs(t.clientX - startX) > MOVE_TOLERANCE || Math.abs(t.clientY - startY) > MOVE_TOLERANCE) {
                          if (pressTimer) { clearTimeout(pressTimer); pressTimer = null; }
                          dragged = null;
                          draggedIdx = null;
                      }
                      return;
                  }

                  // In active drag — block page scroll.
                  e.preventDefault();

                  if (clone) {
                      clone.style.left = (t.clientX - offX) + 'px';
                      clone.style.top = (t.clientY - offY) + 'px';
                  }

                  if (clone) clone.style.display = 'none';
                  var under = document.elementFromPoint(t.clientX, t.clientY);
                  if (clone) clone.style.display = '';

                  var overCard = under ? (function () {
                      var n = under;
                      while (n && n.nodeType === 1) {
                          if (n.classList && n.classList.contains('record') && n.classList.contains('image')
                              && n.parentNode && n.parentNode.id === 'image-container') return n;
                          n = n.parentNode;
                      }
                      return null;
                  })() : null;

                  if (overCard !== lastOver) {
                      clearHighlights();
                      if (overCard && overCard !== dragged) overCard.classList.add('drag-over');
                      lastOver = overCard;
                  }
              }, { passive: false, capture: true });

              document.addEventListener('touchend', function (e) {
                  if (!dragged || !active) { cleanup(); return; }

                  var t = (e.changedTouches && e.changedTouches[0]) || null;
                  var dropTarget = null;
                  if (t) {
                      if (clone) clone.style.display = 'none';
                      var under = document.elementFromPoint(t.clientX, t.clientY);
                      if (clone) clone.style.display = '';
                      var n = under;
                      while (n && n.nodeType === 1) {
                          if (n.classList && n.classList.contains('record') && n.classList.contains('image')
                              && n.parentNode && n.parentNode.id === 'image-container') { dropTarget = n; break; }
                          n = n.parentNode;
                      }
                  }

                  if (dropTarget && dropTarget !== dragged) {
                      var container = dropTarget.parentNode;
                      var all = Array.prototype.slice.call(container.querySelectorAll('.record.image'));
                      var order = all.map(function (c) { return parseInt(c.getAttribute('data-index'), 10); });
                      var fromIdx = draggedIdx;
                      var toIdx = parseInt(dropTarget.getAttribute('data-index'), 10);
                      var fromPos = order.indexOf(fromIdx);
                      if (fromPos > -1) order.splice(fromPos, 1);
                      var toPos = order.indexOf(toIdx);
                      if (toPos < 0) toPos = order.length;
                      order.splice(toPos, 0, fromIdx);

                      console.log('[evTouchDrag] drop', { from: fromIdx, to: toIdx, order: order });

                      if (window.Livewire && typeof window.Livewire.dispatch === 'function') {
                          window.Livewire.dispatch('reorderImages', { orderedIndexes: order });
                      } else {
                          console.warn('[evTouchDrag] Livewire.dispatch not available');
                      }
                  }
                  cleanup();
              }, { capture: true });

              document.addEventListener('touchcancel', cleanup, { capture: true });

              console.log('[evTouchDrag] delegated touch listeners attached');
          })();
          </script>
</div>{{-- /single-root --}}