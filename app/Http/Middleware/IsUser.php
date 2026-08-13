<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsUser
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->role !== 'user') {
            abort(403, 'Access Denied');
        }

        if ($request->user()->status === 'suspended') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect('/login')->with([
                'message' => 'حساب شما به دلیل عدم پرداخت قرض بیش از ۶ ماه تعلیق شده است. لطفاً با مدیریت تماس بگیرید.',
                'alert-type' => 'error',
            ]);
        }

        return $next($request);
    }
}
