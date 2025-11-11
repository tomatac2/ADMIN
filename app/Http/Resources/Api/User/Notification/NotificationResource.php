<?php

namespace App\Http\Resources\Api\User\Notification;

use App\Http\Resources\BaseResource;

class NotificationResource extends BaseResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => @$this->data['title'],
            'message' => @$this->data['message'],
            'type' => @$this->data['type'],
            'is_read' => (bool) $this->read_at,
            'time' => formatNotificationTime($this->created_at, $userTz ?? config('app.timezone')),
        ];
    }
}
