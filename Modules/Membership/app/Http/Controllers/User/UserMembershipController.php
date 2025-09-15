<?php

namespace Modules\Membership\app\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Modules\Membership\app\Models\MembershipHistory;
use Modules\Membership\app\Models\UserMembership;

class UserMembershipController extends Controller
{
    public function all_membership()
    {
        $user_id = Auth::guard('web')->user()->id;

        $all_memberships = MembershipHistory::with('membership:id,membership_type_id')->latest()
            ->where('user_id',$user_id)
            ->paginate(10);

        $user_membership = UserMembership::where('user_id',$user_id)->first();

        $total_listings_limit = UserMembership::where('user_id',$user_id)
            ->where('payment_status','complete')
            ->whereDate('expire_date', '>', Carbon::now())
            ->sum('listing_limit');

        return view('membership::frontend.user.membership.membership',compact(
            'all_memberships',
            'total_listings_limit',
            'user_membership'
        ));
    }

    // pagination
    function pagination(Request $request)
    {
        if($request->ajax()){
            $user_id = Auth::guard('web')->user()->id;
            $all_memberships = $request->search_tring == ''
                ? UserMembership::where('user_id',$user_id)->latest()->paginate(2)
                : UserMembership::where('user_id',$user_id)->where('created_at', 'LIKE', "%". strip_tags($request->string_search) ."%")->paginate(10);
            return view('membership::frontend.user.membership.search-result', compact('all_memberships'))->render();
        }
    }

    // search category
    public function search_history(Request $request)
    {
        $all_memberships = UserMembership::where('user_id',Auth::guard('web')->user()->id)->where('created_at', 'LIKE', "%". strip_tags($request->string_search) ."%")->paginate(10);
        return $all_memberships->total() >= 1 ? view('membership::frontend.user.membership.search-result', compact('all_memberships'))->render() : response()->json(['status'=>__('nothing')]);
    }
}
