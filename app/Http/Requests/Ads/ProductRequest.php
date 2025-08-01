<?php

namespace App\Http\Requests\Ads;

use App\Models\SubCategory;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        if ($this->has('sub_category_id')) {
            $subCategory = SubCategory::find($this->sub_category_id);
            $this->merge([
                'sub_category_type' => $subCategory?->name,
            ]);
        }
    }
    /*  protected function prepareForValidation()
      {
          if ($this->has('sub_category_id')) {
              $subCategory = SubCategory::find($this->sub_category_id);

              if ($subCategory) {

                  $nameArray = json_decode($subCategory->name, true);

                  $this->merge([
                      'sub_category_type' => $nameArray['en'] ?? $subCategory->name, // fallback to raw name if not JSON
                  ]);
              }
          }
      }*/

    public function rules(): array
    {
        $subCategoryType = $this->input('sub_category_type');

        $rules = [
            'title' => 'required|string|max:255',
            // 'governorate' => 'required|in:Damascus|دمشق,Aleppo|حلب,Homs|حمص,Hama|حماة,Latakia|اللاذقية,Daraa|درعا,Deir ez-Zor|دير الزور,Raqqa|الرقة,Idlib|إدلب,Sweida|السويداء,Quneitra|القنيطرة,Tartus|طرطوس,Hasakah|الحسكة,Rif Dimashq|ريف دمشق',
            'governorate' => 'required|in:دمشق,حلب,حمص,حماة,اللاذقية,درعا,دير الزور,الرقة,إدلب,السويداء,القنيطرة,طرطوس,الحسكة,ريف دمشق',
            'address_details' => 'nullable|string',
            'description' => 'nullable|string',
            'phone_number' => 'required|string|max:20',
            'email' => 'nullable|email',
            'sub_category_id' => 'required|exists:sub_categories,id',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            'price' => 'nullable|numeric',
            'price_type' => 'nullable|in:free,negotiable,non-negotiable,غير قابل للتفاوض,قابل للتفاوض,مجاني', // 'مجاني','قابل للتفاوض','غير قابل للتفاوض'
            'state' => 'nullable|in:new,used,مستعمل,جديد',
            'lat' => ['required', 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
            'long' => ['required', 'regex:/^[-]?((((1[0-7]\d)|(\d?\d))\.(\d+))|180(\.0+)?)$/'],
            'currency' => ['required', 'in:SYP,EUR,USD,TRY'],
        ];

        if (in_array($subCategoryType, ['animal', 'veterinary', 'supply', 'حيوان', 'بيطري', 'مستلزمات'])) {
            $rules = array_merge($rules, [
                // 'group_type' => 'required|in:animal,veterinary,supply',
                'type' => 'nullable|string|max:255',
                'brand' => 'nullable|string|max:255',
                'age' => 'nullable|string|max:255',
                'gender' => 'nullable|in:male,female,ذكر,أنثى',

                'service_type' => 'nullable|string|max:255',

                'specialization' => 'nullable|string|max:255',
                'service_provider_name' => 'nullable|string|max:255',
                'work_time' => 'nullable|string|max:255',

                'services_price' => 'nullable|string|max:255',

                'vaccinations' => 'nullable|string|max:255',

                'model_or_size' => 'nullable|string|max:255',
                'color' => 'nullable|string|max:255',
                'appropriate_to' => 'nullable|string|max:255',

                'accessories' => 'nullable|string|max:65535',
            ]);
        }

        if (in_array($subCategoryType, ['mobile', 'laptop', 'tv', 'هاتف محمول', 'حاسوب محمول', 'تلفاز'])) {
            $rules = array_merge($rules, [
                'type' => 'nullable|string|max:255',
                'brand' => 'nullable|string|max:255',
                'model' => 'nullable|string|max:255',
                'made_in' => 'nullable|string|max:255',
                'year_of_manufacture' => 'nullable|date',
                'screen_size' => 'nullable|string',
                'warranty' => 'nullable|string|max:255',

                'accessories' => 'nullable|string|max:255',

                'camera' => 'nullable|string|max:255',

                'storage' => 'nullable|string|max:255',

                'color' => 'nullable|string|max:255',
                'supports_sim' => 'nullable|boolean',

                'operation_system' => 'nullable|string|max:255',
                'screen_card' => 'nullable|string|max:255',
                'ram' => 'nullable|string|max:255',
                'processor' => 'nullable|string|max:255',
            ]);
        }
        if (in_array($subCategoryType, ['cars', 'motorcycles', 'bicycles', 'tires & supplies', 'سيارات', 'دراجات نارية', 'دراجات هوائية', 'إطارات ومستلزمات'])) {
            $rules = array_merge($rules, [
                'type' => 'nullable|string|max:255',
                'brand' => 'nullable|string|max:255',
                'model' => 'nullable|string|max:255',
                'year' => 'nullable|string|max:255',
                'kilometers' => 'nullable|string|max:255',
                'fuel_type' => 'nullable|string|max:255',
                'dipstick' => 'nullable|in:normal,automatic,half_automatic,عادي,أوتوماتيك,نصف أوتوماتيك',
                'engine_capacity' => 'nullable|string|max:255',
                'num_of_doors' => 'nullable|string|max:255',
                'topology_status' => 'nullable|string|max:255',
                'size' => 'nullable|string|max:255',
                'color' => 'nullable|string|max:255',
            ]);
        }
        if (in_array($subCategoryType, ['propertys', 'offices', 'lands', 'عقارات', 'مكاتب', 'أراضي'])) {
            $rules = array_merge($rules, [
                'type' => 'nullable|string|max:255',
                'ownership' => 'nullable|string|max:255',
                'contract_type' => 'nullable|string|max:255',
                'num_of_room' => 'nullable|string|max:255',
                'num_of_bathroom' => 'nullable|string|max:255',
                'num_of_balconies' => 'nullable|string|max:255',
                'area' => 'nullable|string|max:255',
                'floor' => 'nullable|string|max:255',
                'furnished' => 'nullable|boolean',
                'age_of_construction' => 'nullable|string|max:65535',
                'readiness' => 'nullable|string',
                'facade' => 'nullable|string|max:255',
                'nature_of_land' => 'nullable|string|max:255',
                'street_width' => 'nullable|string|max:255',
            ]);
        }
        if (in_array($subCategoryType, ['playStation', 'musical instruments', 'books & magazines', 'video games', 'بلاي ستيشن', 'آلات موسيقية', 'كتب ومجلات', 'ألعاب فيديو'])) {
            $rules = array_merge($rules, [
                'type' => 'nullable|string|max:255', // edition
                'model' => 'nullable|string|max:255',
                'storage' => 'nullable|string|max:255',
                'edition' => 'nullable|string|max:255',
                'attached_games' => 'nullable|boolean',
                'num_of_accessories_supplied' => 'nullable|string|max:255',
                'warranty' => 'nullable|string|max:255',
                'date_of_purchase' => 'nullable|string|max:255',

                'color' => 'nullable|string|max:255',
                'brand' => 'nullable|string|max:255',
                'accessories' => 'nullable|string|max:65535',

                'title_of_book' => 'nullable|string|max:255',
                'language' => 'nullable|string|max:255',
                'number_of_copies' => 'nullable|integer|min:0',
                'author' => 'nullable|string|max:255',
                'publishing_house_and_year' => 'nullable|string|max:255',

                'name' => 'nullable|string|max:255',
                'version' => 'nullable|string|max:255',
                'online_availability' => 'nullable|boolean',
            ]);
        }
        if (in_array($subCategoryType, ['fashion', 'beauty products', 'Sports', 'baby supplies', 'medical supplies', 'موضة', 'منتجات تجميل', 'رياضة', 'مستلزمات أطفال', 'مستلزمات طبية'])) {
            $rules = array_merge($rules, [
                'type' => 'nullable|string|max:255',
                'size' => 'nullable|string|max:255',
                'brand' => 'nullable|string|max:255',
                'model' => 'nullable|string|max:255',
                'season' => 'nullable|string|max:255',
                'color' => 'nullable|string|max:255',
                'warranty' => 'nullable|string|max:255',

                'material' => 'nullable|string|max:255',
                'special_characteristics' => 'nullable|string|max:255',
                'accessories' => 'nullable|string|max:65535',

                'age_group' => 'nullable|string|max:255',

                'year_of_manufacture' => 'nullable|string|max:255',
                'max_endurance' => 'nullable|string|max:255',
                'compatible_vehicles' => 'nullable|string|max:255',
            ]);
        }
        if (in_array($subCategoryType, ['miscellaneous', 'furniture', 'متفرقات', 'أثاث'])) {
            $rules = array_merge($rules, [
                'type' => 'nullable|string|max:255',
                'brand' => 'nullable|string|max:255',
                'model' => 'nullable|string|max:255',
                'year_of_manufacture' => 'nullable|string|max:255',
                'size_or_weight' => 'nullable|string|max:255',
                'color' => 'nullable|string|max:255',
                'warranty' => 'nullable|string|max:255',
                'accessories' => 'nullable|string|max:65535',

                'main_specification' => 'nullable|string|max:65535',

                'dimensions' => 'nullable|string|max:255',
                'state_specification' => 'nullable|string|max:65535',
                'made_from' => 'nullable|string|max:255',
            ]);
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'title.required' => __('product.title_required'),
            'governorate.required' => __('product.governorate_required'),
            'governorate.in' => __('product.governorate_invalid'),
            'address_details.string' => __('product.address_details_string'),
            'description.string' => __('product.description_string'),
            'phone_number.required' => __('product.phone_required'),
            'phone_number.max' => __('product.phone_max'),
            'email.email' => __('product.email_invalid'),

            'sub_category_id.required' => __('product.sub_category_required'),
            'sub_category_id.exists' => __('product.sub_category_not_found'),

            'images.array' => __('product.images_array'),
            'images.max' => __('product.images_max'),
            'images.*.image' => __('product.images_must_be_image'),
            'images.*.mimes' => __('product.images_mimes'),
            'images.*.max' => __('product.images_size_max'),

            'price.required' => __('product.price_required'),
            'price.numeric' => __('product.price_numeric'),
            'price_type.in' => __('product.price_type_invalid'),

            'state.in' => __('product.state_invalid'),

            'age.integer' => __('product.age_integer'),
            'age.min' => __('product.age_min'),
            'age.max' => __('product.age_max'),

            'num_of_doors.integer' => __('product.num_of_doors_integer'),
            'num_of_doors.min' => __('product.num_of_doors_min'),

            'num_of_room.integer' => __('product.num_of_room_integer'),
            'num_of_room.min' => __('product.num_of_room_min'),

            'num_of_bathroom.integer' => __('product.num_of_bathroom_integer'),
            'num_of_bathroom.min' => __('product.num_of_bathroom_min'),

            'num_of_balconies.integer' => __('product.num_of_balconies_integer'),
            'num_of_balconies.min' => __('product.num_of_balconies_min'),

            'area.integer' => __('product.area_integer'),
            'area.min' => __('product.area_min'),

            'floor.integer' => __('product.floor_integer'),
            'floor.min' => __('product.floor_min'),

            'street_width.integer' => __('product.street_width_integer'),
            'street_width.min' => __('product.street_width_min'),

            'furnished.boolean' => __('product.furnished_boolean'),
            'readiness.boolean' => __('product.readiness_boolean'),

            'supports_sim.boolean' => __('product.supports_sim_boolean'),
            'online_availability.boolean' => __('product.online_availability_boolean'),
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
