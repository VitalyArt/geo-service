<?php

declare(strict_types=1);

use Nyholm\Psr7;
use Spiral\RoadRunner;
use Vitalyart\Geo\Exceptions\ApiException;
use Vitalyart\Geo\Handlers\IndexHandler;
use Vitalyart\Geo\Handlers\V1\ContainsHandler;
use Vitalyart\Geo\Services\RequestBodyParserService;
use Vitalyart\Geo\Services\RouteService;

include __DIR__ . '/vendor/autoload.php';

$worker = RoadRunner\Worker::create();
$psrFactory = new Psr7\Factory\Psr17Factory();

$psr7 = new RoadRunner\Http\PSR7Worker($worker, $psrFactory, $psrFactory, $psrFactory);

$router = new RouteService();
$router->addRoute('GET', IndexHandler::ROUTE, IndexHandler::class);
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
    } catch (ApiException $e) {
        $headers = [
            'Content-type' => 'application/json',
        ];

        $body = [
            'error_description' => $e->getMessage(),
        ];

        $psr7->respond(new Psr7\Response($e->getCode(), $headers, json_encode($body)));
    } catch (Throwable $e) {
        $psr7->respond(new Psr7\Response(500, [], $e->getMessage() . PHP_EOL . $e->getTraceAsString()));
    }

    gc_collect_cycles();
}