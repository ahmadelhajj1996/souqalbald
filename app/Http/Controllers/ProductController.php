<?php

namespace App\Http\Controllers;

use App\Http\Requests\Ads\ProductRequest;
use App\Models\AnimalProductDetail;
use App\Models\CarProductDetail;
use App\Models\DevicesProductDetail;
use App\Models\ElectronicsProductDetail;
use App\Models\EntertainmentProductDetail;
use App\Models\Favorite;
use App\Models\MiscellaneousProductDetail;
use App\Models\Product;
use App\Models\RealEstateProductDetail;
use App\Models\Review;
use App\Models\SubCategory;
use App\Models\Violation;
use App\Traits\ApiResponseTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request)
    {
        $query = Product::with([
            'animalProductDetails',
            'deviceDetails',
            'carProductDetails',
            'realEstateProductDetails',
            'entertainmentProductDetails',
            'miscellaneousProductDetails',
            'electronicsProductDetails',
            'category',
            'subCategory',
            'images',
            'reviews.user',
            'costs',
        ]);
        if ($request->filled('title')) {
            $query->where('title', 'like', '%'.$request->title.'%');
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->filled('sub_category_id')) {
            $query->where('sub_category_id', $request->sub_category_id);
        }
        if ($request->filled('id')) {
            $id = $request->id;
            Product::where('id', $id)->increment('views', 1);
            $query->where('id', $request->id);
        }
        if ($request->filled('price') && $request->price == 0) {
            $query->where('price', 0);
        }
        if ($request->filled('price_min')) {
            $query->where('price', '>=', $request->price_min);
        }
        if ($request->filled('price_max')) {
            $query->where('price', '<=', $request->price_max);
        }
        if ($request->filled('is_featured')) {
            $query->where('is_featured', filter_var($request->is_featured, FILTER_VALIDATE_BOOLEAN));
        }
        if ($request->filled('seller_id')) {
            $query->where('added_by', $request->seller_id);
        }
        if ($request->filled('newest')) {

            $threeDaysAgo = Carbon::now()->subDays(3);
            $query->where('created_at', '>=', $threeDaysAgo)
                ->orderBy('created_at', 'desc');
        }
        $query->orderBy('is_featured', 'desc')
            ->orderBy('created_at', 'desc');
        $products = $query->get()->map(function (Product $p) {
            $detailRels = [
                'animalProductDetails',
                'deviceDetails',
                'carProductDetails',
                'realEstateProductDetails',
                'entertainmentProductDetails',
                'miscellaneousProductDetails',
                'electronicsProductDetails',
            ];

            $details = null;
            foreach ($detailRels as $rel) {
                $relData = $p->{$rel};

                if ($relData instanceof Collection && $relData->isNotEmpty()) {
                    $details = $relData->first();
                    break;
                }

                if ($relData instanceof Model) {
                    $details = $relData;
                    break;
                }
            }

            return [
                'product' => $p->only([
                    'id',
                    'title',
                    'description',
                    'price',
                    'final_price',
                    'category_id',
                    'sub_category_id',
                    'added_by',
                    'address_details',
                    'long',
                    'lat',
                    'seller_phone',
                     'is_active' ,                 
                    'created_at',
                    'updated_at',
                ]),
                'costs' => $p->costs,
                'category' => $p->category,
                'subCategory' => $p->subCategory,
                // 'costs' => $p->costs()->get(['cost_after_change','to_currency',]),
                // 'category' => $p->category->only(['id','name','image']),
                // 'subCategory' => $p->subCategory->only(['id','name','image']),
                'images' => $p->images->pluck('image'),
                'details' => $details,
                'reviews' => $p->reviews->only(['id', 'rate', 'comment', 'created_at'])
                    ->map(function ($r) use ($p) {
                        $r['user'] = $p
                            ->reviews
                            ->firstWhere('id', $r['id'])
                            ->user
                            ->only(['id', 'name']);

                        return $r;
                    }),
            ];
        });

        return $this->successResponse(
            ['products' => $products],
            'products_retrieved_successfully'
        );
    }

    public function myProducts(Request $request)
    {
        $userId = Auth::id();

        $query = Product::with([
            'category',
            'subCategory',
            'images',
            'reviews.user',
            'animalProductDetails',
            'deviceDetails',
            'carProductDetails',
            'realEstateProductDetails',
            'entertainmentProductDetails',
            'miscellaneousProductDetails',
            'electronicsProductDetails',
            'costs',
        ])->where('added_by', $userId);

        if ($request->filled('is_featured')) {
            $query->where('is_featured', filter_var($request->is_featured, FILTER_VALIDATE_BOOLEAN));
        }
        if ($request->filled('price') && $request->price == 0) {
            $query->where('price', 0);
        }

        $products = $query->get()->map(function (Product $p) {
            $detailRels = [
                'animalProductDetails',
                'deviceDetails',
                'carProductDetails',
                'realEstateProductDetails',
                'entertainmentProductDetails',
                'miscellaneousProductDetails',
                'electronicsProductDetails',
            ];

            $details = null;
            foreach ($detailRels as $rel) {
                $relData = $p->{$rel};
                if ($relData instanceof Collection && $relData->isNotEmpty()) {
                    $details = $relData->first();
                    break;
                }
                if ($relData instanceof Model) {
                    $details = $relData;
                    break;
                }
            }

            return [
                'product' => $p->only([
                    'id',
                    'title',
                    'description',
                    'price',
                    'final_price',
                    'category_id',
                    'sub_category_id',
                    'added_by',
                    'views',
                    'favorites_number',
                    'is_featured',
                    'long',
                    'lat',
                    'seller_phone',
                    'created_at',
                    'updated_at',
                ]),
                'costs' => $p->costs,
                'category' => $p->category,
                'subCategory' => $p->subCategory,
                'images' => $p->images->pluck('image'),
                'details' => $details,
                'reviews' => $p->reviews->map->only(['id', 'rate', 'comment', 'created_at'])
                    ->map(function ($r) use ($p) {
                        $r['user'] = $p
                            ->reviews
                            ->firstWhere('id', $r['id'])
                            ->user
                            ->only(['id', 'name']);

                        return $r;
                    }),
            ];
        });

        return $this->successResponse(
            ['products' => $products],
            'user_products_retrieved_successfully'
        );
    }

    public function store(ProductRequest $request)
    {
        $data = $request->except(['images', 'age', 'weight', 'breed', 'group_type', 'gender', 'specialization', 'service_provider_name', 'work_time', 'vaccinations', 'model_or_size', 'appropriate_to', 'operation_system', 'screen_card', 'ram', 'processor', 'device_type']);

        $user = auth()->user();
        $data['added_by'] = $user->id;
        $sub_category = SubCategory::find($request->sub_category_id);
        $data['category_id'] = $sub_category->category_id;
        $name = $sub_category->name;
        $product = Product::create($data);

        if (in_array($name, ['animal', 'veterinary', 'supply', 'حيوان', 'بيطري', 'مستلزمات'])) {
            $product->animalProductDetails()->create([
                // 'group_type'             => $request->sub_category_type,
                'type' => $request->type,
                'brand' => $request->brand,
                'age' => $request->age,
                'gender' => $request->gender,
                'service_type' => $request->service_type,
                'specialization' => $request->specialization,
                'service_provider_name' => $request->service_provider_name,
                'work_time' => $request->work_time,
                'services_price' => $request->services_price,
                'vaccinations' => $request->vaccinations,
                'model_or_size' => $request->model_or_size,
                'color' => $request->color,
                'appropriate_to' => $request->appropriate_to,
                'accessories' => $request->accessories,
            ]);
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $image) {
                    if ($index >= 5) {
                        break;
                    }

                    $product->images()->create([
                        'image' => $image->store('products', 'public'),
                    ]);
                }
            }

            return $this->successResponse($product->load(['images', 'animalProductDetails']), 'product', 'product_created_successfully');
        }

        if (in_array($name, ['mobile', 'laptop', 'tv', 'هاتف محمول', 'حاسوب محمول', 'تلفاز'])) {
            $product->deviceDetails()->create([
                // 'device_type'        => $request->sub_category_type,
                'type' => $request->type,
                'brand' => $request->brand,
                'model' => $request->model,
                'made_in' => $request->made_in,
                'year_of_manufacture' => $request->year_of_manufacture,
                'screen_size' => $request->screen_size,
                'warranty' => $request->warranty,
                'camera' => $request->camera,
                'storage' => $request->storage,
                'color' => $request->color,
                'supports_sim' => $request->supports_sim,
                'operation_system' => $request->operation_system,
                'screen_card' => $request->screen_card,
                'ram' => $request->ram,
                'processor' => $request->processor,
                'accessories' => $request->accessories,
            ]);
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $image) {
                    if ($index >= 5) {
                        break;
                    }

                    $product->images()->create([
                        'image' => $image->store('products', 'public'),
                    ]);
                }
            }

            return $this->successResponse($product->load(['images', 'deviceDetails']), 'product', 'product_created_successfully');
        }
        if (in_array($name, ['cars', 'motorcycles', 'bicycles', 'tires & supplies', 'سيارات', 'دراجات نارية', 'دراجات هوائية', 'إطارات ومستلزمات'])) {
            $product->carProductDetails()->create([
                // 'group_type'        => $request->sub_category_type,
                'type' => $request->type,
                'brand' => $request->brand,
                'model' => $request->model,
                'year' => $request->year,
                'kilometers' => $request->kilometers,
                'fuel_type' => $request->fuel_type,
                'dipstick' => $request->dipstick,
                'engine_capacity' => $request->engine_capacity,
                'num_of_doors' => $request->num_of_doors,
                'topology_status' => $request->topology_status,
                'size' => $request->size,
                'color' => $request->color,
            ]);
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $image) {
                    if ($index >= 5) {
                        break;
                    }

                    $product->images()->create([
                        'image' => $image->store('products', 'public'),
                    ]);
                }
            }

            return $this->successResponse($product->load(['images', 'carProductDetails']), 'product', 'product_created_successfully');
        }
        if (in_array($name, ['propertys', 'offices', 'lands', 'عقارات', 'مكاتب', 'أراضي'])) {
            $product->realEstateProductDetails()->create([
                // 'group_type'           => $request->sub_category_type,
                'type' => $request->type,
                'ownership' => $request->ownership,
                'contract_type' => $request->contract_type,
                'num_of_room' => $request->num_of_room,
                'num_of_bathroom' => $request->num_of_bathroom,
                'num_of_balconies' => $request->num_of_balconies,
                'area' => $request->area,
                'floor' => $request->floor,
                'furnished' => $request->furnished,
                'age_of_construction' => $request->age_of_construction,
                'readiness' => $request->readiness,
                'facade' => $request->facade,
                'nature_of_land' => $request->nature_of_land,
                'street_width' => $request->street_width,
            ]);
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $image) {
                    if ($index >= 5) {
                        break;
                    }

                    $product->images()->create([
                        'image' => $image->store('products', 'public'),
                    ]);
                }
            }

            return $this->successResponse($product->load(['images', 'realEstateProductDetails']), 'product', 'product_created_successfully');
        }
        if (in_array($name, ['playStation', 'musical instruments', 'books & magazines', 'video games', 'بلاي ستيشن', 'آلات موسيقية', 'كتب ومجلات', 'ألعاب فيديو'])) {
            $product->entertainmentProductDetails()->create([
                // 'group_type'                  => $request->sub_category_type,
                'type' => $request->type,
                'model' => $request->model,
                'storage' => $request->storage,
                'attached_games' => $request->attached_games,
                'num_of_accessories_supplied' => $request->num_of_accessories_supplied,
                'warranty' => $request->warranty,
                'date_of_purchase' => $request->date_of_purchase,
                'edition' => $request->edition,
                'color' => $request->color,
                'brand' => $request->brand,
                'accessories' => $request->accessories,

                'title_of_book' => $request->title_of_book,
                'language' => $request->language,
                'number_of_copies' => $request->number_of_copies,
                'author' => $request->author,
                'publishing_house_and_year' => $request->publishing_house_and_year,

                'name' => $request->name,
                'version' => $request->version,
                'online_availability' => $request->online_availability,
            ]);
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $image) {
                    if ($index >= 5) {
                        break;
                    }

                    $product->images()->create([
                        'image' => $image->store('products', 'public'),
                    ]);
                }
            }

            return $this->successResponse($product->load(['images', 'entertainmentProductDetails']), 'product', 'product_created_successfully');
        }
        if (in_array($name, ['fashion', 'beauty products', 'Sports', 'baby supplies', 'medical supplies', 'موضة', 'منتجات تجميل', 'رياضة', 'مستلزمات أطفال', 'مستلزمات طبية'])) {
            $product->miscellaneousProductDetails()->create([
                // 'group_type'              => $request->sub_category_type,
                'type' => $request->type,
                'size' => $request->size,
                'brand' => $request->brand,
                'model' => $request->model,
                'season' => $request->season,
                'color' => $request->color,
                'warranty' => $request->warranty,

                'material' => $request->material,
                'special_characteristics' => $request->special_characteristics,
                'accessories' => $request->accessories,

                'age_group' => $request->age_group,

                'year_of_manufacture' => $request->year_of_manufacture,
                'max_endurance' => $request->max_endurance,
                'compatible_vehicles' => $request->compatible_vehicles,
            ]);
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $image) {
                    if ($index >= 5) {
                        break;
                    }

                    $product->images()->create([
                        'image' => $image->store('products', 'public'),
                    ]);
                }
            }

            return $this->successResponse($product->load(['images', 'miscellaneousProductDetails']), 'product', 'product_created_successfully');
        }
        if (in_array($name, ['miscellaneous', 'furniture', 'متفرقات', 'أثاث'])) {
            $product->electronicsProductDetails()->create([
                // 'group_type'            => $request->sub_category_type,
                'type' => $request->type,
                'brand' => $request->brand,
                'model' => $request->model,
                'year_of_manufacture' => $request->year_of_manufacture,
                'size_or_weight' => $request->size_or_weight,
                'color' => $request->color,
                'warranty' => $request->warranty,
                'accessories' => $request->accessories,

                'main_specification' => $request->main_specification,

                'dimensions' => $request->dimensions,
                'state_specification' => $request->state_specification,
                'made_from' => $request->made_from,
            ]);
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $image) {
                    if ($index >= 5) {
                        break;
                    }

                    $product->images()->create([
                        'image' => $image->store('products', 'public'),
                    ]);
                }
            }

            return $this->successResponse($product->load(['images', 'electronicsProductDetails']), 'product', 'product_created_successfully');
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                if ($index >= 5) {
                    break;
                }

                $product->images()->create([
                    'image' => $image->store('products', 'public'),
                ]);
            }
        }

        return $this->successResponse($product->load(['images']), 'product', 'product_created_successfully');
    }

    public function markAsFeatured($id, Request $request)
    {

        $product = Product::findOrFail($id);

        $product->is_featured = true;

        $product->save();

        return $this->successResponse(
            ['product' => $product->only([
                'id',
                'name',
                'price',
                'is_featured',
                'category_id',
                'sub_category_id',
                'added_by',
                'views',
                'favorites_number',
                'created_at',
                'updated_at',
            ])],
            'product_marked_as_featured'
        );
    }

    public function getFavourites()
    {
        $user = Auth::user();

        $favorites = $user->favorites()
            ->with('product.images', 'product.category', 'product.subCategory', 'product.costs')
            ->get()
            ->map(function (Favorite $fav) {
                return [
                    'favorite_id' => $fav->id,
                    'product' => $fav->product,
                    'added_at' => $fav->created_at,
                ];
            });

        return $this->successResponse(
            ['favorites' => $favorites],
            'favorites_retrieved_successfully'
        );
    }

    public function MarkAsFav(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $userId = Auth::id();
        $productId = $request->product_id;

        $favorite = Favorite::firstOrCreate([
            'user_id' => $userId,
            'product_id' => $productId,
        ]);

        return $this->successResponse(
            ['favorite_id' => $favorite->id],
            'product_added_to_favorites'
        );
    }

    public function deleteFavorite(Request $request)
    {
        $request->validate([
            'favorite_id' => ['required', 'exists:favorites,id'],
        ]);

        $favorite = Favorite::find($request->input('favorite_id'));
        if ($favorite === null) {
            return $this->errorResponse(message: 'Not found');
        }
        $favorite->delete();

        return $this->successResponse(message: 'product_removed_from_favorite');
    }

    public function addReview(Request $request, Product $product)
    {
        $data = $request->validate([
            'rate' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        $review = Review::create([
            'user_id' => Auth::id(),
            'product_id' => $product->id,
            'rate' => $data['rate'],
            'comment' => $data['comment'] ?? null,
        ]);

        return $this->successResponse(
            ['review' => $review],
            'review_created_successfully'
        );
    }

    public function getReviews(Product $product)
    {
        $reviews = $product->reviews()
            ->with('user:id,name')               // eager load reviewer’s name
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function (Review $r) {
                return [
                    'id' => $r->id,
                    'rate' => $r->rate,
                    'comment' => $r->comment,
                    'user' => $r->user,
                    'created_at' => $r->created_at,
                ];
            });

        return $this->successResponse(
            ['reviews' => $reviews],
            'reviews_retrieved_successfully'
        );
    }

    public function getViolations(Product $product)
    {
        $violations = $product->violations()
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn (Violation $v) => [
                'id' => $v->id,
                'type' => $v->type,
                'notes' => $v->notes,
                'reported_at' => $v->created_at,
            ]);

        return $this->successResponse(
            ['violations' => $violations],
            'violations_retrieved_successfully'
        );
    }

    public function addViolation(Request $request, Product $product)
    {
        $data = $request->validate([
            'type' => [
                'required',
                Rule::in([
                    'inappropriate_content',
                    'misleading_information',
                    'duplicate_listing',
                    'misclassified',
                ]),
            ],
            'notes' => 'nullable|string',
        ]);

        $violation = $product->violations()->create($data);

        return $this->successResponse(
            ['violation' => [
                'id' => $violation->id,
                'type' => $violation->type,
                'notes' => $violation->notes,
            ]],
            'violation_reported_successfully',
            201
        );
    }

    public function toggleActivation(Request $request)
    {
        $request->validate([
            'product_id' => ['required', 'exists:products,id'],
        ]);
        $product = Product::find($request->input('product_id'));
        if ($product == null) {
            return $this->errorResponse('Not found');
        }
        $product->update(['is_active' => ! $product->is_active]);

        return $this->successResponse(message: 'updated');
    }

    public function productsDetailsKeys()
    {
        $models = [
            AnimalProductDetail::class,
            DevicesProductDetail::class,
            CarProductDetail::class,
            RealEstateProductDetail::class,
            EntertainmentProductDetail::class,
            MiscellaneousProductDetail::class,
            ElectronicsProductDetail::class,
        ];
        $result = [];
        foreach ($models as $model) {
            $result[class_basename($model)] = (new $model)->getFillable();
        }

        return $this->successResponse($result);
    }

    public function delete(Request $request)
    {
        $request->validate([
            'product_id' => ['required', 'exists:products,id'],
        ]);
        $product = Product::find($request->input('product_id'));

        if (
            $product === null ||
            (
                $product->added_by !== $request->user()->id &&
                ! Auth::user()->hasRole('admin')
            )
        ) {
            return $this->errorResponse('Not found');
        }
        $product->delete();

        return $this->successResponse(message: 'deleted');
    }
}
