<?php

declare(strict_types=1);

namespace App\Domain\Browser;

use HeadlessChromium\Browser\ProcessAwareBrowser;

interface HeadlessBrowser
{
    public function createBrowser(): ProcessAwareBrowser;
}
