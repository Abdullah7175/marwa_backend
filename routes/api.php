<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\WebController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CustomPackageController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\PanelController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SeoController;

Route::prefix('blogs')->group(function () {
    Route::post('/create', [BlogController::class, 'store']);
    Route::post('/{id}', [BlogController::class, 'update']);
    Route::get('/delete/{id}', [BlogController::class, 'destroy']);
});




Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::prefix('custom-packages')->group(function () {
    Route::post('/create', [CustomPackageController::class, 'store']);
    Route::get('/', [CustomPackageController::class, 'index']);
    Route::get('/{id}', [CustomPackageController::class, 'show']);
    Route::put('/{id}', [CustomPackageController::class, 'update']);
    Route::get('/delete/{id}', [CustomPackageController::class, 'destroy']);
});

Route::prefix('reviews')->group(function () {
    Route::post('/create', [ReviewController::class, 'store']);
    Route::get('/', [ReviewController::class, 'index']);
    Route::get('/{id}', [ReviewController::class, 'show']);
    Route::put('/{id}', [ReviewController::class, 'update']);
    Route::get('/delete/{id}', [ReviewController::class, 'destroy']);
});


Route::prefix('panel')->group(function () {
    Route::get('/categories', [PanelController::class, 'getAllCategories']);
    Route::get('/hotels', [PanelController::class, 'getAllHotels']);
    Route::post('/category/update', [PanelController::class, 'updateCategory']);
    Route::post('/hotel/update', [PanelController::class, 'updateHotel']);

});







Route::post('/categories/create',  [CategoryController::class, 'store']);
Route::get('/categories/delete/{id}',  [CategoryController::class, 'destroy']);

Route::post('/packages/create',  [PackageController::class, 'store']);

Route::post('/hotels/create',  [HotelController::class, 'store']);

Route::post('/packages/update',  [PackageController::class, 'update']);
Route::post('/hotels/update',  [HotelController::class, 'update']);

Route::get('/packages/delete/{id}',  [PackageController::class, 'destroy']);


Route::get('/web/packs',[WebController::class,'getPackages']);


Route::get('/web/blogs', [WebController::class, 'getBlogs']);

Route::post('/web/inquiry/submit', [WebController::class, 'createIquiry']);
Route::get('/inquiries', [WebController::class, 'getInquiries']);
Route::get('/inquiry/delete/{id}',[WebController::class,'deleteInquiry']);



Route::delete('/users/{id}',  [UserController::class, 'destroy']);
Route::post('/users', [UserController::class, 'store']);
Route::post('/login', [UserController::class, 'login']);

Route::prefix('seo')->group(function () {
    Route::get('/page', [SeoController::class, 'getPageSeo']);
    Route::get('/blog', [SeoController::class, 'getBlogSeo']);
    Route::get('/package', [SeoController::class, 'getPackageSeo']);
    Route::post('/page/update', [SeoController::class, 'updatePageSeo']);
    Route::post('/blog/update', [SeoController::class, 'updateBlogSeo']);
    Route::post('/package/update', [SeoController::class, 'updatePackageSeo']);
    Route::get('/all', [SeoController::class, 'getAllSeoSettings']);
    Route::delete('/page/delete', [SeoController::class, 'deletePageSeo']);
});