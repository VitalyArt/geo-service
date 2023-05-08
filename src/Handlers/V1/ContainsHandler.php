<?php

declare(strict_types=1);

namespace Vitalyart\Geo\Handlers\V1;

use InvalidArgumentException;
use Location\Coordinate;
use Location\Polygon;
use Vitalyart\Geo\Exceptions\ApiException;
use Vitalyart\Geo\Handlers\BaseHandler;

class ContainsHandler extends BaseHandler
{
    public const ROUTE = '/v1/contains';

    protected function safeHandle(): void
    {
        $body = $this->request->getParsedBody();

        $this->validate($body);

        $polygon = new Polygon();

        foreach ($body['polygon'] as $point) {
            try {
                $polygon->addPoint(new Coordinate($point['lat'], $point['lng']));
            } catch (InvalidArgumentException $e) {
                throw new ApiException($e->getMessage(), 400);
            }
        }

        try {
            $point = new Coordinate($body['point']['lat'], $body['point']['lng']);
        } catch (InvalidArgumentException $e) {
            throw new ApiException($e->getMessage(), 400);
        }

        $this->responseBody = [
            'contains' => $polygon->contains($point),
        ];
    }

    private function validate(array $body): void
    {
        if (!isset($body['polygon'])) {
            throw new ApiException('The "polygon" parameter missing', 400);
        }

        if (!is_array($body['polygon'])) {
            throw new ApiException('The "polygon" parameter  has incorrect type', 400);
        }

        if (count($body['polygon']) < 3) {
            throw new ApiException('The "polygon" parameter must contain at least 3 values', 400);
        }

        foreach ($body['polygon'] as $i => $point) {
            if (!isset($point['lat'])) {
                throw new ApiException('The "polygon[' . $i . '][lat]" parameter missing', 400);
            }

            if (!isset($point['lng'])) {
                throw new ApiException('The "polygon[' . $i . '][lng]" parameter missing', 400);
            }
        }

        if (!isset($body['point'])) {
            throw new ApiException('The "point" parameter missing', 400);
        }

        if (!isset($body['point']['lat'])) {
            throw new ApiException('The "point[lat]" parameter missing', 400);
        }

        if (!isset($body['point']['lng'])) {
            throw new ApiException('The "point[lng]" parameter missing', 400);
        }
    }
}
