<?php

namespace App\Http\Resources\Api\User\Shared;

use Illuminate\Http\Resources\Json\JsonResource;

class PaginationResource extends JsonResource
{

    private $paginationName;

    public function __construct($paginationDate, $name = "page")
    {
        parent::__construct($paginationDate);
        $this->paginationName = $name;
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'current_page'      => (int)$this->currentPage(),
            'from'              => (int)$this->firstItem(),
            'last_page'         => (int)$this->lastPage(),
            'per_page'          => (int)$this->perPage(),
            'to'                => (int)$this->lastItem() ,
            'total'             => (int)$this->total(),
            'count'             => (int)$this->count(),
            'has_next'          => (bool)$this->hasMorePages(),
            'next_page_url'     =>    $this->nextPageUrl(),
            'previous_page_url'   =>    $this->previousPageUrl(),
            'pagination_name' => (string)$this->paginationName,

        ];
    }
}
