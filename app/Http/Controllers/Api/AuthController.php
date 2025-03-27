<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends BaseController
{
    public function login(Request $request): JsonResponse
    {
        $validator = $this->validateLoginRequest($request);
        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors());
        }
        if (!Auth::attempt($request->only('email', 'password'))) {
            return $this->sendError('Invalid Credentials', ['error' => 'Invalid Credentials']);
        }

        $user = Auth::user();
        return $this->generateAuthResponse($user, 'Login Successful!');
    }

    public function register(Request $request): JsonResponse
    {
        $validator = $this->validateRegisterRequest($request);
        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors());
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role
        ]);


        $loggedInUser = Auth::loginUsingId($user->id);
        return $this->generateAuthResponse($loggedInUser, 'Registration Successful!');
    }

    public function logout(Request $request): JsonResponse
    {
        auth()->user()->tokens()->delete();
        return $this->sendResponse([], 'User logged out successfully.');
    }

    private function validateLoginRequest(Request $request)
    {
        return Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);
    }

    private function validateRegisterRequest(Request $request)
    {
        return Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
            'role' => 'required|in:user,admin'
        ]);
    }

    private function generateAuthResponse($user, $message)
    {
        // Delete all previous tokens before generating a new one
        $user->tokens()->delete();

        $success = [
            'token' => $user->createToken('auth_token')->plainTextToken,
            'data' => new UserResource($user),
        ];
        return $this->sendResponse($success, $message ?? 'User login successful.');
    }
}
