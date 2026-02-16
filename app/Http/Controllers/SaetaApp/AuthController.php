<?php

namespace App\Http\Controllers\SaetaApp;

use App\Http\Controllers\Controller;
use App\Models\Dominio;
use App\Models\Negocio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Testing\Fluent\Concerns\Has;

class AuthController extends Controller
{
    const MAX_LOGIN_ATTEMPTS = 5;

    /**
     * Tiempo de bloqueo en minutos
     */
    const LOCKOUT_TIME = 15;

    /**
     * Intentos por usuario específico
     */
    const MAX_USER_ATTEMPTS = 3;

    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'username' => 'required',
                'password' => 'required'
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 400);
            }
            $username = $request->username;
            $password = $request->password;
            $ip = $request->ip();

            // Rate limiting por IP
            $ipKey = 'login_attempts_ip:' . $ip;
            $ipAttempts = RateLimiter::attempts($ipKey);

            if ($ipAttempts >= self::MAX_LOGIN_ATTEMPTS) {
                $seconds = RateLimiter::availableIn($ipKey);

                // Log de intento sospechoso
                Log::warning("Rate limit excedido para IP: {$ip}");

                return response()->json([
                    'success' => false,
                    'message' => "Demasiados intentos de inicio de sesión. Intente nuevamente en " . ceil($seconds / 60) . " minutos.",
                    'retry_after' => $seconds
                ], 429);
            }

            // Rate limiting por usuario
            $userKey = 'login_attempts_user:' . $username;
            $userAttempts = Cache::get($userKey, 0);

            if ($userAttempts >= self::MAX_USER_ATTEMPTS) {
                $lockoutUntil = Cache::get($userKey . ':lockout');
                if ($lockoutUntil && now()->timestamp < $lockoutUntil) {
                    $remainingTime = $lockoutUntil - now()->timestamp;

                    return response()->json([
                        'success' => false,
                        'message' => "Usuario temporalmente bloqueado. Intente nuevamente en " . ceil($remainingTime / 60) . " minutos.",
                        'retry_after' => $remainingTime
                    ], 429);
                } else {
                    // Reset si ya pasó el tiempo de bloqueo
                    Cache::forget($userKey);
                    Cache::forget($userKey . ':lockout');
                }
            }
            $credentials = filter_var($username, FILTER_VALIDATE_EMAIL) ?
                ['email' => $username, 'password' => $password] :
                ['username' => $username, 'password' => $password];

            $user = User::where('email', $username)
                ->orWhere('username', $username)
                ->select('id','name','email')
                ->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => "Usuario inactivo o inexistente"
                ], 401);
            }

            RateLimiter::clear($ipKey);
            Cache::forget($userKey);
            Cache::forget($userKey . ':lockout');


            /** @var \PHPOpenSourceSaver\JWTAuth\JWTGuard $auth */
            $auth = auth('api');
            $token = $auth->attempt($credentials);

            if ($token) {
                $dominios = Dominio::where('user_id', $user->id)
                ->select('full_dominio','id','api_key','protocol', 'nombre', 'vencimiento')
                ->get()
                ->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'api_key' => $item->api_key,
                            'full_dominio' => $item->full_dominio,
                            'api_url'=> $item->full_dominio . '/api',
                            'url' => $item->protocol . $item->full_dominio,
                            'nombre'=>$item->nombre,
                            'vencimiento'=>$item->vencimiento
                    ];
                });

                return response()->json([
                    'success' => true,
                    'results' => [
                        'user' => $user,
                        'instancias'=>$dominios
                    ]
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => "Error de credenciales"
            ], 401);
        } catch (\Throwable $th) {
            Log::error($th);
            //Errores::create(['descripcion' => 'Error en el login. detalle: '.$th->getMessage()]);
            throw $th;
        }
    }

    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:6'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 400);
            }

            // Rate limiting por usuario
            $userKey = 'register:' . $request->email;
            $userAttempts = Cache::get($userKey, 0);

            if ($userAttempts >= self::MAX_USER_ATTEMPTS) {
                $lockoutUntil = Cache::get($userKey . ':lockout');
                if ($lockoutUntil && now()->timestamp < $lockoutUntil) {
                    $remainingTime = $lockoutUntil - now()->timestamp;

                    return response()->json([
                        'success' => false,
                        'message' => "Email temporalmente bloqueado. Intente nuevamente en " . ceil($remainingTime / 60) . " minutos.",
                        'retry_after' => $remainingTime
                    ], 429);
                } else {
                    // Reset si ya pasó el tiempo de bloqueo
                    Cache::forget($userKey);
                    Cache::forget($userKey . ':lockout');
                }
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);

            // Crear un negocio asociado al usuario
            $negocio = Negocio::create([
                'nombre' => $request->name,
                'user_id' => $user->id,
                'temporal' => $request->password // Solo para marcarlo como temporal, no se guarda así
            ]);

            return response()->json([
                'success' => true,
                'message' => "Usuario registrado exitosamente. Estate atento a tu correo para más detalles."
            ]);

        } catch (\Throwable $th) {
            Log::error($th);
            //Errores::create(['descripcion' => 'Error en el registro. detalle: '.$th->getMessage()]);
            throw $th;
        }
    }

}