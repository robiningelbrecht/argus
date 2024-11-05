<?php

declare(strict_types=1);

namespace App\Controller;

use App\Domain\Capture\PageCapture;
use App\Infrastructure\RateLimiting\RateLimiter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final readonly class CapturePageRequestHandler implements RequestHandler
{
    public function __construct(
        private PageCapture $pageCapture,
    ) {
    }

    #[RateLimiter('capture_page')]
    #[Route(path: '/screenshot', methods: ['GET', 'POST'])]
    public function handle(Request $request): JsonResponse
    {
        return new JsonResponse(['lol']);
    }
}
