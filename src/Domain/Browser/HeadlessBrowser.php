<?php

declare(strict_types=1);

namespace App\Domain\Browser;

use HeadlessChromium\Browser;

interface HeadlessBrowser
{
    public function createBrowser(): Browser;
}
