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

    /**
     * @throws UnknownProperties
     */
    public function login(LoginRequest $request): UserResource
    {
        $loginData = $request->data();
        $user = $this->userDbRepository->findByEmail($loginData->email);

        if (!$user || !Hash::check($loginData->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Nepareizi pieteikšanās dati'],
            ]);
        }

        if (!$user->isActive()) {
            throw ValidationException::withMessages([
                'email' => ['Konts ir deaktivizēts'],
            ]);
        }

        $user->tokens()->delete();

        return $request->responseResource($user);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(null, 204);
    }

    public function me(Request $request): UserResource
    {
        return UserResource::make($request->user()->load('roleRelation'));
    }

    public function refresh(Request $request): UserResource
    {
        $user = $request->user();
        $request->user()->currentAccessToken()->delete();

        return UserResource::make($user->load('roleRelation'));
    }
}
