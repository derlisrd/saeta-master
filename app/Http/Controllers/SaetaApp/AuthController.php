<?php

namespace App\Http\Controllers\SaetaApp;

use App\Http\Controllers\Controller;
use App\Models\Dominio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Cache;



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
                $refreshToken = JWTAuth::claims([
                    'type' => 'refresh',
                    'user_id' => $user->id
                ])->fromUser($user);

                $dominios = Dominio::where('user_id', $user->id)
                ->select('full_dominio','id','api_key')
                ->get();

                return response()->json([
                    'success' => true,
                    'results' => [
                        'user' => $user,
                        'instancias'=>$dominios,
                        'tokenRaw' => $token,
                        'token' => 'Bearer ' . $token,
                        'refresh_token' => $refreshToken,
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

    public function me()
    {
        return response()->json(Auth::guard('api')->user());
    }

    public function logout()
    {
        Auth::guard('api')->logout();

        return response()->json([
            'message' => 'Sesión cerrada exitosamente'
        ]);
    }

    public function refresh()
    {
        /** @var \PHPOpenSourceSaver\JWTAuth\JWTGuard $guard */
        $guard = Auth::guard('api');

        return $this->respondWithToken($guard->refresh());
    }

    protected function respondWithToken($token)
    {
        /** @var \PHPOpenSourceSaver\JWTAuth\JWTGuard $guard */
        $guard = Auth::guard('api');

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $guard->factory()->getTTL() * 60,
            'user' => Auth::guard('api')->user()
        ]);
    }
}