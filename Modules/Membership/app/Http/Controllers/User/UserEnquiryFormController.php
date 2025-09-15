<?php

namespace Modules\Membership\app\Http\Controllers\User;

use App\Helpers\FlashMsg;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Modules\Membership\app\Models\Enquiry;

class UserEnquiryFormController extends Controller
{

    public function all_enquiries()
    {
        $user_id = Auth::guard('web')->user()->id;
        $all_enquiries = Enquiry::with('listing')->latest()->where('user_id', $user_id)->paginate(10);
        return view('membership::frontend.user.enquiry.all-enquiry',compact('all_enquiries'));
    }

    // pagination
    function pagination(Request $request)
    {
        if($request->ajax()){
            $user_id = Auth::guard('web')->user()->id;
            $all_enquiries = $request->search_tring == ''
                ? Enquiry::with('listing')->where('user_id',$user_id)->latest()->paginate(10)
                : Enquiry::with('listing')->where('user_id',$user_id)->where('created_at', 'LIKE', "%". strip_tags($request->string_search) ."%")->paginate(10);
            return view('membership::frontend.user.enquiry.search-result', compact('all_enquiries'))->render();
        }
    }

    // search category
    public function search_history(Request $request)
    {
        $all_enquiries = Enquiry::with('listing')->where('user_id',Auth::guard('web')->user()->id)->where('created_at', 'LIKE', "%". strip_tags($request->string_search) ."%")->paginate(10);
        return $all_enquiries->total() >= 1 ? view('membership::frontend.user.enquiry.search-result', compact('all_enquiries'))->render() : response()->json(['status'=>__('nothing')]);
    }

    // delete enquiry
    public function delete_enquiry($id)
    {
        $enquiry = Enquiry::find($id);
        if($enquiry){
            $enquiry->delete();
            toastr_error(__('Enquiry Successfully Deleted'));
            return redirect()->back();
        }else{
            toastr_error(__('Enquiry is not deletable'));
            return redirect()->back();
        }
    }

}
