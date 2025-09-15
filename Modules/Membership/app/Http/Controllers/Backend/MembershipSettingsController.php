<?php

namespace Modules\Membership\app\Http\Controllers\Backend;

use App\Helpers\FlashMsg;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Membership\app\Models\Membership;

class MembershipSettingsController extends Controller
{

    public function membership_settings(Request $request)
    {
        if($request->isMethod('post')){
            $request->validate([
                'register_membership' => 'required|numeric|gt:0',
                'package_expire_notify_mail_days'=> 'required|array',
                'package_expire_notify_mail_days.*'=> 'required|max:7'
            ]);

            $all_fields = [
                'register_membership',
                'membership_get_started_button_title',
                'membership_upgrade_button_title',
                'membership_renew_button_title',
                'membership_renew_modal_title',
                'renew_button_before_expire_days',
                'current_membership_button_title',
                'current_membership_modal_title',
            ];
            foreach ($all_fields as $field) {
                update_static_option($field, $request->$field);
            }
            update_static_option('package_expire_notify_mail_days',json_encode($request->package_expire_notify_mail_days));
            return redirect()->back()->with(FlashMsg::settings_update());
        }

        $memberships = Membership::with('membership_type')->where('status',1)->get();
        return view('membership::backend.settings.membership-settings',compact('memberships'));
    }
}
