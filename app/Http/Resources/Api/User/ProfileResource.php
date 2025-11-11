<?php

namespace App\Http\Resources\Api\User;

use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'country_code' => $this->country_code,
            'image' => $this->profile_image?->original_url,
            'gender' => $this->gender,
            'is_verified' => (bool)$this->is_verified,
            'date_of_birth' => $this->date_of_birth?->format('Y-m-d'),
            'notifiable' => $this->notifiable
        ];
    }
} 