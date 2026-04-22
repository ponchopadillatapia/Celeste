<?php

namespace App\Http\Controllers;

use App\Events\NewChatMessage;
use App\Models\ChatMessage;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    /**
     * Obtener lista de departamentos (excluyendo el del usuario actual).
     */
    public function departments(Request $request)
    {
        $departments = \App\Models\User::whereNotNull('department')
            ->where('department', '!=', $request->user()->department)
            ->distinct()
            ->pluck('department');

        return response()->json($departments);
    }

    /**
     * Obtener mensajes entre dos departamentos.
     */
    public function index(Request $request)
    {
        $request->validate([
            'department' => 'required|string',
        ]);

        $userDept = $request->user()->department;
        $targetDept = $request->department;

        $messages = ChatMessage::where(function ($q) use ($userDept, $targetDept) {
                $q->where('department_from', $userDept)->where('department_to', $targetDept);
            })
            ->orWhere(function ($q) use ($userDept, $targetDept) {
                $q->where('department_from', $targetDept)->where('department_to', $userDept);
            })
            ->with('user:id,name,department')
            ->orderBy('created_at', 'asc')
            ->paginate(50);

        return response()->json($messages);
    }

    /**
     * Enviar mensaje a otro departamento (WebSocket).
     */
    public function store(Request $request)
    {
        $request->validate([
            'department_to' => 'required|string',
            'message' => 'required|string|max:1000',
        ]);

        $chatMessage = ChatMessage::create([
            'user_id' => $request->user()->id,
            'department_from' => $request->user()->department,
            'department_to' => $request->department_to,
            'message' => $request->message,
        ]);

        $chatMessage->load('user:id,name,department');

        // Emitir por WebSocket (si está disponible)
        try {
            broadcast(new NewChatMessage($chatMessage))->toOthers();
        } catch (\Exception $e) {
            // WebSocket no disponible, continuar sin broadcast
        }

        return response()->json($chatMessage, 201);
    }
}
