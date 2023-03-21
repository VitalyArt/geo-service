<?php

declare(strict_types=1);

namespace Vitalyart\Geo\Handlers;

use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

abstract class BaseHandler
{
    protected int $responseCode = 200;

    protected array $responseHeaders = [
        'Content-type' => 'application/json',
    ];

    protected array $responseBody = [];

    abstract protected function safeHandle(): void;

    public function __construct(protected ServerRequestInterface $request)
    {
    }

    public function handle(): ResponseInterface
    {
        $this->safeHandle();

        return new Response($this->responseCode, $this->responseHeaders, json_encode($this->responseBody));
    }
}