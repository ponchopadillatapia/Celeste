<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Canales de Broadcasting (WebSocket)
|--------------------------------------------------------------------------
*/

// Canal privado de notificaciones por usuario
Broadcast::channel('notifications.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

// Canal de presencia para chat entre departamentos
Broadcast::channel('chat.department.{department}', function ($user, $department) {
    if ($user->department === $department) {
        return ['id' => $user->id, 'name' => $user->name, 'department' => $user->department];
    }
    return false;
});

// Canal público para encuestas en vivo (todos pueden ver resultados)
Broadcast::channel('survey.{surveyId}', function () {
    return true;
});
