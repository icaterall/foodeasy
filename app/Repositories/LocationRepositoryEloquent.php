<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\LocationRepository;
use App\Entities\Location;
use App\Validators\LocationValidator;
use DB;
/**
 * Class LocationRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class LocationRepositoryEloquent extends BaseRepository implements LocationRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Location::class;
    }

    /**
     * Get nearest available riders to specific location
     *
     * @param $latitude
     * @param $longitude
     * @param int $distance
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function nearLocations($latitude, $longitude, $distance = 1, $limit = 10)
    {
        return $this->model->with('rider')
            ->selectRaw('*, (111.045 * DEGREES(ACOS(COS(RADIANS(?))
                            * COS(RADIANS(`lat`))
                            * COS(RADIANS(`long`) - RADIANS(?))
                            + SIN(RADIANS(?))
                            * SIN(RADIANS(`lat`))))) AS distance_in_km',
                [$latitude, $longitude, $latitude])
            ->where('current', 1)
            ->whereHas('rider', function ($query) {
                $query->where('is_available', '1')
                 ->where('is_approved', '1')
                 ->where('is_active', '1');
                })

->whereNotIn('rider_id', DB::table('order_offer_history') ->where('status','accept')->pluck('rider_id'))



            ->having('distance_in_km', '<', $distance)
            ->orderBy('distance_in_km')
            ->limit($limit)
            
            ->get();
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public
    function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

}
