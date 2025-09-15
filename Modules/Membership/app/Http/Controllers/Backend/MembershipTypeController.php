<?php

namespace Modules\Membership\app\Http\Controllers\Backend;

use App\Helpers\FlashMsg;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Membership\app\Models\MembershipType;

class MembershipTypeController extends Controller
{
    public function all_type(Request $request)
    {
        if($request->isMethod('post')){
            $request->validate([
                'type'=> 'required|unique:membership_types|max:191',
                'validity'=> 'required|integer|between:7,365'
            ],
                [
                    'validity.between' => __('Validity must be a number between 7 to 365 days'),
                ]);
            MembershipType::create([
                'type' => $request->type,
                'validity' => $request->validity,
            ]);

            return back()->with(FlashMsg::item_new(__('New Type Successfully Added')));
        }

        $all_types = MembershipType::latest()->paginate(10);
        return view('membership::backend.type.all-type',compact('all_types'));
    }

    public function edit_type(Request $request)
    {
        $request->validate([
            'type'=> 'required||max:191|unique:membership_types,type,'.$request->type_id,
            'validity'=> 'required|integer|between:7,365'
        ],
            [
                'validity.between' => __('Validity must be a number between 7 to 365 days'),
            ]);

        MembershipType::where('id',$request->type_id)->update([
            'type' => $request->type,
            'validity' => $request->validity,
        ]);
        FlashMsg::item_new(__('Type Successfully Updated'));
        return back();
    }

    // search membership
    public function search_type(Request $request)
    {
        $all_types = MembershipType::where('type', 'LIKE', "%". strip_tags($request->string_search) ."%")->paginate(10);
        return $all_types->total() >= 1 ? view('membership::backend.type.search-result', compact('all_types'))->render() : response()->json(['status'=>__('nothing')]);
    }

    // pagination
    function pagination(Request $request)
    {
        if($request->ajax()){
            $all_types =  $request->string_search == '' ? MembershipType::latest()->paginate(10) : MembershipType::where('type', 'LIKE', "%". strip_tags($request->string_search) ."%")->paginate(10);
            return view('membership::backend.type.search-result', compact('all_types'))->render();
        }
    }

    // delete membership type
    public function delete_type($id)
    {
        $type = MembershipType::find($id);
        $type_memberships = $type->memberships?->count();
        if($type_memberships == 0){
            $type->delete();
            return back()->with(FlashMsg::error(__('Type Successfully Deleted')));
        }else{
            return back()->with(FlashMsg::error(__('Type is not deletable because it is related to other memberships')));
        }
    }

    // bulk action membership type
    public function bulk_action_type(Request $request){
        foreach($request->ids as $type_id){
            $type = MembershipType::find($type_id);
            $type_memberships = $type->memberships?->count();
            if($type_memberships == 0){
                $type->delete();
            }
        }
        return back()->with(FlashMsg::item_new(__('Selected Type Successfully Deleted')));
    }
}
