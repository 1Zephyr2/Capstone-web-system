<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Return the current user's notifications as JSON, for the bell dropdown.
     * GET /notifications
     */
    public function index(Request $request)
    {
        $notifications = AppNotification::where('user_id', Auth::id())
            ->orderByDesc('created_at')
            ->limit(20)
            ->get()
            ->map(fn($n) => [
                'id'         => $n->id,
                'type'       => $n->type,
                'title'      => $n->title,
                'message'    => $n->message,
                'link'       => $n->link,
                'unread'     => $n->isUnread(),
                'created_at' => $n->created_at->diffForHumans(),
            ]);

        $unreadCount = AppNotification::where('user_id', Auth::id())->unread()->count();

        return response()->json([
            'notifications' => $notifications,
            'unread_count'  => $unreadCount,
        ]);
    }

    public function markRead(AppNotification $notification)
    {
        abort_if($notification->user_id !== Auth::id(), 403);
        $notification->update(['read_at' => now()]);
        return response()->json(['success' => true]);
    }

    public function markAllRead()
    {
        AppNotification::where('user_id', Auth::id())->unread()->update(['read_at' => now()]);
        return response()->json(['success' => true]);
    }
}
