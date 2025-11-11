<?php

namespace App\Http\Requests\Api;

use App\Rules\MatchCurrentPassword;

class UpdatePasswordRequest extends BaseRequest
{

    public function rules(): array
    {
        return [
            'current_password' => ['required','string', new MatchCurrentPassword],
            'password' => ['required','string','min:8'],
            'password_confirmation' => ['required','min:8','same:password'],
        ];
    }

}
