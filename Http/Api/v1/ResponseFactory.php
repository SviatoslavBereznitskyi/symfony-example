<?php

declare(strict_types=1);

namespace App\Http\Api\v1;

/**
 * Class ResponseFactory
 */
final class ResponseFactory
{
    public const SUCCESS = 'success';
    public const FAILURE = 'failure';

    public const INCORRECT_TOKEN_ERROR           = 1000;
    public const CONFIRMATION_NOT_REQUIRED_ERROR = 1001;
    public const TOKEN_INVALID_ERROR             = 1002;
    public const TOKEN_EXPIRED_ERROR             = 1003;
    public const USER_EXISTS_ERROR               = 1004;
    public const USER_NOT_EXISTS_ERROR           = 1005;
    public const ENTITY_VALIDATION_ERROR         = 1006;
    public const EMAIL_ALREADY_EXISTS            = 1007;
    public const UNSUPPORTED_GRANT_TYPE          = 2;
    public const INVALID_CLIENT                  = 4;
    public const INVALID_REQUEST                 = 8;
    public const INVALID_GRANT_TYPE              = 10;
    public const FORBIDDEN                       = 403;
    public const UNABLE_TO_SEND_EMAIL            = 1008;

    private static array $messages  = [
        self::INCORRECT_TOKEN_ERROR           => 'incorrect_token',
        self::CONFIRMATION_NOT_REQUIRED_ERROR => 'confirmation_not_required',
        self::TOKEN_INVALID_ERROR             => 'invalid_token',
        self::TOKEN_EXPIRED_ERROR             => 'expired_token',
        self::USER_EXISTS_ERROR               => 'user_exists',
        self::USER_NOT_EXISTS_ERROR           => 'user_not_exists',
        self::EMAIL_ALREADY_EXISTS            => 'email_already_exists',
        self::UNSUPPORTED_GRANT_TYPE          => 'unsupported_grant_type',
        self::INVALID_CLIENT                  => 'invalid_client',
        self::INVALID_REQUEST                 => 'invalid_request',
        self::INVALID_GRANT_TYPE              => 'invalid_grant',
        self::ENTITY_VALIDATION_ERROR         => 'validation_failed',
        self::UNABLE_TO_SEND_EMAIL            => 'unable_to_send_email',
        self::FORBIDDEN                       => 'forbidden',
    ];

    public static function getMessage(int $code): string
    {
        return isset(self::$messages[$code]) ? (string)self::$messages[$code] : (string)$code;
    }
}
