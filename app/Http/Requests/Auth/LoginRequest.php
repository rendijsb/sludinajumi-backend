<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

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
            self::EMAIL => ['required', 'email'],
            self::PASSWORD => ['required'],
            self::REMEMBER => ['boolean'],
        ];
    }
}
