<?php

namespace App\Tests\Infrastructure\ValueObject\Identifier;

use App\Infrastructure\ValueObject\Identifier\UuidFactory;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\UuidInterface;

class UuidFactoryTest extends TestCase
{
    public function testGenerate(): void
    {
        $this->assertInstanceOf(UuidInterface::class, (new UuidFactory())->generate());
    }
}
