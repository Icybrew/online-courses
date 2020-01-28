<?php


namespace App\Core\Factories;


use App\Core\Views\View;

/**
 * Class ViewFactory
 * @package App\Core\Support\Factories
 */
class ViewFactory implements IFactory
{
    /**
     * @param string|null $name
     * @param array $data
     * @return View
     */
    public function make(string $name = null, array $data = []): View
    {
        return new View($name, $data);
    }
}
