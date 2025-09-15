<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Backend\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Actions\Media\v1\MediaHelper;
use App\Models\Backend\MediaUpload;
use Illuminate\Support\Facades\Storage;

class AdminProfileController extends Controller
{
  public function __construct(private MediaHelper $mediaHelper)
  {
  }

  public function adminProfileUpdate(Request $request)
  {
    $this->validate($request, [
      'name' => 'required|string|max:191',
      'email' => 'required|email|max:191',
      'image' => 'nullable|image|mimes:jpg,png,jpeg,gif',
      'about' => 'nullable|string|max:1000',
    ]);

    $data = $request->only(['name', 'email', 'about']);
    $oldImageId = null;
    $newImageId = null;

    try {
      $admin = Auth::guard('admin')->user();

      if ($request->hasFile('image')) {
        $oldImageId = $admin->image;

        $image = $this->mediaHelper->uploadMedia(
          $request->file('image'),
          'admin'
        );

        if ($image) {
          $data['image'] = $image->id;
          $newImageId = $image->id;
        } else {
          throw new \Exception('فشل في رفع الصورة');
        }
      }

      $admin->update($data);

      if ($oldImageId && $newImageId) {
        try {
          $this->mediaHelper->deleteMediaImage($oldImageId, 'admin');
        } catch (\Exception $e) {
          Log::warning('Failed to delete old image: ' . $e->getMessage());
        }
      }

      return redirect()->back()->with([
        'msg' => __('Profile updated successfully'),
        'type' => 'success'
      ]);

    } catch (\Exception $e) {
      if ($newImageId) {
        try {
          $this->mediaHelper->deleteMediaImage($newImageId, 'admin');
        } catch (\Exception $deleteException) {
          Log::error('Failed to rollback image upload: ' . $deleteException->getMessage());
        }
      }

      Log::error('Profile update failed: ' . $e->getMessage());
      return redirect()->back()->with([
        'msg' => __('Something went wrong! Please try again.'),
        'type' => 'danger'
      ]);
    }
  }

  public function adminPasswordChange(Request $request)
  {
    $this->validate($request, [
      'old_password' => 'required|string',
      'password' => 'required|string|min:8|confirmed'
    ]);

    try {
      $user = Admin::findOrFail(Auth::guard('admin')->user()->id);

      if (Hash::check($request->old_password, $user->password)) {
        $user->password = Hash::make($request->password);
        $user->save();

        Auth::guard('admin')->logout();

        return redirect()->route('admin.login')->with([
          'msg' => __('Password changed successfully. Please login with your new password.'),
          'type' => 'success'
        ]);
      }

      return redirect()->back()->with([
        'msg' => __('Current password is incorrect. Please try again.'),
        'type' => 'danger'
      ]);
    } catch (\Exception $e) {
      Log::error('Password change failed: ' . $e->getMessage());
      return redirect()->back()->with([
        'msg' => __('Something went wrong! Please try again.'),
        'type' => 'danger'
      ]);
    }
  }

  public function adminLogout()
  {
    try {
      Log::info("Admin logout initiated for user: " . Auth::guard('admin')->user()->id);
      Auth::guard('admin')->logout();

      return redirect()->route('admin.form.login')->with([
        'msg' => __('You have been logged out successfully'),
        'type' => 'success'
      ]);
    } catch (\Exception $e) {
      Log::error('Logout failed: ' . $e->getMessage());
      return redirect()->route('admin.form.login')->with([
        'msg' => __('Logout completed'),
        'type' => 'info'
      ]);
    }
  }

  public function adminProfile()
  {
    try {
      $admin = Auth::guard('admin')->user();

      $imageUrl = $this->getAdminImageUrl($admin->image);

      return view('backend.auth.edit-profile', compact('admin', 'imageUrl'));

    } catch (\Exception $e) {
      Log::error('Profile page load failed: ' . $e->getMessage());
      return redirect()->back()->with([
        'msg' => __('Unable to load profile page'),
        'type' => 'danger'
      ]);
    }
  }

  public function adminPassword()
  {
    try {
      return view('backend.auth.change-password');
    } catch (\Exception $e) {
      Log::error('Password page load failed: ' . $e->getMessage());
      return redirect()->back()->with([
        'msg' => __('Unable to load password page'),
        'type' => 'danger'
      ]);
    }
  }

  private function getAdminImageUrl($imageId): ?string
  {
    if (!$imageId) {
      return null;
    }

    $mediaUpload = MediaUpload::find($imageId);
    if (!$mediaUpload) {
      return null;
    }

    $fullPath = 'uploads/media-uploader/' . $mediaUpload->path;
    return asset('storage/' . $fullPath);
  }
}