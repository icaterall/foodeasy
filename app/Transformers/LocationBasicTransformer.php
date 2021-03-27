<?php

namespace App\Transformers;

use App\Entities\Location;
use League\Fractal\TransformerAbstract;

class LocationBasicTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     * @param Location $location
     * @return array
     */
    public function transform(Location $location)
    {
        return [
            'lat' => $location->lat,
            'long' => $location->long,
            'current' => $location->current,
        ];
    }
}
