<?php

namespace App\Http\Resources\Api\User\Faq;

use App\Http\Resources\BaseResource;

class FaqCategoryResource extends BaseResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => @$this->name,
        ];
    }
}
