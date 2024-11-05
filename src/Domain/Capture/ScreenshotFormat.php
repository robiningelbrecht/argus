<?php

declare(strict_types=1);

namespace App\Domain\Capture;

enum ScreenshotFormat: string
{
    case PNG = 'png';
    case JPEG = 'jpeg';
    case WEBP = 'webp';
}
