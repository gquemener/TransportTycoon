<?php
declare(strict_types=1);

namespace App\Console;

final class DomainEventLogger
{
    public function log(object $event): void
    {
        printf('[%s] %s %s', date(\DateTimeInterface::ISO8601), get_class($event), json_encode($event));
    }
}
