<?php

namespace Modules\Membership\app\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class FrontendMembershipController extends Controller
{


    public function user_login(Request $request)
    {
        $email_or_username = filter_var($request->username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|min:6'
        ],
            [
                'username.required' => sprintf(__('%s is required'),$email_or_username),
                'password.required' => __('password is required')
            ]);

        return Auth::guard('web')->attempt([$email_or_username => $request->username, 'password' => $request->password])
            ? response()->json(['status' => 'success','balance' => Auth::user()->user_wallet->balance ?? 0 ])
            : response()->json(['msg' => sprintf(__('Your %s or Password Is Wrong !!'),$email_or_username),'status' => 'failed']);
    }
}
