<?php

namespace Modules\Chat\app\Http\Controllers\Backend;

use App\Helpers\FlashMsg;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PusherSettingsController extends Controller
{
    public function pusher_settings(Request $request)
    {
        if($request->isMethod('post')){
            $pusher = $request->validate([
                "PUSHER_APP_ID" => "required",
                "PUSHER_APP_KEY" => "required",
                "PUSHER_APP_SECRET" => "required",
                "PUSHER_APP_CLUSTER" => "required",
            ]);

            setEnvValue($pusher);
            return back()->with(FlashMsg::item_new(__('Pusher Settings Updated Successfully.')));
        }
        return view('chat::admin.pusher-settings');
    }
}
