<?php
/**
 * File name: Coupon.php
 * Last modified: 2020.08.23 at 19:56:12
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2020
 */

namespace App\Models;
use App\Models\Cart;
use Carbon\Carbon;
use Eloquent as Model;

/**
 * Class Coupon
 * @package App\Models
 * @version August 23, 2020, 6:10 pm UTC
 *
 * @property string code
 * @property double discount
 * @property string discount_type
 * @property string description
 * @property dateTime expires_at
 * @property boolean enabled
 */
class Coupon extends Model
{

    public $table = 'coupons';
    


    public $fillable = [
        'code',
        'discount',
        'discount_type',
        'description',
        'single_use',
        'minimum_order',
        'restaurant_id',
        'user_id',
        'expires_at',
        'enabled',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'code' => 'string',
        'discount' => 'double',
        'discount_type' => 'string',
        'description' => 'string',
        'expires_at' => 'datetime',
        'enabled' => 'boolean'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'code' => 'required|unique:coupons|max:50',
        'discount' => 'required|numeric|min:0',
        'discount_type' => 'required',
        'expires_at' => 'required|date|after_or_equal:tomorrow'
    ];

    /**
     * New Attributes
     *
     * @var array
     */
    protected $appends = [
        
    ];


        public function validateCoupon($user_id,$coupon)
        {

                $subtotal = 0;
                $user_email = User::find($user_id)->email;
                $carts = Cart::where('user_id',$user_id)->get();

                $parts = explode('@', $user_email);
                $domain = array_pop($parts);
                


                foreach ($carts as $key => $cart) {
                  $subtotal = $subtotal + $cart['total_item'];
                }

                $promo = Coupon::where('code', $coupon)->first();
                $restaurant = Cart::where('user_id',$user_id)->first()->food->restaurant;

                if (!empty($promo) ) 

                {

                    if($coupon != 'spoon4siswa' and $coupon != 'SPOON4SISWA')
                    {
                    if ($promo->expiration_date != '0000-00-00 00:00:00') {
                        $today_date = Carbon::now();
                        $expire_date = $promo->expiration_date;
                        $data_difference = ($today_date->diffInDays($promo->expiration_date, false)) + 1;  //false param
                    }

                    if (($promo->restaurant_id != null) and ($promo->restaurant_id != $restaurant->id)) {
                        $message = 'This coupon cannot be used for this restaurant';
                        
                    } elseif (($promo->minimum_order != null) and ($promo->minimum_order > $subtotal)) {
                        $message = 'The minimum order for this coupon is MYR'.$promo->minimum_order;
                       
                    } elseif (($promo->user_id != null) and ($promo->user_id != $user_id)) {
                        $message = 'You cannot use this coupon';
                       
                    } elseif (($promo->expiration_date != '0000-00-00 00:00:00') and ($data_difference <= 0)) {
                        $message = 'Coupon has been expired';
                       
                    } elseif (($promo->single_use == 1) and ((Order::where('promo_code', $promo->code)->where('user_id', $user_id)->first()) != null)) {
                        $message = 'You have used this coupon before';
                       
                    } elseif ($promo->enabled == 0) {
                        $message = 'This coupon is not avilable';
                    } 

                    else {
                          Cart::where('user_id',$user_id)->update(['coupon_id' =>  $promo->id]);
                          $message = 'This coupon applied successfully';
                         }
                    }

       elseif ( $domain == 'siswa.ukm.edu.my' ) {

                    if ($promo->expiration_date != '0000-00-00 00:00:00') {
                        $today_date = Carbon::now();
                        $expire_date = $promo->expiration_date;
                        $data_difference = ($today_date->diffInDays($promo->expiration_date, false)) + 1;  //false param
                    }

                    if (($promo->restaurant_id != null) and ($promo->restaurant_id != $restaurant->id)) {
                        $message = 'This coupon cannot be used for this restaurant';
                        
                    } elseif (($promo->minimum_order != null) and ($promo->minimum_order > $subtotal)) {
                        $message = 'The minimum order for this coupon is MYR'.$promo->minimum_order;
                       
                    } elseif (($promo->user_id != null) and ($promo->user_id != $user_id)) {
                        $message = 'You cannot use this coupon';
                       
                    } elseif (($promo->expiration_date != '0000-00-00 00:00:00') and ($data_difference <= 0)) {
                        $message = 'Coupon has been expired';
                       
                    } elseif (($promo->single_use == 1) and ((Order::where('promo_code', $promo->code)->where('user_id', $user_id)->first()) != null)) {
                        $message = 'You have used this coupon before';
                       
                    } elseif ($promo->enabled == 0) {
                        $message = 'This coupon is not avilable';
                    } 

                    else {
                          Cart::where('user_id',$user_id)->update(['coupon_id' =>  $promo->id]);
                          $message = 'UKM coupon applied successfully';
                         }

                } else $message = 'This coupon only for UKM students';


           }   
           else $message = 'We are unable to verify this coupon, please check if it is correct';


                return $message;
        }

    
}
