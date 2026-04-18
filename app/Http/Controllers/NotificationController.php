<?php

namespace App\Http\Controllers;

use App\Events\NewNotification;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Listar notificaciones del usuario autenticado.
     */
    public function index(Request $request)
    {
        $notifications = Notification::where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($notifications);
    }

    /**
     * Obtener detalle de una notificación.
     */
    public function show(Request $request, Notification $notification)
    {
        if ($notification->user_id !== $request->user()->id) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        // Marcar como leída al ver detalle
        $notification->update(['read' => true]);

        return response()->json($notification);
    }

    /**
     * Marcar notificación como leída.
     */
    public function markAsRead(Request $request, Notification $notification)
    {
        if ($notification->user_id !== $request->user()->id) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        $notification->update(['read' => true]);

        return response()->json(['message' => 'Notificación marcada como leída.']);
    }

    /**
     * Marcar todas como leídas.
     */
    public function markAllAsRead(Request $request)
    {
        Notification::where('user_id', $request->user()->id)
            ->where('read', false)
            ->update(['read' => true]);

        return response()->json(['message' => 'Todas las notificaciones marcadas como leídas.']);
    }

    /**
     * Contar notificaciones no leídas.
     */
    public function unreadCount(Request $request)
    {
        $count = Notification::where('user_id', $request->user()->id)
            ->where('read', false)
            ->count();

        return response()->json(['unread_count' => $count]);
    }

    /**
     * Crear notificación (admin) y emitir por WebSocket.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'type' => 'required|in:mensaje,multa,asamblea,pago_atrasado',
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'data' => 'nullable|array',
        ]);

        $notification = Notification::create($request->only('user_id', 'type', 'title', 'body', 'data'));

        // Emitir por WebSocket en tiempo real
        broadcast(new NewNotification($notification))->toOthers();

        return response()->json($notification, 201);
    }
}
