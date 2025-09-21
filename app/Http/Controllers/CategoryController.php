<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Package;

class CategoryController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories',
        ]);

        $category = Category::create([
            'name' => $request->name,
        ]);

        return response()->json(['message' => 'Category created successfully', 'category' => $category], 201);
    }
    public function destroy($id)
    {
        
        $user = Category::find($id);

        if (!$user) {
            return response()->json(['error' => 'Category Id is Invalid'], 404);
        }
        Package::where('category_id',$id)->delete();

        $user->update(['status'=>'delete']);
        $user->save();

        return response()->json(['message' => 'Category deleted successfully'], 200);
    }
}

