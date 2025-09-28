<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Package;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::where('status', '!=', 'delete')->get();
        return response()->json($categories, 200);
    }

    public function show($id)
    {
        $category = Category::find($id);
        
        if (!$category) {
            return response()->json(['error' => 'Category not found'], 404);
        }
        
        return response()->json($category, 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories',
        ]);

        $category = Category::create([
            'name' => $request->name,
            'status' => 'active'
        ]);

        return response()->json(['message' => 'Category created successfully', 'category' => $category], 201);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $id,
            'status' => 'nullable|string|in:active,inactive'
        ]);

        $category = Category::find($id);

        if (!$category) {
            return response()->json(['error' => 'Category not found'], 404);
        }

        $category->update([
            'name' => $request->name,
            'status' => $request->status ?? $category->status
        ]);

        return response()->json(['message' => 'Category updated successfully', 'category' => $category], 200);
    }

    public function destroy($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['error' => 'Category not found'], 404);
        }

        // Soft delete by updating status
        $category->update(['status' => 'delete']);
        
        // Also delete related packages
        Package::where('category_id', $id)->delete();

        return response()->json(['message' => 'Category deleted successfully'], 200);
    }
}

