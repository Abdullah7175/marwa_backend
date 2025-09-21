<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Package;
use App\Models\Inquiry;
use App\Models\Blog;
use App\Models\BlogElement;
use Illuminate\Support\Facades\Validator;

class WebController extends Controller
{

    public function getBlogs(){

        $blogs = Blog::all();
        $res = [];
        foreach($blogs as $b){
           $data = $b->toArray();
           $elements = BlogElement::where('blog_id',$b['id'])->get();
           $res[] = array_merge($data,['elements'=>$elements]);
        }
        return response()->json($res,200);
    }






   public function getPackages(){
    $res = [];

    $categories = Category::where('status','active')->get();
    foreach($categories as $cat){
        $packs = Package::where('category_id',$cat['id'])->get();
        $res[] = array_merge($cat->toArray(),['list'=>$packs]);
    }


    return response()->json($res,200);
   }



   public function createIquiry(Request $request)
   {
       // Validation rules
       $rules = [
           'name' => 'required|string',
           'email' => 'required|email',
           'phone' => 'required|string',
           'message' => 'required|string',
       ];

       // Validate the request data
       $validator = Validator::make($request->all(), $rules);

       // If validation fails, return error response
       if ($validator->fails()) {
           return response()->json(['errors' => $validator->errors()], 200);
       }

       // Create inquiry
       $inquiry = Inquiry::create($validator->validated());

       // Return success response
       return response()->json(['message' => 'Inquiry created successfully', 'inquiry' => $inquiry], 201);
   }


   public function deleteInquiry($id){
    $i = Inquiry::find($id);
    if($i){
        $i->delete();
    }

    return response()->json(Inquiry::all(),200);
   }

   public function getInquiries(){
    return response()->json(Inquiry::all(),200);
   }



}
