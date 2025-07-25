<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use ApiResponseTrait;

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
