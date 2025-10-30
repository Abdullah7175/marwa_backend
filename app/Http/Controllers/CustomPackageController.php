<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CustomPackage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class CustomPackageController extends Controller
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

    public function index()
    {
        return CustomPackage::all();
    }

    public function show($id)
    {
        return CustomPackage::findOrFail($id);
    }

    public function store(Request $request)
    {
        try {
            // Define validation rules
            $rules = [
                'user_name' => 'required|string|max:255',
                'tour_days' => 'required|integer|min:1',
                'flight_from' => 'required|string|max:255',
                'country' => 'required|string|max:255',
                'city' => 'required|string|max:255',
                'no_of_travelers' => 'required|integer|min:1',
                'travelers_visa_details' => 'nullable|string',
                'phone' => 'required|string|max:20',
                'email' => 'required|email|max:255',
                'additional_comments' => 'nullable|string',
                'signature_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                'total_amount_hotels' => 'required|numeric|min:0',
                'hotel_makkah_id' => 'nullable|integer',
                'hotel_madina_id' => 'nullable|integer',
                // nights fields are not present in DB; accept but ignore if sent
                'nights_in_makkah' => 'sometimes|integer',
                'nights_in_madina' => 'sometimes|integer',
            ];
    
            // Perform validation
            $validatedData = $request->validate($rules);
    
            // Handle file upload
            $signatureImagePath =$this->saveImage($request->file('signature_image'),'signature_images');
    
            // Remove fields that do not exist in the database schema
            unset($validatedData['nights_in_makkah'], $validatedData['nights_in_madina']);

            // Create CustomPackage instance
            $customPackage = CustomPackage::create(array_merge($validatedData, [
                'signature_image_url' => $signatureImagePath
            ]));
    
            return response()->json($customPackage, 201);
        } catch (ValidationException $e) {
            // Return validation errors as JSON response
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            // Define validation rules
            $rules = [
                'user_name' => 'required|string|max:255',
                'tour_days' => 'required|integer|min:1',
                'flight_from' => 'required|string|max:255',
                'country' => 'required|string|max:255',
                'city' => 'required|string|max:255',
                'no_of_travelers' => 'required|integer|min:1',
                'travelers_visa_details' => 'nullable|string',
                'phone' => 'required|string|max:20',
                'email' => 'required|email|max:255',
                'additional_comments' => 'nullable|string',      
                'total_amount_hotels' => 'required|numeric|min:0',
                'hotel_makkah_id' => 'nullable|integer|min:1',
                'hotel_madina_id' => 'nullable|integer|min:1',
                // nights fields are not in DB; accept but ignore
                'nights_in_makkah' => 'sometimes|integer|min:0',
                'nights_in_madina' => 'sometimes|integer|min:0',
            ];
    
            // Perform validation
            $validatedData = $request->validate($rules);
            $signatureImagePath =$validatedData['signature_image'];
            
       
            if(isset($_FILES['signature_image'])==true){
                $image = $_FILES['signature_image'];
                if(!$image){
                    return response()->json(['error'=>'please put signature_image ok']);
                }
                $imagePath = $this->saveImage($request->file('signature_image'), 'signature_images');

                $signatureImagePath = $imagePath;
                

            }else{
               //return response()->json(['error'=>'please put signature_image']);
            }
            // Handle file upload
    
            // Remove fields that do not exist in the database schema
            unset($validatedData['nights_in_makkah'], $validatedData['nights_in_madina']);

            // Update CustomPackage instance
            CustomPackage::find($id)->update(array_merge($validatedData, [
                'signature_image_url' => $signatureImagePath
            ]));
    
            return response()->json(CustomPackage::find($id), 201);
        } catch (ValidationException $e) {
            // Return validation errors as JSON response
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    public function destroy($id)
    {
        CustomPackage::findOrFail($id)->delete();
        return response('Deleted Successfully', 200);
    }
}
