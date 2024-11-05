<?php

declare(strict_types=1);

namespace App\Controller\Capture;

use App\Controller\RequestHandler;
use App\Domain\Capture\PageCapture;
use App\Infrastructure\Http\CaptureRequest;
use App\Infrastructure\RateLimiting\RateLimiter;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final readonly class PdfPageRequestHandler implements RequestHandler
{
    public function __construct(
        private PageCapture $pageCapture,
    ) {
    }

    #[RateLimiter('capture_page')]
    #[Route(path: '/pdf/page', methods: ['GET', 'POST'])]
    public function handle(Request $request): JsonResponse
    {
        $request = new CaptureRequest($request);
        if (!$url = $request->getUrl()) {
            throw new BadRequestException('Parameter "url" is required.');
        }

        $capture = $this->pageCapture->screenshot($url);

        return new JsonResponse([$capture]);
    }
}
