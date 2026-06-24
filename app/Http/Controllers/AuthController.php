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

        // Intentar enviar correo de verificación
        $mailSent = $this->sendMail(
            $user->email,
            'Código de verificación - CHAT SUPER-PONCHO',
            $user->name,
            'Tu código de verificación es:',
            $code
        );

        if (!$mailSent) {
            // Si no se pudo enviar, verificar automáticamente
            $user->update([
                'email_verified_at' => now(),
                'email_verification_code' => null,
            ]);
        }

        return response()->json([
            'message' => $mailSent
                ? 'Usuario registrado. Revisa tu correo para verificar tu cuenta.'
                : 'Usuario registrado y verificado. Ya puedes iniciar sesión.',
            'user' => $user->only('id', 'name', 'email', 'role', 'department'),
            'requires_verification' => $mailSent,
        ], 201);
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

        // Generar token automáticamente al verificar
        $token = $user->createToken('Navegador Web')->plainTextToken;

        return response()->json([
            'message' => 'Correo verificado exitosamente.',
            'token' => $token,
            'user' => $user->only('id', 'name', 'email', 'role', 'department'),
        ]);
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
            return response()->json(['message' => 'Si el correo existe, recibirás un código.', 'mail_sent' => false]);
        }

        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user->update([
            'password_reset_code' => $code,
            'password_reset_code_expires_at' => now()->addMinutes(15),
        ]);

        $mailSent = $this->sendMail(
            $user->email,
            'Recuperación de contraseña - CHAT SUPER-PONCHO',
            $user->name,
            'Tu código de recuperación de contraseña es:',
            $code
        );

        return response()->json([
            'message' => $mailSent
                ? 'Código enviado a tu correo. Revisa tu bandeja de entrada.'
                : 'No se pudo enviar el correo. Verifica la configuración SMTP.',
            'mail_sent' => $mailSent,
        ]);
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

    /**
     * Enviar correo HTML con código.
     */
    private function sendMail(string $to, string $subject, string $name, string $text, string $code): bool
    {
        try {
            $html = '
            <div style="font-family:Arial,sans-serif;max-width:480px;margin:0 auto;background:#0a0a12;border:1px solid #1a1a2e;border-radius:12px;padding:32px;">
                <h2 style="color:#00f0ff;text-align:center;margin-bottom:8px;">🏢 CHAT SUPER-PONCHO</h2>
                <p style="color:#777;text-align:center;font-size:13px;margin-bottom:24px;">Sistema de gestión residencial</p>
                <p style="color:#e0e0e8;font-size:15px;">Hola <strong>' . e($name) . '</strong>,</p>
                <p style="color:#aaa;font-size:14px;">' . e($text) . '</p>
                <div style="text-align:center;margin:24px 0;">
                    <span style="display:inline-block;background:linear-gradient(135deg,#6e00ff,#00f0ff);color:#fff;font-size:28px;font-weight:700;letter-spacing:8px;padding:14px 28px;border-radius:10px;">' . e($code) . '</span>
                </div>
                <p style="color:#555;font-size:12px;text-align:center;">Este código expira en 15 minutos. Si no solicitaste esto, ignora este correo.</p>
            </div>';

            Mail::html($html, function ($message) use ($to, $subject) {
                $message->to($to)->subject($subject);
            });

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
