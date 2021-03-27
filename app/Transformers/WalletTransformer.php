<?php

namespace App\Transformers;

use App\Models\Rider;
use League\Fractal\TransformerAbstract;

class WalletTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     * @param User $user
     * @return array
     */
    public function transform(Rider $user)
    {
        return [
            'amount' => $user->wallet->amount,
        ];
    }
}
