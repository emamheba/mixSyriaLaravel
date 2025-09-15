<?php

namespace Modules\Notification\App\Http\Controllers\Backend;

use Modules\Notification\App\Http\Controllers\Controller;
use Modules\Notification\App\Services\NotificationService;
use Modules\Notification\App\Models\NotificationType;
use Illuminate\Http\Request;

class NotificationAdminController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index()
    {
        $types = NotificationType::all();
        return view('notification::admin.index', compact('types'));
    }

    public function settings(Request $request)
    {
        if ($request->isMethod('post')) {
            // Save notification settings
            return redirect()->back()->with('success', 'Settings saved successfully');
        }
        
        return view('notification::admin.settings');
    }

    public function sendNotification(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'type' => 'required|exists:notification_types,slug',
            'title' => 'required|string|max:255',
            'message' => 'required|string',
        ]);
        
        $notification = $this->notificationService->create(
            $request->user_id,
            $request->type,
            $request->title,
            $request->message,
            $request->data ?? []
        );
        
        return redirect()->back()->with('success', 'Notification sent successfully');
    }
}