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

    public function createBrowser(bool $enableDarkMode): Browser
    {
        $browser = null;
        // @TODO: Start separate browser for dark / no-dark mode.
        if ($this->filesystem->fileExists(self::SOCKET_FILE)) {
            $socket = $this->filesystem->read(self::SOCKET_FILE);
            try {
                $browser = BrowserFactory::connectToBrowser($socket);
            } catch (BrowserConnectionFailed) {
            }
        }

        if (!$browser) {
            // The browser was probably closed, start it again.
            $options = [
                'keepAlive' => true,
                'headless' => true,
                'noSandbox' => true,
            ];
            if ($enableDarkMode) {
                $options['customFlags'] = ['--enable-features=WebContentsForceDark'];
            }
            $browserFactory = new BrowserFactory();
            $browserFactory->setOptions($options);
            $browser = $browserFactory->createBrowser();

            $this->filesystem->write(self::SOCKET_FILE, $browser->getSocketUri());
        }

        return $browser;
    }
}
