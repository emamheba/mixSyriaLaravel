<?php

namespace Modules\Chat\app\Http\Middleware;

use App\Models\User;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Square\Models\Vendor;


class CheckVendorActiveMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::guard("web")->check()) {
            $expiresAt = Carbon::now()->addMinutes(1); // keep online for 1 min
            Cache::put('vendor_is_online_' . Auth::guard("web")->user()->id(), true, $expiresAt);
            User::where('id', Auth::guard("web")->user()->id())->update(['check_online_status' => (new \DateTime())->format("Y-m-d H:i:s")]);
        }

        return $next($request);
    }
}
