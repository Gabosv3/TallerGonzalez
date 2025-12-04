<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    private const MAX_LOGIN_ATTEMPTS = 5;
    private const LOCK_TIME_MINUTES = 15;

    public function login(LoginRequest $request)
    {
        $emailKey = strtolower($request->input('email', ''));
        $key = 'login_attempts:' . $request->ip() . '|' . sha1($emailKey);
        
        if (RateLimiter::tooManyAttempts($key, self::MAX_LOGIN_ATTEMPTS)) {
            $seconds = RateLimiter::availableIn($key);
            throw ValidationException::withMessages([
                'email' => ["Demasiados intentos. Intenta en {$seconds}s."],
            ]);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            RateLimiter::hit($key, self::LOCK_TIME_MINUTES * 60);
            throw ValidationException::withMessages([
                'email' => ['Credenciales incorrectas.'],
            ]);
        }

        // Validar condiciones del usuario
        $validation = $this->validateUserLogin($user, $key);
        if ($validation) {
            return $validation;
        }

        RateLimiter::clear($key);
        // Revocar tokens previos con el mismo nombre para evitar proliferación
        if (method_exists($user, 'tokens')) {
            $user->tokens()->where('name', 'auth_token')->delete();
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => new UserResource($user),
            'token' => $token,
            'message' => 'Sesión iniciada correctamente',
        ]);
    }

    /**
     * Validar condiciones del usuario
     */
    private function validateUserLogin(User $user, string $key)
    {
        if (!$user->email_verified_at) {
            return $this->authErrorResponse('Verifica tu email.', 403, $key);
        }

        if ($user->deleted_at) {
            return $this->authErrorResponse('Acceso denegado.', 403, $key);
        }

        return null;
    }

    /**
     * Respuesta de error de autenticación con rate limiting
     */
    private function authErrorResponse(string $message, int $status, string $key)
    {
        RateLimiter::hit($key, self::LOCK_TIME_MINUTES * 60);
        return response()->json(['message' => $message], $status);
    }

    public function logout(Request $request)
    {
        try {
            $token = $request->user()->currentAccessToken();
            
            if ($token) {
                $token->delete();
            }

            return response()->json([
                'message' => 'Sesión cerrada correctamente',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al cerrar la sesión',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function user(Request $request)
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'message' => 'Usuario no autenticado',
            ], 401);
        }

        return response()->json([
            'user' => new UserResource($user->load('roles')),
        ]);
    }

    public function checkAuth(Request $request)
    {
        $user = $request->user();
        
        return response()->json([
            'authenticated' => (bool) $user,
            'user' => $user ? new UserResource($user->load('roles')) : null,
        ]);
    }

    /**
     * Renovar token de acceso
     */
    public function refreshToken(Request $request)
    {
        $user = $request->user();

        if (!$user || $user->deleted_at) {
            return response()->json(['message' => 'Acceso denegado.'], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => new UserResource($user),
            'token' => $token,
            'message' => 'Token renovado',
        ]);
    }
}
