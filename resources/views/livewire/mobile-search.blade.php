

@push('css')
<style>
/* Force Font Awesome icons to display */
.fa, .fas, .far, .fab, .fal {
    font-family: "Font Awesome 5 Free" !important;
    font-weight: 900 !important;
    -moz-osx-font-smoothing: grayscale;
    -webkit-font-smoothing: antialiased;
    display: inline-block;
    font-style: normal;
    font-variant: normal;
    text-rendering: auto;
    line-height: 1;
}

/* Specific icon content for FA5 compatibility */
.fa-caret-down:before {
    content: "\f0d7";
}

.fa-user:before {
    content: "\f007";
}

.fa-search:before {
    content: "\f002";
}

/* Fix gender dropdown button and icon */
.primary-search-gender .btn {
    position: relative;
    text-align: left;
}

.primary-search-gender .btn .fa {
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
}

/* Dark theme for all select boxes */
select.form-control,
select.select-box {
    background-color: #2c2c2c !important;
    color: #fff !important;
    border: 1px solid rgb(68 68 68) !important;
    border-radius: 4px !important;
    padding: 10px 12px !important;
    height: 44px !important;
    appearance: none !important;
    -webkit-appearance: none !important;
    -moz-appearance: none !important;
    background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23999' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e") !important;
    background-repeat: no-repeat !important;
    background-position: right 12px center !important;
    background-size: 16px !important;
    padding-right: 40px !important;
}

select.form-control option,
select.select-box option {
    background-color: #2c2c2c !important;
    color: #fff !important;
}

/* Select2 dropdown dark theme */
.select2-container--default .select2-selection--single {
    background-color: #2c2c2c !important;
    border: 1px solid rgb(68 68 68) !important;
    border-radius: 4px !important;
    height: 44px !important;
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
    color: #fff !important;
    line-height: 42px !important;
    padding-left: 12px !important;
}

.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 42px !important;
}

.select2-dropdown {
    background-color: #2c2c2c !important;
    border: 1px solid rgb(68 68 68) !important;
}

.select2-container--default .select2-results__option {
    background-color: #2c2c2c !important;
    color: #fff !important;
}

.select2-container--default .select2-results__option--highlighted[aria-selected] {
    background-color: #404040 !important;
    color: #fff !important;
}

.select2-container--default .select2-results__option[aria-selected=true] {
    background-color: #404040 !important;
}

.select2-container--default .select2-search--dropdown .select2-search__field {
    background-color: #333 !important;
    border: 1px solid rgb(68 68 68) !important;
    color: #fff !important;
}

/* Labels styling */
.form-group label {
    color: #fff !important;
    margin-bottom: 8px !important;

}
</style>
@endpush
<div>
    
    <style>
        html {
            font-family: sans-serif;
            -ms-text-size-adjust: 100%;
            -webkit-text-size-adjust: 100%;
            -webkit-tap-highlight-color: transparent;
            font-size: inherit
        }

        body,
        pre {
            line-height: 1.42857143
        }

        body {
            font: 14px/1.7 Helvetica, Arial, sans-serif;
            background: #333 url({{smart_asset('assets/images/web/pic.png')}}) top left;
            margin: 0;
            color: #fff;
            background-color: #333
        }

        footer,
        header,
        pre {
            display: block
        }

        a {
            background-color: transparent;
            color: #f4b827;
            text-decoration: none
        }

        a:active,
        a:hover {
            outline: 0
        }

        b,
        strong {
            font-weight: 700
        }

        h1 {
            font-weight: 500;
            line-height: 1.1;
            color: inherit;
            margin: 20px 0 10px;
            font-size: 36px
        }

        pre {
            overflow: auto;
            font-family: Monaco, Menlo, Consolas, "Courier New", monospace, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
            border-radius: 4px;
            padding: 9.5px;
            margin: 0 0 10px;
            font-size: 13px;
            word-break: break-all;
            word-wrap: break-word;
            color: #474747;
            background-color: #f5f5f5;
            border: 1px solid #ccc
        }

        button,
        input,
        select {
            color: inherit;
            font: inherit;
            margin: 0
        }

        button {
            overflow: visible;
            -webkit-appearance: button;
            cursor: pointer
        }

        button,
        select {
            text-transform: none
        }

        button::-moz-focus-inner,
        input::-moz-focus-inner {
            border: 0;
            padding: 0
        }

        input[type=checkbox] {
            -webkit-box-sizing: border-box;
            box-sizing: border-box;
            padding: 0;
            margin: 4px 0 0;
            margin-top: 1px \9;
            line-height: normal
        }

        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button {
            height: auto
        }

        *,
        :after,
        :before {
            -webkit-box-sizing: border-box;
            box-sizing: border-box
        }

        button,
        input,
        select {
            font-family: inherit;
            font-size: inherit;
            line-height: inherit
        }

        a:focus,
        a:hover {
            color: #dca623;
            text-decoration: underline
        }

        a:focus,
        input[type=checkbox]:focus {
            outline: 5px auto -webkit-focus-ring-color;
            outline-offset: -2px
        }

        [role=button] {
            cursor: pointer
        }

        p {
            margin: 0 0 10px
        }

        ul {
            margin-top: 0;
            margin-bottom: 10px
        }

        .list-inline {
            margin-left: -5px;
            padding-left: 0;
            list-style: none
        }

        .list-inline>li {
            display: inline-block;
            padding-left: 5px;
            padding-right: 5px
        }

        abbr[title] {
            cursor: help;
            border-bottom: 1px dotted #c2c2c2
        }

        .container-fluid {
            margin-right: auto;
            margin-left: auto;
            padding-left: 15px;
            padding-right: 15px
        }

        .container-fluid:after,
        .container-fluid:before {
            content: " ";
            display: table
        }

        .container-fluid:after {
            clear: both
        }

        .row {
            margin-left: -15px;
            margin-right: -15px
        }

        .row:after,
        .row:before {
            content: " ";
            display: table
        }

        .row:after {
            clear: both
        }

        .col-sm-3,
        .col-sm-6 {
            position: relative;
            min-height: 1px;
            padding-left: 15px;
            padding-right: 15px
        }

        @media (min-width:768px) {

            .col-sm-3,
            .col-sm-6 {
                float: left
            }

            .col-sm-3 {
                width: 25%
            }

            .col-sm-6 {
                width: 50%
            }
        }

        label {
            display: inline-block;
            max-width: 100%;
            margin-bottom: 5px;
            font-weight: 700
        }

        select[multiple] {
            height: auto
        }

        .form-control {
            display: block;
            font-size: 14px;
            line-height: 1.42857143;
            color: #3d3d3d;
            width: 100%;
            height: 34px;
            padding: 6px 12px;
            background-color: #fff;
            background-image: none;
            border: 1px solid #ccc;
            border-radius: 4px;
            -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075);
            box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075);
            -webkit-transition: border-color ease-in-out .15s, -webkit-box-shadow ease-in-out .15s;
            transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
            transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s, -webkit-box-shadow ease-in-out .15s
        }

        .form-control:focus {
            border-color: #66afe9;
            outline: 0;
            -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075), 0 0 8px rgba(102, 175, 233, .6);
            box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075), 0 0 8px rgba(102, 175, 233, .6)
        }

        .form-control::-webkit-input-placeholder {
            color: #777;
            opacity: 1
        }

        .form-control::-moz-placeholder {
            color: #777;
            opacity: 1
        }

        .form-control:-ms-input-placeholder,
        .form-control::-ms-input-placeholder {
            color: #777;
            opacity: 1
        }

        .form-control::-ms-expand {
            border: 0;
            background-color: transparent
        }

        .form-group {
            margin-bottom: 15px
        }

        .checkbox {
            position: relative;
            display: block;
            margin-top: 10px;
            margin-bottom: 10px
        }

        .checkbox input[type=checkbox] {
            margin-top: 4px \9;
            position: static;
            display: inline;
            margin-left: 0;
            margin-right: 10px
        }

        @media (min-width:768px) {
            .form-inline .form-group {
                display: inline-block;
                margin-bottom: 0;
                vertical-align: middle
            }

            .form-inline .form-control {
                display: inline-block;
                width: auto;
                vertical-align: middle
            }

            .form-inline .checkbox,
            .form-inline .control-label {
                margin-bottom: 0;
                vertical-align: middle
            }

            .form-inline .checkbox {
                display: inline-block;
                margin-top: 0
            }

            .form-inline .checkbox input[type=checkbox] {
                position: relative;
                margin-left: 0
            }
        }

        .btn {
            display: inline-block;
            margin-bottom: 0;
            font-weight: 400;
            text-align: center;
            vertical-align: middle;
            -ms-touch-action: manipulation;
            touch-action: manipulation;
            cursor: pointer;
            background-image: none;
            border: 1px solid transparent;
            white-space: nowrap;
            padding: 6px 12px;
            font-size: 14px;
            line-height: 1.42857143;
            border-radius: 4px;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none
        }

        .btn:active:focus,
        .btn:focus {
            outline: 5px auto -webkit-focus-ring-color;
            outline-offset: -2px
        }

        .btn:focus,
        .btn:hover {
            color: #333;
            text-decoration: none
        }

        .btn:active {
            outline: 0
        }

        .btn-primary {
            color: #333;
            background-color: #f4b827
        }

        .btn-primary:focus {
            color: #333;
            border-color: #7c5906
        }

        .btn-primary:active {
            color: #333
        }

        .btn-primary:hover {
            color: #333;
            background-color: #dd9f0b;
            border-color: #bb870a
        }

        .btn-primary:active:focus,
        .btn-primary:active:hover {
            color: #333;
            background-color: #bb870a;
            border-color: #7c5906
        }

        .btn-primary:active {
            background-image: none
        }

        .btn-block {
            display: block;
            width: 100%
        }

        .dropdown {
            position: relative
        }

        .dropdown-menu {
            position: absolute;
            top: 100%;
            left: 0;
            display: none;
            float: left;
            min-width: 160px;
            padding: 5px 0;
            margin: 2px 0 0;
            list-style: none;
            font-size: 14px;
            text-align: left;
            background-color: #fff;
            border: 1px solid #ccc;
            border: 1px solid rgba(0, 0, 0, .15);
            border-radius: 4px;
            -webkit-box-shadow: 0 6px 12px rgba(0, 0, 0, .175);
            box-shadow: 0 6px 12px rgba(0, 0, 0, .175);
            background-clip: padding-box
        }

        .dropdown-menu>li>a {
            display: block;
            padding: 3px 20px;
            clear: both;
            font-weight: 400;
            line-height: 1.42857143;
            color: #474747;
            white-space: nowrap
        }

        .dropdown-menu>li>a:focus,
        .dropdown-menu>li>a:hover {
            text-decoration: none;
            color: #3a3a3a;
            background-color: #f5f5f5
        }

        .dropdown-menu>.active>a,
        .dropdown-menu>.active>a:focus,
        .dropdown-menu>.active>a:hover {
            color: #fff;
            text-decoration: none;
            outline: 0;
            background-color: #f4b827
        }

        .nav:after,
        .nav:before {
            content: " ";
            display: table
        }

        .nav:after {
            clear: both
        }

        .nav {
            margin-bottom: 0;
            padding-left: 0;
            list-style: none
        }

        .nav>li,
        .nav>li>a {
            position: relative;
            display: block
        }

        .nav>li>a {
            padding: 10px 15px
        }

        .nav>li>a:focus,
        .nav>li>a:hover {
            text-decoration: none;
            background-color: #333
        }

        .nav-pills>li {
            float: left
        }

        .nav-pills>li>a {
            border-radius: 4px
        }

        .nav-pills>li+li {
            margin-left: 2px
        }

        .nav-pills>li.active>a,
        .nav-pills>li.active>a:focus,
        .nav-pills>li.active>a:hover {
            color: #fff;
            background-color: #f4b827
        }

        .nav-stacked>li {
            float: none
        }

        .nav-stacked>li+li {
            margin-top: 2px;
            margin-left: 0
        }

        .navbar {
            position: relative;
            min-height: 50px;
            margin-bottom: 20px;
            border: 1px solid transparent
        }

        .navbar:after,
        .navbar:before {
            content: " ";
            display: table
        }

        .navbar:after {
            clear: both
        }

        @media (min-width:768px) {
            .navbar {
                border-radius: 4px
            }
        }

        .navbar-header:after,
        .navbar-header:before {
            content: " ";
            display: table
        }

        .navbar-header:after {
            clear: both
        }

        @media (min-width:768px) {
            .navbar-header {
                float: left
            }
        }

        .container-fluid>.navbar-header {
            margin-right: -15px;
            margin-left: -15px
        }

        @media (min-width:768px) {
            .container-fluid>.navbar-header {
                margin-right: 0;
                margin-left: 0
            }
        }

        .navbar-brand {
            float: left;
            padding: 15px;
            font-size: 18px;
            line-height: 20px;
            height: 50px
        }

        .navbar-brand:focus,
        .navbar-brand:hover {
            text-decoration: none
        }

        @media (min-width:768px) {
            .navbar>.container-fluid .navbar-brand {
                margin-left: -15px
            }
        }

        .navbar-inverse {
            background-color: #222;
            border-color: #090909
        }

        .navbar-inverse .navbar-brand {
            color: #e8e8e8
        }

        .navbar-inverse .navbar-brand:focus,
        .navbar-inverse .navbar-brand:hover {
            color: #fff;
            background-color: transparent
        }

        @-ms-viewport {
            width: device-width
        }

        @media (max-width:767px) {
            .hidden-xs {
                display: none !important
            }
        }

        .fa {
            display: inline-block;
            font-family: 'FontAwesome' !important;
            speak: none;
            font-style: normal;
            font-weight: 400;
            font-variant: normal;
            text-transform: none;
            line-height: 1;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale
        }

        .fa-fw {
            width: 1.28571429em;
            text-align: center
        }

        .dropdown-menu {
            z-index: 1002
        }

        @media (max-width:767px) {
            .sr-only-xs {
                position: absolute;
                width: 1px;
                height: 1px;
                padding: 0;
                overflow: hidden;
                clip: rect(0, 0, 0, 0);
                white-space: nowrap;
                -webkit-clip-path: inset(50%);
                clip-path: inset(50%);
                border: 0
            }
        }

        .margin-top {
            margin-top: 20px
        }

        .header-nav-buttons>form.button_to {
            display: inline-block
        }

        @media (max-width:767px) {
            h1 {
                font-size: 21px;
                margin-bottom: 8px
            }
        }

        .content-wrapper.no-sidebar #content {
            position: relative;
            float: left;
            width: 100%;
            min-height: 1px
        }

        #content {
            padding-bottom: 10px
        }

        h1 {
            font-family: Helvetica, Arial, sans-serif
        }

        form input::-webkit-input-placeholder {
            color: #a9a8a8
        }

        form input::-moz-placeholder {
            color: #a9a8a8
        }

        form input:-ms-input-placeholder,
        form input::-ms-input-placeholder {
            color: #a9a8a8
        }

        .dark-form .btn-dark,
        .dark-form .form-control,
        .dark-form .select-box,
        .dark-form .twitter-typeahead {
            background-color: #333;
            background: -webkit-gradient(linear, left top, left bottom, from(#222), to(#333));
            background: linear-gradient(#222, #333);
            text-shadow: 0 -1px 0 rgba(0, 0, 0, .2);
            color: #e6e6e6;
            border: 1px solid #555
        }

        .dark-form .btn-dark::-webkit-input-placeholder,
        .dark-form .form-control::-webkit-input-placeholder,
        .dark-form .select-box::-webkit-input-placeholder,
        .dark-form .select2-container .select2-input::-webkit-input-placeholder {
            color: #e6e6e6;
            opacity: 1
        }

        .dark-form .btn-dark::-moz-placeholder,
        .dark-form .form-control::-moz-placeholder,
        .dark-form .select-box::-moz-placeholder,
        .dark-form .select2-container .select2-input::-moz-placeholder {
            color: #e6e6e6;
            opacity: 1
        }

        .dark-form .btn-dark:-ms-input-placeholder,
        .dark-form .btn-dark::-ms-input-placeholder,
        .dark-form .form-control:-ms-input-placeholder,
        .dark-form .form-control::-ms-input-placeholder,
        .dark-form .select-box:-ms-input-placeholder,
        .dark-form .select-box::-ms-input-placeholder {
            color: #e6e6e6;
            opacity: 1
        }

        .dark-form .btn-dark:disabled,
        .dark-form .form-control:disabled,
        .dark-form .select-box:disabled {
            background: #262626;
            -webkit-transition: none;
            transition: none;
            opacity: .65
        }

        .dark-form .btn-dark:active,
        .dark-form .btn-dark:focus,
        .dark-form .btn-dark:hover,
        .dark-form .form-control:active,
        .dark-form .form-control:focus,
        .dark-form .form-control:hover,
        .dark-form .select-box:active,
        .dark-form .select-box:focus,
        .dark-form .select-box:hover {
            background: #333;
            -webkit-box-shadow: inset 0 3px 5px rgba(0, 0, 0, .125);
            box-shadow: inset 0 3px 5px rgba(0, 0, 0, .125)
        }

        .dark-form .btn-dark:disabled,
        .dark-form .btn-dark:readonly,
        .dark-form .combobox-box input:readonly,
        .dark-form .dropdown-element:readonly,
        .dark-form .form-control:disabled,
        .dark-form .form-control:readonly,
        .dark-form .input-group-addon:readonly,
        .dark-form .my-listings-nav>.my-listing-nav-link-listing:readonly,
        .dark-form .select-box:disabled,
        .dark-form .select-box:readonly {
            background: #262626;
            -webkit-transition: none;
            transition: none;
            opacity: .65;
            border: 1px solid #555
        }

        .dark-form .select2-container .select2-input {
            border-color: rgba(255, 255, 255, .1);
            background-color: #333;
            background: -webkit-gradient(linear, left top, left bottom, from(#222), to(#333));
            background: linear-gradient(#222, #333);
            text-shadow: 0 -1px 0 rgba(0, 0, 0, .2);
            color: #e6e6e6
        }

        .dark-form .select2-container .select2-input:-ms-input-placeholder,
        .dark-form .select2-container .select2-input::-ms-input-placeholder {
            color: #e6e6e6;
            opacity: 1
        }

        .dark-form .select2-container .select2-input:disabled {
            background: #262626;
            -webkit-transition: none;
            transition: none;
            opacity: .65
        }

        .dark-form .select2-container .select2-input:active,
        .dark-form .select2-container .select2-input:focus,
        .dark-form .select2-container .select2-input:hover {
            background: #333;
            -webkit-box-shadow: inset 0 3px 5px rgba(0, 0, 0, .125);
            box-shadow: inset 0 3px 5px rgba(0, 0, 0, .125)
        }

        .dark-form .select2-container .select2-choice,
        .dark-form .select2-container .select2-choices {
            background-color: #333;
            background: -webkit-gradient(linear, left top, left bottom, from(#222), to(#333));
            background: linear-gradient(#222, #333);
            text-shadow: 0 -1px 0 rgba(0, 0, 0, .2);
            color: #e6e6e6;
            border: 1px solid #555
        }

        .dark-form .select2-container .select2-choice::-webkit-input-placeholder,
        .dark-form .select2-container .select2-choices::-webkit-input-placeholder,
        .dark-form .tt-input::-webkit-input-placeholder {
            color: #e6e6e6;
            opacity: 1
        }

        .dark-form .select2-container .select2-choices .select2-search-field>input::-webkit-input-placeholder {
            opacity: 1
        }

        .dark-form .select2-container .select2-choice::-moz-placeholder,
        .dark-form .select2-container .select2-choices::-moz-placeholder,
        .dark-form .tt-input::-moz-placeholder {
            color: #e6e6e6;
            opacity: 1
        }

        .dark-form .select2-container .select2-choices .select2-search-field>input::-moz-placeholder {
            opacity: 1
        }

        .dark-form .select2-container .select2-choice:-ms-input-placeholder,
        .dark-form .select2-container .select2-choice::-ms-input-placeholder,
        .dark-form .select2-container .select2-choices:-ms-input-placeholder,
        .dark-form .select2-container .select2-choices::-ms-input-placeholder,
        .dark-form .tt-input:-ms-input-placeholder,
        .dark-form .tt-input::-ms-input-placeholder {
            color: #e6e6e6;
            opacity: 1
        }

        .dark-form .select2-container .select2-choices .select2-search-field>input:-ms-input-placeholder,
        .dark-form .select2-container .select2-choices .select2-search-field>input::-ms-input-placeholder {
            opacity: 1
        }

        .dark-form .select2-container .select2-choice:disabled,
        .dark-form .select2-container .select2-choices .select2-search-field>input:disabled,
        .dark-form .select2-container .select2-choices:disabled {
            background: #262626;
            -webkit-transition: none;
            transition: none;
            opacity: .65
        }

        .dark-form .tt-input:disabled {
            background: #262626;
            -webkit-transition: none;
            transition: none
        }

        .dark-form .select2-container .select2-choice:active,
        .dark-form .select2-container .select2-choice:focus,
        .dark-form .select2-container .select2-choice:hover,
        .dark-form .select2-container .select2-choices .select2-search-field>input:active,
        .dark-form .select2-container .select2-choices .select2-search-field>input:focus,
        .dark-form .select2-container .select2-choices .select2-search-field>input:hover,
        .dark-form .select2-container .select2-choices:active,
        .dark-form .select2-container .select2-choices:focus,
        .dark-form .select2-container .select2-choices:hover,
        .dark-form .tt-input:active,
        .dark-form .tt-input:focus,
        .dark-form .tt-input:hover {
            background: #333;
            -webkit-box-shadow: inset 0 3px 5px rgba(0, 0, 0, .125);
            box-shadow: inset 0 3px 5px rgba(0, 0, 0, .125)
        }

        .dark-form .select2-container .select2-choice .select2-arrow {
            border: 0
        }

        .dark-form .select2-container .select2-choices .select2-search-field>input {
            background-color: #333;
            background: -webkit-gradient(linear, left top, left bottom, from(#222), to(#333));
            background: linear-gradient(#222, #333);
            text-shadow: 0 -1px 0 rgba(0, 0, 0, .2);
            color: #e6e6e6 !important
        }

        .dark-form .select2-container .select2-choices .select2-search-field>input::-webkit-input-placeholder {
            color: #e6e6e6 !important
        }

        .dark-form .select2-container .select2-choices .select2-search-field>input::-moz-placeholder {
            color: #e6e6e6 !important
        }

        .dark-form .select2-container .select2-choices .select2-search-field>input:-ms-input-placeholder,
        .dark-form .select2-container .select2-choices .select2-search-field>input::-ms-input-placeholder {
            color: #e6e6e6 !important
        }

        .dark-form .tt-input {
            background-color: #333;
            background: -webkit-gradient(linear, left top, left bottom, from(#222), to(#333));
            background: linear-gradient(#222, #333);
            text-shadow: 0 -1px 0 rgba(0, 0, 0, .2);
            color: #e6e6e6
        }

        .dark-form .tt-input:active,
        .dark-form .tt-input:hover {
            background-color: #333 !important
        }

        .dark-form .tt-input:disabled {
            background-color: #262626 !important;
            opacity: .65 !important
        }

        .btn.btn-dark {
            font-family: "Helvetica Neue", Helvetica, Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji"
        }

        #header .nav-bar {
            margin-bottom: 0
        }

        #header .nav-bar .back-link {
            float: left
        }

        .header-nav-buttons {
            float: right
        }

        @media (min-width:768px) {
            .header-nav-buttons {
                position: absolute;
                right: 15px
            }
        }

        @media (max-width:767px) {
            .header-nav-buttons {
                margin-top: 5px;
                margin-right: -10px
            }
        }

        .header-nav-buttons>form.button_to:last-child>.btn-navbar-header {
            border-top-right-radius: 4px;
            border-bottom-right-radius: 4px
        }

        .header-nav-buttons .btn-navbar-header {
            border-radius: 0;
            border-left-width: 0;
            color: #fff
        }

        .header-nav-buttons .btn-navbar-header:hover {
            color: #fff
        }

        .header-nav-buttons .btn-navbar-header:first-child {
            border-top-left-radius: 4px;
            border-bottom-left-radius: 4px;
            border-left-width: 1px
        }

        .btn-navbar-header {
            margin-right: 0;
            padding: 9px 14px;
            margin-top: 8px;
            margin-bottom: 8px;
            background-color: transparent;
            background-image: none;
            border: 1px solid transparent;
            border-radius: 4px;
            border-color: #333;
            font-size: 15px;
            line-height: 23px
        }

        .btn-navbar-header:focus {
            outline: 0;
            background-color: #333
        }

        .btn-navbar-header:hover {
            background-color: #333
        }

        .btn-navbar-header .fa {
            font-size: 18px;
            padding-top: 2px;
            width: 20px;
            line-height: 25px
        }

        @media (max-width:767px) {
            .btn-navbar-header {
                width: 46px
            }
        }

        #header .nav-bar .title h1 {
            font-family: inherit;
            font-weight: 500;
            font-size: 18px;
            margin: 0
        }

        #header {
            background: rgba(0, 0, 0, .3);
            margin-bottom: 20px
        }

        @media (max-width:767px) {
            #header {
                margin-bottom: 10px
            }
        }

        .logo {
            margin: 5px 0;
            text-indent: -9999px;
            display: block;
            width: 165px;
            height: 55px;
            background: url({{smart_asset('assets/images/web/logo.png')}}) 0 0 no-repeat
        }

        @media (max-width:767px) {
            .logo {
                margin-left: -10px
            }
        }

        .navbar-inverse {
            background: #000;
            margin: 0
        }

        .nav-bar {
            z-index: 2;
            padding: 10px 0;
            position: relative
        }

        .nav-bar .back-link {
            border: 0;
            float: left;
            padding-left: 4px
        }

        .nav-bar .back-link span {
            display: inline-block;
            max-width: 204px;
            overflow: hidden;
            text-overflow: ellipsis;
            vertical-align: top
        }

        @media (max-width:767px) {
            .logo {
                margin-left: -10px
            }

            .container-fluid>.navbar-header {
                margin-left: 0;
                margin-right: 0
            }

            #user-reviews .answer-block:has(.payment-options),
            .activity-stream-full .answer-block:has(.payment-options),
            .block:has(.payment-options),
            .listing-question .answer-block:has(.payment-options),
            .my-listings-questions .answer-block:has(.payment-options),
            .my-listings-reviews .answer-block:has(.payment-options),
            .payment-options-box:has(.payment-options),
            .styled-questions .answer-block:has(.payment-options),
            .title-block:has(.payment-options) {
                margin: 0 -.5rem;
                border-radius: .375rem
            }
        }

        img.gemoji {
            width: 1.429em;
            height: 1.429em;
            margin-left: 2px;
            -webkit-user-drag: none
        }

        .select2-container {
            margin: 0;
            position: relative;
            display: inline-block;
            vertical-align: middle
        }

        .select2-container,
        .select2-search,
        .select2-search input {
            -webkit-box-sizing: border-box;
            box-sizing: border-box
        }

        .select2-container .select2-choice {
            display: block;
            overflow: hidden;
            position: relative;
            border: 1px solid #aaa;
            white-space: nowrap;
            text-decoration: none;
            background-clip: padding-box;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            background: #fff -webkit-gradient(linear, left bottom, left top, from(#eee), color-stop(50%, #fff));
            background: #fff linear-gradient(to top, #eee 0%, #fff 50%)
        }

        .select2-container .select2-choice>.select2-chosen {
            margin-right: 26px;
            display: block;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
            float: none;
            width: auto
        }

        .select2-container .select2-choice abbr {
            display: none;
            width: 12px;
            height: 12px;
            position: absolute;
            right: 24px;
            font-size: 1px;
            text-decoration: none;
            border: 0;
            background: url({{smart_asset('assets/images/web/pic2.png')}}) top right no-repeat;
            cursor: pointer;
            outline: 0
        }

        .select2-container .select2-choice abbr:hover {
            background-position: right -11px;
            cursor: pointer
        }

        .select2-container .select2-choice .select2-arrow {
            display: inline-block;
            width: 18px;
            height: 100%;
            position: absolute;
            right: 0;
            top: 0;
            border-radius: 0 4px 4px 0;
            background-clip: padding-box;
            background: #ccc -webkit-gradient(linear, left bottom, left top, from(#ccc), color-stop(60%, #eee));
            background: #ccc linear-gradient(to top, #ccc 0%, #eee 60%)
        }

        .select2-container .select2-choice .select2-arrow b {
            display: block;
            width: 100%;
            height: 100%;
            background: url({{smart_asset('assets/images/web/pic2.png')}}) no-repeat 0 1px
        }

        .select2-search {
            display: inline-block;
            width: 100%;
            min-height: 26px;
            margin: 0;
            padding-left: 4px;
            padding-right: 4px;
            position: relative;
            z-index: 10000;
            white-space: nowrap
        }

        .select2-container-multi .select2-choices,
        .select2-search input {
            height: auto !important;
            margin: 0;
            border: 1px solid #aaa;
            min-height: 26px
        }

        .select2-search input {
            width: 100%;
            outline: 0;
            font-size: 1em;
            -webkit-box-shadow: none;
            box-shadow: none;
            background: url({{smart_asset('assets/images/web/pic2.png')}}) no-repeat 100% -22px, -webkit-gradient(linear, left top, left bottom, color-stop(85%, #fff), color-stop(99%, #eee)) 0 0;
            background: url({{smart_asset('assets/images/web/pic2.png')}}) no-repeat 100% -22px, linear-gradient(to bottom, #fff 85%, #eee 99%) 0 0;
            padding: 4px 20px 4px 5px
        }

        .select2-hidden-accessible {
            border: 0;
            clip: rect(0 0 0 0);
            height: 1px;
            margin: -1px;
            overflow: hidden;
            padding: 0;
            position: absolute;
            width: 1px
        }

        .select2-container-multi .select2-choices {
            padding: 0 5px 0 0;
            position: relative;
            cursor: text;
            overflow: hidden;
            background: #fff -webkit-gradient(linear, left top, left bottom, color-stop(1%, #eee), color-stop(15%, #fff));
            background: #fff linear-gradient(to bottom, #eee 1%, #fff 15%)
        }

        .select2-container-multi .select2-choices li {
            float: left;
            list-style: none
        }

        .select2-container-multi .select2-choices .select2-search-field {
            margin: 0;
            padding: 0;
            white-space: nowrap
        }

        .select2-container-multi .select2-choices .select2-search-field input {
            padding: 5px;
            font-family: sans-serif;
            font-size: 100%;
            color: #666;
            outline: 0;
            border: 0;
            -webkit-box-shadow: none;
            box-shadow: none;
            background: 0 0 !important
        }

        .select2-default {
            color: #999 !important
        }

        .select2-search-choice-close {
            display: block;
            width: 12px;
            height: 13px;
            position: absolute;
            right: 3px;
            font-size: 1px;
            outline: 0;
            background: url({{smart_asset('assets/images/web/pic2.png')}}) right top no-repeat
        }

        .select2-offscreen,
        .select2-offscreen:focus {
            clip: rect(0 0 0 0) !important;
            border: 0 !important;
            margin: 0 !important;
            padding: 0 !important;
            overflow: hidden !important;
            outline: 0 !important;
            left: 0 !important;
            top: 0 !important
        }

        .select2-display-none {
            display: none
        }

        @media only screen and (-webkit-min-device-pixel-ratio:2),
        only screen and (min-resolution:2dppx) {

            .select2-container .select2-choice .select2-arrow b,
            .select2-container .select2-choice abbr,
            .select2-search input,
            .select2-search-choice-close {
                background-image: url({{smart_asset('assets/images/web/pic1.png')}}) !important;
                background-size: 60px 40px !important
            }

            .select2-search input {
                background-position: 100% -21px !important
            }
        }

        .select2-container.form-control {
            background: 0 0;
            -webkit-box-shadow: none;
            box-shadow: none;
            border: 0;
            display: block;
            margin: 0;
            padding: 0
        }

        .select2-container .select2-choice,
        .select2-container .select2-choices,
        .select2-container .select2-choices .select2-search-field input {
            background: 0 0;
            padding: 0
        }

        .select2-container .select2-choice,
        .select2-container .select2-choices,
        .select2-container .select2-choices .select2-search-field input,
        .select2-search input {
            border-color: #ccc;
            border-radius: 4px;
            color: #3d3d3d;
            font-family: "Helvetica Neue", Helvetica, Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
            background-color: #fff;
            -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075);
            box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075)
        }

        .select2-container .select2-choices .select2-search-field input {
            -webkit-box-shadow: none;
            box-shadow: none
        }

        .select2-container .select2-choice {
            height: 34px;
            line-height: 1.42857143
        }

        .select2-container.select2-container-multi.form-control {
            height: auto
        }

        .select2-container-multi .select2-choices .select2-search-field input {
            height: 32px;
            margin: 0
        }

        .select2-choice>span:first-child,
        .select2-chosen,
        .select2-container .select2-choices .select2-search-field input {
            padding: 6px 12px
        }

        .select2-container .select2-choice .select2-arrow {
            border-left: none;
            background: 0 0
        }

        .select2-container .select2-choice .select2-arrow b {
            background-position: 0 3px
        }

        .select2-search-choice-close {
            margin-top: -7px;
            top: 50%
        }

        .select2-container .select2-choice abbr {
            top: 50%
        }

        .select2-offscreen,
        .select2-offscreen:focus {
            width: 1px !important;
            height: 1px !important;
            position: absolute !important
        }

        .select2-input::-webkit-input-placeholder {
            color: #777;
            opacity: 1
        }

        .select2-input::-moz-placeholder {
            color: #777;
            opacity: 1
        }

        .select2-input:-ms-input-placeholder,
        .select2-input::-ms-input-placeholder {
            color: #777;
            opacity: 1
        }

        .select2-search-field>input.select2-input::-webkit-input-placeholder {
            color: #777 !important;
            opacity: 1
        }

        .select2-search-field>input.select2-input::-moz-placeholder {
            color: #777 !important;
            opacity: 1
        }

        .select2-search-field>input.select2-input:-ms-input-placeholder,
        .select2-search-field>input.select2-input::-ms-input-placeholder {
            color: #777 !important;
            opacity: 1
        }

        .select2-no-results {
            padding: 4px 12px
        }

        .select2-hidden-accessible {
            visibility: hidden
        }

        .select2-choices>.select2-search-field {
            min-width: 148px
        }

        .dropdown-gender-menu {
            min-width: 168px;
            z-index: 10000 !important;
        }

        @media (max-width:767px) {

            .dropdown-gender-menu,
            .primary-search-gender>.btn,
            .select2-choices>.select2-search-field {
                min-width: 100%
            }

            .primary-search-gender>.btn {
                position: relative;
                text-align: left
            }

            .primary-search-gender>.btn .fa {
                position: absolute;
                right: 5px;
                top: 8px
            }
        }

        .num-range {
            display: block
        }

        .num-range-from,
        .num-range-to {
            display: inline-block;
            width: 50%;
            vertical-align: top
        }

        @media (min-width:768px) and (max-width:991px) {
            .num-range-from {
                width: 55%
            }
        }

        .num-range-to {
            padding-left: 1px
        }

        @media (min-width:768px) and (max-width:991px) {
            .num-range-to {
                width: 45%
            }
        }

        .search-bar--gender {
            min-height: 34px;
            vertical-align: middle
        }

        .listings-primary-search .form-control[multiple],
        .listings-search-advanced-fields .form-control[multiple] {
            background-color: #333;
            background: -webkit-gradient(linear, left top, left bottom, from(#222), to(#333));
            background: linear-gradient(#222, #333);
            text-shadow: 0 -1px 0 rgba(0, 0, 0, .2);
            color: #e6e6e6;
            border: 1px solid #555;
            max-height: 34px;
            vertical-align: middle;
            width: 100%
        }

        .listings-primary-search .form-control[multiple]::-webkit-input-placeholder,
        .listings-search-advanced-fields .form-control[multiple]::-webkit-input-placeholder {
            color: #e6e6e6;
            opacity: 1
        }

        .listings-primary-search .form-control[multiple]::-moz-placeholder,
        .listings-search-advanced-fields .form-control[multiple]::-moz-placeholder {
            color: #e6e6e6;
            opacity: 1
        }

        .listings-primary-search .form-control[multiple]:-ms-input-placeholder,
        .listings-primary-search .form-control[multiple]::-ms-input-placeholder,
        .listings-search-advanced-fields .form-control[multiple]:-ms-input-placeholder,
        .listings-search-advanced-fields .form-control[multiple]::-ms-input-placeholder {
            color: #e6e6e6;
            opacity: 1
        }

        .listings-primary-search .form-control[multiple]:disabled,
        .listings-search-advanced-fields .form-control[multiple]:disabled {
            background: #262626;
            -webkit-transition: none;
            transition: none;
            opacity: .65
        }

        .listings-primary-search .form-control[multiple]:active,
        .listings-primary-search .form-control[multiple]:focus,
        .listings-primary-search .form-control[multiple]:hover,
        .listings-search-advanced-fields .form-control[multiple]:active,
        .listings-search-advanced-fields .form-control[multiple]:focus,
        .listings-search-advanced-fields .form-control[multiple]:hover {
            background: #333;
            -webkit-box-shadow: inset 0 3px 5px rgba(0, 0, 0, .125);
            box-shadow: inset 0 3px 5px rgba(0, 0, 0, .125)
        }

        .listings-primary-search {
            min-height: 34px
        }

        .listings-primary-search .q_services_include_all>select {
            width: 196px
        }

        .listings-primary-search .price-control>select {
            margin-right: -1px;
            width: 81px
        }

        .listings-primary-search .form-group {
            margin-right: 15px
        }

        @media (max-width:767px) {
            .listings-primary-search .form-group {
                margin-right: 0
            }

            .listings-primary-search .typeahead-city-wrapper {
                display: block
            }
        }

        @media (min-width:768px) and (max-width:1199px) {
            .listings-primary-search .typeahead-city-wrapper {
                max-width: 160px
            }
        }

        .listings-search-main-fields-secondary {
            display: inline
        }

        @media (max-width:767px) {
            .listings-search-main-fields-secondary {
                display: none
            }

            .full-search .listings-search-main-fields-secondary {
                display: block
            }
        }

        @media (min-width:768px) {
            .full-search .listings-search-advanced-fields .form-group {
                display: block;
                margin-top: 15px
            }

            .full-search .listings-search-advanced-fields .form-group .form-control[multiple],
            .full-search .listings-search-advanced-fields .form-group input.form-control,
            .full-search .listings-search-advanced-fields .form-group select {
                width: 100%
            }

            .full-search .listings-search-advanced-fields .form-group label {
                display: block;
                margin-bottom: 0
            }
        }

        .typeahead-city-wrapper {
            vertical-align: top
        }

        .typeahead-city-wrapper .twitter-typeahead {
            padding: 0;
            z-index: 3
        }

        .typeahead-city-wrapper .tt-hint,
        .typeahead-city-wrapper .tt-input,
        .typeahead-city-wrapper .typeahead-city {
            display: block;
            width: 100% !important;
            height: 100%;
            background-color: transparent !important;
            -webkit-box-sizing: border-box;
            box-sizing: border-box;
            padding-left: 29px;
            padding-right: 6px;
            font-size: 14px;
            line-height: 1.42857143;
            border: 1px solid transparent;
            border-radius: 4px
        }

        .tt-dropdown-menu {
            min-width: 300px;
            background: #333;
            border: 1px solid #555;
            z-index: 1002;
            opacity: 0;
            text-align: left
        }

        .navbar-top-nav .title a,
        .tt-dropdown-menu .tt-dataset-location {
            color: #fff
        }

        .search-form .tt-dropdown-menu {
            min-width: 200px
        }

        @media (max-width:767px) {
            .search-form .tt-dropdown-menu {
                min-width: 100%
            }
        }

        .advanced-search-checkboxes {
            padding-top: 20px
        }

        .advanced-search-checkboxes>.form-group {
            float: left;
            margin-right: 20px
        }

        .advanced-search-checkboxes>.form-group:last-child {
            margin-right: 0
        }

        .price-control:after {
            content: " ";
            display: block;
            clear: left
        }

        .price-control>.currency-select-container {
            display: inline-block;
            max-width: 86px;
            margin-right: -1px
        }

        .price-control>.price-currency {
            display: inline-block;
            width: auto
        }

        .price-control>.price-amount {
            display: inline-block;
            max-width: 83px;
            margin-left: 0;
            vertical-align: top
        }

        .price-control>.price-amount:focus {
            z-index: 1000
        }

        .price-control .price-currency+.price-amount {
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
            margin-left: -1px
        }

        .price-control .currency-select-container:first-child a,
        .price-control .price-currency:first-child {
            border-top-right-radius: 0;
            border-bottom-right-radius: 0
        }

        .currency-select-dropdown {
            min-width: 120px
        }

        .btn-dark {
            background: #3d3d3d;
            border-color: rgba(255, 255, 255, .1);
            color: #fff
        }

        .btn-dark:active,
        .btn-dark:focus,
        .btn-dark:hover {
            background: #474747;
            border-color: rgba(255, 255, 255, .1);
            color: #fff
        }

        .btn {
            text-shadow: 0 -1px 0 rgba(0, 0, 0, .2);
            -webkit-box-shadow: inset 0 1px 0 rgba(255, 255, 255, .15), 0 1px 1px rgba(0, 0, 0, .075);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, .15), 0 1px 1px rgba(0, 0, 0, .075)
        }

        .btn:active {
            background-image: none;
            -webkit-box-shadow: inset 0 3px 5px rgba(0, 0, 0, .125);
            box-shadow: inset 0 3px 5px rgba(0, 0, 0, .125)
        }

        .btn-primary {
            background: #f4b827 -webkit-gradient(linear, left top, left bottom, from(#f4b827), to(#d3980b)) repeat-x;
            background: #f4b827 linear-gradient(#f4b827, #d3980b) repeat-x;
            border-color: #c9910a
        }

        .btn-primary:focus,
        .btn-primary:hover {
            background-color: #d3980b;
            background-position: 0 -15px
        }

        .btn-primary:active {
            background-color: #d3980b;
            border-color: #c9910a
        }

        form .btn-primary[data-btn-submit]:after {
            font-family: FontAwesome !important;
            content: " \e94c";
            opacity: .8;
            display: inline-block;
            margin-left: 10px;
            height: 18px;
            width: 5px
        }

        .navbar-top-nav {
            padding-top: 10px;
            padding-bottom: 10px;
            overflow: hidden
        }

        .navbar-top-nav .title {
            text-align: center;
            margin-top:-25px
        }

        .navbar-top-nav .title h1 {
            padding-top: 6px;
            min-height: 26px
        }

        .nav.nav-dark {
            background-color: #333;
            border: 1px solid rgba(255, 255, 255, .1);
            border-radius: 2px
        }

        .nav.nav-dark>li>a {
            border-radius: 0;
            color: #fff;
            background-color: #333
        }

        .nav.nav-dark>li:hover>a {
            background-color: rgba(166, 166, 166, .1)
        }

        .nav.nav-dark>li.active>a {
            background-color: rgba(255, 255, 255, .1)
        }

        .form-group {
            margin-bottom: 1.5rem
        }

        .block__coinvert li:has(.selected) {
            background-color: rgba(0, 0, 0, .25)
        }

        .d-flex {
            display: -webkit-box !important;
            display: -ms-flexbox !important;
            display: flex !important
        }

        .d-inline-flex {
            display: -webkit-inline-box !important;
            display: -ms-inline-flexbox !important;
            display: inline-flex !important
        }

        .mb-0 {
            margin-bottom: 0 !important
        }

        .mr-1 {
            margin-right: .25rem !important
        }

        .mb-3 {
            margin-bottom: 1rem !important
        }

        .py-0 {
            padding-top: 0 !important;
            padding-bottom: 0 !important
        }

        .flex-wrap {
            -ms-flex-wrap: wrap !important;
            flex-wrap: wrap !important
        }

        .align-items-start {
            -webkit-box-align: start !important;
            -ms-flex-align: start !important;
            align-items: flex-start !important
        }

        .align-items-center {
            -webkit-box-align: center !important;
            -ms-flex-align: center !important;
            align-items: center !important
        }

        .content-wrapper.no-sidebar #content {
            padding: 0
        }

        .container-fluid {
            max-width: 1340px
        }

        [role=button],
        a,
        button,
        input,
        label,
        select {
            -ms-touch-action: manipulation;
            touch-action: manipulation
        }

        .modal {
            display: none
        }

        .fs {
            visibility: hidden
        }

        .tooltip {
            position: absolute
        }

        .typeahead-city-wrapper {
    position: relative;
}

.typeahead-city-wrapper::before {
    content: "\f3c5"; /* Map marker icon */
    font-family: "Font Awesome 5 Free";
    font-weight: 900;
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: #aaa;
    z-index: 5;
    pointer-events: none;
}

.search-bar--city {
    padding-left: 40px !important;
}

.city-results {
    position: absolute;
    width: 100%;
    max-height: 250px;
    overflow-y: auto;
    z-index: 1060 !important;
    background-color: #333 !important;
    border: 1px solid #555 !important;
    border-radius: 4px;
    margin-top: 2px;
}

.city-item {
    padding: 10px 15px;
    cursor: pointer;
    color: white !important;
    border-bottom: 1px solid #444;
}

.city-item:last-child {
    border-bottom: none;
}

.city-item:hover {
    background-color: #444 !important;
}

/* Select2 styling */
.select2-container {
    width: 100% !important;
    z-index: 1060 !important;
}

.select2-container--default .select2-selection--single,
.select2-container--default .select2-selection--multiple {
    background-color: #333 !important;
    border: 1px solid #555 !important;
    color: white !important;
    height: 38px !important;
    border-radius: 4px !important;
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
    color: white !important;
    line-height: 36px !important;
    padding-left: 12px !important;
}

.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 36px !important;
}

.select2-dropdown {
    background-color: #333 !important;
    border: 1px solid #555 !important;
    color: white !important;
    z-index: 1061 !important;
}

.select2-container--default .select2-results__option {
    color: white !important;
    padding: 8px 12px !important;
}

.select2-container--default .select2-results__option--highlighted[aria-selected] {
    background-color: #444 !important;
}

.select2-search--dropdown .select2-search__field {
    background-color: #444 !important;
    color: white !important;
    border: 1px solid #666 !important;
    padding: 8px !important;
}

/* Chosen styling */
.chosen-container {
    width: 100% !important;
    z-index: 1060 !important;
}

.chosen-container-multi .chosen-choices {
    background-color: #333 !important;
    background: -webkit-gradient(linear, left top, left bottom, from(#222), to(#333))!important;
    background: linear-gradient(#222, #333)!important;
    border: 1px solid #5a5a5a !important;
    border-radius: 5px !important;
    height: 35px !important;
    min-height: 35px !important;
    padding: 0 8px !important;
}

.chosen-container-multi .chosen-choices li.search-field input[type="text"] {
    height: 33px !important;
    color: #ffffff !important;
    font-size: 14px !important;
}

.chosen-container .chosen-drop {
    background: #2c2c2c !important;
    color: #ffffff !important;
    border: 1px solid #444 !important;
    z-index: 1061 !important;
}

.chosen-container .chosen-results li.highlighted {
    background: #444 !important;
    color: #ffffff !important;
}

.chosen-container .chosen-results li {
    color: #ffffff !important;
}

.chosen-container .chosen-search input[type="text"] {
    background: #2c2c2c !important;
    color: #ffffff !important;
    border: 1px solid #444 !important;
}

.chosen-container .search-choice {
    background: #444 !important;
    color: #ffffff !important;
    border: 1px solid #666 !important;
}

/* Force Chosen dropdown to be visible */
.chosen-container.chosen-with-drop .chosen-drop {
    display: block !important;
    visibility: visible !important;
    opacity: 1 !important;
    z-index: 1061 !important;
}

#mobile_city_results {
    background-color: #333 !important;
    border-color: #555 !important;
    color: white !important;
    z-index: 9999 !important;
    text-align: left;
    position: absolute !important;
}

/* City search form group needs high z-index */
.form-group:has(#mobile_city_search) {
    position: relative !important;
    z-index: 9999 !important;
}

.city-item {
    padding: 10px 15px;
    cursor: pointer;
    color: white !important;
    border-bottom: 1px solid #444;
    text-align: left;
}

.city-item:last-child {
    border-bottom: none;
}

.city-item:hover {
    background-color: #444 !important;
}

/* Fix for selected city text alignment */
#mobile_selected_city_name {
    text-align: left !important;
    display: block !important;
    width: 100% !important;
}

/* Fix for input padding */
#mobile_city_search {
    padding-left: 12px !important;
    text-align: left !important;
}

/* Fix for dropdown items */
.dropdown-item {
    color: white !important;
    background-color: transparent !important;
}

.dropdown-item:hover, .dropdown-item:focus {
    background-color: #444 !important;
    color: white !important;
}

/* Dark dropdown theme */
.dark-dropdown {
    background-color: #333 !important;
    border-color: #555 !important;
    color: white !important;
}

.dark-dropdown .dropdown-item {
    color: white !important;
}

.dark-dropdown .dropdown-item:hover {
    background-color: #444 !important;
}

.mobile-search-modal .form-control,
.mobile-search-modal select,
.mobile-search-modal input,
.mobile-search-modal textarea {
    background-color: #333 !important;
    color: white !important;
    border: 1px solid #555 !important;
    border-radius: 4px !important;
}

.mobile-search-modal .form-control:focus,
.mobile-search-modal select:focus,
.mobile-search-modal input:focus,
.mobile-search-modal textarea:focus {
    background-color: #444 !important;
    color: white !important;
    border-color: #007bff !important;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25) !important;
}

.mobile-search-modal .form-control::placeholder,
.mobile-search-modal select::placeholder,
.mobile-search-modal input::placeholder,
.mobile-search-modal textarea::placeholder {
    color: #aaa !important;
}

/* Select2 styling */
.mobile-search-modal .select2-container {
    width: 100% !important;
    z-index: 1060 !important;
}

.mobile-search-modal .select2-container--default .select2-selection--single,
.mobile-search-modal .select2-container--default .select2-selection--multiple {
    background-color: #333 !important;
    border: 1px solid #555 !important;
    color: white !important;
    height: 38px !important;
    border-radius: 4px !important;
}

.mobile-search-modal .select2-container--default .select2-selection--single .select2-selection__rendered {
    color: white !important;
    line-height: 36px !important;
    padding-left: 12px !important;
}

.mobile-search-modal .select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 36px !important;
    background-color: transparent !important;
    border-left: none !important;
}

.mobile-search-modal .select2-container--default .select2-selection--single .select2-selection__arrow b {
    border-color: #aaa transparent transparent transparent !important;
}

.mobile-search-modal .select2-dropdown {
    background-color: #333 !important;
    border: 1px solid #555 !important;
    color: white !important;
    z-index: 1061 !important;
}

.mobile-search-modal .select2-container--default .select2-results__option {
    color: white !important;
    padding: 8px 12px !important;
}

.mobile-search-modal .select2-container--default .select2-results__option--highlighted[aria-selected] {
    background-color: #444 !important;
}

.mobile-search-modal .select2-search--dropdown .select2-search__field {
    background-color: #444 !important;
    color: white !important;
    border: 1px solid #666 !important;
    padding: 8px !important;
}

.mobile-search-modal .select2-container--default .select2-selection--multiple .select2-selection__choice {
    background-color: #444 !important;
    border: 1px solid #666 !important;
    color: white !important;
    padding: 3px 8px !important;
}

.mobile-search-modal .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
    color: #ccc !important;
    margin-right: 5px !important;
}

/* Chosen styling */
.mobile-search-modal .chosen-container {
    width: 100% !important;
    z-index: 1060 !important;
    font-size: 14px !important;
}

.mobile-search-modal .chosen-container-multi .chosen-choices {
    background-color: #333 !important;
    background-image: none !important;
    border: 1px solid #555 !important;
    border-radius: 4px !important;
    min-height: 38px !important;
    padding: 4px 8px !important;
}

.mobile-search-modal .chosen-container-multi .chosen-choices li.search-field input[type="text"] {
    height: 30px !important;
    color: #aaa !important;
    font-size: 14px !important;
}

.mobile-search-modal .chosen-container .chosen-drop {
    background: #333 !important;
    color: white !important;
    border: 1px solid #555 !important;
    z-index: 1061 !important;
    border-radius: 0 0 4px 4px !important;
}

.mobile-search-modal .chosen-container .chosen-results li {
    color: white !important;
    padding: 8px 12px !important;
}

.mobile-search-modal .chosen-container .chosen-results li.highlighted {
    background: #444 !important;
    color: white !important;
}

.mobile-search-modal .chosen-container .chosen-search input[type="text"] {
    background: #444 !important;
    color: white !important;
    border: 1px solid #666 !important;
    padding: 8px !important;
}

.mobile-search-modal .chosen-container-multi .chosen-choices li.search-choice {
    background-color: #444 !important;
    background-image: none !important;
    border: 1px solid #666 !important;
    color: white !important;
    padding: 5px 25px 5px 8px !important;
    border-radius: 3px !important;
}

.mobile-search-modal .chosen-container-multi .chosen-choices li.search-choice .search-choice-close {
    top: 6px !important;
    right: 3px !important;
}

/* Regular select elements */
.mobile-search-modal select.form-control {
    background-color: #333 !important;
    color: white !important;
    border: 1px solid #555 !important;
    height: 38px !important;
    padding: 6px 12px !important;
    appearance: auto !important; /* Show native dropdown arrow */
}

.mobile-search-modal select.form-control option {
    background-color: #333 !important;
    color: white !important;
}

/* Fix for dropdown z-index */
.select2-container--open {
    z-index: 9999 !important;
}

.chosen-container.chosen-with-drop .chosen-drop {
    z-index: 9999 !important;
}

/* City search dropdown */
#mobile_city_results {
    background-color: #333 !important;
    border-color: #555 !important;
    color: white !important;
    z-index: 1060 !important;
    text-align: left;
}

.city-item {
    padding: 10px 15px;
    cursor: pointer;
    color: white !important;
    border-bottom: 1px solid #444;
    text-align: left;
}

.city-item:last-child {
    border-bottom: none;
}

.city-item:hover {
    background-color: #444 !important;
}

/* Fix for selected city text alignment */
#mobile_selected_city_name {
    text-align: left !important;
    display: block !important;
    width: 100% !important;
}

/* Fix for input padding */
#mobile_city_search {
    padding-left: 12px !important;
    text-align: left !important;
}

/* Fix for dropdown items */
.dropdown-item {
    color: white !important;
    background-color: transparent !important;
}

.dropdown-item:hover, .dropdown-item:focus {
    background-color: #444 !important;
    color: white !important;
}

.select2-dark-theme {
    background-color: #333 !important;
    color: #fff !important;
}

.select2-container.select2-container--default .select2-selection--single,
.select2-container.form-control,
.select2-container.select-box,
#s2id_buts,
.select2-choice {
    background-color: #333 !important;
    background-image: none !important;
    background: #333 !important;
    color: #e6e6e6 !important;
    border: 1px solid #555 !important;
    box-shadow: none !important;
}

/* Target the Select2 dropdown */
.select2-drop,
.select2-drop-active,
.select2-dropdown {
    background-color: #333 !important;
    color: #e6e6e6 !important;
    border: 1px solid #555 !important;
}

/* Target the Select2 options */
.select2-results .select2-result,
.select2-results .select2-result-label,
.select2-results__option {
    background-color: #333 !important;
    color: #e6e6e6 !important;
}

/* Target the highlighted option */
.select2-results .select2-highlighted,
.select2-results__option--highlighted {
    background-color: #444 !important;
    color: #fff !important;
}

/* Target the Select2 search field */
.select2-search input {
    background-color: #222 !important;
    color: #e6e6e6 !important;
    border: 1px solid #555 !important;
}

/* Target the Select2 chosen text */
.select2-chosen {
    color: #e6e6e6 !important;
}

/* Target the Select2 arrow */
.select2-arrow {
    background-color: #444 !important;
    background-image: none !important;
    border-left: 1px solid #555 !important;
}

.select2-arrow b {
    border-color: #e6e6e6 transparent transparent transparent !important;
}

/* Target the Select2 container when it has focus */
.select2-container-active .select2-choice,
.select2-container-active .select2-choices {
    border: 1px solid #777 !important;
    box-shadow: 0 0 5px rgba(255, 255, 255, 0.3) !important;
}

/* Target the Select2 placeholder */
.select2-default .select2-chosen {
    color: #aaa !important;
}

input[type="text"].form-control,
input[type="email"].form-control,
input[type="number"].form-control,
input[type="search"].form-control,
input[type="password"].form-control,
textarea.form-control {
    background-color: #333 !important;
    color: #e6e6e6 !important;
    border: 1px solid #555 !important;
    box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075) !important;
}

/* Style placeholder text */
input[type="text"].form-control::placeholder,
input[type="email"].form-control::placeholder,
input[type="number"].form-control::placeholder,
input[type="search"].form-control::placeholder,
input[type="password"].form-control::placeholder,
textarea.form-control::placeholder {
    color: #aaa !important;
    opacity: 1;
}

/* Style focus state */
input[type="text"].form-control:focus,
input[type="email"].form-control:focus,
input[type="number"].form-control:focus,
input[type="search"].form-control:focus,
input[type="password"].form-control:focus,
textarea.form-control:focus {
    background-color: #3a3a3a !important;
    color: #fff !important;
    border-color: #777 !important;
    box-shadow: 0 0 5px rgba(255, 255, 255, 0.3) !important;
    outline: none !important;
}

/* Style disabled state */
input[type="text"].form-control:disabled,
input[type="email"].form-control:disabled,
input[type="number"].form-control:disabled,
input[type="search"].form-control:disabled,
input[type="password"].form-control:disabled,
textarea.form-control:disabled {
    background-color: #2a2a2a !important;
    color: #888 !important;
    cursor: not-allowed;
}

/* Style form labels to match */
.form-group label {
    color: #e6e6e6 !important;
 
}

.custom-control-input {
    /* Hide the default checkbox but keep it accessible */
    opacity: 0;
    z-index: 1;
    position: absolute;
}

/* Style the custom checkbox container */
.custom-control {
    padding-left: 2rem;
    position: relative;
    display: inline-block;
    min-height: 1.5rem;
}

/* Style the custom checkbox label */
.custom-control-label {
    position: relative;
    margin-bottom: 0;
    vertical-align: top;
    color: #e6e6e6 !important;
    cursor: pointer;
}

/* Style the custom checkbox indicator (the box) */
.custom-control-label::before {
    position: absolute;
    top: 0.25rem;
    left: -1.5rem;
    display: block;
    width: 1rem;
    height: 1rem;
    content: "";
    background-color: #333 !important;
    border: 1px solid #555 !important;
    border-radius: 0.25rem;
    transition: background-color 0.15s ease-in-out, border-color 0.15s ease-in-out;
}

/* Style the checkmark/indicator */
.custom-control-label::after {
    position: absolute;
    top: 0.25rem;
    left: -1.5rem;
    display: block;
    width: 1rem;
    height: 1rem;
    content: "";
    background: no-repeat 50% / 50% 50%;
}

/* Style the checked state */
.custom-control-input:checked ~ .custom-control-label::before {
    background-color: #007bff !important;
    border-color: #007bff !important;
}

/* Style the checkmark when checked */
.custom-control-input:checked ~ .custom-control-label::after {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%23fff' d='M6.564.75l-3.59 3.612-1.538-1.55L0 4.26 2.974 7.25 8 2.193z'/%3e%3c/svg%3e");
}

/* Style the focus state */
.custom-control-input:focus ~ .custom-control-label::before {
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25) !important;
}

/* Style the disabled state */
.custom-control-input:disabled ~ .custom-control-label {
    color: #888 !important;
    cursor: not-allowed;
}

.custom-control-input:disabled ~ .custom-control-label::before {
    background-color: #2a2a2a !important;
}

/* Style the indeterminate state if needed */
.custom-control-input:indeterminate ~ .custom-control-label::before {
    background-color: #007bff !important;
    border-color: #007bff !important;
}

.custom-control-input:indeterminate ~ .custom-control-label::after {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 4 4'%3e%3cpath stroke='%23fff' d='M0 2h4'/%3e%3c/svg%3e");
}

@media (max-width: 768px) {
    input[type="text"].form-control, input[type="email"].form-control, input[type="number"].form-control, input[type="search"].form-control, input[type="password"].form-control, textarea.form-control {
        margin-left: 0px;
    
    }

    .form-group label {
    margin-left: 4px;
    font-weight: bold;
}


.verified-label {
    background: -webkit-gradient(linear, left bottom, right top, from(#f4b827), to(#c3931f));
    background: linear-gradient(to top right, #f4b827, #c3931f);
    color: #333;
    font-weight: 700;
    text-transform: uppercase;
    border-radius: 3px;
}

#header {
        margin-bottom: 0px;
    }

    .navbar-top-nav {
    padding-top: 10px;
    padding-bottom: 10px;
    overflow: hidden;
    background: #212121;
}

        .btn-primary:not(:disabled):not(.disabled).active, .btn-primary:not(:disabled):not(.disabled):active, .show>.btn-primary.dropdown-toggle {
        color: #1c1a1a;

    }


.btn-primary {
    background-color: #f4b827 ;
    border-color: #f4b827 ;
}
}


    </style>
    <script>
        ! function(a) {
            "use strict";
            var b = function(b, c, d) {
                function e(a) {
                    return h.body ? a() : void setTimeout(function() {
                        e(a)
                    })
                }

                function f() {
                    i.addEventListener && i.removeEventListener("load", f), i.media = d || "all"
                }
                var g, h = a.document,
                    i = h.createElement("link");
                if (c) g = c;
                else {
                    var j = (h.body || h.getElementsByTagName("head")[0]).childNodes;
                    g = j[j.length - 1]
                }
                var k = h.styleSheets;
                i.rel = "stylesheet", i.href = b, i.media = "only x", e(function() {
                    g.parentNode.insertBefore(i, c ? g : g.nextSibling)
                });
                var l = function(a) {
                    for (var b = i.href, c = k.length; c--;)
                        if (k[c].href === b) return a();
                    setTimeout(function() {
                        l(a)
                    })
                };
                return i.addEventListener && i.addEventListener("load", f), i.onloadcssdefined = l, l(f), i
            };
            "undefined" != typeof exports ? exports.loadCSS = b : a.loadCSS = b
        }("undefined" != typeof global ? global : this);
        ! function(a) {
            if (a.loadCSS) {
                var b = loadCSS.relpreload = {};
                if (b.support = function() {
                        try {
                            return a.document.createElement("link").relList.supports("preload")
                        } catch (b) {
                            return !1
                        }
                    }, b.poly = function() {
                        for (var b = a.document.getElementsByTagName("link"), c = 0; c < b.length; c++) {
                            var d = b[c];
                            "preload" === d.rel && "style" === d.getAttribute("as") && (a.loadCSS(d.href, d, d
                                .getAttribute("media")), d.rel = null)
                        }
                    }, !b.support()) {
                    b.poly();
                    var c = a.setInterval(b.poly, 300);
                    a.addEventListener && a.addEventListener("load", function() {
                        b.poly(), a.clearInterval(c)
                    }), a.attachEvent && a.attachEvent("onload", function() {
                        a.clearInterval(c)
                    })
                }
            }
        }(this);
        loadCSS(
            ''
            );
        head_ = document.getElementsByTagName("head")[0];
        for (var i = 0; i < head_.children.length; i++) {
            if (head_.children[i].getAttribute("media") === 'screen') {
                head_.appendChild(head_.children[i]);
                break;
            }
        }
    </script><noscript>
        <link rel="stylesheet" media="screen"
            href="" />
    </noscript>
    <link rel="stylesheet" media="print"
        href=""
        data-turbolinks-track="reload" />
    <meta name="csrf-param" content="authenticity_token" />
    <meta name="csrf-token"
        content="C6Vq2MuWirQup/O/ZMwpjnJDxaRgrTD8BOkRMmy2wye8ddXa1jwnfCCDmLIhZeTagirRFDDuswHbDm/GpJ2FfA==" />
    <link href="https://ae.massagerepublic.co/search-female-escorts" rel="canonical" />
    <script
        src=""
        data-turbolinks-track="reload" defer="defer"></script>
    <meta content="no-preview" name="turbolinks-cache-control" />
    </head>


<div class="nav-bar navbar-top-nav">
    <div class="container-fluid">
        <a class="back-link" href="https://massagerepublic.com/">
            <i class="fa fa-angle-left fa-fw"></i>
            <span class="hidden-xs">Back</span>
        </a>
        <div class="title">
            <h1>
                <a href="/female-escorts-in-{{ strtolower($gender) }}-escorts">
                    Search Escort {{ $selectedcity ? trim($selectedcity) : '' }}
                </a>
            </h1>
        </div>
    </div>
</div>
    <div class="container-fluid " style="margin-top:10px">

    
    
        <div class="content-wrapper no-sidebar">
            <div id="content">
                
                <div class="search-form-container dark-form"  >
                 
                    <form action="{{ route('mobile.search.results', ['gender' => $gender]) }}" method="GET">
                       
                        <!-- Gender Selection -->
                        <div class="form-group dropdown primary-search-gender mb-2" style="position: relative; z-index: 10000;">
                            <button class="btn btn-dark search-bar--gender w-100" data-toggle="dropdown" type="button">
                                <i class="fas fa-user mr-2"></i>{{ ucfirst($gender) }} escorts <i class="fas fa-caret-down float-right mt-1"></i>
                            </button>
                            <ul class="dropdown-menu nav nav-pills nav-stacked nav-dark dropdown-gender-menu w-100">
                                <li class="{{ $gender == 'female' ? 'active' : '' }}">
                                    <a href="{{ route('mobile.search', ['gender' => 'female']) }}"
                                        title="Female Escorts">Female escorts</a>
                                </li>
                                <li class="{{ $gender == 'male' ? 'active' : '' }}">
                                    <a href="{{ route('mobile.search', ['gender' => 'male']) }}"
                                        title="Male Escorts">Male escorts</a>
                                </li>
                                <li class="{{ $gender == 'shemale' ? 'active' : '' }}">
                                    <a href="{{ route('mobile.search', ['gender' => 'shemale']) }}"
                                        title="Shemale Escorts">Shemale escorts</a>
                                </li>
                            </ul>
                        </div>

                        <!-- City Selection -->
                        <div class="form-group mb-2" style="position: relative; z-index: 9999 !important;">
                            
                            <div class="position-relative">
                                <input type="text" id="mobile_city_search" class="form-control form-control-lg" 
                                       value="{{ trim($selectedcity) }}" placeholder="Type city..." autocomplete="off">
                                <div id="mobile_city_results" class="dropdown-menu w-100 dark-dropdown" 
                                     style="display:none; max-height:250px; overflow-y:auto;">
                                </div>
                            </div>
                            <input type="hidden" wire:model="city" name="city" id="mobile_selected_city" value="{{ $city }}">
                            <input type="hidden" name="selectedcity" id="mobile_selected_city_text" value="{{ $selectedcity }}">
                            <small id="mobile_selected_city_name" class="form-text text-muted text-left w-100 d-block">
                                {{ $selectedcity ? 'Selected: ' . trim($selectedcity) : 'No city selected' }}
                            </small>
                        </div>

                        <!-- Price Range -->
                        <div class="form-group mb-2">
                            
                            <div class="d-flex">
                                <select wire:model="currency" name="currency" class="form-control mr-1 select2-single" style="width: 40%;">
                                    @foreach ($currencies as $curr)
                                        <option value="{{ $curr->id }}">{{ $curr->code }}</option>
                                    @endforeach
                                </select>
                                <input wire:model="rate" name="rate" type="number" class="form-control" id="rate"
                                    placeholder="Max price">
                            </div>
                        </div>

                    

                        <!-- Services Dropdown (Desktop Style) -->
                        <div class="form-group mb-2" wire:ignore>
                           
                            <div class="services-dropdown-container" style="position: relative;">
                                <!-- The display box that looks like an input -->
                                <div id="mobile-services-display-box" 
                                     style="background-color: #2c2c2c; color: white; border: 1px solid rgb(68 68 68); cursor: pointer; font-size: 13px; height: 44px; padding: 10px 12px; display: flex; align-items: center; justify-content: space-between; font-weight: bold; border-radius: 4px; width: 100%; box-sizing: border-box;">
                                    <span id="mobile-services-display-text" style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: calc(100% - 20px);">All Services</span>
                                    <span style="color: #999; flex-shrink: 0;">▼</span>
                                </div>
                                
                                <!-- The dropdown list -->
                                <div id="mobile-services-dropdown-list" 
                                     style="display: none; position: absolute; top: 100%; left: 0; right: 0; background-color: #2c2c2c; border: 1px solid rgb(68 68 68); border-top: none; border-radius: 0 0 4px 4px; max-height: 200px; overflow-y: auto; z-index: 1000; box-shadow: 0 4px 6px rgba(0,0,0,0.3);">
                                    @foreach($services as $service)
                                        <div class="mobile-service-option" 
                                             data-id="{{ $service->id }}" 
                                             data-name="{{ $service->name }}"
                                             style="padding: 10px 12px; color: white; cursor: pointer; border-bottom: 1px solid #404040; display: flex; align-items: center; font-size: 13px;">
                                            <input type="checkbox" 
                                                   id="mobile-service-{{ $service->id }}" 
                                                   style="display: none;"
                                                   @if(is_array($sservices) && in_array($service->id, $sservices)) checked 
                                                   @elseif(is_string($sservices) && in_array($service->id, explode(',', $sservices))) checked @endif>
                                            <label for="mobile-service-{{ $service->id }}" style="margin: 0; cursor: pointer; width: 100%;">
                                                {{ $service->name }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                                
                                <!-- Hidden input for Livewire -->
                                <input type="hidden" wire:model="sservices" name="sservices[]" id="mobile-services-hidden-input">
                            </div>
                        </div>
                </div>
            </div>

            <!-- Bust Size --> 
            <div class="form-group mb-2 select ">
                <label for="buts">Bust size</label>
                <select wire:model="buts" id="buts" class="select required form-control select-box"
                onfocus="if(jQuery && jQuery.fn.select2 && !jQuery(this).hasClass('select2-hidden-accessible')){jQuery(this).select2({theme:'default',width:'100%',dropdownParent:jQuery('body')});jQuery(this).select2('open');}">
                    <option value="">Any</option>
                    @foreach ($busts as $bust)
                        <option value="{{ $bust->id }}">{{ $bust->name }}</option>
                    @endforeach
                </select>
            </div>

          



            <!-- Orientation -->
            <div class="form-group mb-2">
                <label for="ori">Orientation</label>
                <select wire:model="ori" name="ori" id="ori" class="select required form-control select-box ">
                    <option value="">Any</option>
                    <option value="1">Heterosexual</option>
                    <option value="2">Bisexual</option>
                    <option value="3">Lesbian or Gay</option>
                </select>
            </div>

            <!-- Checkboxes -->
            <div class="col-sm-121">
                <div class="d-flex flex-wrap align-items-center gap-3 p-0 mb-4 advanced-search-checkboxes">
                    <div class="form-group boolean optional q_verified_true mb-0">
                        <input value="0" type="hidden" name="q[verified_true]">
                        <label style="margin-left:0px" class="boolean optional control-label px-2 py-1 verified-label checkbox" for="q_verified_true">
                            <input wire:model="verified" autocomplete="off" class="boolean optional" type="checkbox" value="1" name="verified" id="q_verified_true">
                            Verified
                        </label>
                    </div>
                    <div title="At their place" class="form-group boolean optional q_incalls_true mb-0" data-toggle="tooltip" data-placement="top">
                        <input value="0" type="hidden" name="q[incalls_true]">
                        <label style="margin-left:0px" class="boolean optional control-label checkbox" for="q_incalls_true">
                            <input wire:model="incall" autocomplete="off" class="boolean optional" type="checkbox" value="1" name="incall" id="q_incalls_true">
                            Incalls
                        </label>
                    </div>
                    <div title="At your place" class="form-group boolean optional q_outcalls_true mb-0" data-toggle="tooltip" data-placement="top">
                        <input value="0" type="hidden" name="q[outcalls_true]">
                        <label class="boolean optional control-label checkbox" for="q_outcalls_true">
                            <input wire:model="outcall" autocomplete="off" class="boolean optional" type="checkbox" value="1" name="outcall" id="q_outcalls_true">
                            Outcalls
                        </label>
                    </div>
                    <div class="form-group boolean optional q_smokes_false mb-0">
                        <input value="0" type="hidden" name="q[smokes_false]">
                        <label class="boolean optional control-label checkbox" for="q_smokes_false">
                            <input wire:model="nonsmoker" autocomplete="off" class="boolean optional" type="checkbox" value="1" name="nonsmoker" id="q_smokes_false">
                            Non-smoker
                        </label>
                    </div>
                    <div class="form-group boolean optional q_has_reviews mb-0">
                        <input value="0" type="hidden" name="q[has_reviews]">
                        <label class="boolean optional control-label checkbox" for="q_has_reviews">
                            <input wire:model="withreviews" autocomplete="off" class="boolean optional" type="checkbox" value="1" name="withreviews" id="q_has_reviews">
                            With reviews
                        </label>
                    </div>
                </div>
            </div>

            <!-- Ethnicity -->
            <div class="form-group mb-2">
                <label for="ethnicity">Ethnicity</label>
                <select wire:model="ethnicity" name="ethnicity" id="ethnicity" class="select required form-control select-box select2-single" >
                    <option value="">Any</option>
                    @foreach ($ethnicities as $eth)
                        <option value="{{ $eth->id }}">{{ $eth->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Nationality -->
            <div class="form-group mb-2">
                <label for="nationality">Nationality</label>
                <select wire:model="nationality" name="nationality" id="nationality" class="select required form-control select-box select2-single">
                    <option value="">Any</option>
                    @foreach ($countries as $country)
                        <option value="{{ $country->id }}">{{ $country->nicename }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Age Range -->
            <div class="form-group mb-2">
                <label>Age</label>
                <div class="d-flex">
                    <select wire:model="agefrom" name="agefrom" class="select required form-control select-box mr-1">
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
                    <select wire:model="ageto" name="ageto" class="select required form-control select-box">
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

            <!-- Height Range -->
            <div class="form-group mb-2">
                <label>Height (cm)</label>
                <div class="d-flex">
                    <select wire:model="heightfrom" name="heightfrom" class="select required form-control select-box mr-1">
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
                    <select wire:model="heightto" name="heightto" class="select required form-control select-box">
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

            <!-- Name -->
            <div class="form-group mb-2">
                <label for="name">Name</label>
                <input wire:model="name" name="name" type="text" class="form-control" id="name"
                    placeholder="Search by name">
            </div>

            <!-- Languages -->
            <div class="form-group mb-2">
                <label for="language">Languages</label>
                <select wire:model="language" name="language" id="language" class="select required form-control select-box select2-single">
                    <option value="">Any</option>
                    @foreach ($languages as $lang)
                        <option value="{{ $lang->id }}">{{ $lang->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Shaved -->
            <div class="form-group mb-2">
                <label for="isshaved">Shaved</label>
                <select wire:model="isshaved" name="isshaved" id="isshaved" class="select required form-control select-box">
                    <option value="">Any</option>
                    <option value="no">No</option>
                    <option value="partially">Partially</option>
                    <option value="yes">Yes</option>
                </select>
            </div>

            <!-- Hair Color -->
            <div class="form-group mb-2">
                <label for="haircolor">Hair color</label>
                <select wire:model="haircolor" name="haircolor" id="haircolor" class="select required form-control select-box">
                    <option value="">Any</option>
                    @foreach ($hairs as $hair)
                        <option value="{{ $hair->id }}">{{ $hair->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Search Button -->
            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-search"></i> Search
                </button>
            </div>
            </form>
           

    </div>
</div>

@push('css')
    <style>
        /* Custom styles for mobile search */
        .search-form-container {
            padding: 0px 0;
        }

        .card {
            background-color: #333;
            border-color: #444;
        }

        .dropdown-menu {
            max-height: 300px;
            overflow-y: auto;
        }

        .custom-control-label {
            color: #fff;
        }

        .custom-control-input:checked~.custom-control-label::before {
            background-color: #007bff;
            border-color: #007bff;
        }

        /* Chosen styling - keep your working implementation */
        .chosen-container {
            width: 100% !important;
            z-index: 9999 !important;
        }

        .chosen-container-multi .chosen-choices {
            background-color: #333 !important;
            background: -webkit-gradient(linear, left top, left bottom, from(#222), to(#333)) !important;
            background: linear-gradient(#222, #333) !important;
            border: 1px solid #5a5a5a !important;
            border-radius: 5px !important;
            height: 35px !important;
            min-height: 35px !important;
            padding: 0 8px !important;
        }

        .chosen-container-multi .chosen-choices li.search-field input[type="text"] {
            height: 33px !important;
            color: #ffffff !important;
            font-size: 14px !important;
        }

        .chosen-container .chosen-drop {
            background: #2c2c2c !important;
            color: #ffffff !important;
            border: 1px solid #444 !important;
            z-index: 9999 !important;
        }

        .chosen-container .chosen-results li.highlighted {
            background: #444 !important;
            color: #ffffff !important;
        }

        .chosen-container .chosen-results li {
            color: #ffffff !important;
        }

        .chosen-container .chosen-search input[type="text"] {
            background: #2c2c2c !important;
            color: #ffffff !important;
            border: 1px solid #444 !important;
        }

        .chosen-container .search-choice {
            background: #444 !important;
            color: #ffffff !important;
            border: 1px solid #666 !important;
        }

        /* Force Chosen dropdown to be visible */
        .chosen-container.chosen-with-drop .chosen-drop {
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
            z-index: 9999 !important;
        }
    </style>
@endpush

@push('js')

<script>
$(document).ready(function() {
    let searchTimeout;
    
    // Mobile Services Dropdown Handler
    var selectedServices = [];
    
    // Toggle dropdown visibility
    $(document).on('click', '#mobile-services-display-box', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $('#mobile-services-dropdown-list').toggle();
        console.log('Mobile services dropdown toggled');
    });
    
    // Close dropdown when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.services-dropdown-container').length) {
            $('#mobile-services-dropdown-list').hide();
        }
    });
    
    // Handle service selection
    $(document).on('click', '.mobile-service-option', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var checkbox = $(this).find('input[type="checkbox"]');
        var serviceId = $(this).data('id');
        var serviceName = $(this).data('name');
        
        // Toggle checkbox
        checkbox.prop('checked', !checkbox.prop('checked'));
        
        // Update selected services array
        if (checkbox.prop('checked')) {
            if (!selectedServices.includes(serviceId)) {
                selectedServices.push(serviceId);
            }
            $(this).css('background-color', '#404040');
        } else {
            selectedServices = selectedServices.filter(id => id !== serviceId);
            $(this).css('background-color', '');
        }
        
        // Update display text
        updateMobileServicesDisplay();
        
        // Update hidden input and sync with Livewire
        $('#mobile-services-hidden-input').val(selectedServices.join(',')).trigger('change');
        if (typeof @this !== 'undefined') {
            @this.set('sservices', selectedServices);
        }
        
        console.log('Selected services:', selectedServices);
    });
    
    function updateMobileServicesDisplay() {
        var displayText = 'All Services';
        if (selectedServices.length > 0) {
            var names = [];
            selectedServices.forEach(function(id) {
                var option = $('.mobile-service-option[data-id="' + id + '"]');
                if (option.length) {
                    names.push(option.data('name'));
                }
            });
            displayText = names.join(', ');
        }
        $('#mobile-services-display-text').text(displayText);
    }
    
    // Initialize with pre-selected services
    $('.mobile-service-option input[type="checkbox"]:checked').each(function() {
        var serviceId = $(this).closest('.mobile-service-option').data('id');
        selectedServices.push(serviceId);
        $(this).closest('.mobile-service-option').css('background-color', '#404040');
    });
    updateMobileServicesDisplay();
    
    // Function to search cities
    function searchCities(query) {
        clearTimeout(searchTimeout);
        
        if (query.length < 2) {
            $('#mobile_city_results').hide();
            return;
        }
        
        searchTimeout = setTimeout(function() {
            $.ajax({
                url: '{{ route("cities.search") }}',
                type: 'POST',
                data: {
                    query: query,
                    _token: '{{ csrf_token() }}'
                },
                success: function(data) {
                    // Clear previous results
                    $('#mobile_city_results').empty();
                    
                    if (data.length === 0) {
                        $('#mobile_city_results').append('<div class="dropdown-item text-left">No cities found</div>');
                    } else {
                        // Add each city to the results
                        $.each(data, function(index, city) {
                            const cityName = city.name + (city.country ? ` (${city.country})` : '');
                            const item = $('<div class="dropdown-item city-item text-left"></div>')
                                .text(cityName)
                                .data('id', city.id)
                                .data('name', city.name);
                            
                            $('#mobile_city_results').append(item);
                        });
                    }
                    
                    // Show the results dropdown
                    $('#mobile_city_results').show();
                },
                error: function() {
                    $('#mobile_city_results').html('<div class="dropdown-item text-danger text-left">Error loading cities</div>').show();
                }
            });
        }, 300);
    }
    
    // Event listener for input changes
    $('#mobile_city_search').on('input', function() {
        searchCities($(this).val().trim());
    });
    
    // Event listener for clicking on a city item
    $(document).on('click', '.city-item', function() {
        const cityId = $(this).data('id');
        const cityName = $(this).data('name');
        
        $('#mobile_selected_city').val(cityId);
        $('#mobile_selected_city_text').val(cityName.trim());
        $('#mobile_city_search').val(cityName.trim());
        $('#mobile_selected_city_name').text('Selected: ' + cityName.trim()).removeClass('text-muted').addClass('text-success');
        $('#mobile_city_results').hide();
        
        // Update Livewire component
        @this.set('city', cityId);
        @this.set('selectedcity', cityName.trim());
        @this.call('citySelected', cityId, cityName.trim());
    });
    
    // Event listener for clicking outside the dropdown
    $(document).on('click', function(event) {
        if (!$(event.target).closest('.position-relative').length) {
            $('#mobile_city_results').hide();
        }
    });
    
    // Event listener for focusing on the search input
    $('#mobile_city_search').on('focus', function() {
        if ($(this).val().trim().length >= 2) {
            searchCities($(this).val().trim());
        }
    });
    
    // If there's a previously selected city, show it in the input
    if ($('#mobile_selected_city').val()) {
        const cityName = $('#mobile_selected_city_name').text().replace('Selected: ', '').trim();
        $('#mobile_city_search').val(cityName);
    }
    
    // Initialize Select2 for specific fields
    function initSelect2Fields() {
        if (typeof $.fn.select2 === 'function') {
            $('.select2-single').each(function() {
                if (!$(this).hasClass('select2-hidden-accessible')) {
                    $(this).select2({
                        theme: 'classic',
                        width: '100%',
                        dropdownParent: $('body'),
                        dropdownCssClass: 'select2-dark-theme'
                    });
                }
            });
        }
    }
    
    // Initialize Chosen for services
    function initChosenSelects() {
        if (typeof $.fn.chosen === 'function') {
            $('.chosen-select').each(function() {
                var selectId = $(this).attr('id');
                
                // Destroy if already initialized
                if ($(this).data('chosen')) {
                    $(this).chosen('destroy');
                }
                
                // Initialize with mobile-friendly settings
                $(this).chosen({
                    width: "100%",
                    search_contains: true,
                    no_results_text: "No results found",
                    placeholder_text_multiple: "Select options",
                    disable_search_threshold: 10,
                    inherit_select_classes: true
                });
                
                // Add change handler for Livewire integration
                $(this).off('change').on('change', function() {
                    var property = $(this).attr('data-property') || 'sservices';
                    var selectedValues = $(this).val() || [];
                    
                    @this.set(property, selectedValues);
                });
            });
        }
    }
    
    // Initialize components
    initSelect2Fields();
    initChosenSelects();
    
    // Re-initialize after Livewire updates
    document.addEventListener('livewire:load', function() {
        Livewire.hook('message.processed', function() {
            setTimeout(function() {
                initSelect2Fields();
                initChosenSelects();
            }, 100);
        });
    });
});

if ($.fn.select2) {
        // Destroy existing Select2 instances first
        $('select.select-box, select.form-control').select2('destroy');
        
        // Reinitialize with dark theme
        $('select.select-box, select.form-control').select2({
            dropdownCssClass: 'select2-dark-dropdown',
            containerCssClass: 'select2-dark-container'
        });
    }
    
    // Additional CSS classes for the new instances
    $('<style>')
        .text(`
            .select2-dark-dropdown {
                background-color: #333 !important;
                color: #e6e6e6 !important;
            }
            .select2-dark-container {
                background-color: #333 !important;
                color: #e6e6e6 !important;
            }
        `)
        .appendTo('head');
</script>
@endpush
