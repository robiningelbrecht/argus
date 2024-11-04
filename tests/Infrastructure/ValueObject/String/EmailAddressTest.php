<?php

namespace App\Tests\Infrastructure\ValueObject\String;

use App\Infrastructure\ValueObject\String\EmailAddress;
use PHPUnit\Framework\TestCase;

class EmailAddressTest extends TestCase
{
    public function testDefaults(): void
    {
        $this->assertEquals('no-reply@cubeacademy.com', EmailAddress::noReplyAtCubeAcademy());
        $this->assertEquals('info@cubeacademy.com', EmailAddress::infoAtCubeAcademy());
    }
}
