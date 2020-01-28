<?php

/*
 * Application's bootstrap
 * Preparing everything before launching application.
 */
use App\Core\Foundation\Application;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

/*
 * Symfony Container initialization
 */
$containerBuilder = new ContainerBuilder();
$loader = new PhpFileLoader($containerBuilder, new FileLocator(__DIR__));
$loader->load('services.php');

/*
 * Application initialization
 */
$app = new Application($containerBuilder);
