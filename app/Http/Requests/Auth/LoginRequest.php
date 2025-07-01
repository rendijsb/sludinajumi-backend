<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use App\Http\DataTransferObjects\Auth\LoginRequestData;
use App\Http\Resources\Users\UserResource;
use App\Models\Users\User;
use Illuminate\Foundation\Http\FormRequest;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class LoginRequest extends FormRequest
{
    private const EMAIL = 'email';
    private const PASSWORD = 'password';
    private const REMEMBER = 'remember';

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            self::EMAIL => ['required', 'email', 'string', 'max:255'],
            self::PASSWORD => ['required', 'string'],
            self::REMEMBER => ['boolean'],
        ];
    }

    /**
     * @param null $key
     * @param null $default
     * @throws UnknownProperties
     */
    public function data($key = null, $default = null): LoginRequestData
    {
        return new LoginRequestData([
            LoginRequestData::EMAIL => $this->get(self::EMAIL),
            LoginRequestData::PASSWORD => $this->get(self::PASSWORD),
            LoginRequestData::REMEMBER => $this->boolean(self::REMEMBER),
        ]);
    }

    public function responseResource(User $user): UserResource
    {
        return UserResource::make($user->load('roleRelation'));
    }
}
