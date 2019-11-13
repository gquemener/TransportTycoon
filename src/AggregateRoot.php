<?php
declare(strict_types=1);

namespace App;

trait AggregateRoot
{
    private $events = [];

    private function record(object $event): void
    {
        $this->events[] = $event;
    }

    public function popEvents(): array
    {
        $events = $this->events;
        $this->events = [];

        return $events;
    }
}
