<?php

namespace Modules\Membership\app\Http\Controllers\Backend;

use App\Helpers\FlashMsg;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Modules\Membership\app\Models\Membership;
use Modules\Membership\app\Models\MembershipFeature;
use Modules\Membership\app\Models\MembershipType;

class MembershipController extends Controller
{
    public function all_membership()
    {
        $all_memberships = Membership::with('membership_type')->latest()->paginate(10);
        return view('membership::backend.membership.all-membership',compact('all_memberships'));
    }

    public function add_membership(Request $request)
    {
        if($request->isMethod('post')){
            $request->validate([
                'type'=> 'required',
                'title'=> ['required',Rule::unique('memberships')->where(fn ($query) => $query->where('membership_type_id', request()->type)),'max:191'],
                'price'=> 'required',
                'listing_limit'=> 'required|gt:0',
                'feature'=> 'required|array',
                'status'=> 'nullable|array',
            ]);

            DB::beginTransaction();

            $enquiry_form = isset($request->enquiry_form) ? 1 : 0;
            $business_hour = isset($request->business_hour) ? 1 : 0;
            $membership_badge = isset($request->membership_badge) ? 1 : 0;

            try {
                $subscription = Membership::create([
                    'membership_type_id' => $request->type,
                    'title' => $request->title,
                    'price' => $request->price,
                    'image' => $request->image ?? '',
                    'listing_limit' => $request->listing_limit,
                    'gallery_images' => $request->gallery_images,
                    'featured_listing' => $request->featured_listing ?? 0,
                    'enquiry_form' => $enquiry_form,
                    'business_hour' => $business_hour,
                    'membership_badge' => $membership_badge,
                    'status' => 1,
                ]);

                $arr = [];
                foreach($request->feature as $key => $attr) {
                    $arr[] = [
                        'membership_id' => $subscription->id,
                        'feature' => $request->feature[$key] ?? '',
                        'status' => $request->status[$key] ?? 'off',
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ];
                }
                $data = Validator::make($arr,["*.feature" => "required"]);
                $data->validated();
                MembershipFeature::insert($arr);
                DB::commit();
                return back()->with(FlashMsg::item_new(__('New Subscription Successfully Added')));
            }catch(Exception $e){

            }
        }

        $all_types = MembershipType::all_types();
        return view('membership::backend.membership.add-membership',compact('all_types'));
    }

    public function edit_membership(Request $request,$id)
    {
        if($request->isMethod('post')){
            $request->validate([
                'title'=> ['required',Rule::unique('memberships')->where(fn ($query) => $query->where('membership_type_id', request()->type))->ignore($id),'max:191'],
                'type'=> 'required',
                'price'=> 'required',
                'listing_limit'=> 'required|gt:0',
                'feature'=> 'required|array',
                'status'=> 'nullable|array'
            ],[
                'title.unique'=> __('Title already exists for this membership type')
            ]);

            DB::beginTransaction();

            $enquiry_form = isset($request->enquiry_form) ? 1 : 0;
            $business_hour = isset($request->business_hour) ? 1 : 0;
            $membership_badge = isset($request->membership_badge) ? 1 : 0;

            try {
                Membership::where('id',$id)->update([
                    'membership_type_id' => $request->type,
                    'title' => $request->title,
                    'price' => $request->price,
                    'image' => $request->image ?? '',
                    'listing_limit' => $request->listing_limit,
                    'gallery_images' => $request->gallery_images,
                    'featured_listing' => $request->featured_listing ?? 0,
                    'enquiry_form' => $enquiry_form,
                    'business_hour' => $business_hour,
                    'membership_badge' => $membership_badge,
                ]);

                MembershipFeature::where('membership_id',$id)->delete();

                $arr = [];
                foreach($request->feature as $key => $attr) {
                    $arr[] = [
                        'membership_id' => $id,
                        'feature' => $request->feature[$key] ?? '',
                        'status' => $request->status[$key] ?? 'off',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
                }
                $data = Validator::make($arr,["*.feature" => "required"]);
                $data->validated();
                MembershipFeature::insert($arr);
                DB::commit();
                return back()->with(FlashMsg::item_new(__('Subscription Successfully Updated')));
            }catch(Exception $e){}
        }
        $all_types = MembershipType::all_types();
        $membership_details = Membership::with('features')->where('id',$id)->first();
        return !empty($membership_details) ?  view('membership::backend.membership.edit-membership',compact('all_types','membership_details')) : back();
    }


    // search category
    public function search_membership(Request $request)
    {
        $all_memberships = Membership::whereHas('membership_type', function ($query) use ($request){
            $query->where('type', 'LIKE', "%". strip_tags($request->string_search) ."%");
        })
            ->with(['membership_type' => function($query) use ($request){
                $query->where('type', 'LIKE', "%". strip_tags($request->string_search) ."%");
            }])
            ->paginate(10);
        return $all_memberships->total() >= 1 ? view('membership::backend.membership.search-result', compact('all_memberships'))->render() : response()->json(['status'=>__('nothing')]);
    }

    // pagination
    function pagination(Request $request)
    {
        if($request->ajax()){
            $request->string_search == ''
                ? $all_memberships = Membership::with('membership_type')->latest()->paginate(10)
                : $all_memberships = Membership::whereHas('membership_type', function ($query) use ($request){
                $query->where('type', 'LIKE', "%". strip_tags($request->string_search) ."%");
            })
                ->with(['memberships_type' => function($query) use ($request){
                    $query->where('type', 'LIKE', "%". strip_tags($request->string_search) ."%");
                }])
                ->paginate(10);
            return view('membership::backend.membership.search-result', compact('all_memberships'))->render();
        }
    }

    // delete membership
    public function delete_membership($id)
    {
        $membership = Membership::find($id);
        $membership_users = $membership->user_memberships?->count();
        if($membership_users == 0){
            $membership->delete();
            return back()->with(FlashMsg::error(__('Membership Successfully Deleted')));
        }else{
            return back()->with(FlashMsg::error(__('Membership is not deletable because it is related to user Memberships')));
        }
    }

    // bulk action membership
    public function bulk_action_membership(Request $request){
        foreach($request->ids as $membership_id){
            $membership = Membership::find($membership_id);
            $membership_users = $membership->user_memberships?->count();
            if($membership_users == 0){
                $membership->delete();
            }
        }
        return back()->with(FlashMsg::error(__('Selected Memberships Successfully Deleted')));
    }

    // change membership status
    public function status($id)
    {
        $membership = Membership::select('status')->where('id',$id)->first();
        $membership->status == 1 ? $status=0 : $status=1;
        Membership::where('id',$id)->update(['status'=>$status]);
        return redirect()->back()->with(FlashMsg::error(__('Status Successfully Changed')));
    }
}
