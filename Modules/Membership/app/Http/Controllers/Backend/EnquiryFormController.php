<?php

namespace Modules\Membership\app\Http\Controllers\Backend;

use App\Helpers\FlashMsg;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Membership\app\Models\Enquiry;

class EnquiryFormController extends Controller
{
    public function all_enquiry(Request $request)
    {
        $all_enquiries = Enquiry::latest()->paginate(10);
        return view('membership::backend.enquiry.all-enquiry',compact('all_enquiries'));
    }

    // search
    public function search_enquiry(Request $request)
    {
        $all_enquiries = Enquiry::where('name', 'LIKE', "%". strip_tags($request->string_search) ."%")->paginate(10);
        return $all_enquiries->total() >= 1 ? view('membership::backend.enquiry.search-result', compact('all_enquiries'))->render() : response()->json(['status'=>__('nothing')]);
    }

    // pagination
    function pagination(Request $request)
    {
        if($request->ajax()){
            $all_enquiries =  $request->string_search == '' ? Enquiry::latest()->paginate(10) : Enquiry::where('name', 'LIKE', "%". strip_tags($request->string_search) ."%")->paginate(10);
            return view('membership::backend.enquiry.search-result', compact('all_enquiries'))->render();
        }
    }

    // delete enquiry
    public function delete_enquiry($id)
    {
        $enquiry = Enquiry::find($id);
        if($enquiry){
            $enquiry->delete();
            return back()->with(FlashMsg::error(__('Enquiry Successfully Deleted')));
        }else{
            return back()->with(FlashMsg::error(__('Enquiry is not deletable')));
        }
    }

    // bulk action enquiry
    public function bulk_action_enquiry(Request $request){
        foreach($request->ids as $enquiry_id){
            $enquiry = Enquiry::find($enquiry_id);
            if($enquiry){
                $enquiry->delete();
            }
        }
        return back()->with(FlashMsg::item_new(__('Selected Enquiry Successfully Deleted')));
    }
}
