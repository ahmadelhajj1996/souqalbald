<?php

namespace App\Http\Controllers;

use App\Models\Seller;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Throwable;

class SellerController extends Controller
{
    use ApiResponseTrait;

    public function getProfile(Request $request)
    {
        $user = Auth::user();
        if ($this->checkIfSeller($user)) {
            $seller = Seller::where('user_id', $user->id)->first();

            return $this->successResponse(
                result: ['seller' => $seller],
                message: 'profile'
            );
        }

        return $this->errorResponse(message: 'not seller');
    }

    public function editProfile(Request $request)
    {
        $data = $request->validate([
            'store_owner_name' => ['required', 'string', 'max:55'],
            'store_name' => ['required', 'string', 'max:55'],
            'address' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'regex:/^09[1-9]{1}\d{7}$/'],
        ]);

        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('sellers/logos', 'public');
            $data = array_merge($data, ['logo' => $logoPath]);
        }

        if ($request->hasFile('cover_image')) {
            $coverPath = $request->file('cover_image')->store('sellers/covers', 'public');
            $data = array_merge($data, ['cover_image' => $coverPath]);
        }

        $user = Auth::user();
        if ($this->checkIfSeller($user)) {
            $seller = Seller::where('user_id', $user->id)->first();
            $seller->update($data);

            return $this->successResponse(
                result: ['seller' => $seller->fresh()],
                message: 'seller profile updated'
            );
        }

        return $this->errorResponse(message: 'not seller');
    }

    public function getSeller(Request $request)
    {
        $request->validate([
            'user_id' => ['required', 'exists:users,id']
        ]);

        $user = User::find($request->input('user_id'));
        if ($this->checkIfSeller($user)) {
            $seller = Seller::where('user_id', $user->id)->first();
            return $this->successResponse(result: [
                'seller' => $seller
            ]);
        }
        return $this->errorResponse(message: 'not seller');
    }

    private function checkIfSeller($user)
    {
        if ($user !== null && $user->hasRole('seller') && Seller::where('user_id', $user->id)->exists()) {
            return true;
        }

        return false;
    }

    public function index()
    {
        try {
        $sellers = Seller::select('id', 'user_id', 'store_owner_name', 'store_name', 'address', 'logo', 'description', 'status', 'created_at')
            ->whereHas('user', function($query) {
                $query->where('is_active', 1);
            })
            ->with(['user' => function($query) {
                $query->select('id', 'name', 'email', 'phone', 'is_active');
            }])
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
                    'cover' => $s->cover_image,
                    'phone' => $s->phone,
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
