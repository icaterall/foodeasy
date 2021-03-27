<?php

namespace App\Transformers;

use App\Entities\Notification;
use League\Fractal\TransformerAbstract;

class NotificationTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     * @param Notification $notification
     * @return array
     */
    public function transform(Notification $notification)
    {
        return [
            'uuid' => $notification->uuid,
            'title' => $notification->title,
            'body' => $notification->body,
            'data' => json_decode($notification->data, true),
            'badge' => $notification->badge,
            'created_at' => $notification->created_at
        ];
    }
}
