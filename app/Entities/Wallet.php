<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Wallet Account.
 *
 * @package namespace App\Entities;
 */
class Wallet extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * @var string \
     */
    protected $connection = 'mysql2';
    /**
     * @var string
     */
    protected $table = 'riders_wallet';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];

}
