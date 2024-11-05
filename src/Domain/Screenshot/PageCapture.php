<?php

declare(strict_types=1);

namespace App\Domain\Screenshot;

use App\Domain\Browser\Chromium;
use App\Infrastructure\ValueObject\String\Url;
use HeadlessChromium\Page;

final readonly class PageCapture
{
    public function __construct(
        private Chromium $chromium,
    ) {
    }

    public function screenshot(Url $url): string
    {
        $browser = $this->chromium->createBrowser();
        $page = $browser->createPage();
        $page->getSession()->on('method:Network.responseReceived', function (array $params): void {
            // var_dump($params['response']['status']);
            // if (response.status() >= 200 && response.status() <= 399) {
        });
        $page->getSession()->on('method:Runtime.consoleAPICalled', function (array $message): void {
            // var_dump($message);
        });

        $page
            ->navigate((string) $url)
            ->waitForNavigation(Page::NETWORK_IDLE);
        $page->setViewport(1920, 1080)->await();

        $screenshot = $page->screenshot([
            'format' => 'jpeg',  // default to 'png' - possible values: 'png', 'jpeg', 'webp'
            'quality' => 90,      // only when format is 'jpeg' or 'webp' - default 100
        ]);

        $screenshotBase64 = $screenshot->getBase64();
        $page->close();

        return $screenshotBase64;
    }
}
