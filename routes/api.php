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

// Blogs CRUD
Route::prefix('blogs')->group(function () {
    Route::post('/create', [BlogController::class, 'store']);
    Route::get('/', [BlogController::class, 'index']);
    Route::get('/{id}', [BlogController::class, 'show']);
    Route::put('/{id}', [BlogController::class, 'update']);
    Route::delete('/{id}', [BlogController::class, 'destroy']);
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
    // Test route to verify API is accessible
    Route::get('/test', function() {
        return response()->json(['status' => 'API route accessible', 'timestamp' => now()], 200);
    });
    
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







// Categories CRUD
Route::prefix('categories')->group(function () {
    Route::post('/create', [CategoryController::class, 'store']);
    Route::get('/', [CategoryController::class, 'index']);
    Route::get('/{id}', [CategoryController::class, 'show']);
    Route::put('/{id}', [CategoryController::class, 'update']);
    Route::delete('/{id}', [CategoryController::class, 'destroy']);
});

// Packages CRUD
Route::prefix('packages')->group(function () {
    Route::post('/create', [PackageController::class, 'store']);
    Route::get('/', [PackageController::class, 'index']);
    Route::get('/{id}', [PackageController::class, 'show']);
    Route::put('/{id}', [PackageController::class, 'update']);
    Route::delete('/{id}', [PackageController::class, 'destroy']);
});

// Hotels CRUD
Route::prefix('hotels')->group(function () {
    Route::post('/create', [HotelController::class, 'store']);
    Route::get('/', [HotelController::class, 'index']);
    Route::get('/{id}', [HotelController::class, 'show']);
    Route::put('/{id}', [HotelController::class, 'update']);
    Route::delete('/{id}', [HotelController::class, 'destroy']);
});


Route::get('/web/packs',[WebController::class,'getPackages']);


Route::get('/web/blogs', [WebController::class, 'getBlogs']);

// Inquiries CRUD
Route::prefix('inquiries')->group(function () {
    Route::post('/create', [WebController::class, 'createIquiry']);
    Route::get('/', [WebController::class, 'getInquiries']);
    Route::get('/{id}', [WebController::class, 'showInquiry']);
    Route::put('/{id}', [WebController::class, 'updateInquiry']);
    Route::delete('/{id}', [WebController::class, 'deleteInquiry']);
    // Secure manual forward of an inquiry to external webhook
    Route::post('/{id}/forward-webhook', [WebController::class, 'forwardInquiryWebhook']);
});

// Public inquiry submission
Route::post('/web/inquiry/submit', [WebController::class, 'createIquiry']);



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

// File serving endpoint for images
Route::get('/files', function (Request $request) {
    $path = $request->query('path');
    
    if (!$path) {
        return response()->json(['error' => 'Path parameter is required'], 400);
    }
    
    // Remove leading slash if present
    $path = ltrim($path, '/');
    
    // Security check - prevent directory traversal attacks
    if (strpos($path, '..') !== false || strpos($path, './') !== false) {
        return response()->json(['error' => 'Access denied - invalid path'], 403);
    }
    
    // Define allowed image directories (for security)
    $allowedImageDirs = [
        'package_images',
        'hotel_images',
        'blogs_images',
        'signature_images',
        'videos',
    ];
    
    // Remove 'storage/' prefix if present for normalization
    $normalizedPath = $path;
    if (str_starts_with($path, 'storage/')) {
        $normalizedPath = substr($path, 8); // Remove 'storage/' prefix
    }
    
    // Check if path starts with an allowed directory or images/
    $isAllowed = false;
    $relativePath = $normalizedPath;
    
    // Check if it's a direct image directory path (e.g., package_images/file.jpg)
    foreach ($allowedImageDirs as $dir) {
        if (str_starts_with($normalizedPath, $dir . '/') || $normalizedPath === $dir) {
            $isAllowed = true;
            break;
        }
    }
    
    // Also allow paths starting with storage/ or images/
    if (!$isAllowed) {
        if (str_starts_with($path, 'storage/') || str_starts_with($path, 'images/')) {
            $isAllowed = true;
            $relativePath = str_starts_with($path, 'images/') ? substr($path, 7) : substr($path, 8);
        }
    }
    
    if (!$isAllowed) {
        return response()->json([
            'error' => 'Access denied - path must be in an allowed image directory',
            'allowed_directories' => array_merge($allowedImageDirs, ['storage/', 'images/'])
        ], 403);
    }
    
    // Try multiple possible locations for the file
    $possiblePaths = [];
    
    // All paths should ultimately be in storage/app/public/...
    $possiblePaths = [
        storage_path('app/public/' . $normalizedPath), // Direct path (package_images/file.jpg)
        storage_path('app/public/' . $relativePath),   // Relative path
        public_path('storage/' . $normalizedPath),     // Symlinked public/storage/...
        public_path('storage/' . $relativePath),       // Symlinked public/storage/...
    ];
    
    // Also try with the original path format if it had storage/ prefix
    if (str_starts_with($path, 'storage/')) {
        $originalRelative = substr($path, 8);
        $possiblePaths = array_merge($possiblePaths, [
            storage_path('app/public/' . $originalRelative),
            public_path('storage/' . $originalRelative),
        ]);
    }
    
    // Handle images/ prefix paths (from Next.js or public directory)
    if (str_starts_with($path, 'images/')) {
        $imagesRelative = substr($path, 7);
        $possiblePaths = array_merge($possiblePaths, [
            public_path('images/' . $imagesRelative),
            public_path($path),
            storage_path('app/public/images/' . $imagesRelative),
            storage_path('app/public/' . $imagesRelative),
        ]);
    }
    
    $fullPath = null;
    foreach ($possiblePaths as $possiblePath) {
        // Additional security: ensure the resolved path is within allowed directories
        $realPath = realpath($possiblePath);
        if ($realPath && file_exists($realPath)) {
            // Verify the file is in an allowed directory
            $allowedDirs = [
                realpath(storage_path('app/public')),
                realpath(public_path()),
                realpath(base_path('public')),
            ];
            
            foreach ($allowedDirs as $allowedDir) {
                if ($allowedDir && str_starts_with($realPath, $allowedDir)) {
                    $fullPath = $realPath;
                    break 2; // Break out of both loops
                }
            }
        }
    }
    
    if (!$fullPath) {
        return response()->json([
            'error' => 'File not found',
            'searched_paths' => $possiblePaths,
            'requested_path' => $path
        ], 404);
    }
    
    $mimeType = mime_content_type($fullPath);
    
    return response()->file($fullPath, [
        'Content-Type' => $mimeType,
        'Cache-Control' => 'public, max-age=31536000', // Cache for 1 year
    ]);
});