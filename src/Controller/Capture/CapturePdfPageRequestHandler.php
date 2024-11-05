<?php

declare(strict_types=1);

namespace App\Controller\Capture;

use App\Controller\RequestHandler;
use App\Infrastructure\RateLimiting\RateLimiter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final readonly class CapturePdfPageRequestHandler implements RequestHandler
{
    #[RateLimiter('capture_page')]
    #[Route(path: '/capture/pdf', methods: ['GET', 'POST'])]
    public function handle(Request $request): JsonResponse
    {
        return new JsonResponse([]);
    }
}
