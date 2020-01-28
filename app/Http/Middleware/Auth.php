<?php


namespace App\Http\Middleware;


/**
 * Class Auth
 * @package App\Http\Middleware
 */
class Auth
{
    public function handle($request, \Closure $next)
    {
        $user = $request->getSession()->get('user');

        if (!is_null($user) && !empty($user)) {
            return $next($request);
        } else {
            return redirect()->route('login.show');
        }
    }
}
