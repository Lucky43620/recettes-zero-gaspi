<?php

namespace App\Http\Controllers;

use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class NotificationController extends Controller
{
    public function __construct(
        private NotificationService $notificationService
    ) {}

    public function index()
    {
        $user = Auth::user();
        $notifications = $this->notificationService->getUserNotifications($user, 20);
        $unreadCount = $this->notificationService->getUnreadCount($user);

        return Inertia::render('Notifications/Index', [
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
        ]);
    }

    public function markAsRead($id)
    {
        $this->notificationService->markAsRead($id);
        return back();
    }

    public function markAllAsRead()
    {
        $this->notificationService->markAllAsRead(Auth::user());
        return back()->with('success', 'Toutes les notifications ont été marquées comme lues');
    }

    public function destroy($id)
    {
        $this->notificationService->deleteNotification($id);
        return back()->with('success', 'Notification supprimée');
    }
}
