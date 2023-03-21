<?php

declare(strict_types=1);

namespace Vitalyart\Geo\Handlers;

use InvalidArgumentException;
use Location\Coordinate;
use Location\Polygon;

class ContainsHandler extends BaseHandler
{
    public const ROUTE = '/contains';

    protected function safeHandle(): void
    {
        $body = $this->request->getParsedBody();

        $this->validate($body);

        $geofence = new Polygon();

        foreach ($body['polygon'] as $point) {
            $geofence->addPoint(new Coordinate($point['lat'], $point['lng']));
        }

        $point = new Coordinate($body['point']['lat'], $body['point']['lng']);

        $this->responseBody = [
            'contains' => $geofence->contains($point),
        ];
    }

    private function validate(array $body): void
    {
        if (!isset($body['polygon'])) {
            throw new InvalidArgumentException('The "polygon" parameter missing');
        }

        if (!is_array($body['polygon'])) {
            throw new InvalidArgumentException('The "polygon" parameter  has incorrect type');
        }

        if (count($body['polygon']) < 3) {
            throw new InvalidArgumentException('The "polygon" parameter must contain at least 3 values');
        }

        foreach ($body['polygon'] as $i => $point) {
            if (!isset($point['lat'])) {
                throw new InvalidArgumentException('The "polygon[' . $i . '][lat]" parameter missing');
            }

            if (!isset($point['lng'])) {
                throw new InvalidArgumentException('The "polygon[' . $i . '][lng]" parameter missing');
            }
        }

        if (!isset($body['point'])) {
            throw new InvalidArgumentException('The "point" parameter missing');
        }

        if (!isset($body['point']['lat'])) {
            throw new InvalidArgumentException('The "point[lat]" parameter missing');
        }

        if (!isset($body['point']['lng'])) {
            throw new InvalidArgumentException('The "point[lng]" parameter missing');
        }
    }
}