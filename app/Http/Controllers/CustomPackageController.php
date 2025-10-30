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
            // Force JSON response
            $request->headers->set('Accept', 'application/json');
            
            // Convert string numbers to integers for multipart form data
            $data = $request->all();
            $integerFields = ['tour_days', 'no_of_travelers', 'hotel_makkah_id', 'hotel_madina_id', 'nights_in_makkah', 'nights_in_madina'];
            foreach ($integerFields as $field) {
                if (isset($data[$field]) && is_string($data[$field]) && is_numeric($data[$field])) {
                    $data[$field] = (int) $data[$field];
                    $request->merge([$field => $data[$field]]);
                }
            }
            
            // Define validation rules
            $rules = [
                'user_name' => 'required|string|max:255',
                'tour_days' => 'required|integer|min:1',
                'flight_from' => 'required|string|max:255',
                'country' => 'required|string|max:255',
                'city' => 'required|string|max:255',
                'no_of_travelers' => 'required|integer|min:1',
                'travelers_visa_details' => 'nullable|string',
                'phone' => 'required|string|max:255', // Increased max length
                'email' => 'required|email|max:255',
                'additional_comments' => 'nullable|string',
                'signature_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120', // Increased to 5MB
                'total_amount_hotels' => 'required|numeric|min:0',
                'hotel_makkah_id' => 'nullable|integer|min:0', // Now nullable in DB
                'hotel_madina_id' => 'nullable|integer|min:0', // Now nullable in DB
                'nights_in_makkah' => 'nullable|integer|min:0',
                'nights_in_madina' => 'nullable|integer|min:0',
            ];
    
            // Perform validation
            $validatedData = $request->validate($rules);
    
            // Handle file upload
            if (!$request->hasFile('signature_image')) {
                return response()->json([
                    'errors' => ['signature_image' => ['The signature image field is required.']]
                ], 422);
            }
            
            $signatureImagePath = $this->saveImage($request->file('signature_image'), 'signature_images');
            
            if (!$signatureImagePath) {
                return response()->json([
                    'errors' => ['signature_image' => ['Failed to upload signature image.']]
                ], 422);
            }

            // Prepare data for database
            $packageData = [
                'user_name' => $validatedData['user_name'],
                'tour_days' => (int) $validatedData['tour_days'],
                'flight_from' => $validatedData['flight_from'],
                'country' => $validatedData['country'],
                'city' => $validatedData['city'],
                'no_of_travelers' => (int) $validatedData['no_of_travelers'],
                'travelers_visa_details' => $validatedData['travelers_visa_details'] ?? null,
                'phone' => $validatedData['phone'],
                'email' => $validatedData['email'],
                'additional_comments' => $validatedData['additional_comments'] ?? null,
                'signature_image_url' => $signatureImagePath,
                'total_amount_hotels' => (float) $validatedData['total_amount_hotels'],
                'hotel_makkah_id' => isset($validatedData['hotel_makkah_id']) && $validatedData['hotel_makkah_id'] !== null ? (int) $validatedData['hotel_makkah_id'] : null,
                'hotel_madina_id' => isset($validatedData['hotel_madina_id']) && $validatedData['hotel_madina_id'] !== null ? (int) $validatedData['hotel_madina_id'] : null,
                'nights_in_makkah' => isset($validatedData['nights_in_makkah']) ? (int) $validatedData['nights_in_makkah'] : null,
                'nights_in_madina' => isset($validatedData['nights_in_madina']) ? (int) $validatedData['nights_in_madina'] : null,
            ];

            // Create CustomPackage instance
            $customPackage = CustomPackage::create($packageData);
    
            return response()->json($customPackage, 201)->header('Content-Type', 'application/json');
        } catch (ValidationException $e) {
            // Return validation errors as JSON response
            return response()->json([
                'errors' => $e->errors(),
                'message' => 'Validation failed'
            ], 422)->header('Content-Type', 'application/json');
        } catch (\Exception $e) {
            // Return generic error for other exceptions
            return response()->json([
                'error' => 'Failed to create custom package',
                'message' => $e->getMessage()
            ], 500)->header('Content-Type', 'application/json');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            // Force JSON response
            $request->headers->set('Accept', 'application/json');
            
            // Define validation rules
            $rules = [
                'user_name' => 'required|string|max:255',
                'tour_days' => 'required|integer|min:1',
                'flight_from' => 'required|string|max:255',
                'country' => 'required|string|max:255',
                'city' => 'required|string|max:255',
                'no_of_travelers' => 'required|integer|min:1',
                'travelers_visa_details' => 'nullable|string',
                'phone' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'additional_comments' => 'nullable|string',      
                'total_amount_hotels' => 'required|numeric|min:0',
                'hotel_makkah_id' => 'nullable|integer|min:0',
                'hotel_madina_id' => 'nullable|integer|min:0',
                'signature_image' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:5120',
                // nights fields are not in DB; accept but ignore
                'nights_in_makkah' => 'sometimes|integer|min:0',
                'nights_in_madina' => 'sometimes|integer|min:0',
            ];
    
            // Convert string numbers to integers for multipart form data
            $data = $request->all();
            $integerFields = ['tour_days', 'no_of_travelers', 'hotel_makkah_id', 'hotel_madina_id', 'nights_in_makkah', 'nights_in_madina'];
            foreach ($integerFields as $field) {
                if (isset($data[$field]) && is_string($data[$field]) && is_numeric($data[$field])) {
                    $data[$field] = (int) $data[$field];
                    $request->merge([$field => $data[$field]]);
                }
            }
            
            // Perform validation
            $validatedData = $request->validate($rules);
            
            // Prepare data for database
            $packageData = [
                'user_name' => $validatedData['user_name'],
                'tour_days' => (int) $validatedData['tour_days'],
                'flight_from' => $validatedData['flight_from'],
                'country' => $validatedData['country'],
                'city' => $validatedData['city'],
                'no_of_travelers' => (int) $validatedData['no_of_travelers'],
                'travelers_visa_details' => $validatedData['travelers_visa_details'] ?? null,
                'phone' => $validatedData['phone'],
                'email' => $validatedData['email'],
                'additional_comments' => $validatedData['additional_comments'] ?? null,
                'total_amount_hotels' => (float) $validatedData['total_amount_hotels'],
                'hotel_makkah_id' => isset($validatedData['hotel_makkah_id']) && $validatedData['hotel_makkah_id'] !== null ? (int) $validatedData['hotel_makkah_id'] : null,
                'hotel_madina_id' => isset($validatedData['hotel_madina_id']) && $validatedData['hotel_madina_id'] !== null ? (int) $validatedData['hotel_madina_id'] : null,
                'nights_in_makkah' => isset($validatedData['nights_in_makkah']) ? (int) $validatedData['nights_in_makkah'] : null,
                'nights_in_madina' => isset($validatedData['nights_in_madina']) ? (int) $validatedData['nights_in_madina'] : null,
            ];
            
            // Handle file upload if provided
            if ($request->hasFile('signature_image')) {
                $imagePath = $this->saveImage($request->file('signature_image'), 'signature_images');
                if ($imagePath) {
                    $packageData['signature_image_url'] = $imagePath;
                }
            }

            // Update CustomPackage instance
            $customPackage = CustomPackage::findOrFail($id);
            $customPackage->update($packageData);
    
            return response()->json($customPackage->fresh(), 200)->header('Content-Type', 'application/json');
        } catch (ValidationException $e) {
            // Return validation errors as JSON response
            return response()->json([
                'errors' => $e->errors(),
                'message' => 'Validation failed'
            ], 422)->header('Content-Type', 'application/json');
        } catch (\Exception $e) {
            // Return generic error for other exceptions
            return response()->json([
                'error' => 'Failed to update custom package',
                'message' => $e->getMessage()
            ], 500)->header('Content-Type', 'application/json');
        }
    }

    public function destroy($id)
    {
        CustomPackage::findOrFail($id)->delete();
        return response('Deleted Successfully', 200);
    }
}
