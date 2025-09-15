<?php

namespace Modules\Notification\App\Http\Middleware;

use App\Models\User;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class CheckUserActiveMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $expiresAt = Carbon::now()->addMinutes(1); // keep online for 1 min
            Cache::put('user_is_online_' . Auth::user()->id, true, $expiresAt);
            User::where('id', Auth::user()->id)->update(['last_seen_at' => now()]);
        }

        return $next($request);
    }
}