<?php

declare(strict_types=1);

namespace App\Domain\Capture;

use App\Infrastructure\Browser\Chromium;
use App\Infrastructure\ValueObject\Math\Percentage;
use App\Infrastructure\ValueObject\String\Url;
use HeadlessChromium\Clip;
use HeadlessChromium\Exception\NavigationExpired;
use HeadlessChromium\Exception\OperationTimedOut;
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
        ?string $waitForNavigation,
    ): string {
        if ($captureFullPage && $clip) {
            throw new \RuntimeException('Cannot use both fullPage and clip, they are mutually exclusive');
        }

        if ($waitForNavigation && !defined("HeadlessChromium\Page::{$waitForNavigation}")) {
            throw new \RuntimeException(sprintf('Invalid value "%s" for waitForNavigation', $waitForNavigation));
        }

        $browser = $this->chromium->createBrowser($enableDarkMode);
        $page = $browser->createPage();
        $page->getSession()->on('method:Network.responseReceived', function (array $params): void {
            // var_dump($params['response']['status']);
            // if (response.status() >= 200 && response.status() <= 399) {
        });
        $page->getSession()->on('method:Runtime.consoleAPICalled', function (array $message): void {
            // var_dump($message);
        });

        try {
            $page->navigate((string) $url)
                ->waitForNavigation($waitForNavigation ? Page::{$waitForNavigation} : Page::LOAD);
        } catch (OperationTimedOut|NavigationExpired $e) {
            $page->close();
            $browser->close();

            throw $e;
        }

        $page->setViewport(
            width: $viewport->getWidth(),
            height: $viewport->getHeight()
        )->await();

        $screenshotOptions = [
            'format' => $format->value,
            'quality' => $quality->toInt(),
            'optimizeForSpeed' => true,
        ];

        if ($clip) {
            $screenshotOptions['clip'] = $clip;
        }

        if ($captureFullPage) {
            $screenshotOptions = [
                ...$screenshotOptions,
                'captureBeyondViewport' => true,
                'clip' => $page->getFullPageClip(1),
            ];
        }

        $screenshot = $page->screenshot($screenshotOptions);

        $screenshotBase64 = $screenshot->getBase64();
        $page->close();
        $browser->close();

        return $screenshotBase64;
    }
}
