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
        if ($image && $image->isValid()) {
            $path = $image->store($directory, 'public');
            $url = Storage::url($path);
            // Ensure URL starts with /storage/ for proper preview
            if (strpos($url, '/storage/') !== 0 && strpos($url, 'http') !== 0) {
                $url = '/storage/' . ltrim($url, '/');
            }
            return $url;
        }
        return null;
    }

    /**
     * Format image URLs to ensure they're previewable
     */
    private function formatImageUrl($url)
    {
        if (!$url) return null;
        
        // If already a full URL, return as is
        if (strpos($url, 'http://') === 0 || strpos($url, 'https://') === 0) {
            return $url;
        }
        
        // Ensure it starts with /storage/
        if (strpos($url, '/storage/') !== 0) {
            $url = '/storage/' . ltrim($url, '/');
        }
        
        return $url;
    }

    /**
     * Format package response with proper image URLs
     */
    private function formatPackageResponse($package)
    {
        $data = $package->toArray();
        
        // Format all image URLs
        if (isset($data['package_image'])) {
            $data['package_image'] = $this->formatImageUrl($data['package_image']);
        }
        if (isset($data['hotel_makkah_image'])) {
            $data['hotel_makkah_image'] = $this->formatImageUrl($data['hotel_makkah_image']);
        }
        if (isset($data['hotel_madina_image'])) {
            $data['hotel_madina_image'] = $this->formatImageUrl($data['hotel_madina_image']);
        }
        if (isset($data['trans_image'])) {
            $data['trans_image'] = $this->formatImageUrl($data['trans_image']);
        }
        if (isset($data['visa_image'])) {
            $data['visa_image'] = $this->formatImageUrl($data['visa_image']);
        }
        
        return $data;
    }

    public function index()
    {
        $packages = Package::with('category')->get();
        $formatted = $packages->map(function ($package) {
            return $this->formatPackageResponse($package);
        });
        return response()->json($formatted, 200);
    }

    public function show($id)
    {
        $package = Package::with('category')->find($id);
        
        if (!$package) {
            return response()->json(['error' => 'Package not found'], 404);
        }
        
        return response()->json($this->formatPackageResponse($package), 200);
    }

    public function store(Request $request)
    {
        try {
            // Normalize incoming scalar types from multipart form-data
            $booleanFields = [
                'is_roundtrip','ziyarat','guide','hotel_makkah_enabled','hotel_madina_enabled',
                'visa_enabled','ticket_enabled','breakfast_enabled','dinner_enabled','transport_enabled'
            ];
            $integerFields = ['nights_makkah','nights_madina','nights','category_id'];

            $payload = $request->all();
            foreach ($booleanFields as $field) {
                if (array_key_exists($field, $payload)) {
                    $payload[$field] = in_array((string)$payload[$field], ['1','true','on'], true) ? 1 : 0;
                }
            }
            foreach ($integerFields as $field) {
                if (array_key_exists($field, $payload)) {
                    $payload[$field] = (int) $payload[$field];
                }
            }
            $request->merge($payload);

            // Validate with proper boolean handling - accept boolean, integer (0/1), or string ('0'/'1'/'true'/'false')
            // Match database schema exactly: price fields are varchar(255), not numeric
            $request->validate([
                'name' => 'required|string|max:255',
                'price_single' => 'nullable|string|max:255',
                'what_to_expect' => 'nullable|string',
                'price_quad' => 'nullable|string|max:255',
                'main_points' => 'nullable|string|max:255',
                'price_double' => 'nullable|string|max:255',
                'price_tripple' => 'nullable|string|max:255',
                'currency' => 'nullable|string|max:255',
                'hotel_makkah_name' => 'nullable|string|max:255',
                'hotel_madina_name' => 'nullable|string|max:255',
                'hotel_makkah_detail' => 'nullable|string',
                'hotel_madina_detail' => 'nullable|string',
                'hotel_madina_image' => 'nullable|file|image',
                'hotel_makkah_image' => 'nullable|file|image',
                'trans_title' => 'nullable|string|max:255',
                'trans_detail' => 'nullable|string',
                'trans_image' => 'nullable|file|image',
                'visa_title' => 'nullable|string|max:255',
                'visa_detail' => 'nullable|string',
                'visa_image' => 'nullable|file|image',
                'nights_makkah' => 'required|integer|min:0',
                'nights_madina' => 'required|integer|min:0',
                'nights' => 'required|integer|min:0',
                'is_roundtrip' => 'required',
                'ziyarat' => 'required',
                'guide' => 'required',
                'email' => 'nullable|email|max:255',
                'whatsapp' => 'nullable|string|max:255',
                'phone' => 'nullable|string|max:255',
                'hotel_makkah_enabled' => 'required',
                'hotel_madina_enabled' => 'required',
                'visa_enabled' => 'required',
                'ticket_enabled' => 'required',
                'breakfast_enabled' => 'required',
                'dinner_enabled' => 'required',
                'visa_duration' => 'nullable|string|max:255',
                'package_image' => 'nullable|file|image',
                'transport_enabled' => 'required',
                'category_id' => 'required|integer|exists:categories,id',
                // SEO fields
                'meta_title' => 'nullable|string|max:255',
                'meta_description' => 'nullable|string',
                'meta_keywords' => 'nullable|string|max:255',
                'og_title' => 'nullable|string|max:255',
                'og_description' => 'nullable|string',
                'og_image' => 'nullable|string|max:255',
                'twitter_title' => 'nullable|string|max:255',
                'twitter_description' => 'nullable|string',
                'twitter_image' => 'nullable|string|max:255',
            ], [
                // Custom error messages
                'category_id.exists' => 'The selected category does not exist.',
                'package_image.image' => 'The package image must be an image file.',
                'hotel_madina_image.image' => 'The hotel Madina image must be an image file.',
                'hotel_makkah_image.image' => 'The hotel Makkah image must be an image file.',
                'trans_image.image' => 'The transport image must be an image file.',
                'visa_image.image' => 'The visa image must be an image file.',
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
                'package_image',
                'transport_enabled',
                'category_id',
                'meta_title',
                'meta_description',
                'meta_keywords',
                'og_title',
                'og_description',
                'og_image',
                'twitter_title',
                'twitter_description',
                'twitter_image',
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
            $package->load('category');
    
            return response()->json([
                'message' => 'Package created successfully', 
                'package' => $this->formatPackageResponse($package)
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'errors' => $e->errors(),
                'message' => 'Validation failed. Please check the errors below.',
                'received_data' => $request->except(['package_image', 'hotel_madina_image', 'hotel_makkah_image', 'trans_image', 'visa_image']) // Exclude files for readability
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Package creation error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'error' => 'Failed to create package',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function update(Request $request)
    {
        try {
            // Normalize incoming scalar types from multipart form-data
            $booleanFields = [
                'is_roundtrip','ziyarat','guide','hotel_makkah_enabled','hotel_madina_enabled',
                'visa_enabled','ticket_enabled','breakfast_enabled','dinner_enabled','transport_enabled'
            ];
            $integerFields = ['nights_makkah','nights_madina','nights','category_id'];

            $payload = $request->all();
            foreach ($booleanFields as $field) {
                if (array_key_exists($field, $payload)) {
                    $payload[$field] = in_array((string)$payload[$field], ['1','true','on'], true) ? 1 : 0;
                }
            }
            foreach ($integerFields as $field) {
                if (array_key_exists($field, $payload)) {
                    $payload[$field] = (int) $payload[$field];
                }
            }
            $request->merge($payload);

            // Match database schema exactly: price fields are varchar(255), not numeric
            $request->validate([
                'id' =>'required',
                'name' => 'required|string|max:255',
                'price_single' => 'nullable|string|max:255',
                'what_to_expect' => 'nullable|string',
                'price_quad' => 'nullable|string|max:255',
                'main_points' => 'nullable|string|max:255',
                'price_double' => 'nullable|string|max:255',
                'price_tripple' => 'nullable|string|max:255',
                'currency' => 'nullable|string|max:255',
                'hotel_makkah_name' => 'nullable|string|max:255',
                'hotel_madina_name' => 'nullable|string|max:255',
                'hotel_makkah_detail' => 'nullable|string',
                'hotel_madina_detail' => 'nullable|string',
                'hotel_madina_image' => 'nullable|file|image',
                'hotel_makkah_image' => 'nullable|file|image',
                'trans_title' => 'nullable|string|max:255',
                'trans_detail' => 'nullable|string',
                'trans_image' => 'nullable|file|image',
                'visa_title' => 'nullable|string|max:255',
                'visa_detail' => 'nullable|string',
                'visa_image' => 'nullable|file|image',
                'nights_makkah' => 'required|integer|min:0',
                'nights_madina' => 'required|integer|min:0',
                'nights' => 'required|integer|min:0',
                'is_roundtrip' => 'required',
                'ziyarat' => 'required',
                'guide' => 'required',
                'email' => 'nullable|email|max:255',
                'whatsapp' => 'nullable|string|max:255',
                'phone' => 'nullable|string|max:255',
                'hotel_makkah_enabled' => 'required',
                'hotel_madina_enabled' => 'required',
                'visa_enabled' => 'required',
                'ticket_enabled' => 'required',
                'breakfast_enabled' => 'required',
                'dinner_enabled' => 'required',
                'visa_duration' => 'nullable|string|max:255',
                'package_image' => 'nullable|file|image',
                'transport_enabled' => 'required',
                'category_id' => 'required|exists:categories,id',
                // SEO fields
                'meta_title' => 'nullable|string|max:255',
                'meta_description' => 'nullable|string',
                'meta_keywords' => 'nullable|string|max:255',
                'og_title' => 'nullable|string|max:255',
                'og_description' => 'nullable|string',
                'og_image' => 'nullable|string|max:255',
                'twitter_title' => 'nullable|string|max:255',
                'twitter_description' => 'nullable|string',
                'twitter_image' => 'nullable|string|max:255',
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
            $package->load('category');
    
            return response()->json([
                'message' => 'Package Updated successfully', 
                'package' => $this->formatPackageResponse($package)
            ], 200);
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
