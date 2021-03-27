<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Facades\App\Models\Order;
use Facades\App\Helpers\GetRider;
use Facades\App\Models\Rider;
use Facades\App\Models\RiderHistory;
use Facades\App\Models\OrderOffer;

class NotifyToRider implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

//----------------Send to Riders
  public $order;
  public $uuid;

    /**
     * Create a new job instance.
     *
     * @return void
     */
   public function __construct($order,$uuid)
    {

       $this->order = $order;
       $this->uuid = $uuid;
       
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

  try {

      GetRider::SendNotificationToRider($this->order->id,$this->uuid);
    
     }catch (ValidatorException $e) {
                return $this->sendError($e->getMessage());
            }
    }
}
