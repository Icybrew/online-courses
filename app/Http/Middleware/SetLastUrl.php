<?php


namespace App\Http\Middleware;


use Symfony\Component\HttpFoundation\Request;


/**
 * Class SetLastUrl
 * @package App\Http\Middleware
 */
class SetLastUrl
{
    public function handle(Request $request, \Closure $next)
    {
        $response = $next($request);

        if ($request->hasSession()) {
            $last_url = ("http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
            $request->getSession()->set('last_url', $last_url);
        }

        return $response;

    }
}
