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

class WebController extends Controller
{

    public function getBlogs(){

        $blogs = Blog::all();
        $res = [];
        foreach($blogs as $b){
           $data = $b->toArray();
           $elements = BlogElement::where('blog_id',$b['id'])->get();
           $res[] = array_merge($data,['elements'=>$elements]);
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
           return response()->json(['errors' => $validator->errors()], 200);
       }

       // Create inquiry
       $inquiry = Inquiry::create($validator->validated());

       // Fire webhook (non-blocking best-effort)
       $this->postInquiryToWebhook($inquiry);

       // Return success response
       return response()->json(['message' => 'Inquiry created successfully', 'inquiry' => $inquiry], 201);
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
    */
   private function postInquiryToWebhook(Inquiry $inquiry, bool $returnResponse = false)
   {
       $url = env('INQUIRY_WEBHOOK_URL');
       if (!$url) {
           return $returnResponse ? ['success' => false, 'status' => null, 'body' => 'Webhook URL not configured'] : null;
       }
       $secret = env('INQUIRY_WEBHOOK_SECRET', '');

       $payload = [
           'id' => $inquiry->id,
           'name' => $inquiry->name,
           'email' => $inquiry->email,
           'phone' => $inquiry->phone,
           'message' => $inquiry->message,
           'created_at' => $inquiry->created_at,
       ];

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
