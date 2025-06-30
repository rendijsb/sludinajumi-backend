<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use App\Http\DataTransferObjects\Auth\RegisterRequestData;
use App\Http\Resources\Users\UserResource;
use App\Models\Users\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class RegisterRequest extends FormRequest
{
    private const NAME = 'name';
    private const EMAIL = 'email';
    private const PHONE = 'phone';
    private const PASSWORD = 'password';
    private const TERMS_ACCEPTED = 'terms_accepted';

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            self::NAME => ['required', 'string', 'max:255'],
            self::EMAIL => ['required', 'string', 'email', 'max:255', 'unique:users'],
            self::PHONE => ['nullable', 'string', 'max:20', 'unique:users'],
            self::PASSWORD => ['required', 'confirmed', Password::defaults()],
            self::TERMS_ACCEPTED => ['required', 'accepted'],
        ];
    }

    /**
     * @param null $key
     * @param null $default
     * @throws UnknownProperties
     */
    public function data($key = null, $default = null): RegisterRequestData
    {
        return new RegisterRequestData([
            RegisterRequestData::NAME => $this->get(self::NAME),
            RegisterRequestData::EMAIL => $this->get(self::EMAIL),
            RegisterRequestData::PHONE => $this->get(self::PHONE),
            RegisterRequestData::PASSWORD => $this->get(self::PASSWORD),
            RegisterRequestData::TERMS_ACCEPTED => $this->get(self::TERMS_ACCEPTED),
        ]);
    }

    public function responseResource(User $user): UserResource
    {
        return UserResource::make($user->load('roleRelation'));
    }
}
