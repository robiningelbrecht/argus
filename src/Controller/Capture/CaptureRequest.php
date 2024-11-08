<?php

declare(strict_types=1);

namespace App\Controller\Capture;

use App\Domain\Capture\ScreenshotFormat;
use App\Domain\Capture\Viewport;
use App\Infrastructure\Serialization\Json;
use App\Infrastructure\ValueObject\Math\Percentage;
use App\Infrastructure\ValueObject\String\Url;
use HeadlessChromium\Clip;
use Symfony\Component\HttpFoundation\Request;

final readonly class CaptureRequest
{
    private array $data;

    private function __construct(
        private Request $request,
    ) {
        $this->data = match ($this->request->getMethod()) {
            'GET' => $this->request->query->all(),
            'POST' => Json::decode($this->request->getContent()),
        };
    }

    public static function fromRequest(Request $request): self
    {
        return new self($request);
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

    public function getViewport(): Viewport
    {
        if (!empty($this->data['viewport']['device'])) {
            return Viewport::device(
                $this->data['viewport']['device'],
                $this->data['viewport']['landscape'] ?? false,
            );
        }

        if (empty($this->data['viewport']['width'])) {
            return Viewport::default();
        }
        if (empty($this->data['viewport']['height'])) {
            return Viewport::default();
        }

        return Viewport::fromWithAndHeight(
            $this->data['viewport']['width'],
            $this->data['viewport']['height']
        );
    }

    public function captureFullPage(): bool
    {
        return !empty($this->data['captureFullPage']);
    }

    public function enableDarkMode(): bool
    {
        return !empty($this->data['enableDarkMode']);
    }

    public function getClip(): ?Clip
    {
        if (empty($this->data['clip'])) {
            return null;
        }

        if (!isset($this->data['clip']['x'], $this->data['clip']['y'],
            $this->data['clip']['width'],$this->data['clip']['height'])) {
            return null;
        }

        $x = $this->data['clip']['x'];
        $y = $this->data['clip']['y'];
        $width = $this->data['clip']['width'];
        $height = $this->data['clip']['height'];

        return new Clip(
            x: $x,
            y: $y,
            width: $width,
            height: $height,
            scale: (float) $this->data['clip']['scale'] ?? 1.0,
        );
    }

    public function getWaitForNavigation(): ?string
    {
        return $this->data['waitForNavigation'] ?? null;
    }
}
