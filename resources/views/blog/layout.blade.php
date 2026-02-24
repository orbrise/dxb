<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <title>@yield('title', 'Blog') - Massage Republic</title>
    <meta name="description" content="@yield('meta_description', 'The Massage Republic Blog - helping adult providers and users to have more fun and success.')">
    <meta name="keywords" content="@yield('meta_keywords', '')">
    
    <!-- Open Graph -->
    <meta property="og:title" content="@yield('og_title', 'Massage Republic Blog')">
    <meta property="og:description" content="@yield('og_description', 'The Massage Republic Blog')">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:image" content="@yield('og_image', asset('assets/images/logo.png'))">
    
    <link rel="canonical" href="{{ url()->current() }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&family=Arimo:wght@400;500;700&display=swap" rel="stylesheet">
    
    <style>
        /* Reset & Base */
        *, *::before, *::after {
            box-sizing: border-box;
        }
        
        .btn-close {
            display:none;
        }

        body {
            margin: 0;
            padding: 0;
            font: 15px/1.5 Arimo, Helvetica, Arial, sans-serif;
            color: #ffffff;
            background: url('https://d257pz9kz95xf4.cloudfront.net/assets/layout/texture-b9beac30c0dfa74797a20c4f2547cff4c3bc597319acc0c53a60c95abdba3e51.png') #000000;
        }
        
        a {
            color: #f4b827;
            text-decoration: none;
        }
        
        a:hover {
            text-decoration: underline;
            
        }
        
        h1, h2, h3, h4, h5, h6 {
            font-family: Lato, Helvetica, Arial, sans-serif;
            font-weight: 700;
            margin: 0;
        }
        
        img {
            max-width: 100%;
            height: auto;
        }
        
        /* Layout */
        .content-outer {
            max-width: 1020px;
            margin: 0 auto;
            padding: 0 15px;
        }
        
        /* Header */
        .header-outer {
            background: #000000;
            width: 100%;
        }
        
        .header-inner {
            max-width: 1020px;
            margin: 0 auto;
            padding: 22px 16px;
            text-align: left;
            position: relative;
        }
        
        .header-logo img {
            max-width: 400px;
            height: auto;
            object-fit: contain;
        }
        
        /* Hero CTA */
        .hero {
            width: 100%;
            background: #000000;
            border-bottom: 1px solid #333;
        }
        
        .hero-inner {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            font-size: 1.5rem;
            font-weight: normal;
            padding: 1.5rem 16px 1rem;
            text-align: center;
            align-items: center;
            justify-content: center;
            border-top: 1px solid #222222;
            
        }
        
        .btn-primary {
            display: inline-flex;
            align-items: center;
            background-color: #f4b827;
            padding: 0.5rem 1rem;
            color: #000000;
            text-decoration: none;
            border-radius: 10px;
            font-size: 1.2rem;
            font-weight: bold;
        }
        
        .btn-primary:hover {
            text-decoration: none;
            background-color: #d9a520;
        }
        
        .btn-primary svg {
            margin-left: 0.5rem;
        }
        
        /* Main Layout */
        .main-outer {
            padding: 30px 0;
        }
        
        .columns-inner {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        
        .column-center-outer {
            flex: 1;
            min-width: 0;
        }
        
        .column-right-outer {
            width: 310px;
            flex-shrink: 0;
        }
        
        @media (max-width: 900px) {
            .column-right-outer {
                width: 100%;
            }
        }
        
        /* Post Cards */
        .date-outer {
            position: relative;
            margin: 0 0 20px;
            padding: 8px 20px;
            background-color: rgba(29, 29, 29, 0.6);
            border: 1px solid #222222;
            border-radius: 10px;
        }
        
        .date-outer:first-child {
            margin-top: 0;
        }
        
        .date-header {
            color: #bebebe;
            font-size: 1.1em;
            display: block;
            padding: 0.5em 0;
            float: right;
        }
        
        .post-outer {
            padding: 15px 0;
        }
        
        .post-outer:first-child {
            padding-top: 0;
        }
        
        h1.post-title, h1.post-title a {
            font-size: 1.2em;
            color: #ffffff;
            margin: 0 0 0.5em;
        }
        
        h1.post-title a:hover {
            color: #f4b827;
            text-decoration: underline;
        }
        
        .post-body {
            line-height: 1.7em;
            position: relative;
            font-size: 1.05em;
            color: #e0e0e0;
        }
        
        .post-body p {
            margin: 0 0 1.5em 0;
            text-align: justify;
        }
        
        .post-body p:last-child {
            margin-bottom: 0;
        }
        
        .post-body img {
            padding: 8px;
            background: #222222;
            border: 1px solid #000000;
            border-radius: 0;
            max-width: 100%;
            display: block;
            margin: 1.5em auto;
            object-fit: cover;
        }
        
        .post-body h2, .post-body h3 {
            margin: 1.5em 0 1em 0;
            padding: 0;
            font-size: 1.3em;
            color: #ffffff;
            font-weight: bold;
        }
        
        .post-body h2:first-child, .post-body h3:first-child {
            margin-top: 0;
        }
        
        .post-body ul, .post-body ol {
            margin: 0 0 1.5em 0;
            padding-left: 2.5em;
        }
        
        .post-body li {
            margin-bottom: 0.6em;
            line-height: 1.6em;
        }
        
        .post-body strong {
            color: #ffffff;
        }
        
        .post-body a {
            color: #f4b827;
            text-decoration: underline;
        }
        
        .post-body a:hover {
            color: #d9a520;
        }
        
        .post-body blockquote {
            font-style: italic;
            margin: 1.5em 10px;
            padding: 0.5em 15px;
            border-left: 4px solid #f4b827;
            color: #999;
            background: rgba(0,0,0,0.2);
        }
        
        .read-more-link {
            color: #f4b827;
            text-decoration: underline;
        }
        
        .read-more-link:hover {
            color: #d9a520;
            text-decoration: underline;
        }
         
        .post-footer {
            margin: 1.5em 0 0;
            padding-top: 1em;
            border-top: 1px solid #333;
            font-size: 0.9em;
            color: #888;
        }
        
        .post-author, .post-timestamp {
            margin-right: 15px;
        }
        
        .post-meta-info {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            color: #888;
            font-size: 0.9em;
        }
        
        /* Share Icons */
        .share-icons {
            display: flex;
            align-items: center;
            gap: 2px;
            margin-top: 10px;
        }
        
        .share-icons a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 15px;
            height: 15px;
            background: #333;
            border-radius: 3px;
            color: #fff;
            text-decoration: none;
            transition: background 0.2s;
        }
        
        .share-icons a:hover {
            text-decoration: none;
        }
        
        .share-icons a.share-email:hover { background: #EA4335; }
        .share-icons a.share-x:hover { background: #000; }
        .share-icons a.share-facebook:hover { background: #1877F2; }
        .share-icons a.share-pinterest:hover { background: #E60023; }
        .share-icons a.share-linkedin:hover { background: #0A66C2; }
        .share-icons a.share-whatsapp:hover { background: #25D366; }
        
        .share-icons svg {
            width: 12px;
            height: 12px;
            fill: currentColor;
        }
        
        /* Sidebar Widgets */
        .sidebar-widget {
            background-color: rgba(29, 29, 29, 0.6);
            border: 1px solid #222222;
            border-radius: 10px;
            padding: 0 15px 15px;
            margin-bottom: 20px;
        }
        
        .sidebar-widget h3.title {
            font-size: 14px;
            font-weight: bold;
            padding: 0.6em 0 0.5em;
            margin: 0 0 0.5em;
            border-bottom: 1px solid #333;
            color: #ffffff;
        }
        
        .sidebar-widget .widget-content {
            font-size: 14px;
            line-height: 1.6;
        }
        
        .sidebar-widget ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .sidebar-widget ul li {
            padding: 0.25em 0;
        }
        
        /* Blog Archive */
        .archive-list ul {
            padding-left: 25px;
        }
        
        .archive-list .toggle {
            cursor: pointer;
            color: #666;
            margin-right: 5px;
        }
        
        .archive-list .post-count {
            color: #666;
            font-size: 0.9em;
        }
        
        /* Pagination */
        .blog-pager {
            padding: 15px;
            font-size: 120%;
            background-color: #141414;
            border: 1px solid #222222;
            border-radius: 10px;
            margin-top: 1em;
            text-align: center;
        }
        
        .blog-pager a {
            margin: 0 10px;
        }
        
        /* Footer */
        .footer-outer {
            margin-top: 0;
            padding: 0;
            color: #ffffff;
            border-top: 1px solid #222222;
            background: #141414;
            width: 100%;
        }
        
        .footer-inner {
            padding: 20px 16px;
            text-align: center;
        }
        
        /* Mobile Menu */
        .btn-offcanvas {
            cursor: pointer;
            appearance: none;
            background: transparent;
            border: none;
            padding: 1rem;
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            z-index: 100;
            display: none;
        }
        
        .btn-close {
            cursor: pointer;
            appearance: none;
            background: none;
            border: none;
            padding: 1.3rem;
            position: absolute;
            top: 0;
            right: 0;
            z-index: 1;
        }
        
        @media (max-width: 900px) {
            .btn-offcanvas {
                display: block;
            }
            
            .column-right-outer.offcanvas {
                position: fixed;
                right: 0;
                top: 0;
                transform: translateX(100%);
                z-index: 200;
                visibility: hidden;
                transition: all 0.3s ease-in-out;
                width: 100%;
                height: 100vh;
                overflow-y: auto;
                background: #1d1d1d;
            }
            
            .column-right-outer.offcanvas.open {
                visibility: visible;
                transform: translateX(0);
            }
        }
        
        /* Category/Tag Links */
        .category-link {
            display: inline-block;
            background: #f4b827;
            color: #000;
            padding: 2px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .category-link:hover {
            background: #d9a520;
            text-decoration: none;
        }
        
        .tag-link {
            display: inline-block;
            background: #333;
            color: #f4b827;
            padding: 3px 10px;
            border-radius: 4px;
            font-size: 12px;
            margin: 2px;
        }
        
        .tag-link:hover {
            background: #444;
            text-decoration: none;
        }
        
        /* Featured Image */
        .featured-image {
            margin: 1em 0;
        }
        
        .featured-image img {
            width: 100%;
            max-height: 400px;
            object-fit: cover;
        }
        
        /* Search Form */
        .search-form {
            display: flex;
            gap: 5px;
        }
        
        .search-form input[type="text"] {
            flex: 1;
            padding: 8px 12px;
            border: 1px solid #333;
            border-radius: 5px;
            background: #222;
            color: #fff;
            font-size: 14px;
        }
        
        .search-form button {
            padding: 8px 15px;
            background: #f4b827;
            color: #000;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }
        
        .search-form button:hover {
            background: #d9a520;
        }
        
        /* Clear float */
        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }
        @media (max-width: 768px) {
        .header-logo img {
    max-width: 250px;
}

.column-right-outer.offcanvas.open {
        padding-top: 2rem;
    }

     .btn-close {
            display:block;
        }

        }


        
    </style>
    @stack('styles')
</head>
<body>
    <!-- Header (Full Width) -->
    <header class="header-outer">
        <div class="header-inner">
            <!-- Mobile Menu Button -->
            <button class="btn-offcanvas" aria-controls="offcanvas" data-toggle="offcanvas" type="button">
                <svg fill="#fff" height="32" viewBox="0 0 16 16" width="32" xmlns="http://www.w3.org/2000/svg">
                    <path d="M2.5 11.5A.5.5 0 0 1 3 11h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4A.5.5 0 0 1 3 7h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4A.5.5 0 0 1 3 3h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5z" fill-rule="evenodd"></path>
                </svg>
            </button>
            
            <div class="header-logo">
                <a href="{{ route('blog.index') }}">
                    <img src="https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEg-5Von9iV8OPwB_zFqJZsiqhiPeDk4wYGmHtP2OHnyn6GGb4FHZGBSTqMIa1COcoraRlakWkL5S1KyJNlFmgXGV4TqL5sK9zwQ5SksQdLC0f8p_RgKJtZxZcpz4L7eSJPbyw0zLGnlhdQ/s1600/mr-logo-blog_v1.png" 
                         alt="Massage Republic Blog" 
                         height="50" 
                         width="400">
                </a>
            </div>
        </div>
    </header>
    
    <!-- Hero CTA (Full Width) -->
    <div class="hero">
        <div class="hero-inner">
            Trying to get to massagerepublic.com.co?
            <a class="btn-primary" href="https://massagerepublic.com.co/">
                <span>Visit the main site</span>
                <svg xmlns="http://www.w3.org/2000/svg" height="36" viewBox="0 -960 960 960" width="36" fill="#000">
                    <path d="M646-440H200q-17 0-28.5-11.5T160-480q0-17 11.5-28.5T200-520h446L532-634q-12-12-11.5-28t11.5-28q12-12 28.5-12.5T589-691l183 183q6 6 8.5 13t2.5 15q0 8-2.5 15t-8.5 13L589-269q-12 12-28.5 11.5T532-270q-11-12-11.5-28t11.5-28l114-114Z"></path>
                </svg>
            </a>
        </div>
    </div>
    
    <div class="content">
        <div class="content-outer">
            <!-- Main Content -->
            <div class="main-outer">
                <div class="columns-inner">
                    <!-- Main Column -->
                    <div class="column-center-outer">
                        @yield('content')
                    </div>
                    
                    <!-- Sidebar -->
                    <div class="column-right-outer offcanvas" id="offcanvas">
                        <!-- Close Button -->
                        <button class="btn-close" aria-label="Close sidebar" onclick="document.getElementById('offcanvas').classList.remove('open');">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                        </button>
                        
                        <div class="column-right-inner">
                            <!-- Go to main site -->
                            <div class="sidebar-widget">
                                <h3 class="title">This is our blog</h3>
                                <div class="widget-content">
                                    <strong><a href="https://massagerepublic.com.co">Go to the main site</a></strong>
                                </div>
                            </div>
                            
                            <!-- About -->
                            <div class="sidebar-widget">
                                <h3 class="title">About Massage Republic</h3>
                                <div class="widget-content">
                                    <p><strong><a href="https://massagerepublic.com.co/">MassageRepublic.com.co</a></strong> was created to help service providers connect with their audience and serve the needs visitors in a responsible way.</p>
                                    <p>This blog is about new features on the site and feedback from advertisers and users. Go to the <a href="{{ route('blog.index') }}"><strong>blog homepage</strong></a>.</p>
                                </div>
                            </div>
                            
                            <!-- Search -->
                            <div class="sidebar-widget">
                                <h3 class="title">Search</h3>
                                <div class="widget-content">
                                    <form action="{{ route('blog.search') }}" method="GET" class="search-form">
                                        <input type="text" name="q" placeholder="Search articles..." value="{{ request('q') }}">
                                        <button type="submit">Go</button>
                                    </form>
                                </div>
                            </div>
                            
                            <!-- Categories -->
                            @if(isset($categories) && $categories->count() > 0)
                            <div class="sidebar-widget">
                                <h3 class="title">Categories</h3>
                                <div class="widget-content">
                                    <ul>
                                        @foreach($categories as $cat)
                                            <li>
                                                <a href="{{ route('blog.category', $cat->slug) }}">{{ $cat->name }}</a>
                                                <span style="color: #666;">({{ $cat->posts_count }})</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                            @endif
                            
                            <!-- Tags -->
                            @if(isset($popularTags) && $popularTags->count() > 0)
                            <div class="sidebar-widget">
                                <h3 class="title">Popular Tags</h3>
                                <div class="widget-content">
                                    @foreach($popularTags as $tag)
                                        <a href="{{ route('blog.tag', $tag->slug) }}" class="tag-link">{{ $tag->name }}</a>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                            
                            <!-- Blog Archive -->
                            @if(isset($archiveData) && count($archiveData) > 0)
                            <div class="sidebar-widget">
                                <h3 class="title">Blog Archive</h3>
                                <div class="widget-content archive-list">
                                    <ul style="list-style: none; padding: 0; margin: 0;">
                                        @foreach($archiveData as $year => $yearData)
                                            <li class="archive-year">
                                                <span class="toggle" onclick="toggleArchive(this)" style="cursor: pointer;">▶</span>
                                                <a href="{{ route('blog.archive', ['year' => $year]) }}">{{ $year }}</a>
                                                <span class="post-count">({{ $yearData['count'] }})</span>
                                                <ul class="archive-months" style="display: none; list-style: none; padding-left: 25px; margin: 5px 0 0 0;">
                                                    @foreach($yearData['months'] as $month => $monthData)
                                                        <li class="archive-month">
                                                            <span class="toggle" onclick="toggleArchive(this)" style="cursor: pointer;">▶</span>
                                                            <a href="{{ route('blog.archive', ['year' => $year, 'month' => $month]) }}">{{ $monthData['name'] }}</a>
                                                            <span class="post-count">({{ $monthData['count'] }})</span>
                                                            <ul class="archive-posts" style="display: none; list-style: none; padding-left: 25px; margin: 5px 0 0 0;">
                                                                @foreach($monthData['posts'] as $archivePost)
                                                                    <li style="padding: 2px 0;">
                                                                        <a href="{{ route('blog.show', $archivePost['slug']) }}">{{ $archivePost['title'] }}</a>
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Footer (Full Width) -->
    <footer class="footer-outer">
        <div class="footer-inner">
            <p>&copy; {{ date('Y') }} Massage Republic</p>
        </div>
    </footer>
    
    <script>
        // Mobile menu toggle
        const offcanvas = document.querySelector('.offcanvas');
        
        document.addEventListener('click', function(event) {
            if (event.target.closest('[data-toggle]')) {
                offcanvas.classList.add('open');
            } else if (event.target.closest('[data-dismiss]')) {
                offcanvas.classList.remove('open');
            }
        }, false);
        
        // Archive toggle function
        function toggleArchive(el) {
            const parentLi = el.parentElement;
            const subList = parentLi.querySelector(':scope > ul');
            if (subList) {
                if (subList.style.display === 'none') {
                    subList.style.display = 'block';
                    el.textContent = '▼';
                } else {
                    subList.style.display = 'none';
                    el.textContent = '▶';
                }
            }
        }
    </script>
    
    @stack('scripts')
</body>
</html>
