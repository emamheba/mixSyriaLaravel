<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\FlashMsg;
use App\Http\Controllers\Controller;
use App\Mail\BasicMail;
use App\Models\Backend\IdentityVerification;
use App\Models\Backend\Listing;
use App\Models\Backend\ListingTag;
use App\Models\Backend\MediaUpload;
use App\Models\Common\ListingReport;
use App\Models\Frontend\AccountDeactivate;
use App\Models\Frontend\ListingFavorite;
use App\Models\Frontend\Review;
use App\Models\Frontend\UserNotification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Modules\Chat\app\Models\LiveChat;
use Modules\Chat\app\Models\LiveChatMessage;
use Modules\CountryManage\app\Models\Country;
use Modules\CountryManage\app\Models\State;
use Modules\CountryManage\app\Models\City;
use Modules\Membership\app\Models\BusinessHours;
use Modules\Membership\app\Models\Enquiry;
use Modules\Membership\app\Models\MembershipHistory;
use Modules\Membership\app\Models\UserMembership;
use Modules\SMSGateway\app\Models\UserOtp;
use Modules\SupportTicket\app\Models\ChatMessage;
use Modules\SupportTicket\app\Models\Ticket;
use Modules\Wallet\app\Models\Wallet;
use WpOrg\Requests\Auth;
use App\Actions\Media\v1\MediaHelper;

class UserManageController extends Controller
{
  public function __construct(private MediaHelper $mediaHelper)
  {
  }

  public function add_user(Request $request)
  {
    if ($request->isMethod('post')) {
      $request->validate([
        'first_name' => 'required|max:191',
        'last_name' => 'required|max:191',
        'email' => 'required|email|unique:users|max:191',
        'username' => 'required|unique:users|max:191',
        'phone' => 'required|unique:users|max:191',
        'password' => 'required|min:6|max:191|confirmed',
        'country_id' => 'nullable|exists:countries,id',
        'state_id' => 'nullable|exists:states,id',
        'city_id' => 'nullable|exists:cities,id',
      ]);

      $email_verify_tokn = sprintf("%d", random_int(123456, 999999));
      $user = User::create([
        'first_name' => $request->first_name,
        'last_name' => $request->last_name,
        'email' => $request->email,
        'username' => $request->username,
        'phone' => $request->phone,
        'password' => Hash::make($request->password),
        'terms_conditions' => 1,
        'email_verify_token' => $email_verify_tokn,
        'email_verified' => 1,
        'country_id' => $request->country_id,
        'state_id' => $request->state_id,
        'city_id' => $request->city_id,
        'status' => 1,
      ]);

      Wallet::create([
        'user_id' => $user->id,
        'balance' => 0,
        'remaining_balance' => 0,
        'withdraw_amount' => 0,
        'status' => 1
      ]);

      return back()->with(FlashMsg::item_new(__('User Successfully Created')));
    }

    return redirect()->route('admin.users.page');
  }


  public function all_users(Request $request)
  {
    $countries = Country::all();
    $states = State::all();
    $cities = City::all();

    $query = User::with('identity_verify');

    if ($request->filled('string_search')) {
      $search = $request->string_search;
      $query->where(function ($q) use ($search) {
        $q->where('first_name', 'like', "%{$search}%")
          ->orWhere('last_name', 'like', "%{$search}%")
          ->orWhere('email', 'like', "%{$search}%")
          ->orWhere('phone', 'like', "%{$search}%");
      });
    }

    if ($request->has('status') && $request->status !== '') {
      $query->where('status', $request->status);
    }

    if ($request->has('verified') && $request->verified !== '') {
      $query->where('email_verified', $request->verified);
    }

    if ($request->filled('country_id')) {
      $query->where('country_id', $request->country_id);
    }
    if ($request->filled('state_id')) {
      $query->where('state_id', $request->state_id);
    }
    if ($request->filled('city_id')) {
      $query->where('city_id', $request->city_id);
    }

    $all_users = $query->latest()
      ->paginate(10)
      ->withQueryString();

    $total_users = User::count();
    $verified_users = User::where('email_verified', 1)->count();
    $active_users = User::where('status', 1)->count();
    $inactive_users = User::where('status', 0)->count();
    $verified_percentage = $total_users
      ? round(($verified_users / $total_users) * 100)
      : 0;
    $active_percentage = $total_users
      ? round(($active_users / $total_users) * 100)
      : 0;
    $inactive_percentage = $total_users
      ? round(($inactive_users / $total_users) * 100)
      : 0;

    $last_month_users = User::where('created_at', '>=', now()->subDays(30))->count();
    $prev_month_users = User::whereBetween('created_at', [now()->subDays(60), now()->subDays(30)])->count();
    $user_growth_rate = $prev_month_users
      ? round((($last_month_users - $prev_month_users) / $prev_month_users) * 100)
      : 0;

    return view('backend.pages.user.users.all-users', compact(
      'all_users',
      'countries',
      'states',
      'cities',
      'total_users',
      'verified_users',
      'active_users',
      'inactive_users',
      'verified_percentage',
      'active_percentage',
      'inactive_percentage',
      'user_growth_rate'
    ));
  }


  function user_pagination(Request $request)
  {
    if ($request->ajax()) {
      $all_users = User::with(['identity_verify'])->latest()->paginate(10);
      return view('backend.pages.user.users.search-result', compact('all_users'));
    }
  }

  public function search_user(Request $request)
  {
    $all_users = User::where(function ($q) use ($request) {
      $q->where('first_name', 'LIKE', "%" . strip_tags($request->string_search) . "%")
        ->orWhere('last_name', 'LIKE', "%" . strip_tags($request->string_search) . "%")
        ->orWhere('email', 'LIKE', "%" . strip_tags($request->string_search) . "%")
        ->orWhere('phone', 'LIKE', "%" . strip_tags($request->string_search) . "%");
    })->paginate(10);
    return $all_users->total() >= 1 ? view('backend.pages.user.users.search-result', compact('all_users'))->render() : response()->json(['status' => __('nothing')]);
  }

  public function edit_info(Request $request)
  {
    $request->validate([
      'edit_first_name' => 'required',
      'edit_last_name' => 'required',
      'edit_username' => 'required|max:191|unique:users,username,' . $request->edit_user_id,
      'edit_email' => 'required|max:191|unique:users,email,' . $request->edit_user_id,
      'edit_phone' => 'required|max:191|unique:users,phone,' . $request->edit_user_id,
    ]);
    User::where('id', $request->edit_user_id)->update([
      'first_name' => $request->edit_first_name,
      'last_name' => $request->edit_last_name,
      'username' => $request->edit_username,
      'email' => $request->edit_email,
      'phone' => $request->edit_phone,
      'country_id' => $request->edit_country,
      'state_id' => $request->edit_state,
      'city_id' => $request->edit_city,
    ]);

    try {
      $message = get_static_option('user_info_update_message') ?? __('Your information successfully updated');
      $message = str_replace(["@name", "@username", "@email"], [$request->edit_first_name . ' ' . $request->edit_last_name, $request->edit_username, $request->edit_email], $message);
      Mail::to($request->edit_email)->send(new BasicMail([
        'subject' => get_static_option('user_info_update_subject') ?? __('User Info Update Email'),
        'message' => $message
      ]));
    } catch (\Exception $e) {
    }
    FlashMsg::item_new(__('User Info Successfully Updated'));
    return back();
  }

  public function change_password(Request $request)
  {
    if ($request->isMethod('post')) {
      $request->validate([
        'password' => 'required|min:6',
        'confirm_password' => 'required|min:6',
      ]);
      if ($request->password === $request->confirm_password) {
        $user = User::select(['email', 'first_name', 'last_name'])->first();
        User::where('id', $request->user_id)->update(['password' => Hash::make($request->password)]);

        try {
          $message = get_static_option('user_password_change_message') ?? __('Your password has been changed');
          $message = str_replace(["@name", "@password"], [$user->first_name . ' ' . $user->last_name, $request->password], $message);
          Mail::to($user->email)->send(new BasicMail([
            'subject' => get_static_option('user_password_change_subject') ?? __('User Password Change Email'),
            'message' => $message
          ]));
        } catch (\Exception $e) {
        }
        return response()->json(['status' => __('ok')]);
      }
      return response()->json(['status' => __('not_match')]);
    }
  }

  public function change_status($id)
  {
    $user = User::select(['email', 'status'])->where('id', $id)->first();
    $user->status == 1 ? $status = 0 : $status = 1;
    User::where('id', $id)->update(['status' => $status]);
    if ($user->status == 0) {
      try {
        $message = get_static_option('user_status_active_message') ?? __('Your account status has been changed from inactive to active.');
        $message = str_replace(["@name"], [$user->first_name . ' ' . $user->last_name], $message);
        Mail::to($user->email)->send(new BasicMail([
          'subject' => get_static_option('user_status_active_subject') ?? __('User Status Activate Email'),
          'message' => $message
        ]));
      } catch (\Exception $e) {

      }
    } else {
      try {
        $message = get_static_option('user_status_inactive_message') ?? __('Your account status has been changed from active to inactive.');
        $message = str_replace(["@name"], [$user->first_name . ' ' . $user->last_name], $message);
        Mail::to($user->email)->send(new BasicMail([
          'subject' => get_static_option('user_status_inactive_subject') ?? __('User Status Inactivate Email'),
          'message' => $message
        ]));
      } catch (\Exception $e) {

      }
    }
    return redirect()->back()->with(FlashMsg::item_new(__('Status Successfully Changed')));
  }

  public function delete_user($id)
  {
    User::find($id)->delete();
    return redirect()->back()->with(FlashMsg::error(__('User Successfully Deleted')));
  }


  public function permanent_delete_user(Request $request, $id)
  {
    $listing_ids = Listing::where('user_id', $id)->pluck('id');
    ListingTag::whereIn('listing_id', $listing_ids)->delete();

    $listings = Listing::where('user_id', $id)->get();
    foreach ($listings as $listing) {
      $this->deleteListingMedia($listing);
    }

    Listing::where('user_id', $id)->delete();
    ListingReport::where('user_id', $id)->delete();
    ListingFavorite::where('user_id', $id)->delete();

    IdentityVerification::where('user_id', $id)->delete();
    UserNotification::where('user_id', $id)->delete();
    Review::where('user_id', $id)->delete();
    AccountDeactivate::where('user_id', $id)->delete();

    if (moduleExists("SMSGateway") && !empty(get_static_option('otp_login_status'))) {
      UserOtp::where('user_id', $id)->delete();
    }

    if (moduleExists("Membership")) {
      MembershipHistory::where('user_id', $id)->delete();
      UserMembership::where('user_id', $id)->delete();
      BusinessHours::where('user_id', $id)->delete();
      Enquiry::where('user_id', $id)->delete();
    }

    $media_uploads = MediaUpload::where(["user_id" => $id, "type" => "web"])->get();
    foreach ($media_uploads as $media) {
      $this->mediaHelper->deleteMediaImage($media->id, 'web');
    }

    $tickets = Ticket::where('user_id', $id)->get();
    foreach ($tickets as $ticket) {
      ChatMessage::where("ticket_id", $ticket->id)->delete();
      $ticket->delete();
    }

    if (moduleExists("Chat")) {
      $live_chats = LiveChat::where('user_id', $id)->get();
      foreach ($live_chats as $chat) {
        LiveChatMessage::where('live_chat_id', $chat->id)->delete();
        $chat->delete();
      }
    }

    if (moduleExists("Wallet")) {
      Wallet::where('user_id', $id)->delete();
    }

    $user = User::withTrashed()->find($id);
    $user->forceDelete();

    return back()->with(FlashMsg::error(__('User Successfully Deleted Permanently.')));
  }



  public function pagination_trashed_users(Request $request)
  {
    if ($request->ajax()) {
      $all_users = User::onlyTrashed()->latest()->paginate(10);
      return view('backend.pages.user.users.search-result-for-delete-users', compact('all_users'))->render();
    }
  }

  public function search_trashed_user(Request $request)
  {
    \Log::info('Search request received: ' . $request->string_search);

    $search_term = $request->string_search;

    if (empty($search_term)) {
      $all_users = User::onlyTrashed()->latest()->paginate(10);
    } else {
      $all_users = User::onlyTrashed()
        ->where(function ($q) use ($search_term) {
          $q->where('first_name', 'LIKE', '%' . $search_term . '%')
            ->orWhere('last_name', 'LIKE', '%' . $search_term . '%')
            ->orWhere('email', 'LIKE', '%' . $search_term . '%')
            ->orWhere('phone', 'LIKE', '%' . $search_term . '%')
            ->orWhere('username', 'LIKE', '%' . $search_term . '%');
        })
        ->latest()
        ->paginate(10);
    }

    \Log::info('Search results count: ' . $all_users->count());

    if ($all_users->count() > 0) {
      return view('backend.pages.user.users.search-result-for-delete-users', compact('all_users'))->render();
    } else {
      return response()->json(['status' => 'nothing']);
    }
  }
  public function restore_user(Request $request, $id)
  {
    User::withTrashed()->find($id)->restore();
    return redirect()->back()->with(FlashMsg::success(__('User Successfully Restored')));
  }

  public function verify_user_email($id)
  {
    $user = User::select(['email_verified', 'email', 'first_name', 'last_name'])->where('id', $id)->first();
    User::where('id', $id)->update(['email_verified' => 1]);
    try {
      $message = get_static_option('user_email_verified_message') ?? __('Your email address successfully verified.');
      $message = str_replace(["@name"], [$user->first_name . ' ' . $user->last_name], $message);
      Mail::to($user->email)->send(new BasicMail([
        'subject' => get_static_option('user_email_verified_subject') ?? __('Disable 2FA Email'),
        'message' => $message
      ]));
    } catch (\Exception $e) {
    }
    return redirect()->back()->with(FlashMsg::item_new(__('Email Address Successfully Verified')));
  }

  private function deleteListingMedia(Listing $listing): void
  {
    try {
      if ($listing->image) {
        $this->mediaHelper->deleteMediaImage($listing->image, 'web');
      }

      if ($listing->gallery_images) {
        $ids = explode('|', $listing->gallery_images);
        foreach ($ids as $id) {
          if (!empty(trim($id))) {
            $this->mediaHelper->deleteMediaImage(intval($id), 'web');
          }
        }
      }
    } catch (\Exception $e) {
      Log::error('Media deletion error in listing: ' . $e->getMessage());
    }
  }

}
