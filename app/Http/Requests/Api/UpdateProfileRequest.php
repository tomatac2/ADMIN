<?php

namespace App\Http\Requests\Api;

class UpdateProfileRequest extends BaseRequest
{

    public function rules(): array
    {
        $id = auth('sanctum')->user()->id;
        return [
            'name'  => ['nullable', 'string', 'max:255'],
            'email' => ['nullable','email', 'unique:users,email,'.$id.',id,deleted_at,NULL'],
            'phone' => ['nullable', 'digits_between:6,15','unique:users,phone,'.$id.',id,deleted_at,NULL'],
            'country_code' => ['nullable', 'string'],
            'gender' => ['nullable', 'in:male,female'],
            'date_of_birth' => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'address.*.type.in' => 'Address type can be either shipping or billing',
        ];
    }
}
