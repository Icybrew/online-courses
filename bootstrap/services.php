<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use App\Core\Database\DB;
use Mpdf\Mpdf;
use Symfony\Component\HttpFoundation\Request;
use App\Core\Routing\URL;
use App\Http\Kernel;
use App\Core\Factories\Factory;
use App\Core\Routing\Router;
use App\Core\Database\Database;
use App\Core\Config\Config;

use League\OAuth2\Client\Provider\Google;
use Madcoda\Youtube\Youtube;

use DrewM\MailChimp\MailChimp;
use Symfony\Component\HttpFoundation\Session\Session;

return function (ContainerConfigurator $configurator) {
    $configurator->parameters()
        ->set('container', self::class)
        ->set('google', [
            'clientId' => Config::get('google', 'client_id'),
            'clientSecret' => Config::get('google', 'client_secret'),
            'redirectUri' => Config::get('google', 'redirect_url'),
            //'hostedDomain' => 'http://localhost/online-courses', // optional; used to restrict access to users on your G Suite/Google Apps for Business accounts
            'scopes' => [
                'https://www.googleapis.com/auth/youtube',
                'https://www.googleapis.com/auth/youtube.readonly',
                'https://www.googleapis.com/auth/youtube.force-ssl',
                'https://www.googleapis.com/auth/youtube.upload',
                'https://www.googleapis.com/auth/youtubepartner',
                'https://www.googleapis.com/auth/youtubepartner-channel-audit'
            ]
        ])
        ->set('youtube.key', ['key' => ''])
        ->set('mailChimp.api.key', Config::get('app', 'mailchimp.key'));

    $services = $configurator->services();

    /*
     * Application's core services
     */
    $services->set('request', Request::class)
        ->factory([Request::class, 'createFromGlobals'])
        ->alias(Request::class, 'request');

    $services->set('url', URL::class)
        ->args([ref(Request::class)])
        ->alias(URL::class, 'url');

    $services->set('kernel', Kernel::class)
        ->alias(Kernel::class, 'kernel');

    $services->set('factory', Factory::class)
        ->alias(Factory::class, 'factory');

    $services->set('router', Router::class)
        ->alias(Router::class, 'router');

    $services->set('session', Session::class)
        ->call('start')
        ->alias(Session::class, 'session');

    $services->set('database', Database::class)
        ->alias(Database::class, 'database');


    /*
     * Other services
     */
    $services->set('mpdf', Mpdf::class)
        ->alias(Mpdf::class, 'mpdf');

    $services->set('google', Google::class)
        ->args(['%google%'])
        ->alias('League\OAuth2\Client\Provider\Google', 'google');

    $services->set('youtube', Youtube::class)
        ->args(['%youtube.key%'])
        ->alias('Madcoda\Youtube\Youtube', 'youtube');

    $services->set('mailChimp', MailChimp::class)
        ->args(['%mailChimp.api.key%'])
        ->alias('DrewM\MailChimp\MailChimp', 'mailChimp');
};
