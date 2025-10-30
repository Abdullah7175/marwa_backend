<?php

namespace App\Http\Controllers;



use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Package;
use App\Models\Hotel;
use App\Models\Blog;


class PanelController extends Controller
{
    public function getAllCategories(){
        $res = Category::all();
        $retS = [];
        foreach($res as $cat){
            $packages_count  = Package::where('category_id',$cat->id)->count();
            $retS[]=array_merge($cat->toArray(),['packages_count'=>$packages_count]);
        }

        return response()->json($retS,200);
    }
    public function getAllHotels(){
        $hotels = Hotel::all()->map(function ($hotel) {
            return $this->formatHotelResponse($hotel);
        });

        return response()->json($hotels, 200);
    }

    /**
     * Format hotel data to ensure all required fields are present
     */
    private function formatHotelResponse($hotel)
    {
        // Extract numeric value from charges string
        $chargesNumeric = preg_replace('/[^0-9.]/', '', $hotel->charges ?? '0');
        $chargesNumeric = $chargesNumeric ? (float) $chargesNumeric : 0;
        
        // Parse rating to ensure it's numeric
        $rating = $hotel->rating ?? 0;
        if (!is_numeric($rating)) {
            if (preg_match('/(\d+\.?\d*)/', $rating, $matches)) {
                $rating = (float) $matches[1];
            } else {
                $rating = 0.0;
            }
        } else {
            $rating = (float) $rating;
        }
        
        $currency = $hotel->currency ?? 'USD';
        
        return [
            'id' => $hotel->id,
            'name' => $hotel->name ?? '',
            'location' => $hotel->location ?? '',
            'charges' => $hotel->charges ?? '0',
            'charges_numeric' => $chargesNumeric,
            'rating' => $rating,
            'image' => $hotel->image ?? '',
            'description' => $hotel->description ?? '',
            'currency' => $currency,
            'phone' => $hotel->phone ?? '',
            'email' => $hotel->email ?? '',
            'status' => $hotel->status ?? 'active',
            'breakfast_enabled' => $hotel->breakfast_enabled ?? false,
            'dinner_enabled' => $hotel->dinner_enabled ?? false,
            'price_per_night' => $currency . $chargesNumeric,
            'created_at' => $hotel->created_at,
            'updated_at' => $hotel->updated_at,
        ];
    }

    public function updateCategory(Request $request){
        $action = $request->input('action','update_status');
        $id = $request->input('id',-1);
        if($id==-1){
            return response()->json(['error'=>'please put id'],200);
        }

        if($action =='update_status'){
            $state = $request->input('status','active');
            $category = Category::find($id);
            if($category){
                $category->update(['status'=>$state]);
            }
            return response()->json(['message'=>"updated succesfully"],200);

        }else if($action=='delete'){
            $category = Category::find($id);
            if($category){
                $category->delete();
            }
            return response()->json(['message'=>"deleted succesfully"],200);

        }
    }

    public function updateHotel(Request $request){
        $action = $request->input('action','update_status');
        $id = $request->input('id',-1);
        if($id==-1){
            return response()->json(['error'=>'please put id'],200);
        }

        if($action =='update_status'){
            $state = $request->input('status','active');
            $hotel = Hotel::find($id);
            if($hotel){
                $hotel->update(['status'=>$state]);
            }
            return response()->json(['message'=>"updated succesfully"],200);

        }else if($action=='delete'){
            $hotel = Hotel::find($id);
            if($hotel){
                $hotel->update(['status'=>'deleted']);

            }
            return response()->json(['message'=>"deleted succesfully"],200);

        }
    }
}
