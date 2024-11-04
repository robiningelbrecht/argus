<?php

namespace App\Console;

use App\Domain\Browser\Chromium;
use HeadlessChromium\Page;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:some:test')]
final class TestRemoveCommand extends Command
{
    public function __construct(
        private readonly Chromium $chromium,
    ) {
        parent::__construct();
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $browser = $this->chromium->createBrowser();
        $page = $browser->createPage();
        $page->getSession()->on('method:Network.responseReceived', function (array $params): void {
            var_dump($params['response']['status']);
            // if (response.status() >= 200 && response.status() <= 399) {
        });
        $page->getSession()->on('method:Runtime.consoleAPICalled', function (array $message): void {
            var_dump($message);
        });

        $page
            ->navigate('https://platform.lochting.com/preview/product-wall/productWall-d5b1b8bd-326e-4e95-b6bd-6d6c15928693?auth=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJsb2NodGluZy5jb20iLCJhdWQiOiJsb2NodGluZy5jb20iLCJpYXQiOjE3MjczNDI0MzIuMjEyMjY0LCJuYmYiOjE3MjczNDI0MzIuMjEyMjY0LCJleHAiOjE3MjczNzg0MzIuMjEyMjY0LCJzdWIiOiJ1c2VyLTI1ZjkwYmU1LTU1ZDQtNDY3NS05ZjU4LTJiMmJjNjdhMzRkYyIsInVzZXJuYW1lIjoiU2VyZ2l1IFBhcmFzY2hpdiIsInJvbGUiOiJwbGF0Zm9ybUFkbWluaXN0cmF0b3IiLCJjb21wYW55SWQiOiJjb21wYW55LWJhbGR3aW4iLCJjb21wYW55TmFtZSI6IkJhbGR3aW4iLCJjb21wYW55Q291bnRyeSI6IkJFIiwiY29tcGFueUxvY2FsZXMiOlsiZW5fQkUiLCJubF9CRSIsImZyX0JFIl0sImNvbXBhbnlUeXBlIjoib3RoZXIiLCJjYW5TaGFyZVdpdGhQbGF0Zm9ybSI6ZmFsc2UsImNhblNoYXJlV2l0aENvdW50cmllcyI6W10sImNhblVzZVNoYXJpbmdHcm91cHMiOnRydWUsImJyYW5jaElkIjoiYnJhbmNoLTExYmQ1Y2RiLWRjZjItNDMwYy04ZmMyLWYxOWM4YmFmOTI1ZSIsImJyYW5jaExvY2FsZXMiOlsiZW5fQkUiLCJubF9CRSIsImZyX0JFIl0sImJyYW5jaE5hbWUiOiJCYWxkd2luIFJPIiwiYnJhbmNoVGltZXpvbmUiOiJFdXJvcGUvQnJ1c3NlbHMifQ.nggkIr9pUl81T05hMDioLflPQbfMjpRQv-fGTLerI0c')
            ->waitForNavigation(Page::NETWORK_IDLE);
        $page->setViewport(1920, 1080)->await();

        $screenshot = $page->screenshot([
            'format' => 'jpeg',  // default to 'png' - possible values: 'png', 'jpeg', 'webp'
            'quality' => 90,      // only when format is 'jpeg' or 'webp' - default 100
        ]);

        // save the screenshot
        // $screenshot->saveToFile('test.jpeg');
        $page->close();

        return Command::SUCCESS;
    }
}
