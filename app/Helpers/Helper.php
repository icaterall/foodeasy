<?php

namespace App\Helpers;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Pagination\Paginator;
use Facades\App\Models\AppSetting;
use Facades\App\Models\Order;
use Session;
use Response;
use Str;
use DateTime;
use DateTimeImmutable;
use DatePeriod;
use DateInterval;
use App\Jobs\updateEarning;
use App\Jobs\sendEmail;
use App\Jobs\sendReadyEmail;
use Facades\App\Models\Earning;
use Facades\App\Models\RestaurantsPayout;
use Facades\App\Services\Emails\OrderStatusEmail;
use Facades\App\Models\OrderOffer;
use Facades\App\Models\RestaurantWrokingDay;
class Helper
{



// -------------------Check if Restaurant is open Now

    public function isOpenAt($StoreID,$today)
    {

      // Get all available Next days
      $nextDay = RestaurantWrokingDay::where('restaurant_id', $StoreID)
      ->Where('available',1)
      ->where('day_id','>',$today)
      ->first();
            
            // Get all available previous days
      $previousDay = RestaurantWrokingDay::where('restaurant_id',$StoreID)
            ->Where('available',1)
            ->where('day_id','<=',$today)
            ->OrderBy('day_id','asc')
            ->first();

      if($nextDay)
      {
                  $when_open_day = $nextDay->day_id;
                  $open_day_string = date('D', strtotime("Sunday +$when_open_day  days")); 
                  $store_open = Carbon::createFromFormat('H:i', $nextDay->open_time);
                  $store_close = Carbon::createFromFormat('H:i', $nextDay->close_time);
                  $store_open_time = $store_open->format('h:i a');  
                  $store_open_hr =$store_open->format('H:i');
                  $store_close_hr =$store_close->format('H:i');                

$OpenDays[]=
[

   'day_id'=>$when_open_day,
   'open_time'=>$store_open_hr,
   'close_time'=>$store_close_hr,
    'open_text'=>$open_day_string.' '.$store_open_time,
];

      return $OpenDays;
      }

      else if($previousDay)
            {
              

                        $when_open_day = $previousDay->day_id;
                        $open_day_string = date('D', strtotime("Sunday +$when_open_day  days")); 
                        $store_open = Carbon::createFromFormat('H:i', $previousDay->open_time);
                        $store_close = Carbon::createFromFormat('H:i', $previousDay->close_time);
                        $store_open_time = $store_open->format('h:i a'); 

                        $store_open_hr =$store_open->format('H:i');
                        $store_close_hr =$store_close->format('H:i'); 

          

          $OpenDays[]=
[

   'day_id'=>$when_open_day,
   'open_time'=>$store_open_hr,
   'close_time'=>$store_close_hr,
    'open_text'=>$open_day_string.' '.$store_open_time,
];

      return $OpenDays;


            }
        
        else
        {
          return 'Permanently closed';
        }

      }

    public function isThisStoreOpen($store)
    {
        // Check if store is available that day
        $now = Carbon::now();
        $day = $now->format('N');

        $storeDay = RestaurantWrokingDay::where('restaurant_id', $store->id)
            ->where('day_id', $day)
            ->where('available', 1)
            ->first();

//if not available today
        if ($storeDay == null) {
           
            $store->closed = 1;
            $open_days = $this->isOpenAt($store->id,$day);   
            if($open_days == 'Permanently closed')
             $store->open_at ='Permanently closed';    
             else $store->open_at = $open_days[0]['open_text'];
        }


else {
        // Check store hours
        $store_open = Carbon::createFromFormat('H:i', $storeDay->open_time);
        $store_close = Carbon::createFromFormat('H:i', $storeDay->close_time);
        $when_open_day = $storeDay->day_id;
        $open_day_string = date('D', strtotime("Sunday +$when_open_day  days"));
        $store_open_time = $store_open->format('h:i a'); 

       
        if ($now >= $store_open && $now <= $store_close) {
            
            $store->closed = 0;
        }       
        
        else if($now < $store_open) // will open soon today

        {
                
                $store->closed = 1;
                $store->open_at = $store_open_time;

        } else

        {
            
            $store->closed = 1;
             $open_days = $this->isOpenAt($store->id,$day);
            $store->open_at =$open_days[0]['open_text'];

        }
   }


return $store;
    }


    public function isStoreListOpen($stores)
    {
        // Check if store is available that day
        $now = Carbon::now();
        $day = $now->format('N');
    
    
foreach ($stores as $store) {

        $storeDay = RestaurantWrokingDay::where('restaurant_id', $store->id)
            ->where('day_id', $day)
            ->where('available', 1)
            ->first();

//if not available today
        if ($storeDay == null) {
           
            $store->closed = 1;
            $open_days = $this->isOpenAt($store->id,$day);   
            if($open_days == 'Permanently closed')
             $store->open_at ='Permanently closed';    
             else $store->open_at = $open_days[0]['open_text'];
        }


else {
        // Check store hours
        $store_open = Carbon::createFromFormat('H:i', $storeDay->open_time);
        $store_close = Carbon::createFromFormat('H:i', $storeDay->close_time);
        $when_open_day = $storeDay->day_id;
        $open_day_string = date('D', strtotime("Sunday +$when_open_day  days"));
        $store_open_time = $store_open->format('h:i a'); 

       
        if ($now >= $store_open && $now <= $store_close) {
            
            $store->closed = 0;
        }       
        
        else if($now < $store_open) // will open soon today

        {
                
                $store->closed = 1;
                $store->open_at = $store_open_time;

        } else

        {
            
            $store->closed = 1;
             $open_days = $this->isOpenAt($store->id,$day);
            $store->open_at =$open_days[0]['open_text'];

        }
   }
}

return $stores;
    }




    public function isStoreOpen($store)
    {
        // Check if store is available that day   
        $now = Carbon::now();
        $day = $now->format('N');
        $storeDay = RestaurantWrokingDay::where('restaurant_id', $store->id)
            ->where('day_id', $day)
            ->where('available', 1)
            ->first();


//if not available today
        if (!$storeDay) {
            
            $store->closed = 1;
            $open_days = $this->isOpenAt($store->id,$day);
            if($open_days != 'Permanently closed')
            {           $store->day_id =$open_days[0]['day_id'];
                        $store->open_time =$open_days[0]['open_time'];
                        $store->close_time =$open_days[0]['close_time'];
                        $store->open_at_time =$open_days[0]['open_text'];
            } else {
                      $store->open_at_time = 'Permanently closed';
                      
            }
        }
else {
        // Check store hours
        $store_open = Carbon::createFromFormat('H:i', $storeDay->open_time);
        $store_close = Carbon::createFromFormat('H:i', $storeDay->close_time);
        $when_open_day = $storeDay->day_id;
        $open_day_string = date('D', strtotime("Sunday +$when_open_day  days"));
        $store_open_time = $store_open->format('h:i a'); 
          $open_time = $store_open->format('H:i'); 
           $close_time = $store_close->format('H:i'); 

        if ($now >= $store_open && $now <= $store_close) {
              
               $store->closed = 0;
                $store->open_at_time = $store_open_time;
                $store->day_id =$day;
                $store->open_time =$open_time;
                $store->close_time =$close_time;
                $store->open_at_time =$store_open_time;
        }       
        
        else if($now < $store_open) // will open soon today

        {
               
                $store->closed = 1;
                $store->open_at_time = $store_open_time;
                $store->day_id =$day;
                $store->open_time =$open_time;
                $store->close_time =$close_time;
                $store->open_at_time =$store_open_time;

        } else

        {
            
            $store->closed = 1;
            $open_days = $this->isOpenAt($store->id,$day);
            $store->day_id =$open_days[0]['day_id'];
            $store->open_time =$open_days[0]['open_time'];
            $store->close_time =$open_days[0]['close_time'];
            $store->open_at_time =$open_days[0]['open_text'];
        }
   }

return $store;
    }




public function findOpenHours($Storeid,$date)
{

    $now = Carbon::now();
    $today = $now->format('Y-m-d');

       $getDayId = strtotime($date);      
       $day_id = date('N',$getDayId);
 
$current_time = strtotime($now);

$frac = 900;
$r = $current_time % $frac;

$new_time = $current_time + ($frac-$r);
$new_time = date('H:i', $new_time);
 
      $OpenHours=RestaurantWrokingDay::where('restaurant_id', $Storeid)
      ->Where('day_id',$day_id)
      ->first();
    $TodayTime = Carbon::parse($new_time);
    $endTodayTime = $TodayTime->addMinutes(45);
    $open_today_time = $endTodayTime->format('H:i');

    $StoreTime = Carbon::parse($OpenHours->open_time);
      $today_date = $StoreTime;


    $endStoreTime = $StoreTime->addMinutes(30);

    $open_store_time = $endStoreTime->format('H:i');



if(($today == $date) && ($this->isOpen($Storeid,$now)))
{
 
$period = new DatePeriod(
  new DateTimeImmutable($open_today_time),
  new DateInterval('PT15M'),
  new DateTimeImmutable($OpenHours->close_time),
  DatePeriod::EXCLUDE_START_DATE
);

}
else
{
  $period = new DatePeriod(
  new DateTimeImmutable($open_store_time),
  new DateInterval('PT15M'),
  new DateTimeImmutable($OpenHours->close_time),
  DatePeriod::EXCLUDE_START_DATE
);

}



$slots = [];

if( ($today == $date) && ($this->isOpen($Storeid,$now)) )
{

   $slots[] = ['value' => 'now', 'time_name' => 'ASAP'];
 
}

foreach ($period as $date) {
  $slots[] = [
    'value' => $date->format('H:i'), 
    'time_name' => $date->format('h:i A')
  ];


}


return $slots;
}



    public function isStoreWillOpenToday($StoreID)
    {
        // Check if store is available that day
        $now = Carbon::now();
        $day_id = $now->format('N');

        $storeDay = RestaurantWrokingDay::where('restaurant_id', $StoreID)
            ->where('day_id', $day_id)
            ->where('available', 1)
            ->first();

        if (!$storeDay) {
            return false;
        }

        // Check store hours
        $store_open = Carbon::createFromFormat('H:i', $storeDay->open_time);
        $store_close = Carbon::createFromFormat('H:i', $storeDay->close_time);

        if ($now <= $store_close) {
            return true;
        }       
        
        return false;
    }




    public function isOpen($StoreID,$full_date)
    {      
      
        // Check if store is available that day
        $full_date = Carbon::parse($full_date);
        


        $day = $full_date->format('N');

        $storeDay = RestaurantWrokingDay::where('restaurant_id', $StoreID)
            ->where('day_id', $day)
            ->where('available', 1)
            ->first();




        if (!$storeDay) {
            return false;
        }

        // Check store hours
        $store_open = Carbon::createFromFormat('H:i', $storeDay->open_time)->format('H:i');
        $store_close = Carbon::createFromFormat('H:i', $storeDay->close_time)->format('H:i');
        $date = $full_date->format('Y-m-d');
      
        $store_open =  Carbon::parse($date.' '.$store_open);
        $store_close =  Carbon::parse($date.' '.$store_close);





        if ($full_date >= $store_open && $full_date <= $store_close) {
          
          
          return true; 
        
        }       
       
        return false;
    }

public function findOpenDaysTime($Storeid)
{
      $OpenDays = RestaurantWrokingDay::where('restaurant_id', $Storeid)
      ->Where('available',1)
      ->get();

      $checktoday = $this->isOpen($Storeid,Carbon::now());

      $calender_days   = [];
      $period = new DatePeriod(
          new DateTime(),
          new DateInterval('P1D'), 
          7 // Apply the interval 6 times on top of the starting date
      );

foreach ($period as $period_day)
  {
  
foreach ($OpenDays as  $OpenDay) {
  if($period_day->format('N') == $OpenDay->day_id)
  {
    $calender_days[]= [
                   'day_text'=>$period_day->format('D, M. d'),
                   'full_date'=>$period_day->format('Y-m-d'),            
                   'time' => $this->findOpenHours($Storeid,$period_day->format('Y-m-d'))
                 ];
               }
           } 
    }

return $calender_days;

}






    public function isAppOpen($open_at,$close_at)
    {
        // Check if store is available that day
        $now = Carbon::now();
        $day = $now->format('N');
        // Check store hours
        $app_open = Carbon::createFromFormat('H:i', $open_at);
        $app_close = Carbon::createFromFormat('H:i', $close_at);

        if ($now >= $app_open && $now <= $app_close) {
            return true;
        }       
        
        return false;
    }



//----------End opens


public function GetOrderInArray($order_id)
{

     $order = Order::find($order_id);
     $restaurant = $order->foods->first()->restaurant;
     $order_status = OrderOffer::where('order_number', $order_id)
            ->first();

foreach($order->foodOrders as $foods){

     $order_items[] = [ 
                        'item_name' => $foods->food->name,
                        'item_size' => $foods->food_size,
                        'item_qty' => $foods->quantity,
                      ]; 
                }



      $date = Carbon::parse($order->created_at);
      $isToday = $date->isToday();
      $isTomorrow = $date->isTomorrow();
        if($isToday == true)
        {
           $isTodayOrTomorrow ='Today';
        } elseif($isTomorrow == true)
          {
             $isTodayOrTomorrow ='Tomorrow'; 
          } else
            {
              $isTodayOrTomorrow = date('D d-m-Y', strtotime($order->created_at));  
            }


$orderdate = Carbon::parse($order->date);

$isOrderToday = $orderdate->isToday();

$isOrderTomorrow = $orderdate->isTomorrow();

  if($isOrderToday == true)
    {
      $isOrderTodayOrTomorrow ='Today';
    } elseif($isOrderTomorrow == true)
      {
       $isOrderTodayOrTomorrow ='Tomorrow'; 
      } else
        {
         $isOrderTodayOrTomorrow = date("D d-m-Y",strtotime($order->date));  
        }

$order_date = $isOrderTodayOrTomorrow;
$order_time = date("h:i a",strtotime($order->time));
$order_subtotal ='MYR '.$order->subtotal;
$restaurant_subtotal ='MYR '.$order->restaurant_subtotal;


      if($order->tax != 0)
      {
        $order_tax ='MYR '.$order->tax;
      } else $order_tax = 0;

      if($order->delivery_fee != 0)
      {
        $delivery_fee ='MYR '.$order->delivery_fee;
      } else $delivery_fee = 0;
      if($order->discount != 0)
      {
        $discount ='MYR '.$order->discount;
      } else $discount = 0;
      if($order->tips != 0)
      {
        $order_tips ='MYR '.$order->tips;
      } else $order_tips = 0;


$active = 'n';
$is_cash = 'n';
if($order->active == 1)
$active = 'y';
if($order->is_cash == 1)
$is_cash = 'y';


 $totalpayment ='MYR '.$order->total;
// Fixed Order info
    $order_information[] = [
        'order_number' => $order->id,
        'order_created_at' => date('h:i a, Y-m-d', strtotime($order->created_at)),
        'order_time' => $order_time,
        'order_date' => date("D d-m-Y",strtotime($order->date)),
        'order_instruction' => $order->order_notes,
        'customer_name' => $order->user->name,
        'customer_phone' => $order->user->mobile,
        'restaurant_name' => $restaurant->name,
        'restaurant_phone' => $restaurant->mobile,
        'restaurant_address' => $restaurant->address,
        'order_subtotal' => $order_subtotal,
        'restaurant_subtotal' => $restaurant_subtotal,
        'customer_tax' => $order_tax,
        'delivery_fee' =>$delivery_fee,
        'discount' => $discount,
        'order_tips' => $order_tips,
        'total_payment' => $totalpayment,
        'user_address' => $order->deliveryAddress->address,
        'active' => $active,
        'is_cash' => $is_cash,
        'promotion_applied' => $order->promo_code,
        'order_items' => $order_items,

    ];
return $order_information;
}
  

//Get App Setting Value
  public function getKeyValue($keyValue)

  {
    
      $ref = AppSetting::get()->toArray();
      $data = array_map(function($obj){
        return (array) $obj;
            }, $ref);
          
      foreach ($data as $key => $value) {

         if($value['key'] == $keyValue)
         {
            return $value['value'];
            break;
         }
       }
       
  }


public function updateEarning(){
           $orders = Order::all();
         foreach ($orders as $key => $order) {
        
         $restaurant = $order->foods->first();
         

         if($restaurant)
        {

         $restaurant = $restaurant->restaurant;
         $orders_payouts = 0; 
         $orders_amount = 0;

         $restaurant_orders = Order::GetRestaurantOrders($restaurant->id);
         
         if($restaurant_orders)
         {
           $orders_amount =  $restaurant_orders->sum('restaurant_total'); 
           $orders_payouts = RestaurantsPayout::where('restaurant_id',$restaurant->id);
            if($orders_payouts)
            $orders_payouts = $orders_payouts->sum('amount');
            
           
            $restaurant_earning = $orders_amount - $orders_payouts;

        Earning::updateOrCreate([
                'restaurant_id' => $restaurant->id,
            ], [
                'restaurant_earning' => $restaurant_earning,
                
            ]);
         }
       }
     }
    }



  public function updateStatus($order,$status_id)
  {

     
       if($order)
       {
    switch ($status_id) {
        case 2:
        
        dispatch(new updateEarning())->delay(now()->addSeconds(10));
        dispatch(new sendEmail($order->id))->delay(now()->addSeconds(3));
        $order->update([ 
              'estimated_time' => 25,
              'order_type' => 'asap',
              'order_status_note' => 'The restaurant is preparing food now',
              ]);
       
        break;  
        case 3:
         $order
         ->update([ 
             
              'estimated_time' => 15,
              'order_status_note' => 'Food is ready for collect',
              ]);

            break;
         
       case 4:
       
       if($order->isdelivery == 1)
        {
          $order->update([ 
                        'estimated_time' => 10,
                        'order_status_note' => 'Order is on the way',
                        ]);
                 } 
                 else {
          $order->update([ 
                        'estimated_time' => 0,
                        'order_status_note' => 'Your food is ready',
                        ]);
                 } 

            break;   
         
       case 5:
        $order->update([ 
              'estimated_time' => 0,
              'order_status_note' => 'Order has been delivered',
              ]);  
            break;  

          }

       }

     
  }
}