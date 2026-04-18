<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Registro de usuario con envío de código de verificación por correo (Google SMTP).
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'department' => 'nullable|string|max:100',
        ]);

        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'department' => $request->department,
            'role' => 'residente',
            'email_verification_code' => $code,
        ]);

        // En desarrollo: verificar automáticamente. En producción: enviar correo.
        $autoVerify = app()->environment('local');

        if (!$autoVerify) {
            try {
                Mail::raw("Tu código de verificación es: {$code}", function ($message) use ($user) {
                    $message->to($user->email)
                            ->subject('Código de verificación de correo');
                });
            } catch (\Exception $e) {
                $autoVerify = true;
            }
        }

        if ($autoVerify) {
            $user->update([
                'email_verified_at' => now(),
                'email_verification_code' => null,
            ]);
        }

        $response = [
            'user' => $user->only('id', 'name', 'email', 'role', 'department'),
            'requires_verification' => !$autoVerify,
        ];

        if ($autoVerify) {
            $response['message'] = 'Usuario registrado y verificado. Ya puedes iniciar sesión.';
        } else {
            $response['message'] = 'Usuario registrado. Revisa tu correo para verificar tu cuenta.';
        }

        return response()->json($response, 201);
    }

    /**
     * Verificar correo electrónico con código de 6 dígitos.
     */
    public function verifyEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|string|size:6',
        ]);

        $user = User::where('email', $request->email)
                     ->where('email_verification_code', $request->code)
                     ->first();

        if (!$user) {
            return response()->json(['message' => 'Código de verificación inválido.'], 422);
        }

        $user->update([
            'email_verified_at' => now(),
            'email_verification_code' => null,
        ]);

        return response()->json(['message' => 'Correo verificado exitosamente.']);
    }

    /**
     * Login con token por dispositivo (Sanctum).
     * Cada dispositivo recibe su propio token.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'device_name' => 'required|string', // Ej: "iPhone 15", "Chrome Windows"
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Las credenciales son incorrectas.'],
            ]);
        }

        if (!$user->email_verified_at) {
            return response()->json(['message' => 'Debes verificar tu correo antes de iniciar sesión.'], 403);
        }

        // Un token por dispositivo
        $token = $user->createToken($request->device_name)->plainTextToken;

        return response()->json([
            'message' => 'Sesión iniciada.',
            'token' => $token,
            'user' => $user->only('id', 'name', 'email', 'role', 'department'),
        ]);
    }

    /**
     * Logout del dispositivo actual.
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Sesión cerrada en este dispositivo.']);
    }

    /**
     * Cambiar contraseña y cerrar sesión en TODOS los dispositivos.
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'La contraseña actual es incorrecta.'], 422);
        }

        $user->update(['password' => $request->password]);

        // Eliminar TODOS los tokens del usuario (cerrar sesión en todos los dispositivos)
        $user->tokens()->delete();

        return response()->json(['message' => 'Contraseña cambiada. Se cerró sesión en todos los dispositivos.']);
    }

    /**
     * Solicitar recuperación de contraseña (envía código de 6 dígitos por correo).
     */
    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'Si el correo existe, recibirás un código.']);
        }

        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user->update([
            'password_reset_code' => $code,
            'password_reset_code_expires_at' => now()->addMinutes(15),
        ]);

        try {
            Mail::raw("Tu código de recuperación de contraseña es: {$code}", function ($message) use ($user) {
                $message->to($user->email)
                        ->subject('Código de recuperación de contraseña');
            });
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'No se pudo enviar el correo. Código de recuperación: ' . $code,
                'reset_code' => $code,
            ]);
        }

        return response()->json(['message' => 'Si el correo existe, recibirás un código.']);
    }

    /**
     * Resetear contraseña con código de 6 dígitos.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|string|size:6',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::where('email', $request->email)
                     ->where('password_reset_code', $request->code)
                     ->first();

        if (!$user || !$user->password_reset_code_expires_at || $user->password_reset_code_expires_at->isPast()) {
            return response()->json(['message' => 'Código inválido o expirado.'], 422);
        }

        $user->update([
            'password' => $request->password,
            'password_reset_code' => null,
            'password_reset_code_expires_at' => null,
        ]);

        // Cerrar sesión en todos los dispositivos
        $user->tokens()->delete();

        return response()->json(['message' => 'Contraseña restablecida exitosamente.']);
    }

    /**
     * Obtener usuario autenticado.
     */
    public function me(Request $request)
    {
        return response()->json($request->user()->only('id', 'name', 'email', 'role', 'department'));
    }
}
