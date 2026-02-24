<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BlogController;

/*
|--------------------------------------------------------------------------
| Blog Subdomain Routes
|--------------------------------------------------------------------------
|
| These routes are for the blog subdomain (blog.massagerepublic.com.co)
| They handle all public-facing blog functionality.
|
*/

Route::get('/', [BlogController::class, 'index'])->name('blog.index');
Route::get('/search', [BlogController::class, 'search'])->name('blog.search');
Route::get('/archive/{year}/{month?}', [BlogController::class, 'archive'])->name('blog.archive')->where(['year' => '[0-9]{4}', 'month' => '[0-9]{1,2}']);
Route::get('/category/{slug}', [BlogController::class, 'category'])->name('blog.category');
Route::get('/tag/{slug}', [BlogController::class, 'tag'])->name('blog.tag');
Route::get('/{slug}', [BlogController::class, 'show'])->name('blog.show');
 