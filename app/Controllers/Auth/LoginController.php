<?php

declare(strict_types=1);

namespace App\Controllers\Auth;

use CodeIgniter\Shield\Controllers\LoginController as ShieldLoginController;
use CodeIgniter\Shield\Validation\ValidationRules;

final class LoginController extends ShieldLoginController
{
    protected function getValidationRules(): array
    {
        $rules = new ValidationRules();

        return [
            'username' => config('Auth')->usernameValidationRules,
            'password' => $rules->getPasswordRules(),
        ];
    }
}
