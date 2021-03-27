<?php

namespace App\Transformers;

use App\Entities\OfferHistory;
use League\Fractal\TransformerAbstract;

class OfferHistoryTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     * @param OfferHistory $offerHistory
     * @return array
     */
    public function transform(OfferHistory $offerHistory)
    {
        return [
            'order_number' => $offerHistory->offer->order_number,
            'status' => $offerHistory->status,
            'reason' => $offerHistory->offer->reason,
            'note' => $offerHistory->offer->status_note,
            'data' => json_decode($offerHistory->offer->data, true),
            'created_at' => $offerHistory->created_at,
        ];
    }
}
