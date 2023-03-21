<?php

declare(strict_types=1);

namespace Vitalyart\Geo\Services;

use Psr\Http\Message\ServerRequestInterface;

class RequestBodyParserService
{
    public function parse(ServerRequestInterface &$request): void
    {
        $request->getBody()->rewind();

        $bodyContent = $request->getBody()->getContents();

        try {
            $request = $request->withParsedBody(json_decode($bodyContent, true, JSON_THROW_ON_ERROR));
        } catch (\JsonException $e) {
            // Do nothing
        }
    }
}
