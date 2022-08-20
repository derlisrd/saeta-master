<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
      }


    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'username'=>['required','string'],
            //'email' => ['required', 'email'],
            'password' => ['required','string'],
        ]);

        $remember = $request->filled('remember');

        if (Auth::attempt($credentials,$remember)) {
            $request->session()->regenerate();
            return redirect()->intended('/');
        }

        return back()->withErrors([
            'username' => __('auth.failed')
        ]);
    }
}
