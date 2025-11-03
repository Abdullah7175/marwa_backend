<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\Blog;
use App\Models\BlogElement;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;

class BlogController extends Controller
{

    private function saveImage($image, $directory)
    {
        try {
            if ($image && $image->isValid()) {
            $path = $image->store($directory, 'public');
                if ($path) {
            $url = Storage::url($path);
                    // Ensure URL starts with /storage/
                    if (strpos($url, '/storage/') !== 0 && strpos($url, 'http') !== 0) {
                        $url = '/storage/' . ltrim($url, '/');
                    }
            return $url;
                }
            }
        } catch (\Exception $e) {
            \Log::error('Image save error: ' . $e->getMessage());
            return null;
        }
        return null;
    }
    
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'body' => 'nullable|string',
                'image' => 'required|file',
                'elements' => 'required|array',
            ]);

            $imagePath = $this->saveImage($request->file('image'), 'blogs_images');

            $blog = Blog::create([
                'title' => $validatedData['title'],
                'body' => $validatedData['body'] ?? '',
                'image' => $imagePath,
            ]);
            $blog_id = $blog['id'];

            $elements = $validatedData['elements'];
            $normalizedElements = [];

            // Normalize elements from JSON strings or arrays
            foreach ($elements as $index => $item) {
                if (is_string($item)) {
                    $decoded = json_decode($item, true);
                    if ($decoded === null || !is_array($decoded)) {
                        throw ValidationException::withMessages([
                            "elements.$index" => ['Invalid JSON element.'],
                        ]);
                    }
                    $normalizedElements[] = $decoded;
                } elseif (is_array($item)) {
                    $normalizedElements[] = $item;
                } else {
                    throw ValidationException::withMessages([
                        "elements.$index" => ['Element must be JSON string or object.'],
                    ]);
                }
            }

            // Create elements with sections and ordering support
            foreach ($normalizedElements as $index => $e) {
                if (!isset($e['element_type'])) {
                    throw ValidationException::withMessages([
                        "elements.$index" => ['Each element must include element_type.'],
                    ]);
                }

                $elementData = [
                    'element_type' => $e['element_type'],
                    'blog_id' => $blog_id,
                    'section_title' => $e['section_title'] ?? null,
                    'order' => isset($e['order']) ? (int)$e['order'] : $index,
                ];

                // Handle image elements
                if ($e['element_type'] === 'image') {
                    $fieldName = $e['value'] ?? null;
                    if ($fieldName && $request->hasFile($fieldName)) {
                        $elemImagePath = $this->saveImage($request->file($fieldName), 'blogs_images');
                        if ($elemImagePath) {
                            $elementData['value'] = $elemImagePath;
                        } else {
                            throw ValidationException::withMessages([
                                "elements.$index" => ["Failed to upload image for field '$fieldName'."],
                            ]);
                        }
                    } elseif (isset($e['value']) && (filter_var($e['value'], FILTER_VALIDATE_URL) || strpos($e['value'], '/storage/') === 0)) {
                        // If it's already a URL (existing image), use it
                        $elementData['value'] = $e['value'];
                    } else {
                        throw ValidationException::withMessages([
                            "elements.$index" => ["Image field '$fieldName' not found in request or invalid value."],
                        ]);
                    }
                } else {
                    // For non-image elements, use the value directly
                    $elementData['value'] = $e['value'] ?? '';
                }

                BlogElement::create($elementData);
            }

            // Load blog with elements for response
            $blog->load('elements');
            $data = $blog->toArray();
            $data['elements'] = $blog->elements;
            $data['elements_by_sections'] = $blog->getElementsBySections();

            return response()->json($data, 201);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create blog', 'message' => $e->getMessage()], 500);
        }
    }
    
    public function index()
    {
        $blogs = Blog::with('elements')->get();
        $res = [];
        foreach($blogs as $b){
           $data = $b->toArray();
           $data['elements'] = $b->elements;
           $data['elements_by_sections'] = $b->getElementsBySections();
           $res[] = $data;
        }
        return response()->json($res, 200);
    }

    public function show($id)
    {
        $blog = Blog::with('elements')->find($id);
        
        if (!$blog) {
            return response()->json(['error' => 'Blog not found'], 404);
        }
        
        $data = $blog->toArray();
        $data['elements'] = $blog->elements;
        $data['elements_by_sections'] = $blog->getElementsBySections();
        
        return response()->json($data, 200);
    }

    public function update(Request $request, $id)
    {
        try {
            // Validation - make body explicitly nullable and allow empty strings
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'body' => 'nullable|string',
                'image' => 'nullable|file',
                'elements' => 'required|array'
            ]);

            $blog = Blog::find($id);
            if(!$blog){
                return response()->json(['error' => 'Blog not found'], 404);
            }

            // Handle main image update
            $imagePath = $blog->image;
            if($request->hasFile('image')){
                $newImagePath = $this->saveImage($request->file('image'), 'blogs_images');
                if ($newImagePath) {
                    $imagePath = $newImagePath;
                }
            }
            
            // Update blog post - explicitly handle body field
            $updateData = [
                'title' => $validatedData['title'],
                'image' => $imagePath
            ];
            
            // Handle body field - check if it exists in request (even if empty)
            if (isset($validatedData['body'])) {
                $updateData['body'] = $validatedData['body'];
            } elseif ($request->has('body')) {
                // Explicitly check if body was sent in request
                $updateData['body'] = $request->input('body', '');
            }
            
            $blog->update($updateData);
            
            // Delete existing elements
            BlogElement::where('blog_id', $id)->delete();

            // Normalize and add new elements
            $elements = $validatedData['elements'];
            $normalizedElements = [];

            // Normalize elements from JSON strings or arrays
            foreach ($elements as $index => $item) {
                if (is_string($item)) {
                    $decoded = json_decode($item, true);
                    if ($decoded === null || !is_array($decoded)) {
                        throw ValidationException::withMessages([
                            "elements.$index" => ['Invalid JSON element.'],
                        ]);
                    }
                    $normalizedElements[] = $decoded;
                } elseif (is_array($item)) {
                    $normalizedElements[] = $item;
                } else {
                    throw ValidationException::withMessages([
                        "elements.$index" => ['Element must be JSON string or object.'],
                    ]);
                }
            }

            // Get all uploaded files to check against
            $allFiles = $request->allFiles();

            // Create new elements with sections and ordering
            foreach ($normalizedElements as $index => $e) {
                if (!isset($e['element_type'])) {
                    throw ValidationException::withMessages([
                        "elements.$index" => ['Each element must include element_type.'],
                    ]);
                }

                $elementData = [
                    'element_type' => $e['element_type'],
                    'blog_id' => $id,
                    'section_title' => isset($e['section_title']) && $e['section_title'] !== '' ? $e['section_title'] : null,
                    'order' => isset($e['order']) ? (int)$e['order'] : $index,
                ];

                // Handle image elements
                if ($e['element_type'] === 'image') {
                    $fieldName = $e['value'] ?? null;
                    $imageSaved = false;
                    
                    // First, check if a new file was uploaded with this field name
                    if ($fieldName && isset($allFiles[$fieldName])) {
                        $elemImagePath = $this->saveImage($request->file($fieldName), 'blogs_images');
                        if ($elemImagePath) {
                            $elementData['value'] = $elemImagePath;
                            $imageSaved = true;
                        }
                    }
                    
                    // If no new file uploaded, check if it's an existing image
                    if (!$imageSaved) {
                        if (isset($e['value']) && !empty($e['value'])) {
                            $existingValue = $e['value'];
                            
                            // Check if it's a full URL (http/https)
                            if (filter_var($existingValue, FILTER_VALIDATE_URL) || 
                                strpos($existingValue, 'http://') === 0 ||
                                strpos($existingValue, 'https://') === 0) {
                                // Full URL - keep it
                                $elementData['value'] = $existingValue;
                                $imageSaved = true;
                            }
                            // Check if it's a storage path (/storage/...)
                            elseif (strpos($existingValue, '/storage/') === 0) {
                                // Storage path - keep it
                                $elementData['value'] = $existingValue;
                                $imageSaved = true;
                            }
                            // Check if it's just a filename - construct the path
                            elseif (preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $existingValue)) {
                                // It's a filename - construct the storage URL
                                $elementData['value'] = '/storage/blogs_images/' . basename($existingValue);
                                $imageSaved = true;
                            }
                            // If it doesn't match any pattern but has a value, keep it
                            else {
                                $elementData['value'] = $existingValue;
                                $imageSaved = true;
                            }
                        }
                    }
                    
                    // If still not saved, skip this element (don't throw error - might be intentional)
                    if (!$imageSaved) {
                        continue; // Skip this element instead of throwing error
                    }
                } else {
                    // For non-image elements, use the value directly
                    $elementData['value'] = $e['value'] ?? '';
                }

                // Only create element if we have valid data
                if (isset($elementData['value']) || $elementData['element_type'] !== 'image') {
                    BlogElement::create($elementData);
                }
            }
            
            // Reload blog with elements
            $blog->load('elements');
            $blog->refresh(); // Ensure we get the latest data
            $data = $blog->toArray();
            $data['elements'] = $blog->elements;
            $data['elements_by_sections'] = $blog->getElementsBySections();
            
            return response()->json(['message' => 'Blog updated successfully', 'blog' => $data], 200);
            
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (\Exception $e) {
            // Log the full error for debugging
            \Log::error('Blog update error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'blog_id' => $id,
                'request_data' => $request->except(['elements']) // Don't log full elements array
            ]);
            
            return response()->json([
                'error' => 'Failed to update blog', 
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    public function destroy($id)
    {
        // Delete blog post
        $blog = Blog::findOrFail($id);
        $blog->delete();

        return response()->json(['message' => 'Blog post deleted successfully'], 200);
    }
}