<?php

declare(strict_types=1);

namespace App\Domain\Capture;

use App\Infrastructure\Browser\Chromium;
use App\Infrastructure\ValueObject\Math\Percentage;
use App\Infrastructure\ValueObject\String\Url;
use HeadlessChromium\Clip;
use HeadlessChromium\Page;

final readonly class PageCapture
{
    public function __construct(
        private Chromium $chromium,
    ) {
    }

    public function screenshot(
        Url $url,
        ScreenshotFormat $format,
        Percentage $quality,
        Viewport $viewport,
        bool $captureFullPage,
        ?Clip $clip,
        bool $enableDarkMode,
    ): string {
        $browser = $this->chromium->createBrowser($enableDarkMode);
        $page = $browser->createPage();
        $page->getSession()->on('method:Network.responseReceived', function (array $params): void {
            // var_dump($params['response']['status']);
            // if (response.status() >= 200 && response.status() <= 399) {
        });
        $page->getSession()->on('method:Runtime.consoleAPICalled', function (array $message): void {
            // var_dump($message);
        });

        $page->navigate((string) $url)
            ->waitForNavigation(Page::NETWORK_IDLE);

        $page->setViewport(
            width: $viewport->getWidth(),
            height: $viewport->getHeight()
        )->await();

        $screenshotOptions = [
            'format' => $format->value,
            'quality' => $quality->toInt(),
        ];

        if ($clip) {
            $screenshotOptions['clip'] = $clip;
        }

        if ($captureFullPage) {
            $screenshotOptions = [
                ...$screenshotOptions,
                'captureBeyondViewport' => true,
                'clip' => $page->getFullPageClip(),
            ];
        }

        $screenshot = $page->screenshot($screenshotOptions);

        $screenshotBase64 = $screenshot->getBase64();
        $page->close();

        return $screenshotBase64;
    }
}
