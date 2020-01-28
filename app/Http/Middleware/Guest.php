<?php


namespace App\Http\Middleware;


/**
 * Class Guest
 * @package App\Http\Middleware
 */
class Guest
{
    public function handle($request, \Closure $next)
    {
        $user = $request->getSession()->get('user');

        if (is_null($user) && empty($user)) {
            return $next($request);
        } else {
            return redirect()->route('home');
        }
    }
}
