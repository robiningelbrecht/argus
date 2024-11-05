<?php

declare(strict_types=1);

namespace App\Domain\Capture;

final readonly class Viewport
{
    private function __construct(
        private int $width,
        private int $height,
    ) {
    }

    public function getWidth(): int
    {
        return $this->width;
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    public function turn(): self
    {
        return new self(
            width: $this->height,
            height: $this->width
        );
    }

    public static function fromWithAndHeight(int $width, int $height): self
    {
        return new self(
            width: $width,
            height: $height
        );
    }

    public static function default(): self
    {
        return new self(
            width: 1920,
            height: 1080
        );
    }

    public static function device(string $deviceName, bool $turnToLandscape): self
    {
        $deviceViewport = match ($deviceName) {
            'iPhone12', 'iPhone12Pro' => [390, 844],
            'iPhone12Mini' => [360, 780],
            'iPhone12ProMax' => [428, 926],
            'iPhoneSE' => [214, 379],
            'iPhone11ProMax', 'iPhone11XsMax', 'iPhone11Xr', 'iPhone11' => [414, 896],
            'iPhone11Pro', 'iPhoneX', 'iPhone11X', 'iPhone11Xs' => [375, 812],
            'iPhone8Plus', 'iPhone7Plus', 'iPhone6Plus', 'iPhone6sPlus' => [414, 736],
            'iPhone8', 'iPhone7', 'iPhone6', 'iPhone6s' => [375, 667],
            'iPadPro' => [1024, 1366],
            'iPad4', 'iPad3', 'iPadAir2', 'iPadAir1', 'iPadMini', 'iPadMini2', 'iPadMini3', 'Nexus9' => [768, 1024],
            'Nexus6P', 'Nexus5X', 'GooglePixel', 'GooglePixelXL', 'GooglePixel2' => [411, 731],
            'GooglePixel2XL' => [411, 823],
            'SamsungGalaxyNote5', 'OnePlus3' => [480, 853],
            'LGG5', 'LGG4', 'LGG3', 'SamsungGalaxyS7', 'SamsungGalaxyS7Edge' => [360, 640],
            'SamsungGalaxyS9', 'SamsungGalaxyS9+', 'SamsungGalaxyS8', 'SamsungGalaxyS8+' => [360, 740],
            'Nexus7' => [600, 960],
            'SamsungGalaxyTab10' => [800, 1280],
            'ChromebookPixel' => [1280, 850],
            default => throw new \InvalidArgumentException(sprintf('Unknown device: %s', $deviceName)),
        };

        $viewport = new self(...$deviceViewport);
        if ($turnToLandscape) {
            $viewport = $viewport->turn();
        }

        return $viewport;
    }
}
