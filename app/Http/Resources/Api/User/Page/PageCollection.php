<?php

namespace App\Http\Resources\Api\User\Page;

use App\Http\Resources\Api\User\Shared\PaginationResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PageCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => PageResource::collection($this->collection),
            'pagination' => new PaginationResource($this->resource),
        ];
    }
}
