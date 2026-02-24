<!-- Optimized CSS Loading Strategy -->

<!-- Critical CSS for above-the-fold content (already inlined in app-optimized.blade.php) -->

<!-- Load non-critical CSS asynchronously -->
<link rel="preload" href="{{smart_asset('assets/css/application.css')}}" as="style" onload="this.onload=null;this.rel='stylesheet'">
<noscript><link rel="stylesheet" href="{{smart_asset('assets/css/app2.css')}}"></noscript>

<!-- Touch icons optimized - only essential ones -->
<link href="{{smart_asset('assets/images/web/icon.png')}}" rel="apple-touch-icon" sizes="180x180" />
<link href="{{smart_asset('assets/images/web/icon2.png')}}" rel="apple-touch-icon" sizes="152x152" />

<!-- Load additional CSS only when needed -->
<script>
    // Function to load CSS asynchronously
    function loadCSS(href, before, media) {
        var doc = window.document;
        var ss = doc.createElement("link");
        var ref;
        if (before) {
            ref = before;
        } else {
            var refs = (doc.body || doc.getElementsByTagName("head")[0]).childNodes;
            ref = refs[refs.length - 1];
        }
        var sheets = doc.styleSheets;
        ss.rel = "stylesheet";
        ss.href = href;
        ss.media = "only x";
        function ready(cb) {
            if (doc.body) {
                return cb();
            }
            setTimeout(function() {
                ready(cb);
            });
        }
        ready(function() {
            ref.parentNode.insertBefore(ss, (before ? ref : ref.nextSibling));
        });
        var onloadcssdefined = function(cb) {
            var resolvedHref = ss.href;
            var i = sheets.length;
            while (i--) {
                if (sheets[i].href === resolvedHref) {
                    return cb();
                }
            }
            setTimeout(function() {
                onloadcssdefined(cb);
            });
        };
        function loadCB() {
            if (ss.addEventListener) {
                ss.removeEventListener("load", loadCB);
            }
            ss.media = media || "all";
        }
        if (ss.addEventListener) {
            ss.addEventListener("load", loadCB);
        }
        ss.onloadcssdefined = onloadcssdefined;
        onloadcssdefined(loadCB);
        return ss;
    }
    
    // Load additional styles after critical content
    window.addEventListener('load', function() {
        // Load any additional CSS files here
    });
</script>
