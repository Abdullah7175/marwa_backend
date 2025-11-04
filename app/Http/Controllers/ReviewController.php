<?php

// app/Http/Controllers/ReviewController.php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ReviewController extends Controller
{
    public function index()
    {
        $reviews = Review::all()->map(function ($review) {
            return $this->formatReviewResponse($review);
        });
        return response()->json($reviews);
    }
    private function save($image, $directory)
    {
        if ($image && $image->isValid()) {
            $path = $image->store($directory, 'public');
            $url = Storage::url($path);
            // Ensure URL starts with /storage/ for proper preview
            if (strpos($url, '/storage/') !== 0 && strpos($url, 'http') !== 0) {
                $url = '/storage/' . ltrim($url, '/');
            }
            return $url;
        }
        return null;
    }

    /**
     * Format video/image URL to ensure it's previewable
     */
    private function formatMediaUrl($url)
    {
        if (!$url) return null;
        
        // If already a full URL, return as is
        if (strpos($url, 'http://') === 0 || strpos($url, 'https://') === 0) {
            return $url;
        }
        
        // Ensure it starts with /storage/
        if (strpos($url, '/storage/') !== 0) {
            $url = '/storage/' . ltrim($url, '/');
        }
        
        return $url;
    }

    /**
     * Format review response with proper video URLs
     */
    private function formatReviewResponse($review)
    {
        $data = $review->toArray();
        
        // Format video URL
        if (isset($data['video_url'])) {
            $data['video_url'] = $this->formatMediaUrl($data['video_url']);
        }
        
        return $data;
    }
    public function store(Request $request)
    {
        // Force JSON response and prevent redirects
        $request->headers->set('Accept', 'application/json');
        
        // Increase timeout for large file uploads
        set_time_limit(600); // 10 minutes
        
        try {
            $request->validate([
                'user_name' => 'required|string|max:255',
                'detail' => 'required|string',
                // Accept all video formats and allow up to 1GB files
                'video' => 'nullable|file|mimes:mp4,mov,avi,wmv,flv,mkv,webm,m4v,3gp,mpeg,mpg,ogv,ts,m2ts,mts|max:1048576',
            ]);

            $data = $request->only(['user_name', 'detail']);

            if ($request->hasFile('video')) {
                $videoFile = $request->file('video');
                if ($videoFile && $videoFile->isValid()) {
                    $path = $this->save($videoFile, 'videos');
                    if ($path) {
                        $data['video_url'] = $path;
                    }
                }
            }

            $review = Review::create($data);

            return response()->json($this->formatReviewResponse($review), 201)->header('Content-Type', 'application/json');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'errors' => $e->errors()
            ], 422)->header('Content-Type', 'application/json');
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to create review',
                'message' => $e->getMessage()
            ], 500)->header('Content-Type', 'application/json');
        }
    }

    public function show($id)
    {
        $review = Review::findOrFail($id);
        return response()->json($this->formatReviewResponse($review));
    }

    public function update(Request $request, $id)
    {
        // Increase timeout for large file uploads
        set_time_limit(600); // 10 minutes
        
        $request->validate([
            'user_name' => 'sometimes|required|string|max:255',
            'detail' => 'sometimes|required|string',
            // Accept all video formats and allow up to 1GB files
            'video' => 'nullable|file|mimes:mp4,mov,avi,wmv,flv,mkv,webm,m4v,3gp,mpeg,mpg,ogv,ts,m2ts,mts|max:1048576',
            'video_url' => 'nullable|string',
        ]);

        $review = Review::findOrFail($id);
        $data = $request->only(['user_name', 'detail', 'video_url']);

        // Handle video file upload if provided
        if ($request->hasFile('video')) {
            // Delete old video if exists
            if ($review->video_url) {
                $oldPath = str_replace('/storage/', '', $review->video_url);
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }
            
            $videoFile = $request->file('video');
            if ($videoFile && $videoFile->isValid()) {
                $path = $this->save($videoFile, 'videos');
                if ($path) {
                    $data['video_url'] = $path;
                }
            }
        }

        $review->update($data);

        return response()->json($this->formatReviewResponse($review));
    }

    public function destroy($id)
    {
        $review = Review::findOrFail($id);
        
        // Delete the video file from storage if it exists
        if ($review->video_url) {
            // Remove /storage/ prefix to get the actual storage path
            $videoPath = str_replace('/storage/', '', $review->video_url);
            
            // Check if file exists and delete it
            if (Storage::disk('public')->exists($videoPath)) {
                Storage::disk('public')->delete($videoPath);
            }
        }
        
        // Delete the database record
        $review->delete();

        return response()->json(['message' => 'Review and video deleted successfully'], 200);
    }
}
