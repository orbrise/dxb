<?php

namespace App\View\Composers;

use Illuminate\View\View;
use App\Services\SeoService;

class SeoComposer
{
    protected $seoService;

    public function __construct(SeoService $seoService)
    {
        $this->seoService = $seoService;
    }

    /**
     * Bind data to the view.
     */
    public function compose(View $view)
    {
        // Skip admin views to avoid variable conflicts
        $viewName = $view->getName();
        if (str_starts_with($viewName, 'admin.')) {
            return;
        }
        
        $seoData = $this->seoService->getCurrentPageSeo();
        
        $view->with([
            'currentSeoData' => $seoData,
            'seoTitle' => $seoData['title'],
            'seoDescription' => $seoData['description'],
            'seoKeywords' => $seoData['keywords'],
            'seoContent' => $seoData['content'],
        ]);
    }
}
