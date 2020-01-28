<?php


namespace App\Http\Middleware;


use Symfony\Component\HttpFoundation\Request;


/**
 * Class VerifyCsrfToken
 * @package App\Http\Middleware
 */
class VerifyCsrfToken
{
    public function handle(Request $request, \Closure $next)
    {
        if ($request->getMethod() == Request::METHOD_POST) {
            if ($request->request->get('_token') != $request->getSession()->get('_token')) {
                // TODO implement kernel return error
                dd('Bad CSRF Token');
                throw new \Error('Bad CSRF Token');
            }
        }

        $request->getSession()->set('_token', bin2hex(random_bytes(16)));

        return $next($request);

    }
}
