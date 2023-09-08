<?php

namespace App\Http\Middleware;

use App\Models\Survey;
use Closure;
use Illuminate\Http\Request;

class PatientMiddleware
{
    /**
     * Handle an incoming request.
     * Checks that any request incoming form the test page, where the patient answers the questionnaires, is authorized with a valid token.
     * The app will send automatically the token with every request. But requests from outside the app wil be blocked.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$request->expectsJson())
            abort(403);

        $token = $request->token ?? null;
        $isAuth = Survey::where('token', '=', $token)->get()->count();
        if (!$isAuth) abort(response()->json(['message' => 'Invalid Token'], 401));

        return $next($request);
    }
}
