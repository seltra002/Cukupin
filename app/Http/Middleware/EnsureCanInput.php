<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCanInput
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || ! $request->user()->canInput()) {
            abort(403, 'Kamu cuma punya akses lihat-saja (view-only).');
        }

        return $next($request);
    }
}
