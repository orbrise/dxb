<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UsersProfile;
use App\Models\City;
use App\Models\Gender;
use App\Models\Page;
use Carbon\Carbon;

class SitemapController extends Controller
{
    /**
     * Sitemap Index - Main sitemap that lists all other sitemaps
     */
    public function index()
    {
        $sitemaps = [
            [
                'loc' => url('sitemaps/pages.xml'),
                'lastmod' => $this->formatDate(Page::max('updated_at'))
            ],
            [
                'loc' => url('sitemaps/cities.xml'),
                'lastmod' => $this->formatDate(City::max('updated_at'))
            ],
            [
                'loc' => url('sitemaps/profiles.xml'),
                'lastmod' => $this->formatDate(UsersProfile::max('updated_at'))
            ],
            [
                'loc' => url('sitemaps/categories.xml'),
                'lastmod' => $this->formatDate(Gender::max('updated_at'))
            ]
        ];

        return response()->view('sitemaps.index', compact('sitemaps'))
                        ->header('Content-Type', 'text/xml');
    }

    /**
     * Format date to W3C format for sitemaps
     * Returns format: YYYY-MM-DDTHH:MM:SS+00:00
     * Ensures valid dates (year >= 1970) for sitemap compatibility
     */
    private function formatDate($date)
    {
        if (!$date) {
            return Carbon::now()->toW3cString();
        }
        
        if (is_string($date)) {
            $date = Carbon::parse($date);
        }
        
        // Ensure date is valid (year >= 1970) for sitemap compatibility
        // Some records may have invalid dates like -0001-11-30 from zero dates
        if ($date->year < 1970) {
            return Carbon::now()->toW3cString();
        }
        
        return $date->toW3cString();
    }

    /**
     * Pages Sitemap - Static pages (about, contact, etc.)
     */
    public function pages()
    {
        $pages = Page::where('is_published', 1)->get();
        
        $urls = [];
        
        // Add static pages
        foreach ($pages as $page) {
            $urls[] = [
                'loc' => url('page/' . $page->slug),
                'lastmod' => $this->formatDate($page->updated_at),
                'changefreq' => 'monthly',
                'priority' => '0.5'
            ];
        }
        
        // Add homepage
        $urls[] = [
            'loc' => url('/'),
            'lastmod' => $this->formatDate(Carbon::now()),
            'changefreq' => 'daily',
            'priority' => '1.0'
        ];
        
        // Add other important static pages
        $staticPages = [
            ['url' => '/login', 'priority' => '0.3', 'changefreq' => 'monthly'],
            ['url' => '/register', 'priority' => '0.3', 'changefreq' => 'monthly'],
            ['url' => '/contact', 'priority' => '0.4', 'changefreq' => 'monthly'],
            ['url' => '/about', 'priority' => '0.4', 'changefreq' => 'monthly'],
        ];
        
        foreach ($staticPages as $page) {
            $urls[] = [
                'loc' => url($page['url']),
                'lastmod' => $this->formatDate(Carbon::now()),
                'changefreq' => $page['changefreq'],
                'priority' => $page['priority']
            ];
        }

        return response()->view('sitemaps.urlset', compact('urls'))
                        ->header('Content-Type', 'text/xml');
    }

    /**
     * Cities Sitemap - Gender + City combinations (e.g., female-escorts-in-dubai)
     */
    public function cities()
    {
        // Only include cities that are marked for sitemap
        $cities = City::where('include_in_sitemap', 1)->get();
        $genders = Gender::all();
        
        $urls = [];
        
        // Generate URLs for each gender + city combination
        // URL format: {gender}-escorts-in-{city}
        foreach ($genders as $gender) {
            foreach ($cities as $city) {
                $genderSlug = strtolower($gender->name);
                
                // Clean and sanitize city slug - remove special characters
                $citySlug = $city->slug ?? strtolower(str_replace(' ', '-', $city->name));
                $citySlug = $this->sanitizeSlug($citySlug);
                
                // Skip if slug is invalid
                if (empty($citySlug)) {
                    continue;
                }
                
                $urls[] = [
                    'loc' => url("/{$genderSlug}-escorts-in-{$citySlug}"),
                    'lastmod' => $this->formatDate($city->updated_at),
                    'changefreq' => 'daily',
                    'priority' => '0.9'
                ];
            }
        }

        return response()->view('sitemaps.urlset', compact('urls'))
                        ->header('Content-Type', 'text/xml');
    }
    
    /**
     * Sanitize slug to remove special characters and ensure URL compatibility
     */
    private function sanitizeSlug($slug)
    {
        // Remove special characters and normalize
        $slug = strtolower($slug);
        $slug = preg_replace('/[^a-z0-9\-]/', '', $slug);
        $slug = preg_replace('/-+/', '-', $slug); // Replace multiple dashes with single dash
        $slug = trim($slug, '-'); // Remove leading/trailing dashes
        
        return $slug;
    }

    /**
     * Profiles Sitemap - Individual escort profiles
     */
    public function profiles()
    {
        // Get active, non-archived profiles
        $profiles = UsersProfile::where('is_active', 1)
                          ->whereNull('archived_at')
                          ->with(['ggender', 'gcity'])
                          ->orderBy('updated_at', 'desc')
                          ->get();
        
        $urls = [];
        
        foreach ($profiles as $profile) {
            // Skip if missing required relationships
            if (!$profile->ggender || !$profile->gcity) {
                continue;
            }
            
            // Generate profile URL: {gender}-escorts-in-{city}/{id}/{slug}
            $genderSlug = strtolower($profile->ggender->name ?? 'female');
            $citySlug = $profile->gcity->slug ?? strtolower(str_replace(' ', '-', $profile->gcity->name ?? 'dubai'));
            $username = $profile->slug ?? $profile->name ?? 'profile';
            
            $urls[] = [
                'loc' => url("/{$genderSlug}-escorts-in-{$citySlug}/{$profile->id}/{$username}"),
                'lastmod' => $this->formatDate($profile->updated_at),
                'changefreq' => 'weekly',
                'priority' => '0.8'
            ];
        }

        return response()->view('sitemaps.urlset', compact('urls'))
                        ->header('Content-Type', 'text/xml');
    }

    /**
     * Categories Sitemap - Gender/service categories
     */
    public function categories()
    {
        $genders = Gender::all();
        
        $urls = [];
        
        // Add gender category pages
        foreach ($genders as $gender) {
            $genderSlug = strtolower($gender->name);
            
            $urls[] = [
                'loc' => url("/{$genderSlug}-escorts"),
                'lastmod' => $this->formatDate($gender->updated_at),
                'changefreq' => 'weekly',
                'priority' => '0.7'
            ];
        }

        return response()->view('sitemaps.urlset', compact('urls'))
                        ->header('Content-Type', 'text/xml');
    }
}
