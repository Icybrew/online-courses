<?php


namespace App\Core\Views;

use App\Core\Routing\URL;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;
use Twig\Markup;
use Twig\TwigFunction;

use App\Core\Config\Config;
use App\Core\Routing\Router;

/**
 * Class View
 * @package Application\Core\Support\Helpers
 */
class View implements IRenderable
{
    /**
     * @var string
     */
    protected $viewName;

    /**
     * @var array
     */

    protected static $data;

    /**
     * View constructor.
     * @param string $name
     * @param array $data
     */
    public function __construct(string $name, array $data = [])
    {
        $this->viewName = $name;
        self::$data = array_merge(self::$data, $data);
    }

    /**
     * @return void
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function render(): void
    {
        $name = $this->viewName . '.php.twig';
        $viewPath = '../resources/views/' . $name;

        $twigLoader = new FilesystemLoader('../resources/views');
        $twig = new Environment($twigLoader, [
            'debug' => true
        ]);

        $twig->addExtension(new DebugExtension());

        $request = app()->get('request');

        // FlashBag Data
        $with = $request->getSession()->getFlashBag()->get('with', [])[0] ?? [];
        self::$data = array_merge(self::$data, $with);

        $errors = $request->getSession()->getFlashBag()->get('errors', []);
        $oldInput = $request->getSession()->getFlashBag()->get('old', []);

        // Asset function
        $twig->addFunction(new TwigFunction('asset', function (?string $asset): ?string {
            return sprintf(Config::get('app', 'root') . '%s', rtrim($asset, '/'));
        }));

        // Method function
        $twig->addFunction(new TwigFunction('method', function (?string $method): ?Markup {
            $field = '<input type="hidden" name="_method" value="' . $method . '">';
            return new Markup($field, 'UTF-8');
        }));

        // Route function
        $twig->addFunction(new TwigFunction('route', function (string $name, array $param = []): ?string {
            $route = Router::findRouteByName($name);
            if (is_null($route)) return null;

            try {
                $url = $route->getUrl($param);
            } catch (\Throwable $ex) {
                return $ex;
            }

            return sprintf(Config::get('app', 'root') . '%s', $url);
        }));

        // Error function
        $twig->addFunction(new TwigFunction('error', function ($name) use ($errors) {
            foreach ($errors as $error) {
                return $error[$name] ?? null;
            }
        }));

        // Old function
        $twig->addFunction(new TwigFunction('old', function ($name) use ($oldInput) {
            foreach ($oldInput as $input) {
                return $input[$name] ?? null;
            }
        }));

        // CSRF function
        $twig->addFunction(new TwigFunction('csrf', function () {
            $token = app()->get('request')->getSession()->get('_token');

            $field = '<input type="hidden" name="_token" value="' . $token . '">';
            return new Markup($field, 'UTF-8');
        }));

        // Auth function
        $twig->addFunction(new TwigFunction('auth', function () {
            return app()->get('request')->getSession()->get('user');
        }));

        // Url function
        $twig->addFunction(new TwigFunction('url', function () {
            return app(URL::class);
        }));

        if (file_exists($viewPath)) {
            $twig->display($name, self::$data);
        } else {
            throw new \Error("View '$viewPath' not found");
        }
    }

    public static function share($name, $data)
    {
        self::$data[$name] = $data;
    }
}
