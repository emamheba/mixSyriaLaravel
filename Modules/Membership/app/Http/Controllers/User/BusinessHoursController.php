<?php

namespace Modules\Membership\app\Http\Controllers\User;

use App\Helpers\FlashMsg;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Modules\Membership\app\Models\BusinessHours;
use Modules\Membership\app\Models\UserMembership;

class BusinessHoursController extends Controller
{

    public function business_hours_add(Request $request){

        $request->validate([
            'days' => 'required|array',
            'opening_times' => 'required|array',
            'closing_times' => 'required|array'
        ]);

        $user_id = Auth::guard('web')->user()->id;

        // membership check
        if(moduleExists('Membership')){
            if(membershipModuleExistsAndEnable('Membership')){
                $user_membership = UserMembership::where('user_id', $user_id)->first();
                if ($user_membership){
                    if($user_membership->business_hour === 0){
                        toastr_error(__('Your Membership Package does not include the business hour feature.'));
                        return redirect()->back();
                    }
                }else{
                    toastr_error(__('Please Purchase a Membership Package'));
                    return redirect()->back();
                }

            }
        }


        $business_hours = BusinessHours::where('user_id', $user_id)->first();

        // Prepare the data to be stored in JSON format
        $data = [
            'days' => $request->days,
            'opening_times' => $request->opening_times,
            'closing_times' => $request->closing_times
        ];

        if(is_null($business_hours)){
            BusinessHours::create([
                'user_id' => $user_id,
                'day_of_week' => json_encode($data),
            ]);
        } else {
            $business_hours->update([
                'day_of_week' => json_encode($data),
            ]);
        }


        toastr_success(__('Added Success---'));
        return redirect()->back();
    }

}
