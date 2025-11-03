<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Hotel;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\QueryException;
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
     * Format image URL to ensure it's previewable
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

    public function index()
    {
        $hotels = Hotel::all()->map(function ($hotel) {
            return $this->formatHotelResponse($hotel);
        });
        return response()->json($hotels, 200);
    }

    public function show($id)
    {
        $hotel = Hotel::find($id);
        
        if (!$hotel) {
            return response()->json(['error' => 'Hotel not found'], 404);
        }
        
        return response()->json($this->formatHotelResponse($hotel), 200);
    }

    /**
     * Format hotel data to ensure all required fields are present
     */
    private function formatHotelResponse($hotel)
    {
        // Extract numeric value from charges string
        $chargesNumeric = preg_replace('/[^0-9.]/', '', $hotel->charges);
        $chargesNumeric = $chargesNumeric ? (float) $chargesNumeric : 0;
        
        return [
            'id' => $hotel->id,
            'name' => $hotel->name ?? '',
            'location' => $hotel->location ?? '',
            'charges' => $hotel->charges ?? '0',
            'charges_numeric' => $chargesNumeric,
            'rating' => $hotel->rating ?? 0,
            'image' => $this->formatImageUrl($hotel->image ?? ''),
            'description' => $hotel->description ?? '',
            'currency' => $hotel->currency ?? 'USD',
            'phone' => $hotel->phone ?? '',
            'email' => $hotel->email ?? '',
            'status' => $hotel->status ?? 'active',
            'breakfast_enabled' => $hotel->breakfast_enabled ?? false,
            'dinner_enabled' => $hotel->dinner_enabled ?? false,
            'price_per_night' => ($hotel->currency ?? 'USD') . $chargesNumeric,
            'created_at' => $hotel->created_at,
            'updated_at' => $hotel->updated_at,
        ];
    }

    public function store(Request $request)
    {
        try {
            // Normalize incoming scalar types from multipart form-data
            $booleanFields = ['breakfast_enabled', 'dinner_enabled'];
            
            $payload = $request->all();
            foreach ($booleanFields as $field) {
                if (array_key_exists($field, $payload)) {
                    $payload[$field] = in_array((string)$payload[$field], ['1','true','on'], true) ? 1 : 0;
                }
            }
            $request->merge($payload);

            // Match database schema: charges is varchar(255), not numeric
            $request->validate([
                'name' => 'required|string|max:255',
                'location' => 'required|string|max:255',
                'charges' => 'required|string|max:255',
                'rating' => 'required|string|max:255',
                'image' => 'required|file|image',
                'description' => 'required|string|max:255',
                'currency' => 'nullable|string|max:255',
                'email' => 'nullable|email|max:255',
                'phone' => 'nullable|string|max:255',
                'breakfast_enabled' => 'nullable',
                'dinner_enabled' => 'nullable',
                'status' => 'nullable|string|max:255'
            ], [
                'image.image' => 'The image must be an image file.',
                'image.required' => 'The image field is required.',
            ]);
            
            // Prepare data - start with required fields that definitely exist
            $data = [
                'name' => $request->input('name'),
                'location' => $request->input('location'),
                'charges' => $request->input('charges'),
                'rating' => $request->input('rating'),
                'description' => $request->input('description'),
            ];

            // Add optional fields - will fail gracefully if columns don't exist
            $optionalFields = ['currency', 'email', 'phone', 'status', 'breakfast_enabled', 'dinner_enabled'];
            foreach ($optionalFields as $field) {
                if ($request->has($field)) {
                    $value = $request->input($field);
                    // Convert boolean fields to 0/1 for database
                    if (in_array($field, ['breakfast_enabled', 'dinner_enabled'])) {
                        $value = in_array((string)$value, ['1','true','on'], true) ? 1 : 0;
                    }
                    // Only add if not null or empty string (for string fields)
                    if ($value !== null && $value !== '') {
                        $data[$field] = $value;
                    }
                }
            }

            // Handle image upload
            if (!$request->hasFile('image')) {
                return response()->json([
                    'error' => 'Image is required',
                    'message' => 'The image field is required.'
                ], 422);
            }

            $imagePath = $this->saveImage($request->file('image'), 'hotel_images');
            
            if (!$imagePath) {
                return response()->json([
                    'error' => 'Failed to save image',
                    'message' => 'The uploaded image could not be saved. Please try again.'
                ], 422);
            }

            $data['image'] = $imagePath;

            // Create hotel with error handling for missing columns
            try {
                $hotel = Hotel::create($data);
            } catch (QueryException $dbEx) {
                // Catch database errors (like missing columns)
                $errorMessage = $dbEx->getMessage();
                
                // Check if it's a column not found error
                if (strpos($errorMessage, 'Unknown column') !== false || 
                    strpos($errorMessage, 'doesn\'t exist') !== false ||
                    strpos($errorMessage, 'Column not found') !== false) {
                    \Log::error('Hotels table missing columns: ' . $errorMessage);
                    return response()->json([
                        'error' => 'Database schema mismatch',
                        'message' => 'The hotels table is missing required columns (currency, email, phone, status, breakfast_enabled, dinner_enabled).',
                        'fix' => 'Run the SQL file: fix_hotels_table.sql in your database, or execute: ' .
                            'ALTER TABLE hotels ADD COLUMN currency VARCHAR(255) NULL DEFAULT "USD" AFTER description, ' .
                            'ADD COLUMN phone VARCHAR(255) NULL AFTER currency, ' .
                            'ADD COLUMN email VARCHAR(255) NULL AFTER phone, ' .
                            'ADD COLUMN status VARCHAR(255) NULL DEFAULT "active" AFTER email, ' .
                            'ADD COLUMN breakfast_enabled TINYINT(1) DEFAULT 0 AFTER status, ' .
                            'ADD COLUMN dinner_enabled TINYINT(1) DEFAULT 0 AFTER breakfast_enabled;',
                        'db_error' => $errorMessage
                    ], 500);
                }
                
                // Re-throw if it's a different database error
                throw $dbEx;
            }
    
            return response()->json([
                'message' => 'Hotel created successfully', 
                'hotel' => $this->formatHotelResponse($hotel)
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'errors' => $e->errors(),
                'message' => 'Validation failed. Please check the errors below.',
                'received_data' => $request->except(['image'])
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Hotel creation error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return response()->json([
                'error' => 'Failed to create hotel',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function update(Request $request)
    {
        try {
            // Normalize incoming scalar types from multipart form-data
            $booleanFields = ['breakfast_enabled', 'dinner_enabled'];
            
            $payload = $request->all();
            foreach ($booleanFields as $field) {
                if (array_key_exists($field, $payload)) {
                    $payload[$field] = in_array((string)$payload[$field], ['1','true','on'], true) ? 1 : 0;
                }
            }
            $request->merge($payload);

            // Match database schema: charges is varchar(255), not numeric
            $request->validate([
                'id' => 'required',
                'name' => 'required|string|max:255',
                'location' => 'required|string|max:255',
                'charges' => 'required|string|max:255',
                'rating' => 'required|string|max:255',
                'description' => 'required|string|max:255',
                'image' => 'nullable|file|image',
                'currency' => 'nullable|string|max:255',
                'email' => 'nullable|email|max:255',
                'phone' => 'nullable|string|max:255',
                'breakfast_enabled' => 'nullable',
                'dinner_enabled' => 'nullable',
                'status' => 'nullable|string|max:255'
            ], [
                'image.image' => 'The image must be an image file.',
            ]);
            
            $data = $request->only([
                'name',
                'location',
                'charges',
                'rating',
                'description',
                'currency',
                'email',
                'phone',
                'breakfast_enabled',
                'dinner_enabled',
                'status'
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
            


            $hotel = Hotel::find($request->input('id'));
            $hotel->update($data);
            $hotel->save();
    
            return response()->json([
                'message' => 'Hotel Updated successfully', 
                'hotel' => $this->formatHotelResponse($hotel)
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'errors' => $e->errors(),
                'message' => 'Validation failed. Please check the errors below.'
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Hotel update error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return response()->json([
                'error' => 'Failed to update hotel',
                'message' => $e->getMessage()
            ], 500);
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
