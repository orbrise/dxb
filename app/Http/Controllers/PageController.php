<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Page;


class PageController extends Controller
{
    public function show($slug)
    {
        // Fetch the page by its slug
        $page = Page::where('slug', $slug)->firstOrFail();

        // Pass the page data to the view
        return view('livewire.page', compact('page'));
    }

   

}
