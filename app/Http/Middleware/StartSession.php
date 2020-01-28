<?php


namespace App\Http\Middleware;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;


/**
 * Class StartSession
 * @package App\Http\Middleware
 */
class StartSession
{
    public function handle(Request $request, \Closure $next)
    {
        if (!$request->hasSession()) {
            $request->setSession(app(Session::class));
        }

        return $next($request);
    }
}
