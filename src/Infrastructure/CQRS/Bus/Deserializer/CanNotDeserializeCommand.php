<?php

declare(strict_types=1);

namespace App\Infrastructure\CQRS\Bus\Deserializer;

class CanNotDeserializeCommand extends \RuntimeException
{
}
