<?php

declare(strict_types=1);

namespace App\Infrastructure\CQRS\Bus\Deserializer;

use App\Infrastructure\CQRS\Bus\Command;

interface CommandDeserializer
{
    public function deserialize(string $serialized): Command;
}
