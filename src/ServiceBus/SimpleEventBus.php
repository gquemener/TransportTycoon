<?php
declare(strict_types=1);

namespace App\ServiceBus;

final class SimpleEventBus implements EventBus
{
    private $listeners;

    public function __construct(array $listeners)
    {
        foreach ($listeners as $event => $l) {
            foreach ($l as $listener) {
                $this->on($event, $listener);
            }
        }
    }

    public function dispatch(object $event): void
    {
        $name = get_class($event);

        if (!isset($this->listeners[$name])) {
            return;
        }

        $parts = explode('\\', $name);
        $methodName = sprintf('on%s', array_pop($parts));

        foreach ($this->listeners[$name] as $listeners) {
            foreach ($listeners as $listener) {
                call_user_func_array([$listener, $methodName], [$event]);
            }
        }
    }

    public function on(string $eventName, object $listener, int $priority = -1): void
    {
        if (!isset($this->listeners[$eventName])) {
            $this->listeners[$eventName] = [];
        }

        $listeners = $this->listeners[$eventName];

        $listeners[$priority][] = $listener;
        krsort($listeners);

        $this->listeners[$eventName] = $listeners;
    }
}
