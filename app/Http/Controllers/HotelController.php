<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Hotel;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;
class HotelController extends Controller
{
    /**
     * Save an image and return its path.
     *
     * @param \Illuminate\Http\UploadedFile $image
     * @param string $directory
     * @return string|null
     */
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
            $request->validate([
                'name' => 'required|string|max:255',
                'charges' => 'required|numeric',
                'description' => 'required|string',
                'rating' => 'required|numeric',
                'location' => 'required|string',           
                'currency' => 'required|string|max:10',               
                'email' => 'required|email',             
                'phone' => 'required|string|max:20',         
                'breakfast_enabled' => 'required|boolean',
                'dinner_enabled' => 'required|boolean',
                'image' =>'required'
              
            ]);
            $data = $request->only([
                'name',
                'rating',
                'location',
                'charges',
                'description',
                'currency',
                'email',
                'phone',
                'breakfast_enabled',
                'dinner_enabled' 
            ]);



      
            if(isset($_FILES['image'])==true){
                $image = $_FILES['image'];
                if(!$image){
                    return response()->json(['error'=>'please put image ok']);
                }
                $imagePath = $this->saveImage($request->file('image'), 'hotel_images');

                $data['image'] = $imagePath;
                

            }else{
               return response()->json(['error'=>'please put image']);
            }
            

            $package = Hotel::create($data);
    
            return response()->json(['message' => 'Package created successfully', 'package' => $package], 201);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }
    public function update(Request $request)
    {
        try {
            $request->validate([
                'id' =>'required',
                'name' => 'required|string|max:255',
                'charges' => 'required|numeric',
                'description' => 'required|string',
                'rating' => 'required|numeric',
                'location' => 'required|string',           
                'currency' => 'required|string|max:10',               
                'email' => 'required|email',             
                'phone' => 'required|string|max:20',         
                'breakfast_enabled' => 'required|boolean',
                'dinner_enabled' => 'required|boolean'
             
            ]);
            $data = $request->only([
                'name',
                'rating',
                'location',
                'charges',
                'description',
                'currency',
                'email',
                'phone',
                'breakfast_enabled',
                'dinner_enabled' 
            ]);







          
            if(isset($_FILES['image'])==true){
                $image = $_FILES['image'];
                if(!$image){
                    return response()->json(['error'=>'please put package_image ok']);
                }
                $imagePath = $this->saveImage($request->file('image'), 'hotel_images');

                $data['image'] = $imagePath;
                

            }else{
               // return response()->json(['error'=>'please put package_image']);
            }
            


            $package = Hotel::find($request->input('id'));
            $package->update($data);
            $package->save();
    
            return response()->json(['message' => 'Hotel Updated successfully', 'hotel' => $package], 200);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }
    
    public function destroy($id)
    {
        
        $user = Hotel::find($id);

        if (!$user) {
            return response()->json(['error' => 'Package Id is Invalid'], 404);
        }
    
        $user->delete();

        return response()->json(['message' => 'Package deleted successfully'], 200);
    }
}
