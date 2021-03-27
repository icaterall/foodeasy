<?php


namespace App\Criteria\Coupons;

use App\Models\Coupon;
use App\Models\Cart;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;
use Carbon\Carbon;
/**
 * Class OrdersOfUserCriteria.
 *
 * @package namespace App\Criteria\Orders;
 */
class getCouponOfUserCriteria implements CriteriaInterface
{
      /**
     * @var int
     */
    private $userId;
    private $coupon;
    /**
     * OrdersOfUserCriteria constructor.
     */
    public function __construct($userId,$coupon)
    {
        $this->userId = $userId;
        $this->coupon = $coupon;
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
       

      

    }
}
