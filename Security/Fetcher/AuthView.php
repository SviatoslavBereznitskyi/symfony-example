<?php

declare(strict_types=1);

namespace App\Security\Fetcher;

/**
 * Class AuthView
 */
class AuthView
{
    /**
     * @var string
     */
    public string $id = '';

    /**
     * @var string
     */
    public string $email  = '';

    /**
     * @var string
     */
    public string $password_hash = '';

    /**
     * @var string
     */
    public string $role = '';

    /**
     * @var string
     */
    public string $status  = '';
}
