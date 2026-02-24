<link href="{{smart_asset('assets/images/web/icon3.png')}}" rel="apple-touch-icon" />
     
    <!-- FOUC Prevention: Hide body until styles are loaded -->
    <style id="fouc-prevention">
      html { visibility: hidden; }
      html.styles-loaded { visibility: visible; }
    </style>
    
    <!-- CSS Variables for dynamic asset URLs -->
    <style>
      :root {
        --asset-bg-pic1: url({{smart_asset('assets/images/web/pic1.png')}});
        --asset-bg-pic2: url({{smart_asset('assets/images/web/pic2.png')}});
        --asset-bg-pic3: url({{smart_asset('assets/images/web/pic3.png')}});
        --asset-logo: url({{smart_asset('assets/images/web/logo.png')}});
      } 
      /* Critical base styles to prevent white flash */
      body {
        background: #333 !important;
        margin: 0;
        color: #fff;
      }
    </style>
    
    <!-- Critical CSS - Load synchronously -->
    <link rel="stylesheet" href="{{smart_asset('assets/css/app.css')}}" />
    
    <!-- Site inline styles (extracted from blade, now cached by browser) -->
    <link rel="stylesheet" href="{{smart_asset('assets/css/site-inline.min.css')}}" />
    
    <!-- Load all essential CSS synchronously to prevent FOUC -->
    <link rel="stylesheet" href="{{smart_asset('assets/css/app2.css')}}" />
    <link rel="stylesheet" href="{{smart_asset('assets/css/app3.css')}}" />
    <link rel="stylesheet" href="{{smart_asset('assets/css/app4.css')}}" />
    
    <!-- Reveal page immediately after CSS loads -->
    <script>document.documentElement.classList.add('styles-loaded');</script>
     
    <!-- Font Awesome 5.15.3 - Load async -->
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'" 
          integrity="sha512-iBBXm8fW90+nuLcSKlbmrPcLa0OT92xO1BIsZ+ywDWZCvqsWgccV3gFoRBv0z+8dLJgyAHIhR35VZc2oM/gI1w==" 
          crossorigin="anonymous" 
          referrerpolicy="no-referrer" />
    <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" /></noscript>
    
    <!-- Critical Font Awesome override (small, must be inline) -->
    <style>
      @font-face {
        font-family: "Font Awesome 5 Free";
        font-style: normal;
        font-weight: 900;
        font-display: swap;
        src: url('{{smart_asset("assets/fonts/fa-solid-900.woff2")}}') format("woff2");
      }
      i[class*="fa"], .fa, .fas, .far, .fab, .fal,
      span[class*="fa"], div[class*="fa"] {
        font-family: "Font Awesome 5 Free" !important;
        font-weight: 900 !important;
        display: inline-block !important;
        font-style: normal !important;
        -webkit-font-smoothing: antialiased !important;
        -moz-osx-font-smoothing: grayscale !important;
      }
      *[class*="fa-"]:before {
        font-family: "Font Awesome 5 Free" !important;
        font-weight: 900 !important;
      }
    </style>
    
    <!-- Print stylesheet -->
    <link rel="stylesheet" media="print" href="{{smart_asset('assets/css/app5.css')}}" />
