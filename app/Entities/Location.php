<?php

namespace App\Entities;

use App\Models\Rider;
use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Location.
 *
 * @package namespace App\Entities;
 */
class Location extends Model implements Transformable
{
    use TransformableTrait;
   public $table = 'rider_locations';
    protected $guarded = 'id';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['lat', 'long', 'current', 'rider_id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function rider()
    {
        return $this->belongsTo(Rider::class);
    }
}
