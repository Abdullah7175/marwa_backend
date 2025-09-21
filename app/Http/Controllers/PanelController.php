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
        $res = Hotel::where('status','!=','deleted')->get();
      

        return response()->json($res,200);
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
