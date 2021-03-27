<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\OfferHistoryRepository;
use App\Entities\OfferHistory;
use App\Validators\OfferValidator;

/**
 * Class OfferHistoryRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class OfferHistoryRepositoryEloquent extends BaseRepository implements OfferHistoryRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return OfferHistory::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

}
