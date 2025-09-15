<?php

namespace Modules\Membership\app\Http\Controllers\Backend;

use App\Helpers\FlashMsg;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MembershipEmailTemplateController extends Controller
{
    const BASE_PATH_SUBSCRIPTION = 'membership::backend.email-template.memberships.';
    public function userMembershipFreeTemplate(Request $request)
    {
        if($request->isMethod('post')){
            $request->validate([
                'user_membership_free_email_subject'=>'required|min:5|max:1000',
                'user_membership_free_message'=>'required|min:10|max:1000',
                'user_membership_free_message_for_admin'=>'required|min:10',
            ]);
            $fields = [
                'user_membership_free_email_subject',
                'user_membership_free_message',
                'user_membership_free_message_for_admin',
            ];
            foreach ($fields as $field) {
                update_static_option($field, $request->$field);
            }
            return redirect()->back()->with(FlashMsg::item_new(__('Update Success')));
        }
        return view(self::BASE_PATH_SUBSCRIPTION.'user-membership-free');
    }

    public function userMembershipPurchaseTemplate(Request $request)
    {
        if($request->isMethod('post')){
            $request->validate([
                'user_membership_purchase_email_subject'=>'required|min:5|max:1000',
                'user_membership_purchase_message'=>'required|min:10|max:1000',
                'user_membership_purchase_message_for_admin'=>'required|min:10',
            ]);
            $fields = [
                'user_membership_purchase_email_subject',
                'user_membership_purchase_message',
                'user_membership_purchase_message_for_admin',
            ];
            foreach ($fields as $field) {
                update_static_option($field, $request->$field);
            }
            return redirect()->back()->with(FlashMsg::item_new(__('Update Success')));

        }
        return view(self::BASE_PATH_SUBSCRIPTION.'user-membership-purchase');
    }

    public function userMembershipRenewTemplate(Request $request)
    {
        if($request->isMethod('post')){
            $request->validate([
                'user_membership_renew_email_subject'=>'required|min:5|max:1000',
                'user_membership_renew_message'=>'required|min:10|max:1000',
                'user_membership_renew_message_for_admin'=>'required|min:10',
            ]);
            $fields = [
                'user_membership_renew_email_subject',
                'user_membership_renew_message',
                'user_membership_renew_message_for_admin',
            ];
            foreach ($fields as $field) {
                update_static_option($field, $request->$field);
            }
            return redirect()->back()->with(FlashMsg::item_new(__('Update Success')));

        }
        return view(self::BASE_PATH_SUBSCRIPTION.'user-membership-renew');
    }

    public function userMembershipActiveTemplate(Request $request)
    {
        if($request->isMethod('post')){
            $request->validate([
                'user_membership_active_email_subject'=>'required|min:5|max:1000',
                'user_membership_active_message'=>'required|min:10|max:1000',
            ]);
            $fields = [
                'user_membership_active_email_subject',
                'user_membership_active_message',
            ];
            foreach ($fields as $field) {
                update_static_option($field, $request->$field);
            }
            return redirect()->back()->with(FlashMsg::item_new(__('Update Success')));

        }
        return view(self::BASE_PATH_SUBSCRIPTION.'user-membership-active');
    }

    public function userMembershipInactiveTemplate(Request $request)
    {
        if($request->isMethod('post')){
            $request->validate([
                'user_membership_inactive_email_subject'=>'required|min:5|max:1000',
                'user_membership_inactive_message'=>'required|min:10|max:1000',
            ]);
            $fields = [
                'user_membership_inactive_email_subject',
                'user_membership_inactive_message',
            ];
            foreach ($fields as $field) {
                update_static_option($field, $request->$field);
            }
            return redirect()->back()->with(FlashMsg::item_new(__('Update Success')));

        }
        return view(self::BASE_PATH_SUBSCRIPTION.'user-membership-inactive');
    }

    public function userMembershipManualPaymentCompleteTemplate(Request $request)
    {
        if($request->isMethod('post')){
            $request->validate([
                'user_membership_manual_payment_complete_email_subject'=>'required|min:5|max:1000',
                'user_membership_manual_payment_complete_message'=>'required|min:10|max:1000',
            ]);
            $fields = [
                'user_membership_manual_payment_complete_email_subject',
                'user_membership_manual_payment_complete_message',
            ];
            foreach ($fields as $field) {
                update_static_option($field, $request->$field);
            }
            return redirect()->back()->with(FlashMsg::item_new(__('Update Success')));

        }
        return view(self::BASE_PATH_SUBSCRIPTION.'user-membership-manual-payment-complete');
    }

    public function userMembershipManualPaymentCompleteToAdminTemplate(Request $request)
    {
        if($request->isMethod('post')){
            $request->validate([
                'user_membership_manual_payment_complete_to_admin_email_subject'=>'required|min:5|max:1000',
                'user_membership_manual_payment_complete_to_admin_message'=>'required|min:10|max:1000',
            ]);
            $fields = [
                'user_membership_manual_payment_complete_to_admin_email_subject',
                'user_membership_manual_payment_complete_to_admin_message',
            ];
            foreach ($fields as $field) {
                update_static_option($field, $request->$field);
            }
            return redirect()->back()->with(FlashMsg::item_new(__('Update Success')));

        }
        return view(self::BASE_PATH_SUBSCRIPTION.'user-membership-manual-payment-complete-to-admin');
    }
}
