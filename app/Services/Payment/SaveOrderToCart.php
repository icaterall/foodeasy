<?php

namespace App\services\Payment;;
use Illuminate\Http\Request;
use Facades\App\Helpers\Helper;
use Facades\App\Helpers\SendSMS;
use Facades\App\Models\DeliveryAddress;
use Facades\App\Models\User;
use Facades\App\Models\Cart;
use Facades\App\Models\Order;
use Facades\App\Models\Food;
use Facades\App\Models\Extra;
use Facades\App\Models\Variation;
use Facades\App\Models\ExtraGroup;
use Facades\App\Models\FoodOrder;
use Facades\App\Models\FoodOrderExtra;
use Illuminate\Support\Facades\Config;
use Facades\App\Services\SendEmail;
use Auth;
use Session;
use Response;
use Redirect;
use Carbon\Carbon;
use Illuminate\Support\Str;
class SaveOrderToCart
{

    public function SaveToOrders($user_id,$delivery_address_id)
    {
     
     $address = DeliveryAddress::where('user_id',$user_id)->first();
     
     if($delivery_address_id != null)
      $address_id = $delivery_address_id;

      else if($address != null)
      $address_id = $address->id;
    
    else $address_id = null;
     
     $carts = Cart::GetAppFinalCart($user_id);
     $cart_amount = Cart::appCart($user_id);
     $restaurant = Food::find($carts['items'][0]['food_id'])->restaurant;
     if($cart_amount['order_type'] == 'preorder')
     {
     $order_time = $cart_amount['time'];
     $order_date = $cart_amount['date'];
     $preorder_type = 'preorder';     
   } else
    {
      $preorder_type = 'asap';
      $mytime=Carbon::now();
      $order_time  = date("h:i a", strtotime($mytime));
      $order_date  = date("Y-m-d ", strtotime($mytime));
    }

if($cart_amount['isdelivery'] != 1)
  {
    $order_type = 0;
    $estimated_time = $restaurant->preparing_time;
  }else $order_type = 1;
   
   $estimated_time = $restaurant->preparing_time + 10;
   $secret = Str::random(30).time();

         $order = Order::create([
                'user_id' => $user_id,
                'order_status_id' => 1,
                'tax' => $cart_amount['tax_value'],
                'service_charge' => $cart_amount['service_charge'],
                'restaurant_tax' => $cart_amount['tax_restaurant_value'],
                'delivery_fee' => $cart_amount['delivery_fee'],
                'delivery_fee_restaurant' => $cart_amount['delivery_restaurant_fee'],
                'time' => $order_time,
                'date' => $order_date,
                'tips' => 0,
                'discount' => $cart_amount['discount'],
                'discount_restaurant' => $cart_amount['discount_restaurant'],
                'subtotal' => $cart_amount['subtotal'],
                'restaurant_subtotal' => $cart_amount['subtotal_restaurant'],
                'total' => $cart_amount['total'],
                'restaurant_total' => $cart_amount['restaurant_total'],
                'is_cash' => 1,
                'isdelivery' => $cart_amount['isdelivery'],
                'secret' => $secret,
                'promo_code' => $cart_amount['code'],
                'hint' => Session::get('order_instruction'),
                'order_type' => $cart_amount['order_type'],
                'active' => 1,
                'is_app' => 1,
                'payment_status' => 1,
                'estimated_time' => $estimated_time,
                'delivery_address_id' => $address_id
              ]);
   
  //-----------save order food
  foreach ($carts['items'] as $key => $food) {

      $food_order = FoodOrder::create([
      'order_id' => $order->id,
      'food_id' => $food['food_id'],
      'food_size' => $food['food_size'],
      'quantity' => $food['quantity'],
      'restaurant_price' => $food['total_price_restaurant'],
      'price' => $food['total_price'],
      'food_instruction' => $food['food_instruction'],
      'food_price' => $food['food_price'],
      'food_price_restaurant' => $food['food_price_restaurant'],
     ]);
 
  $food_order_id = $food_order->id;
  $extras = Cart::find($food['cart_id'])->extras;
 $extra_variations = Cart::find($food['cart_id'])->extra_variations;  
   if(count($extras)>0)
//------------------Save Extra
   {
     foreach ($extras as $key => $extra) {


       FoodOrderExtra::create([
        'extra_id' => $extra->id,
        'extra_group_id' => $extra->extra_group_id,
        'food_order_id' => $food_order_id,
        'price' => $extra->price,
        'restaurant_price' => $extra->restaurant_price
       ]);
     }
   }

   if(count($extra_variations)>0)
//------------------Save Extra
   {
     foreach ($extra_variations as $key => $extra_variation) {
$group_id = Extra::find($extra_variation->extra_id);
       FoodOrderExtra::create([
        'extra_id' => $extra_variation->extra_id,
        'extra_group_id' => $group_id->extra_group_id,
        'food_order_id' => $food_order_id,
        'price' => $extra_variation->price,
        'restaurant_price' => $extra_variation->restaurant_price
       ]);
     }
   }
 }
return $order->id;
}



//----------Delete Cart if success

    public function Destroy_cart($user_id)
    {
    try { 
        

        $cart = Cart::where('user_id',$user_id)->delete(); 
       
        
         } catch (ModelNotFoundException $e) {
         // Handle the error.
        }
    }
 


}


