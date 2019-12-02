<?php
declare(strict_types=1);

namespace App\ServiceBus;

interface EventBus
{
    public function dispatch(object $event): void;

    public function on(string $eventName, object $listener, int $priority = -1): void;
}
