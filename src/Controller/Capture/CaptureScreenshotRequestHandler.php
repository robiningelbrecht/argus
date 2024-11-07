<?php

declare(strict_types=1);

namespace App\Controller\Capture;

use App\Controller\RequestHandler;
use App\Domain\Capture\PageCapture;
use App\Infrastructure\RateLimiting\RateLimiter;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final readonly class CaptureScreenshotRequestHandler implements RequestHandler
{
    public function __construct(
        private PageCapture $pageCapture,
    ) {
    }

    #[RateLimiter('capture_page')]
    #[Route(path: '/capture/screenshot', methods: ['GET', 'POST'])]
    public function handle(Request $request): JsonResponse
    {
        $request = CaptureRequest::fromRequest($request);
        if (!$url = $request->getUrl()) {
            throw new BadRequestException('Parameter "url" is required.');
        }

        $capture = $this->pageCapture->screenshot(
            url: $url,
            format: $request->getScreenshotFormat(),
            quality: $request->getScreenshotQuality(),
            viewport: $request->getViewport(),
            captureFullPage: $request->captureFullPage(),
            clip: $request->getClip(),
            enableDarkMode: $request->enableDarkMode(),
            waitForNavigation: $request->getWaitForNavigation()
        );

        return new JsonResponse([$capture]);
    }
}
