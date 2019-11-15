<?php
declare(strict_types=1);

namespace App\ServiceBus;

final class EventBus
{
    private $listeners;

    public function __construct(array $listeners)
    {
        $this->listeners = $listeners;
    }

    public function dispatch(object $event): void
    {
        $name = get_class($event);
        printf('[%s] %s %s' . "\n", date(\DateTimeInterface::ISO8601), get_class($event), json_encode($event));
        if (isset($this->listeners[$name])) {
            foreach ($this->listeners[$name] as $listener) {
                $listener($event);
            }
        }
    }
}
