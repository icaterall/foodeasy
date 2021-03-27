<?php
/**
 * File name: NearCriteria.php
 * Last modified: 2020.05.03 at 10:15:14
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2020
 *
 */

namespace App\Criteria\Restaurants;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class NearCriteria.
 *
 * @package namespace App\Criteria\Restaurants;
 */
class NearRestaurantCriteria implements CriteriaInterface
{

    /**
     * @var array
     */
    private $request;

    /**
     * NearCriteria constructor.
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Apply criteria in query repository
     *
     * @param string $model
     * @param RepositoryInterface $repository
     *
     * @return mixed
     */
    public function apply($model, RepositoryInterface $repository)
    {
      
        if ($this->request->has(['myLat', 'myLon'])) {

            $myLat = $this->request->get('myLat');
            $myLon = $this->request->get('myLon');
            
            return $model->select(DB::raw("SQRT(
                POW(69.1 * (latitude - $myLat), 2) +
                POW(69.1 * ($myLon - longitude) * COS(latitude / 57.3), 2)) AS distance"), "restaurants.*")
                ->selectRaw(" '0' AS 'open_at'")
                ->where('restaurants.active', '1')                
                ->groupBy('id')
                ->havingRaw(" `distance`  < `delivery_range` ")
                ->orderBy('distance');

        } else {

            return $model->where('closed', '1')->orderBy('closed');
        }
    }
}
