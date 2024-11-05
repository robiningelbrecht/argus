<?php

declare(strict_types=1);

namespace App\Domain\Capture;

use App\Infrastructure\Serialization\Json;
use App\Infrastructure\ValueObject\String\Percentage;
use App\Infrastructure\ValueObject\String\Url;
use Symfony\Component\HttpFoundation\Request;

final readonly class CaptureRequest
{
    private array $data;

    public function __construct(
        private Request $request,
    ) {
        $this->data = match ($this->request->getMethod()) {
            'GET' => $this->request->query->all(),
            'POST' => Json::decode($this->request->getContent()),
        };
    }

    public function getUrl(): ?Url
    {
        return Url::fromOptionalString($this->data['url'] ?? null);
    }

    public function getScreenshotFormat(): ?ScreenshotFormat
    {
        return ScreenshotFormat::tryFrom($this->data['format'] ?? '') ?? ScreenshotFormat::WEBP;
    }

    public function getScreenshotQuality(): Percentage
    {
        return Percentage::fromInt($this->data['quality'] ?? 100);
    }
}
