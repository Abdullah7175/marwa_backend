<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Direct file serving route for images
Route::get('/storage/{path}', function ($path) {
    $possiblePaths = [
        storage_path('app/public/' . $path),
        storage_path($path),
        public_path('storage/' . $path),
    ];
    
    foreach ($possiblePaths as $possiblePath) {
        if (file_exists($possiblePath)) {
            $mimeType = mime_content_type($possiblePath);
            return response()->file($possiblePath, [
                'Content-Type' => $mimeType,
                'Cache-Control' => 'public, max-age=31536000',
            ]);
        }
    }
    
    abort(404);
})->where('path', '.*');