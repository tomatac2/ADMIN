<?php

namespace App\Http\Resources\Api\User\Country;

use App\Http\Resources\Api\StateResource;
use App\Http\Resources\BaseResource;
use Illuminate\Http\Request;

class CountriesResource  extends BaseResource
{
  protected $showSensitiveAttributes = true;

  public static $wrap = null;

  /**
   * Transform the resource into an array.
   *
   * @return array<string, mixed>
   */
  public function toArray(Request $request): array
  {
    return [
      'id' => $this->id,
      'name' => $this->name,
      'calling_code' => $this->calling_code,
      'flag' => $this->flag,
    ];
  }
}
