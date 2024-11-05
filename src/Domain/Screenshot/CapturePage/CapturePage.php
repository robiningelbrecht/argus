<?php

declare(strict_types=1);

namespace App\Domain\Screenshot\CapturePage;

use App\Infrastructure\CQRS\Bus\DomainCommand;
use App\Infrastructure\ValueObject\String\Url;

final class CapturePage extends DomainCommand
{
    public function __construct(
        private readonly Url $url,
    ) {
    }

    public function getUrl(): Url
    {
        return $this->url;
    }
}
