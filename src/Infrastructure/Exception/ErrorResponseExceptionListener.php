<?php

namespace App\Infrastructure\Exception;

use App\Infrastructure\Http\HttpStatusCode;
use App\Infrastructure\ValueObject\String\PlatformEnvironment;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

final readonly class ErrorResponseExceptionListener implements EventSubscriberInterface
{
    public function __construct(
        private PlatformEnvironment $platformEnvironment,
    ) {
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        $statusCode = match (true) {
            $exception instanceof NotFoundHttpException => HttpStatusCode::NOT_FOUND,
            $exception instanceof \InvalidArgumentException => HttpStatusCode::BAD_REQUEST,
            default => HttpStatusCode::INTERNAL_SERVER_ERROR,
        };

        $event->allowCustomResponseCode();
        $response = JsonErrorResponse::fromThrowableAndEnvironment($exception, $statusCode, $this->platformEnvironment);
        $event->setResponse($response);
    }

    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::EXCEPTION => 'onKernelException'];
    }
}
