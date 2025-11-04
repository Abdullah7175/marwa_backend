<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Package;
use App\Models\Inquiry;
use App\Models\Blog;
use App\Models\BlogElement;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class WebController extends Controller
{

    public function getBlogs(){

        $blogs = Blog::with('elements')->get();
        $res = [];
        foreach($blogs as $b){
           $data = $b->toArray();
           // Get elements ordered by section and order
           $data['elements'] = $b->elements;
           // Also include elements grouped by sections for easier frontend rendering
           $data['elements_by_sections'] = $b->getElementsBySections();
           $res[] = $data;
        }
        return response()->json($res,200);
    }






   public function getPackages(){
    $res = [];

    $categories = Category::where('status','active')->get();
    foreach($categories as $cat){
        $packs = Package::where('category_id',$cat['id'])->get();
        $res[] = array_merge($cat->toArray(),['list'=>$packs]);
    }


    return response()->json($res,200);
   }



   public function createIquiry(Request $request)
   {
       try {
           // Force JSON response to prevent redirects
           $request->headers->set('Accept', 'application/json');
           
           // Validation rules - package fields are all optional
           $rules = [
               'name' => 'required|string|max:255',
               'email' => 'required|email|max:255',
               'phone' => 'required|string|max:255',
               'message' => 'required|string',
               // Optional package details (only sent from package detail page)
               'package_name' => 'nullable|string|max:255',
               'price_double' => 'nullable|string|max:255',
               'price_triple' => 'nullable|string|max:255',
               'price_quad' => 'nullable|string|max:255',
               'currency' => 'nullable|string|max:255',
               'nights_makkah' => 'nullable|string|max:255',
               'nights_madina' => 'nullable|string|max:255',
               'total_nights' => 'nullable|string|max:255',
               'hotel_makkah_name' => 'nullable|string|max:255',
               'hotel_madina_name' => 'nullable|string|max:255',
               'transportation_title' => 'nullable|string|max:255',
               'visa_title' => 'nullable|string|max:255',
               'breakfast_included' => 'nullable|boolean',
               'dinner_included' => 'nullable|boolean',
               'visa_included' => 'nullable|boolean',
               'ticket_included' => 'nullable|boolean',
               'roundtrip' => 'nullable|boolean',
               'ziyarat_included' => 'nullable|boolean',
               'guide_included' => 'nullable|boolean',
           ];

           // Validate the request data
           $validatedData = $request->validate($rules);

           // Create inquiry
           $inquiry = Inquiry::create($validatedData);

           // Fire webhook (non-blocking best-effort)
           $this->postInquiryToWebhook($inquiry);

           // Return success response
           return response()->json([
               'message' => 'Inquiry created successfully',
               'inquiry' => $inquiry
           ], 201)->header('Content-Type', 'application/json');
       } catch (ValidationException $e) {
           return response()->json([
               'errors' => $e->errors(),
               'message' => 'Validation failed'
           ], 422)->header('Content-Type', 'application/json');
       } catch (\Exception $e) {
           return response()->json([
               'error' => 'Failed to create inquiry',
               'message' => $e->getMessage()
           ], 500)->header('Content-Type', 'application/json');
       }
   }


   public function showInquiry($id)
   {
       $inquiry = Inquiry::find($id);
       
       if (!$inquiry) {
           return response()->json(['error' => 'Inquiry not found'], 404);
       }
       
       return response()->json($inquiry, 200);
   }

   public function updateInquiry(Request $request, $id)
   {
       $inquiry = Inquiry::find($id);
       
       if (!$inquiry) {
           return response()->json(['error' => 'Inquiry not found'], 404);
       }
       
       // Validation rules
       $rules = [
           'name' => 'required|string',
           'email' => 'required|email',
           'phone' => 'required|string',
           'message' => 'required|string',
       ];

       // Validate the request data
       $validator = Validator::make($request->all(), $rules);

       // If validation fails, return error response
       if ($validator->fails()) {
           return response()->json(['errors' => $validator->errors()], 422);
       }

       // Update inquiry
       $inquiry->update($validator->validated());

       // Return success response
       return response()->json(['message' => 'Inquiry updated successfully', 'inquiry' => $inquiry], 200);
   }

   public function deleteInquiry($id){
       $inquiry = Inquiry::find($id);
       
       if (!$inquiry) {
           return response()->json(['error' => 'Inquiry not found'], 404);
       }
       
       $inquiry->delete();
       return response()->json(['message' => 'Inquiry deleted successfully'], 200);
   }

   public function getInquiries(){
       return response()->json(Inquiry::all(), 200);
   }

   /**
    * Securely forward an inquiry to the configured webhook (manual trigger).
    * Header X-Api-Key must match env('ADMIN_API_KEY').
    */
   public function forwardInquiryWebhook(Request $request, $id)
   {
       $apiKey = $request->header('X-Api-Key');
       $expected = env('ADMIN_API_KEY');
       if (!$expected || !hash_equals((string)$expected, (string)$apiKey)) {
           return response()->json(['error' => 'Unauthorized'], 401);
       }

       $inquiry = Inquiry::find($id);
       if (!$inquiry) {
           return response()->json(['error' => 'Inquiry not found'], 404);
       }

       $resp = $this->postInquiryToWebhook($inquiry, true);
       return response()->json([
           'success' => $resp['success'],
           'status' => $resp['status'],
           'body' => $resp['body']
       ], $resp['success'] ? 200 : 502);
   }

   /**
    * Build and send signed webhook to external portal.
    * Now includes package details if inquiry came from package detail page.
    */
   private function postInquiryToWebhook(Inquiry $inquiry, bool $returnResponse = false)
   {
       $url = env('INQUIRY_WEBHOOK_URL');
       if (!$url) {
           return $returnResponse ? ['success' => false, 'status' => null, 'body' => 'Webhook URL not configured'] : null;
       }
       $secret = env('INQUIRY_WEBHOOK_SECRET', '');

       // Base inquiry data
       $payload = [
           'id' => $inquiry->id,
           'name' => $inquiry->name,
           'email' => $inquiry->email,
           'phone' => $inquiry->phone,
           'message' => $inquiry->message,
           'created_at' => $inquiry->created_at,
       ];

       // Add package details if this inquiry came from package detail page
       if ($inquiry->package_name) {
           $payload['package_details'] = [
               'package_name' => $inquiry->package_name,
               'pricing' => [
                   'double' => $inquiry->price_double,
                   'triple' => $inquiry->price_triple,
                   'quad' => $inquiry->price_quad,
                   'currency' => $inquiry->currency ?? 'USD',
               ],
               'duration' => [
                   'nights_makkah' => $inquiry->nights_makkah,
                   'nights_madina' => $inquiry->nights_madina,
                   'total_nights' => $inquiry->total_nights,
               ],
               'hotels' => [
                   'makkah' => $inquiry->hotel_makkah_name,
                   'madina' => $inquiry->hotel_madina_name,
               ],
               'services' => [
                   'transportation' => $inquiry->transportation_title,
                   'visa' => $inquiry->visa_title,
               ],
               'inclusions' => [
                   'breakfast' => (bool)$inquiry->breakfast_included,
                   'dinner' => (bool)$inquiry->dinner_included,
                   'visa' => (bool)$inquiry->visa_included,
                   'ticket' => (bool)$inquiry->ticket_included,
                   'roundtrip' => (bool)$inquiry->roundtrip,
                   'ziyarat' => (bool)$inquiry->ziyarat_included,
                   'guide' => (bool)$inquiry->guide_included,
               ],
           ];
       }

       $timestamp = (string) time();
       $body = json_encode($payload);
       $signature = hash_hmac('sha256', $timestamp . '.' . $body, (string)$secret);
       $idempotencyKey = 'inq-' . $inquiry->id;

       try {
           $response = Http::timeout(8)
               ->withHeaders([
                   'Content-Type' => 'application/json',
                   'X-Webhook-Timestamp' => $timestamp,
                   'X-Webhook-Signature' => $signature,
                   'Idempotency-Key' => $idempotencyKey,
               ])->post($url, $payload);

           if ($returnResponse) {
               return [
                   'success' => $response->successful(),
                   'status' => $response->status(),
                   'body' => $response->body(),
               ];
           }
       } catch (\Throwable $e) {
           if ($returnResponse) {
               return [
                   'success' => false,
                   'status' => null,
                   'body' => $e->getMessage(),
               ];
           }
       }

       return null;
   }
}
