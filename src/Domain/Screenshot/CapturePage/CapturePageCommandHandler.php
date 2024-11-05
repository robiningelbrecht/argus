<?php

declare(strict_types=1);

namespace App\Domain\Screenshot\CapturePage;

use App\Domain\Screenshot\PageCapture;
use App\Infrastructure\CQRS\Bus\Command;
use App\Infrastructure\CQRS\Bus\CommandHandler;

final readonly class CapturePageCommandHandler implements CommandHandler
{
    public function __construct(
        private PageCapture $pageCapture,
    ) {
    }

    public function handle(Command $command): void
    {
        assert($command instanceof CapturePage);

        $this->pageCapture->screenshot($command->getUrl());
    }
}
