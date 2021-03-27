<?php

namespace App\Entities;

use App\Models\Rider;
use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class ExpoToken.
 *
 * @package namespace App\Entities;
 */
class ExpoToken extends Model implements Transformable
{
    use TransformableTrait;

    protected $table = 'exponent_push_notification_interests';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];

    public function rider()
    {
        return $this->belongsTo(Rider::class, 'id', 'key');
    }

}
