<?php

namespace App\Core\Config;


/**
 * Class Config
 * @package App\Core\Config
 */
class Config
{
    /**
     * @param $config
     * @param $setting
     * @return mixed|null
     */
    public static function get($config, $setting)
    {
        $path = __DIR__ . '/../../../config/' . $config . '.php';

        $config = (include $path);

        return isset($config[$setting]) ? $config[$setting] : null;
    }
}
