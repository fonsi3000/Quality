<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Log;

class SsoController extends Controller
{
    public function handle(Request $request)
    {
        // 1) sin slash final
        $frontendUrl = 'https://app.espumasmedellin-litoral.com/';
        $backendUrl  = config('app.url');

        $token = $request->query('token');


        if (!$token) {
            return redirect()->away("{$frontendUrl}/dashboard?auth_error=missing_token");
        }

        try {
            Log::info('Decodificando token SSO');

            $decoded = JWT::decode(
                $token,
                new Key(config('services.sso.secret'), 'HS256')
            );

            $email = $decoded->email ?? null;

            if (!$email) {
                return redirect()->away("{$frontendUrl}/dashboard?auth_error=missing_email");
            }

            $user = User::where('email', $email)->first();

            if (!$user) {
                return redirect()->away("{$frontendUrl}/dashboard?auth_error=user_not_found");
            }

            Auth::login($user);

            // 2) regenera la sesión
            $request->session()->regenerate();

            // 3) usa ruta relativa para mantener el mismo host y no perder la cookie
            return redirect()->intended('/dashboard');
        } catch (Exception $e) {
            return redirect()->away("{$frontendUrl}/dashboard?auth_error=invalid_token");
        }
    }
}
