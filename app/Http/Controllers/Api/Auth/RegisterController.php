<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\CustomerRegisterRequest;
use App\Http\Requests\Auth\SellerRegisterRequest;
use App\Http\Requests\Auth\UpgradeToSellerRequest;
use App\Models\Notification;
use App\Models\OtpCode;
use App\Models\Seller;
use App\Models\User;
use App\Services\FirebaseService;
use App\Traits\ApiResponseTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Passport\Token;
use Laravel\Socialite\Facades\Socialite;
use Spatie\Permission\Models\Role;
use Throwable;

class RegisterController extends Controller
{
    use ApiResponseTrait;

    protected $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    public function customerRegister(CustomerRegisterRequest $request)
    {
        try {
            DB::beginTransaction();
            $profileImage = null;
            if ($request->hasFile('profile_image')) {
                $profileImage = $request->file('profile_image')->store('customer/profile', 'public');
            }
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'age' => $request->age,
                'profile_image' => $profileImage,

            ]);
            if (! Role::where('name', 'customer')->exists()) {
                throw new Exception(__('auth.role_not_found'));
            }
            $user->assignRole(Role::where('name', 'customer')->where('guard_name', 'api')->first());

            $token = $user->createToken('CustomerToken')->accessToken;

            DB::commit();

            return $this->successResponse([
                'token' => $token,
                'user' => $user,
            ], 'auth', 'registered');
        } catch (Throwable $e) {
            DB::rollBack();

            return $this->errorResponse('registration_failed', 'auth', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function sellerRegister(SellerRegisterRequest $request)
    {
        try {
            DB::beginTransaction();

            $logoPath = null;
            if ($request->hasFile('logo')) {
                $logoPath = $request->file('logo')->store('sellers/logos', 'public');
            }

            $coverPath = null;
            if ($request->hasFile('cover_image')) {
                $coverPath = $request->file('cover_image')->store('sellers/covers', 'public');
            }

            $user = User::create([
                'name' => $request->store_owner_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
            ]);

            $seller = Seller::create([
                'user_id' => $user->id,
                'store_owner_name' => $request->store_owner_name,
                'store_name' => $request->store_name,
                'address' => $request->address,
                'logo' => $logoPath ? $logoPath : null,
                'cover_image' => $coverPath ? $coverPath : null,
                'description' => $request->description,
            ]);

            $role = Role::where('name', 'seller')->where('guard_name', 'api')->first();

            if (! $role) {
                throw new \Exception(__('auth.role_not_found'));
            }

            $user->assignRole($role);

            $token = $user->createToken('SellerToken')->accessToken;

            DB::commit();

            $admins = User::role('admin', 'api')->get();
            foreach ($admins as $admin) {
                $firebaseToken = $admin ? $admin->firebase_token : null;
                if ($firebaseToken && strlen($firebaseToken) > 0) {
                    $title = 'New seller';
                    $body = 'A new seller register in your app';
                    $type = 'notification';
                    $sub_type = 'notification';
                    $data = [
                        'type' => $type,
                        'sub_type' => $sub_type,
                    ];

                    try {
                        Notification::create([
                            'user_id' => $user->id,
                            'type' => $sub_type,
                            'data' => json_encode($data),
                            'title' => $title,
                            'body' => $body,
                            'sent_at' => now(),
                            'read' => false,
                        ]);
                        $this->firebaseService->sendNotification(
                            $data,
                            $title,
                            $body,
                            $firebaseToken
                        );
                        error_log('Notification sent successfully.');
                    } catch (\Kreait\Firebase\Exception\Messaging\NotFound $e) {
                        error_log('Firebase token not found or invalid: ' . $firebaseToken);
                    } catch (\Exception $e) {
                        error_log('Failed to send notification: ' . $e->getMessage());
                    }
                }
            }

            return $this->successResponse([
                'token' => $token,
                'user' => $user,
                'seller' => $seller,
            ], 'auth', 'registered');
        } catch (Throwable $e) {
            DB::rollBack();

            return $this->errorResponse('registration_failed', 'auth', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function guestRegister()
    {
        $user = User::create([
            'name' => 'Guest-' . uniqid(),
            'email' => null,
            'password' => Hash::make(Str::random(12)),
        ]);
        $role = Role::where('name', 'guest')->where('guard_name', 'api')->first();

        if (! $role) {
            throw new \Exception(__('auth.role_not_found'));
        }
        $user->assignRole($role);

        $token = $user->createToken('GuestToken')->accessToken;

        return $this->successResponse([
            'token' => $token,
            'user' => $user,
        ], 'auth', 'guest_registered');
    }

    public function upgradeToSeller(UpgradeToSellerRequest $request)
    {
        try {
            DB::beginTransaction();

            $user = auth()->user();
            $logoPath = null;

            if ($request->hasFile('logo')) {
                $logoPath = $request->file('logo')->store('sellers/logos', 'public');
            }

            $coverPath = null;
            if ($request->hasFile('cover_image')) {
                $coverPath = $request->file('cover_image')->store('sellers/covers', 'public');
            }

            if ($request->filled('email')) {
                $user->email = $request->email;
            }

            if ($request->filled('phone')) {
                $user->phone = $request->phone;
            }
            if ($user->email == null) {
                return $this->errorResponse(__('this_user_should_add_email'), 'auth', 404);
            }
            if ($user->phone == null) {
                return $this->errorResponse(__('this_user_should_add_phone'), 'auth', 404);
            }
            $user->save();

            $seller = Seller::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'store_owner_name' => $request->store_owner_name,
                    'store_name' => $request->store_name,
                    'address' => $request->address,
                    'logo' => $logoPath,
                    'cover_image' => $coverPath,
                    'description' => $request->description,
                ]
            );
            if (! Role::where('name', 'seller')->exists()) {
                throw new \Exception(__('auth.role_not_found'));
            }
            $user->syncRoles(['seller']);
            DB::commit();

            return $this->successResponse([
                'user' => $user,
                'seller' => $seller,
            ], 'auth', 'upgraded_to_seller');
        } catch (Throwable $e) {
            DB::rollBack();

            return $this->errorResponse('upgrade_failed', 'auth', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (! $user) {
            return $this->errorResponse(__('user_not_found'), 'auth', 404);
        }
        if (! Hash::check($credentials['password'], $user->password)) {
            return $this->errorResponse(__('invalid_password'), 'auth', 401);
        }
        Auth::login($user);
        $token = $user->createToken('AuthToken')->accessToken;

        return $this->successResponse([
            'token' => $token,
            'user' => $user,
            'role' => $user->getRoleNames()->first(),
        ], 'auth', 'login_success');
    }

    public function updatePassword(Request $request)
    {
        try {
            $validator = validator($request->all(), [
                'old_password' => 'required',
                'password' => 'required|confirmed|min:6',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => 0,
                    'message' => __('auth.validation_failed'),
                    'result' => ['errors' => $validator->errors()],
                ], 422);
            }

            $user = auth()->user();

            if (! Hash::check($request->old_password, $user->password)) {
                return $this->errorResponse('invalid_old_password', 'auth', 400);
            }

            $user->update(['password' => Hash::make($request->password)]);

            return $this->successResponse([], 'auth', 'password_updated');
        } catch (Throwable $e) {
            return $this->errorResponse('update_password_failed', 'auth', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function sendResetOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();
        if (! $user) {
            return $this->errorResponse('email_not_found', 'auth', 404);
        }
        //  $otp = rand(100000, 999999);
        $otp = '111111';
        $token = uniqid('reset_', true);

        OtpCode::create([
            'email' => $user->email,
            'otp' => $otp,
            'token' => $token,
            'expires_at' => now()->addMinutes(10),
            'is_used' => false,

        ]);

        // Mail::to($user->email)->send(new ResetOtpMail($otp));

        return $this->successResponse([
            'message' => __('auth.otp_sent'),
        ], 'auth');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|numeric',
        ]);

        $otpRecord = OtpCode::where('email', $request->email)
            ->where('otp', $request->otp)
            ->where('is_used', false)
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if (! $otpRecord) {
            return $this->errorResponse('invalid_otp', 'auth', 400);
        }

        return $this->successResponse([
            'token' => $otpRecord->token,
        ], 'auth', 'otp_verified');
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required',
            'password' => 'required|confirmed|min:6',
        ]);

        $otpRecord = OtpCode::where('email', $request->email)
            ->where('token', $request->token)
            ->where('is_used', false)
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if (! $otpRecord) {
            return $this->errorResponse('invalid_token', 'auth', 400);
        }

        $user = User::where('email', $request->email)->first();
        if (! $user) {
            return $this->errorResponse('email_not_found', 'auth', 404);
        }

        $user->update(['password' => Hash::make($request->password)]);
        $otpRecord->delete();

        return $this->successResponse([], 'auth', 'password_reset_success');
    }

    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();

            return $this->successResponse(
                null,
                'auth',
                'user_deleted_successfully.'
            );
        } catch (Throwable $e) {
            return $this->errorResponse('delete_failed', 'auth', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function markAsInactive($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->is_active = 0;
            $user->save();

            return $this->successResponse(
                $user,
                'auth',
                'user_inactivated_successfully.'
            );
        } catch (Throwable $e) {
            return $this->errorResponse('inactivate_failed', 'auth', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function logout(Request $request)
    {
        try {
            if ($request->user() == null) {
                throw new \Exception('User not found');
            }
            $request->user()->tokens->each(function (Token $token) {
                $token->revoke();
                $token->refreshToken?->revoke();
            });

            return $this->successResponse(message: 'Logged out');
        } catch (\Exception) {
            return $this->errorResponse(message: 'Error logging out');
        }
    }

    public function redirectToSocialProvider($provider)
    {
        return Socialite::driver($provider)->stateless()->redirect();
    }

    public function handleSocialProviderCallback($provider)
    {
        try {
            if (!in_array($provider, ['facebook', 'google'])) {
                throw new \Exception("invalid provider");
            }
            $socialUser = Socialite::driver($provider)->stateless()->user();

            $user = User::firstOrCreate([
                'email' => $socialUser->getEmail(),
            ], [
                'name' => $socialUser->getName() ?? $socialUser->getNickname() ?? 'Social User',
                'password' => Hash::make("password_{$socialUser->getName()}"),
                'provider_id' => $socialUser->getId(),
                'provider' => $provider,
            ]);

            if (! $user->hasRole('customer')) {
                $role = Role::where('name', 'customer')->where('guard_name', 'api')->first();
                $user->assignRole($role);
            }

            $token = $user->createToken('SocialCustomerToken')->accessToken;

            return response()->json([
                'token' => $token,
                'user' => $user,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function handleSocialProviderToken(Request $request, $provider)
    {
        try {
            if (!in_array($provider, ['facebook', 'google'])) {
                throw new \Exception("invalid auth provider");
            }
            $request->validate([
                'token' => ['required', 'string']
            ]);
            $token = $request->input('token') ?? '';
            $socialUser = Socialite::driver($provider)
                ->stateless()
                ->userFromToken($token);

            $user = User::firstOrCreate([
                'email' => $socialUser->getEmail(),
            ], [
                'name' => $socialUser->getName() ?? $socialUser->getNickname() ?? 'Social User',
                'password' => Hash::make("password_{$socialUser->getName()}"),
                'provider_id' => $socialUser->getId(),
                'provider' => $provider,
            ]);

            if (! $user->hasRole('customer')) {
                $role = Role::where('name', 'customer')->where('guard_name', 'api')->first();
                $user->assignRole($role);
            }

            $token = $user->createToken('SocialCustomerToken')->accessToken;

            return response()->json([
                'token' => $token,
                'user' => $user,
                'password' => "password_{$socialUser->getName()}",
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
