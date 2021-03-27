<?php

namespace App\Transformers;

use App\Entities\Offer;
use League\Fractal\TransformerAbstract;

class OfferTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     * @param Offer $offer
     * @return array
     */
    public function transform(Offer $offer)
    {
        return [
            'order_number' => $offer->order_number,
            'status' => $offer->status,
            'note' => $offer->note,
            'reason' => $offer->reason,
            'data' => json_decode($offer->data, true),
            'created_at' => $offer->created_at,
        ];
    }
}
