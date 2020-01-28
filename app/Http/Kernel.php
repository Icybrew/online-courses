<?php


namespace App\Http;

use App\Core\Routing\Kernel as HttpKernel;

use App\Http\Middleware\Admin;
use App\Http\Middleware\Auth;
use App\Http\Middleware\Guest;
use App\Http\Middleware\SetLastUrl;
use App\Http\Middleware\StartSession;
use App\Http\Middleware\VerifyCsrfToken;

/**
 * Class Kernel
 * @package Application\Http
 */
class Kernel extends HttpKernel
{
    /**
     * Global Middleware's
     * These middleware's are run during every request.
     *
     * @var array
     */
    protected $middleware = [
        StartSession::class,
        //VerifyCsrfToken::class,
        SetLastUrl::class
    ];

    /**
     * Route specific Middleware's
     * These are used by specific routes.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth' => Auth::class,
        'guest' => Guest::class,
        'admin' => Admin::class
    ];
}
