<?php

namespace Modules\CountryManage\app\Http\Controllers;

use App\Helpers\FlashMsg;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Modules\CountryManage\app\Models\City;
use Modules\CountryManage\app\Models\State;
use Modules\CountryManage\app\Models\District; 

class LocationController extends Controller
{
    private $default_country_id = 1;

    
    public function all_state(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'state' => 'required|unique:states|max:191',
                'timezone' => 'nullable',
            ]);
            
            State::create([
                'state' => $request->state,
                'country_id' => $this->default_country_id, 
                'timezone' => $request->timezone,
                'status' => $request->status ?? 1,
            ]);
            
            FlashMsg::item_new(__('New State Successfully Added'));
        }
        
        $all_states = State::withCount('cities')->where('country_id', $this->default_country_id)
                          ->latest()
                          ->paginate(10);
                          
        return view('countrymanage::state.all-state', compact('all_states'));
    }

    public function edit_state(Request $request)
    {
        $request->validate([
            'edit_state' => 'required|max:191|unique:states,state,' . $request->state_id,
            'edit_timezone' => 'nullable',
        ]);
        
        State::where('id', $request->state_id)->update([
            'state' => $request->edit_state,
            'country_id' => $this->default_country_id, 
            'timezone' => $request->edit_timezone,
        ]);
        
        return redirect()->back()->with(FlashMsg::item_new(__('State Successfully Updated')));
    }

    public function change_status_state($id)
    {
        $state = State::where('id', $id)
                     ->where('country_id', $this->default_country_id)
                     ->select('status')
                     ->first();
                     
        if ($state) {
            $status = $state->status == 1 ? 0 : 1;
            State::where('id', $id)->update(['status' => $status]);
            return redirect()->back()->with(FlashMsg::item_new(__('Status Successfully Changed')));
        }
        
        return redirect()->back()->with(FlashMsg::item_delete(__('State Not Found')));
    }

    public function delete_state($id)
    {
        $state = State::where('id', $id)
                      ->where('country_id', $this->default_country_id)
                      ->first();
        if ($state) {
        
            $state->delete();
            return redirect()->back()->with(FlashMsg::item_delete(__('State Successfully Deleted')));
        }
        return redirect()->back()->with(FlashMsg::item_delete(__('State Not Found')));
    }

    public function bulk_action_state(Request $request)
    {
        $action = $request->action;
        $ids = $request->ids;
        
        if ($action == 'delete') {
            State::whereIn('id', $ids)
                 ->where('country_id', $this->default_country_id)
                 ->delete();
            return redirect()->back()->with(FlashMsg::item_delete(__('Selected States Successfully Deleted')));
        } elseif ($action == 'activate') {
            State::whereIn('id', $ids)
                 ->where('country_id', $this->default_country_id)
                 ->update(['status' => 1]);
            return redirect()->back()->with(FlashMsg::item_new(__('Selected States Successfully Activated')));
        } elseif ($action == 'deactivate') {
            State::whereIn('id', $ids)
                 ->where('country_id', $this->default_country_id)
                 ->update(['status' => 0]);
            return redirect()->back()->with(FlashMsg::item_new(__('Selected States Successfully Deactivated')));
        }
        
        return redirect()->back();
    }

    public function search_state(Request $request)
    {
        $all_states = State::withCount('cities')->where('country_id', $this->default_country_id)
                          ->where('state', 'LIKE', "%" . strip_tags($request->string_search) . "%")
                          ->paginate(10);
                          
        if ($all_states->total() >= 1) {
            return view('countrymanage::state.search-result', compact('all_states'))->render();
        } else {
            return response()->json(['status' => __('nothing')]);
        }
    }


    // ================= City Management =================
    public function all_city(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'state' => 'required',
                'city' => 'required|unique:cities|max:191',
            ]);
            
            $state = State::where('id', $request->state)
                         ->where('country_id', $this->default_country_id)
                         ->first();
                         
            if (!$state) {
                return redirect()->back()->with(FlashMsg::item_delete(__('Invalid State Selected')));
            }
            
            City::create([
                'city' => $request->city,
                'country_id' => $this->default_country_id,
                'state_id' => $request->state,
                'status' => $request->status ?? 1,
            ]);
            
            FlashMsg::item_new(__('New City Successfully Added'));
        }
        
        $all_states = State::where('country_id', $this->default_country_id)
                          ->where('status', 1)
                          ->get();
                          
        $all_cities = City::with(['state','districts'])->withCount('districts')
                         ->where('country_id', $this->default_country_id)
                         ->latest()
                         ->paginate(10);
                         
        return view('countrymanage::city.all-city', compact('all_states', 'all_cities'));
    }

    public function edit_city(Request $request)
    {
        $request->validate([
            'city' => 'required|max:191|unique:cities,city,' . $request->city_id,
            'state' => 'required',
        ]);
        
        $state = State::where('id', $request->state)
                     ->where('country_id', $this->default_country_id)
                     ->first();
                     
        if (!$state) {
            return redirect()->back()->with(FlashMsg::item_delete(__('Invalid State Selected')));
        }
        
        City::where('id', $request->city_id)->update([
            'city' => $request->city,
            'state_id' => $request->state,
            'country_id' => $this->default_country_id, 
        ]);
        
        return redirect()->back()->with(FlashMsg::item_new(__('City Successfully Updated')));
    }

    public function city_status($id)
    {
        $city = City::where('id', $id)
                   ->where('country_id', $this->default_country_id)
                   ->select('status')
                   ->first();
                   
        if ($city) {
            $status = $city->status == 1 ? 0 : 1;
            City::where('id', $id)->update(['status' => $status]);
            return redirect()->back()->with(FlashMsg::item_new(__('Status Successfully Changed')));
        }
        
        return redirect()->back()->with(FlashMsg::item_delete(__('City Not Found')));
    }

    public function delete_city($id)
    {
        City::where('id', $id)
            ->where('country_id', $this->default_country_id)
            ->delete();
            
        return redirect()->back()->with(FlashMsg::item_delete(__('City Successfully Deleted')));
    }

    public function bulk_action_city(Request $request)
    {
        $action = $request->action;
        $ids = $request->ids;
        
        if ($action == 'delete') {
            City::whereIn('id', $ids)
                ->where('country_id', $this->default_country_id)
                ->delete();
            return redirect()->back()->with(FlashMsg::item_delete(__('Selected Cities Successfully Deleted')));
        } elseif ($action == 'activate') {
            City::whereIn('id', $ids)
                ->where('country_id', $this->default_country_id)
                ->update(['status' => 1]);
            return redirect()->back()->with(FlashMsg::item_new(__('Selected Cities Successfully Activated')));
        } elseif ($action == 'deactivate') {
            City::whereIn('id', $ids)
                ->where('country_id', $this->default_country_id)
                ->update(['status' => 0]);
            return redirect()->back()->with(FlashMsg::item_new(__('Selected Cities Successfully Deactivated')));
        }
        
        return redirect()->back();
    }

    public function search_city(Request $request)
    {
        $all_cities = City::with(['state','districts'])->withCount('districts')
                         ->where('country_id', $this->default_country_id)
                         ->where('city', 'LIKE', "%" . strip_tags($request->string_search) . "%")
                         ->paginate(10);
                         
        if ($all_cities->total() >= 1) {
            return view('countrymanage::city.search-result', compact('all_cities'))->render();
        } else {
            return response()->json(['status' => __('nothing')]);
        }
    }

    // public function state_pagination(Request $request)
    // {
    //     if ($request->ajax()) {
    //         $all_states = State::where('country_id', $this->default_country_id)
    //                           ->latest()
    //                           ->paginate(10);
    //         return view('countrymanage::state.search-result', compact('all_states'))->render();
    //     }
    // }

    // public function city_pagination(Request $request)
    // {
    //     if ($request->ajax()) {
    //         $all_cities = City::with('state')
    //                          ->where('country_id', $this->default_country_id)
    //                          ->latest()
    //                          ->paginate(10);
    //         return view('countrymanage::city.search-result', compact('all_cities'))->render();
    //     }
    // }

    
    // DISTRICT MANAGEMENT ---
    
  public function all_district(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'state_id' => 'required',
                'city_id' => 'required',
                'district' => 'required|unique:districts|max:191',
            ]);

            District::create([
                'district' => $request->district,
                'city_id' => $request->city_id,
                'state_id' => $request->state_id,
                'country_id' => $this->default_country_id,
                'status' => $request->status ?? 1,
            ]);

            return redirect()->back()->with(FlashMsg::item_new(__('New District Successfully Added')));
        }

        $districts_query = District::where('country_id', $this->default_country_id);
        $stats = [
            'total' => $districts_query->count(),
            'active' => (clone $districts_query)->where('status', 1)->count(),
            'inactive' => (clone $districts_query)->where('status', 0)->count(),
            'total_cities' => City::where(['country_id' => $this->default_country_id, 'status' => 1])->count(),
        ];

        $all_states = State::where(['country_id' => $this->default_country_id, 'status' => 1])->get();

        $all_districts = $districts_query->with(['city', 'state'])
                                 ->latest()
                                 ->paginate(10);

        return view('countrymanage::district.all-district', compact('all_states', 'all_districts', 'stats'));
    }
    
    public function edit_district(Request $request)
    {
        $request->validate([
            'district' => 'required|max:191|unique:districts,district,' . $request->district_id,
            'city_id' => 'required',
            'state_id' => 'required',
        ]);
        
        District::where('id', $request->district_id)->update([
            'district' => $request->district,
            'city_id' => $request->city_id,
            'state_id' => $request->state_id,
            'country_id' => $this->default_country_id,
        ]);
        
        return redirect()->back()->with(FlashMsg::item_new(__('District Successfully Updated')));
    }
    
    public function district_status($id)
    {
        $district = District::find($id);
        if ($district) {
            $status = $district->status == 1 ? 0 : 1;
            $district->update(['status' => $status]);
            return redirect()->back()->with(FlashMsg::item_new(__('Status Successfully Changed')));
        }
        return redirect()->back()->with(FlashMsg::item_delete(__('District Not Found')));
    }

    public function delete_district($id)
    {
        District::where('id', $id)->delete();
        return redirect()->back()->with(FlashMsg::item_delete(__('District Successfully Deleted')));
    }

    public function bulk_action_district(Request $request)
    {
        $action = $request->action;
        $ids = $request->ids;

        if ($action == 'delete') {
            District::whereIn('id', $ids)->delete();
            return redirect()->back()->with(FlashMsg::item_delete(__('Selected Districts Successfully Deleted')));
        } elseif ($action == 'activate') {
            District::whereIn('id', $ids)->update(['status' => 1]);
            return redirect()->back()->with(FlashMsg::item_new(__('Selected Districts Successfully Activated')));
        } elseif ($action == 'deactivate') {
            District::whereIn('id', $ids)->update(['status' => 0]);
            return redirect()->back()->with(FlashMsg::item_new(__('Selected Districts Successfully Deactivated')));
        }

        return redirect()->back();
    }
    
    public function search_district(Request $request)
    {
        $all_districts = District::with(['city', 'state'])
                                ->where('country_id', $this->default_country_id)
                                ->where('district', 'LIKE', "%" . strip_tags($request->string_search) . "%")
                                ->paginate(10);
        
        if ($all_districts->total() >= 1) {
            return view('countrymanage::district.search-result', compact('all_districts'))->render();
        } else {
            return response()->json(['status' => 'nothing']);
        }
    }
    
    
    public function getStates(Request $request = null)
    {
        $states = State::where('country_id', $this->default_country_id)
                      ->where('status', 1)
                      ->get();
                      
        return response()->json($states);
    }

    public function getCities(Request $request)
    {
        $cities = City::where('state_id', $request->state_id)
                      ->where('status', 1)
                      ->get();
                    
        return response()->json($cities);
    }
    
    public function getDistricts(Request $request)
    {
        $districts = District::where('city_id', $request->city_id)
                           ->where('status', 1)
                           ->get();
                           
        return response()->json($districts);
    }
}