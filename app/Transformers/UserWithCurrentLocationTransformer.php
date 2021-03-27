<?php

namespace App\Transformers;

use App\Models\Rider;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use League\Fractal\TransformerAbstract;

class UserWithCurrentLocationTransformer extends TransformerAbstract
{
    protected $defaultIncludes = [
        'currentLocation',
    ];

    /**
     * A Fractal transformer.
     * @param User $user
     * @return array
     */
    public function transform(Rider $user)
    {
        return [
            'uuid' => $user->uuid,
            'name' => $user->name,
            'email' => $user->email,
            'mobile' => $user->mobile,
            'is_approved' => $user->is_approved,
            'is_available' => $user->is_available,
            'stage' => $user->profile->stage,
            'photo' => URL::to(Storage::url($user->profile->photo)),
        ];
    }

    /**
     * @param User $user
     * @return \League\Fractal\Resource\Item
     */
    public function includeCurrentLocation(Rider $user)
    {
        $location = $user->currentLocation;
        return $this->item($location, new LocationBasicTransformer());
    }
}
