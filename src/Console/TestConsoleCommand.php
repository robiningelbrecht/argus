<?php

namespace App\Console;

use App\Domain\Capture\CapturePage\CapturePage;
use App\Infrastructure\CQRS\Bus\CommandBus;
use App\Infrastructure\ValueObject\String\Url;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:test')]
final class TestConsoleCommand extends Command
{
    public function __construct(
        private readonly CommandBus $commandBus,
    ) {
        parent::__construct();
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->commandBus->dispatch(new CapturePage(
            Url::fromString('https://robiningelbrecht.be')
        ));

        return Command::SUCCESS;
    }
}
