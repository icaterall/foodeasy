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
class NearCriteria implements CriteriaInterface
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

        if($this->request->order_mode == 1)
       {  
            $myLat = $this->request->get('myLat');
            $myLon = $this->request->get('myLon');
            
            return $model->select('restaurants.*')
                    ->selectRaw("6371 * acos(
                            cos( radians($myLat) )
                            * cos( radians( latitude ) )
                            * cos( radians( longitude ) - radians($myLon) )
                            + sin( radians($myLat) )
                            * sin( radians( latitude ) )
                        ) AS 'distance'")
                ->selectRaw(" '0' AS 'open_at'")
                ->where('restaurants.active', '1')
                ->where('restaurants.featured', '1')
                ->where('restaurants.available_for_delivery', 1)
                    ->havingRaw(" `distance`  < `delivery_range` ")
                   ->groupBy('id')
                ->orderBy('distance');
        }

       else 
       {
            $myLat = $this->request->get('myLat');
            $myLon = $this->request->get('myLon');
            
            return $model->select('restaurants.*')
                    ->selectRaw("6371 * acos(
                            cos( radians($myLat) )
                            * cos( radians( latitude ) )
                            * cos( radians( longitude ) - radians($myLon) )
                            + sin( radians($myLat) )
                            * sin( radians( latitude ) )
                        ) AS 'distance'")
                ->selectRaw(" '0' AS 'open_at'")
                ->where('restaurants.active', '1')
                ->where('restaurants.featured', '1')
                ->where('restaurants.available_for_pickup', 1)
                    ->havingRaw(" `distance`  < `delivery_range` ")
                   ->groupBy('id')
                ->orderBy('distance');
             }
          }
          else {

            return $model->where('closed', '1')->orderBy('closed');
        }
    }
}
