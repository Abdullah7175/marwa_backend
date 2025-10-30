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
        $reviews = Review::all();
        return response()->json($reviews);
    }
    private function save($image, $directory)
    {
        if ($image->isValid()) {
            $path = $image->store($directory, 'public');
            $url = Storage::url($path);
            return $url;
        }
        return null;
    }
    public function store(Request $request)
    {
        // Force JSON response and prevent redirects
        $request->headers->set('Accept', 'application/json');
        
        try {
            $request->validate([
                'user_name' => 'required|string|max:255',
                'detail' => 'required|string',
                'video' => 'nullable|file|mimes:mp4,mov,avi|max:10240', // Made optional since video might not always be uploaded
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

            return response()->json($review, 201)->header('Content-Type', 'application/json');
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
        return response()->json($review);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'user_name' => 'sometimes|required|string|max:255',
            'detail' => 'sometimes|required|string',
            'video_url' => 'nullable|string',
        ]);

        $review = Review::findOrFail($id);
        $review->update($request->all());

        return response()->json($review);
    }

    public function destroy($id)
    {
        $review = Review::findOrFail($id);
        $review->delete();

        return response()->json(null, 204);
    }
}
