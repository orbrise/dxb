<!doctype html>
<html ⚡ lang="en">
<head>
    <meta charset="utf-8">
    <script async src="https://cdn.ampproject.org/v0.js"></script>
    <title>@yield('title', 'Massage Republic - Escorts Directory')</title>
    <link rel="canonical" href="@yield('canonical', url('/'))">
    <meta name="viewport" content="width=device-width,minimum-scale=1,initial-scale=1">
    <meta name="description" content="@yield('description', 'Find escorts and adult services in your city')">
    
    <!-- AMP Boilerplate -->
    <style amp-boilerplate>body{-webkit-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-moz-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-ms-animation:-amp-start 8s steps(1,end) 0s 1 normal both;animation:-amp-start 8s steps(1,end) 0s 1 normal both}@-webkit-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-moz-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-ms-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-o-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}</style>
    <noscript><style amp-boilerplate>body{-webkit-animation:none;-moz-animation:none;-ms-animation:none;animation:none}</style></noscript>
    
    <!-- AMP Components -->
    <script async custom-element="amp-carousel" src="https://cdn.ampproject.org/v0/amp-carousel-0.1.js"></script>
    <script async custom-element="amp-sidebar" src="https://cdn.ampproject.org/v0/amp-sidebar-0.1.js"></script>
    <script async custom-element="amp-accordion" src="https://cdn.ampproject.org/v0/amp-accordion-0.1.js"></script>
    
    <!-- Custom Styles (max 75KB) -->
    <style amp-custom>
        /* Base Styles */
        * {
            box-sizing: border-box;
        }
        
        body {
            margin: 0;
            padding: 0;
            background-color: #232323;
            color: #ffffff;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 14px;
            line-height: 1.5;
        }
        
        a {
            color: #f4b827;
            text-decoration: none;
        }
        
        a:hover {
            text-decoration: underline;
        }
        
        /* Header */
        .amp-header {
            background-color: #1a1a1a;
            padding: 15px 20px;
            border-bottom: 1px solid #333;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .amp-header-inner {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .amp-logo {
            color: #f4b827;
            font-size: 20px;
            font-weight: bold;
        }
        
        .amp-nav {
            display: flex;
            gap: 15px;
        }
        
        .amp-nav a {
            color: #ffffff;
            padding: 8px 15px;
            border-radius: 4px;
            background-color: #333;
        }
        
        .amp-nav a:hover {
            background-color: #444;
            text-decoration: none;
        }
        
        /* Main Container */
        .amp-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        /* Page Title */
        .amp-page-title {
            color: #f4b827;
            font-size: 24px;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #333;
        }
        
        /* Card Styles */
        .amp-card {
            background-color: #2c2c2c;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            border: 1px solid #3a3a3a;
        }
        
        .amp-card:hover {
            border-color: #f4b827;
        }
        
        /* Profile Listing */
        .amp-profile-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        
        .amp-profile-item {
            display: flex;
            background-color: #2c2c2c;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #3a3a3a;
        }
        
        .amp-profile-image {
            flex-shrink: 0;
            width: 120px;
            height: 150px;
            background-color: #1a1a1a;
        }
        
        .amp-profile-info {
            padding: 15px;
            flex-grow: 1;
        }
        
        .amp-profile-name {
            color: #f4b827;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 8px;
        }
        
        .amp-profile-details {
            color: #ccc;
            font-size: 13px;
            margin-bottom: 5px;
        }
        
        .amp-profile-price {
            color: #4CAF50;
            font-weight: bold;
            margin-top: 10px;
        }
        
        .amp-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 11px;
            margin-right: 5px;
        }
        
        .amp-badge-premium {
            background-color: #f4b827;
            color: #000;
        }
        
        .amp-badge-verified {
            background-color: #4CAF50;
            color: #fff;
        }
        
        .amp-badge-featured {
            background-color: #2196F3;
            color: #fff;
        }
        
        /* Button Styles */
        .amp-btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #f4b827;
            color: #000;
            border-radius: 5px;
            font-weight: bold;
            text-align: center;
        }
        
        .amp-btn:hover {
            background-color: #d4a01f;
            text-decoration: none;
        }
        
        .amp-btn-outline {
            background-color: transparent;
            border: 2px solid #f4b827;
            color: #f4b827;
        }
        
        .amp-btn-outline:hover {
            background-color: #f4b827;
            color: #000;
        }
        
        /* Grid System */
        .amp-row {
            display: flex;
            flex-wrap: wrap;
            margin: 0 -10px;
        }
        
        .amp-col {
            padding: 0 10px;
            width: 100%;
        }
        
        @media (min-width: 768px) {
            .amp-col-md-6 { width: 50%; }
            .amp-col-md-4 { width: 33.333%; }
            .amp-col-md-3 { width: 25%; }
        }
        
        /* Footer */
        .amp-footer {
            background-color: #1a1a1a;
            padding: 30px 20px;
            margin-top: 40px;
            border-top: 1px solid #333;
            text-align: center;
        }
        
        .amp-footer-links {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .amp-footer-links a {
            color: #999;
        }
        
        .amp-footer-copy {
            color: #666;
            font-size: 12px;
        }
        
        /* Pagination */
        .amp-pagination {
            display: flex;
            justify-content: center;
            gap: 5px;
            margin-top: 30px;
            flex-wrap: wrap;
        }
        
        .amp-pagination a,
        .amp-pagination span {
            display: inline-block;
            padding: 8px 15px;
            background-color: #333;
            color: #fff;
            border-radius: 4px;
        }
        
        .amp-pagination .active {
            background-color: #f4b827;
            color: #000;
        }
        
        /* Services Tags */
        .amp-services {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            margin-top: 10px;
        }
        
        .amp-service-tag {
            display: inline-block;
            padding: 3px 8px;
            background-color: #3a3a3a;
            color: #ccc;
            border-radius: 3px;
            font-size: 11px;
        }
        
        /* Gallery */
        .amp-gallery {
            margin-bottom: 20px;
        }
        
        /* Reviews */
        .amp-review {
            background-color: #333;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 10px;
        }
        
        .amp-review-author {
            color: #f4b827;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .amp-review-text {
            color: #ccc;
            font-size: 13px;
        }
        
        .amp-stars {
            color: #f4b827;
        }
        
        /* Notice Box */
        .amp-notice {
            background-color: #2c2c2c;
            border-left: 4px solid #f4b827;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .amp-notice-title {
            color: #f4b827;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        /* City Grid */
        .amp-city-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
        }
        
        @media (min-width: 768px) {
            .amp-city-grid {
                grid-template-columns: repeat(4, 1fr);
            }
        }
        
        .amp-city-item {
            background-color: #333;
            padding: 15px;
            text-align: center;
            border-radius: 5px;
        }
        
        .amp-city-item:hover {
            background-color: #444;
        }
        
        /* Utility */
        .amp-text-center { text-align: center; }
        .amp-text-muted { color: #999; }
        .amp-mt-20 { margin-top: 20px; }
        .amp-mb-20 { margin-bottom: 20px; }
    </style>
    
    @yield('head')
</head>
<body>
    <!-- Header -->
    <header class="amp-header">
        <div class="amp-header-inner">
            <a href="/amp" class="amp-logo">Massage Republic</a>
            <nav class="amp-nav">
                <a href="/amp/female-escorts-in-dubai">Dubai</a>
                <a href="/sign-in">Sign In</a>
            </nav>
        </div>
    </header>
    
    <!-- Main Content -->
    <main class="amp-container">
        @yield('content')
    </main>
    
    <!-- Footer -->
    <footer class="amp-footer">
        <div class="amp-footer-links">
            <a href="/amp">Home</a>
            <a href="/toc">Terms</a>
            <a href="/privacy">Privacy</a>
            <a href="/contact">Contact</a>
        </div>
        <div class="amp-footer-copy">
            &copy; {{ date('Y') }} Massage Republic. All rights reserved.
        </div>
        <div class="amp-mt-20 amp-text-muted" style="font-size: 11px;">
            Alternative domains: 
            <a href="https://ae.massagerepublic.com.co">ae.massagerepublic.com.co</a> | 
            <a href="https://pk.massagerepublic.com.co">pk.massagerepublic.com.co</a> |
            <a href="https://escorts.ninja">escorts.ninja</a>
        </div>
    </footer>
</body>
</html>
