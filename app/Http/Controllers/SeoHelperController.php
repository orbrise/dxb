<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Admin\SeoKeywordController;
use App\Models\Service;
use App\Models\City;
use App\Models\Country;

class SeoHelperController extends Controller
{
    /**
     * Get SEO content for a specific page based on parameters
     * 
     * @param string|null $page Legacy page identifier
     * @param int|null $serviceId Service/Category ID
     * @param int|null $cityId City ID
     * @param int|null $countryId Country ID
     * @return array SEO content array
     */
    public static function getSeoContent($page = null, $serviceId = null, $cityId = null, $countryId = null)
    {
        $seoController = new SeoKeywordController();
        return $seoController->getSeoTagsForPage($page, $serviceId, $cityId, $countryId);
    }

    /**
     * Get SEO content by service name, city name, and country name
     * 
     * @param string|null $serviceName
     * @param string|null $cityName
     * @param string|null $countryName
     * @return array SEO content array
     */
    public static function getSeoContentByNames($serviceName = null, $cityName = null, $countryName = null)
    {
        $serviceId = null;
        $cityId = null;
        $countryId = null;

        if ($serviceName) {
            $service = Service::where('name', 'like', "%{$serviceName}%")->first();
            $serviceId = $service ? $service->id : null;
        }

        if ($cityName) {
            $city = City::where('name', 'like', "%{$cityName}%")->first();
            $cityId = $city ? $city->id : null;
        }

        if ($countryName) {
            $country = Country::where('nicename', 'like', "%{$countryName}%")->first();
            $countryId = $country ? $country->id : null;
        }

        return self::getSeoContent(null, $serviceId, $cityId, $countryId);
    }

    /**
     * Render SEO meta tags HTML
     * 
     * @param array $seoData
     * @return string HTML meta tags
     */
    public static function renderSeoTags($seoData)
    {
        $html = '';
        
        if (!empty($seoData['title'])) {
            $html .= '<title>' . htmlspecialchars($seoData['title']) . '</title>' . "\n";
            $html .= '<meta property="og:title" content="' . htmlspecialchars($seoData['title']) . '">' . "\n";
        }
        
        if (!empty($seoData['description'])) {
            $html .= '<meta name="description" content="' . htmlspecialchars($seoData['description']) . '">' . "\n";
            $html .= '<meta property="og:description" content="' . htmlspecialchars($seoData['description']) . '">' . "\n";
        }
        
        if (!empty($seoData['keywords'])) {
            $html .= '<meta name="keywords" content="' . htmlspecialchars($seoData['keywords']) . '">' . "\n";
        }
        
        return $html;
    }
}
