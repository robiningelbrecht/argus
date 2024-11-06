<?php

declare(strict_types=1);

namespace App\Infrastructure\Browser;

use HeadlessChromium\Browser;

interface HeadlessBrowser
{
    public function createBrowser(): Browser;
}
