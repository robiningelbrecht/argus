<?php

declare(strict_types=1);

namespace App\Domain\Capture\CapturePage;

use App\Domain\Capture\PageCapture;
use App\Domain\Capture\ScreenshotFormat;
use App\Domain\Capture\Viewport;
use App\Infrastructure\CQRS\Bus\Command;
use App\Infrastructure\CQRS\Bus\CommandHandler;
use App\Infrastructure\ValueObject\Math\Percentage;

final readonly class CapturePageCommandHandler implements CommandHandler
{
    public function __construct(
        private PageCapture $pageCapture,
    ) {
    }

    public function handle(Command $command): void
    {
        assert($command instanceof CapturePage);

        $this->pageCapture->screenshot(
            url: $command->getUrl(),
            format: ScreenshotFormat::WEBP,
            quality: Percentage::fromInt(80),
            viewport: Viewport::default(),
            captureFullPage: false,
            clip: null,
            enableDarkMode: false,
        );
    }
}
