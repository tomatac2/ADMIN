<?php

namespace App\Http\Resources\Api\User\State;

use App\Http\Resources\Api\User\Page\PageResource;
use App\Http\Resources\Api\User\Shared\PaginationResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class StateCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => StateResource::collection($this->collection),
            'pagination' => new PaginationResource($this->resource),
        ];
    }
}
