<?php

declare(strict_types=1);

namespace App\Controller\Capture;

use App\Infrastructure\Serialization\Json;
use App\Infrastructure\ValueObject\String\Url;
use Symfony\Component\HttpFoundation\Request;

final readonly class CaptureRequest
{
    private array $data;

    public function __construct(
        private Request $request,
    )
    {
        if ($this->request->isMethod('GET')) {

        }

        $this->data = match ($this->request->getMethod()) {
            'GET' => $this->request->query->all(),
            'POST' => Json::decode($this->request->getContent()),
        };
    }

    public function getUrl(): ?Url
    {
        return Url::fromOptionalString($this->data['url'] ?? null);
    }
}