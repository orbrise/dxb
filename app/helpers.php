<?php

use App\Services\SeoService;

if (!function_exists('getSeoData')) {
    /**
     * Get SEO data for current page or specific URL
     */
    function getSeoData($url = null)
    {
        $seoService = new SeoService();
        
        if ($url) {
            return $seoService->getSeoFromUrl($url);
        }
        
        return $seoService->getCurrentPageSeo();
    }
}

if (!function_exists('renderSeoTags')) {
    /**
     * Render SEO meta tags HTML
     */
    function renderSeoTags($seoData = null)
    {
        if (!$seoData) {
            $seoData = getSeoData();
        }
        
        $html = '';
        
        if (!empty($seoData['title'])) {
            $html .= '<title>' . e($seoData['title']) . '</title>' . "\n";
            $html .= '<meta property="og:title" content="' . e($seoData['title']) . '">' . "\n";
        }
        
        if (!empty($seoData['description'])) {
            $html .= '<meta name="description" content="' . e($seoData['description']) . '">' . "\n";
            $html .= '<meta property="og:description" content="' . e($seoData['description']) . '">' . "\n";
        }
        
        if (!empty($seoData['keywords'])) {
            $html .= '<meta name="keywords" content="' . e($seoData['keywords']) . '">' . "\n";
        }
        
        // Additional meta tags
        $html .= '<meta property="og:type" content="website">' . "\n";
        $html .= '<meta property="og:url" content="' . e(request()->url()) . '">' . "\n";
        
        return $html;
    }
}

if (!function_exists('getSeoTitle')) {
    /**
     * Get SEO title for current page
     */
    function getSeoTitle($url = null)
    {
        $seoData = getSeoData($url);
        return $seoData['title'] ?? config('app.name');
    }
}

if (!function_exists('getSeoDescription')) {
    /**
     * Get SEO description for current page
     */
    function getSeoDescription($url = null)
    {
        $seoData = getSeoData($url);
        return $seoData['description'] ?? '';
    }
}

if (!function_exists('getSeoKeywords')) {
    /**
     * Get SEO keywords for current page
     */
    function getSeoKeywords($url = null)
    {
        $seoData = getSeoData($url);
        return $seoData['keywords'] ?? '';
    }
}

if (!function_exists('getSeoContent')) {
    /**
     * Get SEO content for current page
     */
    function getSeoContent($url = null)
    {
        $seoData = getSeoData($url);
        return $seoData['content'] ?? '';
    }
}
if (!function_exists('getCountryFromDomain')) {
    /**
     * Detect country from domain/subdomain
     * Examples: ae.domain.com -> UAE, pk.domain.com -> Pakistan
     * Returns Country model or null
     */
    function getCountryFromDomain($domain = null)
    {
        if (!$domain) {
            $domain = request()->getHost();
        }
        
        // Extract subdomain prefix (e.g., 'ae' from 'ae.domain.com')
        $parts = explode('.', $domain);
        
        // If domain has subdomain (more than 2 parts)
        if (count($parts) > 2) {
            $prefix = strtolower($parts[0]);
            
            // Find country by domain_prefix
            $country = \App\Models\Country::where('domain_prefix', $prefix)->first();
            
            if ($country) {
                return $country;
            }
        }
        
        // Default to UAE if no match found
        return \App\Models\Country::where('domain_prefix', 'ae')
                ->orWhere('nicename', 'like', '%united arab emirates%')
                ->first();
    }
}

if (!function_exists('getCurrentCountry')) {
    /**
     * Get current country based on domain (detected fresh each time)
     * Note: Not cached in session because users might switch between subdomains
     */
    function getCurrentCountry()
    {
        // Always detect from current domain (don't cache in session)
        // This ensures ae.domain.com and pk.domain.com work correctly
        return getCountryFromDomain();
    }
}

if (!function_exists('getDomainPrefix')) {
    /**
     * Get domain prefix from current request
     * Returns: 'ae', 'pk', 'in', etc. or null
     */
    function getDomainPrefix()
    {
        $domain = request()->getHost();
        $parts = explode('.', $domain);
        
        if (count($parts) > 2) {
            return strtolower($parts[0]);
        }
        
        return null;
    }
}

if (!function_exists('getFeaturedCitySlug')) {
    /**
     * Get the city slug for navigation links
     * Priority: 1) Current route city parameter, 2) Featured city for country, 3) Fallback
     */
    function getFeaturedCitySlug()
    {
        // First, check if we're on a page with a city route parameter
        $currentCity = request()->route('city');
        if ($currentCity) {
            return $currentCity; // Use the city from the current URL
        }
        
        // Otherwise, get the country and find its featured city
        $country = getCurrentCountry();
        
        if (!$country) {
            return 'dubai'; // Default fallback
        }
        
        // Get first featured city for this country
        $featuredCity = \App\Models\City::where('country', $country->nicename)
            ->where('is_featured', 1)
            ->orderBy('name', 'asc')
            ->first();
        
        if ($featuredCity) {
            return strtolower(str_replace(' ', '-', $featuredCity->name));
        }
        
        // If no featured city, get first city for this country
        $firstCity = \App\Models\City::where('country', $country->nicename)
            ->orderBy('name', 'asc')
            ->first();
        
        if ($firstCity) {
            return strtolower(str_replace(' ', '-', $firstCity->name));
        }
        
        // Ultimate fallback
        return 'dubai';
    }
}

if (!function_exists('smart_image_path')) {
    /**
     * Get image path with WebP priority fallback
     * Returns the path for WebP if available, otherwise jpg, then png, then original
     * 
     * @param string $basePath Base path without extension or with original extension
     * @param string $originalExt Original file extension
     * @return array ['webp' => path, 'fallback' => path]
     */
    function smart_image_path($imagePath)
    {
        if (empty($imagePath)) {
            return ['webp' => null, 'fallback' => null, 'original' => null];
        }
        
        // Get path info
        $pathInfo = pathinfo($imagePath);
        $basePath = ($pathInfo['dirname'] !== '.' ? $pathInfo['dirname'] . '/' : '') . $pathInfo['filename'];
        $originalExt = $pathInfo['extension'] ?? 'jpg';
        
        return [
            'webp' => $basePath . '.webp',
            'jpg' => $basePath . '.jpg',
            'png' => $basePath . '.png',
            'original' => $imagePath,
            'base' => $basePath,
            'ext' => $originalExt
        ];
    }
}

if (!function_exists('webp_image')) {
    /**
     * Generate an img tag that tries WebP first, falls back to jpg/png via onerror
     * 
     * @param string $imagePath Full image path (e.g., userimages/1/2/image.jpg)
     * @param string $alt Alt text for the image
     * @param array $attrs Additional attributes (class, width, height, loading, style)
     * @return string HTML img element with WebP priority
     */
    function webp_image($imagePath, $alt = '', $attrs = [])
    {
        if (empty($imagePath)) {
            return '';
        }
        
        $paths = smart_image_path($imagePath);
        
        // Build attributes string
        $attrStr = '';
        $defaultAttrs = [
            'class' => 'img-responsive',
            'loading' => 'lazy'
        ];
        $mergedAttrs = array_merge($defaultAttrs, $attrs);
        
        foreach ($mergedAttrs as $key => $value) {
            if ($value !== null && $value !== '') {
                $attrStr .= ' ' . $key . '="' . e($value) . '"';
            }
        }
        
        $webpUrl = smart_asset($paths['webp']);
        $jpgUrl = smart_asset($paths['jpg']);
        $pngUrl = smart_asset($paths['png']);
        
        // Try WebP first, fallback to jpg, then png
        $onerror = "this.onerror=function(){this.onerror=null;this.src='" . $pngUrl . "';};this.src='" . $jpgUrl . "';";
        
        return '<img src="' . $webpUrl . '" alt="' . e($alt) . '"' . $attrStr . ' onerror="' . $onerror . '">';
    }
}

if (!function_exists('smart_image_url')) {
    /**
     * Get the WebP URL for an image path - converts any image extension to .webp
     * 
     * @param string $imagePath Full image path (e.g., userimages/1/2/image.jpg)
     * @return string WebP URL via CDN
     */
    function smart_image_url($imagePath)
    {
        if (empty($imagePath)) {
            return '';
        }
        
        // Get path info and replace extension with .webp
        $pathInfo = pathinfo($imagePath);
        $basePath = ($pathInfo['dirname'] !== '.' ? $pathInfo['dirname'] . '/' : '') . $pathInfo['filename'];
        $webpPath = $basePath . '.webp';
        
        return smart_asset($webpPath);
    }
}

if (!function_exists('image_fallback_script')) {
    /**
     * Generate onerror fallback script for WebP images
     * 
     * @param string $imagePath Full image path
     * @return string JavaScript onerror handler
     */
    function image_fallback_script($imagePath)
    {
        if (empty($imagePath)) {
            return '';
        }
        
        $paths = smart_image_path($imagePath);
        $jpgUrl = smart_asset($paths['jpg']);
        $pngUrl = smart_asset($paths['png']);
        
        return "this.onerror=function(){this.onerror=null;this.src='" . $pngUrl . "';};this.src='" . $jpgUrl . "';";
    }
}

