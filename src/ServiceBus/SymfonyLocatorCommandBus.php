<?php
declare(strict_types=1);

namespace App\ServiceBus;

use Psr\Container\ContainerInterface;

final class SymfonyLocatorCommandBus implements CommandBus
{
    private $locator;

    public function __construct(ContainerInterface $locator)
    {
        $this->locator = $locator;
    }

    public function dispatch(object $command): void
    {
        $name = get_class($command);
        if (!$this->locator->has($name)) {
            throw new \InvalidArgumentException(sprintf('No handler found for command "%s"', get_class($command)));
        }
        $handler = $this->locator->get($name);

        $handler->handle($command);
    }
}
