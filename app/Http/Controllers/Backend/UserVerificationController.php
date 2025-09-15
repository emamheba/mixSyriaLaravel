<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Backend\IdentityVerification;
use App\Models\User;
use App\Mail\BasicMail;
use App\Helpers\FlashMsg;
use Illuminate\Support\Facades\Mail;
use App\Actions\Media\v1\MediaHelper;
use App\Models\Backend\MediaUpload;

class UserVerificationController extends Controller
{
  public function index()
  {
    $totalUsers = User::count();
    $verifiedUsers = User::where('verified_status', 1)->count();
    $pendingVerifications = IdentityVerification::where('status', 0)->count();
    $rejectedVerifications = IdentityVerification::where('status', 2)->count();

    $all_requests = IdentityVerification::whereHas('user')
      ->with(['user', 'user_country', 'user_state', 'user_city'])
      ->latest()
      ->paginate(10);

    return view('backend.pages.user.verification.verification-request', compact(
      'all_requests',
      'totalUsers',
      'verifiedUsers',
      'pendingVerifications',
      'rejectedVerifications'
    ));
  }

  protected function sendNotification($user, $subjectKey, $messageKey, $replacements = [])
  {
    $subject = get_static_option($subjectKey) ?? '';
    $message = get_static_option($messageKey) ?? '';
    foreach ($replacements as $search => $replace) {
      $message = str_replace($search, $replace, $message);
    }
    try {
      Mail::to($user->email)->send(new BasicMail([
        'subject' => $subject,
        'message' => $message,
      ]));
    } catch (\Exception $e) {
    }
  }

  public function verification_requests()
  {
    $all_requests = IdentityVerification::whereHas('user')
      ->with('user')
      ->where(function ($query) {
        $query->where('status', 0)
          ->orWhere('status', 2);
      })
      ->latest()
      ->paginate(10);

    return view('backend.pages.user.verification.verification-request', compact('all_requests'));
  }

  public function identity_details(Request $request)
  {
    $user_details = User::select([
      'id',
      'first_name',
      'last_name',
      'email',
      'phone',
      'username',
      'image',
      'country_id',
      'state_id',
      'city_id',
    ])
      ->where('id', $request->user_id)
      ->first();

    $user_identity_details = IdentityVerification::where('user_id', $request->user_id)->first();

    if ($user_details || $user_identity_details) {
      $frontDocumentUrl = $this->getImageUrl($user_identity_details->front_document);
      $backDocumentUrl = $this->getImageUrl($user_identity_details->back_document);
      $userImageUrl = $this->getImageUrl($user_details->image);

      return view('backend.pages.user.verification.profile-and-identity-compare', compact(
        'user_details',
        'user_identity_details',
        'frontDocumentUrl',
        'backDocumentUrl',
        'userImageUrl'
      ));
    } else {
      return redirect()->back()->with(FlashMsg::error('User not found'));
    }
  }
  public function identity_verify_status(Request $request)
  {
    $user = User::find($request->user_id);
    if (!$user) {
      return redirect()->back()->with(FlashMsg::error('User not found'));
    }

    $newStatus = ($user->verified_status == 1) ? 0 : 1;
    $user->update(['verified_status' => $newStatus]);

    IdentityVerification::where('user_id', $request->user_id)
      ->update(['verify_by' => auth()->user()->id]);

    if ($newStatus == 1) {
      $this->sendNotification($user, 'user_identity_verify_confirm_subject', 'user_identity_verify_confirm_message', [
        '@name' => $user->first_name . ' ' . $user->last_name,
        '@username' => $user->username,
        '@email' => $user->email,
      ]);

      IdentityVerification::where('user_id', $request->user_id)->update(['status' => 1]);
      return redirect()->back()->with(FlashMsg::success('User verification approved successfully'));
    } else {
      $this->sendNotification($user, 'user_identity_re_verify_subject', 'user_identity_re_verify_message', [
        '@name' => $user->first_name . ' ' . $user->last_name,
        '@username' => $user->username,
        '@email' => $user->email,
      ]);

      IdentityVerification::where('user_id', $request->user_id)->update(['status' => 2]);
      return redirect()->back()->with(FlashMsg::success('User verification status updated successfully'));
    }
  }

  public function identity_verify_decline(Request $request)
  {
    $user = User::find($request->user_id);
    if (!$user) {
      return redirect()->back()->with(FlashMsg::error('User not found'));
    }
    $user->update(['verified_status' => 0]);
    IdentityVerification::where('user_id', $request->user_id)->update(['status' => 2]);

    $this->sendNotification($user, 'user_identity_decline_subject', 'user_identity_decline_message', [
      '@name' => $user->first_name . ' ' . $user->last_name,
      '@username' => $user->username,
      '@email' => $user->email,
    ]);

    return redirect()->back()->with(FlashMsg::success('User verification declined successfully'));
  }

  public function search_verification_requests(Request $request)
  {
    $search = $request->search;

    $all_requests = IdentityVerification::whereHas('user', function ($query) use ($search) {
      $query->where('first_name', 'LIKE', "%{$search}%")
        ->orWhere('last_name', 'LIKE', "%{$search}%")
        ->orWhere('email', 'LIKE', "%{$search}%")
        ->orWhere('username', 'LIKE', "%{$search}%");
    })
      ->with(['user', 'user_country', 'user_state', 'user_city'])
      ->latest()
      ->paginate(10);

    $totalUsers = User::count();
    $verifiedUsers = User::where('verified_status', 1)->count();
    $pendingVerifications = IdentityVerification::where('status', 0)->count();
    $rejectedVerifications = IdentityVerification::where('status', 2)->count();

    return view('backend.pages.user.verification.user-list', compact(
      'all_requests',
      'totalUsers',
      'verifiedUsers',
      'pendingVerifications',
      'rejectedVerifications',
      'search'
    ));
  }

  public function filter_verification_requests(Request $request)
  {
    $status = $request->status;

    $query = IdentityVerification::whereHas('user')
      ->with(['user', 'user_country', 'user_state', 'user_city']);

    if ($status !== null) {
      $query->where('status', $status);
    }

    $all_requests = $query->latest()->paginate(10);

    $totalUsers = User::count();
    $verifiedUsers = User::where('verified_status', 1)->count();
    $pendingVerifications = IdentityVerification::where('status', 0)->count();
    $rejectedVerifications = IdentityVerification::where('status', 2)->count();

    return view('backend.pages.user.verification.user-list', compact(
      'all_requests',
      'totalUsers',
      'verifiedUsers',
      'pendingVerifications',
      'rejectedVerifications',
      'status'
    ));
  }
  private function getImageUrl($imageId)
  {
    if (!$imageId)
      return null;

    $mediaUpload = MediaUpload::find($imageId);
    if (!$mediaUpload)
      return null;

    $fullPath = 'uploads/media-uploader/' . $mediaUpload->path;
    return asset('storage/' . $fullPath);
  }
}
