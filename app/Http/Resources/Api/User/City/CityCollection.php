<?php

namespace App\Http\Resources\Api\User\City;

use App\Http\Resources\Api\User\Shared\PaginationResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CityCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => CityResource::collection($this->collection),
            'pagination' => new PaginationResource($this->resource),
        ];
    }
}
