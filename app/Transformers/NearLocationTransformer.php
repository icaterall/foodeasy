<?php

namespace App\Transformers;

use App\Entities\Location;
use League\Fractal\TransformerAbstract;

class NearLocationTransformer extends TransformerAbstract
{
    protected $defaultIncludes = [
        'rider',
    ];

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
            'distance_in_km' => $location->distance_in_km,
        ];
    }

    /**
     * @param Location $location
     * @return \League\Fractal\Resource\Item
     */
    public function includeRider(Location $location)
    {
        $user = $location->rider;
        return $this->item($user, new UserBasicTransformer());
    }
}
