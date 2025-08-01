<?php

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class SellerRegisterRequest extends FormRequest
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
            'cover_image' => 'nullable|image',
            'description' => 'nullable|string',
            'phone' => 'required|string|max:20|unique:users,phone',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ];
    }

    public function messages(): array
    {
        return [
            'store_owner_name.required' => __('auth.store_owner_name_required'),
            'store_name.required' => __('auth.store_name_required'),
            'address.required' => __('auth.address_required'),
            'logo.image' => __('auth.logo_must_be_image'),
            'cover_image.image' => __('auth.logo_must_be_image'),
            'description.required' => __('auth.description_required'),
            'phone.required' => __('auth.phone_required'),
            'email.required' => __('auth.email_required'),
            'phone.unique' => __('auth.phone_unique'),
            'email.unique' => __('auth.email_unique'),
            'email.email' => __('auth.email_invalid'),
            'password.required' => __('auth.password_required'),
            'password.confirmed' => __('auth.password_mismatch'),
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
