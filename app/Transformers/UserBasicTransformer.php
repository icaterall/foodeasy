<?php

namespace App\Transformers;

use App\Models\Rider;
use League\Fractal\TransformerAbstract;

class UserBasicTransformer extends TransformerAbstract
{
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
            'is_available' => $user->is_available,
        ];
    }
}
