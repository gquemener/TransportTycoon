<?php
declare(strict_types=1);

namespace App\ServiceBus;

final class EventDispatchingCommandHandler
{
    private $handler;
    private $eventBus;

    public function __construct(object $handler, EventBus $eventBus)
    {
        $this->handler = $handler;
        $this->eventBus = $eventBus;
    }

    public function handle(object $command): void
    {
        $result = $this->handler->handle($command);

        if (!$result instanceof \Generator) {
            return;
        }

        foreach ($result as $event) {
            $this->eventBus->dispatch($event);
        }
    }
}
