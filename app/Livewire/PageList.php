<?php
namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Cache;
use App\Models\Page;

class PageList extends Component
{
    public function render()
    {
        // Footer renders on every page site-wide. Published pages change
        // rarely — cache for 10 minutes to kill the per-request SELECT.
        $pages = Cache::remember('footer.published_pages', 600, function () {
            return Page::where('is_published', true)
                ->orderBy('order_index', 'asc')
                ->get();
        });

        return view('livewire.page-list', [
            'pages' => $pages,
        ]);
    }
}