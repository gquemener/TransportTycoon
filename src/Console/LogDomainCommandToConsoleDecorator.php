<?php
declare(strict_types=1);

namespace App\Console;

use App\ServiceBus\CommandBus;
use Symfony\Component\Console\Output\OutputInterface;

final class LogDomainCommandToConsoleDecorator implements CommandBus
{
    private $decorated;
    private $output;

    public function __construct(CommandBus $decorated)
    {
        $this->decorated = $decorated;
    }

    public function setOutput(OutputInterface $output): void
    {
        $this->output = $output;
    }

    public function dispatch(object $command): void
    {
        if (null !== $this->output && $this->output->isVeryVerbose()) {
            $this->output->writeln(sprintf(
                'Dispatching <info>command</info> <comment>%s</comment> %s',
                get_class($command),
                json_encode($command)
            ));
        }

        $this->decorated->dispatch($command);
    }
}
