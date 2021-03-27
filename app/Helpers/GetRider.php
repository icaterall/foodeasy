<?php

namespace App\Helpers;

use Carbon\Carbon;
use Facades\App\Models\OrderOffer;
use Facades\App\Models\Rider;
use App\Models\RiderHistory;
use Facades\App\Models\Order;
use GuzzleHttp\Client;
use App\Http\Controllers\Api\ApiController;
use App\Repositories\LocationRepositoryEloquent;
use App\Transformers\NearLocationTransformer;
use App\Transformers\UserWithCurrentLocationTransformer;
use Session;
use Response;
use Str;
use DateTime;
use DateTimeImmutable;
use DatePeriod;
use DateInterval;
use Log;
class GetRider
{
     public function __construct(LocationRepositoryEloquent $locationRepositoryEloquent)
    {
        $this->location = $locationRepositoryEloquent;
    }




    /**
     * Get near available riders to specific location
     * @param Request $request
     * @return mixed
     */
    public function findRider($lat,$long,$distance,$limit)
    {
        try {

            return  $data = $this->location->nearLocations($lat, $long, $distance, $limit);
           $this->respond(fractal()->collection($data, new NearLocationTransformer())->toArray());
        } catch (\Exception $exception) {
            echo "Error: " . $exception->getMessage();
        }
    }

  


             public function SendNotificationToRider($order_id,$uuid)
                  {

                    $now = Carbon::now();
                    $rider = Rider::where('uuid',$uuid)->first();
                    $order = Order::find($order_id);
                    


                   if($order->driver_id == Null)
                          { 

                    $rider_history = RiderHistory::where('order_number',$order_id)
                                ->where('rider_id',$rider->id)
                                ->first();
                    
                  
                    $offers = OrderOffer::where('order_number',$order_id)->first();                         
                  

                 $check_rider_state = RiderHistory::where('order_number',$order_id)
                                ->where('status','pending')
                                ->first();

                      if($check_rider_state != null)          
                   
                   {
                    
                    if($now->diffInSeconds($check_rider_state->created_at) > 37)
                                                 // Go and send to the next rider
                                                {
                   
                                                 $check_rider_state->update(['status' => 'ignore']);
                                               }
                    }


                                if($rider_history == Null) //rider didn't recive any notification
                                       
                                  {        
                                    $riderHistory = new RiderHistory();
                                    $riderHistory->order_offer_id = $offers->id;
                                    $riderHistory->order_number = $order_id;
                                    $riderHistory->status = 'pending';
                                    $riderHistory->rider_id = $rider->id;
                                    $riderHistory->save();
                                  }
                       



                $offers = OrderOffer::where('order_number',$order->id)->first();
                $client = new \GuzzleHttp\Client();   
                $endpoint = "https://riderapi.spoongate.com/api/notifications";    

                  try
                  {
                  $response = $client->request('POST', $endpoint, ['query' => [
                              'user_uuid'=> $uuid,
                              'title' => 'Order # '.$order_id,
                              'body' => 'You have new order please check it before next 30s',
                              'data' => $offers->data
                  ]]);
                  
                  $statusCode = $response->getStatusCode();
                  $content = $response->getBody();
                  $content = json_decode($response->getBody(), true);
                }

                    catch (Exception $e)
                       {
                       echo "Error: " . $e->getMessage();
                       }
                 }
           }

  

}