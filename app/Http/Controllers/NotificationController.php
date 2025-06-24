<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $notifications = $user->notifications()->paginate(10);
        
        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead($id)
    {
        $user = Auth::user();
        $notification = $user->notifications()->findOrFail($id);
        $notification->markAsRead();
        
        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        $user = Auth::user();
        $user->unreadNotifications->markAsRead();
        
        return response()->json(['success' => true]);
    }

    public function getUnreadCount()
    {
        $user = Auth::user();
        $count = $user->unreadNotifications->count();
        
        return response()->json(['count' => $count]);
    }

    public function acceptConnection($notificationId)
    {
        $user = Auth::user();
        $notification = $user->notifications()->findOrFail($notificationId);
        
        if ($notification->data['type'] === 'connection_request') {
            $connection = \App\Models\Connection::findOrFail($notification->data['connection_id']);
            $connection->status = 'aceita';
            $connection->save();
            
            $notification->markAsRead();
            
            return response()->json(['success' => true, 'message' => 'Conexão aceita!']);
        }
        
        return response()->json(['success' => false, 'message' => 'Notificação inválida']);
    }

    public function rejectConnection($notificationId)
    {
        $user = Auth::user();
        $notification = $user->notifications()->findOrFail($notificationId);
        
        if ($notification->data['type'] === 'connection_request') {
            $connection = \App\Models\Connection::findOrFail($notification->data['connection_id']);
            $connection->status = 'recusada';
            $connection->save();
            
            $notification->markAsRead();
            
            return response()->json(['success' => true, 'message' => 'Conexão recusada!']);
        }
        
        return response()->json(['success' => false, 'message' => 'Notificação inválida']);
    }
}
