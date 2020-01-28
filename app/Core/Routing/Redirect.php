<?php


namespace App\Core\Routing;

use App\Core\Views\IRenderable;

use App\Core\Config\Config;
use Symfony\Component\HttpFoundation\Request;


class Redirect implements IRenderable
{
    private $to;

    public function __construct(string $to = null)
    {
        $this->to = $this->getBaseUrl($to);
    }

    public function render()
    {
        header("location: $this->to");
    }

    public function route(string $name, array $parameters = []): ?Redirect
    {
        $route = Router::findRouteByName($name);

        if (isset($route)) {
            $url = $route->getUrl($parameters);

            $this->to = $this->getBaseUrl($url);
        } else {
            throw new \Exception("Route '$name' not found!");
        }
        return $this;
    }

    public function back(): self
    {
        $this->to = app(Request::class)->getSession()->get('last_url');
        return $this;
    }

    public function with(array $data = []): self
    {
        app(Request::class)->getSession()->getFlashBag()->add('with', $data);
        return $this;
    }

    public function withErrors(array $errors = []): self
    {
        $bag = app(Request::class)->getSession()->getFlashBag();
        $bag->add('errors', $errors);

        return $this;
    }

    public function withInput(): self
    {
        $request = app(Request::class);

        $bag = $request->getSession()->getFlashBag();
        $bag->add('old', $request->request->all());

        return $this;
    }

    public function getBaseUrl($url): string
    {
        return "http://$_SERVER[HTTP_HOST]" . Config::get('app', 'root') . $url;
    }

}
