<?php

namespace App\Http\Controllers;

use App\Models\SubCategory;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class SubCategoryController extends Controller
{
    use ApiResponseTrait;

    /**
     * Display a listing of the resource.
     */
    private function validateRequest(Request $request, array $rules)
    {
        try {
            return $request->validate($rules);
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw new HttpResponseException(
                $this->errorResponse(__('messages.validation_failed'), 'messages', 422, [
                    'errors' => $e->errors(),
                ])
            );
        }
    }

    public function index(Request $request)
    {
        $query = SubCategory::with('category', 'products');
        if ($request->filled('id')) {
            $query->where('id', $request->input('id'));
        }

        $subCategories = $query->get();

        return $this->successResponse($subCategories, 'messages', 'sub_categoriesـretrievedـsuccessfully');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $this->validateRequest($request, [
            'category_id' => [
                'required',
                Rule::exists('categories', 'id')->whereNull('deleted_at'),
            ],
            'name' => 'required|array',
            'name.en' => 'required|string|max:255',
            'name.ar' => 'required|string|max:255',
            'image' => 'nullable|image',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = 'storage/'.$request->file('image')->store('subcategories', 'public');
        }

        $subCategory = SubCategory::create($data);

        return $this->successResponse($subCategory, 'messages', 'created');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $subCategory = SubCategory::find($id);
        if (! $subCategory) {
            return $this->errorResponse(__('not_found'), 'messages', 404);
        }
        $data = $this->validateRequest($request, [
            'name' => 'sometimes|array',
            'name.en' => 'required_with:name|string|max:255',
            'name.ar' => 'required_with:name|string|max:255',
            'image' => 'nullable|image',
        ]);

        if ($request->hasFile('image')) {
            if ($subCategory->image) {
                Storage::disk('public')->delete($subCategory->image);
            }
            $data['image'] = 'storage/'.$request->file('image')->store('subcategories', 'public');
        }

        $subCategory->update($data);

        return $this->successResponse($subCategory, 'messages', 'updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $subCategory = SubCategory::find($id);

        if (! $subCategory) {
            return $this->errorResponse(__('not_found'), 'messages', 404);
        }

        $subCategory->delete();

        return $this->successResponse([], 'messages', 'deleted');
    }
}
