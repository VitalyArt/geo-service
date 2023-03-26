<?php

declare(strict_types=1);

namespace Vitalyart\Geo\Handlers;

class IndexHandler extends BaseHandler
{
    public const ROUTE = '/';

    protected function safeHandle(): void
    {
        $this->responseCode = 302;
        $this->responseHeaders['Location'] = 'index.html';
    }
}
