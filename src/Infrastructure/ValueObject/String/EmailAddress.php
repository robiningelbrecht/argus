<?php

namespace App\Infrastructure\ValueObject\String;

final readonly class EmailAddress extends NonEmptyStringLiteral
{
    private const NO_REPLY = 'no-reply@cubeacademy.com';
    private const INFO = 'info@cubeacademy.com';

    public static function noReplyAtCubeAcademy(): self
    {
        return self::fromString(self::NO_REPLY);
    }

    public static function infoAtCubeAcademy(): self
    {
        return self::fromString(self::INFO);
    }
}
