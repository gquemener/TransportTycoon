<?php
declare(strict_types=1);

namespace App\ServiceBus;

final class SimpleEventBus implements EventBus
{
    private $listeners;

    public function __construct(array $listeners)
    {
        $this->listeners = $listeners;
    }

    public function dispatch(object $event): void
    {
        $name = get_class($event);
        if (isset($this->listeners[$name])) {
            foreach ($this->listeners[$name] as $listener) {
                $listener($event);
            }
        }
    }
}
