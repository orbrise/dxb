<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ ucfirst($gender ?? 'Female') }} Escorts in {{ ucfirst($selectedcity ?? 'Dubai') }} - Mobile Search</title>
    
    <!-- Favicon -->
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    
    <!-- Chosen CSS - Load before custom styles -->
    <link rel="stylesheet" href="{{smart_asset('chosen/chosen.css')}}">
    
    <!-- Select2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    
    <!-- Custom Styles -->
    <style>
        /* Font Awesome Global Fix */
        i.fa, i.fas, i.far, i.fab, i.fal,
        .fa, .fas, .far, .fab, .fal {
            font-family: "Font Awesome 5 Free" !important;
            font-weight: 900 !important;
            -moz-osx-font-smoothing: grayscale;
            -webkit-font-smoothing: antialiased;
            display: inline-block !important;
            font-style: normal !important;
            font-variant: normal !important;
            text-rendering: auto !important;
            line-height: 1;
        }

        /* Icon unicode content */
        .fa-user-circle:before { content: "\f2bd" !important; }
        .fa-user:before { content: "\f007" !important; }
        .fa-sign-out-alt:before { content: "\f2f5" !important; }
        .fa-caret-down:before { content: "\f0d7" !important; }
        .fa-search:before { content: "\f002" !important; }

        body {
            background-color: #222;
            color: #fff;
            padding-bottom: 70px; /* Space for fixed footer */
        }
        
        /* ... your existing styles ... */
        
        /* Enhanced Chosen styling for mobile */
        .chosen-container {
            width: 100% !important;
            z-index: 9999 !important; /* Ensure dropdown appears above other elements */
        }
        
        .chosen-container-multi .chosen-choices {
            background-color: #444 !important;
            border: 1px solid #555 !important;
            padding: 8px !important;
            min-height: 44px !important; /* Taller for mobile touch */
        }
        
        .chosen-container-multi .chosen-choices li.search-field input[type="text"] {
            color: #fff !important;
            height: 30px !important;
            font-size: 16px !important; /* Prevents iOS zoom */
        }
        
        .chosen-container .chosen-drop {
            background-color: #333 !important;
            border-color: #444 !important;
            z-index: 9999 !important;
            position: absolute !important;
            top: 100% !important;
            width: 100% !important;
        }
        
        .chosen-container .chosen-results {
            max-height: 240px !important; /* Taller results area for mobile */
        }
        
        .chosen-container .chosen-results li {
            color: #fff !important;
            padding: 10px 8px !important; /* Larger touch targets */
        }
        
        .chosen-container .chosen-results li.highlighted {
            background-color: #444 !important;
        }
        
        .chosen-container-multi .chosen-choices li.search-choice {
            background-color: #555 !important;
            border-color: #666 !important;
            color: #fff !important;
            margin: 4px 5px 4px 0 !important;
            padding: 6px 20px 6px 8px !important; /* Larger for touch */
            font-size: 14px !important;
        }
        
        .chosen-container-multi .chosen-choices li.search-choice .search-choice-close {
            top: 7px !important; /* Center the X button */
        }
        
        /* Fix for mobile modals */
        .modal-backdrop {
            z-index: 1040 !important;
        }
        
        .modal {
            z-index: 1050 !important;
        }
        
        /* Force Chosen to be visible */
        .chosen-container.chosen-container-multi {
            display: block !important;
            visibility: visible !important;
        }
        
          @media (max-width: 768px) {
            input[type="text"].form-control, input[type="email"].form-control, input[type="number"].form-control, input[type="search"].form-control, input[type="password"].form-control, textarea.form-control {
    margin-left: 6px;
    height: 37px;
}
        }
    </style>
    
    @livewireStyles
    @stack('css')
</head>
<body>

    <header id="header">
        @include('components.layouts.header')
      @yield('headerform')
      </header>


    <!-- Navbar -->
    
    
    <!-- Main Content -->
    <main>
        {{ $slot }}
    </main>
    
    <!-- Footer Navigation -->

    <!-- jQuery first -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js"></script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"></script>
    
    <!-- Chosen JS - Load before Livewire scripts -->
    <script src="{{ asset('chosen/chosen.jquery.js') }}" type="text/javascript"></script>
    
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script src="{{ asset('chosen/chosen.jquery.js')}}" type="text/javascript"></script>
    <script src="{{ asset('chosen/docsupport/prism.js')}}" type="text/javascript" charset="utf-8"></script>
    <script src="{{ asset('chosen/docsupport/init.js')}}" type="text/javascript" charset="utf-8"></script>
    
    @livewireScripts
    
    <script>
    // Listen for currency updates from Livewire and update the currency combobox
    document.addEventListener('livewire:init', () => {
        Livewire.on('currency-updated', (event) => {
            const currencySelect = document.querySelector('select[data-currency-combobox]');
            if (currencySelect) {
                // Update the value
                currencySelect.value = event.currencyId;
                
                // Trigger Select2 update if it's initialized
                if (typeof jQuery !== 'undefined' && jQuery(currencySelect).data('select2')) {
                    jQuery(currencySelect).val(event.currencyId).trigger('change.select2');
                }
            }
        });
    });
    </script>

    @include('components.layouts.footer')
    <!-- Custom Scripts - After Livewire scripts -->
    <script>
        // Check if Chosen is loaded
        $(document).ready(function() {
            if (!$.fn.chosen) {
                console.error('Chosen plugin not loaded!');
            } else {
                console.log('Chosen plugin loaded successfully');
                
                // Initialize all Chosen selects
                initializeChosenSelects();
            }
            
            // Initialize Select2 if available
            if ($.fn.select2) {
                $('.select2-single').select2({
                    theme: 'default',
                    width: '100%',
                    dropdownParent: $('body')
                });
            }
            
            // Re-initialize after Livewire updates
            document.addEventListener('livewire:load', function() {
                Livewire.hook('message.processed', function() {
                    setTimeout(initializeChosenSelects, 100);
                });
            });
        });
        
        // Function to initialize all Chosen selects
        function initializeChosenSelects() {
            console.log('Initializing all Chosen selects');
            
            $('.chosen-select').each(function() {
                var selectId = $(this).attr('id');
                console.log('Initializing Chosen for:', selectId);
                
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
                    var componentId = $(this).closest('[wire\\:id]').attr('wire:id');
                    var property = $(this).attr('data-property') || 'sservices';
                    var selectedValues = $(this).val() || [];
                    
                    console.log('Chosen select changed:', selectId, 'Values:', selectedValues);
                    
                    if (window.Livewire && componentId) {
                        window.Livewire.find(componentId).set(property, selectedValues);
                    }
                });
            });
        }
        
        // Re-initialize on window resize (helps with orientation changes)
        $(window).on('resize', function() {
            setTimeout(initializeChosenSelects, 100);
        });
        
        // Force Chosen to update when any modal is shown
        $(document).on('shown.bs.modal', function() {
            setTimeout(initializeChosenSelects, 100);
        });
    </script>
    
    @stack('js')
</body>
</html>