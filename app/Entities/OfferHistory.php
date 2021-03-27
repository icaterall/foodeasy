<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class OfferHistory.
 *
 * @package namespace App\Entities;
 */
class OfferHistory extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * @var string \
     */
    protected $connection = 'mysql2';
    /**
     * @var string
     */
    protected $table = 'order_offer_history';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    
    protected $fillable = ['order_number','status', 'status_note', 'reason', 'order_offer_id','rider_id','delivery_fee','driver_payout_id'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function offer()
    {
        return $this->belongsTo(Offer::class,'order_offer_id');
    }

}
