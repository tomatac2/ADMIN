<?php

namespace App\Http\Resources\Api\User\Country;

use App\Http\Resources\Api\User\Page\PageResource;
use App\Http\Resources\Api\User\Shared\PaginationResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CountryCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => CountriesResource::collection($this->collection),
            'pagination' => new PaginationResource($this->resource),
        ];
    }
}
