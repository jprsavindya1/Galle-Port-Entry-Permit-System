<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{    /*
     ***********  middleware setup *********   
    */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        if (!$user || !in_array($user->role, $roles)) {
            abort(403, 'Unauthorized. You do not have the required role.');
        }

        return $next($request);
    }
}
