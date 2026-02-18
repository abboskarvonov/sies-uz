<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\LoginRequest;
use App\Http\Requests\Api\V1\Auth\UpdateProfileRequest;
use App\Http\Resources\Api\V1\Admin\UserResource;
use App\Http\Traits\Api\ApiResponses;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use ApiResponses;

    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->errorResponse(
                'INVALID_CREDENTIALS',
                'The provided credentials are incorrect.',
                401
            );
        }

        $deviceName = $request->input('device_name', 'mobile-app');
        $token = $user->createToken($deviceName)->plainTextToken;

        $user->load('roles');

        return $this->successResponse([
            'user' => new UserResource($user),
            'token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    public function logout()
    {
        request()->user()->currentAccessToken()->delete();

        return $this->successResponse(['message' => 'Successfully logged out.']);
    }

    public function profile()
    {
        $user = request()->user();
        $user->load('roles');

        return $this->successResponse(new UserResource($user));
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = $request->user();

        $data = $request->only(['name', 'email']);

        if ($request->filled('password')) {
            $data['password'] = $request->password;
        }

        $user->update($data);
        $user->load('roles');

        return $this->successResponse(new UserResource($user));
    }
}
