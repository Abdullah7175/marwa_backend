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
                'image' => 'required|file',
                'elements' => 'required|array',
            ]);

            $imagePath = $this->saveImage($request->file('image'), 'blogs_images');

            $blog = Blog::create([
                'title' => $validatedData['title'],
                'image' => $imagePath,
            ]);
            $blog_id = $blog['id'];

            $elements = $validatedData['elements'];
            $normalizedElements = [];

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

            foreach ($normalizedElements as $index => $e) {
                if (!isset($e['element_type']) || !isset($e['value'])) {
                    throw ValidationException::withMessages([
                        "elements.$index" => ['Each element must include element_type and value.'],
                    ]);
                }

                if ($e['element_type'] === 'image') {
                    $fieldName = $e['value'];
                    if (!$request->hasFile($fieldName)) {
                        throw ValidationException::withMessages([
                            "elements.$index" => ["Image field '$fieldName' not found in request."],
                        ]);
                    }
                    $elemImagePath = $this->saveImage($request->file($fieldName), 'blogs_images');
                    BlogElement::create([
                        'element_type' => 'image',
                        'value' => $elemImagePath,
                        'blog_id' => $blog_id,
                    ]);
                } else {
                    BlogElement::create([
                        'element_type' => $e['element_type'],
                        'value' => $e['value'],
                        'blog_id' => $blog_id,
                    ]);
                }
            }

            return response()->json($blog, 201);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        }
    }
    public function index()
    {
        $blogs = Blog::all();
        $res = [];
        foreach($blogs as $b){
           $data = $b->toArray();
           $elements = BlogElement::where('blog_id',$b['id'])->get();
           $res[] = array_merge($data,['elements'=>$elements]);
        }
        return response()->json($res, 200);
    }

    public function show($id)
    {
        $blog = Blog::find($id);
        
        if (!$blog) {
            return response()->json(['error' => 'Blog not found'], 404);
        }
        
        $data = $blog->toArray();
        $elements = BlogElement::where('blog_id', $blog->id)->get();
        $data['elements'] = $elements;
        
        return response()->json($data, 200);
    }

    public function update(Request $request, $id)
    {
        try {
            // Validation
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'image' => 'nullable|file',
                'elements' => 'required|array'
            ]);

            $blog = Blog::find($id);
            if(!$blog){
                return response()->json(['error' => 'Blog not found'], 404);
            }

            // Handle image update
            $imagePath = $blog->image;
            if($request->hasFile('image')){
                $imagePath = $this->saveImage($request->file('image'), 'blogs_images');
            }
            
            // Update blog post
            $blog->update([
                'title' => $validatedData['title'],
                'image' => $imagePath
            ]);
            
            // Delete existing elements
            BlogElement::where('blog_id', $id)->delete();

            // Add new elements
            $elements = $validatedData['elements'];
            $elementsArray = [];
            foreach ($elements as $jsonString) {
                $elementsArray[] = json_decode($jsonString, true);
            }
            
            // Create new elements
            foreach ($elementsArray as $e) {
                if ($e['element_type'] === 'image' && isset($e['value']) && $request->hasFile($e['value'])) {
                    $imagePath = $this->saveImage($request->file($e['value']), 'blogs_images');
                    BlogElement::create([
                        'element_type' => 'image',
                        'value' => $imagePath,
                        'blog_id' => $id
                    ]);
                } else {
                    BlogElement::create([
                        'element_type' => $e['element_type'],
                        'value' => $e['value'],
                        'blog_id' => $id
                    ]);
                }
            }
            
            return response()->json(['message' => 'Blog updated successfully', 'blog' => $blog], 200);
            
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
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
