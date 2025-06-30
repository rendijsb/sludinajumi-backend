<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\Users\UserResource;
use App\Services\Repositories\UserLogicRepository;
use App\Services\Repositories\UserDbRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class AuthController extends Controller
{
    public function __construct(
        private readonly UserLogicRepository $userLogicRepository,
        private readonly UserDbRepository    $userDbRepository
    )
    {
    }

    /**
     * @throws UnknownProperties
     */
    public function register(RegisterRequest $request): UserResource
    {
        return $request->responseResource($this->userLogicRepository->register($request->data()));
    }

    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $user = $this->userDbRepository->findByEmail($request->email);

            if (!$user || !Hash::check($request->password, $user->password)) {
                throw ValidationException::withMessages([
                    'email' => ['Nepareizi dati'],
                ]);
            }

            if (!$user->isActive()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Konts ir deaktivizēts'
                ], 403);
            }

            // Revoke all existing tokens
            $user->tokens()->delete();

            // Create new token
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Veiksmīga pieteikšanās',
                'data' => [
                    'user' => new UserResource($user->load('roleRelation')),
                    'token' => $token,
                ]
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Nepareizi dati',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Pieteikšanās neizdevās',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Veiksmīgi izrakstījāties'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Izrakstīšanās neizdevās',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => new UserResource($request->user()->load('roleRelation'))
        ]);
    }

    public function refresh(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            $request->user()->currentAccessToken()->delete();

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'data' => [
                    'user' => new UserResource($user->load('roleRelation')),
                    'token' => $token,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token atjaunošana neizdevās',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
