<?php

namespace App\Http\Controllers;

use App\Models\Offer;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class OfferController extends Controller
{
    use ApiResponseTrait;

    public function store(Request $request)
    {
        $data = $request->validate([
            'offerable_type' => 'required|in:product,category,sub_category',
            'offerable_id' => 'required|integer',
            'type' => 'required|in:bogo,discount',
            'description' => 'nullable|string',
            'discount_percentage' => 'nullable|integer|min:1|max:100',
            'image' => ['nullable','image']
        ]);

        if ($data['type'] === 'discount' && empty($data['discount_percentage'])) {
            return $this->errorResponse('discount_percentage_required', 422);
        }

        if ($request->hasFile('image')) {
            $logoPath = $request->file('image')->store('sellers/offers', 'public');
            $data = array_merge($data, ['image' => $logoPath]);
        }

        $typeMap = [
            'product' => \App\Models\Product::class,
            'category' => \App\Models\Category::class,
            'sub_category' => \App\Models\SubCategory::class,
        ];

        $data['offerable_type'] = $typeMap[$data['offerable_type']];

        $offer = Offer::create($data);

        return $this->successResponse(
            ['offer' => $offer],
            'offer_created_successfully'
        );
    }

    public function index()
    {
        $offers = Offer::with('offerable')->get()->map(function (Offer $o) {
            return [
                'id' => $o->id,
                'type' => $o->type,
                'description' => $o->description,
                'discount' => $o->discount_percentage,
                'image' => $o->image,
                'on' => [
                    'type' => class_basename($o->offerable_type),
                    'data' => $o->offerable,
                ],
            ];
        });

        return $this->successResponse(
            ['offers' => $offers],
            'offers_retrieved_successfully'
        );
    }
}
