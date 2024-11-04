<?php

declare(strict_types=1);

namespace App\Infrastructure\CQRS\Bus\Deserializer;

use App\Infrastructure\CQRS\Bus\Command;
use App\Infrastructure\Serialization\Json;

class JsonCommandDeserializer implements CommandDeserializer
{
    public function deserialize(string $serialized): Command
    {
        $decoded = $this->decode($serialized);

        $fqcn = $this->normalize($decoded['commandName']);
        if (!class_exists($fqcn)) {
            throw new CanNotDeserializeCommand("Class $fqcn does not exist. Did you include the full FQCN? Did you properly escape backslashes?");
        }
        $reflectionClass = new \ReflectionClass($fqcn);
        $this->guardThatCommandHasConstructor($reflectionClass);

        $parameters = $reflectionClass->getConstructor()->getParameters();
        /** @var array{commandName: string, payload: array<mixed>} $payload */
        $payload = $decoded['payload'];
        $arguments = $this->buildArgumentList($payload, $parameters);

        /** @var Command $command */
        $command = $reflectionClass->newInstanceArgs($arguments);

        return $command;
    }

    private function normalize(string $fqcn): string
    {
        $fqcn = str_replace('.', '\\', trim($fqcn));

        return (!str_starts_with($fqcn, '\\')) ? '\\'.$fqcn : $fqcn;
    }

    /**
     * @return array{commandName: string, payload: array<mixed>}
     */
    private function decode(string $json): array
    {
        $decoded = Json::decode($json);
        $this->assertCommandName($decoded);
        $this->assertPayload($decoded);

        return $decoded;
    }

    /**
     * @param array<mixed> $decoded
     */
    private function assertPayload(array $decoded): void
    {
        if (!isset($decoded['payload'])) {
            throw new CanNotDeserializeCommand('Missing field payload in json');
        }
    }

    /**
     * @param array<mixed> $decoded
     */
    private function assertCommandName(array $decoded): void
    {
        if (!isset($decoded['commandName'])) {
            throw new CanNotDeserializeCommand('Missing field commandName in json');
        }

        if (!is_string($decoded['commandName'])) {
            throw new CanNotDeserializeCommand('commandName should be a string');
        }
    }

    private function guardThatCommandHasConstructor(\ReflectionClass $reflectionClass): void
    {
        if (!$reflectionClass->getConstructor()) {
            throw new CanNotDeserializeCommand('The command does not have a constructor');
        }
    }

    /**
     * @param array{commandName: string, payload: array<mixed>} $payload
     */
    private function guardThatPayloadHasParameterIfRequired(array $payload, \ReflectionParameter $parameter): void
    {
        $payloadHasParameter = isset($payload[$parameter->getName()]);

        if (!$payloadHasParameter && !$parameter->isOptional()) {
            throw new CanNotDeserializeCommand(sprintf('The parameter [%s] is missing from the Command payload. Add it to the payload or make it optional in the Command constructor.', $parameter->name));
        }
    }

    /**
     * @param array{commandName: string, payload: array<mixed>} $payload
     * @param \ReflectionParameter[]                            $parameters
     *
     * @return array<mixed>
     */
    private function buildArgumentList(array $payload, array $parameters): array
    {
        $arguments = [];
        $remainingProperties = $payload;

        foreach ($parameters as $parameter) {
            $this->guardThatPayloadHasParameterIfRequired($payload, $parameter);
            unset($remainingProperties[$parameter->name]);
            $payloadHasParameter = isset($payload[$parameter->name]);
            $arguments[] = $payloadHasParameter ? $payload[$parameter->name] : $parameter->getDefaultValue();
        }

        $this->guardThatThereAreNoAlienProperties($remainingProperties);

        return $arguments;
    }

    /**
     * @param array<mixed> $remainingProperties
     */
    private function guardThatThereAreNoAlienProperties(array $remainingProperties): void
    {
        if (!empty($remainingProperties)) {
            throw new CanNotDeserializeCommand(sprintf('The parameters [%s] are never used in the Command payload. Remove them from the payload or make sure the Command\'s constructor has parameters with the same name.', implode(', ', array_keys($remainingProperties))));
        }
    }
}
