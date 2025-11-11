<?php

namespace App\Http\Resources\Api\User\Faq;

use App\Http\Resources\BaseResource;

class FaqResource extends BaseResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'category' => FaqCategoryResource::make($this->faqCategory),
            'title' => @$this->title,
            'description' => @$this->description,
        ];
    }
}
