<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Package;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;
class PackageController extends Controller
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
                'price_single' => 'required|numeric',
                'what_to_expect' => 'required|string',
                'price_quad' => 'required|numeric',
                'main_points' => 'required|string',
                'price_double' => 'required|numeric',
                'price_tripple' => 'required|numeric',
                'currency' => 'required|string|max:10',
                'hotel_makkah_name' => 'nullable|string',
                'hotel_madina_name' => 'nullable|string',
                'hotel_makkah_detail' => 'nullable|string',
                'hotel_madina_detail' => 'nullable|string',
                'hotel_madina_image' => 'nullable', // Update with appropriate validation rules
                'hotel_makkah_image' => 'nullable', // Update with appropriate validation rules
                'trans_title' => 'nullable|string',
                'trans_detail' => 'nullable|string',
                'trans_image' => 'nullable', // Update with appropriate validation rules
                'visa_title' => 'nullable|string',
                'visa_detail' => 'nullable|string',
                'visa_image' => 'nullable', // Update with appropriate validation rules
                'nights_makkah' => 'required|integer|min:0',
                'nights_madina' => 'required|integer|min:0',
                'nights' => 'required|integer|min:0',
                'is_roundtrip' => 'required|boolean',
                'ziyarat' => 'required|boolean',
                'guide' => 'required|boolean',
                'email' => 'required|email',
                'whatsapp' => 'required|string|max:20',
                'phone' => 'required|string|max:20',
                'hotel_makkah_enabled' => 'required|boolean',
                'hotel_madina_enabled' => 'required|boolean',
                'visa_enabled' => 'required|boolean',
                'ticket_enabled' => 'required|boolean',
                'breakfast_enabled' => 'required|boolean',
                'dinner_enabled' => 'required|boolean',
                'visa_duration' => 'nullable|string',
                'package_image' => 'nullable',
                'transport_enabled' => 'required|boolean',
                'category_id' => 'required|exists:categories,id',
            ]);
            $data = $request->only([
                'name',
                'price_single',
                'what_to_expect',
                'price_quad',
                'main_points',
                'price_double',
                'price_tripple',
                'currency',
                'hotel_makkah_name',
                'hotel_madina_name',
                'hotel_makkah_detail',
                'hotel_madina_detail',
                'trans_title',
                'trans_detail',
                'visa_title',
                'visa_detail',
                'nights_makkah',
                'nights_madina',
                'nights',
                'is_roundtrip',
                'ziyarat',
                'guide',
                'email',
                'whatsapp',
                'phone',
                'hotel_makkah_enabled',
                'hotel_madina_enabled',
                'visa_enabled',
                'ticket_enabled',
                'breakfast_enabled',
                'dinner_enabled',
                'visa_duration',
                'transport_enabled',
                'category_id',
            ]);



            if($request->input('hotel_madina_enabled')==1  ){
                if(isset($_FILES['hotel_madina_image'])==true){
                    $image = $_FILES['hotel_madina_image'];
                    if(!$image){
                        return response()->json(['error'=>'please put hotel_madina_image ok']);
                    }
                    $imagePath = $this->saveImage($request->file('hotel_madina_image'), 'package_images');

                    $data['hotel_madina_image'] = $imagePath;
                    

                }else{
                    return response()->json(['error'=>'please put hotel_madina_image']);
                }

            }
            if($request->input('hotel_makkah_enabled')==1  ){
                if(isset($_FILES['hotel_makkah_image'])==true){
                    $image = $_FILES['hotel_makkah_image'];
                    if(!$image){
                        return response()->json(['error'=>'please put hotel_makkah_image ok']);
                    }
                    $imagePath = $this->saveImage($request->file('hotel_makkah_image'), 'package_images');

                    $data['hotel_makkah_image'] = $imagePath;
                    

                }else{
                    return response()->json(['error'=>'please put hotel_makkah_image']);
                }
            }

            if($request->input('transport_enabled')==1  ){
                if(isset($_FILES['trans_image'])==true){
                    $image = $_FILES['trans_image'];
                    if(!$image){
                        return response()->json(['error'=>'please put trans_image ok']);
                    }
                    $imagePath = $this->saveImage($request->file('trans_image'), 'package_images');

                    $data['trans_image'] = $imagePath;
                    

                }else{
                    return response()->json(['error'=>'please put trans_image']);
                }

            }


            if($request->input('visa_enabled')==1  ){
                if(isset($_FILES['visa_image'])==true){
                    $image = $_FILES['visa_image'];
                    if(!$image){
                        return response()->json(['error'=>'please put visa_image ok']);
                    }
                    $imagePath = $this->saveImage($request->file('visa_image'), 'package_images');

                    $data['visa_image'] = $imagePath;
                    

                }else{
                    return response()->json(['error'=>'please put visa_image']);
                }

            }
            if(isset($_FILES['package_image'])==true){
                $image = $_FILES['package_image'];
                if(!$image){
                    return response()->json(['error'=>'please put package_image ok']);
                }
                $imagePath = $this->saveImage($request->file('package_image'), 'package_images');

                $data['package_image'] = $imagePath;
                

            }else{
               return response()->json(['error'=>'please put package_image']);
            }
            

            $package = Package::create($data);
    
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
                'price_single' => 'required|numeric',
                'what_to_expect' => 'required|string',
                'price_quad' => 'required|numeric',
                'main_points' => 'required|string',
                'price_double' => 'required|numeric',
                'price_tripple' => 'required|numeric',
                'currency' => 'required|string|max:10',
                'hotel_makkah_name' => 'nullable|string',
                'hotel_madina_name' => 'nullable|string',
                'hotel_makkah_detail' => 'nullable|string',
                'hotel_madina_detail' => 'nullable|string',
                'hotel_madina_image' => 'nullable', // Update with appropriate validation rules
                'hotel_makkah_image' => 'nullable', // Update with appropriate validation rules
                'trans_title' => 'nullable|string',
                'trans_detail' => 'nullable|string',
                'trans_image' => 'nullable', // Update with appropriate validation rules
                'visa_title' => 'nullable|string',
                'visa_detail' => 'nullable|string',
                'visa_image' => 'nullable', // Update with appropriate validation rules
                'nights_makkah' => 'required|integer|min:0',
                'nights_madina' => 'required|integer|min:0',
                'nights' => 'required|integer|min:0',
                'is_roundtrip' => 'required|boolean',
                'ziyarat' => 'required|boolean',
                'guide' => 'required|boolean',
                'email' => 'required|email',
                'whatsapp' => 'required|string|max:20',
                'phone' => 'required|string|max:20',
                'hotel_makkah_enabled' => 'required|boolean',
                'hotel_madina_enabled' => 'required|boolean',
                'visa_enabled' => 'required|boolean',
                'ticket_enabled' => 'required|boolean',
                'breakfast_enabled' => 'required|boolean',
                'dinner_enabled' => 'required|boolean',
                'visa_duration' => 'nullable|string',
                'package_image' => 'nullable',
                'transport_enabled' => 'required|boolean',
                'category_id' => 'required|exists:categories,id',
            ]);
            $data = $request->only([
                'name',
                'price_single',
                'what_to_expect',
                'price_quad',
                'main_points',
                'price_double',
                'price_tripple',
                'currency',
                'hotel_makkah_name',
                'hotel_madina_name',
                'hotel_makkah_detail',
                'hotel_madina_detail',
                'trans_title',
                'trans_detail',
                'visa_title',
                'visa_detail',
                'nights_makkah',
                'nights_madina',
                'nights',
                'is_roundtrip',
                'ziyarat',
                'guide',
                'email',
                'whatsapp',
                'phone',
                'hotel_makkah_enabled',
                'hotel_madina_enabled',
                'visa_enabled',
                'ticket_enabled',
                'breakfast_enabled',
                'dinner_enabled',
                'visa_duration',
                'transport_enabled',
                'category_id',
            ]);





            if($request->input('hotel_madina_enabled')==1  ){
                if(isset($_FILES['hotel_madina_image'])==true){
                    $image = $_FILES['hotel_madina_image'];
                    if(!$image){
                        return response()->json(['error'=>'please put hotel_madina_image ok']);
                    }
                    $imagePath = $this->saveImage($request->file('hotel_madina_image'), 'package_images');

                    $data['hotel_madina_image'] = $imagePath;
                    

                }else{
                   // return response()->json(['error'=>'please put hotel_madina_image']);
                }

            }
            if($request->input('hotel_makkah_enabled')==1  ){
                if(isset($_FILES['hotel_makkah_image'])==true){
                    $image = $_FILES['hotel_makkah_image'];
                    if(!$image){
                        return response()->json(['error'=>'please put hotel_makkah_image ok']);
                    }
                    $imagePath = $this->saveImage($request->file('hotel_makkah_image'), 'package_images');

                    $data['hotel_makkah_image'] = $imagePath;
                    

                }else{
                   // return response()->json(['error'=>'please put hotel_makkah_image']);
                }
            }

            if($request->input('transport_enabled')==1  ){
                if(isset($_FILES['trans_image'])==true){
                    $image = $_FILES['trans_image'];
                    if(!$image){
                        return response()->json(['error'=>'please put trans_image ok']);
                    }
                    $imagePath = $this->saveImage($request->file('trans_image'), 'package_images');

                    $data['trans_image'] = $imagePath;
                    

                }else{
                   // return response()->json(['error'=>'please put trans_image']);
                }

            }


            if($request->input('visa_enabled')==1  ){
                if(isset($_FILES['visa_image'])==true){
                    $image = $_FILES['visa_image'];
                    if(!$image){
                        return response()->json(['error'=>'please put visa_image ok']);
                    }
                    $imagePath = $this->saveImage($request->file('visa_image'), 'package_images');

                    $data['visa_image'] = $imagePath;
                    

                }else{
                    //return response()->json(['error'=>'please put visa_image']);
                }

            }
            if(isset($_FILES['package_image'])==true){
                $image = $_FILES['package_image'];
                if(!$image){
                    return response()->json(['error'=>'please put package_image ok']);
                }
                $imagePath = $this->saveImage($request->file('package_image'), 'package_images');

                $data['package_image'] = $imagePath;
                

            }else{
               // return response()->json(['error'=>'please put package_image']);
            }
            


            $package = Package::find($request->input('id'));
            $package->update($data);
            $package->save();
    
            return response()->json(['message' => 'Package Updated successfully', 'package' => $package], 200);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }
    
    public function destroy($id)
    {
        
        $user = Package::find($id);

        if (!$user) {
            return response()->json(['error' => 'Package Id is Invalid'], 404);
        }
    
        $user->delete();

        return response()->json(['message' => 'Package deleted successfully'], 200);
    }
}
