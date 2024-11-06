<?php

declare(strict_types=1);

namespace App\Infrastructure\Browser;

use HeadlessChromium\Browser;
use HeadlessChromium\BrowserFactory;
use HeadlessChromium\Exception\BrowserConnectionFailed;
use League\Flysystem\FilesystemOperator;

final readonly class Chromium implements HeadlessBrowser
{
    private const string SOCKET_FILE = 'chromium-socket';

    public function __construct(
        private FilesystemOperator $filesystem,
    ) {
    }

    public function createBrowser(): Browser
    {
        $browser = null;
        if ($this->filesystem->fileExists(self::SOCKET_FILE)) {
            $socket = $this->filesystem->read(self::SOCKET_FILE);
            try {
                $browser = BrowserFactory::connectToBrowser($socket);
            } catch (BrowserConnectionFailed) {
            }
        }

        if (!$browser) {
            // The browser was probably closed, start it again
            $browserFactory = new BrowserFactory();
            $browserFactory->setOptions([
                'keepAlive' => true,
                'headless' => true,
                'noSandbox' => true,
                'customFlags' => ['--enable-features=WebContentsForceDark'],
            ]);
            $browser = $browserFactory->createBrowser();

            $this->filesystem->write(self::SOCKET_FILE, $browser->getSocketUri());
        }

        return $browser;
    }
}