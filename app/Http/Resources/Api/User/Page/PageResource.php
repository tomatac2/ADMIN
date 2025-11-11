<?php

namespace App\Http\Resources\Api\User\Page;

use App\Http\Resources\BaseResource;

class PageResource extends BaseResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
        ];
    }
}
