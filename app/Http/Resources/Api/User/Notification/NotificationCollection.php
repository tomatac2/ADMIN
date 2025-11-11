<?php

namespace App\Http\Resources\Api\User\Notification;

use App\Http\Resources\Api\User\Shared\PaginationResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class NotificationCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => NotificationResource::collection($this->collection),
            'pagination' => new PaginationResource($this->resource),
        ];
    }
}
