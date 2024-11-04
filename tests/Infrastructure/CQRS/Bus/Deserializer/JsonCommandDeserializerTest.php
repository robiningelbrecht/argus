<?php

namespace App\Tests\Infrastructure\CQRS\Bus\Deserializer;

use App\Infrastructure\CQRS\Bus\Deserializer\CanNotDeserializeCommand;
use App\Infrastructure\CQRS\Bus\Deserializer\JsonCommandDeserializer;
use App\Tests\Infrastructure\CQRS\Bus\RunAnOperation\RunAnOperation;
use PHPUnit\Framework\TestCase;

class JsonCommandDeserializerTest extends TestCase
{
    private JsonCommandDeserializer $jsonCommandDeserializer;

    public function testItShouldDeserialize(): void
    {
        $json = <<<JSON
        
        {
            "commandName": "App.Tests.Infrastructure.CQRS.Bus.RunAnOperation.RunAnOperation", 
            "payload": {
                "value": "a string"
            }
        }
        JSON;

        static::assertInstanceOf(
            RunAnOperation::class,
            $this->jsonCommandDeserializer->deserialize($json)
        );
    }

    public function testItShouldThrowWhenClassDoesNotExist(): void
    {
        $json = <<<JSON
        
        {
            "commandName": "App.Tests.Infrastructure.CQRS.Bus.NonExistingCommand.NonExistingCommand",
            "payload": {}
        }  
        JSON;

        $this->expectException(CanNotDeserializeCommand::class);
        $this->expectExceptionMessage('Class \App\Tests\Infrastructure\CQRS\Bus\NonExistingCommand\NonExistingCommand does not exist. Did you include the full FQCN? Did you properly escape backslashes?');
        $this->jsonCommandDeserializer->deserialize($json);
    }

    public function testItShouldThrowWhenConstructorIsMissing(): void
    {
        $json = <<<JSON
        
        {
            "commandName": "App.Tests.Infrastructure.CQRS.Bus.RunAnOperationCommand.RunAnOperationCommand",
            "payload": {}
        }  
        JSON;

        $this->expectException(CanNotDeserializeCommand::class);
        $this->expectExceptionMessage('The command does not have a constructor');
        $this->jsonCommandDeserializer->deserialize($json);
    }

    public function testItShouldThrowWithAlienProperties(): void
    {
        $json = <<<JSON
        
        {
            "commandName": "App.Tests.Infrastructure.CQRS.Bus.RunAnOperation.RunAnOperation",
            "payload": {
                "value": "a string",
                "alienValue": "a string"
            }
        }
        JSON;

        $this->expectException(CanNotDeserializeCommand::class);
        $this->expectExceptionMessage('The parameters [alienValue] are never used in the Command payload. Remove them from the payload or make sure the Command\'s constructor has parameters with the same name.');
        $this->jsonCommandDeserializer->deserialize($json);
    }

    public function testItShouldThrowWhenPayloadIsMissing(): void
    {
        $json = <<<JSON
        
        {
            "commandName": "App.Tests.Infrastructure.CQRS.Bus.RunAnOperation.RunAnOperation"
        }
        JSON;

        $this->expectException(CanNotDeserializeCommand::class);
        $this->expectExceptionMessage('Missing field payload in json');
        $this->jsonCommandDeserializer->deserialize($json);
    }

    public function testItShouldThrowWhenCommandNameIsMissing(): void
    {
        $json = <<<JSON
        {
            "payload": {
                "value": "a string"
            }
        }
        JSON;

        $this->expectException(CanNotDeserializeCommand::class);
        $this->expectExceptionMessage('Missing field commandName in json');
        $this->jsonCommandDeserializer->deserialize($json);
    }

    public function testItShouldThrowWhenCommandNameIsNotAString(): void
    {
        $json = <<<JSON
        {
            "commandName": ["App.Tests.Infrastructure.CQRS.Bus.RunAnOperation.RunAnOperation"],
            "payload": {
                "value": "a string"
            }
        }
        JSON;

        $this->expectException(CanNotDeserializeCommand::class);
        $this->expectExceptionMessage('commandName should be a string');
        $this->jsonCommandDeserializer->deserialize($json);
    }

    public function testItShouldThrowWhenFieldIsMissingFromPayload(): void
    {
        $json = <<<JSON
        
        {
            "commandName": "App.Tests.Infrastructure.CQRS.Bus.RunAnOperation.RunAnOperation",
            "payload": {}
        }  
        JSON;

        $this->expectException(CanNotDeserializeCommand::class);
        $this->expectExceptionMessage('The parameter [value] is missing from the Command payload. Add it to the payload or make it optional in the Command constructor.');
        $this->jsonCommandDeserializer->deserialize($json);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->jsonCommandDeserializer = new JsonCommandDeserializer();
    }
}
