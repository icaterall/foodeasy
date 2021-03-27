<?php

namespace App\Transformers;

use App\Models\Rider;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    protected $defaultIncludes = [
        'profile',
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
        ];
    }

    /**
     * @param $user
     * @return \League\Fractal\Resource\Item
     */
    public function includeProfile(User $user)
    {
        $profile = $user->profile;
        return $this->item($profile, new ProfileTransformer());
    }
}
