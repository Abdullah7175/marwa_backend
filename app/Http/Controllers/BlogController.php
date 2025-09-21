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
            // Validation
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'image' => 'file|required',
                'elements' => 'required|array'
            ]);


            $imagePath = $this->saveImage($request->file('image'),'blogs_images');
            // Create blog post
            $blog = Blog::create(['title'=>$validatedData['title'],'image'=>$imagePath]);
            $blog_id = $blog['id'];


           // return $blog_id;

        

            $elements = $validatedData['elements'];
            $elementsArray = [];
            foreach ($elements as $jsonString) {
                $elementsArray[] = json_decode($jsonString, true);
            }
            //types ={heading,subheading,points,image}
            foreach ($elementsArray as $e) {
                if ($e['element_type'] === 'image') {
                    $imagePath = $request->file($e['value']);
                    $imagePath = $this->saveImage($imagePath,'blogs_images');
                    
                    BlogElement::create(['element_type'=>'image','value'=>$imagePath,'blog_id'=>$blog_id]);
                }else{
                    BlogElement::create(['element_type'=>$e['element_type'],'value'=>$e['value'],'blog_id'=>$blog_id]);

                }
            }
        } catch (ValidationException $e) {
            // Return validation error response
            return response()->json(['error' => $e->errors()], 422);
        }

        

        return response()->json($blog, 201);
    }
    public function update(Request $request, $id)
    {
        try {
            // Validation
            $validatedData = $request->validate([
            
                'title' => 'required|string|max:255',
                'image' => 'file',
                'elements' => 'required|array'
            ]);



            $blog = Blog::find($id);
            if(!$blog){
                return response()->json(['error'=>'invalid id'],200);
            }

         
            
            $imagePath = $blog->image;
            if($request->file('image')!=null){
                $imagePath = $this->saveImage($request->file('image'),'blogs_images');
            }
            // Create blog post
            $blog->update(['title'=>$validatedData['title'],'image'=>$imagePath]);
            $blog_id = $blog['id']; 
            $res = BlogElement::where('blog_id',$blog_id)->delete();

            $elements = $validatedData['elements'];
            $elementsArray = [];
            foreach ($elements as $jsonString) {
                $elementsArray[] = json_decode($jsonString, true);
            }
            //types ={heading,subheading,points,image}
            foreach ($elementsArray as $e) {
                $element = BlogElement::find($e['id']);
                if($element){
                    $element->update(['element_type'=>$e['element_type'],'value'=>$e['value'],'blog_id'=>$blog_id]);
                    $element->save();
                }else{
                    BlogElement::create(['element_type'=>$e['element_type'],'value'=>$e['value'],'blog_id'=>$blog_id]);
                }
                
            }
        } catch (ValidationException $e) {
            // Return validation error response
            return response()->json(['error' => $e->errors()], 422);
        }

        

        return response()->json($blog, 201);
    }

    public function destroy($id)
    {
        // Delete blog post
        $blog = Blog::findOrFail($id);
        $blog->delete();

        return response()->json(['message' => 'Blog post deleted successfully'], 200);
    }
}
