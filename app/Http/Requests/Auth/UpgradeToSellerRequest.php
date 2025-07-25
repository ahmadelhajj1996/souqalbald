<?php

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UpgradeToSellerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'store_owner_name' => 'required|string|max:255',
            'store_name' => 'required|string|max:255',
            'address' => 'required|string',
            'logo' => 'nullable|image',
            'description' => 'nullable|string',
            'phone' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('users', 'phone')->ignore($this->user()->id),
            ],
            'email' => [
                'nullable',
                'email',
                Rule::unique('users', 'email')->ignore($this->user()->id),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'store_owner_name.required' => __('auth.store_owner_name_required'),
            'store_name.required' => __('auth.store_name_required'),
            'address.required' => __('auth.address_required'),
            'logo.image' => __('auth.logo_must_be_image'),
            'description.required' => __('auth.description_required'),
            'phone.unique' => __('auth.phone_unique'),
            'email.unique' => __('auth.email_unique'),
            'email.email' => __('auth.email_invalid'),
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => 0,
            'message' => __('auth.validation_failed'),
            'result' => ['errors' => $validator->errors()],
        ], 422));
    }
}
