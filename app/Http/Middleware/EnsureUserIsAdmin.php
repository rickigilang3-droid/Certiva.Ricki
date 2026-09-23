<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || ! $request->user()->isAdmin()) {
            if ($request->user() && $request->user()->isMahasiswa()) {
                return redirect()->route('student.certificates')
                    ->with('warning', 'Halaman tersebut hanya dapat diakses oleh Otoritas Administrator.');
            }

            abort(403, 'Akses terbatas untuk Administrator Otoritas Kampus.');
        }

        return $next($request);
    }
}
