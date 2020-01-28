<?php


namespace App\Core\Routing;

use Closure;


/**
 * Interface IKernel
 * @package App\Core\Routing
 */
interface IKernel
{
    /**
     * @param $ojbect
     * @param Closure $next
     * @return mixed
     */
    public function handle($ojbect, Closure $next);
}