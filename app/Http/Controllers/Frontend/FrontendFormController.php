<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Mail\ContactMessage;
use App\Mail\CustomFormBuilderMail;
use App\Models\Backend\FormBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class FrontendFormController extends Controller
{
    public function send_contact_message(Request $request)
    {
        $validated_data = $this->get_filtered_data_from_request(get_static_option('contact_page_contact_form_fields'), $request);
        $all_attachment = $validated_data['all_attachment'];
        $all_field_serialize_data = $validated_data['field_data'];
        $success_message = !empty($succ_msg) ? $succ_msg : __('Thanks for your contact !!');

        try{
            Mail::to(get_static_option('site_global_email'))->send(new ContactMessage($all_field_serialize_data, $all_attachment,
                __('You Have Contact Message from') . ' ' . get_static_option('site_' . get_default_language() . '_title')));
        }catch(\Exception $e){
            return response()->json([
                'msg' => $e->getMessage(),
                'type' => 'danger'
            ]);
        }

        return response()->json([
            'msg' => $success_message,
            'type' => 'success'
        ]);
    }


    public function get_filtered_data_from_request($option_value, $request)
    {

        $all_attachment = [];
        $all_quote_form_fields = (array) json_decode($option_value);
        $all_field_type = isset($all_quote_form_fields['field_type']) ? (array) $all_quote_form_fields['field_type'] : [];
        $all_field_name = isset($all_quote_form_fields['field_name']) ? $all_quote_form_fields['field_name'] : [];
        $all_field_required = isset($all_quote_form_fields['field_required'])  ? (object) $all_quote_form_fields['field_required'] : [];
        $all_field_mimes_type = isset($all_quote_form_fields['mimes_type']) ? (object) $all_quote_form_fields['mimes_type'] : [];
        //get field details from, form request
        $all_field_serialize_data = $request->all();
        unset($all_field_serialize_data['_token']);
        if (!empty($all_field_name)) {
            foreach ($all_field_name as $index => $field) {
                $is_required = !empty($all_field_required) && property_exists($all_field_required, $index) ? $all_field_required->$index : '';
                $mime_type = !empty($all_field_mimes_type) && property_exists($all_field_mimes_type, $index) ? $all_field_mimes_type->$index : '';
                $field_type = isset($all_field_type[$index]) ? $all_field_type[$index] : '';
                if (!empty($field_type) && $field_type == 'file') {
                    unset($all_field_serialize_data[$field]);
                }
                $validation_rules = !empty($is_required) ? 'required|' : '';
                $validation_rules .= !empty($mime_type) ? $mime_type : '';
                //validate field
                $this->validate($request, [
                    $field => $validation_rules
                ]);

                if ($field_type == 'file' && $request->hasFile($field)) {
                    $filed_instance = $request->file($field);
                    $file_extenstion = $filed_instance->getClientOriginalExtension();
                    $attachment_name = 'attachment-' . Str::random(32) . '-' . $field . '.' . $file_extenstion;

                    // Image scan start
                    $uploaded_file = $filed_instance;
                    $file_extension = $uploaded_file->getClientOriginalExtension();
                    if (in_array($file_extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                        $processed_image = Image::make($uploaded_file);
                        $image_default_width = $processed_image->width();
                        $image_default_height = $processed_image->height();
                        $processed_image->resize($image_default_width, $image_default_height, function ($constraint) {
                            $constraint->aspectRatio();
                        });
                        $processed_image->save('assets/uploads/attachment/applicant/' . $attachment_name);
                    }else{
                        $filed_instance->move('assets/uploads/attachment/applicant', $attachment_name);
                    } // Image scan end

                    $all_attachment[$field] = 'assets/uploads/attachment/applicant/' . $attachment_name;
                }
            }
        }
        return [
            'all_attachment' => $all_attachment,
            'field_data' => $all_field_serialize_data
        ];
    }


    public function custom_form_builder_message(Request $request)
    {
        $this->validate($request, [
            "custom_form_id" => 'required'
        ]);

        $google_captcha_result = google_captcha_check($request->gcaptcha_token);

        $field_details = FormBuilder::find($request->custom_form_id);
        unset($request['custom_form_id']);
        $validated_data = $this->get_filtered_data_from_request($field_details->fields, $request, false);
        $all_attachment = $validated_data['all_attachment'];
        $all_field_serialize_data = $validated_data['field_data'];

        try {
            Mail::to($field_details->email)->send(new CustomFormBuilderMail([
                    'data' => [
                        'all_fields' => $all_field_serialize_data,
                        'attachments' => $all_attachment,
                    ],
                    'form_title' => $field_details->title,
                    'subject' => sprintf(__('You Have Message from').'%s', $field_details->title) . ' ' . get_static_option('site_title')
                ])
            );
        } catch (\Exception $e) {
            toastr_warning($e->getMessage());
            return redirect()->back();

        }
        toastr_success($field_details->success_message);
        return redirect()->back();
    }
}
