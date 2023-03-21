<?php

declare(strict_types=1);

namespace Vitalyart\Geo\Services;

use Psr\Http\Message\ServerRequestInterface;
use Spiral\Core\Exception\Container\NotFoundException;
use Vitalyart\Geo\Handlers\BaseHandler;

class RouteService
{
    private array $handlers = [];

    public function addRoute(string $method, string $route, string $handler): void
    {
        $this->handlers[$method . '-' . $route] = $handler;
    }

    public function handle(ServerRequestInterface $request)
    {
        $method = $request->getMethod();
        $route  = $request->getUri()->getPath();

        $key = $method . '-' . $route;

        if (!isset($this->handlers[$key])) {
            throw new NotFoundException('Not found');
        }

        /** @var BaseHandler $handler */
        $handler = new $this->handlers[$key]($request);

        return $handler->handle();
    }
}