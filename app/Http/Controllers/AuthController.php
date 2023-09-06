<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request): Response
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required'
        ]);

        $credentials = $request->all();

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            return response(['user' => $user]);
        }

        abort(401, 'invalid-credentials');
    }

    public function logout()
    {
        Auth::logout();
        return response(null, 204);
    }
}
