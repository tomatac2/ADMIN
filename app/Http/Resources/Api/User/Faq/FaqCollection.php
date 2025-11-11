<?php

namespace App\Http\Resources\Api\User\Faq;

use App\Http\Resources\Api\User\Shared\PaginationResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class FaqCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => FaqResource::collection($this->collection),
            'pagination' => new PaginationResource($this->resource),
        ];
    }
}
