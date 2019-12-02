<?php
declare(strict_types=1);

namespace App\Console;

use App\ServiceBus\EventBus;
use Symfony\Component\Console\Output\OutputInterface;

final class LogDomainEventToConsoleDecorator implements EventBus
{
    private $decorated;
    private $output;

    public function __construct(EventBus $decorated)
    {
        $this->decorated = $decorated;
    }

    public function setOutput(OutputInterface $output): void
    {
        $this->output = $output;
    }

    public function dispatch(object $event): void
    {
        if (null !== $this->output && $this->output->isVerbose()) {
            $this->output->writeln(sprintf(
                'Dispatching <info>event</info> <comment>%s</comment> %s',
                get_class($event),
                json_encode($event)
            ));
        }

        $this->decorated->dispatch($event);
    }

    public function on(string $eventName, object $listener, int $priority = -1): void
    {
        $this->decorated->on($eventName, $listener, $priority);
    }
}
