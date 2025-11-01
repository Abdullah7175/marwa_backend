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
        if ($image->isValid()) {
            $path = $image->store($directory, 'public');
            $url = Storage::url($path);
            return $url;
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
                    'order' => $e['order'] ?? $index,
                ];

                // Handle image elements
                if ($e['element_type'] === 'image') {
                    $fieldName = $e['value'] ?? null;
                    if ($fieldName && $request->hasFile($fieldName)) {
                        $elemImagePath = $this->saveImage($request->file($fieldName), 'blogs_images');
                        $elementData['value'] = $elemImagePath;
                    } elseif (isset($e['value']) && filter_var($e['value'], FILTER_VALIDATE_URL)) {
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
            // Validation
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
                $imagePath = $this->saveImage($request->file('image'), 'blogs_images');
            }
            
            // Update blog post
            $blog->update([
                'title' => $validatedData['title'],
                'body' => $validatedData['body'] ?? $blog->body,
                'image' => $imagePath
            ]);
            
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
                    'section_title' => $e['section_title'] ?? null,
                    'order' => $e['order'] ?? $index,
                ];

                // Handle image elements
                if ($e['element_type'] === 'image') {
                    $fieldName = $e['value'] ?? null;
                    if ($fieldName && $request->hasFile($fieldName)) {
                        // New image uploaded
                        $elemImagePath = $this->saveImage($request->file($fieldName), 'blogs_images');
                        $elementData['value'] = $elemImagePath;
                    } elseif (isset($e['value']) && (filter_var($e['value'], FILTER_VALIDATE_URL) || strpos($e['value'], '/storage/') === 0)) {
                        // Existing image URL, keep it
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
            
            // Reload blog with elements
            $blog->load('elements');
            $data = $blog->toArray();
            $data['elements'] = $blog->elements;
            $data['elements_by_sections'] = $blog->getElementsBySections();
            
            return response()->json(['message' => 'Blog updated successfully', 'blog' => $data], 200);
            
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update blog', 'message' => $e->getMessage()], 500);
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