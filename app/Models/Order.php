<?php

namespace App\Models;
use Facades\App\Helpers\Helper;
use Facades\App\Helpers\GetRider;
use Illuminate\Database\Eloquent\SoftDeletes;

use Eloquent as Model;


class Order extends Model
{
    use SoftDeletes;
    public $table = 'orders';
    


    public $fillable = [
        'user_id',
        'order_status_id',
        'order_status_note',
        'tax',
        'restaurant_tax',
        'delivery_fee_restaurant',
        'discount_restaurant',
        'delivery_fee',
        'time',
        'date',
        'tips',
        'discount',
        'service_charge',
        'subtotal',
        'restaurant_subtotal',
        'total',
        'restaurant_total',
        'is_cash',
        'isdelivery',
        'secret',
        'promo_code',
        'hint',
        'order_type',
        'active',
        'payment_status',
        'driver_id',
        'delivery_address_id',
        'estimated_time',
        'job_execute',
        'restaurant_payout_id',
        'is_app',
         'created_at'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'user_id' => 'integer',
        'order_status_id' => 'integer',
        'tax' => 'double',
        'hint' => 'string',
        'date' => 'string',
        'time' => 'string',
        'order_type' => 'string',
        'status' => 'string',
        'payment_id' => 'integer',
        'delivery_address_id' => 'integer',
        'delivery_fee'=>'double',
        'active'=>'boolean',
        'isdelivery'=>'boolean',
        'is_cash'=>'boolean',
        'driver_id' => 'integer',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'user_id' => 'required|exists:users,id',
        'order_status_id' => 'required|exists:order_statuses,id',
        'payment_id' => 'exists:payments,id',
        'driver_id' => 'nullable|exists:users,id',
    ];

    /**
     * New Attributes
     *
     * @var array
     */
    protected $appends = [
        'custom_fields',
        
    ];

    public function customFieldsValues()
    {
        return $this->morphMany('App\Models\CustomFieldValue', 'customizable');
    }

    public function getCustomFieldsAttribute()
    {
        $hasCustomField = in_array(static::class,setting('custom_field_models',[]));
        if (!$hasCustomField){
            return [];
        }
        $array = $this->customFieldsValues()
            ->join('custom_fields','custom_fields.id','=','custom_field_values.custom_field_id')
            ->where('custom_fields.in_table','=',true)
            ->get()->toArray();

        return convertToAssoc($array,'name');
    }



      public function GetRestaurantOrders($restaurant_id)
     {

            return $this->join("food_orders", "orders.id", "=", "food_orders.order_id")
                ->join("foods", "foods.id", "=", "food_orders.food_id")
                ->where('foods.restaurant_id', $restaurant_id)
                ->where('orders.payment_status', 1)
                ->where('orders.active', 1)
                ->orderBy('orders.id', 'DESC')
                ->groupBy('orders.id')
                
                ->select('orders.*')->get();
            
     }


        public function getRider($order)
        {
           
            if(($order->isdelivery == 1) AND ($order->has_riders == 0) AND ($order->driver_id == null))
            {
                
                    $distance = Helper::getKeyValue('rider_distance');
                    $limit = Helper::getKeyValue('rider_limit');
                    $restaurant = $order->foods->first()->restaurant;
                    $lat = $restaurant->latitude;
                    $long = $restaurant->longitude;

                   $riders = GetRider::findRider($lat,$long,$distance,$limit);
               
              if($riders)
              {
                foreach ($riders as $rider) { 
                    $rider_ids = [];
                     foreach ($riders as $key => $rider) {
                      $rider_id = Rider::find($rider->rider_id)->uuid;
                       $rider_ids[] = $rider_id;
                     }

                 }
                 

                 return $rider_ids;
                   
                }

             return null;  
            } 
            
          
          }
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function driver()
    {
        return $this->belongsTo(\App\Models\User::class, 'driver_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function orderStatus()
    {
        return $this->belongsTo(\App\Models\OrderStatus::class, 'order_status_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     **/
    public function foodOrders()
    {
        return $this->hasMany(\App\Models\FoodOrder::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     **/
    public function foods()
    {
        return $this->belongsToMany(\App\Models\Food::class, 'food_orders');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function payment()
    {
        return $this->belongsTo(\App\Models\Payment::class, 'payment_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function deliveryAddress()
    {
        return $this->belongsTo(\App\Models\DeliveryAddress::class, 'delivery_address_id', 'id');
    }
    
}
