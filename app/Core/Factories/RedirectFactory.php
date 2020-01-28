<?php


namespace App\Core\Factories;


use App\Core\Routing\Redirect;

/**
 * Class RedirectFactory
 * @package App\Core\Support\Factories
 */
class RedirectFactory implements IFactory
{
    /**
     * @param string|null $to
     * @return Redirect
     */
    public function make(string $to = null): Redirect
    {
        return new Redirect($to);
    }
}
