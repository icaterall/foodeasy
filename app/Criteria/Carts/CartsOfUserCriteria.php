<?php


namespace App\Criteria\Carts;

use App\Models\Cart;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class OrdersOfUserCriteria.
 *
 * @package namespace App\Criteria\Orders;
 */
class CartsOfUserCriteria implements CriteriaInterface
{
    /**
     * @var cart
     */
    private $userId;

    /**
     * OrdersOfUserCriteria constructor.
     */
    public function __construct($userId)
    {
        $this->userId = $userId;
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
      if (auth()->user()->hasRole('client')) {
            return $model->where('carts.user_id', $this->userId)->select('carts.*');
            }
       
    }
}
