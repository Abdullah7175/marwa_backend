<?php

namespace App\Http\Controllers;

use App\Models\SeoSettings;
use App\Models\Blog;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SeoController extends Controller
{
    /**
     * Get SEO settings for a specific page
     */
    public function getPageSeo(Request $request): JsonResponse
    {
        $pageName = $request->input('page_name');
        
        if (!$pageName) {
            return response()->json([
                'success' => false,
                'message' => 'Page name is required'
            ], 400);
        }

        $seoSettings = SeoSettings::getForPage($pageName);
        
        if (!$seoSettings) {
            // Return default SEO settings
            $seoSettings = new SeoSettings();
            $seoSettings->page_name = $pageName;
        }

        return response()->json([
            'success' => true,
            'data' => $seoSettings
        ]);
    }

    /**
     * Get SEO settings for a blog post
     */
    public function getBlogSeo(Request $request): JsonResponse
    {
        $blogId = $request->input('blog_id');
        
        if (!$blogId) {
            return response()->json([
                'success' => false,
                'message' => 'Blog ID is required'
            ], 400);
        }

        $blog = Blog::find($blogId);
        
        if (!$blog) {
            return response()->json([
                'success' => false,
                'message' => 'Blog not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'meta_title' => $blog->meta_title,
                'meta_description' => $blog->meta_description,
                'meta_keywords' => $blog->meta_keywords,
                'og_title' => $blog->og_title,
                'og_description' => $blog->og_description,
                'og_image' => $blog->og_image,
                'twitter_title' => $blog->twitter_title,
                'twitter_description' => $blog->twitter_description,
                'twitter_image' => $blog->twitter_image
            ]
        ]);
    }

    /**
     * Get SEO settings for a package
     */
    public function getPackageSeo(Request $request): JsonResponse
    {
        $packageId = $request->input('package_id');
        
        if (!$packageId) {
            return response()->json([
                'success' => false,
                'message' => 'Package ID is required'
            ], 400);
        }

        $package = Package::find($packageId);
        
        if (!$package) {
            return response()->json([
                'success' => false,
                'message' => 'Package not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'meta_title' => $package->meta_title,
                'meta_description' => $package->meta_description,
                'meta_keywords' => $package->meta_keywords,
                'og_title' => $package->og_title,
                'og_description' => $package->og_description,
                'og_image' => $package->og_image,
                'twitter_title' => $package->twitter_title,
                'twitter_description' => $package->twitter_description,
                'twitter_image' => $package->twitter_image
            ]
        ]);
    }

    /**
     * Update SEO settings for a page
     */
    public function updatePageSeo(Request $request): JsonResponse
    {
        $request->validate([
            'page_name' => 'required|string',
            'meta_title' => 'required|string|max:60',
            'meta_description' => 'required|string|max:160',
            'meta_keywords' => 'nullable|string',
            'og_title' => 'nullable|string|max:60',
            'og_description' => 'nullable|string|max:160',
            'og_image' => 'nullable|string',
            'twitter_title' => 'nullable|string|max:60',
            'twitter_description' => 'nullable|string|max:160',
            'twitter_image' => 'nullable|string',
            'structured_data' => 'nullable|array'
        ]);

        $seoSettings = SeoSettings::updateOrCreate(
            ['page_name' => $request->page_name],
            $request->all()
        );

        return response()->json([
            'success' => true,
            'message' => 'SEO settings updated successfully',
            'data' => $seoSettings
        ]);
    }

    /**
     * Update SEO settings for a blog
     */
    public function updateBlogSeo(Request $request): JsonResponse
    {
        $request->validate([
            'blog_id' => 'required|exists:blogs,id',
            'meta_title' => 'nullable|string|max:60',
            'meta_description' => 'nullable|string|max:160',
            'meta_keywords' => 'nullable|string',
            'og_title' => 'nullable|string|max:60',
            'og_description' => 'nullable|string|max:160',
            'og_image' => 'nullable|string',
            'twitter_title' => 'nullable|string|max:60',
            'twitter_description' => 'nullable|string|max:160',
            'twitter_image' => 'nullable|string'
        ]);

        $blog = Blog::find($request->blog_id);
        $blog->update($request->except('blog_id'));

        return response()->json([
            'success' => true,
            'message' => 'Blog SEO settings updated successfully',
            'data' => $blog
        ]);
    }

    /**
     * Update SEO settings for a package
     */
    public function updatePackageSeo(Request $request): JsonResponse
    {
        $request->validate([
            'package_id' => 'required|exists:packages,id',
            'meta_title' => 'nullable|string|max:60',
            'meta_description' => 'nullable|string|max:160',
            'meta_keywords' => 'nullable|string',
            'og_title' => 'nullable|string|max:60',
            'og_description' => 'nullable|string|max:160',
            'og_image' => 'nullable|string',
            'twitter_title' => 'nullable|string|max:60',
            'twitter_description' => 'nullable|string|max:160',
            'twitter_image' => 'nullable|string'
        ]);

        $package = Package::find($request->package_id);
        $package->update($request->except('package_id'));

        return response()->json([
            'success' => true,
            'message' => 'Package SEO settings updated successfully',
            'data' => $package
        ]);
    }

    /**
     * Get all SEO settings
     */
    public function getAllSeoSettings(): JsonResponse
    {
        $seoSettings = SeoSettings::all();
        
        return response()->json([
            'success' => true,
            'data' => $seoSettings
        ]);
    }

    /**
     * Delete SEO settings for a page
     */
    public function deletePageSeo(Request $request): JsonResponse
    {
        $pageName = $request->input('page_name');
        
        if (!$pageName) {
            return response()->json([
                'success' => false,
                'message' => 'Page name is required'
            ], 400);
        }

        $seoSettings = SeoSettings::where('page_name', $pageName)->first();
        
        if (!$seoSettings) {
            return response()->json([
                'success' => false,
                'message' => 'SEO settings not found'
            ], 404);
        }

        $seoSettings->delete();

        return response()->json([
            'success' => true,
            'message' => 'SEO settings deleted successfully'
        ]);
    }
}
