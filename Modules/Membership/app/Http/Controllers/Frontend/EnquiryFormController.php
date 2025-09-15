<?php

namespace Modules\Membership\app\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Modules\Membership\app\Models\Enquiry;

class EnquiryFormController extends Controller
{
    public function enquiry_form_submit(Request $request){
        try {
            $validatedData = $request->validate([
                'user_id' => 'required',
                'listing_id' => 'required',
                'name' => 'required',
                'email' => 'required',
                'phone' => 'required',
                'message' => 'required'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->errors(),
            ]);
        }

        $submit_form  = Enquiry::create([
            'user_id' => $request->user_id,
            'listing_id' => $request->listing_id,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'message' => $request->message
        ]);

        if($submit_form){
            return response()->json([
                'status' => 'add_success',
                'message' => __('Your message has been successfully submitted')
            ]);
        }
    }
}
