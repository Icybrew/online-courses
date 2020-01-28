<?php


namespace App\Http\Middleware;


use App\Core\Routing\Exceptions\HttpException;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class Admin
 * @package App\Http\Middleware
 */
class Admin
{
    public function handle(Request $request, \Closure $next)
    {
        $user = $request->getSession()->get('user');

        if (is_null($user)) {
            throw new HttpException(401);
        }

        if (is_null($user->role) || $user->role != 'Admin') {
            throw new HttpException(401);
        }

        return $next($request);
    }
}
