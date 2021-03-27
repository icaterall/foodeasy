<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Profile.
 *
 * @package namespace App\Entities;
 */
class Profile extends Model implements Transformable
{
    use TransformableTrait;
    /**
     * @var string
     */
    protected $guarded = 'id';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['address', 'region', 'photo', 'driving_license', 'roa_tax', 'bike_photo', 'ic_number',
        'coverage_area', 'bank_name', 'account_number', 'rider_id'];

}
