<?php

namespace Modules\Membership\app\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Membership\app\Models\MembershipHistory;
use Modules\Membership\app\Models\UserMembership;

class MembershipHistoryController extends Controller
{
    public function user_membership_history($user_id)
    {
        $user_memberships_history = MembershipHistory::where('user_id', $user_id)->latest()->paginate(10);
        $user_info = User::find($user_id);
        $route = route("admin.user.membership.history.paginate.data");
        return view('membership::backend.user-membership.history.user-membership-history',compact(['user_memberships_history','user_info','route']));
    }

    // pagination
    function pagination(Request $request)
    {
        if($request->ajax()){
            $user_memberships_history = $request->string_search == ''
                ? MembershipHistory::latest()->paginate(10)
                : MembershipHistory::latest()->$this->query__($request);

            $route = route("admin.user.membership.history.paginate.data");

            return view('membership::backend.user-membership.history.search-result', compact('user_memberships_history', 'route'))->render();
        }
    }

    // search string
    public function search_membership(Request $request)
    {
        $query = MembershipHistory::latest();
        if($request->filter_val != ''){
            if($request->filter_val == 1){
                $query->where('status',1);
            }
            if($request->filter_val == 0){
                $query->where('status',0);
            }
            if($request->filter_val == 'manual_payment'){
                $query->where('payment_gateway','manual_payment');
            }
        }

        $user_memberships_history = $query->where(function($q) use($request){
            $q->where('id', 'LIKE', "%". strip_tags($request->string_search) ."%")
                ->orWhere('user_id', 'LIKE', "%". strip_tags($request->string_search) ."%")
                ->orWhere('created_at', 'LIKE', "%". strip_tags($request->string_search) ."%")
                ->orWhere('expire_date', 'LIKE', "%". strip_tags($request->string_search) ."%");
        })->paginate(10);

        $route = route("admin.user.membership.search");

        return $user_memberships_history->total() >= 1 ? view('membership::backend.user-membership.history.search-result', compact('user_memberships_history', 'route'))->render() : response()->json(['status'=>__('nothing')]);
    }
}
