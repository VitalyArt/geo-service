<?php

declare(strict_types=1);

use Spiral\Core\Exception\Container\NotFoundException;
use Spiral\RoadRunner;
use Nyholm\Psr7;
use Vitalyart\Geo\Handlers\ContainsHandler;
use Vitalyart\Geo\Services\RouteService;
use Vitalyart\Geo\Services\RequestBodyParserService;

include __DIR__ . '/vendor/autoload.php';

$worker = RoadRunner\Worker::create();
$psrFactory = new Psr7\Factory\Psr17Factory();

$psr7 = new RoadRunner\Http\PSR7Worker($worker, $psrFactory, $psrFactory, $psrFactory);

$router = new RouteService();
$router->addRoute('POST', ContainsHandler::ROUTE, ContainsHandler::class);

while (true) {
    try {
        $request = $psr7->waitRequest();

        if ($request === null) {
            break;
        }
    } catch (Throwable $e) {
        $psr7->respond(new Psr7\Response(400, [], $e->getMessage())); // Bad Request
        continue;
    }

    $bodyParser = new RequestBodyParserService();
    $bodyParser->parse($request);

    try {
        $psr7->respond($router->handle($request));
    } catch (NotFoundException $e) {
        $psr7->respond(new Psr7\Response(404, [], $e->getMessage()));
    } catch (Throwable $e) {
        $psr7->respond(new Psr7\Response(500, [], $e->getMessage() . PHP_EOL . $e->getTraceAsString()));
    }

    gc_collect_cycles();
}