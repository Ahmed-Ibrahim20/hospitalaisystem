<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();

        if (! $user || ! $user->role || $user->role->name !== $role) {
            // إذا كان المستخدم لا يملك الصلاحية، وجهه للداشبورد مع رسالة منبثقة
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'ليس لديك الصلاحية للوصول لهذه الصفحة',
                    'redirect' => route('dashboard')
                ], 403);
            }

            // حفظ رسالة الخطأ في الجلسة
            session()->flash('error', 'ليس لديك الصلاحية للوصول لهذه الصفحة');

            // إعادة التوجيه للداشبورد
            return redirect()->route('dashboard');
        }

        return $next($request);
    }
}
