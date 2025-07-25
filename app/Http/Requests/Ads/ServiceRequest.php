<?php

namespace App\Http\Requests\Ads;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'title' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|string|max:255',
            'governorate' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'days_hours' => 'nullable|string|max:255',
            'phone_number' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            'lat' => ['required', 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
            'long' => ['required', 'regex:/^[-]?((((1[0-7]\d)|(\d?\d))\.(\d+))|180(\.0+)?)$/'],
        ];

        return $rules;
    }

    public function messages(): array
    {
        return [
            'title.required' => __('service.title_required'),
            'title.string' => __('service.title_string'),
            'title.max' => __('service.title_max'),

            'type.required' => __('service.type_required'),
            'type.string' => __('service.type_string'),
            'type.max' => __('service.type_max'),

            'description.string' => __('service.description_string'),

            'price.numeric' => __('service.price_numeric'),

            'governorate.required' => __('service.governorate_required'),
            'governorate.string' => __('service.governorate_string'),
            'governorate.max' => __('service.governorate_max'),

            'location.required' => __('service.location_required'),
            'location.string' => __('service.location_string'),
            'location.max' => __('service.location_max'),

            'days_hours.string' => __('service.days_hours_string'),
            'days_hours.max' => __('service.days_hours_max'),

            'phone_number.required' => __('service.phone_required'),
            'phone_number.string' => __('service.phone_string'),
            'phone_number.max' => __('service.phone_max'),

            'email.email' => __('service.email_invalid'),
            'email.max' => __('service.email_max'),

            'images.array' => __('service.images_array'),
            'images.max' => __('service.images_max'),
            'images.*.image' => __('service.images_image'),
            'images.*.mimes' => __('service.images_mimes'),
            'images.*.max' => __('service.images_max_file'),
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => __('validation.failed'),
            'errors' => $validator->errors(),
        ], 422));
    }
}
