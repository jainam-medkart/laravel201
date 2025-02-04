<?php

namespace App\Http\Controllers;

use App\Repositories\UserRepository;
use App\Http\Responses\ApiSuccessResponse;
use App\Http\Responses\ApiErrorResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Exception;

class AuthController extends Controller
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
            ]);

            if ($validator->fails()) {
                return ApiErrorResponse::create(new ValidationException($validator), 422, $validator->errors()->toArray());
            }

            $user = $this->userRepository->createUser([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            return ApiSuccessResponse::create(null, 'User registered successfully', 201);
        } catch (ValidationException $e) {
            return ApiErrorResponse::create($e, 422, $e->errors());
        } catch (Exception $e) {
            return ApiErrorResponse::create($e, 500);
        }
    }

    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            $user = $this->userRepository->getByEmail($request->email);

            if (! $user || ! Hash::check($request->password, $user->password)) {
                return ApiErrorResponse::create(new Exception('Invalid credentials'), 401);
            }

            $userModel = $this->userRepository->getById($user->id);
            $token = $this->userRepository->createToken($userModel, 'auth_token');

            return ApiSuccessResponse::create(['token_type' => 'Bearer', 'access_token' => $token], 'Login successful');
        } catch (ValidationException $e) {
            return ApiErrorResponse::create($e, 422, $e->errors());
        } catch (Exception $e) {
            return ApiErrorResponse::create($e, 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->tokens()->delete();

            return ApiSuccessResponse::create(null, 'Logged out successfully');
        } catch (Exception $e) {
            return ApiErrorResponse::create($e, 500);
        }
    }

    public function getUserInfo(Request $request)
    {
        try {
            $userId = $request->user()->id;
            $user = $this->userRepository->getById($userId);
            $tokens = $request->user()->tokens()->get();

            return ApiSuccessResponse::create(
                ['user' => $user, 'tokens' => $tokens],
                'Details Fetched Successfully',
                200
            );
        } catch (Exception $e) {
            return ApiErrorResponse::create($e, 500);
        }
    }
}