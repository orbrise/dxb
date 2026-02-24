<?php
namespace App\Livewire;

use Livewire\Component;
use App\Models\Page;

class PageList extends Component
{
    public function render()
    {
        $pages = Page::where('is_published', true)
                    ->orderBy('order_index', 'asc')
                    ->get();
        return view('livewire.page-list', [
            'pages' => $pages,
        ]);
    }
}