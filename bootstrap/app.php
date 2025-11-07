<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
    /*
     ***********  middleware setup extend here*********   
    */
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
   ->withMiddleware(function (Middleware $middleware): void {
    $middleware->alias([
        'role' => \App\Http\Middleware\RoleMiddleware::class,
        'check.session' => \App\Http\Middleware\CheckSessionExpiration::class,
    ]);
    
    // Add session check middleware to web group
    $middleware->web(append: [
        \App\Http\Middleware\CheckSessionExpiration::class,
    ]);
})

    ->withExceptions(function (Exceptions $exceptions): void {
        // Handle authentication exceptions (session expiration, unauthenticated users)
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Your session has expired. Please login again.'], 401);
            }
            
            return redirect()->route('login')->with('error', 'Your session has expired. Please login again.');
        });
        
        // Handle TokenMismatchException (CSRF token expired)
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Your session has expired. Please refresh and try again.'], 419);
            }
            
            return redirect()->route('login')->with('error', 'Your session has expired. Please login again.');
        });
    })->create();
