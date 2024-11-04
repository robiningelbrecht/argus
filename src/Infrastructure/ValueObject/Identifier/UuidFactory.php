<?php

declare(strict_types=1);

namespace App\Infrastructure\ValueObject\Identifier;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class UuidFactory
{
    public function generate(): UuidInterface
    {
        return Uuid::uuid4();
    }
}
