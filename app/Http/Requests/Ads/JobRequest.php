<?php

namespace App\Http\Requests\Ads;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class JobRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'job_type' => 'required|in:full_time,part_time,internship,remotly,temporary_contract',
            'governorate' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'salary' => 'nullable|string|max:255',
            'education' => 'nullable|string|max:255',
            'experience' => 'nullable|string|max:255',
            'type' => 'required|in:job_vacancy,search_for_work',
            'job_title' => 'required|string|max:255',
            'skills' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'work_hours' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'phone_number' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            'lat' => ['required', 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
            'long' => ['required', 'regex:/^[-]?((((1[0-7]\d)|(\d?\d))\.(\d+))|180(\.0+)?)$/'],
            'currency' => ['required', 'in:SYP,EUR,USD,TRY'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => __('job.title_required'),
            'title.string' => __('job.title_string'),
            'title.max' => __('job.title_max'),

            'job_title.required' => __('job.job_title_required'),
            'job_title.string' => __('job.job_title_string'),
            'job_title.max' => __('job.job_title_max'),

            'job_type.required' => __('job.job_type_required'),
            'job_type.in' => __('job.job_type_invalid'),

            'type.required' => __('job.type_required'),
            'type.in' => __('job.type_invalid'),

            'governorate.required' => __('job.governorate_required'),
            'governorate.string' => __('job.governorate_string'),
            'governorate.max' => __('job.governorate_max'),

            'location.required' => __('job.location_required'),
            'location.string' => __('job.location_string'),
            'location.max' => __('job.location_max'),

            'salary.string' => __('job.salary_string'),
            'salary.max' => __('job.salary_max'),

            'education.string' => __('job.education_string'),
            'education.max' => __('job.education_max'),

            'experience.string' => __('job.experience_string'),
            'experience.max' => __('job.experience_max'),

            'skills.string' => __('job.skills_string'),
            'skills.max' => __('job.skills_max'),

            'description.string' => __('job.description_string'),

            'work_hours.string' => __('job.work_hours_string'),
            'work_hours.max' => __('job.work_hours_max'),

            'start_date.date' => __('job.start_date_date'),

            'phone_number.required' => __('job.phone_required'),
            'phone_number.string' => __('job.phone_string'),
            'phone_number.max' => __('job.phone_max'),

            'email.email' => __('job.email_invalid'),
            'email.max' => __('job.email_max'),

            'images.array' => __('job.images_array'),
            'images.max' => __('job.images_max'),
            'images.*.image' => __('job.images_image'),
            'images.*.mimes' => __('job.images_mimes'),
            'images.*.max' => __('job.images_max_file'),
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
