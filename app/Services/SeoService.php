<?php

namespace App\Services;

use App\Models\SeoKeyword;
use App\Models\Gender;
use App\Models\City;
use App\Models\Country;
use App\Models\Setting;
use App\Models\UrlAlias;
use App\Models\DefaultSeoSetting;

class SeoService
{
    /**
     * Extract SEO data from URL pattern like /female-escorts-in-dubai
     */
    public function getSeoFromUrl($url)
    {
        // Remove domain and protocol if present
        $path = parse_url($url, PHP_URL_PATH);
        $path = trim($path, '/');
        
        // Check if this URL is an alias and get the base pattern
        $actualPath = $this->resolveUrlAlias($path);
        
        // Default SEO data
        $seoData = [
            'title' => config('app.name'),
            'keywords' => '',
            'description' => '',
            'content' => '',
            'gender' => null,
            'city' => null,
            'country' => null,
            'is_alias' => $actualPath !== $path,
            'original_url' => $path,
            'resolved_url' => $actualPath
        ];
        
        // If empty path (homepage), return default
        if (empty($actualPath)) {
            return $this->getDefaultSeoData('homepage');
        }
        
        // Try to parse the URL pattern: gender-escorts-in-city
        if (preg_match('/^([a-zA-Z]+)-escorts-in-([a-zA-Z-]+)$/', $actualPath, $matches)) {
            $genderSlug = $matches[1]; // e.g., 'female'
            $citySlug = $matches[2];   // e.g., 'dubai'

            $seoData = array_merge($seoData, $this->getSeoDataBySlug($genderSlug, $citySlug));
            $seoData['is_alias'] = $actualPath !== $path;
            $seoData['original_url'] = $path;
            $seoData['resolved_url'] = $actualPath;

            return $seoData;
        }

        // News page pattern: {gender}-escort-news-in-{city}[/{type}]
        // Routes: news.all + news.page (see routes/web.php)
        if (preg_match('/^(female|male|shemale)-escort-news-in-([a-z0-9\-]+)(?:\/(new-escorts|new-reviews|new-questions))?$/', $actualPath, $matches)) {
            $newsData = $this->getNewsPageSeoData($matches[1], $matches[2], $matches[3] ?? null);
            $seoData = array_merge($seoData, $newsData);
            $seoData['is_alias'] = $actualPath !== $path;
            $seoData['original_url'] = $path;
            $seoData['resolved_url'] = $actualPath;
            return $seoData;
        }

        // Try alternative patterns or specific pages
        return array_merge($seoData, $this->getSeoDataByPage($actualPath));
    }

    /**
     * Resolve SEO for the News (What's New) pages. Admin sets a single
     * "news-page" context in default_seo_settings; placeholders {gender},
     * {city}, {country}, {type}, {site_name} are substituted at runtime.
     * Falls back to a generated title when the context row is absent.
     */
    private function getNewsPageSeoData($genderSlug, $citySlug, $typeSlug = null)
    {
        $gender = Gender::where('slug', $genderSlug)
                       ->orWhere('name', $genderSlug)
                       ->first();

        $city = City::where('slug', $citySlug)->first();

        $genderName = $gender ? ucfirst($gender->name) : ucfirst($genderSlug);
        $cityName = $city ? $city->name : ucwords(str_replace('-', ' ', $citySlug));
        $countryName = ($city && !empty($city->country)) ? $city->country : '';

        $typeLabelMap = [
            'new-escorts'   => 'New Escorts',
            'new-reviews'   => 'New Reviews',
            'new-questions' => 'New Questions',
        ];
        $typeLabel = $typeSlug ? ($typeLabelMap[$typeSlug] ?? '') : '';

        $defaultSeo = DefaultSeoSetting::where('name', 'news-page')
                                       ->where('is_active', true)
                                       ->first();

        $siteName = config('app.name');

        if ($defaultSeo) {
            $placeholders = ['{gender}', '{city}', '{country}', '{type}', '{site_name}', '{sitename}'];
            $values = [$genderName, $cityName, $countryName, $typeLabel, $siteName, $siteName];

            $title = str_replace($placeholders, $values, $defaultSeo->title);
            $description = str_replace($placeholders, $values, $defaultSeo->description ?? '');
            $keywords = str_replace($placeholders, $values, $defaultSeo->keywords ?? '');
            $content = str_replace($placeholders, $values, $defaultSeo->content ?? '');

            $title = preg_replace('/\s+/', ' ', trim($title));
            $description = preg_replace('/\s+/', ' ', trim($description));

            return [
                'title' => $title,
                'keywords' => $keywords,
                'description' => $description,
                'content' => $content,
                'gender' => $gender,
                'city' => $city,
                'country' => $city->country ?? null,
                'seo_record' => null,
                'default_seo_setting' => $defaultSeo,
            ];
        }

        // Fallback title mirrors NewsPage::render() so head <title> stays sensible
        // even before an admin fills in the news-page context row.
        $title = $typeLabel
            ? "{$cityName} Escort News: {$typeLabel} | {$siteName}"
            : "{$cityName} Escort News | {$siteName}";

        return [
            'title' => $title,
            'keywords' => "{$cityName} escort news, {$genderName} escorts, new reviews, new questions",
            'description' => "Latest {$genderName} escort news in {$cityName}: newly listed escorts, fresh reviews, and answered questions.",
            'content' => '',
            'gender' => $gender,
            'city' => $city,
            'country' => $city->country ?? null,
            'seo_record' => null,
            'default_seo_setting' => null,
        ];
    }
    
    /**
     * Resolve URL alias to base pattern
     */
    private function resolveUrlAlias($path)
    {
        if (empty($path)) {
            return $path;
        }
        
        $alias = UrlAlias::findByCustomUrl($path);
        
        if ($alias) {
            return $alias->base_pattern;
        }
        
        return $path;
    }
    
    /**
     * Get SEO data by gender and city slug
     */
    private function getSeoDataBySlug($genderSlug, $citySlug)
    {
        // Find gender by slug/name — exact match only, since LIKE '%male%' also matches 'female'.
        $gender = Gender::where('slug', $genderSlug)
                       ->orWhere('name', $genderSlug)
                       ->first();
        
        // Find city by slug/name (normalize city slug)
        $cityName = str_replace('-', ' ', $citySlug);
        $city = City::where('name', 'like', "%{$cityName}%")
                   ->orWhere('slug', $citySlug)
                   ->first();
        
        // If we found both, try to get specific SEO data
        if ($gender && $city) {
            // Try multiple approaches to find the SEO record
            $seoKeyword = null;
            
            // First, try exact match with city's country if it exists
            if (isset($city->country_id) && $city->country_id) {
                $seoKeyword = SeoKeyword::where('gender_id', $gender->id)
                                      ->where('city_id', $city->id)
                                      ->where('country_id', $city->country_id)
                                      ->first();
            }
            
            // If not found, try with any country for this gender and city
            if (!$seoKeyword) {
                $seoKeyword = SeoKeyword::where('gender_id', $gender->id)
                                      ->where('city_id', $city->id)
                                      ->first(); // Remove country restriction
            }
            
            // Fallback: try without country
            if (!$seoKeyword) {
                $seoKeyword = SeoKeyword::where('gender_id', $gender->id)
                                      ->where('city_id', $city->id)
                                      ->whereNull('country_id')
                                      ->first();
            }
            
            // Fallback: try gender only
            if (!$seoKeyword) {
                $seoKeyword = SeoKeyword::where('gender_id', $gender->id)
                                      ->whereNull('city_id')
                                      ->whereNull('country_id')
                                      ->first();
            }
            
            // If found, return the SEO data
            if ($seoKeyword) {
                return [
                    'title' => $seoKeyword->title,
                    'keywords' => $seoKeyword->keywords,
                    'description' => $seoKeyword->description,
                    'content' => $seoKeyword->content,
                    'gender' => $gender,
                    'city' => $city,
                    'country' => $city->country ?? null,
                    'seo_record' => $seoKeyword
                ];
            }
            
            // If no specific SEO found, generate default for this combination
            return $this->generateDefaultSeoData($gender, $city);
        }
        
        // If no specific SEO found, try to get default SEO with appropriate context
        if ($gender || $city) {
            return $this->generateDefaultSeoData($gender, $city);
        }
        
        return $this->getDefaultSeoData();
    }
    
    /**
     * Get SEO data by specific page
     */
    private function getSeoDataByPage($page)
    {
        // First, try to find a specific SEO keyword record for this page
        $seoKeyword = SeoKeyword::where('page', $page)
                               ->whereNull('gender_id')
                               ->whereNull('city_id')
                               ->whereNull('country_id')
                               ->first();
        
        if ($seoKeyword) {
            return [
                'title' => $seoKeyword->title,
                'keywords' => $seoKeyword->keywords,
                'description' => $seoKeyword->description,
                'content' => $seoKeyword->content,
                'gender' => null,
                'city' => null,
                'country' => null,
                'seo_record' => $seoKeyword
            ];
        }
        
        // If no specific SEO keyword found, try to get page-specific default SEO
        $context = $this->normalizePageContext($page);
        
        // Try to find a default SEO setting that matches the page context
        $defaultSeo = DefaultSeoSetting::where('name', $context)
                                     ->where('is_active', true)
                                     ->first();
        
        if ($defaultSeo) {
            return [
                'title' => $defaultSeo->title,
                'keywords' => $defaultSeo->keywords ?? '',
                'description' => $defaultSeo->description ?? '',
                'content' => $defaultSeo->content ?? '',
                'gender' => null,
                'city' => null,
                'country' => null,
                'seo_record' => null,
                'default_seo_setting' => $defaultSeo
            ];
        }
        
        // Fallback to global default SEO
        return $this->getDefaultSeoData('global');
    }
    
    /**
     * Normalize page path to context name
     */
    private function normalizePageContext($page)
    {
        // Remove leading/trailing slashes and convert to lowercase
        $page = trim($page, '/');
        $page = strtolower($page);
        
        // Map common page patterns to context names
        $pageMapping = [
            'login' => 'login',
            'register' => 'register',
            'signup' => 'register',
            'contact' => 'contact',
            'contact-us' => 'contact',
            'about' => 'about',
            'about-us' => 'about',
            'dashboard' => 'profile-dashboard',
            'profile' => 'profile-dashboard',
            'my-account' => 'profile-dashboard',
            'my-dashboard' => 'profile-dashboard',
            'user/dashboard' => 'profile-dashboard',
            'admin/dashboard' => 'admin-dashboard',
            'forgot-password' => 'forgot-password',
            'reset-password' => 'reset-password',
            'terms' => 'terms',
            'terms-of-service' => 'terms',
            'privacy' => 'privacy',
            'privacy-policy' => 'privacy',
            'faq' => 'faq',
            'help' => 'help',
            'support' => 'contact',
        ];
        
        // Return mapped context or the page itself
        return $pageMapping[$page] ?? $page;
    }
    
    /**
     * Generate default SEO data for gender/city combination with smart fallback
     */
    private function generateDefaultSeoData($gender = null, $city = null, $country = null)
    {
        // First, try to find a context-specific default SEO setting
        $context = $this->determineContext($gender, $city, $country);
        $defaultSeo = $this->getContextSpecificDefaultSeo($context);
        
        if ($defaultSeo) {
            // Use the default SEO setting but customize with gender/city info if available
            $title = $defaultSeo['title'];
            $description = $defaultSeo['description'];
            $keywords = $defaultSeo['keywords'];
            $content = $defaultSeo['content'];
            
            // Prepare placeholder values
            $siteName = config('app.name');
            $genderName = $gender ? ucfirst($gender->name) : '';
            $cityName = $city ? $city->name : '';
            
            // Safely get country name
            $countryName = '';
            if ($country && is_object($country) && isset($country->nicename)) {
                $countryName = $country->nicename;
            } elseif ($city && is_object($city) && !empty($city->country)) {
                // The city table has a 'country' string column with the country name
                $countryName = $city->country;
            }
            
            // Replace all placeholders
            $placeholders = ['{gender}', '{city}', '{country}', '{site_name}', '{sitename}'];
            $values = [$genderName, $cityName, $countryName, $siteName, $siteName];
            
            $title = str_replace($placeholders, $values, $title);
            $description = str_replace($placeholders, $values, $description);
            $keywords = str_replace($placeholders, $values, $keywords);
            $content = str_replace($placeholders, $values, $content);
            
            // Clean up any remaining empty placeholders or double spaces
            $title = preg_replace('/\s+/', ' ', trim($title));
            $description = preg_replace('/\s+/', ' ', trim($description));
            
            return [
                'title' => $title,
                'keywords' => $keywords,
                'description' => $description,
                'content' => $content,
                'gender' => $gender,
                'city' => $city,
                'country' => $country,
                'seo_record' => null,
                'default_seo_setting' => $defaultSeo['default_seo_setting']
            ];
        }
        
        // Fallback to generated content if no default SEO settings
        $siteName = config('app.name');
        $genderName = $gender ? ucfirst($gender->name) : '';
        $cityName = $city ? $city->name : '';
        
        // Safely get country name for fallback
        $countryName = '';
        if ($country && is_object($country) && isset($country->nicename)) {
            $countryName = $country->nicename;
        } elseif ($city && is_object($city) && !empty($city->country)) {
            // The city table has a 'country' string column with the country name
            $countryName = $city->country;
        }
        
        $title = $siteName;
        $description = "Find the best services";
        $keywords = "";
        
        if ($gender && $city) {
            $title = "{$genderName} Escorts in {$cityName}";
            $description = "Premium {$genderName} escort services in {$cityName}";
            $keywords = "{$genderName} escorts, {$cityName} escorts, escort services";
            
            if ($countryName) {
                $title .= " - {$countryName}";
                $description .= ", {$countryName}";
                $keywords .= ", {$countryName}";
            }
            
            $title .= " | {$siteName}";
            $description .= ". Professional and discreet escort services.";
        }
        
        return [
            'title' => $title,
            'keywords' => $keywords,
            'description' => $description,
            'content' => '',
            'gender' => $gender,
            'city' => $city,
            'country' => $country,
            'seo_record' => null,
            'default_seo_setting' => null
        ];
    }
    
    /**
     * Determine context for default SEO selection
     * Returns array of contexts to try in order of priority
     */
    private function determineContext($gender = null, $city = null, $country = null)
    {
        if ($gender && $city) {
            // For gender+city URLs like female-escorts-in-dubai, try city-pages first
            return ['city-pages', 'escorts', 'global'];
        } elseif ($city) {
            return ['city-pages', 'global'];
        } elseif ($gender) {
            return ['escorts', 'global'];
        }
        
        return ['global'];
    }
    
    /**
     * Get context-specific default SEO setting
     * Accepts single context or array of contexts to try in order
     */
    private function getContextSpecificDefaultSeo($contexts)
    {
        // Convert to array if single context passed
        if (!is_array($contexts)) {
            $contexts = [$contexts];
        }
        
        // Try each context in order until we find an active one
        foreach ($contexts as $context) {
            $defaultSeo = DefaultSeoSetting::where('name', $context)
                                         ->where('is_active', true)
                                         ->first();
            
            if ($defaultSeo) {
                return [
                    'title' => $defaultSeo->title,
                    'keywords' => $defaultSeo->keywords ?? '',
                    'description' => $defaultSeo->description ?? '',
                    'content' => $defaultSeo->content ?? '',
                    'default_seo_setting' => $defaultSeo
                ];
            }
        }
        
        return null;
    }
    
    /**
     * Get default SEO data with fallback hierarchy
     */
    private function getDefaultSeoData($context = 'global')
    {
        // Try to get context-specific default SEO first
        $defaultSeo = DefaultSeoSetting::where('name', $context)
                                     ->where('is_active', true)
                                     ->first();
        
        // If no context-specific setting found, try to get any active default SEO by priority
        if (!$defaultSeo) {
            $defaultSeo = DefaultSeoSetting::active()
                                         ->byPriority()
                                         ->first();
        }
        
        // If we found a default SEO setting, use it
        if ($defaultSeo) {
            return [
                'title' => $defaultSeo->title,
                'keywords' => $defaultSeo->keywords ?? '',
                'description' => $defaultSeo->description ?? '',
                'content' => $defaultSeo->content ?? '',
                'gender' => null,
                'city' => null,
                'country' => null,
                'seo_record' => null,
                'default_seo_setting' => $defaultSeo
            ];
        }
        
        // Fallback to site settings if no default SEO settings exist
        $setting = Setting::first();
        
        return [
            'title' => $setting->site_name ?? config('app.name'),
            'keywords' => $setting->seo_keywords ?? '',
            'description' => $setting->seo_description ?? '',
            'content' => '',
            'gender' => null,
            'city' => null,
            'country' => null,
            'seo_record' => null,
            'default_seo_setting' => null
        ];
    }
    
    /**
     * Get current page SEO data based on request
     */
    public function getCurrentPageSeo()
    {
        // Request-scoped memoization: the SeoComposer runs for every view compose,
        // which on a listing page is ~10 times per request. The URL doesn't change
        // mid-request, so cache once and reuse.
        static $cache = [];
        $currentUrl = request()->getPathInfo();
        if (!array_key_exists($currentUrl, $cache)) {
            $cache[$currentUrl] = $this->getSeoFromUrl($currentUrl);
        }
        return $cache[$currentUrl];
    }
}
