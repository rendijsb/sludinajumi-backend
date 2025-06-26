<?php

declare(strict_types=1);

namespace App\Http\DataTransferObjects\Auth;

use Spatie\DataTransferObject\DataTransferObject;

class RegisterRequestData extends DataTransferObject
{
    public const NAME = 'name';
    public const EMAIL = 'email';
    public const PHONE = 'phone';
    public const PASSWORD = 'password';
    public const TERMS_ACCEPTED = 'termsAccepted';

    public string $name;
    public string $email;
    public ?string $phone;
    public string $password;
    public bool $termsAccepted;
}
