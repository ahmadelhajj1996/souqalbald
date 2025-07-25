<?php

namespace App\Http\Controllers;

use App\Models\Seller;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Throwable;

class SellerController extends Controller
{
    use ApiResponseTrait;

    public function index()
    {
        try {
            $sellers = Seller::with('user:id,name,email,phone')
                ->select('id', 'user_id', 'store_owner_name', 'store_name', 'address', 'logo', 'description', 'created_at')
                ->where('status', 'accepted')
                ->where('is_active', 1)
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            return $this->successResponse($sellers, 'auth', 'fetched_successfully.');
        } catch (\Throwable $e) {

            return $this->errorResponse('fetch_failed', 'auth', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function updateSellerStatus(Request $request, Seller $seller)
    {
        $request->validate([
            'status' => 'required|in:accepted,rejected',
        ]);
        try {
            DB::beginTransaction();
            $seller->status = $request->status;
            $seller->save();
            $role = Role::where('name', 'seller')
                ->where('guard_name', 'api')
                ->first();

            if (! $role) {
                throw new \Exception(__('auth.role_not_found'));
            }

            $seller->user->assignRole('seller');

            DB::commit();

            return $this->successResponse($seller, 'auth', 'seller_status_updated_successfully.');
        } catch (Throwable $e) {
            DB::rollBack();

            return $this->errorResponse('updated_failed', 'auth', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function markSellerAsFeatured($id)
    {

        $seller = Seller::findOrFail($id);

        $seller->is_featured = true;

        $seller->save();

        return $this->successResponse(
            ['seller' => $seller->only(['id', 'name', 'is_featured', 'created_at', 'updated_at'])],
            'seller_marked_as_featured'
        );
    }

    public function stores(Request $request)
    {

        $query = Seller::with([
            'user',
            'products',
        ]);

        if ($request->filled('is_featured')) {
            $query->where('is_featured', filter_var(
                $request->is_featured,
                FILTER_VALIDATE_BOOLEAN
            ));
        }
        if ($request->filled('id')) {
            $query->where('id', $request->id);
        }
        $stores = $query->get()->map(function (Seller $s) {
            return [
                'store' => [
                    'id' => $s->id,
                    'store_owner_name' => $s->store_owner_name,
                    'store_name' => $s->store_name,
                    'address' => $s->address,
                    'logo' => $s->logo,
                    'description' => $s->description,
                    'is_featured' => $s->is_featured,
                    'created_at' => $s->created_at,
                    'updated_at' => $s->updated_at,
                ],
                'user' => $s->user,
                'products' => $s->products,
            ];
        });

        return $this->successResponse(
            ['stores' => $stores],
            'stores_retrieved_successfully'
        );
    }
}
