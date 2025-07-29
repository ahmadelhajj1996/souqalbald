<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    use ApiResponseTrait;

    public function getProfile(Request $request)
    {
        return $this->successResponse(
            result: ['user' => Auth::user()],
            message: 'profile'
        );
    }

    public function editProfile(Request $request)
    {
        $user = Auth::user();
        $data = $request->validate([
            'name' => ['required', 'string', 'max:55'],
            'age' => ['required', 'numeric', 'min:12', 'max:100'],
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'phone' => [
                'required',
                'regex:/^09[1-9]{1}\d{7}$/',
                Rule::unique('users')->ignore($user->id),
            ],
        ]);
        Auth::user()->update($data);
        return $this->successResponse(
            result: ['user' => Auth::user()],
            message: 'profile updated'
        );
    }

    public function toggleActivation(Request $request)
    {
        $request->validate([
            'user_id' => ['required', 'exists:users,id'],
        ]);
        $user = User::find($request->input('user_id'));
        if ($user == null) {
            return $this->errorResponse('Not found');
        }
        $user->update(['is_active' => ! $user->is_active]);

        return $this->successResponse(message: 'updated');
    }
}
