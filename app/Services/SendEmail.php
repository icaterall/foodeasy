<?php

namespace App\services;
use Illuminate\Support\Str;
use Facades\App\Models\User;
use Facades\App\Models\DeliveryAddress;
use Facades\App\Models\Order;
use Illuminate\Support\Facades\Mail;
use Facades\App\Helpers\Helper;
use Carbon\Carbon;
use PDF;
use Route;
use Illuminate\Support\Facades\Config;
class SendEmail
{
    
              //Send Order Email
    public function SendOrderEmail($order_id, $order_status, $send_to, $cc_email ,$order,$total_payment,$order_url,$email_title)
    {    
          $send_from = Helper::getKeyValue('send_from');
          $url = Config::get('app.url');
         $hungerpark = Str::contains($url, ['hungerpark']);
        $eatstation = Str::contains($url, ['eatstation']);
        if($eatstation)
        $url = 'https://eatstation.app/';
        else if($hungerpark)
        $url = 'https://hungerpark.com/';
        else $url = 'https://spoongate.com/';
        

        $order = Order::find($order_id);
        $mailData = [
            'order_id' => $order_id,
            'order_status' => $order_status,
            'total_payment' => $total_payment,
            'order_url' => $order_url,
            'url' => $url,
            'email_title' =>$email_title,
            'order' => $order
        ];
        try {
                Mail::send('include.emails.order_action' , $mailData, function ($message) use ($order_id, $order_status,$send_to, $cc_email ,$send_from) {
                    $message->from($send_from);
                    
                   if($cc_email != null) 
                    {
                        $message->to($send_to)
                        ->cc([$cc_email])
                         ->subject('SpoonGate ::'.$order_status.' --Order #'.$order_id);
                     } else
                     {
                      $message->to($send_to)
                       ->subject('SpoonGate ::'.$order_status.' --Order #'.$order_id);  
                     }
                });
                $response = [
                    'status' => 'success',
                    'msg' => 'Mail sent successfully',
                ];
            } catch (\Exception $e) {
                // Log all  errors
                \Log::info($e);
                $response['status'] = 'error';
                $response['code'] = $e->getCode();
                $response['message'] = $e->getMessage();
             
               
            }
    }







}
